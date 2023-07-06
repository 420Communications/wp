<?php
/**
 * Template Name: Search Home
 *
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

get_header();
?>

<section class="intro-wrapper bgimage overlay overlay--dark">
	<?php if ( has_post_thumbnail() ) { ?>
		<div class="bg_image_holder">
			<?php echo sprintf( '<img src="%s" alt="%s">', esc_url( get_the_post_thumbnail_url() ), direo_get_image_alt( get_post_thumbnail_id( get_the_ID() ) ) ); ?>
		</div>
	<?php } ?>

	<?php if ( is_Directorist() ) { ?>
		<div class="directory_content_area direo-search-home">
			<div class="container">
				<div class="row">
					<div class="col-lg-10 offset-lg-1" id="directorist">
						<?php echo do_shortcode( '[directorist_search_listing]' ); ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
</section>

<?php
if ( is_elements() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
	wp_reset_postdata();
} else {
	while ( have_posts() ) {
		the_post();
		?>
		<section class="search-home-area section-padding-strict">
			<div class="container">
				<div class="row">
					<div class="col-md-12">

						<?php

						the_content();

						direo_page_pagination();

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
