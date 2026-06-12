<?php defined( 'ABSPATH' ) || exit; ?>

<label for="<?php echo esc_attr( $args['identifier'] ); ?>">
	<?php if ( ! empty( $args['label'] ) ) : ?>
		<span><?php echo esc_html( $args['label'] ); ?></span>
	<?php endif; ?>
	<input
		type="text"
		id="<?php echo esc_attr( $args['identifier'] ); ?>"
		name="<?php echo esc_attr( $args['identifier'] ); ?>"
		value="<?php echo esc_attr( $args['value'] ?? '' ); ?>"
	/>
</label>

<?php if ( ! empty( $args['description'] ) ) : ?>
	<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
<?php endif; ?>
