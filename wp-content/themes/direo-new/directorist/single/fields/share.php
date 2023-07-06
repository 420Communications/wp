<?php
/**
 * @author  wpWax
 * @since   6.7
 * @version 7.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="directorist-single-listing-action directorist-social-share">

	<?php directorist_icon( $icon );?> <?php esc_html_e( 'Share', 'direo' ); ?>

	<ul class="directorist-social-share-links">
		<?php foreach ( $listing->social_share_data() as $social ): ?>
			<li class="directorist-social-links__item">
				<a href="<?php echo esc_url( $social['link'] ? $social['link'] : '#' );?>" target="_blank">
					<?php directorist_icon( $social['icon'] ); ?>
					<?php echo esc_html( $social['title'] );?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

</div>