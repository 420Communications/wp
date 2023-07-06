<?php
use \Directorist\Directorist_Listing_Search_Form;
use Directorist\Helper;

/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

$banner	= get_post_meta( direo_page_id(), 'banner_style', true );
?>

<div class="breadcrumb-wrapper content_above">
	<div class="container">
		<div class="row">
			<div class="col-lg-12">

				<?php if ( 'search' == $banner ) { ?>

					<h2 class="page-title m-bottom-30">

						<?php
						if ( direo_directorist_pages( 'search_result_page' ) ) {
							echo function_exists( 'direo_listing_search_title' ) ? direo_listing_search_title( 'Results For: ' ) : get_the_title();
						} else {
							echo get_the_title();
						}
						?>

					</h2>
					
					<div class="atbd_wrapper ads-advaced--wrapper">
						<div class="row">
							<div class="col-lg-10 offset-lg-1 quick-search atbd_wrapper">
								<?php echo do_shortcode( '[directorist_search_listing show_title_subtitle="no" search_button="yes" more_filters_button="no" show_popular_category="no"]' ); ?>
							</div>
						</div>
					</div>

					<?php
				} else { ?>

					<h1 class="page-title"><?php echo direo_get_page_title(); ?></h1>

					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item">
								<a href="<?php echo esc_url( home_url() ); ?>">
									<?php esc_html_e( 'Home', 'direo' ); ?>
								</a>
							</li>
							
							<li class="breadcrumb-item active">
								<?php echo direo_get_page_title(); ?>
							</li>
						</ol>
					</nav>

				<?php } ?>

			</div>
		</div>
	</div>
</div>