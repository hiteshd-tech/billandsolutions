<?php
/**
 * Download time predictor utility.
 *
 * Estimates download/processing time of assets to help import decisions.
 *
 * Quick usage:
 *
 * ```php
 * $pred = new DownloadTimePredictor();
 * $pred->add_observation( $size_bytes, $time_seconds );
 * $eta = $pred->predict_time( $next_size );
 * ```
 *
 * @package BetterBlockEditor
 */

namespace BetterBlockEditor\Modules\DemoContent;

defined( 'ABSPATH' ) || exit;

/**
 * Download time forecaster for a single session.
 *
 * Assumptions:
 * - Only current session history is available.
 * - Features: file size (bytes) and observed time (seconds).
 *
 * Usage:
 *   $pred = new DownloadTimePredictor( array(
 *       'min_samples_for_ols' => 5,
 *       'ema_alpha'           => 0.3,
 *       'forget'              => 0.98,
 *       'outlier_low'         => 0.5,
 *       'outlier_high'        => 2.0,
 *       'init_p_scale'        => 1e6,
 *   ) );
 *
 *   // After each completed download:
 *   $pred->add_observation( $size_bytes, $time_seconds );
 *
 *   // Predict time for the next file of size $size_bytes_next:
 *   $eta_seconds = $pred->predict_time( $size_bytes_next );
 */
class DownloadTimePredictor {

	/**
	 * Minimum samples to fit initial OLS before switching to RLS.
	 *
	 * @var int
	 */
	private $min_samples_for_ols = 5;

	/**
	 * EMA alpha for speed (responsiveness).
	 *
	 * @var float
	 */
	private $ema_alpha = 0.3;

	/**
	 * RLS forgetting factor (0.95–0.995 is typical).
	 *
	 * @var float
	 */
	private $forget = 0.98;

	/**
	 * Outlier low threshold as ratio to EMA speed.
	 *
	 * @var float
	 */
	private $outlier_low = 0.5;

	/**
	 * Outlier high threshold as ratio to EMA speed.
	 *
	 * @var float
	 */
	private $outlier_high = 2.0;

	/**
	 * Initial covariance scale for RLS.
	 *
	 * @var float
	 */
	private $init_p_scale = 1000000.0;

	// ---- Running stats for tiny batch OLS ----

	/**
	 * Count of valid (non-outlier) observations.
	 *
	 * @var int
	 */
	private $n = 0;

	/**
	 * Running sum of observed sizes.
	 *
	 * @var float
	 */
	private $sx = 0.0;

	/**
	 * Running sum of observed times.
	 *
	 * @var float
	 */
	private $sy = 0.0;

	/**
	 * Running sum of squared sizes.
	 *
	 * @var float
	 */
	private $sxx = 0.0;

	/**
	 * Running sum of size/time products.
	 *
	 * @var float
	 */
	private $sxy = 0.0;

	// ---- Model parameters (t = a*size + b) ----

	/**
	 * Seconds per byte (slope).
	 *
	 * @var float|null
	 */
	private $a = null;

	/**
	 * Fixed overhead per file in seconds (intercept).
	 *
	 * @var float|null
	 */
	private $b = null;

	// ---- RLS state ----

	/**
	 * Whether RLS updates are enabled.
	 *
	 * @var bool
	 */
	private $rls_enabled = false;

	/**
	 * 2x2 covariance matrix for RLS updates.
	 *
	 * @var array
	 */
	private $p = array( array( 0.0, 0.0 ), array( 0.0, 0.0 ) );

	// ---- EMA of speed for outlier detection ----

	/**
	 * Exponential moving average of bytes per second.
	 *
	 * @var float|null
	 */
	private $ema_speed = null;

