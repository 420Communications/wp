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

<div class="modal fade" id="submit_scammer_modal" tabindex="-1" role="dialog" aria-labelledby="submit_scammer_modal_label" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="login_modal_label">Submit New Scammer</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<p class="direo-info"><?php echo esc_attr_x( 'Please search in the list to see if already added in the list.', 'placeholder', 'direo' ); ?></p>
				<p class="direo-message"></p>
				<form action="add-scammer" id="add-scammer" method="post">
					<div class="form-group">
						<input type="text" class="form-control" id="direo-scammer-name" name="scammer_name" placeholder="<?php echo esc_attr_x( 'Scammer name', 'placeholder', 'direo' ); ?>" required>
					</div>
					<div class="form-group">
						<input type="text" class="form-control" id="direo-scammer-region" name="scammer_region" placeholder="<?php echo esc_attr_x( 'Scammer Region', 'placeholder', 'direo' ); ?>" required>
					</div>
					<div class="form-group">
						<textarea class="form-control" id="direo-known-information" name="known_information" placeholder="<?php echo esc_attr_x( 'Known information', 'placeholder', 'direo' ); ?>" required></textarea>
					</div>
					<div class="form-group">
						<textarea class="form-control" id="direo-comments" name="comments" placeholder="<?php echo esc_attr_x( 'Comments', 'placeholder', 'direo' ); ?>" required></textarea>
					</div>
					<button class="btn btn-block btn-lg btn-gradient btn-gradient-two add-scammer-submit" type="submit" name="submit"><?php echo esc_attr_x( 'Add Scammer', 'placeholder', 'direo' ); ?></button>
				</form>
			</div>
		</div>
	</div>
</div>
<?php if ( is_front_page() || is_page( 'maps' ) ) : ?>
<script type="text/javascript" src="<?php echo site_url() . '/wp-content/plugins/directorist/assets/js/openstreet-map.min.js'; ?>"></script>
<?php endif; ?>
</body>
</html>