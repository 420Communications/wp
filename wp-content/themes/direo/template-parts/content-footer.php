<?php
/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) {
	return;
}

$default      = '©' . date( 'Y' ) . ' Direo. Made with <span class="la la-heart-o"></span> by <a href="#">WpWax</a>';
$footer_style = get_post_meta( direo_page_id(), 'footer_style', true );
$copy_right   = get_theme_mod( 'copy_right', $default );
$numbers      = range( 1, 4, 1 );

if ( ! $footer_style || 'light' === $footer_style ) {
	$logo_id = attachment_url_to_postid( get_theme_mod( 'footer_logo' ) );
	$logo    = wp_get_attachment_image_src( $logo_id, 'full' );
} else {
	$logo_id = get_theme_mod( 'custom_logo' );
	$logo    = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
}
?>

<footer class="footer-three footer-<?php echo esc_attr( $footer_style ); ?>">
	<?php
	if ( 'footer-hide' !== $footer_style ) {
		if ( is_active_sidebar( 'footer_sidebar_1' ) || is_active_sidebar( 'footer_sidebar_2' ) || is_active_sidebar( 'footer_sidebar_3' ) || is_active_sidebar( 'footer_sidebar_4' ) ) {
			?>
			<div class="footer-top p-top-95">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="footer-widgets p-bottom-25">
								<div class="row">
									<?php
									foreach ( $numbers as $number ) {
										if ( is_active_sidebar( 'footer_sidebar_' . $number ) ) {
											?>
											<div class="col-lg-3 col-sm-6">
												<?php dynamic_sidebar( 'footer_sidebar_' . $number ); ?>
											</div>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}
	?>

	<div class="footer-bottom">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="footer-bottom--content">
						<?php
						echo ! empty( $logo ) ? sprintf( '<a href="%s" class="footer-logo"><img src="%s" alt="%s"></a>', esc_url( home_url( '/' ) ), esc_url( $logo[0] ), direo_get_image_alt( $logo_id ) ) : '';
						?>
						<div class="copyr-content"> <?php echo apply_filters( 'get_the_content', $copy_right ); ?> </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</footer>
