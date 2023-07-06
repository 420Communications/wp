<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.7.4
 * @package Directorist
 */
if (!class_exists('ATPP_Ajax_Handler')) :

    class ATPP_Ajax_Handler
    {
        public function __construct()
        {
            add_action('wp_ajax_atpp_submit_changing_plan', array($this, 'atpp_submit_changing_plan'));
            add_action('wp_ajax_nopriv_atpp_submit_changing_plan', array($this, 'atpp_submit_changing_plan'));
            add_action('wp_ajax_plan_allowances', array($this, 'plan_allowances'));
            add_action('wp_ajax_nopriv_plan_allowances', array($this, 'plan_allowances'));
            add_action('wp_ajax_plan_allowances_on_user_selection', array($this, 'plan_allowances_on_user_selection'));

            add_action('wp_ajax_dynamic_admin_listing_form_and_allowances', array($this, 'dynamic_admin_listing_form_and_allowances'));
            add_action('wp_ajax_atpp_gifting_plan', array($this, 'atpp_gifting_plan'));

            add_action( 'wp_ajax_directorist_plan_dynamic_fields', array( $this, 'directorist_plan_dynamic_fields' ) );
            add_action( 'wp_ajax_atpp_changing_plan', array( $this, 'atpp_changing_plan' ) );
            add_action( 'wp_ajax_atbdp_dynamic_plan', array( $this, 'atbdp_dynamic_plan' ) );


            add_action( 'wp_ajax_select_active_order', array( $this, 'select_active_order' ) );
            add_action( 'wp_ajax_nopriv_select_active_order', array( $this, 'select_active_order' ) );

            add_action( 'wp_ajax_guest_customer', array( $this, 'guest_customer' ) );
            add_action( 'wp_ajax_nopriv_guest_customer', array( $this, 'guest_customer' ) );
        }

        public function guest_customer(){

            $email      = !empty( $_POST['email'] ) ? $_POST['email'] : '';
            $data['error'] = false;
            if( ! empty( $email ) ){
                $response = atbdp_guest_submission( $email );
                if( ! empty( $response['error'] ) ){
                    wp_send_json( $response );
                }
            }else{
                $data['error'] = true;
                $data['error_msg'] = __( 'Email is required', 'directorist-pricing-plans' );

            }
            wp_send_json( $data );
        }

        public function select_active_order(){

            $plan_id = !empty( $_POST['plan_id'] ) ? $_POST['plan_id'] : '';
            $order_id = !empty( $_POST['order_id'] ) ? $_POST['order_id'] : '';
            $general_label = !empty( $_POST['general_label'] ) ? $_POST['general_label'] : '';
            $featured_label = !empty( $_POST['featured_label'] ) ? $_POST['featured_label'] : '';
            $label = !empty( $_POST['label'] ) ? $_POST['label'] : '';

            $remaining  = plans_remaining( $plan_id, $order_id );
            $featured   = $remaining['featured'];
            $regular    = $remaining['regular'];

            // let's update order status
            $regular_exit       = ( 'Unlimited' !== $regular ) && empty( $regular ) ? true : false;
            $featured_exit      = ( 'Unlimited' !== $featured ) && empty( $featured ) ? true : false;

            if( $regular_exit && $featured_exit ){
                update_post_meta( $order_id, '_order_status', 'exit' );
            }

            ob_start();

            ?>
            <div class="directorist-listing-type dpp-selected-order-listing-type">
                <h4 class="directorist-option-title"><?php echo esc_attr( $label ); ?></h4>
                <input type="hidden" name="activated_order" value="<?php echo esc_attr( $order_id ); ?>">
                <div class="directorist-listing-type_list">
                    <?php
                    if ( 'Unlimited' === $regular ) {
                    ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php echo esc_attr( $general_label ); ?>
                                <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-pricing-plans') ?></span>
                            </label>
                        </div>
                    <?php } else { ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php echo esc_attr( $general_label ); ?>
                                <span class="<?php echo $regular > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                    <?php echo '(' . $regular . __(' Remaining', 'directorist-pricing-plans') . ')'; ?></span>
                            </label>
                        </div>
                    <?php } ?>

                    <?php
                    if ( 'Unlimited' === $featured ) {
                    ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="featured" type="radio" class="atbdp_radio_input" name="listing_type" value="featured">
                            <label for="featured" class="featured_listing_type_select">
                                <?php echo esc_attr( $featured_label ); ?>
                                <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-pricing-plans') ?></span>
                            </label>
                        </div>
                    <?php
                    } else { ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="featured" type="radio" class="atbdp_radio_input" name="listing_type" value="featured">
                            <label for="featured" class="featured_listing_type_select">
                                <?php echo esc_attr( $featured_label ); ?>
                                <span class="<?php echo $featured > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                    <?php echo '(' . $featured . __(' Remaining', 'directorist-pricing-plans') . ')'; ?></span>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <?php

            $response = ob_get_clean();
            wp_send_json( $response );
        }

        public function atbdp_dynamic_plan() {
            $directory_type = isset( $_POST['directory_type'] ) ? $_POST['directory_type'] : '';
            $listing_id     = isset( $_POST['listing_id'] ) ? $_POST['listing_id'] : '';
            $user_id        = isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';
            $current_val    = get_post_meta( $listing_id, '_fm_plans', true );

            $meta_queries = array();

            $args = array(
                'post_type'          => 'atbdp_pricing_plans',
                'posts_per_page'     => -1,
                'status'             => 'publish',
            );

            if( ! empty( $directory_type ) ) {
                $meta_queries['directory_type'] = [
                    'key'            => '_assign_to_directory',
                    'value'          => $directory_type,
                    'compare'        => '=',
                ];
            }

            $meta_queries = apply_filters('atbdp_plan_meta_query', $meta_queries);
            $count_meta_queries = count( $meta_queries );

            if( $count_meta_queries ) {
                $args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array('relation' => 'AND'), $meta_queries ) : $meta_queries;
            }

            $atbdp_query = new WP_Query( $args );
            ob_start();
            $this->dynamic_plans( $atbdp_query, $current_val, $user_id );
            $template = ob_get_clean();

            wp_send_json( $template );
        }

        public function dynamic_plans( $atbdp_query, $current_val, $user_id = '' ){
            $user_id        = $user_id ? $user_id : get_current_user_id();
            if ($atbdp_query->have_posts()) {
                $plans = $atbdp_query->posts; ?>
                    <div class="directorist-admin-form-plan-selection">
                        <label for=""><?php echo __('Select Plan', 'directorist-pricing-plans'); ?></label>
                        <div class="directorist-admin-form-plan-selection__content">
                            <select name="admin_plan">
                                <option value="null"><?php echo __('- Select -', 'directorist-pricing-plans'); ?></option>
                                <?php
                                foreach ($plans as $key => $value) {
                                    $class = apply_filters('atbdp_admin_plan_select_option_class', 'listing_plan', $value->ID);
                                    $active_plan = subscribed_package_or_PPL_plans($user_id, 'completed', $value->ID);
                                    $plan_metas = get_post_meta($value->ID);
                                    $plan_type = esc_attr($plan_metas['plan_type'][0]);
                                    $active = '';
                                    if ('package' === $plan_type && $active_plan) {
                                        $active = ' <span class="atbd_badge atbd_badge_open">' . __(' - Active', 'directorist-pricing-plans') . '</span>';
                                    }
                                    ?>
                                    <option class="<?php echo $class.' '.$plan_type ?>" value="<?php echo $value->ID; ?>" <?php echo selected($value->ID, $current_val); ?>><?php echo $value->post_title.$active; ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                            <span class="directorist_loader"></span>
                        </div>
                    </div>

                    <div id="directorist-allowances"></div>
                    <div id="directorist-claim-submit-notification"></div>
                    <div id="directorist-claim-warning-notification"></div>
                    <div class="directorist-admin-form-plan-action">
                        <a target="_blank" href="<?php echo esc_url(ATBDP_Permalink::get_fee_plan_page_link()); ?>" class="directorist-plans button"><?php echo __('Details', 'directorist-pricing-plans'); ?></a>
                        <a href="#" id="confirm_plan" class="button"><?php echo __('Save', 'directorist-pricing-plans'); ?></a>
                    </div>
                <?php
            }
        }

        public function atpp_changing_plan() {
            $listingID = ! empty( $_POST['listingID'] ) ? $_POST['listingID'] : '';

            $directory_type = get_post_meta( $listingID, '_directory_type', true );
            $meta_queries = array();
            $args = array(
                'post_type'          => 'atbdp_pricing_plans',
                'posts_per_page'     => -1,
                'status'             => 'publish',
            );

            $meta_queries[] = array(
                'relation'           => 'OR',
                array(
                    'key'           => '_hide_from_plans',
                    'compare'       => 'NOT EXISTS',
                ),
                array(
                    'key'           => '_hide_from_plans',
                    'value'         => 'yes',
                    'compare'       => '!=',
                ),
            );

            if( ! empty( $directory_type ) ) {
                $meta_queries['directory_type'] = [
                    'key'            => '_assign_to_directory',
                    'value'          => $directory_type,
                    'compare'        => '=',
                ];
            }
            $meta_queries = apply_filters('atbdp_plan_meta_query', $meta_queries);
            $count_meta_queries = count( $meta_queries );
            if( $count_meta_queries ) {
                $args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array('relation' => 'AND'), $meta_queries ) : $meta_queries;
            }

            $atbdp_query = new WP_Query( $args );
            if( $atbdp_query->have_posts() ) {
                global $post;
                $plans = $atbdp_query->posts;

                ob_start();
                printf('<label for="">%s</label><hr>', __('Select Plan', 'directorist-pricing-plans'));
                $template = ob_get_clean();

                foreach ($plans as $key => $value) {
                    $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed', $value->ID);
                    $plan_metas = get_post_meta($value->ID);
                    $unl = __('Unlimited', 'directorist-pricing-plans');
                    $plan_type = esc_attr($plan_metas['plan_type'][0]);
                    $fm_price = esc_attr(atpp_total_price($value->ID));
                    $fm_length = esc_attr( directorist_plan_lifetime( $value->ID ) );
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
                    $fresh_order = directorist_active_orders_without_listing( $value->ID );
                    if ( $fresh_order || ('package' === $plan_type && $active_plan) ) {
                        $active = ' <span class="atbd_badge atbd_badge_open">' . __('Active', 'directorist-pricing-plans') . '</span>';
                    }
                    ob_start();
                    ?>
                    <div class="dcl_pricing_plan_name">
                        <input type="radio" class="new_plan_id" id="<?php echo $value->ID; ?>" name="new_plan" value="<?php echo $value->ID; ?>">
                        <label for="<?php echo $value->ID; ?>">
                            <?php echo $value->post_title; ?>
                        </label>
                        <?php echo $active; ?>
                        <br>
                        <?php echo $allowances; ?>
                        <hr>
                    </div>
                    <?php
                    $template .= ob_get_clean();
                }
            }
            wp_send_json( array(
                'template' => $template
            ) );
        }

        public function directorist_plan_dynamic_fields() {
            $term_id = ! empty( $_POST['listing_type'] ) ? sanitize_text_field( $_POST['listing_type'] ) : '';
            $post_id = ! empty( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';

            $data = [
                'default_type'   => $term_id,
                'post'           => get_post( $post_id ),
             ];
            ob_start();
            ATPP_Data_Controller::dynamic_plan_fields( $data );
            $template = ob_get_clean();
            wp_send_json( $template );
        }


        public function plan_allowances_on_user_selection(){

            $directory_type = !empty( $_POST['directory_type'] ) ? sanitize_text_field( $_POST['directory_type'] ) : default_directory_type();
		    $post_id    	= !empty( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
            $user_id        = !empty( $_POST['user_id'] ) ? sanitize_text_field( $_POST['user_id'] ) : '';
            $current_val    = get_post_meta( $post_id, '_fm_plans', true );

            if ( isset( $post_id ) && get_post_status ( $post_id ) ) {
                $args = array('ID'=> $post_id,'post_author'=> $user_id);
                // update the post, which calls save_post again
                wp_update_post( $args );
           }

            $meta_queries = array();
            $args = array(
                'post_type'          => 'atbdp_pricing_plans',
                'posts_per_page'     => -1,
                'status'             => 'publish',
            );
            if( ! empty( $directory_type ) ) {
                $meta_queries['directory_type'] = [
                    'key'            => '_assign_to_directory',
                    'value'          => $directory_type,
                    'compare'        => '=',
                ];
            }
            $meta_queries = apply_filters('atbdp_plan_meta_query', $meta_queries);
            $count_meta_queries = count( $meta_queries );
            if( $count_meta_queries ) {
                $args['meta_query'] = ( $count_meta_queries > 1 ) ? array_merge( array('relation' => 'AND'), $meta_queries ) : $meta_queries;
            }

            $atbdp_query = new WP_Query( $args );

            ob_start();
            $this->dynamic_plans( $atbdp_query, $current_val, $user_id );
            $template = ob_get_clean();

            wp_send_json( $template );
        }


        public function atpp_gifting_plan(){

            $data           = array('error' => 0);
            $order_id       = isset($_POST["order_id"]) ? (int) ($_POST["order_id"]) : '';
            $post_id        = isset($_POST["post_id"]) ? (int) ($_POST["post_id"]) : '';
            $plan_id        = isset($_POST["plan_id"]) ? (int) ($_POST["plan_id"]) : '';
            $user_id        = isset($_POST['user_id']) ? $_POST['user_id'] : '';
            $listing_type   = isset($_POST["listing_type"]) ? esc_html($_POST["listing_type"]) : '';

            if( ( $plan_id === 'null' ) || empty( $plan_id ) ){
                update_post_meta( $post_id, '_fm_plans', '' );
                $data['message'] = __('Plan changed successfully!', 'directorist-pricing-plans');
                wp_send_json( $data );
            }

            $response     = directorist_validate_plan( $plan_id, $post_id, $order_id, $listing_type, $user_id );
            // wp_send_json( $response );

            if( !empty( $response['need_payment'] ) ){
                
                $order_id = wp_insert_post(array(
                    'post_content' => '',
                    'post_title' => sprintf('Order for the listing ID #%d', $post_id),
                    'post_status' => 'publish',
                    'post_author' => $user_id,
                    'post_type' => 'atbdp_orders',
                    'comment_status' => false,
                ));

                $gateway = 'free';
                // save required data as order post meta
                update_post_meta($order_id, '_transaction_id', wp_generate_password(15, false));
                update_post_meta($order_id, '_listing_id', [ $post_id ] );
                update_post_meta($order_id, '_payment_gateway', $gateway);
                update_post_meta($order_id, '_payment_status', 'completed');
                update_post_meta($post_id, '_plan_order_id', $order_id);
                update_post_meta($order_id, '_fm_plan_ordered', $plan_id);
                $data['message'] = __('Successfully assigned!', 'directorist-pricing-plans');
                wp_send_json( $data );

            }

            if( !empty( $response['error'] ) ){
                $data['validation_error'] = $response['error_msg'];
            }else{
                do_action('atbdp_plan_assigned', $post_id);
                $data['message'] = __('Plan changed successfully!', 'directorist-pricing-plans');
            }

            wp_send_json( $data );
        }

         /**
         * @since 1.6.6
         */

        public function dynamic_admin_listing_form_and_allowances()
        {
            $allowances = '';
            $plan_id        = isset($_POST['plan_id']) ? $_POST['plan_id'] : '';
            $user_id        = isset($_POST['user_id']) ? $_POST['user_id'] : '';
            if( $plan_id == 'null' ) die();
            if( 'package' !== package_or_PPL( $plan_id ) ) die;
		    $post_id    	= sanitize_text_field( $_POST['post_id'] );
            $featured       = get_post_meta( $post_id, '_featured', true );
            $listing_type   = $featured ? 'featured' : 'regular';
            $plan_purchased = subscribed_package_or_PPL_plans( $user_id, 'completed', $plan_id );
            $order_id       = get_post_meta($post_id, '_plan_order_id', true);
            $order_id       = ! empty( $order_id ) ? $order_id : ( $plan_purchased ? $plan_purchased[0]->ID : '');

            $remaining  = plans_remaining( $plan_id, $order_id );
            $featured   = $remaining['featured'];
            $regular    = $remaining['regular'];

            $active_orders = directorist_active_orders( $plan_id, $user_id );

            ob_start();
            if( count( $active_orders ) > 1 ){ ?>

                <div class="dpp-order-select-wrapper">
                    <form action="">
                        <div class="directorist-form-group">
                            <div class="directorist-form-label"><span><?php echo __( 'Active Orders', 'directorist-pricing-plans' )?></span></div>

                            <div class="directorist-dropdown dpp-order-select-dropdown" data-label="<?php esc_attr_e( 'Select active order', 'directorist-pricing-plans'); ?>" data-general_label="<?php esc_attr_e( 'General', 'directorist-pricing-plans');  ?>" data-featured_label="<?php esc_attr_e( 'Featured', 'directorist-pricing-plans'); ?>">
                                <select name="activated_order" class="select_active_order">
                                    <option value=""><?php echo __( 'Select an order', 'directorist-pricing-plans' )?></option>
                                    <?php 
                                        foreach( $active_orders as $order ){
                                            $plan_id = get_post_meta( $order, '_fm_plan_ordered'. true );?>
                                            <option <?php echo selected($order, $order_id); ?> value="<?php echo $order; ?>"><?php echo '#' . esc_attr( $order ); ?></option>
                                        <?php }?>
                                </select>
                            </div>

                            
                        </div>
                    </form>

                </div>


            <?php }else{
                ?>
                <div class="directorist-listing-type">
                    <h4 class="directorist-option-title"><?php _e('Choose Listing Type', 'directorist-pricing-plans') ?></h4>
                    <div class="directorist-listing-type_list">
                        <?php
                        if ( 'Unlimited' === $regular ) {
                        ?>
                            <div class="directorist-input-group --atbdp_inline">
                                <input id="regular" <?php echo ($listing_type == 'regular') ? 'checked' : ''; ?> type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                                <label for="regular"><?php _e(' Regular listing', 'directorist-pricing-plans') ?>
                                    <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-pricing-plans') ?></span>
                                </label>
                            </div>
                        <?php } else { ?>
                            <div class="directorist-input-group --atbdp_inline">
                                <input id="regular" <?php echo $featured == 0 ? 'disabled' : ($listing_type == 'regular' ? 'checked' : ''); ?> type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                                <label for="regular"><?php _e(' Regular listing', 'directorist-pricing-plans') ?>
                                    <span class="<?php echo $regular > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                        <?php echo '(' . $regular . __('Remaining', 'directorist-pricing-plans') . ')'; ?></span>
                                </label>
                            </div>
                        <?php } ?>

                        <?php
                        if ( 'Unlimited' === $featured ) {
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
                                <input id="featured" type="radio" <?php echo $featured == 0 ? 'disabled' : ($listing_type == 'featured' ? 'checked' : ''); ?> class="atbdp_radio_input" name="listing_type" value="featured">
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
            $allowances = ob_get_clean();

            wp_send_json_success( array(
                'allowances'            => $allowances,
            ) );

        }


	public function build_form_data( $type ) {
		$form_data              = array();
		$submission_form_fields = get_term_meta( $type, 'submission_form_fields', true );
		$excluded_fields = array( 'title', 'description', 'location', 'category', 'tag', 'privacy_policy', 'terms_conditions' );

		if ( !empty( $submission_form_fields['groups'] ) ) {
			foreach ( $submission_form_fields['groups'] as $group ) {

				$section           = $group;
				$section['fields'] = array();

				foreach ( $group['fields'] as $field ) {

					if ( in_array( $field, $excluded_fields ) ) {
						continue;
					}

					$section['fields'][ $field ] = $submission_form_fields['fields'][ $field ];
				}

				$form_data[] = $section;
			}
		}

		return $form_data;
	}

        /**
         * @since 1.6.6
         */

        public function plan_allowances()
        {
            $data          = [];
            $plan_id       = isset($_POST['plan_id']) ? $_POST['plan_id'] : '';
            $post_id       = isset($_POST['post_id']) ? $_POST['post_id'] : '';
            $order_id      = get_post_meta( $post_id, '_plan_order_id', true );
            $order_plan_id = get_post_meta( $order_id, '_fm_plans', true );
            $active_orders = directorist_active_orders( $plan_id, '', true );
            $first_active_order = ! empty( $active_orders ) ? $active_orders[0] : '';
            $order_id   = ( $order_plan_id != $plan_id ) ? $first_active_order : $order_id;

            // wp_send_json([
            //     'plan_id' => $plan_id,
            //     'post_id' => $post_id,
            //     'order_id' => $order_id,
            // ]);

            if( 'package' !== package_or_PPL( $plan_id ) ){
                wp_send_json( $data );
            }
            
            $remaining  = plans_remaining( $plan_id, $order_id );
            $featured   = $remaining['featured'];
            $regular    = $remaining['regular'];

            if( count( $active_orders ) > 1 ){ 
                
                foreach( $active_orders as $order ){
                    $valid_order = directorist_valid_order( $order, $plan_id );
                    if( ! $valid_order ){

                        $active_orders = array_diff($active_orders, [$order]);
                    }
                }
            }
            

            if( count( $active_orders ) > 1 ){ ?>

                <div class="dpp-order-select-wrapper">
                    <form action="">
                        <div class="directorist-form-group">
                            <div class="directorist-form-label"><span><?php echo __( 'Active Orders', 'directorist-pricing-plans' )?></span></div>

                            <div class="directorist-dropdown dpp-order-select-dropdown" data-label="<?php esc_attr_e( 'Select active order', 'directorist-pricing-plans'); ?>" data-general_label="<?php esc_attr_e( 'General', 'directorist-pricing-plans');  ?>" data-featured_label="<?php esc_attr_e( 'Featured', 'directorist-pricing-plans'); ?>">
                                <a href="" class="directorist-dropdown-toggle"><span class="directorist-dropdown-toggle__text"><?php echo __( 'Select an order', 'directorist-pricing-plans' )?></span></a>
                                <div class="directorist-dropdown-option">
                                    <ul>
                                        <?php
                                        foreach( $active_orders as $order ){
                                            $plan_id = get_post_meta( $order, '_fm_plan_ordered'. true );
                                            ?>
                                            <li><a href="" data-value="<?php echo $order; ?>"><?php echo '#' . esc_attr( $order ); ?></a></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>

                            
                        </div>
                    </form>

                </div>


            <?php }else{
            ob_start();
            ?>
            <div class="directorist-listing-type">
                <?php $listing_type = !empty($listing_info['listing_type']) ? $listing_info['listing_type'] : ''; ?>

                <h4 class="directorist-option-title"><?php _e('Choose Listing Type', 'directorist-pricing-plans') ?></h4>
                <div class="directorist-listing-type_list">
                    <?php
                    if ( 'Unlimited' === $regular ) {
                    ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" <?php echo ($listing_type == 'regular') ? 'checked' : ''; ?> type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php _e(' Regular listing', 'directorist-pricing-plans') ?>
                                <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-pricing-plans') ?></span>
                            </label>
                        </div>
                    <?php } else { ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" <?php echo $featured == 0 ? 'disabled' : '' ?> <?php echo ($listing_type == 'regular') ? 'checked' : ''; ?> type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php _e(' Regular listing', 'directorist-pricing-plans') ?>
                                <span class="<?php echo $regular > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                    <?php echo '(' . $regular . __('Remaining', 'directorist-pricing-plans') . ')'; ?></span>
                            </label>
                        </div>
                    <?php } ?>

                    <?php
                    if ( 'Unlimited' === $featured ) {
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
                            <input id="featured" type="radio" <?php echo $featured == 0 ? 'disabled' : '' ?> <?php echo ($listing_type == 'featured') ? 'checked' : ''; ?> class="atbdp_radio_input" name="listing_type" value="featured">
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
            $data['html'] = ob_get_clean();

            wp_send_json( $data );
        }

                /**
         * @since 1.3.2
         */

        public function atpp_submit_changing_plan()
        {

            $data           = array('error' => 0);
            $order_id       = isset($_POST["order_id"]) ? (int) ($_POST["order_id"]) : '';
            $post_id        = isset($_POST["post_id"]) ? (int) ($_POST["post_id"]) : '';
            $plan_id        = isset($_POST["plan_id"]) ? (int) ($_POST["plan_id"]) : '';
            $listing_type   = isset($_POST["listing_type"]) ? esc_html($_POST["listing_type"]) : '';

            if( ( $plan_id === 'null' ) || empty( $plan_id ) ){
                update_post_meta( $post_id, '_fm_plans', '' );

                atpp_need_listing_to_refresh( get_current_user_id(), $post_id, $order_id );
                
                $data['message'] = __('Plan changed successfully!', 'directorist-pricing-plans');
                wp_send_json( $data );
            }

            $response = directorist_validate_plan( $plan_id, $post_id, $order_id, $listing_type );

            if( !empty( $response['need_payment'] ) ){
                $data['checkout_url'] = $response['redirect_url'];
                $data['take_payment'] = 'plan';
            }

            if( !empty( $response['error'] ) ){
                $data['validation_error'] = $response['error_msg'];
            }else{
                do_action('atbdp_plan_assigned', $post_id);
                $data['message'] = __('Plan changed successfully!', 'directorist-pricing-plans');
            }

            wp_send_json( $data );
            
        }
    }
endif;