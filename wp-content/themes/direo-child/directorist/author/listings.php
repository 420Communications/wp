<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.3.1
 */

use \Directorist\Helper;

if ( ! defined( 'ABSPATH' ) ) exit;

if(1 == 2) {
?>

<div class="directorist-author-listing-top directorist-flex directorist-justify-content-between">
	<div>
		<h2 class="directorist-author-listing-top__title"><?php esc_html_e( 'My Items' , 'directorist'); ?></h2>
		<div class="directorist-author-listing-type">
		<?php $author->archive_type( $author ); ?>
		</div>
	</div>
</div>

<?php
	$userItems = get_posts( array(
	    'post_type' => 'user-items',
	    'numberposts' => -1,
	    'post_status' => 'publish',
	    'author'      =>  $author->id,
    ));
?>

<div class="directorist-author-listing-content">
    <div class="directorist-row">
        <div class="directorist-col-6">
        	<?php
        		if(!empty($userItems)) {
			    	$i = 1; foreach ($userItems as $key => $value) {
		    		$user_item_images = get_post_meta( $value->ID, '_user_item_images', true );
		    		$user_item_desc = get_post_meta( $value->ID, 'description', true );
		    		$user_item_images = explode(",",$user_item_images);
		    		?>
		    			<div class="directorist-listing-single directorist-listing-card directorist-listing-has-thumb">
			                <figure class="directorist-listing-single__thumb">
			                    <div class="directorist-thumb-bottom-left"></div>
			                    <div class="directorist-thumb-bottom-right"></div>
			                </figure>
			                <div class="directorist-listing-single__content">
			                    <div class="directorist-listing-single__info">
			                        <div class="directorist-listing-single__info--top">
			                            <h4 class="directorist-listing-title"><?php echo $value->post_title; ?></h4>
			                            <p><?php echo $user_item_desc; ?></p>
			                        </div>
			                        <div class="directorist-listing-single__info--excerpt">
			                        	<div class="selected-item-images">
										<?php
											foreach ($user_item_images as $item_key => $item_value) {
												if( !empty($item_value) ) {
												$imageURL = wp_get_attachment_image_url( $item_value, 'thumbnail' );
												$fullImageURL = wp_get_attachment_image_url( $item_value, 'full' );
												?>
												<div class="image_wrapper">
													<a href="<?php echo $fullImageURL; ?>" target="_blank"><img src="<?php echo $imageURL; ?>"></a>
												</div>
												<?php
												}
											}
										?>
										</div>
			                        </div>
			                    </div>
			                </div>
			            </div>
		    		<?php
					}
				}
			?>
        </div>
    </div>
</div>

<?php } ?>