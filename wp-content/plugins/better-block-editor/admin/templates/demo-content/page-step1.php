<?php

defined( 'ABSPATH' ) || exit;

$demo_websites = BetterBlockEditor\Modules\DemoContent\RemoteAPI\Demo::get_demos();
$step2_url     = BetterBlockEditor\Modules\DemoContent\Module::get_admin_step2_url();

// Check if site is launched in playground.
$playground_mode = get_option( 'wpbbe_playground_mode', false );

// Count total and by category.
$total_count        = count( $demo_websites );
$block_editor_count = count(
	array_filter(
		$demo_websites,
		function ( $demo ) {
			return in_array( 'fse', $demo['tags'], true );
		}
	)
);
$shop_count         = count(
	array_filter(
		$demo_websites,
		function ( $demo ) {
			return in_array( 'shop', $demo['tags'], true );
		}
	)
);
$import_history     = \BetterBlockEditor\Modules\DemoContent\Trackers\TrackerBase::get_all_demo_history();
$imported_count     = count( $import_history );
?>

<div class="wp-filter websites-filter" style="display: none;">
	<div class="filter-count">
		<span class="count"><?php echo esc_html( $total_count ); ?></span>
	</div>

	<ul class="filter-links">
		<li><a href="#" class="current" data-filter="all"><?php echo esc_html__( 'All', 'better-block-editor' ); ?></a></li>
		<li><a href="#" data-filter="fse"><?php echo esc_html__( 'Block Editor', 'better-block-editor' ); ?> <span class="count">(<?php echo esc_html( $block_editor_count ); ?>)</span></a></li>
		<li><a href="#" data-filter="imported"><?php echo esc_html__( 'Imported', 'better-block-editor' ); ?> <span class="count">(<?php echo esc_html( $imported_count ); ?>)</span></a></li>
	</ul>

	<form class="search-form">
		<p class="search-box">
			<label for="wp-filter-search-input"><?php echo esc_html__( 'Search', 'better-block-editor' ); ?></label>
			<input type="search" id="wp-filter-search-input" class="wp-filter-search" placeholder="<?php echo esc_attr__( 'Search websites...', 'better-block-editor' ); ?>">
		</p>
	</form>
</div>

<div class="websites-browser">

	<?php foreach ( $demo_websites as $demo ) : ?>
		<?php
		$is_imported = ! empty( \BetterBlockEditor\Modules\DemoContent\Trackers\TrackerBase::get_demo_history( $demo['id'] ) );
		if ( $is_imported ) {
			$actions = array( 'remove', 'keep' );
		} else {
			$actions = array( 'import' );
		}

		// Prepare a sanitized, space-separated list of tags for the data-tags attribute.
		// Output escaping is applied directly so PHPCS recognizes it.
		?>
			<div class="websites-item" data-tags="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_text_field', (array) $demo['tags'] ) ) ); ?>">
				<?php if ( $is_imported ) : ?>
					<div class="notice inline notice-success notice-alt"><p><?php echo esc_html__( 'Imported', 'better-block-editor' ); ?></p></div>
				<?php endif; ?>

			<a href="<?php echo esc_url( $demo['link'] ); ?>" class="websites-preview" target="_blank" rel="noopener">
				<img class="websites-thumbnail" alt="<?php echo esc_attr( $demo['title'] ); ?>" src="<?php echo esc_url( $demo['screenshot'] ); ?>">
				<div class="websites-details">
					<span><?php echo esc_html__( 'Preview & partial import', 'better-block-editor' ); ?></span>
				</div>
			</a>

			<div class="websites-caption">
				<h2><?php echo esc_html( $demo['title'] ); ?></h2>
				<form method="post" action="<?php echo esc_url( $step2_url ); ?>" class="websites-actions">
					<input type="hidden" name="demo_id" value="<?php echo esc_attr( $demo['id'] ); ?>">
					<?php wp_nonce_field( 'wpbbe_import_demo' ); ?>
					<?php foreach ( $actions as $act ) : ?>
						<?php if ( 'import' === $act ) : ?>
							

							<button  type="submit" class="button button-primary" data-action="import" name="import_type" value="full_import"
								
							<?php if ( $playground_mode ) : ?>
									title="<?php echo esc_attr( 'Full site template import isn’t supported in Playground (Live Demo) mode.', 'better-block-editor' ); ?>"
									<?php echo esc_attr( 'disabled' ); ?>
								<?php endif; ?>
							>
								<?php echo esc_html__( 'Import full template', 'better-block-editor' ); ?>
							</button>


						<?php elseif ( 'keep' === $act ) : ?>
							<button type="submit" class="button button-primary" data-action="keep">
								<?php echo esc_html__( 'Keep template', 'better-block-editor' ); ?>
							</button>
						<?php elseif ( 'remove' === $act ) : ?>
							<button type="submit" class="button" data-action="remove">
								<?php echo esc_html__( 'Remove template', 'better-block-editor' ); ?>
							</button>
						<?php endif; ?>
					<?php endforeach; ?>
				</form>
			</div>
		</div>
	<?php endforeach; ?>

</div>
