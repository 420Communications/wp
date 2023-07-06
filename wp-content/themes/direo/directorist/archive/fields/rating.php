<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Return early when review is disabled.
if ( ! directorist_is_review_enabled() ) {
	return;
}
?>

<span class="directorist-info-item directorist-rating-meta directorist-rating-transparent">
    <span class="directorist-rating-avg">
        <?php echo esc_html( $listings->loop['review']['average_reviews'] ); ?>
        <i class="far fa-star"></i>
    </span>
</span>
