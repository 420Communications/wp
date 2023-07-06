<?php
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! function_exists( 'swbd_pricing_plan__include_additional_submission_fields' ) ) {
    function swbd_pricing_plan__include_additional_submission_fields( array $args = [] ) {
        $default = ['submission_form_fields' => []];
        $args    = array_merge( $default, $args );

        $submission_form_fields = ( isset( $args['submission_form_fields'] ) ) ? $args['submission_form_fields'] : [];

        foreach ( $submission_form_fields as $field_key => $field_args ) {

            if ( ! empty( $field_args['modules'] ) ) {
                $_modules = ( is_string( $field_args['modules'] ) ) ? json_decode( $field_args['modules'], true ) : $field_args['modules'];

                if ( is_null( $_modules ) || ! is_array( $_modules ) ) {
                    continue;}

                $_field_key_index = array_search( $field_key, array_keys( $submission_form_fields ) );
                $_counter         = 1;

                foreach ( $_modules as $_module_key => $_module_args ) {
                    $_field_module_key_index = $_field_key_index + $_counter;
                    $_module                 = [];
                    $_module[$_module_key]   = $_module_args;

                    array_splice( $submission_form_fields, $_field_module_key_index, 0, $_module );

                    $_counter++;
                }

            }

        }

        return $submission_form_fields;
    }
}

if ( ! function_exists( 'atbdp_get_option' ) ) {

    /**
     * @return array    It returns the role of the users
     */
    function all_rules() {
        return apply_filters( 'dgrc_default_user_roles', [
            [
                'value' => 'administrator',
                'label' => __( 'Administrator', 'directorist-pricing-plans' ),
            ],
            [
                'value' => 'editor',
                'label' => __( 'Editor', 'directorist-pricing-plans' ),
            ],
            [
                'value' => 'author',
                'label' => __( 'Author', 'directorist-pricing-plans' ),
            ],
            [
                'value' => 'contributor',
                'label' => __( 'Contributor', 'directorist-pricing-plans' ),
            ],
            [
                'value' => 'subscriber',
                'label' => __( 'Subscriber', 'directorist-pricing-plans' ),
            ],
        ] );
    }

    /**
     * It retrieves an option from the database if it exists and returns false if it is not exist.
     * It is a custom function to get the data of custom setting page
     * @param string $name The name of the option we would like to get. Eg. map_api_key
     * @param string $group The name of the group where the option is saved. eg. general_settings
     * @param mixed $default Default value for the option key if the option does not have value then default will be returned
     * @return mixed    It returns the value of the $name option if it exists in the option $group in the database, false otherwise.
     */

    function atbdp_get_option( $name, $group, $default = false ) {
        // at first get the group of options from the database.
        // then check if the data exists in the array and if it exists then return it
        // if not, then return false
        if ( empty( $name ) || empty( $group ) ) {
            if ( ! empty( $default ) ) {
                return $default;
            }

            return false;
        } // vail if either $name or option $group is empty
        $options_array = (array) get_option( $group );
        if ( array_key_exists( $name, $options_array ) ) {
            return $options_array[$name];
        } else {
            if ( ! empty( $default ) ) {
                return $default;
            }

            return false;
        }
    }
}

if ( ! function_exists( 'atbdp_sanitize_array' ) ) {
    /**
     * It sanitize a multi-dimensional array
     * @param array &$array The array of the data to sanitize
     * @return mixed
     */
    function atbdp_sanitize_array( &$array ) {

        foreach ( $array as &$value ) {

            if ( ! is_array( $value ) ) {

                // sanitize if value is not an array
                $value = sanitize_text_field( $value );
            } else {

                // go inside this function again
                atbdp_sanitize_array( $value );
            }
        }

        return $array;
    }
}

if ( ! function_exists( 'is_directoria_active' ) ) {
    /**
     * It checks if the Directorist theme is installed currently.
     * @return bool It returns true if the directorist theme is active currently. False otherwise.
     */
    function is_directoria_active() {
        return wp_get_theme()->get_stylesheet() === 'directoria';
    }
}

