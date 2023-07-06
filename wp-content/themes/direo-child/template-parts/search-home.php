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



<section class="intro-wrapper <?php echo ( !has_post_thumbnail() ) ? 'home-gradiant' : ''; ?> ">

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

						<div class="directorist-search-contents">

							<div class="directorist-container-fluid">

								<div class="directorist-search-top">

									<h2 class="directorist-search-top__title"><?php _e( 'Welcome To Find 420', 'directorist' ); ?></h2>

									<p class="directorist-search-top__subtitle"><?php _e( 'Connect, Learn, and Grow with the Ultimate Cannabis Networking App - Your one-stop place for all your Marijuana needs!', 'directorist' ); ?></p>

								</div>

								<form action="search-location" id="directorist-search-form-home" class="directorist-search-form" method="POST">

									<div class="directorist-search-form-wrap directorist-no-search-border">

										<div class="directorist-search-form-box-wrap">

											<div class="directorist-search-form-box">

												<div class="directorist-search-form-top directorist-flex directorist-align-center directorist-search-form-inline">

													<div class="directorist-search-field directorist-form-group directorist-search-query directorist-form-group directorist-form-address-field">

														<input type="text" id="address" name="location" class="directorist-form-element directorist-location-js" placeholder="Search a location" required="">

														<input type="hidden" name="manual_lat" id="manual_lat" />

														<input type="hidden" name="manual_lng" id="manual_lng" />

														<div class="address_result"><ul></ul></div>

													</div>

													<div class="directorist-search-form-action">

														<div class="directorist-search-form-action__submit">

															<button type="submit" id="search-location-submit" class="directorist-btn directorist-btn-lg directorist-btn-dark directorist-btn-search"><span class="la la-search"></span><?php _e( 'Map', 'directorist' ); ?></button>

														</div>

													</div>

												</div>

											</div>

										</div>

									</div>

								</form>

							</div>

						</div>

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

