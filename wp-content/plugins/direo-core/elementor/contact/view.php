<?php
/**
 * Description.
 *
 * @package WordPress
 * @author  WpWax
 * @since   1.0
 * @version 1.0
 */

$title   = $settings['title'];
$items   = $settings['items'];
$socials = $settings['socials'];
?>

<div class="contact_form_widget contact_page_widget widget atbd_widget widget-card">

	<?php if ( $title ) { ?>
		<div class="atbd_widget_title"><h4><span class="la la-phone"></span><?php echo esc_attr( $title ); ?></h4></div>
	<?php } ?>

	<div class="widget-body atbd_author_info_widget">
		<?php if ( $items ) { ?>
			<div class="atbd_widget_contact_info">
				<ul>
					<?php
					foreach ( $items as $item ) {
						$title = $item['title'];
						$icon  = $item['icon'];
						?>
						<li>
							<span class="la <?php echo esc_attr( $icon ); ?>"></span>
							<span class="atbd_info"><?php echo esc_attr( $title ); ?></span>
						</li>
						<?php
					}
					wp_reset_postdata();
					?>
				</ul>
			</div>
		<?php } ?>

		<?php if ( $socials ) {	?>
			<div class="atbd_social_wrap">
				<?php
				foreach ( $socials as $social ) {
					$url  = $social['url'] ? $social['url']['url'] : '';
					$icon = $social['icon'];
					if ( $title ) {
						?>
						<p><a href="<?php echo esc_url( $url ); ?>"><span class="fab <?php echo esc_attr( $icon ); ?>"></span></a></p>
						<?php
					}
				}
				?>
			</div>
		<?php }	?>
	</div>
</div>