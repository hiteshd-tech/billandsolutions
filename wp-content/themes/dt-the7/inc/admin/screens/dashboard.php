<?php
/**
 * The7 dashboard screen.
 *
 * @package The7\Admin
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="the7-dashboard" class="wrap">
	<h1 aria-hidden="true" class="hidden"><?php esc_html_e( 'The7 Dashboard', 'the7mk2' ); ?></h1>

	<div class="the7-welcome">
		<svg class="the7-logo" xmlns="http://www.w3.org/2000/svg" width="132" height="132" fill="none" viewBox="0 0 132 132">
			<defs>
				<linearGradient id="a" x1="61.991" x2="61.991" y1="5" y2="81.956" gradientUnits="userSpaceOnUse">
					<stop offset=".07" stop-color="#13E3EE"/>
					<stop offset=".9" stop-color="#00A6F4"/>
				</linearGradient>
			</defs>
			<path fill="url(#a)" d="M92.014 10.465C114.397 20.357 130 42.62 130 68.5c0 35.07-28.654 63.499-64 63.499-35.346 0-64-28.429-64-63.5C2 42.62 17.603 20.358 39.985 10.466l1.288 2.908C20.017 22.773 5.2 43.92 5.2 68.5c0 33.316 27.221 60.324 60.8 60.324 33.58 0 60.8-27.008 60.8-60.324 0-24.58-14.817-45.727-36.073-55.127l1.287-2.908ZM66 5a64.6 64.6 0 0 1 3.95.12h-7.9C63.364 5.04 64.681 5 66 5Z"/>
			<path fill="#fff" d="m90.432 44.894-32.827 59.403h-6.62L83.86 44.894H48v-5.366h42.432v5.366ZM52.568 4.762h3.6v1.69h-3.6v6.858c0 .604.16 1.072.48 1.405.336.318.8.476 1.392.476.192 0 .376-.023.552-.071.192-.063.44-.206.744-.429l.744 1.548c-.416.27-.792.453-1.128.548-.336.111-.68.166-1.032.166-1.168 0-2.072-.301-2.712-.904-.64-.604-.96-1.453-.96-2.548V6.452H48.56v-1.69h2.088V1.047h1.92v3.715Zm24.901-.287c1.12 0 2.088.277 2.904.833.816.54 1.447 1.302 1.895 2.286.448.984.672 2.143.672 3.477h-9.42c.04.781.205 1.464.493 2.047a3.78 3.78 0 0 0 1.463 1.572c.624.35 1.337.524 2.136.524.849 0 1.56-.198 2.137-.595a3.961 3.961 0 0 0 1.368-1.572l1.655.833a5.302 5.302 0 0 1-1.224 1.62 5.247 5.247 0 0 1-1.776 1.071c-.672.254-1.424.381-2.256.381-1.168 0-2.2-.262-3.096-.786a5.76 5.76 0 0 1-2.112-2.214c-.496-.937-.744-2.016-.744-3.239 0-1.222.249-2.301.745-3.238a5.642 5.642 0 0 1 2.088-2.19c.895-.54 1.92-.81 3.072-.81Zm-.024 1.69c-.656 0-1.28.151-1.872.453a3.78 3.78 0 0 0-1.416 1.286c-.292.447-.463.971-.513 1.571h7.241c-.045-.593-.192-1.117-.44-1.571a3.285 3.285 0 0 0-1.225-1.286c-.512-.302-1.103-.453-1.775-.453Zm-16.647.349a4.277 4.277 0 0 1 1.248-1.323c.704-.476 1.552-.714 2.544-.714.928 0 1.744.19 2.448.571.704.365 1.248.953 1.632 1.762.4.81.592 1.874.576 3.191v6.668h-1.92v-6.096c0-1.175-.144-2.072-.432-2.691-.272-.62-.648-1.04-1.128-1.262-.48-.238-1.024-.357-1.632-.357-1.056 0-1.88.373-2.472 1.119-.576.73-.864 1.77-.864 3.12v6.167h-1.92V0h1.92v6.514Z"/>
		</svg>
		<h2 class="the7-welcome-text">
			<?php
			// translators: %s: The7.
			printf( esc_html__( 'Welcome to %s', 'the7mk2' ), ' <strong>The7!</strong>' );
			?>
		</h2>
		<p class="the7-version"><?php echo esc_html( sprintf( 'v.%s', THE7_VERSION ) ); ?></p>
	</div>

	<div class="the7-registration">

		<?php if ( is_super_admin() ) : ?>

		<div class="the7-registration-block the7-postbox">

			<?php
			if ( presscore_theme_is_activated() ) {
				require __DIR__ . '/partials/dashboard/theme-de-registration-form.php';
			} else {
				require __DIR__ . '/partials/dashboard/theme-registration-form.php';
			}
			?>

		</div>

		<?php endif; ?>

		<a href="https://guide.the7.io/" target="_blank" class="the7-link-block the7-postbox" rel="nofollow">
			<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
				<path d="M15.233 4.434a6.018 6.018 0 0 1 4.254 1.762c.184.184.355.38.513.586a6.016 6.016 0 0 1 4.767-2.349H38.75v31.132H1.25V4.434h13.983ZM3.75 33.065h32.5V29.2H24.767a3.516 3.516 0 0 0-3.517 3.53v.008L20 32.735l-1.25.003v-.008a3.519 3.519 0 0 0-2.168-3.261 3.516 3.516 0 0 0-1.349-.269H3.75v3.865Zm0-6.365h11.483a6.017 6.017 0 0 1 3.517 1.136V10.45a3.517 3.517 0 0 0-3.517-3.516H3.75V26.7ZM24.767 6.934a3.516 3.516 0 0 0-3.517 3.516v17.386a6.016 6.016 0 0 1 3.517-1.136H36.25V6.934H24.767Z"></path>
			</svg>
			<span><?php esc_html_e( 'Consult User Guide', 'the7mk2' ); ?></span>
		</a>

		<a href="https://support.dream-theme.com/knowledgebase/" target="_blank" class="the7-link-block the7-postbox" rel="nofollow">
			<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
				<path d="M21.584 26.184h-3.167v-2.5h3.167v2.5ZM20.001 9.367a4.435 4.435 0 0 1 4.433 4.41v.293a4.45 4.45 0 0 1-1.298 3.13l-.001.001-1.32 1.32a1.918 1.918 0 0 0-.564 1.36v1.903h-2.5v-1.9a4.415 4.415 0 0 1 1.299-3.134l1.315-1.315a1.95 1.95 0 0 0 .569-1.368v-.277a1.934 1.934 0 0 0-3.867 0l-2.5-.013a4.434 4.434 0 0 1 4.434-4.41Z"></path>
				<path d="M31.217 3a7.617 7.617 0 0 1 7.616 7.616v15.9a7.616 7.616 0 0 1-7.616 7.618h-9.259L10.8 39.665v-5.531H8.866a7.618 7.618 0 0 1-7.616-7.617v-15.9A7.617 7.617 0 0 1 8.866 3h22.35ZM8.867 5.5a5.116 5.116 0 0 0-5.117 5.116v15.9a5.118 5.118 0 0 0 5.116 5.118H13.3v4l8.074-4h9.843a5.118 5.118 0 0 0 5.116-5.117v-15.9A5.116 5.116 0 0 0 31.217 5.5H8.867Z"></path>
			</svg>
			<span><?php esc_html_e( 'Contact Support', 'the7mk2' ); ?></span>
		</a>

	</div>

	<h2 id="pre-made-websites"><?php echo esc_html_x( 'Pre-made Website Templates', 'admin', 'the7mk2' ); ?></h2>

	<?php presscore_get_template_part( 'the7_admin', 'partials/dashboard/demos' ); ?>

</div>
