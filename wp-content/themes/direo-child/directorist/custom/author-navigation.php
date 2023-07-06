<?php

/**

 * @author  wpWax

 * @since   1.0

 * @version 1.0

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



$counter         = 1;

$dashboard_url   = get_permalink( get_directorist_option( 'user_dashboard' ) );

$dashboard_links = direo_dashboard_tabs();

$add_listing_btn = get_theme_mod( 'add_listing_btn', 'Add Listing' );



$blockTabsForSeller = array('dashboard_announcement', 'dashboard_my_listings');

$blockTabsForBuyer = array('dashboard_my_listings', 'dashboard_announcement');

$user_type = get_user_meta( get_current_user_id(), '_user_type', true );

?>



<ul class="list-unstyled">



	<?php foreach ( $dashboard_links as $key => $value ) { if(  ( ( $user_type == 'author' || $user_type == 'dispensary' ) && !in_array($key, $blockTabsForSeller) && diero_user_has_access() == true ) || current_user_can('administrator') ) { ?>



		<li>

			<a href="<?php echo esc_url( $dashboard_url ) . '#active_' . $key; ?>">

				<span class="directorist_menuItem-text">

					<span class="directorist_menuItem-icon"> <i class="<?php echo esc_attr( $value['icon'] ); ?>"></i> </span>

					<?php 
					if( $key == 'live_chat' )  {
						$navTitle = 'Messages';
					} else if( $key == 'dashboard_my_listings' )  {
						$navTitle = 'Edit Location';
					} else {
						$navTitle = $value['title'];
					}

					echo wp_kses_post( $navTitle ); ?>

				</span>

			</a>

		</li>



		<?php $counter++; ?>



	<?php } else if( ( ( $user_type == 'general' || $user_type == 'become_author' ) && !in_array($key, $blockTabsForBuyer) ) || current_user_can('administrator') ) { ?>

		<li>

			<a href="<?php echo esc_url( $dashboard_url ) . '#active_' . $key; ?>">

				<span class="directorist_menuItem-text">

					<span class="directorist_menuItem-icon"> <i class="<?php echo esc_attr( $value['icon'] ); ?>"></i> </span>

					<?php echo wp_kses_post( $value['title'] ); ?>

				</span>

			</a>

		</li>



		<?php $counter++; ?>



	<?php } } ?>

	<?php if($user_type == 'author' || $user_type == 'dispensary' || current_user_can('administrator') ) { ?>

	<?php if(!empty(diero_has_subscription()) || current_user_can('administrator') ) { ?>
		
	<li>
		<a href="<?php echo site_url('dashboard/#active_wc_subscription'); ?>">
			<span class="directorist_menuItem-text">
				<span class="directorist_menuItem-icon"> <i class="la la-money-bill"></i> </span>
				<?php echo wp_kses_post( __( 'Active Subscription', 'directorist') ); ?>
			</span>
		</a>
	</li>

	<?php } ?>

	<li>
		<a href="<?php echo site_url('dashboard/#wc_buy_subscription'); ?>">
			<span class="directorist_menuItem-text">
				<span class="directorist_menuItem-icon"> <i class="la la-money-bill"></i> </span>
				<?php echo wp_kses_post( __( 'Buy Subscription', 'directorist') ); ?>
			</span>
		</a>
	</li>

	<?php } ?>

	<?php if ( $dashboard->user_can_submit() && !empty($add_listing_btn) ) : ?>



		<li>

			<a href="<?php echo esc_url( ATBDP_Permalink::get_add_listing_page_link() ); ?>">

				<span class="directorist_menuItem-text">

					<span class="directorist_menuItem-icon"><i class="las la-plus"></i></span>

					<?php echo ! empty( $add_listing_btn ) ? esc_html( $add_listing_btn ) : ''; ?>

				</span>

			</a>

		</li>



	<?php endif; ?>



	<li>

		<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">

			<span class="directorist_menuItem-text">

				<span class="directorist_menuItem-icon"><i class="las la-sign-out-alt"></i></span>

				<?php esc_html_e( 'Log Out', 'direo' ); ?>

			</span>

		</a>

	</li>



</ul>