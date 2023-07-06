<?php
/**
 * Template Name: Listings With Map
 *
 * @author  WpWax
 * @since   2.7
 * @version 1.0
 */
?>

<!DOCTYPE html>
<html <?php language_attributes( '/languages' ); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php
	wp_body_open();
	get_template_part( 'template-parts/content-menu' );

	while ( have_posts() ) {
		the_post();
		the_content();
	}

	get_template_part( 'template-parts/login', 'register' );

	wp_footer();
	?>

</body>
</html>
