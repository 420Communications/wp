<?php
// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');

class Directorist_Booking_Settings
{
    public function __construct()
    {
        add_filter( 'atbdp_listing_type_settings_field_list', array( $this, 'atbdp_listing_type_settings_field_list' ) );
        add_filter( 'atbdp_extension_fields', array( $this, 'atbdp_extension_fields' ) );
        add_filter( 'atbdp_extension_settings_submenu', array( $this, 'atbdp_extension_settings_submenus' ) );
        add_filter( 'atbdp_pages_settings_fields', array( $this, 'atbdp_pages_settings_fields' ) );
        add_filter( 'atbdp_email_templates_settings_sections', array( $this, 'atbdp_email_templates_settings_sections' ) );
    }

    public function atbdp_extension_fields(  $fields ) {
        $fields[] = ['enable_booking'];
        return $fields;
    }

    public function atbdp_pages_settings_fields(  $fields ) {
        $fields[] = ['booking_confirmation'];
        return $fields;
    }

    public function atbdp_listing_type_settings_field_list( $booking_fields ) {
        $booking_fields['enable_booking'] = [
            'label'             => __('Booking', 'directorist-booking'),
            'type'              => 'toggle',
            'value'             => true,
            'description'       => __('Allow users add and display booking for a listing.', 'directorist-booking'),
        ];
        $booking_fields['bdb_guest_booking'] = [
            'label'             => __('Enable Guest Booking', 'directorist-booking'),
            'type'              => 'toggle',
            'value'             => false,
        ];
        $booking_fields['bdb_commission_rate'] = [
            'type'              => 'text',
            'label'             => __('Commission Rate', 'directorist-booking'),
            'description'       => __('Set commission % for bookings', 'directorist-booking'),
            'value'             => '10',
        ];
        $booking_fields['bdb_booking_hide'] = [
            'label'             => __('Booking Hiding Option', 'directorist-booking'),
            'type'              => 'toggle',
            'value'             => true,
        ];
        $booking_fields['bdb_booking_hiding_label'] = [
            'type'              => 'text',
            'label'             => __('Booking Hiding Label', 'directorist-booking'),
            'value'             => __('Check It to Hide Booking', 'directorist-booking'),
        ];
        $booking_fields['booking_type'] = [
            'label' => __('Booking Type', 'directorist-booking'),
                    'type'  => 'select',
                    'value' => 'all',
                    'options' => [
                        [
                            'value' => 'service',
                            'label' => __('Service Booking', 'directorist-booking'),
                        ],
                        [
                            'value' => 'event',
                            'label' => __('Event Booking', 'directorist-booking'),
                        ],
                        [
                            'value' => 'rent',
                            'label' => __('Rental Booking', 'directorist-booking'),
                        ],
                        [
                            'value' => 'all',
                            'label' => __('All', 'directorist-booking'),
                        ],
                    ],
        ];
        $booking_fields['bdb_booking_type_label'] = [
            'type'              => 'text',
            'label'             => __('Booking Type Label', 'directorist-booking'),
            'value'             => __('Booking Type', 'directorist-booking'),
        ];
        $booking_fields['bdb_timing_type'] = [
            'label'             => __('Booking Timing Type', 'directorist-booking'),
            'type'              => 'toggle',
            'value'             => true,
        ];
        // service fields 
        $booking_fields['bdb_section_label'] = [
            'type'              => 'text',
            'label'             => __('Section Label', 'directorist-booking'),
            'value'             => __('Booking', 'directorist-booking'),
        ]; 
        $booking_fields['bdb_slot_label'] = [
            'type'              => 'text',
            'label'             => __('Timing Type Label', 'directorist-booking'),
            'value'             => __('Choose Timing Type', 'directorist-booking'),
        ];
        $booking_fields['bdb_instant_booking_label'] = [
            'type'              => 'text',
            'label'             => __('Enable Instant Booking Label', 'directorist-booking'),
            'value'             => __('Enable Instant Booking', 'directorist-booking'),
        ];
        $booking_fields['bdb_maximum_guests_label'] = [
            'type'              => 'text',
            'label'             => __('Maximum Guests Label', 'directorist-booking'),
            'value'             => __('Maximum Number of Guests', 'directorist-booking'),
        ];
        $booking_fields['bdb_available_ticket_label'] = [
            'type'              => 'text',
            'label'             => __('Available Ticket Label', 'directorist-booking'),
            'value'             => __('Available Tickets', 'directorist-booking'),
        ];
        $booking_fields['bdb_perbooking_ticket_label'] = [
            'type'              => 'text',
            'label'             => __('Tickets Allowed Per Booking
            Label', 'directorist-booking'),
            'value'             => __('Tickets allowed per booking', 'directorist-booking'),
        ];
        $booking_fields['bdb_request_booking_label'] = [
            'type'              => 'text',
            'label'             => __('Request Booking
            Label', 'directorist-booking'),
            'value'             => __('Request Booking', 'directorist-booking'),
        ];
        $booking_fields['bdb_book_ticket_label'] = [
            'type'              => 'text',
            'label'             => __('Book Ticket
            Label', 'directorist-booking'),
            'value'             => __('Book Ticket', 'directorist-booking'),
        ];
        $booking_fields['bdb_login_booking_label'] = [
            'type'              => 'text',
            'label'             => __('Login for Booking
            Label', 'directorist-booking'),
            'value'             => __('Login for Booking', 'directorist-booking'),
        ];
        $booking_fields['bdb_reservation_fee_label'] = [
            'type'              => 'text',
            'label'             => __('Reservation Fee
            Label', 'directorist-booking'),
            'value'             => __('Reservation Fee', 'directorist-booking'),
        ];
        $booking_fields['booking_confirmation'] = [
            'label'             => __('Booking Confirmation Page', 'directorist-booking'),
            'type'              => 'select',
            'description'       => sprintf(__('Following shortcode must be in the selected page %s', 'directorist-booking'), '<strong style="color: #ff4500;">[directorist_booking_confirmation]</strong>'),
            'value'             => atbdp_get_option('booking_confirmation', 'atbdp_general'),
            'showDefaultOption' => true,
            'options'           => $this->get_pages_vl_arrays(),
        ];
        $booking_fields['bdb_mail_waiting_owner_subject'] = [
            'type'           => 'text',
            'label'          => __('Email Subject', 'directorist-booking'),
            'value'          => __('New booking request for ==LISTING_TITLE==', 'directorist-booking'),
        ];
        $booking_fields['bdb_mail_waiting_owner_body'] = [
            'type'           => 'textarea',
            'label'          => __('Email Body', 'directorist-booking'),
            'value'          => __("
            Dear ==LISTING_OWNER==,

            You have received a new reservation from ==USER_EMAIL== for ==LISTING_TITLE== and is waiting to be approved in your Dashboard! ==CLICK_HERE== to review it.

            Regards,
            ", 'directorist-booking'),
        ];
        $booking_fields['bdb_mail_waiting_user_subject'] = [
            'type'           => 'text',
            'label'          => __('Email Subject', 'directorist-booking'),
            'value'          => __('Booking request for ==LISTING_TITLE== submitted successfully '),
        ];
        $booking_fields['bdb_mail_waiting_user_body'] = [
            'type'           => 'textarea',
            'label'          => __('Email Body', 'directorist-booking'),
            'value'          => __("Welcome ==USERNAME_WHO_BOOKED==,

            Your booking request has been submitted successfully and is waiting to be approved by the owner.
            
            Regards
            ", 'directorist-booking'),
        ];
        $booking_fields['bdb_mail_approved_user_subject'] = [
            'type'           => 'text',
            'label'          => __('Email Subject', 'directorist-booking'),
            'value'          => __('Booking confirmation for ==LISTING_TITLE=='),
        ];
        $booking_fields['bdb_mail_approved_user_body'] = [
            'type'           => 'textarea',
            'label'          => __('Email Body', 'directorist-booking'),
            'value'          => __("Hello ==USERNAME_WHO_BOOKED==,

            Congratulations! Your booking for ==LISTING_TITLE== has been confirmed.
            
            Regards,
            ", 'directorist-booking'),
        ];
        $booking_fields['bdb_mail_paid_owner_subject'] = [
            'type'           => 'text',
            'label'          => __('Email Subject', 'directorist-booking'),
            'value'          => __('New payment received for "==LISTING_TITLE=="', 'directorist-booking'),
        ];
        $booking_fields['bdb_mail_paid_owner_body'] = [
            'type'           => 'textarea',
            'label'          => __('Email Body', 'directorist-booking'),
            'value'          => __("Hello ==LISTING_OWNER==,

            The admin has received a new payment from ==USERNAME_WHO_BOOKED== for ==LISTING_TITLE==.
            
            Regards,
            ", 'directorist-booking'),
        ];

        $booking_fields['bdb_mail_paid_user_subject'] = [
            'type'           => 'text',
            'label'          => __('Email Subject', 'directorist-booking'),
            'value'          => __('Payment confirmation for ==LISTING_TITLE==', 'directorist-booking'),
        ];
        $booking_fields['bdb_mail_paid_user_body'] = [
            'type'           => 'textarea',
            'label'          => __('Email Body', 'directorist-booking'),
            'value'          => __("Hello ==USERNAME_WHO_BOOKED==,

            You have successfully made a payment for ==LISTING_TITLE==.
            
            Thank you,
            ", 'directorist-booking'),
        ];
        $booking_fields['bdb_mail_cancel_user_subject'] = [
            'type'           => 'text',
            'label'          => __('Email Subject', 'directorist-booking'),
            'value'          => __('Booking Cancellation for ==LISTING_TITLE==', 'directorist-booking'),
        ];
        $booking_fields['bdb_mail_cancel_user_body'] = [
            'type'           => 'textarea',
            'label'          => __('Email Body', 'directorist-booking'),
            'value'          => __("Hello ==USERNAME_WHO_BOOKED==,

            Your booking for ==LISTING_TITLE== has been cancelled.
            
            Regards,
            ", 'directorist-booking'),
        ];
        
        return $booking_fields;
    }

    public function atbdp_email_templates_settings_sections( $section ) {
        $section['email_waiting_owner'] = array(
            'title'       => __('For Booking Waiting ( Listing Owner )', 'directorist-booking'),
            'description' => '',
            'fields'      => [ 
                'bdb_mail_waiting_owner_subject', 'bdb_mail_waiting_owner_body'
            ],
        );
        $section['email_waiting_user'] = array(
            'title'       => __('For Booking Waiting ( User )', 'directorist-booking'),
            'description' => '',
            'fields'      => [ 
                'bdb_mail_waiting_user_subject', 'bdb_mail_waiting_user_body'
            ],
        );
        $section['email_approved_user'] = array(
            'title'       => __('For Booking Approved ( User )', 'directorist-booking'),
            'description' => '',
            'fields'      => [ 
                'bdb_mail_approved_user_subject', 'bdb_mail_approved_user_body'
            ],
        );
        $section['email_paid_owner'] = array(
            'title'       => __('For Booking Paid ( Listing Owner )', 'directorist-booking'),
            'description' => '',
            'fields'      => [ 
                'bdb_mail_paid_owner_subject', 'bdb_mail_paid_owner_body'
            ],
        );
        $section['email_paid_user'] = array(
            'title'       => __('For Booking Paid ( User )', 'directorist-booking'),
            'description' => '',
            'fields'      => [ 
                'bdb_mail_paid_owner_subject', 'bdb_mail_paid_user_body'
            ],
        );
        $section['email_cancel_user'] = array(
            'title'       => __('For Booking Cancellation ( User )', 'directorist-booking'),
            'description' => '',
            'fields'      => [
                'bdb_mail_cancel_user_subject', 'bdb_mail_cancel_user_body'
            ],
        );

        return $section;
    }

    public function atbdp_extension_settings_submenus( $submenu ) {
        $submenu['booking_submenu'] = [
            'label' => __('Booking', 'directorist-booking'),
                    'icon' => '<i class="fa fa-id-card"></i>',
                    'sections' => apply_filters( 'atbdp_booking_settings_controls', [
                        'general_section' => [
                            'title'       => '',
                            'description' => __('You can Customize the form of Booking Extension here', 'directorist-booking'),
                            'fields'      =>  [ 'bdb_guest_booking', 'bdb_commission_rate', 'bdb_booking_hide', 'bdb_booking_hiding_label', 'booking_type', 'bdb_booking_type_label', 'bdb_timing_type' ],
                        ],
                        'service_booking' => [
                            'title'       => __('Service Fields', 'directorist-booking'),
                            'fields'      =>  [ 'bdb_section_label', 'bdb_slot_label', 'bdb_instant_booking_label', 'bdb_maximum_guests_label' ],
                        ],
                        'event_booking' => [
                            'title'       => __('Event Fields', 'directorist-booking'),
                            'fields'      =>  [ 'bdb_available_ticket_label', 'bdb_perbooking_ticket_label'],
                        ],
                        'single_listing_booking' => [
                            'title'       => __('Single Listing', 'directorist-booking'),
                            'fields'      =>  [ 'bdb_request_booking_label', 'bdb_book_ticket_label', 'bdb_login_booking_label', 'bdb_reservation_fee_label' ],
                        ],
                    ] ),
        ];

        return $submenu;
    }

    //booking fields
    public function booking_type_fields() {
        return array(
            array(
                'type'          => 'toggle',
                'name'          => 'bdb_guest_booking',
                'label'         => __('Enable Guest Booking', 'directorist-booking'),
                'default'       => 0,
            ),
            array(
                'type'          => 'textbox',
                'name'          => 'bdb_commission_rate',
                'label'         => __('Commission Rate', 'directorist-booking'),
                'description'   => __('Set commission % for bookings', 'directorist-booking'),
                'default'       => 10,
            ),
            array(
                'type'          => 'toggle',
                'name'          => 'bdb_booking_hide',
                'label'         => __('Booking Hiding Option', 'directorist-booking'),
                'default'       => 1,
            ),
            array(
                'type'          => 'textbox',
                'name'          => 'bdb_booking_hiding_label',
                'label'         => __('Booking Hiding Label', 'directorist-booking'),
                'default'       => __('Check It to Hide Booking', 'directorist-booking'),
            ),
            array(
                'type' => 'select',
                'name' => 'booking_type',
                'label' => __('Booking Type', 'directorist-booking'),
                'items' => array(
                    array(
                        'value' => 'service',
                        'label' => __('Service Booking', 'directorist-booking'),
                    ),
                    array(
                        'value' => 'event',
                        'label' => __('Event Booking', 'directorist-booking'),
                    ),
                    array(
                        'value' => 'all',
                        'label' => __('Both', 'directorist-booking'),
                    ),
                ),
                'default' => array(
                    'value' => 'all',
                    'label' => __('Both', 'directorist-booking'),
                ),
            ),
            array(
                'type' => 'textbox',
                'name' => 'bdb_booking_type_label',
                'label' => __('Booking Type Label', 'directorist-booking'),
                'default' => __('Booking Type', 'directorist-booking'),
            ),
            array(
                'type'          => 'toggle',
                'name'          => 'bdb_timing_type',
                'label'         => __('Booking Timing Type', 'directorist-booking'),
                'default'       => 1,
            ),

        ); // ends fields array
    }

    //service booking fields
    public function service_booking_fields() {
        return array(
            array(
                'type' => 'textbox',
                'name' => 'bdb_section_label',
                'label' => __('Section Label', 'directorist-booking'),
                'default' => __('Booking', 'directorist-booking'),
            ),
            array(
                'type' => 'textbox',
                'name' => 'bdb_slot_label',
                'label' => __('Timing Type Label', 'directorist-booking'),
                'default' => __('Choose Timing Type', 'directorist-booking'),
            ),
            array(
                'type' => 'textbox',
                'name' => 'bdb_instant_booking_label',
                'label' => __('Enable Instant Booking Label', 'directorist-booking'),
                'default' => __('Enable Instant Booking', 'directorist-booking'),
            ),
            /* array(
                'type' => 'textbox',
                'name' => 'bdb_reservation_fee_label',
                'label' => __('Reservation Fee Label', 'directorist-booking'),
                'default' => __('Reservation Fee', 'directorist-booking'),
            ), */
            array(
                'type' => 'textbox',
                'name' => 'bdb_maximum_guests_label',
                'label' => __('Maximum Guests Label', 'directorist-booking'),
                'default' => __('Maximum Number of Guests', 'directorist-booking'),
            ),


        ); // ends fields array
    }

    //event booking fields
    public function event_booking_fields() {
        return array(
            array(
                'type' => 'textbox',
                'name' => 'bdb_available_ticket_label',
                'label' => __('Available Ticket Label', 'directorist-booking'),
                'default' => __('Available Tickets', 'directorist-booking'),
            ),
            array(
                'type' => 'textbox',
                'name' => 'bdb_perbooking_ticket_label',
                'label' => __('Tickets Allowed Per Booking
 Label', 'directorist-booking'),
                'default' => __('Tickets allowed per booking
', 'directorist-booking'),
            ),
        ); // ends fields array
    }

    function get_pages_vl_arrays()
        {
            $pages = get_pages();
            $pages_options = array();
            if ($pages) {
                foreach ($pages as $page) {
                    $pages_options[] = array('value' => $page->ID, 'label' => $page->post_title);
                }
            }

            return $pages_options;
        }
}
