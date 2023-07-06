<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 7.3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="directorist-single-listing-action directorist-action-save directorist-tooltip" data-label="Show Chat History" id="show-chat-history-btn"><i class="directorist-icon-mask" aria-hidden="true" style="--directorist-icon: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/img/comments.svg')"></i><a href="void(0)" class="show-chat-history"></a></div>

<div class="directorist-single-listing-action directorist-action-save directorist-tooltip" data-label="<?php esc_attr_e('Favorite', 'directorist'); ?>" id="atbdp-favourites"><?php echo wp_kses_post( the_atbdp_favourites_link() ); ?></div>