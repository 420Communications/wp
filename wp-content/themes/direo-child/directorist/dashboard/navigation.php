<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.0.3.3
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$counter = 1;
$blockTabsForSeller = array('dashboard_announcement', 'order_history', 'packages', 'dashboard_my_listings');
$blockTabsForBuyer = array('packages', 'dashboard_announcement', 'order_history', 'live_chat');
$diero_has_unlimited_msg = diero_has_subscription_for_unlimited_messages(get_current_user_id());
$user_type = diero_get_current_user_meta( get_current_user_id(), '_user_type');
$messageCount = diero_get_current_user_meta( get_current_user_id(), 'messages_available');

if(!empty($diero_has_unlimited_msg) || current_user_can('administrator') ) {
	$messageCount = 'Unlimited';
	$messageCountLabel = '';
} else {
	$messageCount = ( !empty($messageCount) ) ? $messageCount : 0;
	$messageCountLabel = '<span class="diero-messages-left">messages left</span>';
}


?>

<div class="directorist-user-dashboard__nav directorist-tab__nav">
	<span class="directorist-dashboard__nav--close"><i class="<?php atbdp_icon_type( true ); ?>-times"></i></span>
	<div class="directorist-tab__nav__wrapper">

		<ul class="directorist-tab__nav__items">

			<?php foreach ( $dashboard->dashboard_tabs() as $key => $value ) { if( ( ( $user_type == 'author' || $user_type == 'dispensary') && !in_array($key, $blockTabsForSeller) && diero_user_has_access() == true ) || current_user_can('administrator') ) { ?>

				<li class="directorist-tab__nav__item">
					<a href="#" class="directorist-booking-nav-link directorist-tab__nav__link <?php echo ( $counter == 1 ) ? 'directorist-tab__nav__active' : ''; ?>" target="<?php echo esc_attr( $key ); ?>">
						<span class="directorist_menuItem-text">
							<span class="directorist_menuItem-icon">
								<i class="<?php echo esc_attr( $value['icon'] ); ?>"></i>
							</span>
							<?php 
							if( $key == 'live_chat' )  {
								$navTitle = 'Chats';
							} else if( $key == 'dashboard_my_listings' )  {
								$navTitle = 'Edit Location';
							} else {
								$navTitle = $value['title'];
							}

							echo wp_kses_post( $navTitle ); ?>
							<?php echo ( $key == 'live_chat') ? '<span class="diero-message-limit">('. $messageCount .')</span> ' . $messageCountLabel : '' ; ?>
						</span>
					</a>
				</li>

				<?php do_action( 'directorist_dashboard_navigation', $key, $dashboard ); ?>
				<?php $counter++; ?>

				<?php  } else if ( ( ( $user_type == 'general' || $user_type == 'become_author' ) && !in_array($key, $blockTabsForBuyer) ) || current_user_can('administrator') ) { ?>

				<li class="directorist-tab__nav__item">
					<a href="#" class="directorist-booking-nav-link directorist-tab__nav__link <?php echo ( $counter == 1 ) ? 'directorist-tab__nav__active' : ''; ?>" target="<?php echo esc_attr( $key ); ?>">
						<span class="directorist_menuItem-text">
							<span class="directorist_menuItem-icon">
								<i class="<?php echo esc_attr( $value['icon'] ); ?>"></i>
							</span>
							<?php echo wp_kses_post( $value['title'] ); ?>
						</span>
					</a>
				</li>

				<?php do_action( 'directorist_dashboard_navigation', $key, $dashboard ); ?>
				<?php $counter++; ?>

			<?php } } ?>

			<?php if($user_type == 'author' || $user_type == 'dispensary' || current_user_can('administrator') ) { ?>

			<?php if(!empty(diero_has_subscription()) || current_user_can('administrator') ) { ?>

			<li class="directorist-tab__nav__item">
				<a href="#" class="directorist-booking-nav-link directorist-tab__nav__link <?php echo ( $counter == 1 ) ? 'directorist-tab__nav__active' : ''; ?>" target="add_items">
					<span class="directorist_menuItem-text">
						<span class="directorist_menuItem-icon">
							<i class="la la-sitemap"></i>
						</span>
						<?php _e( 'Your Items', 'directorist'); ?>
					</span>
				</a>
			</li>
			
			<li class="directorist-tab__nav__item">
				<a href="#" class="directorist-booking-nav-link directorist-tab__nav__link <?php echo ( $counter == 1 ) ? 'directorist-tab__nav__active' : ''; ?>" target="wc_subscription">
					<span class="directorist_menuItem-text">
						<span class="directorist_menuItem-icon">
							<i class="la la-money-bill"></i>
						</span>
						<?php _e( 'Active Subscription', 'directorist'); ?>
					</span>
				</a>
			</li>


			<?php } ?>

			<li class="directorist-tab__nav__item">
				<a href="#" class="directorist-booking-nav-link directorist-tab__nav__link <?php echo ( $counter == 1 ) ? 'directorist-tab__nav__active' : ''; ?>" target="wc_buy_subscription">
					<span class="directorist_menuItem-text">
						<span class="directorist_menuItem-icon">
							<i class="la la-money-bill"></i>
						</span>
						<?php _e( 'Buy Subscription', 'directorist'); ?>
					</span>
				</a>
			</li>

			<?php } ?>

		</ul>

	</div>

	<?php $dashboard->nav_buttons_template(); ?>

</div>