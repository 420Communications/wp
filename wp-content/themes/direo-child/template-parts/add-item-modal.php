<div class="modal fade" id="add_new_item_modal" tabindex="-1" role="dialog" aria-labelledby="add_new_item_modal_label" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="login_modal_label">Add New Product</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<p class="direo-message"></p>
				<form action="add_user_item" id="add-edit-item" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="product-title" name="product_title" placeholder="<?php echo esc_attr_x( 'Product / Strain Name', 'placeholder', 'direo' ); ?>" required>
					</div>
					<div class="form-group">
						<textarea class="form-control" id="product-description" name="product_description" placeholder="<?php echo esc_attr_x( 'Amounts you offer + pricing', 'placeholder', 'direo' ); ?>" required></textarea>
					</div>
					<div class="form-group product-cat">
						<label><?php echo esc_attr_x( 'Categories', 'placeholder', 'direo' ); ?></label>
						<ul>
							<?php
								$categories = get_categories(array(
									'hide_empty' => 0,
									'taxonomy'   => 'product_cat',
									'exclude' 	 => array(93, 94)
								));

								foreach ($categories as $key => $value) {
									?>
										<li><input type="checkbox" name="product_category[]" value="<?php echo $value->cat_ID; ?>"> <?php echo $value->name; ?></li>
									<?php
								}
							?>
							</ul>
					</div>
					<div class="form-group">
						<input type="hidden" name="featured_image">
						<button class="btn btn-block btn-lg" type="button" id="upload-featured-image" name="upload-featured-image"><?php echo esc_attr_x( 'Upload Featured Image', 'placeholder', 'direo' ); ?></button>
					</div>
					<div class="form-group">
						<input type="hidden" name="gallery_images">
						<button class="btn btn-block btn-lg" type="button" id="upload-gallery-images" name="upload-gallery-images"><?php echo esc_attr_x( 'Upload Gallery Images', 'placeholder', 'direo' ); ?></button>
					</div>
					<input type="hidden" name="product_id" />
					<input type="hidden" name="action" value="add" />
					<button class="btn btn-block btn-lg btn-gradient btn-gradient-two add-new-item-submit" type="submit" name="submit"><?php echo esc_attr_x( 'Add Item', 'placeholder', 'direo' ); ?></button>
				</form>
			</div>
		</div>
	</div>
</div>