if ( ! function_exists( 'is_plan_allowed_business_hours' ) ) {
    /**
     * It checks is user activated business hours and is the purchased plan included that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_business_hours( $plan_id ) {
        //return true;
        //check is BH activated
        if ( class_exists( 'BD_Business_Hour' ) ) {
            // lets check the plan allowances
            $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
            $business_hrs     = selected_plan_meta( $selected_plan_id, 'business_hrs' );
            return ( $business_hrs ) ? true : false;
        } else {
            return false;
        }
    }
}

if ( ! function_exists( 'public_plans' ) ) {
    function public_plans() {
        $args = [
            'post_type'      => 'atbdp_pricing_plans',
            'posts_per_page' => -1,
            'status'         => 'publish',
        ];
        $args['meta_query'] = [
            'relation' => 'OR',
            [
                'key'     => '_hide_from_plans',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => '_hide_from_plans',
                'value'   => 1,
                'compare' => '!=',
            ],
        ];
        $atbdp_query = new WP_Query( $args );
        if ( $atbdp_query->have_posts() ) {
            return $atbdp_query->posts;
        } else {
            return [];
        }
    }
}

if ( ! function_exists( 'is_plan_allowed_listing_video' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_listing_video( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $l_video          = selected_plan_meta( $selected_plan_id, 'l_video' );
        return ( $l_video ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_owner_contact_widget' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_owner_contact_widget( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $cf_owner         = selected_plan_meta( $selected_plan_id, 'cf_owner' );
        return ( $cf_owner ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_listing_email' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_listing_email( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_email         = selected_plan_meta( $selected_plan_id, 'fm_email' );
        return ( $fm_email ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_listing_phone' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_listing_phone( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_phone         = selected_plan_meta( $selected_plan_id, 'fm_phone' );
        return ( $fm_phone ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_listing_webLink' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_listing_webLink( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_web_link      = selected_plan_meta( $selected_plan_id, 'fm_web_link' );
        return ( $fm_web_link ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_listing_social_networks' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_listing_social_networks( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id  = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_social_network = selected_plan_meta( $selected_plan_id, 'fm_social_network' );
        return ( $fm_social_network ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_listing_review' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_listing_review( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_cs_review     = selected_plan_meta( $selected_plan_id, 'fm_cs_review' );
        return ( $fm_cs_review ) ? true : false;
    }
}
if ( ! function_exists( 'is_plan_allowed_listing_faqs' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_listing_faqs( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_listing_faq   = selected_plan_meta( $selected_plan_id, 'fm_listing_faq' );
        return ( $fm_listing_faq ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_category' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_category( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $exclude_cat      = selected_plan_meta( $selected_plan_id, 'exclude_cat' );
        return ( $exclude_cat ) ? $exclude_cat : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_custom_fields' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_custom_fields( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_custom_field  = selected_plan_meta( $selected_plan_id, 'fm_custom_field' );
        return ( $fm_custom_field ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_price' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_price( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_allow_price   = selected_plan_meta( $selected_plan_id, 'fm_allow_price' );
        return ( $fm_allow_price ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_price_limit' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_price_limit( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $price_range      = selected_plan_meta( $selected_plan_id, 'price_range' );
        return ( $price_range ) ? $price_range : false;
    }
}
if ( ! function_exists( 'is_plan_price_unlimited' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_price_unlimited( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $price_range_unl  = selected_plan_meta( $selected_plan_id, 'price_range_unl' );
        return ( $price_range_unl ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_tag' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_tag( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_allow_tag     = selected_plan_meta( $selected_plan_id, 'fm_allow_tag' );
        return ( $fm_allow_tag ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_tag_limit' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_tag_limit( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $tag_range        = selected_plan_meta( $selected_plan_id, 'fm_tag_limit' );
        return ( $tag_range ) ? $tag_range : false;
    }
}
if ( ! function_exists( 'is_plan_tag_unlimited' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_tag_unlimited( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $tag_range_unl    = selected_plan_meta( $selected_plan_id, 'fm_tag_limit_unl' );
        return ( $tag_range_unl ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_slider' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_slider( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_allow_slider  = selected_plan_meta( $selected_plan_id, 'fm_allow_slider' );
        return ( $fm_allow_slider ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_slider_limit' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_slider_limit( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $slider_range     = selected_plan_meta( $selected_plan_id, 'num_image' );
        return ( $slider_range ) ? $slider_range : false;
    }
}
if ( ! function_exists( 'is_plan_slider_unlimited' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_slider_unlimited( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $slider_range_unl = selected_plan_meta( $selected_plan_id, 'num_image_unl' );
        return ( $slider_range_unl ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_average_price_range' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_average_price_range( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id     = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_allow_price_range = selected_plan_meta( $selected_plan_id, 'fm_allow_price_range' );
        return ( $fm_allow_price_range ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_listing_gallery' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_listing_gallery( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id     = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $atfm_listing_gallery = selected_plan_meta( $selected_plan_id, 'atfm_listing_gallery' );
        return ( $atfm_listing_gallery ) ? true : false;
    }
}

if ( ! function_exists( 'is_plan_allowed_featured_listing' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_featured_listing() {
        $selected_plan_id   = selected_plan_id();
        $num_featured       = selected_plan_meta( $selected_plan_id, 'num_featured' );
        $unlimited_featured = selected_plan_meta( $selected_plan_id, 'num_featured_unl' );
        return ( $num_featured || $unlimited_featured ) ? true : false;
    }
}

if ( ! function_exists( 'selected_plan_id' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function selected_plan_id() {
        if ( ATBDP_VERSION < '6.3.0' ) { // add compatibility with directorist version bellow 6.3.0
            if ( ! empty( $_GET['plan'] ) ) {
                $plan_id = $_GET['plan'];
                return $plan_id;
            } else {
                return false;
            }
        } else {
            if ( ! empty( $_POST['plan'] ) ) {
                $plan_id = $_POST['plan'];
                return $plan_id;
            } else {
                return isset( $_GET['plan'] ) ? $_GET['plan'] : '';
            }
        }
    }
}

if ( ! function_exists( 'selected_plan_meta' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function selected_plan_meta( $plan_id, $meta_key ) {

        $plan_meta = get_post_meta( $plan_id, $meta_key, true );
        return $plan_meta;
    }
}

if ( ! function_exists( 'package_or_PPL' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function package_or_PPL( $plan_id ) {
        $selected_plan_id = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $plan_type        = selected_plan_meta( $selected_plan_id, 'plan_type' );
        return ( $plan_type ) ? $plan_type : '';
    }
}

if ( ! function_exists( 'PPL_with_featured' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function PPL_with_featured( $plan = null ) {
        $selected_plan_id    = ! empty( $plan ) ? $plan : selected_plan_id();
        $is_featured_listing = selected_plan_meta( $selected_plan_id, 'is_featured_listing' );
        return ( $is_featured_listing ) ? true : false;
    }
}

if ( ! function_exists( 'subscribed_package_or_PPL_plans' ) ) {
    /**
     * Get the order of current author for ATBDP order table.
     * @return true It returns all the subscribed plan id.
     * @param $plan_id
     * @param $order_status
     * @param $user_id
     */
    function subscribed_package_or_PPL_plans( $user_id, $order_status, $plan_id, $listing_id = null ) {

        if ( ! $user_id ) {
            return [];
        }
        
        $args = [
            'post_type'      => 'atbdp_orders',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'author'         => $user_id,
        ];
        $meta_queries   = [];
        $meta_queries[] = [
            'relation' => 'AND',
            [
                'key'   => '_payment_status',
                'value' => $order_status,
            ],
            [
                'key'   => '_fm_plan_ordered',
                'value' => $plan_id,
            ],
        ];

        $args['meta_query'] = array_merge( ['relation' => 'AND'], $meta_queries );
        $orders             = new WP_Query( $args );

        $Plan_meta = [];
        
        foreach ( $orders->posts as $key => $val ) {

            if( ! empty( $listing_id ) ){

                $order_id = $val->ID;
                $order_listings = get_post_meta( $order_id, '_listing_id', true );

                if( is_array( $order_listings ) ){

                    if( in_array( $listing_id, $order_listings ) ){

                        $Plan_meta[] = ! empty( $val ) ? $val : [];
                        continue;
                    } 

                }else{

                    if( $listing_id == $order_listings ){

                        $Plan_meta[] = ! empty( $val ) ? $val : [];
                        continue;
                    }
                }
            }else{
                $Plan_meta[] = ! empty( $val ) ? $val : [];
            } 
        }

        return ! empty( $Plan_meta ) ? $Plan_meta : false;
    }
}

