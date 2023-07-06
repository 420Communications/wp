<?php
// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');

class Directorist_Booking_Dashboard
{
    public function __construct()
    {
        add_action("atbdp_tab_after_favorite_listings", array($this, "atbdp_tab_after_favorite_listings"));
        add_action("atbdp_tab_after_favorite_listings", array($this, "atbdp_tab_after_my_bookings"));
        add_action("atbdp_tab_content_after_favorite", array($this, "atbdp_tab_content_after_favorite"));
        add_action("atbdp_tab_content_after_favorite", array($this, "atbdp_booking_tab_content_after_my_bookings"));

        // non legacy template
        add_action( 'directorist_after_dashboard_navigation', [ $this, 'non_legacy_add_dashboard_nav_link' ] );
        add_action( 'directorist_after_dashboard_contents', [ $this, 'non_legacy_add_dashboard_nav_content' ] );

        add_action( 'directorist_after_dashboard_navigation', [ $this, 'non_legacy_all_add_dashboard_nav_link' ] );
        add_action( 'directorist_after_dashboard_contents', [ $this, 'non_legacy_all_add_dashboard_nav_content' ] );

        add_action( 'directorist_after_dashboard_navigation', [ $this, 'dashboard_calender_booking_nav_link' ] );
        add_action( 'directorist_after_dashboard_contents', [ $this, 'dashboard_calender_booking_nav_content' ] );
    }

    public function dashboard_calender_booking_nav_link() {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        $html = '<li class="directorist-tab__nav__item"><a href="#" class="directorist-booking-nav-link directorist-tab__nav__link" id="booking_calender_tab" target="booking_calender"><span class="directorist_menuItem-text"><span class="directorist_menuItem-icon"><i class="la la-calendar-o"></i></span>' . esc_html__('Booking Calender', 'directorist-booking') . '</span></a></li>';
        echo apply_filters('directorist_user_dashboard_booking_calender_tab', $html);
    }

    public function dashboard_calender_booking_nav_content() {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        wp_enqueue_style('bdb-daterangepicker-style');
        wp_enqueue_script('bdb-main-js');
        wp_enqueue_script('bdb-dashboard-js');
        wp_enqueue_script('bdb-moment');
        wp_enqueue_script('bdb-daterangepicker');

         ?>
        <div class="directorist-tab__pane" id="booking_calender">
                <?php include BDB_TEMPLATES_DIR . '/booking-calender.php'; ?>
           
        </div>

        <?php
        
    }

    public function non_legacy_all_add_dashboard_nav_link() {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        $html = '<li class="directorist-tab__nav__item atbdp_tab_nav--has-child atbdp_all_booking_nav">';
        $html .= '<a href="#" class="atbdp_all_booking_nav-link atbd-dash-nav-dropdown"><span class="directorist_menuItem-text"><span class="directorist_menuItem-icon"><i class="la la-clipboard-list"></i></span>' . __("All Bookings", "directorist-booking") . '</span><span class="fa fa-angle-down"></span></a>';
        $html .= '<ul class="atbd-dashboard-nav">';
        $html .= '<li><a href="" class="directorist-booking-nav-link directorist-tab__nav__link" target="booking_approved">' . __("Approved", "directorist-booking") . '<span class="badge-active">'. bdb_count_bookings( get_current_user_id(), 'approved' ) .'</span></a></li>';
        $html .= '<li><a href="" class="directorist-booking-nav-link directorist-tab__nav__link" target="booking_waiting">' . __("Pending", "directorist-booking") . '<span class="badge-pending">'. bdb_count_bookings( get_current_user_id(), 'waiting' ) .'</span></a></li>';
        $html .= '<li><a href="" class="directorist-booking-nav-link directorist-tab__nav__link" target="booking_cancelled">' . __("Cancelled", "directorist-booking") . '<span class="badge-cancelled">'. bdb_count_bookings( get_current_user_id(), 'cancelled' ) .'</span></a></li>';
        $html .= '</ul>';
        $html .= '</li>';

        echo apply_filters('atbdp_user_dashboard_all_bookings_tab', $html);
    }

