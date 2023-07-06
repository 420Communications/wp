<?php
// Prohibit direct script loading.
defined('ABSPATH') || die('No direct script access allowed!');

class Directorist_Booking_Wallet
{
    public function __construct()
    {
        add_action( "atbdp_tab_after_favorite_listings", array( $this, "atbdp_tab_after_favorite_listings" ) );
        add_action( "atbdp_tab_content_after_favorite", array( $this, "atbdp_tab_content_after_favorite" ) );

        add_action( 'directorist_after_dashboard_navigation', [ $this, 'non_legacy_add_dashboard_nav_link' ] );
        add_action( 'directorist_after_dashboard_contents', [ $this, 'non_legacy_add_dashboard_nav_content' ] );
    }

    public function non_legacy_add_dashboard_nav_link() {
        $html = '<li class="directorist-tab__nav__item"><a href="" class="directorist-booking-nav-link directorist-tab__nav__link" id="my_wallet_tab" target="my_wallet"><span class="directorist_menuItem-text"><span class="directorist_menuItem-icon"><i class="la la-wallet"></i></span>' . esc_html__('My Wallet', 'directorist-booking') . '</span></a></li>';
        echo apply_filters('atbdp_user_dashboard_wallet_tab', $html);
    }

    public function non_legacy_add_dashboard_nav_content() {
        $commissions = new WP_Query( array(
            'post_type' => 'bdb_commission',
            'status'    => 'published',
        ) );
        $get_current_user_id        = get_current_user_id();
        $author_name     = get_the_author_meta( 'user_nicename', $get_current_user_id );
        $available_amount           = 0;
        $total_payout               = 0;
        $total_commission_order     = 0;
        $total_earning              = 0;
        $total_payout_order         = 0;
        if( $commissions->have_posts() ) {
            while( $commissions->have_posts() ) { $commissions->the_post();
                $username                = get_post_meta( get_the_ID(), '_username', true );
                if( $author_name == $username ) {
                    $available_amount                        = get_post_meta( get_the_ID(), '_total_balance_pay', true);
                    $_commission_listing_name                = get_post_meta( get_the_ID(), '_listing_name', true );
                    $_commission_publish_date                = get_post_meta( get_the_ID(), '_publish_date', true );
                    $_commission_order_id                    = get_post_meta( get_the_ID(), '_order_id', true );
                    $_commission_amount                      = get_post_meta( get_the_ID(), '_amount', true );
                    $_commission_site_fee                    = get_post_meta( get_the_ID(), '_site_fee', true );
                    $_commission_balance_pay                 = get_post_meta( get_the_ID(), '_balance_pay', true );

                    $unserialize_commission_listing_name     = unserialize( base64_decode( $_commission_listing_name ) );
                    $unserialize_commission_publish_date     = unserialize( base64_decode( $_commission_publish_date ) );
                    $unserialize_commission_order_id         = unserialize( base64_decode( $_commission_order_id     ) );
                    $unserialize_commission_amount           = unserialize( base64_decode( $_commission_amount ) );
                    $unserialize_commission_site_fee         = unserialize( base64_decode( $_commission_site_fee ) );
                    $unserialize_commission_balance_pay      = unserialize( base64_decode( $_commission_balance_pay ) );

                    $_unserialize_commission_listing_name[]  = $unserialize_commission_listing_name;
                    $_unserialize_commission_publish_date[]  = $unserialize_commission_publish_date;
                    $_unserialize_commission_order_id[]      = $unserialize_commission_order_id;
                    $_unserialize_commission_amount[]        = $unserialize_commission_amount;
                    $_unserialize_commission_site_fee[]      = $unserialize_commission_site_fee;
                    $_unserialize_commission_balance_pay[]   = $unserialize_commission_balance_pay;

                    $commission_listing_name                 = wallet_array_value( $_unserialize_commission_listing_name );
                    $commission_publish_date                 = wallet_array_value( $_unserialize_commission_publish_date );
                    $commission_order_id                     = wallet_array_value( $_unserialize_commission_order_id );
                    $commission_amount                       = wallet_array_value( $_unserialize_commission_amount );
                    $commission_site_fee                     = wallet_array_value( $_unserialize_commission_site_fee );
                    $commission_balance_pay                  = wallet_array_value( $_unserialize_commission_balance_pay );

                    $commission_order                        = get_post_meta( get_the_ID(), '_order_count', true);
                    $total_commission_order                  = ! empty( $commission_order ) ? $commission_order : 0;

                }
                wp_reset_postdata();

            }
        }

        $payouts = new WP_Query( array(
            'post_type' => 'bdb_payout',
            'status'    => 'published'
        ) );

        if( $payouts->have_posts() ) {
            $payout_total_balance_pay   = [];
            $payout_order               = [];
            while( $payouts->have_posts() ) { $payouts->the_post();
                $username                = get_post_meta( get_the_ID(), '_username', true );
                if( $author_name == $username ) {
                    $payout_total_balance_pay[]          = get_post_meta( get_the_ID(), '_total_balance_pay', true );
                    $payout_order[]                      = get_post_meta( get_the_ID(), '_order_count', true );

                    $_payout_listing_name                = get_post_meta( get_the_ID(), '_listing_name', true );
                    $_payout_publish_date                = get_post_meta( get_the_ID(), '_publish_date', true );
                    $_payout_order_id                    = get_post_meta( get_the_ID(), '_order_id', true );
                    $_payout_amount                      = get_post_meta( get_the_ID(), '_amount', true );
                    $_payout_site_fee                    = get_post_meta( get_the_ID(), '_site_fee', true );
                    $_payout_balance_pay                 = get_post_meta( get_the_ID(), '_balance_pay', true );

                    $unserialize_payout_listing_name     = unserialize( base64_decode( $_payout_listing_name ) );
                    $unserialize_payout_publish_date     = unserialize( base64_decode( $_payout_publish_date ) );
                    $unserialize_payout_order_id         = unserialize( base64_decode( $_payout_order_id     ) );
                    $unserialize_payout_amount           = unserialize( base64_decode( $_payout_amount ) );
                    $unserialize_payout_site_fee         = unserialize( base64_decode( $_payout_site_fee ) );
                    $unserialize_payout_balance_pay      = unserialize( base64_decode( $_payout_balance_pay ) );

                    $_unserialize_payout_listing_name[]  = $unserialize_payout_listing_name;
                    $_unserialize_payout_publish_date[]  = $unserialize_payout_publish_date;
                    $_unserialize_payout_order_id[]      = $unserialize_payout_order_id;
                    $_unserialize_payout_amount[]        = $unserialize_payout_amount;
                    $_unserialize_payout_site_fee[]      = $unserialize_payout_site_fee;
                    $_unserialize_payout_balance_pay[]   = $unserialize_payout_balance_pay;

                    $payout_listing_name                 = wallet_array_value( $_unserialize_payout_listing_name );
                    $payout_publish_date                 = wallet_array_value( $_unserialize_payout_publish_date );
                    $payout_order_id                     = wallet_array_value( $_unserialize_payout_order_id     );
                    $payout_amount                       = wallet_array_value( $_unserialize_payout_amount );
                    $payout_site_fee                     = wallet_array_value( $_unserialize_payout_site_fee );
                    $payout_balance_pay                  = wallet_array_value( $_unserialize_payout_balance_pay );

                   if( $payout_total_balance_pay ) {
                       $total_payout = 0;
                       foreach( $payout_total_balance_pay as $value) {
                        $total_payout += $value;
                       }
                   }
                   if( $payout_order ) {
                        $total_payout_order = 0;
                        foreach( $payout_order as $value) {
                        $total_payout_order += $value;
                        }
                    }

                }
                wp_reset_postdata();

            }
        }
        $total_earning              = $total_payout + $available_amount;
        $total_available_amount     = atbdp_display_price( $available_amount, false,  '', '', '', false );
        $total_earning              = atbdp_display_price( $total_earning, false,  '', '', '', false  );
        $total_orders               = $total_commission_order + $total_payout_order;

        $available_earning     = [];
        if( ! empty ( $commission_listing_name ) ) {
            foreach ( $commission_listing_name as $index => $name ) {
                $available_earning[] = [
                    'listing_name'  => $name,
                    'date'          => ! empty( $commission_publish_date[ $index ] ) ? $commission_publish_date[ $index ] : [],
                    'order_id'      => ! empty( $commission_order_id[ $index ] ) ? $commission_order_id[ $index ] : [],
                    'amount'        => ! empty( $commission_amount[ $index ] ) ? $commission_amount[ $index ] : [],
                    'site_fee'      => ! empty( $commission_site_fee[ $index ] ) ? $commission_site_fee[ $index ] : [],
                    'earning'       => ! empty( $commission_balance_pay[ $index ] ) ? $commission_balance_pay[ $index ] : [],
                    'status'        => __( 'Pending', 'directorist-booking' )
                ];
            }
        }

        $payout_earning = [];
        if( ! empty ( $payout_listing_name ) ) {
            foreach ( $payout_listing_name as $index => $name ) {
                $payout_earning[] = [
                    'listing_name'  => $name,
                    'date'          => ! empty( $payout_publish_date[ $index ] ) ? $payout_publish_date[ $index ] : [],
                    'order_id'      => ! empty( $payout_order_id[ $index ] ) ? $payout_order_id[ $index ] : [],
                    'amount'        => ! empty( $payout_amount[ $index ] ) ? $payout_amount[ $index ] : [],
                    'site_fee'      => ! empty( $payout_site_fee[ $index ] ) ? $payout_site_fee[ $index ] : [],
                    'earning'       => ! empty( $payout_balance_pay[ $index ] ) ? $payout_balance_pay[ $index ] : [],
                    'status'        => __( 'Completed', 'directorist-booking' )
                ];
            }
        }
        $commission_rate = get_directorist_option( 'bdb_commission_rate', 10 );
        $all_earnings = array_merge( $payout_earning, $available_earning ); ?>
        <div <?php echo apply_filters( 'wallet_dashboard_content_div_attributes', 'class="directorist-tab__pane" id="my_wallet"' ); ?>>
        <?php
        include BDB_TEMPLATES_DIR . '/booking-wallet.php'; ?>
        </div>
        <?php

    }

