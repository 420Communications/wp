<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 7.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$user_type = get_user_meta( $listing->post->post_author, '_user_type', true );
$has_subscription = diero_has_subscription_for_larger_map($listing->post->post_author);

if( !empty($has_subscription) ) {
	$largerIcon = ' larger-icon';
} else {
	$largerIcon = '';
}
				
if ( 'author' == $user_type ) {
	$cat_icon = 'seller-icon' . $largerIcon;
} else if ( 'general' == $user_type || 'become_author' == $user_type ) {
	$cat_icon = 'dispensary-icon';
} else if ( 'dispensary' == $user_type ) {
	$cat_icon = 'dispensary-icon' . $largerIcon;
} else {
	$cat_icon = 'dispensary-icon';
}

?>

<div class="directorist-single-map <?php echo $cat_icon; ?>" data-map="<?php echo esc_attr( $listing->map_data() ); ?>"></div>

<div>
<h4 class="directorist-card__header--title"><?php _e( 'Menu', 'directorist'); ?></h4><br>
<?php echo do_shortcode('[products author='. $listing->post->post_author .']'); ?>
</div>