    public function non_legacy_all_add_dashboard_nav_content() {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        wp_enqueue_style('bdb-daterangepicker-style');
        wp_enqueue_script('bdb-main-js');
        wp_enqueue_script('bdb-dashboard-js');
        wp_enqueue_script('bdb-moment');
        wp_enqueue_script('bdb-daterangepicker');
        // approved tab content
        $html = '<div class="directorist-tab__pane" id="booking_approved">';
        echo apply_filters('atbdp_user_dashboard_approved_bookings_content_wrapper', $html);
            $listings = $this->get_agent_listings('');
            $args = array(
                'owner_id' => get_current_user_id(),
                'type' => 'reservation',
            );
            $limit = 10;
            $pages = '';
            $_GET['status'] = 'approved';
            if (isset($_GET['status'])) {
                $booking_max = bdb_count_bookings(get_current_user_id(), $_GET['status']);
                $pages = ceil($booking_max / $limit);
                $args['status'] = $_GET['status'];
            } else {
                $booking_max = bdb_count_bookings(get_current_user_id());
                $pages = ceil($booking_max / $limit);
            }
            $bookings = BD_Booking()->bdb_booking_database->get_newest_bookings($args, $limit);
            $data = array(
                'message' => '',
                'bookings' => $bookings,
                'listings' => $listings,
                'pages' => $pages
            );

            if (is_user_logged_in()) {
                include BDB_TEMPLATES_DIR . '/all-bookings.php';
            }
        echo wp_kses_post('</div>');

        //pending tab
        $html = '<div class="directorist-tab__pane" id="booking_waiting">';
            echo apply_filters('atbdp_user_dashboard_pending_bookings_content_wrapper', $html);
            $listings = $this->get_agent_listings('');
            $args = array(
                'owner_id' => get_current_user_id(),
                'type' => 'reservation',
            );
            $limit = 10;
            $pages = '';
            $_GET['status'] = 'waiting';
            if (isset($_GET['status'])) {
                $booking_max = bdb_count_bookings(get_current_user_id(), $_GET['status']);
                $pages = ceil($booking_max / $limit);
                $args['status'] = $_GET['status'];
            } else {
                $booking_max = bdb_count_bookings(get_current_user_id());
                $pages = ceil($booking_max / $limit);
            }
            $bookings = BD_Booking()->bdb_booking_database->get_newest_bookings($args, $limit);
            $data = array(
                'message' => '',
                'bookings' => $bookings,
                'listings' => $listings,
                'pages' => $pages
            );

            if (atbdp_logged_in_user()) {
                include BDB_TEMPLATES_DIR . '/all-bookings.php';
            }
        echo wp_kses_post('</div>');

        //cancelled tab
        $html = '<div class="directorist-tab__pane" id="booking_cancelled">';
            echo apply_filters('atbdp_user_dashboard_cancelled_bookings_content_wrapper', $html);
            $listings = $this->get_agent_listings('');
            $args = array(
                'owner_id' => get_current_user_id(),
                'type' => 'reservation',
            );
            $limit = 10;
            $pages = '';
            $_GET['status'] = 'cancelled';
            if (isset($_GET['status'])) {
                $booking_max = bdb_count_bookings(get_current_user_id(), $_GET['status']);
                $pages = ceil($booking_max / $limit);
                $args['status'] = $_GET['status'];
            } else {
                $booking_max = bdb_count_bookings(get_current_user_id());
                $pages = ceil($booking_max / $limit);
            }
            $bookings = BD_Booking()->bdb_booking_database->get_newest_bookings($args, $limit);
            $data = array(
                'message' => '',
                'bookings' => $bookings,
                'listings' => $listings,
                'pages' => $pages
            );

            if (atbdp_logged_in_user()) {
                include BDB_TEMPLATES_DIR . '/all-bookings.php';
            }
        echo wp_kses_post('</div>');
    }

    public function non_legacy_add_dashboard_nav_link() {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        $html = '<li class="directorist-tab__nav__item"><a href="#" class="directorist-booking-nav-link directorist-tab__nav__link" id="my_bookings_tab" target="my_booking"><span class="directorist_menuItem-text"><span class="directorist_menuItem-icon"><i class="la la-calendar-o"></i></span>' . esc_html__('My Bookings', 'directorist-booking') . '</span></a></li>';
        echo apply_filters('atbdp_user_dashboard_booking_tab', $html);
    }