    public function atbdp_tab_after_favorite_listings() {
        $html = '<li class="atbdp_tab_nav--content-link"><a href="" class="atbd_tn_link" id="my_wallet_tab" target="my_wallet"> ' . esc_html__('My Wallet', 'directorist-booking') . '</a></li>';
        echo apply_filters('atbdp_user_dashboard_wallet_tab', $html);
    }

    public function atbdp_tab_content_after_favorite() {

        $commissions = new WP_Query( array(
            'post_type' => 'bdb_commission',
            'status'    => 'published',
        ) );
        $get_current_user_id        = get_current_user_id();
        $author_name     = get_the_author_meta( 'user_nicename', $get_current_user_id );
        $available_amount           = 0;
        $total_payout               = 0;
        $total_commission_order     = 0;
        $total_earning              = 0;
        $total_payout_order         = 0;
        if( $commissions->have_posts() ) {
            while( $commissions->have_posts() ) { $commissions->the_post();
                $username                = get_post_meta( get_the_ID(), '_username', true );
                if( $author_name == $username ) {
                    $available_amount                        = get_post_meta( get_the_ID(), '_total_balance_pay', true);
                    $_commission_listing_name                = get_post_meta( get_the_ID(), '_listing_name', true );
                    $_commission_publish_date                = get_post_meta( get_the_ID(), '_publish_date', true );
                    $_commission_order_id                    = get_post_meta( get_the_ID(), '_order_id', true );
                    $_commission_amount                      = get_post_meta( get_the_ID(), '_amount', true );
                    $_commission_site_fee                    = get_post_meta( get_the_ID(), '_site_fee', true );
                    $_commission_balance_pay                 = get_post_meta( get_the_ID(), '_balance_pay', true );

                    $unserialize_commission_listing_name     = unserialize( base64_decode( $_commission_listing_name ) );
                    $unserialize_commission_publish_date     = unserialize( base64_decode( $_commission_publish_date ) );
                    $unserialize_commission_order_id         = unserialize( base64_decode( $_commission_order_id     ) );
                    $unserialize_commission_amount           = unserialize( base64_decode( $_commission_amount ) );
                    $unserialize_commission_site_fee         = unserialize( base64_decode( $_commission_site_fee ) );
                    $unserialize_commission_balance_pay      = unserialize( base64_decode( $_commission_balance_pay ) );

                    $_unserialize_commission_listing_name[]  = $unserialize_commission_listing_name;
                    $_unserialize_commission_publish_date[]  = $unserialize_commission_publish_date;
                    $_unserialize_commission_order_id[]      = $unserialize_commission_order_id;
                    $_unserialize_commission_amount[]        = $unserialize_commission_amount;
                    $_unserialize_commission_site_fee[]      = $unserialize_commission_site_fee;
                    $_unserialize_commission_balance_pay[]   = $unserialize_commission_balance_pay;

                    $commission_listing_name                 = wallet_array_value( $_unserialize_commission_listing_name );
                    $commission_publish_date                 = wallet_array_value( $_unserialize_commission_publish_date );
                    $commission_order_id                     = wallet_array_value( $_unserialize_commission_order_id );
                    $commission_amount                       = wallet_array_value( $_unserialize_commission_amount );
                    $commission_site_fee                     = wallet_array_value( $_unserialize_commission_site_fee );
                    $commission_balance_pay                  = wallet_array_value( $_unserialize_commission_balance_pay );

                    $commission_order                        = get_post_meta( get_the_ID(), '_order_count', true);
                    $total_commission_order                  = ! empty( $commission_order ) ? $commission_order : 0;

                }
                wp_reset_postdata();

            }
        }

        $payouts = new WP_Query( array(
            'post_type' => 'bdb_payout',
            'status'    => 'published'
        ) );

        if( $payouts->have_posts() ) {
            $payout_total_balance_pay   = [];
            $payout_order               = [];
            while( $payouts->have_posts() ) { $payouts->the_post();
                $username                = get_post_meta( get_the_ID(), '_username', true );
                if( $author_name == $username ) {
                    $payout_total_balance_pay[]          = get_post_meta( get_the_ID(), '_total_balance_pay', true );
                    $payout_order[]                      = get_post_meta( get_the_ID(), '_order_count', true );

                    $_payout_listing_name                = get_post_meta( get_the_ID(), '_listing_name', true );
                    $_payout_publish_date                = get_post_meta( get_the_ID(), '_publish_date', true );
                    $_payout_order_id                    = get_post_meta( get_the_ID(), '_order_id', true );
                    $_payout_amount                      = get_post_meta( get_the_ID(), '_amount', true );
                    $_payout_site_fee                    = get_post_meta( get_the_ID(), '_site_fee', true );
                    $_payout_balance_pay                 = get_post_meta( get_the_ID(), '_balance_pay', true );

                    $unserialize_payout_listing_name     = unserialize( base64_decode( $_payout_listing_name ) );
                    $unserialize_payout_publish_date     = unserialize( base64_decode( $_payout_publish_date ) );
                    $unserialize_payout_order_id         = unserialize( base64_decode( $_payout_order_id     ) );
                    $unserialize_payout_amount           = unserialize( base64_decode( $_payout_amount ) );
                    $unserialize_payout_site_fee         = unserialize( base64_decode( $_payout_site_fee ) );
                    $unserialize_payout_balance_pay      = unserialize( base64_decode( $_payout_balance_pay ) );

                    $_unserialize_payout_listing_name[]  = $unserialize_payout_listing_name;
                    $_unserialize_payout_publish_date[]  = $unserialize_payout_publish_date;
                    $_unserialize_payout_order_id[]      = $unserialize_payout_order_id;
                    $_unserialize_payout_amount[]        = $unserialize_payout_amount;
                    $_unserialize_payout_site_fee[]      = $unserialize_payout_site_fee;
                    $_unserialize_payout_balance_pay[]   = $unserialize_payout_balance_pay;

                    $payout_listing_name                 = wallet_array_value( $_unserialize_payout_listing_name );
                    $payout_publish_date                 = wallet_array_value( $_unserialize_payout_publish_date );
                    $payout_order_id                     = wallet_array_value( $_unserialize_payout_order_id     );
                    $payout_amount                       = wallet_array_value( $_unserialize_payout_amount );
                    $payout_site_fee                     = wallet_array_value( $_unserialize_payout_site_fee );
                    $payout_balance_pay                  = wallet_array_value( $_unserialize_payout_balance_pay );

                   if( $payout_total_balance_pay ) {
                       $total_payout = 0;
                       foreach( $payout_total_balance_pay as $value) {
                        $total_payout += $value;
                       }
                   }
                   if( $payout_order ) {
                        $total_payout_order = 0;
                        foreach( $payout_order as $value) {
                        $total_payout_order += $value;
                        }
                    }

                }
                wp_reset_postdata();

            }
        }
        $total_earning              = $total_payout + $available_amount;
        $total_available_amount     = atbdp_display_price( $available_amount, false,  '', '', '', false );
        $total_earning              = atbdp_display_price( $total_earning, false,  '', '', '', false  );
        $total_orders               = $total_commission_order + $total_payout_order;

        $available_earning     = [];
        if( ! empty ( $commission_listing_name ) ) {
            foreach ( $commission_listing_name as $index => $name ) {
                $available_earning[] = [
                    'listing_name'  => $name,
                    'date'          => ! empty( $commission_publish_date[ $index ] ) ? $commission_publish_date[ $index ] : [],
                    'order_id'      => ! empty( $commission_order_id[ $index ] ) ? $commission_order_id[ $index ] : [],
                    'amount'        => ! empty( $commission_amount[ $index ] ) ? $commission_amount[ $index ] : [],
                    'site_fee'      => ! empty( $commission_site_fee[ $index ] ) ? $commission_site_fee[ $index ] : [],
                    'earning'       => ! empty( $commission_balance_pay[ $index ] ) ? $commission_balance_pay[ $index ] : [],
                    'status'        => __( 'Pending', 'directorist-booking' )
                ];
            }
        }

        $payout_earning = [];
        if( ! empty ( $payout_listing_name ) ) {
            foreach ( $payout_listing_name as $index => $name ) {
                $payout_earning[] = [
                    'listing_name'  => $name,
                    'date'          => ! empty( $payout_publish_date[ $index ] ) ? $payout_publish_date[ $index ] : [],
                    'order_id'      => ! empty( $payout_order_id[ $index ] ) ? $payout_order_id[ $index ] : [],
                    'amount'        => ! empty( $payout_amount[ $index ] ) ? $payout_amount[ $index ] : [],
                    'site_fee'      => ! empty( $payout_site_fee[ $index ] ) ? $payout_site_fee[ $index ] : [],
                    'earning'       => ! empty( $payout_balance_pay[ $index ] ) ? $payout_balance_pay[ $index ] : [],
                    'status'        => __( 'Completed', 'directorist-booking' )
                ];
            }
        }
        $commission_rate = get_directorist_option( 'bdb_commission_rate', 10 );
        $all_earnings = array_merge( $payout_earning, $available_earning );
        ?>
        <div <?php echo apply_filters( 'wallet_dashboard_content_div_attributes', 'class="atbd_tab_inner" id="my_wallet"' ); ?>>
        <?php
        include BDB_TEMPLATES_DIR . '/booking-wallet.php'; ?>
        </div>
        <?php
    }
}