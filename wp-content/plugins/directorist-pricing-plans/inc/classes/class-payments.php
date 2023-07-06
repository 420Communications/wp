<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.7.4
 * @package Directorist
 */
if (!class_exists('ATPP_Payments')) :

    class ATPP_Payments
    {
        public $plan_id;

        public function __construct()
        {
            add_action('atbdp_before_checkout_form_end', array($this, 'atbdp_before_checkout_form_end'));
            
            add_filter('atbdp_checkout_form_data', array($this, 'atpp_checkout_form_data'), 11, 2);
            add_filter('atbdp_payment_receipt_data', array($this, 'atpp_payment_receipt_data'), 12, 3);
            add_filter('atbdp_order_details', array($this, 'atpp_order_details'), 11, 3);
            add_filter('atbdp_order_items', array($this, 'atpp_order_items'), 11, 4);
            add_filter('atbdp_order_created', array($this, 'atbdp_order_created'), 10, 2);
            
            add_filter( 'directorist_stripe_tax_rate_arguments', array( $this, 'directorist_stripe_tax_rate_arguments' ) );
            add_filter('directorist_stripe_gateway_tax_rate', array($this, 'directorist_stripe_gateway_tax_rate'), 10, 2);
            add_filter('directorist_stripe_gateway_total', array($this, 'directorist_stripe_gateway_total'), 10, 2);

            if( ! class_exists( 'SWBDPCoupon' ) ){
                add_filter('atbdp_order_amount', array($this, 'atbdp_set_order_amount'), 10, 2);

            }

            
        }

        public function atbdp_order_created( $order_id, $listing_id ){
            update_post_meta( $listing_id, '_plan_order_id', $order_id );
            update_post_meta( $listing_id, '_listing_order_id', $order_id );
        }
		

        public function directorist_stripe_tax_rate_arguments( $args ){

            $args['display_name'] = get_directorist_option( 'tax_placeholder', 'VAT' );

            return $args;
        }

		 public function atbdp_checkout_form_final_data( $form_data, $listing_id ){

            if( ! empty( $form_data ) ){
                foreach( $form_data as $key => $data ){
                    $price = !empty( $form_data[$key]['price'] ) ? $form_data[$key]['price'] : '';
                    if( !empty( $price ) ){
                        $form_data[$key]['price'] = round( $price );
                    }
                }
            }

            return $form_data;

        }

        public function atbdp_set_order_amount( $total, $order_id ){

            $listing_id             = get_post_meta( $order_id, '_listing_id', true );
            $plan_from_order        = get_post_meta( $order_id, '_fm_plan_ordered', true );
            $plan_from_listing      = get_post_meta( $listing_id, '_fm_plans', true );
            $plan                   = !empty( $plan_from_order ) ? $plan_from_order : $plan_from_listing;
            $plan_tax               = atpp_total_tax( $plan );
            if( $plan_tax ){
                $total = (float)$total + (float)$plan_tax;
            }
            return $total;
        } 

        public function directorist_stripe_gateway_total( $total, $order_id ){

            $listing_id             = get_post_meta( $order_id, '_listing_id', true );
            $plan_from_order        = get_post_meta( $order_id, '_fm_plan_ordered', true );
            $plan_from_listing      = get_post_meta( $listing_id, '_fm_plans', true );
            $plan                   = !empty( $plan_from_order ) ? $plan_from_order : $plan_from_listing;
            $fm_price               = get_post_meta( $plan, 'fm_price', true );
            
            if( $fm_price ){
                $total = $fm_price;
            }
            return $total;
        }

        public function directorist_stripe_gateway_tax_rate( $rate, $order_id ){

            $plan                   = get_post_meta($order_id, '_fm_plan_ordered', true);
            $plan_tax               = directorist_plan_tax_rate( $plan );
            if( $plan_tax ){
                return $plan_tax;
            }
            return $rate;
        }
        
        public function atbdp_before_checkout_form_end() {
            $is_recurring = get_post_meta( $this->plan_id, '_atpp_recurring', true );
            ?>
            <input type="hidden" id="recurring" name="recurring" value="<?php echo esc_attr( $is_recurring ); ?>">
            <?php
        }

        public function atbdp_checkout_after_price($args){
            if( empty( $args['_booking_id'] ) ) { 
                $tax_placeholder = get_directorist_option('tax_placeholder', 'tax');
                $listing_id = isset($args['listing_id']) ? $args['listing_id'] : (isset($args['_listing_id']) ? $args['_listing_id'][0] : (!is_array($args) ? $args : ''));
                $plan_id = get_post_meta($listing_id, '_fm_plans', true);
                if($plan_id){
                $tax = atpp_total_tax($plan_id);
                ?>
                <small><?php echo '('.__('including ', 'directorist-pricing-plans') . $tax .' '. $tax_placeholder . ')'; ?></small>
                <?php
                }
            }
        }

        public function atpp_online_order_processed($order_id, $listing_id)
        {
            $plan_id = get_post_meta($listing_id, '_fm_plans', true);
            $plan_type = package_or_PPL($plan_id);
            $orders = new WP_Query(array(
                'post_type' => 'atbdp_orders',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'author' => $order_id,
                'meta_key' => '_payment_status',
                'meta_value' => 'completed',
            ));
            $all_ids = array();
            foreach ($orders->posts as $key => $val) {
                $all_ids[] = !empty($val) ? $val->ID : array();
            }
            ///$last_order_id =!empty($all_ids[0])?$all_ids[0]:'';
            if (count($all_ids) > 1) {
                array_shift($all_ids);
                $ids_need_to_change_status = !empty($all_ids) ? array_values($all_ids) : '';
                foreach ($ids_need_to_change_status as $index => $val) {
                    if (('pay_per_listng' != $plan_type)) {
                        update_post_meta($order_id, '_payment_status', 'exit');
                    }
                }
            }
        }

        
        /**
         * Add data to the customer receipt after completing an order.
         *
         * @param array $order_items An array of selected package.
         * @param integer $listing_id Listing ID.
         * @return     array      $order_items               Show the data of the packages.
         * @since     1.0.0
         * @access   public
         *
         */
        public function atpp_order_items( $order_items, $order_id, $listing_id, $data )
        {
            $booking_id = get_post_meta( $order_id, '_booking_id', true );
            if( ! empty( $booking_id ) ) return $order_items;
            //if claim listing is active and admin decided to monitize without plan
            $claim_fee = get_post_meta($listing_id[0], '_claim_fee', true);
            $admin_calim = get_directorist_option('claim_charge_by');
            $admin_calim_charge = !empty($admin_calim) ? $admin_calim : '';
            $charge_by = !empty($claim_fee) ? $claim_fee : $admin_calim_charge;
            $is_climer = get_post_meta($listing_id[0], '_claimer_plans', true);
            $order_plan = get_post_meta($order_id, '_fm_plan_ordered', true);
            if (class_exists('DCL_Base') && ('pricing_plan' !== $charge_by) && ( ('static_fee' === $charge_by) && empty($is_climer) && ! directorist_direct_purchase() ) && empty( $order_plan ) ) {
                $selected_plan_id = get_post_meta($listing_id[0], '_fm_plans', true);
                $updated_plan_id = get_user_meta(get_current_user_id(), '_plan_to_active', true);
                $plan_id = !empty($data['o_metas']['_fm_plan_ordered'][0]) ? $data['o_metas']['_fm_plan_ordered'][0] : '';
                $_plan_ids = !empty($selected_plan_id) ? $selected_plan_id : $updated_plan_id;
                $_plan_id = !empty($plan_id) ? $plan_id : $_plan_ids;
                $p_title = get_the_title($listing_id[0]);
                $p_description = get_post_meta($_plan_id, 'fm_description', true);
                $fm_price = get_post_meta($listing_id[0], '_claim_charge', true);
                $admin_common_price = get_directorist_option('claim_listing_price');
                $fm_price = !empty($fm_price) ? $fm_price : $admin_common_price;
                $order_items[] = array(
                    'title' => __('Claim for ', 'directorist-pricing-plans') . $p_title,
                    'desc' => $p_description,
                    'price' => $fm_price,
                );
                return $order_items;
            } else {
                $claimer_plans = get_post_meta($listing_id[0], '_claimer_plans', true);
                if (!empty($claimer_plans)) {
                    return array();
                } else {
                    $selected_plan_id = get_post_meta($listing_id[0], '_fm_plans', true);
                }
                $updated_plan_id = get_user_meta(get_current_user_id(), '_plan_to_active', true);
                $plan_id = !empty($data['o_metas']['_fm_plan_ordered'][0]) ? $data['o_metas']['_fm_plan_ordered'][0] : '';
                $_plan_ids = !empty($selected_plan_id) ? $selected_plan_id : $updated_plan_id;
                $_plan_id = !empty($plan_id) ? $plan_id : $_plan_ids;
                $plan_from_order = get_post_meta( $order_id, '_fm_plan_ordered', true);
                $plan_id = $_plan_id ? $_plan_id : $plan_from_order;
                $p_title = get_the_title($_plan_id);
                $p_description = get_post_meta($_plan_id, 'fm_description', true);
                $fm_price = atpp_total_price($_plan_id);
                $tax_placeholder = get_directorist_option( 'tax_placeholder', 'Tax');
                $order_items[] = array(
                    'title' => $p_title,
                    'desc' => $p_description,
                    'price' => $fm_price,
                );
                if( atpp_total_tax( $_plan_id ) ){
                    $order_items['tax'] = array(
                        'title' => $tax_placeholder,
                        'desc' => '',
                        'price' => atpp_total_tax( $_plan_id ),
                    );
                }
                return $order_items;
            }

        }

              /**
         * Add order details.
         *
         * @param array $order_details An array of containing order details.
         * @param integer $order_id Order ID.
         * @param integer $listing_id Listing ID.
         * @return     array      $order_details    Push additional package to the mail array.
         * @since     1.0.0
         * @access   public
         *
         */
        public function atpp_order_details($order_details, $order_id, $listing_id)
        {
            if (isset($_POST['confirmed'])) {
                return $order_details;
            }
            $updated_plan_id = get_post_meta( $listing_id, '_fm_plans', true );
            $plan_id = !empty( $_GET['plan'] ) ? $_GET['plan'] : $updated_plan_id;
            if ( $plan_id ) {
                update_post_meta( $order_id, '_fm_plan_ordered', $plan_id );
            }
            //if claim listing is active and admin decided to monitize without plan
            update_post_meta($order_id, '_listing_id', $listing_id);
            $claim_fee = get_post_meta($listing_id, '_claim_fee', true);
            $admin_calim = get_directorist_option('claim_charge_by');
            $admin_calim_charge = !empty($admin_calim) ? $admin_calim : '';
            $charge_by = !empty($claim_fee) ? $claim_fee : $admin_calim_charge;
            $is_climer = get_post_meta($listing_id, '_claimer_plans', true);
            if (class_exists('DCL_Base') && ('pricing_plan' !== $charge_by) && ( ('static_fee' === $charge_by) && empty($is_climer) && ! directorist_direct_purchase() ) && ! isset( $_GET['plan_id'] ) ) {
                $p_title = get_the_title($listing_id);
                $p_description = __('Claiming charge for this listing', 'directorist-pricing-plans');
                $fm_price = get_post_meta($listing_id, '_claim_charge', true);
                $admin_common_price = get_directorist_option('claim_listing_price');
                $fm_price = !empty($fm_price) ? $fm_price : $admin_common_price;
                $order_details[] = array(
                    'active' => '1',
                    'label' => $p_title,
                    'desc' => $p_description,
                    'price' => $fm_price,
                    'show_ribbon' => '1',
                );
                return $order_details;
            } else {
                $claimer_plans = get_post_meta($listing_id, '_claimer_plans', true);
                if (!empty($claimer_plans)) {
                    $selected_plan_id = $claimer_plans;
                } else {
                    $selected_plan_id = get_post_meta($listing_id, '_fm_plans', true);
                }
                
                $plan_from_order = get_post_meta( $order_id, '_fm_plan_ordered', true);
                
                $plan_id = $selected_plan_id ? $selected_plan_id : $plan_from_order;
                $plan_id = !empty( $_GET['plan'] ) ? $_GET['plan'] : $plan_id;
                
                $p_title = get_the_title($plan_id);
                $p_description = get_post_meta($plan_id, 'fm_description', true);
                $fm_price = atpp_total_price($plan_id);
                $order_details[] = array(
                    'active' => '1',
                    'label' => $p_title,
                    'desc' => $p_description,
                    'price' => $fm_price,
                    'show_ribbon' => '1',
                );
                return $order_details;
            }


        }

                /*@todo later need to update the receipt content with the purchased packages dynamically e.g. remove Gold package*/
        /**
         * Add data to the customer receipt after completing an order.
         *
         * @param array $receipt_data An array of selected package.
         * @param integer $order_id Order ID.
         * @param integer $listing_id Listing ID.
         * @return     array      $receipt_data               Show the data of the packages.
         * @since     1.0.0
         * @access   public
         *
         */
        public function atpp_payment_receipt_data($receipt_data, $order_id, $listing_id)
        {
            
            //if claim listing is active and admin decided to monitize without plan
            $claim_fee = get_post_meta($listing_id[0], '_claim_fee', true);
            $admin_calim = get_directorist_option('claim_charge_by');
            $admin_calim_charge = !empty($admin_calim) ? $admin_calim : '';
            $charge_by = !empty($claim_fee) ? $claim_fee : $admin_calim_charge;
            $is_climer = get_post_meta($listing_id[0], '_claimer_plans', true);

            if (class_exists('DCL_Base') && ('pricing_plan' !== $charge_by) && ( ('static_fee' === $charge_by) && empty($is_climer) && ! directorist_direct_purchase() ) && ! isset( $_GET['plan_id'] ) ) {
                $claim_fee = get_post_meta($listing_id[0], '_claim_fee', true);
                if ($claim_fee !== 'static_fee') return array();
                $p_title = get_the_title($listing_id[0]);
                $fm_price = get_post_meta($listing_id[0], '_claim_charge', true);
                $admin_common_price = get_directorist_option('claim_listing_price');
                $fm_price = !empty($fm_price) ? $fm_price : $admin_common_price;
                $receipt_data = array(
                    'title' => $p_title,
                    'desc' => __('Claiming charge for this listing', 'directorist-pricing-plans'),
                    'price' => $fm_price,
                );
                return $receipt_data;

            } else {
                $claimer_plans = get_post_meta($listing_id[0], '_claimer_plans', true);
                if (!empty($claimer_plans)) {
                    $selected_plan_id = $claimer_plans;
                } else {
                    $selected_plan_id = get_post_meta($listing_id[0], '_fm_plans', true);
                }
                $plan_from_order = get_post_meta( $order_id, '_fm_plan_ordered', true);
                $plan_id = $selected_plan_id ? $selected_plan_id : $plan_from_order;
                $p_title = get_the_title( $plan_id );
                $p_description = get_post_meta( $plan_id , 'fm_description', true);
                $fm_price = atpp_total_price( $plan_id );
                $receipt_data = array(
                    'title' => $p_title,
                    'desc' => $p_description,
                    'price' => $fm_price,
                );
                return $receipt_data;
            }

        }

                /**
         * Add selected order to the checkout form.
         *
         * @param array $data An array of selected package.
         * @param integer $listing_id Listing ID.
         * @return     array      $data               Show the data of the packages.
         * @since     1.0.0
         * @access   public
         *
         */

        public function atpp_checkout_form_data($data, $listing_id)
        {
            //if claim listing is active and admin decided to monitize without plan
            $claim_fee = get_post_meta($listing_id, '_claim_fee', true);
            $admin_calim = get_directorist_option('claim_charge_by');
            $admin_calim_charge = !empty($admin_calim) ? $admin_calim : '';
            $charge_by = !empty($claim_fee) ? $claim_fee : $admin_calim_charge;
            $is_climer = get_post_meta($listing_id, '_claimer_plans', true);
            if (class_exists('DCL_Base') && ('pricing_plan' !== $charge_by) && ( ('static_fee' === $charge_by) && empty($is_climer) && ! directorist_direct_purchase() ) && ! isset( $_GET['plan_id'] ) ) {
                $claim_charge = get_post_meta($listing_id, '_claim_charge', true);
                $admin_common_price = get_directorist_option('claim_listing_price');
                $fm_price = !empty($claim_charge) ? $claim_charge : $admin_common_price;
                $p_title = get_the_title($listing_id);
                $data[] = array(
                    'type' => 'header',
                    'title' => __('Claim for ', 'directorist-pricing-plans') . $p_title
                );

                $data[] = array(
                    'type' => 'checkbox',
                    'name' => '',
                    'value' => 1,
                    'selected' => 1,
                    'title' => __('Claim for ', 'directorist-pricing-plans') . $p_title,
                    'desc' => __('Claiming charge for this listing ', 'directorist-pricing-plans'),
                    'price' => $fm_price
                );
                return $data;
            } else {
                $claimer_plans = get_post_meta($listing_id, '_claimer_plans', true);
                if (!empty($_POST['fm_plans_updated']) || !empty( $_GET['plan'] )) {
                    $selected_plan_id = !empty($_POST['fm_plan_id_updated']) ? (int)$_POST['fm_plan_id_updated'] : $_GET['plan'];
                    update_user_meta(get_current_user_id(), '_plan_to_active', $selected_plan_id);
                } elseif (!empty($claimer_plans)) {
                    $selected_plan_id = $claimer_plans;
                } else {
                    $selected_plan_id = get_post_meta($listing_id, '_fm_plans', true);
                }
                $p_title = get_the_title($selected_plan_id);
                $p_description = get_post_meta($selected_plan_id, 'fm_description', true);
                $fm_price = atpp_total_price($selected_plan_id);
                $tax_placeholder = get_directorist_option( 'tax_placeholder', 'Tax');
                $data[] = array(
                    'type' => 'header',
                    'title' => $p_title
                );

                $this->plan_id = $selected_plan_id;
                $data[] = array(
                    'type' => 'checkbox',
                    'name' => $selected_plan_id,
                    'value' => 1,
                    'selected' => 1,
                    'title' => $p_title,
                    'desc' => $p_description,
                    'price' => $fm_price,
                );
                if( atpp_total_tax( $selected_plan_id ) ){
                    $data['tax'] = array(
                        'type' => 'checkbox',
                        'name' => '',
                        'value' => 1,
                        'selected' => 1,
                        'title' => ucfirst($tax_placeholder),
                        'desc' => '',
                        'price' => atpp_total_tax( $selected_plan_id ),
                    );
                }
                return $data;
            }
        }

    }
endif;