    public function non_legacy_add_dashboard_nav_content() {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        wp_enqueue_style('bdb-daterangepicker-style');
        wp_enqueue_script('bdb-main-js');
        wp_enqueue_script('bdb-dashboard-js');
        wp_enqueue_script('bdb-moment');
        wp_enqueue_script('bdb-daterangepicker');

        $html = '<div class="directorist-tab__pane" id="my_booking">';
        echo apply_filters('atbdp_user_dashboard_booking_content_wrapper', $html);

        $args = array(
            'bookings_author' => get_current_user_id(),
            'type' => 'reservation'
        );
        $limit = 10;

        $bookings = BD_Booking()->bdb_booking_database->get_newest_bookings($args, $limit);
        $booking_max = bdb_count_my_bookings(get_current_user_id());
        $pages = ceil($booking_max / $limit);
        $data = array(
            'message' => '',
            'type' => 'user_booking',
            'bookings' => $bookings,
            'pages' => $pages
        ); ?>

        <div id="my_bookings_area">
            <?php include BDB_TEMPLATES_DIR . '/all-bookings.php'; ?>
        </div>

        <div class="test"></div>

        <?php
        echo wp_kses_post('</div>');
    }





    // added the tab on dashboard
    public function atbdp_tab_after_favorite_listings()
    {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        $html = '<li class="atbdp_tab_nav--content-link"><a href="" class="atbd_tn_link" id="my_bookings_tab" target="my_booking"> ' . esc_html__('My Bookings', 'directorist-booking') . '</a></li>';
        echo apply_filters('atbdp_user_dashboard_booking_tab', $html);
    }

    // add all bookings tab on dashboard
    public function atbdp_tab_after_my_bookings()
    {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        $html = '<li class="atbdp_tab_nav--content-link atbdp_tab_nav--has-child atbdp_all_booking_nav">';
        $html .= '<a href="#" class="atbdp_all_booking_nav-link directorist-tab__nav__link atbd-dash-nav-dropdown">' . __("All Bookings", "directorist-booking") . ' <span class="fa fa-angle-down"></span></a>';
        $html .= '<ul class="atbd-dashboard-nav">';
        $html .= '<li><a href="" class="atbd_tn_link" target="booking_approved">' . __("Approved", "directorist-booking") . '</a></li>';
        $html .= '<li><a href="" class="atbd_tn_link" target="booking_waiting">' . __("Pending", "directorist-booking") . '</a></li>';
        $html .= '<li><a href="" class="atbd_tn_link" target="booking_cancelled">' . __("Cancelled", "directorist-booking") . '</a></li>';
        $html .= '</ul>';
        $html .= '</li>';

        echo apply_filters('atbdp_user_dashboard_all_bookings_tab', $html);
    }

    // content of my booking tab
    public function atbdp_tab_content_after_favorite()
    {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        wp_enqueue_style('bdb-daterangepicker-style');
        wp_enqueue_script('bdb-main-js');
        wp_enqueue_script('bdb-dashboard-js');
        wp_enqueue_script('bdb-moment');
        wp_enqueue_script('bdb-daterangepicker');

        $html = '<div class="atbd_tab_inner" id="my_booking">';
        echo apply_filters('atbdp_user_dashboard_booking_content_wrapper', $html);

        $args = array(
            'bookings_author' => get_current_user_id(),
            'type' => 'reservation'
        );
        $limit = 10;

        $bookings = BD_Booking()->bdb_booking_database->get_newest_bookings($args, $limit);
        $booking_max = bdb_count_my_bookings(get_current_user_id());
        $pages = ceil($booking_max / $limit);
        $data = array(
            'message' => '',
            'type' => 'user_booking',
            'bookings' => $bookings,
            'pages' => $pages
        ); ?>

        <div id="my_bookings_area">
            <?php include BDB_TEMPLATES_DIR . '/all-bookings.php'; ?>
        </div>

        <div class="test"></div>

        <?php
        echo wp_kses_post('</div>');
    }

