<div class="atbd_tab_inner" id="manage_fees">
    <div class="atbd_manage_fees_wrapper">
    	<h3 class="items-table-title"><?php _e( 'Your Products', 'subscriptions-for-woocommerce'); ?>
    		<?php if( 1 == 2) { ?>
    		<a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="btn btn-sm btn-icon btn-gradient btn-gradient-two icon-left"><?php _e( 'Add New Product', 'subscriptions-for-woocommerce'); ?></a>
    		<?php } ?>
    		<a href="javascript:void(0)" id="open-manage-item-modal" class="btn btn-sm btn-icon btn-gradient btn-gradient-two icon-left"><?php _e( 'Add New Product', 'subscriptions-for-woocommerce'); ?></a>
    	</h3>
		<table class="wps_subscriptions-table">
			<thead>
				<tr>
					<th><?php _e( 'ID', 'subscriptions-for-woocommerce'); ?></th>
					<th><?php _e( 'Title', 'subscriptions-for-woocommerce'); ?></th>
					<th><?php _e( 'Gallery Images', 'subscriptions-for-woocommerce'); ?></th>
					<th><?php _e( 'Actions', 'subscriptions-for-woocommerce'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
					$userItems = get_posts( array(
					    'post_type'   => 'product',
					    'numberposts' => -1,
					    'post_status' => 'publish',
					    'author'      =>  get_current_user_id(),
				    ));

				    if(!empty($userItems)) {
				    	$i = 1; foreach ($userItems as $key => $value) {
				    		$product 		  		= new WC_Product($value->ID);
				    		$product_details  		= $product->get_data();
				    		$product_categories_ids = wc_get_product_term_ids( $value->ID, 'product_cat' );
				    		$product_gallery_images = get_post_meta($value->ID, '_product_image_gallery', true);
				    		$user_item_images 		= explode(",", $product_gallery_images);
				?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><a href="<?php echo get_permalink($value->ID); ?>" target="_blank"><?php echo $value->post_title; ?></a></td>
					<td><div class="selected-item-images">
						<?php
							foreach ($user_item_images as $item_key => $item_value) {
								if( !empty($item_value) ) {
								$imageURL = wp_get_attachment_image_url( $item_value, 'thumbnail' );
								$fullImageURL = wp_get_attachment_image_url( $item_value, 'full' );
								?>
								<div class="image_wrapper">
									<a href="<?php echo $fullImageURL; ?>" target="_blank"><img src="<?php echo $imageURL; ?>"></a>
									<a class="image_wrapper_remove" href="javascript:void(0)" data-id="<?php echo $item_value; ?>" data-postId="<?php echo $value->ID; ?>">Remove</a>
								</div>
								<?php
								}
							}
						?>
						</div>
					</td>
					<td><a href="javascript:void(0)" class="edit-user-item" data-id="<?php echo $value->ID; ?>" data-title="<?php echo $value->post_title; ?>" data-desc="<?php echo $product_details['description'] ?>" data-cat="<?php echo implode(",", $product_categories_ids); ?>" data-featured-image="<?php echo get_post_thumbnail_id($value->ID); ?>" data-gallery-images="<?php echo $product_gallery_images; ?>"><?php _e( 'Edit', 'subscriptions-for-woocommerce'); ?></a> | <a href="javascript:void(0)" class="delete-user-item" data-action="delete" data-id="<?php echo $value->ID; ?>"><?php _e( 'Delete', 'subscriptions-for-woocommerce'); ?></a></td>
				</tr>
				<?php $i++; } } else { ?>
					<tr>
						<td colspan="3" align="center"><?php _e( 'No record found', 'subscriptions-for-woocommerce'); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>