	/**
	 * Constructor.
	 *
	 * @param array $args Optional configuration overrides.
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'min_samples_for_ols' => 5,
			'ema_alpha'           => 0.3,
			'forget'              => 0.98,
			'outlier_low'         => 0.5,
			'outlier_high'        => 2.0,
			'init_p_scale'        => 1000000.0,
		);

		$args = wp_parse_args( $args, $defaults );

		$this->min_samples_for_ols = max( 2, (int) $args['min_samples_for_ols'] );
		$this->ema_alpha           = (float) $args['ema_alpha'];
		$this->forget              = (float) $args['forget'];
		$this->outlier_low         = (float) $args['outlier_low'];
		$this->outlier_high        = (float) $args['outlier_high'];
		$this->init_p_scale        = (float) $args['init_p_scale'];
	}

	/**
	 * Add one observed download (size in bytes, time in seconds).
	 *
	 * @param float $size_bytes File size in bytes.
	 * @param float $time_sec   Download time in seconds.
	 * @return bool True if the observation updated the model, false if treated as an outlier/invalid.
	 */
	public function add_observation( $size_bytes, $time_sec ) {
		$size_bytes = (float) $size_bytes;
		$time_sec   = (float) $time_sec;

		if ( $size_bytes <= 0.0 || $time_sec <= 0.0 ) {
			return false;
		}

		$speed_bps = $size_bytes / $time_sec;

		// Initialize EMA(speed) if needed.
		if ( null === $this->ema_speed ) {
			$this->ema_speed = $speed_bps;
		} else {
			$ratio      = $speed_bps / max( $this->ema_speed, 1e-12 );
			$is_outlier = ( $ratio < $this->outlier_low ) || ( $ratio > $this->outlier_high );

			// Update EMA regardless; soften update if outlier.
			$alpha           = $is_outlier ? ( $this->ema_alpha * 0.25 ) : $this->ema_alpha;
			$this->ema_speed = $alpha * $speed_bps + ( 1.0 - $alpha ) * $this->ema_speed;

			if ( $is_outlier ) {
				// Do not update parameter model with this sample.
				return false;
			}
		}

		// If RLS is enabled and we have parameters, do RLS update.
		if ( $this->rls_enabled && null !== $this->a && null !== $this->b ) {
			$this->rls_update( $size_bytes, $time_sec );
			return true;
		}

		// Accumulate for tiny batch OLS.
		$this->n   += 1;
		$this->sx  += $size_bytes;
		$this->sy  += $time_sec;
		$this->sxx += $size_bytes * $size_bytes;
		$this->sxy += $size_bytes * $time_sec;

		// Fit OLS once we have at least 2 points.
		if ( $this->n >= 2 ) {
			$this->fit_ols();
		}

		// Enable RLS after enough samples and a valid OLS fit.
		if ( ! $this->rls_enabled && $this->n >= $this->min_samples_for_ols && null !== $this->a && null !== $this->b ) {
			$this->enable_rls();
		}

		return true;
	}

	/**
	 * Predict time (seconds) for a given file size (bytes).
	 *
	 * @param float $size_bytes File size in bytes.
	 * @return float|null Predicted seconds, or null if insufficient information.
	 */
	public function predict_time( $size_bytes ) {
		$size_bytes = (float) $size_bytes;

		if ( $size_bytes <= 0.0 ) {
			return null;
		}

		// Prefer linear model if available.
		if ( null !== $this->a && null !== $this->b ) {
			$t = $this->a * $size_bytes + $this->b;
			return max( 0.0, $t );
		}

		// Fallback: EMA-based estimate if model not ready.
		if ( null !== $this->ema_speed && $this->ema_speed > 0.0 ) {
			return $size_bytes / $this->ema_speed;
		}

		return null;
	}

	/**
	 * Reset for a new session.
	 */
	public function reset() {
		$this->n   = 0;
		$this->sx  = 0.0;
		$this->sy  = 0.0;
		$this->sxx = 0.0;
		$this->sxy = 0.0;

		$this->a = null;
		$this->b = null;

		$this->rls_enabled = false;
		$this->p           = array( array( 0.0, 0.0 ), array( 0.0, 0.0 ) );

		$this->ema_speed = null;
	}

	/**
	 * Get current parameters / telemetry (for logging or debugging).
	 *
	 * @return array {
	 *     @type float|null 'a_sec_per_byte' Slope (seconds per byte).
	 *     @type float|null 'b_overhead_sec' Intercept (seconds).
	 *     @type float|null 'ema_speed_bps'  EMA of speed (bytes/sec).
	 *     @type bool        'rls'           Whether RLS is enabled.
	 * }
	 */
	public function get_params() {
		return array(
			'a_sec_per_byte' => $this->a,
			'b_overhead_sec' => $this->b,
			'ema_speed_bps'  => $this->ema_speed,
			'rls'            => $this->rls_enabled,
		);
	}

