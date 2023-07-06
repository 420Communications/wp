<?php
/**
 * @author  wpWax
 * @since   6.6
 * @version 7.3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$counter = 1;
$blockTabsForSeller = array('dashboard_announcement', 'order_history', 'packages', 'dashboard_my_listings');
$blockTabsForBuyer = array('packages', 'dashboard_announcement', 'order_history', 'live_chat');
$user_type = get_user_meta( get_current_user_id(), '_user_type', true );
?>

<div class="directorist-user-dashboard__tab-content directorist-tab__content">

	<?php foreach ( $dashboard->dashboard_tabs() as $key => $value ) { if( ( ( $user_type == 'author' || $user_type == 'dispensary') && !in_array($key, $blockTabsForSeller) && diero_user_has_access() == true ) || current_user_can('administrator') ) { ?>

		<div class="directorist-tab__pane <?php echo ( $counter == 1 ) ? 'directorist-tab__pane--active' : ''; ?>" id="<?php echo esc_attr( $key ); ?>">
			<?php
			// Contents are coming from dashboard template files which are escaped already
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $value['content'];
			?>
		</div>
		<?php
			if (!empty($value['after_content_hook'])) {
				do_action($value['after_content_hook']);
			}
			?>
		<?php do_action( 'directorist_dashboard_contents', $key, $dashboard ); ?>

		<?php $counter++; ?>

		<?php  } else if ( ( ( $user_type == 'general' || $user_type == 'become_author' ) && !in_array($key, $blockTabsForBuyer) ) || current_user_can('administrator') ) { ?>

		<div class="directorist-tab__pane <?php echo ( $counter == 1 ) ? 'directorist-tab__pane--active' : ''; ?>" id="<?php echo esc_attr( $key ); ?>">
			<?php
			// Contents are coming from dashboard template files which are escaped already
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $value['content'];
			?>
		</div>
		<?php
			if (!empty($value['after_content_hook'])) {
				do_action($value['after_content_hook']);
			}
			?>
		<?php do_action( 'directorist_dashboard_contents', $key, $dashboard ); ?>

		<?php $counter++; ?>

	<?php } } ?>

	<?php if($user_type == 'author' || $user_type == 'dispensary' || current_user_can('administrator') ) {
		$user_id = get_current_user_id();

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'wps_subscriptions',
			'post_status' => 'wc-wps_renewal',
			'meta_query' => array(
				array(
					'key'   => 'wps_customer_id',
					'value' => $user_id,
				),
			),

		);
		$wps_subscriptions = get_posts( $args );
	?>

		<?php if($user_type == 'author' || $user_type == 'dispensary' || current_user_can('administrator') ) { ?>

		<?php if(!empty(diero_has_subscription()) || current_user_can('administrator') ) { ?>

		<div class="directorist-tab__pane <?php echo ( $counter == 1 ) ? 'directorist-tab__pane--active' : ''; ?>" id="wc_subscription">
			<div class="atbd_tab_inner" id="manage_fees">
                <div class="atbd_manage_fees_wrapper">
                    <?php
                    	if ( ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
					?>
						<table class="wps_subscriptions-table">
							<thead>
								<tr>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php esc_html_e( 'ID', 'subscriptions-for-woocommerce' ); ?></span></th>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php esc_html_e( 'Status', 'subscriptions-for-woocommerce' ); ?></span></th>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?php echo esc_html_e( 'Next payment date', 'subscriptions-for-woocommerce' ); ?></span></th>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr"><?php echo esc_html_e( 'Recurring Total', 'subscriptions-for-woocommerce' ); ?></span></th>
									<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><?php esc_html_e( 'Action', 'subscriptions-for-woocommerce' ); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php
							foreach ( $wps_subscriptions as $key => $wps_subscription ) {
								$parent_order_id   = get_post_meta( $wps_subscription->ID, 'wps_parent_order', true );
								$wps_wsfw_is_order = false;
								if ( function_exists( 'wps_sfw_check_valid_order' ) && ! wps_sfw_check_valid_order( $parent_order_id ) ) {
									$wps_wsfw_is_order = apply_filters( 'wps_wsfw_check_parent_order', $wps_wsfw_is_order, $parent_order_id );
									if ( false == $wps_wsfw_is_order ) {
										continue;
									}
								}
								?>
										<tr class="wps_sfw_account_row woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
											<td class="wps_sfw_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number">
										<?php echo esc_html( $wps_subscription->ID ); ?>
											</td>
										<?php $wps_status = get_post_meta( $wps_subscription->ID, 'wps_subscription_status', true ); ?>
											<td class="wps_sfw_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status <?php echo ( $wps_status == 'active' ) ? 'success' : 'danger'; ?>">
										<?php
											echo esc_html( ucfirst($wps_status) );
										?>
											</td>
											<td class="wps_sfw_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date">
										<?php
											$wps_next_payment_date = get_post_meta( $wps_subscription->ID, 'wps_next_payment_date', true );
										if ( 'cancelled' === $wps_status ) {
											$wps_next_payment_date = '';
										}
											echo esc_html( wps_sfw_get_the_wordpress_date_format( $wps_next_payment_date ) );
										?>
											</td>
											<td class="wps_sfw_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total">
											<?php
											do_action( 'wps_sfw_display_susbcription_recerring_total_account_page', $wps_subscription->ID );
											?>
											</td>
											<td class="wps_sfw_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions">
												<span class="wps_sfw_account_show_subscription">
													<a href="
											<?php
											echo esc_url( wc_get_endpoint_url( 'show-subscription', $wps_subscription->ID, wc_get_page_permalink( 'myaccount' ) ) );
											?>
													" target="_blank">
											<?php
											esc_html_e( 'Show', 'subscriptions-for-woocommerce' );
											?>
													</a>
												</span>
											</td>
										</tr>
										<?php
							}
							?>
							</tbody>
						</table>
					<?php
				} else {
					?>
					<div class="directorist-notfound-subscription"><?php esc_html_e( 'You do not have any active subscription(s).', 'subscriptions-for-woocommerce' ); ?></div>
					<?php
				}
					?>
                </div>
            </div>
		</div>

		<div class="directorist-tab__pane <?php echo ( $counter == 1 ) ? 'directorist-tab__pane--active' : ''; ?>" id="add_items">
			<?php get_template_part( 'template-parts/show-user-item', 'listing' ); ?>
		</div>

		<?php } ?>

		<div class="directorist-tab__pane <?php echo ( $counter == 1 ) ? 'directorist-tab__pane--active' : ''; ?>" id="wc_buy_subscription">
			<div class="atbd_tab_inner" id="manage_fees">
                <div class="atbd_manage_fees_wrapper">
                	<?php
                	$subsProducts = get_posts( array(
					    'post_type' => 'product',
					    'numberposts' => -1,
					    'post_status' => 'publish',
					    'orderby'     => 'date',
    					'order'       => 'ASC',
					    'tax_query' => array(
					        array(
					            'taxonomy' => 'product_cat',
					            'field' => 'slug',
					            'terms' => $user_type,
					            'operator' => 'IN',
							)
					    ),
				    ));

					?>

					<div class="row">
					    <div class="col-md-12">
					        <div class="">
					            <div id="directorist-pricing-plan-container">
					                <div class="directorist-container-fluid">
					                    <div class="directorist-row message-packages">
					                    	<?php foreach ($subsProducts as $key => $product) {
					                    		$messageLimit = get_post_meta( $product->ID, 'message_limit', true );
					                    		$regularPrice = get_post_meta( $product->ID, '_regular_price', true );
					                    		$expiryNumber = get_post_meta( $product->ID, 'wps_sfw_subscription_expiry_number', true );
					                    		$expiryInterval = get_post_meta( $product->ID, 'wps_sfw_subscription_interval', true );
					                    		$largerIcon = get_post_meta( $product->ID, 'larger_icon', true );
					                    		$isRecommended = get_post_meta( $product->ID, 'is_recommended', true );
					                    		$isUnlimited = get_post_meta( $product->ID, 'unlimited_messages', true );
					                    		$isAddOn = get_post_meta( $product->ID, 'is_addon_package', true );
											?>
					                        <div class="directorist-col-md-3 atpp_default plan">
					                            <div class="directorist-pricing directorist-pricing--1 directorist-pricing-special">
					                            	<?php if( $isRecommended == 1 ) { ?>
					                                <span class="atbd_popular_badge"><?php _e( 'Recommended', 'subscriptions-for-woocommerce' ); ?></span>
					                                <?php } ?>

					                                <div class="directorist-pricing__title">
					                                    <h4><?php echo $product->post_title; ?></h4>
					                                </div>
					                                <div class="directorist-pricing__price">
					                                    <p class="directorist-pricing__value"><?php echo get_woocommerce_currency_symbol() . $regularPrice; if((empty($isAddOn) || $isAddOn != 1) && get_post_meta( $product->ID, '_wps_sfw_product', true ) == 'yes') { ?> <small>/ <?php echo ucfirst($expiryInterval); ?></small><?php } ?></p>
					                                </div>
					                                <div class="directorist-pricing__features">
					                                    <ul>
					                                    	<?php if( $isUnlimited == 1 ) { ?>
					                                    		<li><span class="fa fa-check"> </span><?php _e( 'Unlimited Messages', 'subscriptions-for-woocommerce' ); ?></li>
					                                    	<?php } else { ?>
					                                        	<li><span class="fa fa-check"> </span><?php echo $messageLimit; ?> <?php _e( 'Messages', 'subscriptions-for-woocommerce' ); ?></li>
					                                    	<?php } if(empty($isAddOn) || $isAddOn != 1) { ?>
					                                        <li><span class="fa fa-check"> </span> <?php _e( 'Edit, Update Profile, Bookmarks, Messages, Menu Items', 'subscriptions-for-woocommerce' ); ?></li>
					                                        <li><span class="<?php echo ( $largerIcon == 1 ) ? 'fa fa-check' : 'fa fa-times'; ?>"> </span> <?php _e( 'Larger Icon on the Map', 'subscriptions-for-woocommerce' ); ?></li>
					                                    	<?php } ?>
					                                    </ul>
					                                    <div class="directorist-pricing__action">
					                                        <label><a href="<?php echo site_url('?package-to-purchase=' . $product->ID . '&empty-cart'); ?>" class="directorist-btn directorist-btn-lighter directorist-btn-block directorist-pricing__action--btn">Buy Now</a></label>
					                                    </div>
					                                </div>
					                            </div>
					                        </div>
					                        <?php } ?>
					                    </div>
					                </div>
					            </div>
					        </div>
					    </div>
					</div>
                </div>
            </div>
        </div>

        <?php } ?>

	<?php } ?>

</div>