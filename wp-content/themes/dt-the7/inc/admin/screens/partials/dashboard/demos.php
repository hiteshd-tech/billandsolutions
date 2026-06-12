<?php
/**
 * @package The7
 */

defined( 'ABSPATH' ) || exit;

$demos               = the7_demo_content()->get_demos();
$demos_count         = count( $demos );
$demo_block_disabled = '';
$buttons_disabled    = '';
if ( ! presscore_theme_is_activated() ) {
	$buttons_disabled    = 'disabled';
	$demo_block_disabled = 'the7-disabled';
}
?>

<div class="wp-filter websites-filter">
	<div class="filter-count">
		<span class="count"><?php echo esc_html( $demos_count ); ?></span>
	</div>

	<ul class="filter-links">
		<li><a href="#" class="current" data-filter="*"><?php esc_html_e( 'All', 'the7mk2' ); ?></a></li>
		<?php
		$filter_tags = the7_demo_get_quick_search_tags_list();

		foreach ( $filter_tags as $tag_slug => $tag_name ) {
			echo '<li><a href="#" class="the7-demo-tag" data-filter="' . esc_attr( $tag_slug ) . '">' . esc_html( $tag_name ) . '</a></li>';
		}
		?>
		<li><button class="button drawer-toggle"><?php esc_html_e( 'Single Page', 'the7mk2' ); ?></button></li>
	</ul>

	<form class="search-form">
		<p class="search-box">
			<label for="wp-filter-search-input"><?php esc_html_e( 'Search', 'the7mk2' ); ?></label>
			<input type="search" id="wp-filter-search-input" class="wp-filter-search">
		</p>
	</form>

	<div id="single-page-group" class="group the7-import-by-url-page filter-drawer" data-post-type="page">
		<form class="the7-import-page-form" action="?page=the7-dashboard&action=demo_import" method="post">
			<?php wp_nonce_field( 'the7_import_demo' ); ?>
			<input type="hidden" name="import_type" value="url_import">
			<label class="screen-reader-text" for="the7-import-page-url">Page URL to import</label>
			<input type="search" id="the7-import-page-url" class="widefat" name="the7_post_url" value="" placeholder="Page URL (link)">
			<div class="dt-dummy-button-wrap">
			<button type="submit" id="the7-import-page-submit" class="button button-primary">Import</button>
			</div>
		</form>
	</div>

</div>

<?php if ( $demo_block_disabled ) : ?>

<div class="notice inline the7-dashboard-notice">
	<p>
		<?php esc_html_e( "You'll be able to install pre-made website templates once you register the theme.", 'the7mk2' ); ?>
	</p>
</div>

<?php endif; ?>

<div class="websites-browser <?php echo esc_attr( $demo_block_disabled ); ?>">

<?php foreach ( $demos as $demo ) : ?>

	<?php
	/**
	 * @var The7_Demo $demo
	 */

	$tags = $demo->tags;
	if ( $demo->partially_imported() ) {
		$tags[] = 'imported';
	}
	$tags               = wp_json_encode( $tags );
	$preview_url        = $demo->link ? $demo->link : '#';
	$page_builder_class = 'websites-builder-fse';
	if ( $demo->is_elementor() ) {
		$page_builder_class = 'websites-builder-el';
	} elseif ( $demo->is_wpb() ) {
		$page_builder_class = 'websites-builder-wpb';
	}
	?>

	<div class="websites-item" data-dummy-id="<?php echo esc_attr( $demo->id ); ?>" data-tags="<?php echo esc_attr( $tags ); ?>">

		<?php if ( $demo->partially_imported() ) : ?>

			<div class="notice inline notice-success notice-alt"><p><?php esc_html_e( 'Imported', 'the7mk2' ); ?></p></div>

		<?php endif; ?>

		<a href="<?php echo esc_url( $preview_url ); ?>" class="websites-preview" target="_blank" rel="nofollow">
			<img class="websites-thumbnail" alt="<?php echo esc_attr( $demo->title ); ?>" src="<?php echo esc_url( $demo->screenshot ); ?>">
			<div class="websites-details">
				<span><?php esc_html_e( 'Preview', 'the7mk2' ); ?></span>
			</div>
		</a>
		<div class="websites-caption <?php echo esc_attr( $page_builder_class ); ?>">
			<div role="heading" class="websites-heading"><?php echo esc_html( $demo->title ); ?></div>
			<div class="websites-actions">
				<?php if ( $demo->partially_imported() ) : ?>
					<a href="#" class="button button-primary" data-action="keep" data-progress-text="<?php esc_attr_e( 'Keeping...', 'the7mk2' ); ?>" <?php echo esc_attr( $buttons_disabled ); ?>><?php esc_html_e( 'Keep content', 'the7mk2' ); ?></a>
					<a href="#" class="button" data-action="remove" data-progress-text="<?php esc_attr_e( 'Removing...', 'the7mk2' ); ?>" <?php echo esc_attr( $buttons_disabled ); ?>><?php esc_html_e( 'Remove content', 'the7mk2' ); ?></a>
				<?php else : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=the7-dashboard&action=demo_import' ) ); ?>">
						<?php wp_nonce_field( 'the7_import_demo' ); ?>
						<input type="hidden" name="demo_id" value="<?php echo esc_attr( $demo->id ); ?>">
						<button type="submit" name="import_type" value="full_import" class="button button-primary" <?php echo esc_attr( $buttons_disabled ); ?>>
							<?php esc_html_e( 'Import content', 'the7mk2' ); ?>
						</button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>

<?php endforeach; ?>

</div>
