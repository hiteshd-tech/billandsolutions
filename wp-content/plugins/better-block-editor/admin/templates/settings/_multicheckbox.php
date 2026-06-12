<?php defined( 'ABSPATH' ) || exit; ?>

<fieldset>
	<?php if ( ! empty($args['description']) ) : ?>
		<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
	<?php endif; ?>

	<?php foreach ( $args['options'] as $key => $value ) :
		$disabled = false;
		$label = '';
		if ( is_array( $value ) ) {
			$label = $value['label'] ?? $key;
			$disabled = ! empty( $value['disabled'] );
		} else {
			$label = $value;
		}
		$checked = !empty($args['value'][$key]);
		?>
		<label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( $args['identifier'] . '[' . $key . ']' ); ?>"
				value="1"
				<?php disabled( $disabled ); ?>
				<?php checked( $checked ); ?>
			/>
			<?php echo esc_html( $label ); ?>
		</label><br>
	<?php endforeach; ?>
</fieldset>
