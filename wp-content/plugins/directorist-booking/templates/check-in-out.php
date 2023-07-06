<?php
/**
 * @author  wpWax
 * @since   1.5.0
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ){
	exit;
};

$value_checkin  = isset( $_REQUEST['custom_field']['directorist-booking-check-in'] ) ? $_REQUEST['custom_field']['directorist-booking-check-in'] : '';
$value_checkout = isset( $_REQUEST['custom_field']['directorist-booking-check-out'] ) ? $_REQUEST['custom_field']['directorist-booking-check-out'] : '';

extract( $field_data );
?>

<div class="directorist-search-field directorist-check-in-check-out">

	<div class="directorist-form-group">
		<div class="directorist-booking-entry">
			<input class="directorist-booking-entry__data directorist-form-element directorist-booking-check-in" type="text" name="custom_field[directorist-booking-check-in]" value="<?php echo esc_html( $value_checkin ); ?>" placeholder="<?php echo esc_html( $label ); ?>" <?php required( $required ); ?> />
			<input class="directorist-booking-entry__data directorist-form-element directorist-booking-check-out" type="text" name="custom_field[directorist-booking-check-out]" value="<?php echo esc_html( $value_checkout ); ?>" placeholder="<?php echo esc_html( $label_2 ); ?>" <?php required( $required ); ?> />
		</div>
	</div>

</div>