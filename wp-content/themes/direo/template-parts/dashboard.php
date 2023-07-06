<?php
/**
 * Template Name: Dashboard
 *
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

get_header();

echo ! is_user_logged_in() ? "<script>window.location='" . esc_url( ATBDP_Permalink::get_login_page_url() ) . "'</script>" : '';
echo ! class_exists('Directorist_Base') ? "<script>window.location='" . esc_url( home_url() ) . "'</script>" : '';
?>

<div id="wrapper" class="page-wrapper chiller-theme toggled">
	<?php echo do_shortcode('[directorist_user_dashboard]'); ?>
</div>

<?php
$footer = get_post_meta( direo_page_id(), 'footer_style', true );
if ( ! $footer || 'light' == $footer ) {
	$logo_id = attachment_url_to_postid( get_theme_mod( 'footer_logo' ) );
	$logo    = wp_get_attachment_image_src( $logo_id, 'full' );
} else {
	$logo_id = get_theme_mod( 'custom_logo' );
	$logo    = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
}
?>

<footer class="footer-three footer-<?php echo esc_attr( $footer ); ?>">
	<div class="footer-bottom">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<div class="footer-bottom--content">
						<?php
						if ( $logo ) {
							printf( '<a href="%s" class="footer-logo"><img src="%s" alt="%s"></a>', esc_url( home_url( '/' ) ), esc_url( $logo[0] ), direo_get_image_alt( $logo_id ) );
						}
						?>
						<div class="copyr-content">
							<?php
							$default    = '©' . date( 'Y' ) . ' direo. Made with <span class="la la-heart-o"></span> by <a href="#">WpWax</a>';
							$copy_right = get_theme_mod( 'copy_right', $default );
							echo wpautop( wp_kses_post( $copy_right ) );
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
