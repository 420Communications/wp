<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.7.4
 * @package Directorist
 */
if (!class_exists('Directorist_Plan_Restrictions')) :

    class Directorist_Plan_Restrictions
    {
        public function __construct()
        {

			add_filter( 'atbdp_booking_is_restricted', [$this, 'booking_restriction'], 10, 2 );

			add_filter( 'directorist_faqs_form_field_templete_on_demand', [$this, 'form_faqs_restriction'], 10, 2 );
			add_filter( 'directorist_faqs_single_field_templete_on_demand', [$this, 'single_faqs_restriction'], 10, 2 );

			add_filter( 'directorist_hours_form_field_templete_on_demand', [$this, 'form_hours_restriction'], 10, 2 );
			add_filter( 'directorist_hours_single_field_templete_on_demand', [$this, 'single_hours_restriction'], 10, 2 );
			add_filter( 'atbdp_allow_business_hour', [$this, 'single_widget_hours_restriction'] );


        }

        public function form_hours_restriction( $default, $field_data ) {

            $form       = $field_data['form'];
            $listing_id = $form->add_listing_id;
            $listing_plan = get_post_meta( $listing_id, '_fm_plans', true );
            $plan_id    = ! empty( $_GET['plan'] ) ? $_GET['plan'] : $listing_plan;
			$hours      = get_post_meta( $plan_id, '_bdbh', true );

			if( ! $hours || ( '0' == $hours ) ) {
				return false;
			}
			return $default;
		}

        public function single_widget_hours_restriction( $default ) {

            $plan_id    = get_post_meta( get_the_ID(), '_fm_plans', true );
			$hours      = get_post_meta( $plan_id, '_bdbh', true );

			if( ! $hours || ( '0' == $hours ) ) {
				return false;
			}
			return $default;
		}

        public function single_hours_restriction( $default, $field_data ) {

            $plan_id    = get_post_meta( get_the_ID(), '_fm_plans', true );
			$hours      = get_post_meta( $plan_id, '_bdbh', true );

			if( ! $hours || ( '0' == $hours ) ) {
				return false;
			}
			return $default;
		}

        public function single_faqs_restriction( $default, $field_data ) {

            $plan_id    = get_post_meta( get_the_ID(), '_fm_plans', true );
			$faqs       = get_post_meta( $plan_id, '_faqs', true );

			if( ! $faqs || ( '0' == $faqs ) ) {
				return false;
			}
			return $default;
		}

        public function form_faqs_restriction( $default, $field_data ) {

            $form       = $field_data['form'];
            $listing_id = $form->add_listing_id;
            $listing_plan = get_post_meta( $listing_id, '_fm_plans', true );
            $plan_id    = ! empty( $_GET['plan'] ) ? $_GET['plan'] : $listing_plan;
			$faqs       = get_post_meta( $plan_id, '_faqs', true );

			if( ! $faqs || ( '0' == $faqs ) ) {
				return false;
			}
			return $default;
		}

        public function booking_restriction( $default, $listing_id ) {
			$plan_id = get_post_meta( $listing_id, '_fm_plans', true );
			if( $plan_id ) {
				return ! atbdp_plan_allows_booking( $plan_id );
			}
			return $default;
		}

    }

endif;