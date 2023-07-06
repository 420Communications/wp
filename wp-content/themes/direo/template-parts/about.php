<?php
/**
 * Template Name: About Page
 *
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

get_header();

$header_title = get_theme_mod( 'about_title', esc_html__( 'Place your Business or Explore Anything what you want', 'direo' ) );
$video        = get_theme_mod( 'video', 'https://www.youtube.com/watch?v=0C4fX_x_Vsg' );
$btn          = get_theme_mod( 'btn', esc_html__( 'Play our video', 'direo' ) );
?>

<section class="about-wrapper bg-gradient-ps">
	<div class="about-intro content_above">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-5 col-md-6">
					<?php
					echo ! empty( $header_title ) ? sprintf( '<h1 id="header_title">%s</h1>', esc_attr( $header_title ) ) : '';
					echo ! empty( $btn ) && $video ? sprintf( '<a href="%s" class="video-iframe play-btn-two"> <span class="icon"><i class="la la-youtube-play"></i></span> <span>%s</span> </a>', esc_url( $video ), esc_attr( $btn ) ) : '';
					?>
				</div>
				<?php
				echo has_post_thumbnail() ? sprintf( '<div class="col-lg-6 offset-lg-1 col-md-6 offset-md-0 col-sm-8 offset-sm-2">%s</div>', get_the_post_thumbnail() ) : '';
				?>
			</div>
		</div>
	</div>
</section>

<?php
if ( is_elements() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
} else {
	while ( have_posts() ) {
		the_post();
		?>
		<section class="blog-area section-padding-strict">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<?php
						the_content();
						wp_link_pages(
							array(
								'before'   => '<div class="m-top-50"><nav class="navigation pagination d-flex justify-content-center" role="navigation"><div class="nav-links">',
								'after'    => '</div></nav></div>',
								'pagelink' => '<span class="page-numbers">%</span>',
							)
						);

						if ( comments_open() ) {
							comments_template();
						}
						?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
get_footer();
