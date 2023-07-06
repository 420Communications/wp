<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.8
 * @package Directorist
 */
if (!class_exists('ATPP_Views')) :

    class ATPP_Views
    {
        public function __construct()
        {
            add_shortcode('directorist_pricing_plans', array($this, 'directorist_fee_plane_page'));
            add_action('atbdp_listing_form_after_add_listing_title', array($this, 'atpp_type_of_listing'));
            add_action('atbdp_user_dashboard_listings_before_expiration', array($this, 'atpp_plan_change'));
            add_action('wp_footer', array($this, 'atpp_plan_change_modal'));

            add_action('directorist_dashboard_tabs', array($this, 'directorist_dashboard_tabs'));
            add_action('directorist_before_pricing_plan_page', array($this, 'atbdp_add_listing_page_url'));
            add_action('directorist_dashboard_listing_th_6', array($this, 'directorist_dashboard_listing_th_6'));
            add_action('directorist_dashboard_listing_td_6', array($this, 'directorist_dashboard_listing_td_6'));
            add_filter('directorist_dashboard_listing_action_items', array($this, 'directorist_dashboard_listing_action_items'));
            add_filter( 'directorist_dashboard_listing_action_items_end', array( $this, 'directorist_dashboard_listing_action_items_end' ) );
        }

        public function directorist_dashboard_tabs($tabs)
        {
            $user_order_history = get_directorist_option('user_order_history', 1);
            $user_active_package = get_directorist_option('user_active_package', 1);
            if ($user_active_package) {
                $tabs['packages'] = array(
                    'title'     => __('Packages', 'directorist-pricing-plans'),
                    'content'   => $this->dashboard_package_content(),
                    'icon'        => atbdp_icon_type() . '-money-bill',
                );
            }

            if ($user_order_history) {
                $tabs['order_history'] = array(
                    'title'     => __('Order History', 'directorist-pricing-plans'),
                    'content'   => $this->dashboard_order_history(),
                    'icon'        => atbdp_icon_type() . '-clock-o',
                );
            }


            return $tabs;
        }

        private function dashboard_package_content()
        {
            $order_ids = directorist_plans_dashboard_data( 'order' );
            ob_start();
?>
            <div <?php echo apply_filters('atbdp_dashboard_package_content_div_attributes', 'class="atbd_tab_inner" id="manage_fees"'); ?>>
                <?php
                /**
                 * @since 1.5.3
                 */
                do_action('atbdp_before_package_table'); ?>

                <div class="atbd_manage_fees_wrapper">
                    <?php
                    if (!empty($order_ids)) { ?>
                        <table class="table table-bordered atbd_single_saved_item table-responsive-sm">

                            <thead>
                                <tr>
                                    <th><?php _e('Order ID', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Package Name', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Amount', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Remaining listings', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Payment Gateway', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Order Date', 'directorist-pricing-plans') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($order_ids as $order_id) {
                                    
                                    $plan_id    = get_post_meta( $order_id, '_fm_plan_ordered', true); 
                                    $plan_type  = get_post_meta($plan_id, 'plan_type', true); 

                                    if ('package' !== $plan_type) {
                                        continue;
                                    }

                                    $plan_name  = get_the_title( $plan_id );
                                    $price      = get_post_meta( $order_id, '_amount', true );

                                    $remaining  = plans_remaining( $plan_id, $order_id );
                                    $featured   = $remaining['featured'];
                                    $regular    = $remaining['regular'];
                                    $gateway    = get_post_meta( $order_id, '_payment_gateway', true );
                                    $start_date = get_the_date( get_option('date_format'), $order_id );
                                    $link       = ATBDP_Permalink::get_payment_receipt_page_link( $order_id );


                                    ?>

                                        <tr>
                                            <td class="order_id">
                                                <a class="btn btn-block" target="_blank" href='<?php echo $link; ?>'><?php echo '#' . esc_attr( $order_id ); ?></a>
                                            </td>

                                            <td class="package_name">
                                                <p><?php echo esc_attr($plan_name); ?></p>
                                            </td>

                                            <td class="package_amount">
                                                <p><?php echo esc_attr(atbdp_currency_symbol(atbdp_get_payment_currency()) . $price); ?></p>
                                            </td>

                                            <td class="package_remaining">
                                                <p><?php echo ( 'Unlimited' === $regular ) ? __('Unlimited Regular listing ', 'directorist-pricing-plans') : __('Regular listing ', 'directorist-pricing-plans') . esc_attr( $regular ); ?>
                                                </p>

                                                <p><?php echo ( 'Unlimited' === $featured ) ? __('Unlimited Featured listing ', 'directorist-pricing-plans') : __('Featured listing ', 'directorist-pricing-plans') . esc_attr( $featured ); ?>
                                                </p>
                                            </td>

                                            <td class="package_gateway">
                                                <p><?php echo ('bank_transfer' === $gateway) ? __('Bank Transfer', 'directorist-pricing-plans') : (('free' === $gateway ? __('Free', 'directorist-pricing-plans') : (('paypal_gateway' == $gateway) ? __('PayPal', 'directorist-pricing-plans') : (('stripe_gateway' === $gateway) ? __('Stripe', 'directorist-pricing-plans') : __('Unknown', 'directorist-pricing-plans'))))); ?></p>
                                            </td>

                                            <td class="package_start">
                                                <p><?php echo $start_date; ?></p>
                                            </td>

                                        </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php
                    } else {
                        $text = '<p class="no_package_found">' . __("No package found!", 'directorist-pricing-plans') . '</p>';
                        echo apply_filters('atbdp_no_package_found_text', $text);
                    } ?>
                </div>

                <?php

                /**
                 * @since 1.6.4
                 */
                do_action('atbdp_after_package_table'); ?>
            </div>
        <?php
            return ob_get_clean();
        }

        private function dashboard_order_history()
        {
            $order_ids = directorist_plans_dashboard_data('order');
            ob_start();
        ?>

            <div <?php echo apply_filters('atbdp_dashboard_orderHistory_content_div_attributes', 'class="atbd_tab_inner" id="manage_invoices"'); ?>>

                <?php
                /**
                 * @since 1.5.3
                 */
                do_action('atbdp_before_order_table'); ?>

                <div class="atbd_manage_fees_wrapper">
                    <?php

                    if (!empty($order_ids)) { ?>
                        <table class="table table-bordered atbd_single_saved_item table-responsive-sm">
                            <thead>
                                <tr>
                                    <th><?php _e('Order Number', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Amount', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Plan Name', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Order Date', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Payment Status', 'directorist-pricing-plans') ?></th>
                                    <th><?php _e('Payment Receipt', 'directorist-pricing-plans') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($order_ids as $order_id) {
                                    $package_amount = get_post_meta($order_id, '_amount', true);
                                    $payment_status = get_post_meta($order_id, '_payment_status', true);
                                    $package_amount = (!empty($package_amount)) ? $package_amount : '0';
                                    $order_meta = get_post_meta($order_id);

                                    $plan_id = $order_meta['_fm_plan_ordered'][0];
                                    $plan_name = get_the_title($plan_id);
                                    $link = ATBDP_Permalink::get_payment_receipt_page_link($order_id);
                                    $date = get_the_date(get_option('date_format'), $order_id);

                                ?>
                                    <tr>
                                        <td class="order_no">
                                            <p># <?php echo $order_id; ?></p>
                                        </td>

                                        <td class="package_amount">
                                            <p><?php echo atbdp_currency_symbol(atbdp_get_payment_currency()) . $package_amount; ?></p>
                                        </td>

                                        <td class="name">
                                            <p><?php echo $plan_name; ?></p>
                                        </td>

                                        <td class="date">
                                            <p><?php echo $date; ?></p>
                                        </td>

                                        <td class="name">
                                            <p><?php echo $payment_status; ?></p>
                                        </td>

                                        <td class="action">
                                            <p>
                                                <a class="btn btn-block" href='<?php echo $link; ?>'><?php _e('View', 'directorist-pricing-plans'); ?></a>
                                            </p>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>

                    <?php
                    } else {
                        $text = '<p class="no_order_found">' . __("No order found!", 'directorist-pricing-plans') . '</p>';
                        echo apply_filters('atbdp_no_order_found_text', $text);
                    }  ?>
                </div>

                <?php
                /**
                 * @since 1.6.4
                 */
                do_action('atbdp_after_order_table'); ?>

            </div>
        <?php
            return ob_get_clean();
        }

        public function directorist_dashboard_listing_action_items_end( $items )
        {
            global $post;
            $restrict_listing_deletion = get_directorist_option( 'restrict_listing_deletion' );
            $plan_id = get_post_meta( $post->ID, '_fm_plans', true );
            $modal_id = apply_filters('atbdp_pricing_plan_change_modal_id', 'atpp-plan-change-modal', $post->ID );

            if( isset( $items['renew'] ) ){
                $items['renew']['class'] = 'atpp_change_plan';
                if( !empty( $plan_id ) && ( 'null' != $plan_id ) ){
                    $items['renew']['link'] = ATBDP_Permalink::get_checkout_page_link( $post->ID );
                }else{
                $items['renew']['data_attr'] = 'data-target="' . $modal_id . '" data-listing_id="' . $post->ID . '"';
                }

            }
            if( ! empty( $plan_id ) && $restrict_listing_deletion ) {
                unset( $items['delete'] );
            }
            if( isset( $items['promote'] ) ){
                unset( $items['promote'] );
            }

            return $items;
        }

        public function directorist_dashboard_listing_action_items( $items )
        {

            global $post;
            $plan_id = get_post_meta($post->ID, '_fm_plans', true);
            $change_plan = get_directorist_option('change_plan', 1);
            $modal_id = apply_filters('atbdp_pricing_plan_change_modal_id', 'atpp-plan-change-modal', $post->ID);
            $plan_purchased = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $plan_id);
            $label = has_plan($post->ID) ? ($plan_purchased ? __('Change plan', 'directorist-pricing-plans') : __('Pay Now', 'directorist-pricing-plans')) : __('Promote', 'directorist-pricing-plans');
            $items['pricing_plan'] = array(
                'class'                => 'atpp_change_plan',
                'data_attr'            =>    'data-target="' . $modal_id . '" data-listing_id="' . $post->ID . '"',
                'link'                =>    '#',
                'icon'                =>  '<i class="las la-edit"></i>',
                'label'                =>  $label
            );

            return $items;
        }

        public function directorist_dashboard_listing_td_6($dashboard)
        {
            $plan_id = get_post_meta(get_the_ID(), '_fm_plans', true);
            $plan_name = (has_plan(get_the_ID()) && ('publish' == get_post_status($plan_id))) ? get_the_title($plan_id) : __('No Plan!', 'directorist-pricing-plans');
        ?>
            <td><span class="directorist_listing-plan"><?php echo $plan_name; ?></span></td>
        <?php
        }
        public function directorist_dashboard_listing_th_6()
        {
            ob_start();
            ?>
            <th class="directorist_table-plan"><?php esc_html_e('Plan', 'directorist-pricing-plans'); ?></th>
            <?php
            echo apply_filters( 'directorist_dashboard_listing_plan_th', ob_get_clean());
        }
        /**
         * @since 1.5.1
         */
        public function atbdp_add_listing_page_url($data)
        {

            if( is_user_logged_in() )
            $skip_plans = get_directorist_option('skip_plan_page', 0);
            if (empty($skip_plans)) return; // void if admin not to decide show plan page for active packaged user
			$directory_type = !empty($_GET['directory_type']) ? $_GET['directory_type'] : default_directory_type();
            $type = get_term_by( is_numeric( $directory_type ) ? 'id' : 'slug', $directory_type, ATBDP_TYPE );
            $orders = new WP_Query(array(
                'post_type' => 'atbdp_orders',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'author' => get_current_user_id(),
				'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => '_payment_status',
                        'value' => 'completed',
                        'compare' => '=',
                    ),
                    array(
                        'key' => '_fm_plan_ordered',
                        'compare' => 'EXISTS',
                    ),
                ),

            ));
            foreach ($orders->posts as $key => $val) {

                $order_id = $val->ID;
                $plan_id = get_post_meta($val->ID, '_fm_plan_ordered', true);
                $assign_to_directory = get_post_meta( $plan_id, '_assign_to_directory', true );
                
                if ( $assign_to_directory != $type->term_id ) {
                    return;
                }
    
                if ( 'package' !== package_or_PPL( $plan_id ) ) {
    
                    $order_listings = get_post_meta( $order_id , '_listing_id', true );
    
                    if ( ! $order_listings || ( $order_listings == '0' ) ) {
    
                        $args = [
                            'plan' => $plan_id,
                            'directory_type'=> $directory_type,
                            'order' => $order_id,
                        ];
    
                        $url = add_query_arg( $args, ATBDP_Permalink::get_add_listing_page_link());
    
                        echo '<script>window.location="' . $url . '"</script>';
                        exit;
                    }
    
    
                }
    
                if ( 'package' === package_or_PPL( $plan_id )  ) {
                    
                    $valid_order = directorist_valid_order( $order_id, $plan_id );
                    $order_status = get_post_meta( $order_id, '_order_status', true );

                    if( ! $valid_order || ( 'exit' === $order_status ) ){
                        continue;
                    }

                    $args = [
                        'plan' => $plan_id,
                        'directory_type'=> $directory_type,
                        'order' => $val->ID,
                        ];
                    
					$url = add_query_arg( $args, ATBDP_Permalink::get_add_listing_page_link());

                    echo '<script>window.location="' . $url . '"</script>';
                }
            }
        }



        public function atpp_tab_after_favorite_listings()
        {
            $user_order_history = get_directorist_option('user_order_history', 1);
            $user_active_package = get_directorist_option('user_active_package', 1);
            $user_subscription = 0;

            if (!empty($user_active_package)) {
                $html = '<li class="atbdp_tab_nav--content-link atbd-packages">
                            <a href="" class="atbd_tn_link" target="manage_fees">
                                <span class="directorist_menuItem-text">
                                    <span class="directorist_menuItem-icon"><i class="las la-money-bill"></i></span>' . __('Packages', 'directorist-pricing-plans') . '
                                </span>
                            </a>
                        </li>';
                echo apply_filters('atbdp_package_tab', $html);
            }

            if (!empty($user_order_history)) {
                $html = '<li class="atbdp_tab_nav--content-link atbd-orderhisyory">
                                <a href="" class="atbd_tn_link" target="manage_invoices">
                                    <span class="directorist_menuItem-text">
                                        <span class="directorist_menuItem-icon"><i class="las la-clock"></i></span>' . __('Order History', 'directorist-pricing-plans') . '
                                    </span>
                                 </a>
                            </li>';
                echo apply_filters('atbdp_order_history_tab', $html);
            }

            if (!empty($user_subscription)) { ?>
                <li class="atbdp_tab_nav--content-link atbd-subscription"><a href="" <?php do_action('atbdp_attribute_in_dashboard_subscription_tab'); ?> class="atbd_tn_link" target="manage_subscription"><?php echo apply_filters('atbdp_subscription_tab_text_in_dashboard', __('Subscription', 'directorist-pricing-plans')); ?></a>
                </li>
            <?php
            }
        }

        /**
         * @since 5.5.2
         *
         */
        public function atpp_plan_change_modal()
        {
            $dashboard_page = get_directorist_option('user_dashboard');
            if ($dashboard_page != get_queried_object_id()) return;
            ?>
            <div class="at-modal atm-fade" id="atpp-plan-change-modal">
                <div class="at-modal-content at-modal-lg">
                    <div class="atm-contents-inner">
                        <a href="" class="at-modal-close">
                            <span aria-hidden="true">×</span>
                        </a>
                        <div class="align-items-center">
                            <div class="">
                                <form id="atpp-change-plan-form" class="form-vertical" role="form">
                                    <div class="atbd_modal-header">
                                        <input type="hidden" value="" id="change_listing_id">
                                        <h3 class="atbd_modal-title" id="atpp-plan-label"><?php _e('Change Pricing Plan', 'directorist-pricing-plans'); ?></h3>
                                        <?php
                                        $link = '<a href="' . ATBDP_Permalink::get_fee_plan_page_link() . '" target="_blank">' . __('Click Here', 'directorist-pricing-plans') . '</a>';
                                        printf('<p>%s %s</p>', __('We recommend you check the details of Pricing Plans before changing.', 'directorist-pricing-plans'), $link)
                                        ?>
                                    </div>
                                    <div class="atbd_modal-body">
                                        <div class="dcl_pricing_plan">
                                            <?php
                                            $meta_queries = array();
                                            $args = array(
                                                'post_type' => 'atbdp_pricing_plans',
                                                'posts_per_page' => -1,
                                                'status' => 'publish',
                                            );
                                            $meta_queries[] = array(
                                                'relation' => 'OR',
                                                array(
                                                    'key' => '_hide_from_plans',
                                                    'compare' => 'NOT EXISTS',
                                                ),
                                                array(
                                                    'key' => '_hide_from_plans',
                                                    'value' => 'yes',
                                                    'compare' => '!=',
                                                ),
                                            );
                                            $meta_queries = apply_filters('atbdp_plan_meta_query', $meta_queries);
                                            $count_meta_queries = count($meta_queries);
                                            if ($count_meta_queries) {
                                                $args['meta_query'] = ($count_meta_queries > 1) ? array_merge(array('relation' => 'AND'), $meta_queries) : $meta_queries;
                                            }

                                            $atbdp_query = new WP_Query($args);

                                            if ($atbdp_query->have_posts()) {
                                                global $post;
                                                $plans = $atbdp_query->posts;
                                                printf('<strong style="display: block;">%s</strong><hr>', __('Select Plan', 'directorist-pricing-plans'));
                                                foreach ($plans as $key => $value) {
                                                    $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $value->ID);
                                                    $plan_metas = get_post_meta($value->ID);
                                                    $unl = __('Unlimited', 'directorist-pricing-plans');
                                                    $plan_type = esc_attr($plan_metas['plan_type'][0]);
                                                    $fm_price = esc_attr(atpp_total_price($value->ID));
                                                    $fm_length = esc_attr(directorist_plan_lifetime($value->ID));
                                                    $fm_length_unl = esc_attr($plan_metas['fm_length_unl'][0]);
                                                    $num_regular = esc_attr($plan_metas['num_regular'][0]);
                                                    $num_regular_unl = esc_attr($plan_metas['num_regular_unl'][0]);
                                                    $num_featured = esc_attr($plan_metas['num_featured'][0]);
                                                    $num_featured_unl = esc_attr($plan_metas['num_featured_unl'][0]);
                                                    $regular = (empty($num_regular_unl) ? $num_regular : $unl) . __(' regular & ', 'directorist-pricing-plans');
                                                    $featured = (empty($num_featured_unl) ? $num_featured : $unl) . __(' featured listings', 'directorist-pricing-plans');
                                                    $currency = atbdp_get_payment_currency();
                                                    $symbol = atbdp_currency_symbol($currency);
                                                    $allowances = sprintf('<p class="atbd_plan_core_features"><span class="apc_price">%s</span><span>%s%s</span><span>%s</span>%s</p>', $symbol . $fm_price, empty($fm_length_unl) ? $fm_length : $unl, __(' days', 'directorist-pricing-plans'), ($plan_type === 'package' ? __('Package', 'directorist-pricing-plans') : __('Pay Per Listing', 'directorist-pricing-plans')), ( $plan_type === 'package' ) ? '<span>' . $regular . $featured . '</span>' : '');
                                                    $active = '';
                                                    if ('package' === $plan_type && $active_plan) {
                                                        $active = ' <span class="atbd_badge atbd_badge_open">' . __('Active', 'directorist-pricing-plans') . '</span>';
                                                    }
                                                    $plan_id = $value->ID;
                                                    $post_title = $value->post_title;
                                                    ?>
                                                    <div class="dcl_pricing_plan_names">
                                                        <input type="radio" class="new_plan_id" id="<?php echo $plan_id; ?>" name="new_plan" value="<?php echo $plan_id; ?>">
                                                        <label for="<?php echo $plan_id; ?>"><?php echo $post_title; ?></label>
                                                        <?php echo $active; ?> <br><?php echo $allowances; ?>
                                                        <hr>
                                                    </div>
                                                <?php
                                                }
                                            }
                                            ?>
                                        </div>

                                        <div id="directorist-allowances"></div>
                                        <div id="directorist-claim-submit-notification"></div>
                                        <div id="directorist-claim-warning-notification"></div>
                                    </div>
                                    <div class="atbd_modal-footer">
                                        <button type="submit" class="atbd_modal_btn"><?php esc_html_e('Change', 'directorist-pricing-plans'); ?></button>
                                        <span><i class="<?php atbdp_icon_type(true); ?>-lock"></i> <?php esc_html_e('Secure Payment Process', 'directorist-pricing-plans'); ?></span>
                                    </div>
                                </form>
                            </div><!-- ends: .col-lg-125 -->
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        /**
         * 1.3.2
         * @param $listing_id
         */
        public function atpp_plan_change($listing_id)
        {
            $plan_id = get_post_meta($listing_id, '_fm_plans', true);
            $change_plan = get_directorist_option('change_plan', 1);
            $plan_purchased = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $plan_id);
            $chage_link_text = has_plan($listing_id) ? ($plan_purchased ? __('Change', 'directorist-pricing-plans') : __('Pay Now', 'directorist-pricing-plans')) : __('Promote', 'directorist-pricing-plans');
            $modal_id = apply_filters('atbdp_pricing_plan_change_modal_id', 'atpp-plan-change-modal', $listing_id);
            $change_plan_link = apply_filters('atbdp_plan_change_link_in_user_dashboard', '<span><a data-target="' . $modal_id . '" class="atpp_change_plan" data-listing_id="' . $listing_id . '" href="">' . $chage_link_text . '</a></span>', $listing_id);

            $plan_name = (has_plan($listing_id) && ('publish' == get_post_status($plan_id))) ? get_the_title($plan_id) : __('No Plan!', 'directorist-pricing-plans');
            printf(__('<p><span>Plan:</span> %s %s</p>', 'directorist-pricing-plans'), $plan_name, !empty($change_plan) ? $change_plan_link : '');
        }

        public function atpp_type_of_listing($listing_info)
        {
            if (!is_fee_manager_active()) return false; //void if admin deactivated plan from settings panel
            if ((package_or_PPL($plan = null) === 'pay_per_listng') || !empty($listing_info)) return false;
            $plan_id = selected_plan_id();
            $$remaining  = plans_remaining( $plan_id );
            $featured   = $remaining['featured'];
            $regular    = $remaining['regular'];
        ?>
            <div class="directorist-listing-type">
                <?php $listing_type = !empty($listing_info['listing_type']) ? $listing_info['listing_type'] : ''; ?>

                <h4 class="directorist-option-title"><?php _e('Choose Listing Type', 'directorist-pricing-plans') ?></h4>
                <div class="directorist-listing-type_list">
                    <?php
                    if ('Unlimited' === $featured) {
                    ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" <?php echo ($listing_type == 'regular') ? 'checked' : ''; ?> type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php _e(' Regular listing', 'directorist-pricing-plans') ?>
                                <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-pricing-plans') ?></span>
                            </label>
                        </div>
                    <?php } else { ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" <?php echo ($listing_type == 'regular') ? 'checked' : ''; ?> type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php _e(' Regular listing', 'directorist-pricing-plans') ?>
                                <span class="<?php echo $regular > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                    <?php echo '(' . $regular . __('Remaining', 'directorist-pricing-plans') . ')'; ?></span>
                            </label>
                        </div>
                    <?php } ?>

                    <?php
                    if ('Unlimited' === $regular) {
                    ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="featured" type="radio" class="atbdp_radio_input" <?php echo ($listing_type == 'featured') ? 'checked' : ''; ?> name="listing_type" value="featured">
                            <label for="featured" class="featured_listing_type_select">
                                <?php _e(' Featured listing', 'directorist-pricing-plans') ?>
                                <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-pricing-plans') ?></span>
                            </label>
                        </div>
                    <?php
                    } else { ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="featured" type="radio" <?php echo ($listing_type == 'featured') ? 'checked' : ''; ?> class="atbdp_radio_input" name="listing_type" value="featured">
                            <label for="featured" class="featured_listing_type_select">
                                <?php _e(' Featured listing', 'directorist-pricing-plans') ?>
                                <span class="<?php echo $featured > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                    <?php echo '(' . $featured . __('Remaining', 'directorist-pricing-plans') . ')'; ?></span>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
<?php
        }

        public function directorist_fee_plane_page($atts)
        {
            ob_start();
            ATBDP_Pricing_Plans()->load_template('fee-plans', array('atts' => $atts));
            return ob_get_clean();
        }
    }
endif;