	// ===== Internals =====================================================

	/**
	 * Fit OLS parameters for t = a*size + b using running sums.
	 *
	 * @return void
	 */
	private function fit_ols() {
		$den = ( $this->n * $this->sxx - $this->sx * $this->sx );

		if ( abs( $den ) < 1e-18 ) {
			// Degenerate (e.g., all sizes equal). Fall back to inverse of average speed.
			$avg_speed = ( $this->sy > 0.0 ) ? ( $this->sx / $this->sy ) : null; // Bytes per second.
			if ( null !== $avg_speed && $avg_speed > 0.0 ) {
				$this->a = 1.0 / $avg_speed;
				$this->b = 0.0; // cannot infer overhead safely in this case.
			}
			return;
		}

		$a = ( $this->n * $this->sxy - $this->sx * $this->sy ) / $den;
		$b = ( $this->sy * $this->sxx - $this->sx * $this->sxy ) / $den;

		// Guard against nonsense; keep non-negative slope.
		if ( is_finite( $a ) && is_finite( $b ) && $a >= 0.0 ) {
			$this->a = $a;
			$this->b = $b;
		}
	}

	/**
	 * Enable RLS around the current OLS solution.
	 *
	 * @return void
	 */
	private function enable_rls() {
		$s                 = $this->init_p_scale;
		$this->p           = array( array( $s, 0.0 ), array( 0.0, $s ) );
		$this->rls_enabled = true;
	}

	/**
	 * Perform one RLS update with forgetting.
	 *
	 * @param float $size_bytes File size in bytes.
	 * @param float $time_sec   Observed time in seconds.
	 * @return void
	 */
	private function rls_update( $size_bytes, $time_sec ) {
		$x1     = (float) $size_bytes; // Size.
		$x2     = 1.0;                  // Bias term.
		$y      = (float) $time_sec;    // Time.
		$lambda = $this->forget;

		// P * x vector.
		$p_x1 = $this->p[0][0] * $x1 + $this->p[0][1] * $x2;
		$p_x2 = $this->p[1][0] * $x1 + $this->p[1][1] * $x2;

		// Denominator = lambda + x^T * P * x.
		$denom = $lambda + ( $x1 * $p_x1 + $x2 * $p_x2 );
		if ( $denom <= 1e-18 ) {
			return;
		}

		// Gain K = P*x / denom (2x1 vector).
		$k1 = $p_x1 / $denom;
		$k2 = $p_x2 / $denom;

		// Prediction error e = y - x^T * theta.
		$yhat = ( ( null !== $this->a ? $this->a : 0.0 ) * $x1 ) + ( null !== $this->b ? $this->b : 0.0 ) * $x2;
		$e    = $y - $yhat;

		// Update theta.
		$this->a = ( null !== $this->a ? $this->a : 0.0 ) + $k1 * $e;
		$this->b = ( null !== $this->b ? $this->b : 0.0 ) + $k2 * $e;

		// Update P: P = (1/lambda) * (P - K*x^T*P).
		$k_x00 = $k1 * $x1;
		$k_x01 = $k1 * $x2;
		$k_x10 = $k2 * $x1;
		$k_x11 = $k2 * $x2;

		$k_xp00 = $k_x00 * $this->p[0][0] + $k_x01 * $this->p[1][0];
		$k_xp01 = $k_x00 * $this->p[0][1] + $k_x01 * $this->p[1][1];
		$k_xp10 = $k_x10 * $this->p[0][0] + $k_x11 * $this->p[1][0];
		$k_xp11 = $k_x10 * $this->p[0][1] + $k_x11 * $this->p[1][1];

		$p00 = ( $this->p[0][0] - $k_xp00 ) / $lambda;
		$p01 = ( $this->p[0][1] - $k_xp01 ) / $lambda;
		$p10 = ( $this->p[1][0] - $k_xp10 ) / $lambda;
		$p11 = ( $this->p[1][1] - $k_xp11 ) / $lambda;

		// Enforce symmetry (numerical hygiene).
		$this->p = array(
			array( $p00, 0.5 * ( $p01 + $p10 ) ),
			array( 0.5 * ( $p01 + $p10 ), $p11 ),
		);

		// Ensure non-negative slope.
		if ( $this->a < 0.0 ) {
			$this->a = 0.0;
		}
	}
}
