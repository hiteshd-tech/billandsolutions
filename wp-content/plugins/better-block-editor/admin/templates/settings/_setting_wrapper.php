<?php defined( 'ABSPATH' ) || exit; ?>

<?php
$tab   = $args['tab'] ?? '';
$class = $args['class'] ?? '';
$module_identifier = $args['module'] ?? '';
$template = $args['template'] ?? '';
$template_args = $args['template_args'] ?? [];
?>

<div class="wpbbe-setting <?php echo esc_attr($class); ?>"
     data-module="<?php echo esc_attr($module_identifier); ?>"
     data-tab="<?php echo esc_attr($tab); ?>">

	<?php
	// render inner template
	if (!empty($template)) {
		BetterBlockEditor\Core\Settings::parse_template($template, $template_args);
	}
	?>

</div>
