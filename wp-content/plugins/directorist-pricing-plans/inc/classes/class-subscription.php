<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.7.4
 * @package Directorist
 */
if (!class_exists('Directorist_Subscription')) :

    class Directorist_Subscription
    {
        public function __construct()
        {
            add_action( 'rest_api_init', [ $this, 'api_endpoints' ] );

            add_filter( 'directorist_update_listings_expired_status_query_arguments', [ $this, 'update_expired_listings'] );
        }

        public function update_expired_listings( $query ){

            if( !empty( directorist_recurring_plans() ) ){
                $new_meta_args = [
                    'key'     => '_fm_plans',
                    'value'   => directorist_recurring_plans(),
                    'compare' => 'NOT IN',
                ];
    
                array_push( $query['meta_query'], $new_meta_args );
            }
           
            return $query;
        }

        public function api_endpoints() {

            register_rest_route('directorist/subscription', '/updated/', array(
                'methods' => 'POST',
                'callback' => array($this, 'subscription_updated'),
                'permission_callback' => '__return_true',
            ));
       }

       private function order_updated( $args ){

            $data           = [];
            $order_id       = $args['order_id'];
            $data['status'] = $args['status'];
            $listings       = get_post_meta( $order_id, '_listing_id', true );
            $plan_id        = get_post_meta( $listings, '_fm_plans', true );
            $all_listings   = [];

            if( 'paid' === $args['status'] ){
                update_post_meta( $order_id, '_payment_status', 'completed' );
            }else{
                update_post_meta( $order_id, '_payment_status', 'failed' );
            }

            if( is_array( $listings ) ){
                foreach( $listings as $listing ){
                    array_push( $all_listings, $listing );
                }
            }else{
                array_push( $all_listings, $listings );
            }

            $listings_by_plan = $this->get_listings_by_plan( $plan_id, $order_id );
            if( !empty( $listings_by_plan ) ){
                foreach( $listings_by_plan as $listing ){
                    array_push( $all_listings, $listing );
                }
            }

            foreach( $all_listings as $listing ){
                $data['listing_id'] = $listing;
                $this->listing_updated( $data );
            }
            
        }

        private function get_listings_by_plan( $plan_id, $order_id ){
            if( ! $plan_id ){
                return [];
            }

            $args = array(
                'post_type'         => 'at_biz_dir',
                'posts_per_page'    => -1,
                'post_status'       => array('publish', 'pending', 'private'),
                'fields'            => 'ids',
            );
            $args['meta_query'] = [
                'relation' => 'OR',
                array(
                    'key' => '_fm_plans',
                    'value' => $plan_id,
                    'compare' => '='
                ),
                array(
                    'key' => '_plan_order_id',
                    'value' => $order_id,
                    'compare' => '='
                ),
            ];

            $query = new WP_Query( $args );
            $posts = $query->posts;
            
            return $posts;

        }

        private function listing_updated( $args ){
            $payment_status = $args['status'];
            $listing_id     = $args['listing_id'];

            if( 'paid' === $payment_status ){
                $this->paid( $listing_id );
            }else{
                $this->failed( $listing_id );
            }
            
        }

        private function failed( $listing_id ){
            
            update_post_meta($listing_id, '_listing_status', 'expired');

            wp_update_post(array(
                'ID'            =>  $listing_id,
                'post_status'   =>  apply_filters( 'directorist_auto_renewed_listing_status', 'private' ),
            ));
        }

        private function paid( $listing_id ){
            
            update_post_meta($listing_id, '_listing_status', 'post_status');

            update_post_meta($listing_id, '_renewal_reminder_sent', '');

            $plan_id =  get_post_meta($listing_id, '_fm_plans', true);
            $package_length = directorist_plan_lifetime( $plan_id );
            $package_length = $package_length ? $package_length : '1';

            $current_d = current_time('mysql');
            $date = new DateTime($current_d);
            $date->add(new DateInterval("P{$package_length}D")); // set the interval in days
            $expired_date = $date->format('Y-m-d H:i:s');
            $is_never_expaired = get_post_meta($plan_id, 'fm_length_unl', true);

            if ($is_never_expaired) {
                update_post_meta($listing_id, '_never_expire', '1');
            } else {
                update_post_meta($listing_id, '_expiry_date', $expired_date);
            }

            $directory_type_terms = get_the_terms( $listing_id, ATBDP_DIRECTORY_TYPE );
            $directory_type = ( is_array( $directory_type_terms ) ) ? $directory_type_terms[0]->term_id : '';
            $new_l_status = 'pending';

            if ( $directory_type ) {
                $new_l_status = get_term_meta( $directory_type, 'new_listing_status', true );
            }
            
            wp_update_post(array(
                'ID'            =>  $listing_id,
                'post_status'   =>  apply_filters( 'directorist_auto_renewed_listing_status', $new_l_status ),
            ));
        }

    
       public function subscription_updated( $request ){

        $data = $request->get_params();
		$response = '';

        $order_id = !empty( $data['data']['previous_attributes']['plan']['metadata']['order_id'] ) ? $data['data']['previous_attributes']['plan']['metadata']['order_id'] : false;
        $order_id = !empty( $data['data']['object']['metadata']['order_id'] ) ? $data['data']['object']['metadata']['order_id'] : $order_id;

		$status = !empty( $data['data']['object']['status'] ) ? $data['data']['object']['status'] : false;

		   
		   if( ! empty( $order_id ) ){
			   
			   if( 'active' == $status ){
			   	$this->order_updated( [ 'order_id' => $order_id, 'status' => 'paid' ] );
				$response = 'Success! Order update #' . $order_id;
		   		}else{
				   $this->order_updated( [ 'order_id' => $order_id, 'status' => 'failed' ] );
				   $response = 'Order update #' . $order_id;

			   }
		   }else{
			   $response = 'Order ID is missing!';
		   }
		   
        return $response;
        
    }

    }
endif;