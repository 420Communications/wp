<?php
/**
 * Description.
 *
 * @package WordPress
 * @author  WpWax
 * @since   1.0
 * @version 1.0
 */

$title1 = $settings['title1'];
$texts  = $settings['texts'];
$title3 = $settings['title3'];
$imgs   = $settings['img'];
$img    = $imgs['url'] ? $imgs['url'] : '';
$alt    = $imgs['id'] ? $imgs['id'] : $title1;
$class  = $img ? 'col-lg-5 offset-lg-2' : 'col-md-12 text-center';
$btn    = $settings['btn_url'];
$attr   = '';
if ( ! empty( $btn['url'] ) ) {
	$attr  = 'href="' . $settings['btn_url']['url'] . '"';
	$attr .= ! empty( $settings['btn_url']['is_external'] ) ? ' target="_blank"' : '';
	$attr .= ! empty( $settings['btn_url']['nofollow'] ) ? ' rel="nofollow"' : '';
}
?>

<section class="cta-wrapper">
	<div class="bg_image_holder"><img src="<?php echo get_template_directory_uri(); ?>/img/cta-bg.png" alt=""></div>
	<div class="container content_above">
		<div class="row align-items-center">
			<div class="<?php echo esc_attr( $class ); ?> col-md-7">
				<div id="typed-strings">
					<?php
					if ( $texts ) {
						foreach ( $texts as $text ) {
							echo sprintf( '<p>%s</p>', $text['title2'] );
						}
					}
					?>
				</div>
				<h1><?php echo esc_attr( $title1 ); ?> <span class="text-changeable"></span> <br> <?php echo esc_attr( $title3 ); ?></h1>
				<?php if ( $settings['btn'] ) { ?>
					<a <?php echo wp_kses_post( $attr ); ?> class="btn-gradient btn-gradient-two"><?php echo esc_attr( $settings['btn'] ); ?></a>
				<?php } ?>
			</div>
			<?php if ( $img ) { ?>
				<div class="col-lg-5 col-md-5 text-right">
					<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( direo_get_image_alt( $alt ) ); ?>">
				</div>
			<?php } ?>
		</div>
	</div>
</section>
