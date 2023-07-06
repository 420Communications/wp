<?php
defined( 'ABSPATH' ) || die( 'Direct access is not allowed.' );
/**
 * @since 1.7.4
 * @package Directorist
 */
if ( ! class_exists( 'ATPP_Data_Controller' ) ):

    class ATPP_Data_Controller {
        public $meta         = [];
        public $need_payment = false;
        public function __construct() {
            add_action( 'add_meta_boxes', [$this, 'atpp_add_meta_boxes'] );
            add_action( 'save_post', [$this, 'atpp_save_meta_data'] );
            add_filter( 'atbdp_listing_form_submission_info', [$this, 'atpp_process_the_selected_plans'] );
            add_action( 'atbdp_before_processing_submitted_listing_frontend', [$this, 'validate_submission'] );
            add_action( 'atbdp_order_status_changed', [$this, 'atpp_order_status_changed'], 10, 3 );
            add_action( 'atbdp_order_completed', [$this, 'free_order_completed'], 10, 2 );
            add_action( 'atbdp_before_renewal', [$this, 'atpp_before_renewal'] );
            add_filter( 'atbdp_ultimate_listing_meta_user_submission', [$this, 'listing_meta_user_submission'], 10, 2 );
            add_filter( 'postbox_classes_atbdp_pricing_plans_atbdp-plan-details', [$this, 'add_metabox_classes'] );
            add_filter( 'postbox_classes_atbdp_pricing_plans_directorist-plan-field-details', [$this, 'add_metabox_classes'] );
            add_filter( 'postbox_classes_atbdp_pricing_plans_atbdp-directory-type', [$this, 'add_metabox_type_classes'] );

            add_action( 'after_delete_post', [$this, 'update_listing_order'], 10, 2 );
            add_action( 'trashed_post', [$this, 'update_listing_order_status'] );
            add_action( 'directorist_listing_deleted', [$this, 'update_listing_order_status'] );

        }

        public function update_listing_order_status( $post_id ) {
            if ( ATBDP_POST_TYPE === get_post_type( $post_id ) ) {
                $this->update_order_status( $post_id );
            }
        }

        public function update_listing_order( $post_id, $post ) {
            if ( ATBDP_POST_TYPE === get_post_type( $post_id ) ) {
                $this->update_order_status( $post_id );
            }
        }

        public function update_order_status( $post_id ) {
            $order_id        = get_post_meta( $post_id, '_plan_order_id', true );
            $legacy_order_id = get_post_meta( $post_id, '_plan_order_id', true );
            $$order_id       = $order_id ? $order_id : $legacy_order_id;
            if ( ! empty( $order_id ) ) {
                update_post_meta( $order_id, '_order_status', '' );
            }
        }

        public function add_metabox_type_classes( $classes ) {

            array_push( $classes, 'directorist_postbox' );
            array_push( $classes, 'directorist_postbox_directory_types' );

            return $classes;
        }

        public function add_metabox_classes( $classes ) {

            array_push( $classes, 'directorist_postbox' );

            return $classes;
        }

        public function listing_meta_user_submission( $meta, $info ) {
            $meta['_listing_type'] = ! empty( $info['listing_type'] ) ? sanitize_text_field( $info['listing_type'] ) : '';
            return $meta;
        }

        /**
         * @since 1.3.2
         */

        public function atpp_before_renewal( $listing_id ) {
            update_post_meta( $listing_id, '_refresh_renewal_token', 1 );
            update_post_meta( $listing_id, '_renew_with_plan', 1 );
            $url = add_query_arg( 'renew_with_plan', $listing_id, ATBDP_Permalink::get_dashboard_page_link() );
            wp_safe_redirect( $url );
            exit;
        }

        public function free_order_completed( $order_id, $listing_id ) {

            if( ! $order_id ) {
                return;
            }
            $user_id = get_current_user_id();
            $listing_id   = get_post_meta( $order_id, '_listing_id', true );
            if ( ! empty( $listing_id ) ) {
                if ( is_array( $listing_id ) && count( $listing_id ) > 1 ) {
                    foreach ( $listing_id as $id ) {

                        $directory_type = get_post_meta( $id, '_directory_type', true );
                        if ( $directory_type ) {
                            $new_l_status = get_term_meta( $directory_type, 'new_listing_status', true );
                        }

                        atpp_need_listing_to_refresh( $user_id, $id, $order_id );
                        wp_update_post( [
                            'ID'          => $id,
                            'post_status' => apply_filters( 'atpp_reviewed_listing_status_controller_argument', $new_l_status ),
                        ] );
                    }
                } else {
                    $directory_type = get_post_meta( $listing_id, '_directory_type', true );
                    if ( $directory_type ) {
                        $new_l_status = get_term_meta( $directory_type, 'new_listing_status', true );
                    }
                    atpp_need_listing_to_refresh( $user_id, $listing_id, $order_id );
                    wp_update_post( [
                        'ID'          => $listing_id,
                        'post_status' => apply_filters( 'atpp_reviewed_listing_status_controller_argument', $new_l_status ),
                    ] );
                }
            }

        }

        public function atpp_order_status_changed( $new_status, $old_status, $order_id ) {
            
            $user_id = get_post_field( 'post_author', $order_id );
            $new_l_status = 'pending';
            $listing_id   = get_post_meta( $order_id, '_listing_id', true );
			
			if( 'completed' !== $new_status ) {
				return;
			}
			
			if ( empty( $listing_id ) ) {
				return;
			}

			if ( is_array( $listing_id ) && count( $listing_id ) ) {
				foreach ( $listing_id as $id ) {

					$directory_type = get_post_meta( $id, '_directory_type', true );
					if ( $directory_type ) {
						$new_l_status = get_term_meta( $directory_type, 'new_listing_status', true );
					}

					atpp_need_listing_to_refresh( $user_id, $id, $order_id );
					wp_update_post( [
						'ID'          => $id,
						'post_status' => apply_filters( 'atpp_reviewed_listing_status_controller_argument', $new_l_status ),
					] );
				}
			} else {
				$directory_type = get_post_meta( $listing_id, '_directory_type', true );
				if ( $directory_type ) {
					$new_l_status = get_term_meta( $directory_type, 'new_listing_status', true );
				}

				atpp_need_listing_to_refresh( $user_id, $listing_id, $order_id );
				
				wp_update_post( [
					'ID'          => $listing_id,
					'post_status' => apply_filters( 'atpp_reviewed_listing_status_controller_argument', $new_l_status ),
				] );
			}
        }

        public function validate_submission( $info ) {

            $is_editing = ( ! empty( $info['edited_listing'] ) ) ? true : false;
            $error      = [];
            $data       = [];

            if ( ! $is_editing ) {

                $plan_id         = isset( $info['plan_id'] ) ? $info['plan_id'] : '';
                $listing_type    = isset( $info['listing_type'] ) ? $info['listing_type'] : '';
                $activated_order = ! empty( $info['activated_order'] ) ? $info['activated_order'] : '';
                $plan_type       = package_or_PPL( $plan_id );

                $plan_purchased = subscribed_package_or_PPL_plans( get_current_user_id(), 'completed', $plan_id );
                $order__id      = ! empty( $plan_purchased ) ? $plan_purchased[0]->ID : '';
                $order_id       = ! empty( $activated_order ) ? $activated_order : $order__id;

                if ( $order_id ) {
                    $remaining = plans_remaining( $plan_id, $order_id );
                    $featured  = $remaining['featured'];
                    $regular   = $remaining['regular'];

                    $package_length = directorist_plan_lifetime( $plan_id );
                    $package_length = $package_length ? $package_length : '1';
                    // Current time
                    $start_date   = get_the_date( '', $order_id );
                    $expired_date = '';

                    if ( directorist_validate_date( $start_date ) ) {
                        $date = new DateTime( $start_date );
                        $date->add( new DateInterval( "P{$package_length}D" ) ); // set the interval in days
                        $expired_date = $date->format( 'Y-m-d H:i:s' );
                    }

                    $current_d      = current_time( 'mysql' );
                    $remaining_days = ( $expired_date > $current_d ) ? ( floor( strtotime( $expired_date ) / ( 60 * 60 * 24 ) ) - floor( strtotime( $current_d ) / ( 60 * 60 * 24 ) ) ) : 0; //calculate the number of days remaining in a plan

                    $regular_exit  = ( 'Unlimited' !== $regular ) && empty( $regular ) ? true : false;
                    $featured_exit = ( 'Unlimited' !== $featured ) && empty( $featured ) ? true : false;

                    // wp_send_json([
                    //     'error' => true,
                    //     'boll' =>  $regular_exit && ( $featured_exit )  || ($remaining_days < 0),
                    //     'featured_exit' => $featured_exit,
                    //     'regular_exit' => $regular_exit,
                    //     'remaining_days' => $remaining_days,
                    // ]);
                    if (  ( $regular_exit && $featured_exit ) || ( $remaining_days < 0 ) ) {
                        $msg = __( 'You have crossed the limit! Please try again', 'directorist-pricing-plans' );
                        if (  ( 'pay_per_listng' != $plan_type ) ) {
                            update_post_meta( $order_id, '_order_status', 'exit' );
                            $this->need_payment = true;
                        }
                    } else {
                        if (  ( 'regular' === $listing_type ) && ( 'package' === $plan_type ) ) {
                            if ( $regular_exit ) {
                                $msg = __( 'You have already crossed your limit for regular listing, please try again.', 'directorist-pricing-plans' );
                                array_push( $error, $msg );
                            }
                        }

                        if (  ( 'featured' === $listing_type ) && ( 'package' === $plan_type ) ) {
                            if ( $featured_exit ) {
                                $msg = __( 'You have already crossed your limit for featured listing, please try again', 'directorist-pricing-plans' );
                                array_push( $error, $msg );
                            }
                        }
                    }

                    if ( $error ) {
                        $data['error_msg'] = $error;
                        $data['error']     = true;
                        wp_send_json( $data );
                    }
                }

            }
        }

        /**
         * @return mixed     validate data and handle errors
         * @since     1.8.0
         * @param     string      $info    all submitted data.
         */
        public function atpp_process_the_selected_plans( $data ) {
            $info         = $_POST;
            $error        = [];
            $skip_payment = false;
            $listing_id   = ! empty( $data['id'] ) ? $data['id'] : '';
            if ( ! get_directorist_option( 'fee_manager_enable', 1 ) ) {
                return $data;
            }
            $is_editing = ( ! empty( $data['edited_listing'] ) ) ? true : false;

            $user_id         = get_current_user_id();
            $old_plan_id     = get_post_meta( $listing_id, '_fm_plans', true );
            $listing_type    = isset( $info['listing_type'] ) ? $info['listing_type'] : '';
            $plan_id         = isset( $info['plan_id'] ) ? $info['plan_id'] : $old_plan_id;
            $activated_order = ! empty( $info['activated_order'] ) ? $info['activated_order'] : '';
            $plan_type       = package_or_PPL( $plan_id );
            $checkout_url    = add_query_arg( 'plan_id', $plan_id, ATBDP_Permalink::get_checkout_page_link( $listing_id ) );

            $plan_purchased = subscribed_package_or_PPL_plans( $user_id, 'completed', $plan_id );
            $order__id      = ! empty( $plan_purchased ) ? $plan_purchased[0]->ID : '';
            $order_id       = ! empty( $activated_order ) ? $activated_order : $order__id;
            $order_plan     = get_post_meta( $order_id, '_listing_id', true );
            $order_plan     = ! empty( $order_plan ) ? [ $order_plan ] : [];
            $fresh_active_order = directorist_active_orders_without_listing( $plan_id );

            if( ! empty( $listing_id ) ){
                array_push( $order_plan, $listing_id );
            }

            if ( directorist_direct_purchase() && ( 'package' !== $plan_type ) && $fresh_active_order ) {
                $skip_payment = true;
                update_post_meta( $order_id, '_listing_id', $order_plan );
                update_post_meta( $listing_id, '_plan_order_id', $order_id );
                update_post_meta( $listing_id, '_listing_order_id', $order_id );
            }

            if (  ( 'package' === $plan_type ) && $plan_purchased ) {
                update_post_meta( $order_id, '_listing_id', $order_plan );
                update_post_meta( $listing_id, '_plan_order_id', $order_id );
                update_post_meta( $listing_id, '_listing_order_id', $order_id );
            }

            update_post_meta( $listing_id, '_fm_plans', $plan_id );

            if ( ! $order_id || $this->need_payment ) {
                $data['redirect_url'] = $checkout_url;
                $data['need_payment'] = true;

                wp_update_post( [
                    'ID'          => $listing_id,
                    'post_status' => 'pending',
                ] );
            }

            if ( $is_editing ) {
                return $data;
            }

            $package_length = directorist_plan_lifetime( $plan_id );
            $package_length = $package_length ? $package_length : '1';

            $current_d = current_time( 'mysql' );
            // Calculate new date
            $date = new DateTime( $current_d );
            $date->add( new DateInterval( "P{$package_length}D" ) ); // set the interval in days
            $expired_date      = $date->format( 'Y-m-d H:i:s' );
            $is_never_expaired = get_post_meta( $plan_id, 'fm_length_unl', true );
            if ( $is_never_expaired ) {
                array_push( $this->meta, ['_plan_order_id' => $order_id] );
                update_post_meta( $listing_id, '_never_expire', '1' );
            } else {
                update_post_meta( $listing_id, '_never_expire', 0 );
                update_post_meta( $listing_id, '_expiry_date', $expired_date );
            }

            if ( 'featured' == $listing_type ) {
                update_post_meta( $listing_id, '_featured', '1' );
            }

            update_post_meta( $listing_id, '_need_to_refresh', '1' );
            // for dev
            do_action( 'atbdp_plan_assigned', $listing_id );

            // update the claim status
            $fm_claim = get_post_meta( $plan_id, '_fm_claim', true );
            if ( ! empty( $fm_claim ) ) {
                update_post_meta( $listing_id, '_claimed_by_admin', 1 );
            }

            if ( 'pay_per_listng' === package_or_PPL( $plan_id ) ) {

                if ( PPL_with_featured( $plan_id ) ) {
                    update_post_meta( $listing_id, '_featured', '1' );
                }
                if ( ! $plan_purchased ) {
                    $data['redirect_url'] = $checkout_url;
                    $data['need_payment'] = true;
                    return $data;
                }
            }

            if ( $error ) {
                $data['error_msg'] = $error;
                $data['error']     = true;
            }

            $payment_status            = Directorist\Helper::get_listing_payment_status( $listing_id );
            $rejectable_payment_status = ['failed', 'cancelled', 'refunded'];
            $need_payment              = ( empty( $payment_status ) || in_array( $payment_status, $rejectable_payment_status ) ) ? true : false;

            if ( $need_payment && ! $skip_payment ) {
                $data['redirect_url'] = $checkout_url;
                $data['need_payment'] = true;

                wp_update_post( [
                    'ID'          => $listing_id,
                    'post_status' => 'pending',
                ] );
            }
            // return [
            //     'need_payment' => $need_payment,
            //     'skip_payment' => $skip_payment,
            //     'payment_status' => $payment_status,
            // ];

            return $data;
        }

        private function save_listing_meta( $post_id ) {
            $p          = $_POST;
            $exp_dt     = ! empty( $p['exp_date'] ) ? atbdp_sanitize_array( $p['exp_date'] ) : [];
            $directory  = get_post_meta( $post_id, '_directory_type', true );
            $expiration = get_term_meta( $directory, 'default_expiration', true );

            if ( ! is_empty_v( $exp_dt ) && ! empty( $exp_dt['aa'] ) ) {
                $exp_dt = [
                    'year'  => (int) $exp_dt['aa'],
                    'month' => (int) $exp_dt['mm'],
                    'day'   => (int) $exp_dt['jj'],
                    'hour'  => (int) $exp_dt['hh'],
                    'min'   => (int) $exp_dt['mn'],
                ];
                $exp_dt = get_date_in_mysql_format( $exp_dt );
            } else {
                $exp_dt = calc_listing_expiry_date( '', $expiration ); // get the expiry date in mysql date format using the default expiration date.
            }
            update_post_meta( $post_id, '_expiry_date', $exp_dt );

        }

        /*
         * save data to database from the metaboxes
         */

        public function atpp_save_meta_data( $post_id ) {
            if ( ! isset( $_POST['post_type'] ) ) {
                return $post_id;
            }

            // If this is an autosave, our form has not been submitted, so we don't want to do anything
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return $post_id;
            }

            // Check the logged in user has permission to edit this post
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }

            if ( ! is_admin() ) {
                return $post_id;
            }

            if ( 'at_biz_dir' === $_POST['post_type'] ) {

                $plan_id    = get_post_meta( $post_id, '_fm_plans', true );
                $admin_plan = isset( $_POST['admin_plan'] ) ? $_POST['admin_plan'] : '';
                $user_id    = isset( $_POST['post_author_override'] ) ? $_POST['post_author_override'] : '';
                // $sub_plan_id    = get_post_meta($post_id, '_fm_plans', true);
                $order_id = get_post_meta( $post_id, '_plan_order_id', true );

                update_post_meta( $post_id, '_fm_plans_by_admin', 1 );

                if ( 'null' === $admin_plan ) {
                    $this->save_listing_meta( $post_id );
                } else {
                    atpp_need_listing_to_refresh( $user_id, $post_id, $order_id );
                }

                if ( 'pay_per_listng' === package_or_PPL( $plan_id ) ) {

                    if ( PPL_with_featured( $plan_id ) ) {
                        update_post_meta( $post_id, '_featured', '1' );
                    } else {
                        update_post_meta( $post_id, '_featured', '' );
                    }
                }
            }

            // Check if "atbdp_fee_details_nonce" nonce is set
            if ( isset( $_POST['atbdp_fee_details_nonce'] ) ) {

                // Verify that the nonce is valid
                if ( wp_verify_nonce( $_POST['atbdp_fee_details_nonce'], 'atbdp_save_fee_details' ) ) {

                    // OK to save meta data
                    $assign_to_directory = ! empty( $_POST['assign_to_directory'] ) ? $_POST['assign_to_directory'] : default_directory_type();
                    update_post_meta( $post_id, '_assign_to_directory', $assign_to_directory );

                    $plan_type = ! empty( $_POST['plan_type'] ) ? $_POST['plan_type'] : '';
                    update_post_meta( $post_id, 'plan_type', $plan_type );

                    $free_plan = ! empty( $_POST['free_plan'] ) ? $_POST['free_plan'] : '';
                    update_post_meta( $post_id, 'free_plan', $free_plan );

                    $fm_price = ! empty( $_POST['fm_price'] ) ? $_POST['fm_price'] : '';
                    update_post_meta( $post_id, 'fm_price', $fm_price );

                    $plan_tax = isset( $_POST['plan_tax'] ) ? $_POST['plan_tax'] : '0';
                    update_post_meta( $post_id, 'plan_tax', $plan_tax );

                    $plan_tax_type = isset( $_POST['plan_tax_type'] ) ? $_POST['plan_tax_type'] : '0';
                    update_post_meta( $post_id, 'plan_tax_type', $plan_tax_type );

                    $fm_tax = isset( $_POST['fm_tax'] ) ? $_POST['fm_tax'] : '0';
                    update_post_meta( $post_id, 'fm_tax', $fm_tax );

                    $hide_description = isset( $_POST['hide_description'] ) ? sanitize_textarea_field( $_POST['hide_description'] ) : '';
                    update_post_meta( $post_id, 'hide_description', $hide_description );

                    $fm_description = isset( $_POST['fm_description'] ) ? sanitize_textarea_field( $_POST['fm_description'] ) : '';
                    update_post_meta( $post_id, 'fm_description', $fm_description );

                    $fm_length = isset( $_POST['fm_length'] ) ? (int) $_POST['fm_length'] : '0';
                    update_post_meta( $post_id, 'fm_length', $fm_length );

                    $fm_length_unl = isset( $_POST['fm_length_unl'] ) ? sanitize_text_field( $_POST['fm_length_unl'] ) : '';
                    update_post_meta( $post_id, 'fm_length_unl', $fm_length_unl );

                    $atpp_recurring = isset( $_POST['atpp_recurring'] ) ? sanitize_text_field( $_POST['atpp_recurring'] ) : '';
                    update_post_meta( $post_id, '_atpp_recurring', $atpp_recurring );

                    $hide_recurring = isset( $_POST['hide_recurring'] ) ? sanitize_text_field( $_POST['hide_recurring'] ) : '';
                    update_post_meta( $post_id, 'hide_recurring', $hide_recurring );

                    $recurrence_period_term = isset( $_POST['recurrence_period_term'] ) ? sanitize_text_field( $_POST['recurrence_period_term'] ) : '';
                    update_post_meta( $post_id, '_recurrence_period_term', $recurrence_period_term );

                    $recurrence_time = isset( $_POST['recurrence_time'] ) ? sanitize_text_field( $_POST['recurrence_time'] ) : '';
                    update_post_meta( $post_id, '_recurrence_time', $recurrence_time );

                    $is_featured_listing = isset( $_POST['is_featured_listing'] ) ? ( $_POST['is_featured_listing'] ) : '';
                    update_post_meta( $post_id, 'is_featured_listing', $is_featured_listing );

                    $hide_listing_featured = isset( $_POST['hide_listing_featured'] ) ? ( $_POST['hide_listing_featured'] ) : '';
                    update_post_meta( $post_id, 'hide_listing_featured', $hide_listing_featured );

                    $num_regular = isset( $_POST['num_regular'] ) ? (int) ( $_POST['num_regular'] ) : '';
                    update_post_meta( $post_id, 'num_regular', $num_regular );

                    $hide_listings = isset( $_POST['hide_listings'] ) ? sanitize_text_field( $_POST['hide_listings'] ) : '';
                    update_post_meta( $post_id, 'hide_listings', $hide_listings );

                    $num_regular_unl = isset( $_POST['num_regular_unl'] ) ? sanitize_text_field( $_POST['num_regular_unl'] ) : '';
                    update_post_meta( $post_id, 'num_regular_unl', $num_regular_unl );

                    $num_featured = isset( $_POST['num_featured'] ) ? (int) ( $_POST['num_featured'] ) : '';
                    update_post_meta( $post_id, 'num_featured', $num_featured );

                    $num_featured_unl = isset( $_POST['num_featured_unl'] ) ? sanitize_text_field( $_POST['num_featured_unl'] ) : '';
                    update_post_meta( $post_id, 'num_featured_unl', $num_featured_unl );

                    $hide_featured = isset( $_POST['hide_featured'] ) ? sanitize_text_field( $_POST['hide_featured'] ) : '';
                    update_post_meta( $post_id, 'hide_featured', $hide_featured );

                    $default_pln = isset( $_POST['default_pln'] ) ? sanitize_text_field( $_POST['default_pln'] ) : '';
                    update_post_meta( $post_id, 'default_pln', $default_pln );

                    $hide_from_plans = isset( $_POST['hide_from_plans'] ) ? sanitize_text_field( $_POST['hide_from_plans'] ) : '';
                    update_post_meta( $post_id, '_hide_from_plans', $hide_from_plans );

                    $cf_owner = isset( $_POST['cf_owner'] ) ? sanitize_text_field( $_POST['cf_owner'] ) : '';
                    update_post_meta( $post_id, 'cf_owner', $cf_owner );

                    $hide_Cowner = isset( $_POST['hide_Cowner'] ) ? sanitize_text_field( $_POST['hide_Cowner'] ) : '';
                    update_post_meta( $post_id, 'hide_Cowner', $hide_Cowner );

                    $fm_cs_review = isset( $_POST['fm_cs_review'] ) ? sanitize_text_field( $_POST['fm_cs_review'] ) : '';
                    update_post_meta( $post_id, 'fm_cs_review', $fm_cs_review );

                    $hide_review = isset( $_POST['hide_review'] ) ? sanitize_text_field( $_POST['hide_review'] ) : '';
                    update_post_meta( $post_id, 'hide_review', $hide_review );

                    $exclude_categories = isset( $_POST['exclude_categories'] ) ? sanitize_text_field( $_POST['exclude_categories'] ) : '';
                    $exclude_cat        = ( ! empty( $_POST['exclude_cat'] ) && $exclude_categories ) ? atbdp_sanitize_array( $_POST['exclude_cat'] ) : [];
                    update_post_meta( $post_id, 'exclude_cat', $exclude_cat );

                    $hide_categories = isset( $_POST['hide_categories'] ) ? sanitize_text_field( $_POST['hide_categories'] ) : '';
                    update_post_meta( $post_id, 'hide_categories', $hide_categories );

                    $fm_claim = isset( $_POST['fm_claim'] ) ? sanitize_text_field( $_POST['fm_claim'] ) : '';
                    update_post_meta( $post_id, '_fm_claim', $fm_claim );

                    $hide_claim = isset( $_POST['hide_claim'] ) ? sanitize_text_field( $_POST['hide_claim'] ) : '';
                    update_post_meta( $post_id, '_hide_claim', $hide_claim );

                    // Booking
                    $fm_booking = isset( $_POST['fm_booking'] ) ? sanitize_text_field( $_POST['fm_booking'] ) : '';
                    update_post_meta( $post_id, '_fm_booking', $fm_booking );

                    $hide_booking = isset( $_POST['hide_booking'] ) ? sanitize_text_field( $_POST['hide_booking'] ) : '';
                    update_post_meta( $post_id, '_hide_booking', $hide_booking );

                    // Live Chat
                    $fm_live_chat = isset( $_POST['fm_live_chat'] ) ? sanitize_text_field( $_POST['fm_live_chat'] ) : '';
                    update_post_meta( $post_id, '_fm_live_chat', $fm_live_chat );

                    $hide_live_chat = isset( $_POST['hide_live_chat'] ) ? sanitize_text_field( $_POST['hide_live_chat'] ) : '';
                    update_post_meta( $post_id, '_hide_live_chat', $hide_live_chat );

                    // Mark as Sold
                    $fm_mark_as_sold = isset( $_POST['fm_mark_as_sold'] ) ? sanitize_text_field( $_POST['fm_mark_as_sold'] ) : '';
                    update_post_meta( $post_id, '_fm_mark_as_sold', $fm_mark_as_sold );

                    $hide_mark_as_sold = isset( $_POST['hide_mark_as_sold'] ) ? sanitize_text_field( $_POST['hide_mark_as_sold'] ) : '';
                    update_post_meta( $post_id, '_hide_mark_as_sold', $hide_mark_as_sold );

                    if ( ! empty( $_POST['form_fields'] ) ) {
                        foreach ( $_POST['form_fields'] as $key => $value ) {
                            update_post_meta( $post_id, '_' . $key, $value );
                        }
                    }

                }
            }

            return $post_id;

        }

        /**
         * Register meta boxes for Pricing Plans.
         *
         * @since    1.0.0
         * @access   public
         */
        public function atpp_add_meta_boxes() {
            $multi_directory = get_directorist_option( 'enable_multi_directory', false );
            if (  ( count( $this->directory_types() ) > 1 ) && ( $multi_directory ) ) {
                remove_meta_box( 'atbdp-directory-type', 'atbdp_pricing_plans', 'normal' );
                add_meta_box( 'atbdp-directory-type', __( 'Select Type', 'directorist-pricing-plans' ), [$this, 'directory_type_selection'], 'atbdp_pricing_plans', 'normal', 'high' );
            }
            remove_meta_box( 'atbdp-plan-details', 'atbdp_pricing_plans', 'normal' );
            remove_meta_box( 'directorist-plan-field-details', 'atbdp_pricing_plans', 'normal' );
            remove_meta_box( 'slugdiv', 'atbdp_pricing_plans', 'normal' );

            add_meta_box( 'atbdp-plan-details', __( 'Basic Configurations', 'directorist-pricing-plans' ), [$this, 'atbdp_meta_box_plan_details'], 'atbdp_pricing_plans', 'normal', 'high' );
            add_meta_box( 'directorist-plan-field-details', __( 'Field Configurations', 'directorist-pricing-plans' ), [$this, 'atbdp_meta_box_plan_field_details'], 'atbdp_pricing_plans', 'advanced', 'default' );
        }

        public function directory_types() {
            $listing_types = get_terms( [
                'taxonomy'   => 'atbdp_listing_types',
                'hide_empty' => false,
                'orderby'    => 'date',
                'order'      => 'DSCE',
            ] );
            return $listing_types;
        }

        public function directory_type_selection( $post ) {
            $listing_types = $this->directory_types();
            $default       = default_directory_type();
            $current_type  = get_post_meta( $post->ID, '_assign_to_directory', true );
            $value         = $current_type ? $current_type : $default;
            $data          = [
                'post'            => $post,
                'directory_types' => $listing_types,
                'value'           => $value,
            ];

            /**
             * Display the "Field Details" meta box.
             */
            ATBDP_Pricing_Plans()->load_template( 'plan_directory_type', $data );

        }

        public function atbdp_meta_box_plan_details( $post ) {

            // Add a nonce field so we can check for it later
            wp_nonce_field( 'atbdp_save_fee_details', 'atbdp_fee_details_nonce' );
            /**
             * Display the "Field Details" meta box.
             */
            ATBDP_Pricing_Plans()->load_template( 'admin-meta-fields', ['post' => $post] );

        }
        public function atbdp_meta_box_plan_field_details( $post ) {
            $multi_directory = get_directorist_option( 'enable_multi_directory', false );
            $default         = default_directory_type();
            $current_type    = get_post_meta( $post->ID, '_assign_to_directory', true );
            $value           = $current_type ? $current_type : $default;
            $data            = [
                'post'         => $post,
                'default_type' => ! $multi_directory ? $default : $value,
                'post_meta'    => get_post_meta( $post->ID ),
            ];

            /**
             * Display the "Field Details" meta box.
             */
            ?>
<div class="plan_dynamic_field">
    <?php
    $this::dynamic_plan_fields( $data );
            ?>
</div>
<?php

        }

        public static function dynamic_plan_fields( $data ) {
            ATBDP_Pricing_Plans()->load_template( 'admin-meta-form-fields', $data );
        }

    }
endif;