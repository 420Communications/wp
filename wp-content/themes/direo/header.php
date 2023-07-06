<!DOCTYPE html>

<html <?php language_attributes( '/languages' ); ?>>

<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<?php wp_head(); ?>

</head>



<?php

$template   = basename( get_page_template() );

if ( ( 'dashboard.php' === $template ) || ( 'search-home.php' === $template ) || ( 'about.php' === $template ) ) {

	$template = true;

} else {

	$template = false;

}

?>



<body <?php body_class(); ?>>



	<?php

	wp_body_open();

	

	if ( changed_header_footer() || $template ) {

		get_template_part( 'template-parts/content-menu' );

	} else {

		get_template_part( 'template-parts/content-menu' );

		if ( ! is_singular( 'at_biz_dir' ) ) {

			$banner = get_post_meta( direo_page_id(), 'banner_style', true );

			$banner = ( 'banner_off' !== $banner ) || empty( $banner ) || is_search() ? true : false;

			if ( $banner || 'menu1' === direo_menu_style() ) {

				?>

				<section class="header-breadcrumb bgimage overlay overlay--dark">

					<?php direo_header_background(); ?>

					<?php $banner ? get_template_part( 'template-parts/common/breadcrumb' ) : ''; ?>

				</section>

				<?php

			}

		}

	}