if ( ! function_exists( 'package_or_PPL_with_listing' ) ) {
    /**
     * Get the order of current author for ATBDP order table.
     * @return true It returns all the subscribed plan id.
     * @param $listing_id
     * @param $order_status
     * @param $user_id
     */
    function package_or_PPL_with_listing( $user_id, $order_status, $listing_id ) {
        $orders = new WP_Query( [
            'post_type'      => 'atbdp_orders',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'author'         => $user_id,
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'   => '_payment_status',
                    'value' => $order_status,
                ],
                [
                    'key'   => '_listing_id',
                    'value' => $listing_id,
                ],
            ],
        ] );
        $plan_meta = [];
        foreach ( $orders->posts as $key => $val ) {
            $plan_meta[] = ! empty( $val ) ? $val : [];
        }
        return ( $plan_meta ) ? $plan_meta : false;
    }
}

if ( ! function_exists( 'listings_data_with_plan' ) ) {
    /**
     * Get the order of current author for ATBDP order table.
     * @return true It returns all the listings under a subscribed plan id.
     * @param $plan_id
     * @param $user_id
     * @param $featured
     */
    function listings_data_with_plan( $user_id, $featured, $plan_id, $order_id = null ) {
        $args = [
            'post_type'      => 'at_biz_dir',
            'posts_per_page' => -1,
            'post_status'    => ['publish', 'pending'],
            'fields'         => 'ids',
            'author'         => $user_id,
        ];

        $meta_queries = [];

        $meta_queries['general'] = [
            'relation' => 'AND',
            [
                'key'     => '_fm_plans',
                'value'   => $plan_id,
                'compare' => '=',
            ],
            [
                'key'     => '_plan_order_id',
                'value'   => $order_id,
                'compare' => '=',
            ],
        ];

        if ( '1' === $featured ) {
            $meta_queries['featured'] = [
                [
                    'key'     => '_featured',
                    'value'   => '1',
                    'compare' => '=',
                ],
            ];
        }

        if ( '0' === $featured ) {
            $meta_queries['non_featured'] = [
                'relation' => 'OR',
                [
                    'key'     => '_featured',
                    'value'   => '',
                    'compare' => '=',
                ],
                [
                    'key'     => '_featured',
                    'value'   => '0',
                    'compare' => '=',
                ],
            ];
        }

        $args['meta_query'] = array_merge( ['relation' => 'AND'], $meta_queries );

        $orders = new WP_Query( $args );

        return ( $orders->post_count ) ? $orders->post_count : false;
    }
}
/**
 * @since 1.4.0
 * @return boolean      return true means need not to collect money
 */
