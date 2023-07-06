<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.7.4
 * @package Directorist
 */
if (!class_exists('Directorist_Plan_Email')) :

    class Directorist_Plan_Email
    {
        public function __construct()
        {
            // add_filter('directorist_notify_admin_listing_submitted', array( $this, 'update_email_noticess' ), 10, 2 );

            // add_filter('directorist_notify_owner_listing_submitted', array( $this, 'update_email_noticess' ), 10, 2 );

            // add_filter('directorist_notify_admin_listing_published', array( $this, 'update_email_noticess' ), 10, 2 );

            // add_filter('directorist_notify_owner_listing_published', array( $this, 'update_email_noticess' ), 10, 2 );

            add_filter('directorist_notify_owner_listing_to_expire', array( $this, 'update_email_noticess' ), 10, 2 );

            add_filter('directorist_notify_owner_listing_expired', array( $this, 'update_email_noticess' ), 10, 2 );

            add_filter('directorist_notify_owner_to_renew', array( $this, 'update_email_noticess' ), 10, 2 );

        }

        public function update_email_noticess( $status, $listing_id ) {

            $plan_id = get_post_meta( $listing_id, '_fm_plans', true );
            $duration = directorist_plan_lifetime( $plan_id );

            if( $duration <= 1 ){
                return false;
            }

            return $status;
        }

    }

endif;