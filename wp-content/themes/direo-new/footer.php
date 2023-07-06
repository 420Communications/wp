<?php
/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

if ( ! changed_header_footer() ) {
	get_template_part( 'template-parts/content', 'footer' );
}

wp_footer();
?>

</body>
</html>
