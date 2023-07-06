<?php
/**
 * @author  wpWax
 * @since   1.5.0
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

extract( $field_data );
$value = isset( $_REQUEST['guest'] ) ? $_REQUEST['guest'] : '';
?>

<div class="directorist-search-field directorist-guest-number">

	<?php if ( ! empty( $label ) ) {
		printf( '<label>%s</label>', esc_html( $label ) );
	} ?>

	<div class="directorist-form-group">
		<input class="directorist-form-element" min="0" type="number" name="custom_field[guest]" value="<?php echo esc_html( $value ); ?>" placeholder="<?php echo esc_html( $placeholder ); ?>" step="1" <?php required( $required ); ?> />
	</div>

</div>