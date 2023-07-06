<?php
/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

get_header();

if ( is_elements() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
} else {
	while ( have_posts() ) {
		the_post(); ?>
		<section class="page-area section-padding-strict section-bg">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="">
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
			</div>
		</section>
		<?php
	}
}

get_footer();
