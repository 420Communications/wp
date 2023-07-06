<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.0.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$field_key  = isset( $data['field_key'] ) ? $data['field_key'] : 'custom-radio';
$field_name = str_replace( array( 'custom-', '-' ), array( '', '_' ), $field_key );
?>

<?php if ( direo_has_field_in_search_form( $field_name, $searchform, 'advance' ) ): ?>

	<div class="directorist-search-field">

		<?php if ( ! empty( $data['label'] ) ): ?>
			<label><?php echo esc_html( $data['label'] ); ?></label>
		<?php endif;?>

		<?php foreach ( $data['options'] as $option ) {
			$uniqid = $option['option_value'] . '-' . wp_rand(); ?>

			<div class="directorist-radio directorist-radio-circle">
				<input <?php checked( $value === $option['option_value'] );?> type="radio" id="<?php echo esc_attr( $uniqid ); ?>" name="custom_field[<?php echo esc_attr( $data['field_key'] ); ?>]" value="<?php echo esc_attr( $option['option_value'] ); ?>">
				<label class="directorist-radio__label" for="<?php echo esc_attr( $uniqid ); ?>"><?php echo esc_html( $option['option_label'] ); ?></label>
			</div>

		<?php }?>

	</div>

<?php else: ?>

	<div class="directorist-search-field">

		<div class="directorist-select">

			<select name="custom_field[<?php echo esc_attr( $data['field_key'] ); ?>]" data-placeholder="<?php echo esc_attr( $data['label'] ); ?>" <?php echo ! empty( $data['required'] ) ? 'required="required"' : ''; ?> data-isSearch="true">

				<?php
				printf( '<option value="">%s</option>', esc_html( $data['label'] ) );

				foreach ( $data['options'] as $option ) {
					printf( '<option value="%s" %s>%s</option>', $option['option_value'], checked( $value === $option['option_value'] ), esc_html( $option['option_label'] ) );
				}
				?>

			</select>

		</div>

	</div>

<?php endif;?>