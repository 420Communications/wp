<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.7.4
 * @package Directorist
 */
if (!class_exists('ATPP_Cron')) :

    class ATPP_Cron
    {
        public function __construct()
        {
            add_action('atbdp_schedule_task', array($this, 'atbdp_schedule_task'));
            
        }

        public function atbdp_schedule_task() {
            $args = array(
                'post_type'      => ATBDP_POST_TYPE,
                'posts_per_page' => -1,
                'post_status'    => 'public',
                'fields'         => 'ids',
                'meta_key'       => '_fm_plans',
                'compare'        => 'EXISTS',
            );

            $listings  = new WP_Query($args);
            // var_dump( $listings->post_count );
            // die;
        }


        /**
         * Move featured listing to general
         * @since 6.6.6
         */

        private function featured_listing_followup() {
            $monitization = get_directorist_option('enable_monetization');
            $featured_enable = get_directorist_option('enable_featured_listing');
            if( $monitization && $featured_enable ) {
                $featured_days = get_directorist_option('featured_listing_time', 30);
                // Define the query
                $args = array(
                    'post_type'      => ATBDP_POST_TYPE,
                    'posts_per_page' => -1,
                    'post_status'    => 'public',
                    'fields'         => 'ids',
                    'meta_key'       => '_fm_plans',
                    'compare'        => 'EXISTS',
                );

                $listings  = new WP_Query($args);

                // Start the Loop
                if ($listings->found_posts) {
                    foreach ($listings->posts as $listing) {
                        //$order = $this->get_order_by_listing( $listing->ID );
                        $order = [];
                        if( $order ) {
                            $days = round( abs( strtotime( current_time( 'mysql' ) ) - strtotime( $order[0]->post_date ) ) /86400 );
                            if ( $days > $featured_days ) {
                                do_action('atbdp_listing_featured_to_general', $listing->ID);
                                update_post_meta($listing->ID, '_featured', '');
                            }
                        }
                       
                    }
                }
            }
        }


    }
endif;