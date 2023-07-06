<?php

/**

 * Template Name: Not Allowed

 * @author  WpWax

 * @since   1.10.05

 * @version 1.0

 */



get_header();



$title = get_theme_mod( '404_title', __( "Currently, location cannot be added. Make sure your subscription is active or that you've previously provided location information.", 'direo' ) );

$desc  = get_theme_mod( '404_desc' ); ?>



<section class="section-padding-strict">

	<div class="container">

		<div class="row">

			<div class="col-lg-12">

				<div class="error-contents text-center">

					<h2 class="m-bottom-15"><?php echo esc_attr( $title ); ?></h2>

					<?php echo apply_filters( 'the_content', $desc ); ?>

				</div>

			</div>

		</div>

	</div>

</section>



<?php



get_footer();