    public function atbdp_booking_tab_content_after_my_bookings()
    {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        wp_enqueue_style('bdb-daterangepicker-style');
        wp_enqueue_script('bdb-main-js');
        wp_enqueue_script('bdb-dashboard-js');
        wp_enqueue_script('bdb-moment');
        wp_enqueue_script('bdb-daterangepicker');
        // approved tab content
        $html = '<div class="atbd_tab_inner" id="booking_approved">';
        echo apply_filters('atbdp_user_dashboard_approved_bookings_content_wrapper', $html);
            $listings = $this->get_agent_listings('');
            $args = array(
                'owner_id' => get_current_user_id(),
                'type' => 'reservation',
            );
            $limit = 10;
            $pages = '';
            $_GET['status'] = 'approved';
            if (isset($_GET['status'])) {
                $booking_max = bdb_count_bookings(get_current_user_id(), $_GET['status']);
                $pages = ceil($booking_max / $limit);
                $args['status'] = $_GET['status'];
            } else {
                $booking_max = bdb_count_bookings(get_current_user_id());
                $pages = ceil($booking_max / $limit);
            }
            $bookings = BD_Booking()->bdb_booking_database->get_newest_bookings($args, $limit);
            $data = array(
                'message' => '',
                'bookings' => $bookings,
                'listings' => $listings,
                'pages' => $pages
            );

            if (atbdp_logged_in_user()) {
                include BDB_TEMPLATES_DIR . '/all-bookings.php';
            }
        echo wp_kses_post('</div>');

        //pending tab
        $html = '<div class="atbd_tab_inner" id="booking_waiting">';
            echo apply_filters('atbdp_user_dashboard_pending_bookings_content_wrapper', $html);
            $listings = $this->get_agent_listings('');
            $args = array(
                'owner_id' => get_current_user_id(),
                'type' => 'reservation',
            );
            $limit = 10;
            $pages = '';
            $_GET['status'] = 'waiting';
            if (isset($_GET['status'])) {
                $booking_max = bdb_count_bookings(get_current_user_id(), $_GET['status']);
                $pages = ceil($booking_max / $limit);
                $args['status'] = $_GET['status'];
            } else {
                $booking_max = bdb_count_bookings(get_current_user_id());
                $pages = ceil($booking_max / $limit);
            }
            $bookings = BD_Booking()->bdb_booking_database->get_newest_bookings($args, $limit);
            $data = array(
                'message' => '',
                'bookings' => $bookings,
                'listings' => $listings,
                'pages' => $pages
            );

            if (atbdp_logged_in_user()) {
                include BDB_TEMPLATES_DIR . '/all-bookings.php';
            }
        echo wp_kses_post('</div>');

        //cancelled tab
        $html = '<div class="atbd_tab_inner" id="booking_cancelled">';
            echo apply_filters('atbdp_user_dashboard_cancelled_bookings_content_wrapper', $html);
            $listings = $this->get_agent_listings('');
            $args = array(
                'owner_id' => get_current_user_id(),
                'type' => 'reservation',
            );
            $limit = 10;
            $pages = '';
            $_GET['status'] = 'cancelled';
            if (isset($_GET['status'])) {
                $booking_max = bdb_count_bookings(get_current_user_id(), $_GET['status']);
                $pages = ceil($booking_max / $limit);
                $args['status'] = $_GET['status'];
            } else {
                $booking_max = bdb_count_bookings(get_current_user_id());
                $pages = ceil($booking_max / $limit);
            }
            $bookings = BD_Booking()->bdb_booking_database->get_newest_bookings($args, $limit);
            $data = array(
                'message' => '',
                'bookings' => $bookings,
                'listings' => $listings,
                'pages' => $pages
            );

            if (atbdp_logged_in_user()) {
                include BDB_TEMPLATES_DIR . '/all-bookings.php';
            }
        echo wp_kses_post('</div>');
    }

    /**
     * Function to get ids added by the user/agent
     * @return array array of listing ids
     */
    public function get_agent_listings($status)
    {
        $enable_booking = get_directorist_option('enable_booking', 1);
        if(empty($enable_booking)) return;

        $current_user = wp_get_current_user();

        switch ($status) {
            case 'pending':
                $post_status = array('pending_payment', 'draft', 'pending');
                break;

            case 'active':
                $post_status = array('publish');
                break;

            case 'expired':
                $post_status = array('expired');
                break;

            default:
                $post_status = array('publish', 'pending_payment', 'expired', 'draft', 'pending');
                break;
        }

        return get_posts(array(
            'author' => $current_user->ID,
            'fields' => 'ids', // Only get post IDs
            'posts_per_page' => -1,
            'post_type' => ATBDP_POST_TYPE,
            'post_status' => $post_status,
        ));
    }
}
