<?php
/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */


if ( has_post_thumbnail() && ! post_password_required() ) { ?>
	<figure>
		<?php the_post_thumbnail( 'direo_blog' ); ?>
	</figure>
	<?php
}