if ( ! function_exists( 'directorist_validate_plan' ) ) {
    function directorist_validate_plan( $plan_id, $post_id, $order_id, $listing_type, $user_id = '' ) {
        $error           = [];
        $data            = [];
        $need_payment    = false;
        $activated_order = $order_id;
        $plan_type       = package_or_PPL( $plan_id );
        $user_id         = $user_id ? $user_id : get_current_user_id();

        $plan_purchased = subscribed_package_or_PPL_plans( $user_id, 'completed', $plan_id );
        $order_id       = ! empty( $activated_order ) ? $activated_order : $plan_purchased[0]->ID;

        $remaining = plans_remaining( $plan_id, $order_id );
        $featured  = $remaining['featured'];
        $regular   = $remaining['regular'];

        $package_length = directorist_plan_lifetime( $plan_id );
        $package_length = $package_length ? $package_length : '1';

        // Current time
        $start_date = get_the_date( '', $order_id );
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

        if ( $remaining_days < 0 ) {
            $msg = __( 'You have crossed the limit! Please try again', 'directorist-pricing-plans' );
            if ( ( 'pay_per_listng' != $plan_type ) ) {
                update_post_meta( $order_id, '_order_status', 'exit' );
                $need_payment = true;
            }
        } else {
            if ( ( 'regular' === $listing_type ) && ( 'package' === $plan_type ) ) {
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
            return $data;
        }

        $skip_payment = false;
        $listing_id   = $post_id;

        $old_plan_id    = get_post_meta( $listing_id, '_fm_plans', true );
        $plan_id        = $plan_id ? $plan_id : $old_plan_id;
        $directory_type = get_post_meta( $listing_id, '_directory_type', true );
        $edit_l_status  = get_term_meta( $directory_type, 'edit_listing_status', true );
        $order_plan     = get_post_meta( $order_id, '_listing_id', true );
        $order_plan     = ! empty( $order_plan ) ? [ $order_plan ] : [];
        $checkout_url   = add_query_arg( 'plan_id', $plan_id, ATBDP_Permalink::get_checkout_page_link( $listing_id ) );

        if( ! empty( $listing_id ) ){
            array_push( $order_plan, $listing_id );
        }

        if ( directorist_direct_purchase() && ( 'package' !== $plan_type ) && empty( $order_plan ) && $plan_purchased ) {
            $skip_payment = true;
            update_post_meta( $order_id, '_listing_id', $order_plan );
        }

        if (  ( 'package' === $plan_type ) && $plan_purchased ) {
            update_post_meta( $order_id, '_listing_id', $order_plan );
        }
        update_post_meta( $listing_id, '_plan_order_id', $order_id );
        update_post_meta( $listing_id, '_listing_order_id', $order_id );
        update_post_meta( $listing_id, '_fm_plans', $plan_id );

        if ( 'pay_per_listng' === package_or_PPL( $plan_id ) ) {

            if ( PPL_with_featured( $plan_id ) ) {
                update_post_meta( $post_id, '_featured', '1' );
            }
            $fresh_active_order = directorist_active_orders_without_listing( $plan_id );

            if ( ! $fresh_active_order ) {
                $data['redirect_url'] = $checkout_url;
                $data['need_payment'] = true;
                return $data;
            }
        }

        if ( ! $order_id || $need_payment ) {
            $data['redirect_url'] = $checkout_url;
            $data['need_payment'] = true;

            wp_update_post( [
                'ID'          => $listing_id,
                'post_status' => 'pending',
            ] );
        }

        wp_update_post( [
            'ID'          => $listing_id,
            'post_status' => $edit_l_status,
        ] );
        update_post_meta( $listing_id, '_listing_status', 'post_status' );

        $is_never_expaired = get_post_meta( $plan_id, 'fm_length_unl', true );
        if ( $is_never_expaired ) {
            update_post_meta( $listing_id, '_never_expire', '1' );
        } else {
            update_post_meta( $listing_id, '_never_expire', 0 );
            update_post_meta( $listing_id, '_expiry_date', $expired_date );
        }

        if ( 'featured' == $listing_type ) {
            update_post_meta( $listing_id, '_featured', '1' );
        } else {
            update_post_meta( $listing_id, '_featured', '' );
        }

        update_post_meta( $listing_id, '_need_to_refresh', '1' );
        // for dev
        do_action( 'atbdp_plan_assigned', $listing_id );

        // update the claim status
        $fm_claim = get_post_meta( $plan_id, '_fm_claim', true );
        if ( ! empty( $fm_claim ) ) {
            update_post_meta( $listing_id, '_claimed_by_admin', 1 );
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

        return $data;

    }
}

/**
 * @since 1.1.9
 * //get the listings with the order meta '_need_to_refresh' and if found any order referring the same listing. Yep, refresh it
 * @package Directorist
 * @param $order_id     WooCommerce order id
 * @param $plan_id      Order carried the plan
 *
 */
if ( ! function_exists( 'atpp_need_listing_to_refresh' ) ) {
    function atpp_need_listing_to_refresh( $user_id = null, $post_id = null, $order_id = null ) {

        $plan_id = get_post_meta( $post_id, '_fm_plans', true );

        update_post_meta( $order_id, '_fm_plan_ordered', $plan_id );

        update_post_meta( $post_id, '_plan_order_id', $order_id );
        update_post_meta( $order_id, '_listing_id', $post_id );
        update_post_meta( $post_id, '_listing_status', 'post_status' );
        $package_length = directorist_plan_lifetime( $plan_id );
        $package_length = $package_length ? $package_length : '1';
        // Current time
        $current_d = current_time( 'mysql' );
        // Calculate new date
        $date = new DateTime( $current_d );
        $date->add( new DateInterval( "P{$package_length}D" ) ); // set the interval in days
        $expired_date      = $date->format( 'Y-m-d H:i:s' );
        $is_never_expaired = get_post_meta( $plan_id, 'fm_length_unl', true );
        if ( $is_never_expaired ) {
            update_post_meta( $post_id, '_never_expire', '1' );
        } else {
            update_post_meta( $post_id, '_never_expire', '' );
            update_post_meta( $post_id, '_expiry_date', $expired_date );
        }

        $directory_type_terms = get_the_terms( $post_id, ATBDP_DIRECTORY_TYPE );
        $directory_type       = ( is_array( $directory_type_terms ) ) ? $directory_type_terms[0]->term_id : '';

        $new_l_status = 'pending';

        if ( $directory_type ) {
            $new_l_status = get_term_meta( $directory_type, 'new_listing_status', true );
        }

        // wp_update_post(array(
        //     'ID'    =>  $post_id,
        //     'post_status'   =>  apply_filters( 'atpp_reviewed_listing_status_controller_argument', $new_l_status ),
        // ));

        $token_refresh = get_post_meta( $post_id, '_refresh_renewal_token', true );
        if ( $token_refresh ) {
            update_post_meta( $post_id, '_refresh_renewal_token', 0 );
            update_post_meta( $post_id, '_renewal_token', 0 );
        }

        $featured_listing = get_post_meta( $post_id, '_need_featured', true );
        if ( ! empty( $featured_listing ) ) {
            update_post_meta( $post_id, '_need_featured', '' );
            update_post_meta( $post_id, '_featured', 1 );
        }
        // for dev
        do_action( 'atbdp_plan_assigned', $post_id );
    }
}

if ( ! function_exists( 'atpp_get_used_free_plan' ) ) {
    function atpp_get_used_free_plan( $plan_id, $user_id ) {
        if ( ! $user_id ) {
            return true;
        }

        $fm_price = atpp_total_price( $plan_id );
        if ( $fm_price > 0 ) {
            return true;
        } else {
            $match = subscribed_package_or_PPL_plans( $user_id, 'completed', $plan_id );
            if ( $match ) {
                $listing_id  = get_post_meta( $match[0]->ID, '_listing_id', true );
                $list_status = get_post_status( $listing_id );
                if (  ( 'trash' === $list_status ) || ! $list_status ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
    }
}

/* adding new user meta for new subscription */
if ( ! function_exists( 'atpp_add_new_susbcription_meta' ) ) {
    function atpp_add_new_susbcription_meta( $new_susbcription ) {
        if ( ! empty( $new_susbcription ) ) {
            $uid            = get_current_user_id();
            $existing_subsc = get_user_meta( $uid, 'directorist_user_sbscr', true );
            if ( ! empty( $existing_subsc ) ) {
                array_push( $existing_subsc, $new_susbcription );
                update_user_meta( $uid, 'directorist_user_sbscr', $existing_subsc );
            } else {
                $new_subsc[] = $new_susbcription;
                update_user_meta( $uid, 'directorist_user_sbscr', $new_subsc );
            }
        }
    }
}

function atpp_create_required_pages() {
    $options     = get_option( 'atbdp_option' );
    $page_exists = get_option( 'atbdp_plan_page_create' );
    // $op_name is the page option name in the database.
    // if we do not have the page id assigned in the settings with the given page option name, then create an page
    // and update the option.
    $id = [];
    if ( ! $page_exists ) {
        $id = wp_insert_post(
            [
                'post_title'     => 'Select Your Plan',
                'post_content'   => '[directorist_pricing_plans]',
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
            ]
        );
    }
    // if we have new options then lets update the options with new option values.
    if ( $id ) {
        update_option( 'atbdp_plan_page_create', 1 );
        $options['pricing_plans'] = (int) $id;
        update_option( 'atbdp_option', $options );
    }
    ;
}

/**
 * @since 1.7.0
 * @param init $listing_id
 * @return bool true | false
 */
if ( ! function_exists( 'has_plan' ) ) {
    function has_plan( $listing_id ) {
        $plan   = get_post_meta( $listing_id, '_fm_plans', true );
        $result = ( empty( $plan ) || ( 'null' === $plan ) ) ? false : true;
        return $result;
    }
}

/**
 * @since 1.7.4
 * @param init $plan_id
 * @return int $price
 */
if ( ! function_exists( 'atpp_total_price_with_tax' ) ) {
    function atpp_total_price_with_tax( $plan_id ) {
        $fm_price  = get_post_meta( $plan_id, 'fm_price', true );
        $free_plan = get_post_meta( $plan_id, 'free_plan', true );
        if ( $free_plan ) {
            $grand_total = 0;
        } else {
            $tax         = atpp_total_tax( $plan_id );
            $grand_total = (float) $fm_price + (float) $tax;
        }
        return $grand_total;
    }
}

/**
 * @since 1.7.4
 * @param init $plan_id
 * @return int $price
 */
if ( ! function_exists( 'atpp_total_price' ) ) {
    function atpp_total_price( $plan_id ) {
        $fm_price  = get_post_meta( $plan_id, 'fm_price', true );
        $free_plan = get_post_meta( $plan_id, 'free_plan', true );
        if ( $free_plan ) {
            $grand_total = 0;
        } else {
            $grand_total = (float) $fm_price;
        }
        return $grand_total;
    }
}

/**
 * @since 1.7.4
 * @param init $plan_id
 * @return int $tax
 */
if ( ! function_exists( 'atpp_total_tax' ) ) {
    function atpp_total_tax( $plan_id ) {
        $fm_price = (float) get_post_meta( $plan_id, 'fm_price', true );
        if ( ! $fm_price ) {
            return '';
        }

        $fm_tax        = (float) get_post_meta( $plan_id, 'fm_tax', true );
        $plan_tax_type = get_post_meta( $plan_id, 'plan_tax_type', true );
        $plan_tax      = get_post_meta( $plan_id, 'plan_tax', true );
        if ( '0' != $plan_tax ) {
            if ( 'percent' === $plan_tax_type ) {
                $tax = $fm_price ? ( $fm_tax * $fm_price ) / 100 : 0;
            } else {
                $tax = $fm_price ? $fm_tax : 0;
            }
        } else {
            $tax = 0;
        }

        return apply_filters( 'directorist_plan_tax', $tax, $plan_id );
    }
}
/**
 * @since 1.8.7
 * @param init $plan_id
 * @return int $tax
 */
if ( ! function_exists( 'directorist_plan_tax_rate' ) ) {
    function directorist_plan_tax_rate( $plan_id ) {
        $fm_price = (float) get_post_meta( $plan_id, 'fm_price', true );
        if ( ! $fm_price ) {
            return 0;
        }

        $fm_tax        = (float) get_post_meta( $plan_id, 'fm_tax', true );
        $plan_tax_type = get_post_meta( $plan_id, 'plan_tax_type', true );
        $plan_tax      = get_post_meta( $plan_id, 'plan_tax', true );
        if ( '0' != $plan_tax ) {
            if ( 'percent' === $plan_tax_type ) {
                $tax = $fm_tax;
            } else {
                $tax = $fm_price ? ( $fm_tax / $fm_price ) * 100 : 0;
            }
        } else {
            $tax = 0;
        }

        return apply_filters( 'directorist_plan_tax_rate', $tax, $plan_id );
    }
}

/**
 * @since 1.7.5
 */

if ( ! function_exists( 'plans_remaining' ) ) {
    function plans_remaining( $plan_id = '', $order_id = '' ) {

        $data = [];
        if ( ! $plan_id ) {
            return '';}
        $user_id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : get_current_user_id();

        $num_regular      = get_post_meta( $plan_id, 'num_regular', true );
        $num_featured     = get_post_meta( $plan_id, 'num_featured', true );
        $num_featured_unl = get_post_meta( $plan_id, 'num_featured_unl', true );
        $num_regular_unl  = get_post_meta( $plan_id, 'num_regular_unl', true );

        $user_featured_listing = listings_data_with_plan( $user_id, '1', $plan_id, $order_id );
        $user_regular_listing  = listings_data_with_plan( $user_id, '0', $plan_id, $order_id );

        // $data = [
        //     'regular'   => $user_regular_listing,
        //     'featured'  => $user_featured_listing,
        // ];
        // return $data;
        // var_dump([
        //     'featured' => $user_featured_listing,
        //     'regular' => $user_regular_listing,
        //     'user_id' => $user_id,
        //     'plan_id' => $plan_id,
        //     'order_id' => $order_id,
        // ]);

        $total_regular_listing  = (int)$num_regular - (int)$user_regular_listing;
        $total_featured_listing = (int)$num_featured - (int)$user_featured_listing;
        $total_regular_listing  = max( $total_regular_listing, 0 );
        $total_featured_listing = max( $total_featured_listing, 0 );

        if ( ! $num_regular_unl ) {
            $regular = $total_regular_listing;
        } else {
            $regular = 'Unlimited';
        }

        if ( ! $num_featured_unl ) {
            $featured = $total_featured_listing;
        } else {
            $featured = 'Unlimited';
        }

        $data = [
            'regular'  => $regular,
            'featured' => $featured,
        ];

        return apply_filters( 'directorist_plan_remaining', $data );
    }
}

if ( ! function_exists( 'is_plan_allowed_categories' ) ) {
    /**
     * It checks is user purchased plan included in that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function is_plan_allowed_categories( $plan_id ) {
        // lets check the plan allowances
        $selected_plan_id  = empty( selected_plan_id() ) ? $plan_id : selected_plan_id();
        $fm_allow_category = selected_plan_meta( $selected_plan_id, 'fm_allow_category' );
        return ( $fm_allow_category ) ? true : false;
    }
}

if ( ! function_exists( 'atbdp_plan_allows_booking' ) ) {
    /**
     * It checks is user activated booking and is the purchased plan included that feature.
     * @return bool It returns true if the above mentioned exists.
     */
    function atbdp_plan_allows_booking( $plan_id = '' ) {
        // Check if Booking is activated
        if ( ! class_exists( 'BD_Booking' ) ) {
            return false;}

        // lets check the plan allowances
        $selected_plan_id = ! empty( $plan_id ) ? $plan_id : selected_plan_id();
        $booking          = selected_plan_meta( $selected_plan_id, '_fm_booking' );

        return $booking;
    }
}

// atpp_get_template
function atpp_get_template( $template_file, $args = [] ) {
    if ( is_array( $args ) ) {
        extract( $args );
    }

    $theme_template  = '/directorist-pricing-plans/' . $template_file . '.php';
    $plugin_template = ATPP_TEMPLATES_DIR . $template_file . '.php';

    if ( file_exists( get_stylesheet_directory() . $theme_template ) ) {
        $file = get_stylesheet_directory() . $theme_template;
    } elseif ( file_exists( get_template_directory() . $theme_template ) ) {
        $file = get_template_directory() . $theme_template;
    } else {
        $file = $plugin_template;
    }

    if ( file_exists( $file ) ) {
        include $file;
    }
}

function atpp_add_listing_page_link_with_plan( $plan_id, $is_active ) {

    $active_orders = directorist_active_orders( $plan_id );
    if ( count( $active_orders ) > 1 ) {

        foreach ( $active_orders as $order ) {
            $valid_order = directorist_valid_order( $order, $plan_id );
            if ( ! $valid_order ) {

                $active_orders = array_diff( $active_orders, [$order] );
            }
        }
    }
    $order_id = ! empty( $active_orders[0] ) ? $active_orders[0] : '';

    $link          = home_url();
    $id            = get_directorist_option( 'add_listing_page' );
    $checkout_page = get_directorist_option( 'checkout_page' );
    if ( $id ) {
        $args = [
            'directory_type' => isset( $_GET['directory_type'] ) ? $_GET['directory_type'] : default_directory_type(),
            'plan'           => $plan_id,
        ];

        if ( ! empty( $order_id ) && ( 'package' === package_or_PPL( $plan_id ) ) ) {
            $args['order'] = $order_id;
        }

        $link = get_permalink( ! directorist_direct_purchase() ? $id : ( ! $is_active ? $checkout_page : $id ) );
        $link = add_query_arg( $args, $link );
    }

    return apply_filters( 'atbdp_add_listing_page_url', $link );
}

function directorist_direct_purchase() {
    $direct_purchase = get_directorist_option( 'plan_direct_purchase', false );
    return apply_filters( 'directorist_direct_purchase', $direct_purchase );
}

function directorist_plan_lifetime( $plan_id ) {

    $fm_length         = (int) get_post_meta( $plan_id, 'fm_length', true );
    $recurrence_period = get_post_meta( $plan_id, '_recurrence_period_term', true );

    switch ( $recurrence_period ) {
    case ( 'month' ):
        $lifetime = $fm_length * 30;
        break;

    case ( 'year' ):
        $lifetime = $fm_length * 365;
        break;

    case ( 'week' ):
        $lifetime = $fm_length * 7;
        break;

    case ( 'day' ):
        $lifetime = $fm_length;
        break;

    default:
        $lifetime = $fm_length;
    }

    return $lifetime;
}

function directorist_plans_dashboard_data( $data = 'package' ) {
    $user_id = get_current_user_id();
    $orders  = new WP_Query( [
        'post_type'      => 'atbdp_orders',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'author'         => $user_id,
        'fields'         => 'ids',
        'meta_query'     => [
            'relation' => 'AND',

            [
                'key'     => '_fm_plan_ordered',
                'compare' => 'EXISTS',
            ],

            [
                'relation' => 'OR',
                [
                    'key'     => '_payment_status',
                    'value'   => 'completed',
                    'compare' => '=',
                ],
                [
                    'key'     => '_payment_status',
                    'value'   => 'created',
                    'compare' => '=',
                ],
                [
                    'key'     => '_payment_status',
                    'value'   => 'pending',
                    'compare' => '=',
                ],
            ],
        ],

    ] );

    $subscribed_package_ids   = [];
    $subscribed_package_dates = [];
    $all_order_ids            = [];
    $order_ids                = wp_parse_id_list( $orders->posts );

    if ( ! empty( $order_ids ) && is_callable( '_prime_post_caches' ) ) {
        _prime_post_caches( $order_ids );
    }
    if ( 'order' === $data ) {
        return $order_ids;
    }

    $original_post = $GLOBALS['post'];

    foreach ( $order_ids as $order_id ) {
        $GLOBALS['post'] = get_post( $order_id );
        setup_postdata( $GLOBALS['post'] );

        $all_plan_ids    = get_post_meta( $order_id, '_fm_plan_ordered', true ); //data form order table
        $all_order_ids[] = $all_plan_ids;
        $plan_type       = get_post_meta( $all_plan_ids, 'plan_type', true ); //data form Pricing Plans table

        if ( 'package' === $plan_type ) {
            $subscribed_package_ids[]   = $all_plan_ids;
            $subscribed_package_dates[] = get_the_date();
        }
    }

    $GLOBALS['post'] = $original_post;
    wp_reset_postdata();
    return $subscribed_package_ids;
}

if ( ! function_exists( 'directorist_recurring_plans' ) ) {
    function directorist_recurring_plans() {
        $args = [
            'post_type'      => 'atbdp_pricing_plans',
            'posts_per_page' => -1,
            'status'         => 'publish',
            'meta_key'       => '_atpp_recurring',
            'meta_value'     => 'yes',
            'fields'         => 'ids',
        ];

        $atbdp_query = new WP_Query( $args );
        if ( $atbdp_query->have_posts() ) {
            return $atbdp_query->posts;
        } else {
            return [];
        }
    }
}

if ( ! function_exists( 'directorist_active_orders' ) ) {
    function directorist_active_orders( $plan_id = '', $user_id = '' ) {

        // update_post_meta( '211', '_order_status', '');

        $user_id = $user_id ? $user_id : get_current_user_id();
        if ( empty( $user_id ) ) {
            return [];
        }

        $args = [
            'post_type'      => 'atbdp_orders',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'author'         => $user_id,
            'fields'         => 'ids',
        ];

        $meta = [];

        $meta['plan_status'] = [
            'relation' => 'AND',
            [
                'key'     => '_fm_plan_ordered',
                'value'   => $plan_id,
                'compare' => '=',
            ],
            [
                'key'     => '_payment_status',
                'value'   => 'completed',
                'compare' => '=',
            ],
        ];

        $meta['order_status'] = [
            'relation' => 'OR',
            [
                'key'     => '_order_status',
                'value'   => 'exit',
                'compare' => '!=',
            ],
            [
                'key'     => '_order_status',
                'compare' => 'NOT EXISTS',
            ],
        ];

        $metas = count( $meta );
        if ( $metas ) {
            $args['meta_query'] = ( $metas > 1 ) ? array_merge( ['relation' => 'AND'], $meta ) : $meta;
        }

        $orders = new WP_Query( $args );

        if ( $orders->have_posts() ) {
            return $orders->posts;
        } else {
            return [];
        }
    }
}

if ( ! function_exists( 'directorist_active_orders_without_listing' ) ) {
    function directorist_active_orders_without_listing( $plan_id = '' ) {
        if ( ! get_current_user_id() ) {
            return false;
        }
        $orders = new WP_Query( [
            'post_type'      => 'atbdp_orders',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'author'         => get_current_user_id(),
            'fields'         => 'ids',
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => '_fm_plan_ordered',
                    'value'   => $plan_id,
                    'compare' => '=',
                ],
                [
                    'key'     => '_payment_status',
                    'value'   => 'completed',
                    'compare' => '=',
                ],
                [
                    'key'     => '_listing_id',
                    'value'   => '',
                    'compare' => '=',
                ],
            ],

        ] );
        if ( $orders->have_posts() ) {
            return true;
        } else {
            return false;
        }
    }
}

if ( ! function_exists( 'directorist_valid_order' ) ) {

    function directorist_valid_order( $order_id, $plan_id ) {
        $balance  = plans_remaining( $plan_id, $order_id );
        $featured = ! empty( $balance['featured'] ) ? $balance['featured'] : '';
        $regular  = ! empty( $balance['regular'] ) ? $balance['regular'] : '';

        if ( empty( $featured ) && empty( $regular ) ) {
            return false;
        }

        return true;

    }
}

if ( ! function_exists( 'directorist_validate_date' ) ) {

    function directorist_validate_date( $str ) {

        if ( ! is_string( $str ) ) {
            return false;
        }

        $stamp = strtotime( $str );

        if ( ! is_numeric( $stamp ) ) {
            return false;
        }

        if ( checkdate( date( 'm', $stamp ), date( 'd', $stamp ), date( 'Y', $stamp ) ) ) {
            return true;
        }
        return false;
    }
}