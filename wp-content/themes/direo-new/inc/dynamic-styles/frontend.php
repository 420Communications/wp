<?php
/**
 * @author  wpWax
 * @since   1.0
 * @version 1.0
 */

$primary   = get_theme_mod( 'p_color', '#ff367e' );
$secondary = get_theme_mod( 's_color', '#903af9' );
$success   = get_theme_mod( 'su_color', '#32cc6f' );
$info      = get_theme_mod( 'in_color', '#3a7dfd' );
$danger    = get_theme_mod( 'dn_color', '#fd4868' );
$warning   = get_theme_mod( 'wr_color', '#fa8b0c' );
?>

<?php
/*
==============================
	CSS Variables
===============================*/
?>

:root {
	--color-primary: <?php echo esc_attr( $primary ); ?>;
	--color-primary-rgb: <?php echo esc_attr( direo_hex2rgb( $primary ) ); ?>;

	--color-secondary: <?php echo esc_attr( $secondary ); ?>;
	--color-secondary-rgb: <?php echo esc_attr( direo_hex2rgb( $secondary ) ); ?>;

	--color-success: <?php echo esc_attr( $success ); ?>;
	--color-info: <?php echo esc_attr( $info ); ?>;
	--color-danger: <?php echo esc_attr( $danger ); ?>;
	--color-warning: <?php echo esc_attr( $warning ); ?>;
}