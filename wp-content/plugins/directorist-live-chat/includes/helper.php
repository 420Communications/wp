<?php
defined('ABSPATH') || die('No direct script access allowed!');
/**
 * @package Directorist
 * @since 1.0.0
 */


if ( ! function_exists( 'atbdp_check_live_chat_restriction' ) ) {
    function atbdp_check_live_chat_restriction( $post_id = '' ) {
        // Check Restriction
        $restricted = apply_filters( 'atbdp_live_chat_is_restricted', false, $post_id );

        return $restricted;
    }
}

if (!function_exists('get_chats')){
    function get_chats($listing_author = null, $chat_author = null, $chatListing_id = null)
    {
        $args = array(
            'post_type' => 'atbdp_chat',
            'order' => 'ASC',
            'posts_per_page' => -1,
        );

        $meta_queries = array();
        $meta_queries[] = array(
            'relation' => 'OR',
            array(
                'key' => '_chat_listing_author',
                'value' => !empty($listing_author) ? $listing_author : get_current_user_id(),
                'compare' => '='
            ),
            array(
                'key' => '_chatAuthor_id',
                'value' => !empty($chat_author) ? $chat_author : get_current_user_id(),
                'compare' => '='
            ),
        );

        if (!empty($chatListing_id)) {
            $meta_queries[] = array(
                'key' => '_chatListing_id',
                'value' => !empty($chatListing_id) ? $chatListing_id : '',
                'compare' => '='
            );
        }

        $count_meta_queries = count($meta_queries);
        if ($count_meta_queries) {
            $args['meta_query'] = ($count_meta_queries > 1) ? array_merge(array('relation' => 'AND'), $meta_queries) : $meta_queries;
        }
        $chats = new WP_Query($args);
        return $chats;
    }
}

if (!function_exists('get_chat_by_user')){
    function get_chat_by_user($listing_author = null, $chat_author = null, $chatListing_id = null)
    {
        $args = array(
            'post_type' => 'atbdp_chat',
            'order' => 'ASC',
            'posts_per_page' => -1,
        );

        $meta_queries = array();
        $meta_queries[] = array(
            'relation' => 'AND',
            array(
                'key' => '_chat_listing_author',
                'value' => !empty($listing_author) ? $listing_author : '',
                'compare' => '='
            ),
            array(
                'key' => '_chatAuthor_id',
                'value' => !empty($chat_author) ? $chat_author : '',
                'compare' => '='
            ),
        );

        if (!empty($chatListing_id)) {
            $meta_queries[] = array(
                'key' => '_chatListing_id',
                'value' => !empty($chatListing_id) ? $chatListing_id : '',
                'compare' => '='
            );
        }

        $count_meta_queries = count($meta_queries);
        if ($count_meta_queries) {
            $args['meta_query'] = ($count_meta_queries > 1) ? array_merge(array('relation' => 'AND'), $meta_queries) : $meta_queries;
        }
        $chats = new WP_Query($args);
        return $chats;
    }
}

if (!function_exists('get_chatted_listings')){
    function get_chatted_listings($listing_author = null, $chat_author = null, $chatListing_id = null){
        $args = array(
            'post_type' => 'atbdp_chat',
            'order' => 'ASC',
            'posts_per_page' => -1,
            'meta_key' =>  '_chat_listing_author',
            'meta_value' => !empty($listing_author) ? $listing_author : get_current_user_id(),
            'compare' => '='
        );
        $chats = new WP_Query($args);
        if ($chats->have_posts()){
            $listing_ids = array();
            foreach ($chats->get_posts() as $post){
                $post_id = $post->ID;
                $listing_id = get_post_meta($post_id, '_chatListing_id', true);
                array_push($listing_ids, $listing_id);
            }
            return array_unique($listing_ids);
        }
    }
}

if(!function_exists('listing_chat_exists_by_user')){
    function listing_chat_exists_by_user($listing_author = null, $chat_author = null, $chatListing_id = null){
        $args = array(
            'post_type' => 'atbdp_chat',
            'order' => 'ASC',
            'posts_per_page' => -1,
            'author' => !empty($chat_author) ? (int)$chat_author : get_current_user_id(),
        );
        $meta_queries = array();
        $meta_queries[] = array(
            'relation' => 'AND',
            array(
                'key' => '_chatAuthor_id',
                'value' =>  !empty($chat_author) ? (int)$chat_author : '',
                'compare' => '='
            ),
            array(
                'key' => '_chatListing_id',
                'value' => !empty($chatListing_id) ? (int)$chatListing_id : get_the_ID(),
                'compare' => '='
            ),
        );
        $count_meta_queries = count($meta_queries);
        if ($count_meta_queries) {
            $args['meta_query'] = ($count_meta_queries > 1) ? array_merge(array('relation' => 'AND'), $meta_queries) : $meta_queries;
        }
        $chats = new WP_Query($args);
        return $chats;
    }
}

if(!function_exists('listing_chat_by_admin')){
    function listing_chat_by_admin( $chat_author = null, $chatListing_id = null){
        $args = array(
            'post_type' => 'atbdp_chat',
            'order' => 'ASC',
            'posts_per_page' => -1,
        );
        $meta_queries = array();
        $meta_queries[] = array(
            'relation' => 'AND',
            array(
                'key' => '_chatAuthor_id',
                'value' =>  !empty($chat_author) ? (int)$chat_author : '',
                'compare' => '='
            ),
            array(
                'key' => '_chatListing_id',
                'value' => !empty($chatListing_id) ? (int)$chatListing_id : '',
                'compare' => '='
            ),
        );
        $count_meta_queries = count($meta_queries);
        if ($count_meta_queries) {
            $args['meta_query'] = ($count_meta_queries > 1) ? array_merge(array('relation' => 'AND'), $meta_queries) : $meta_queries;
        }
        $chats = new WP_Query($args);
        return $chats;
    }
}

if(!function_exists('all_chatted_user_by_listing')){
    function all_chatted_user_by_listing($chatListing_id){
        $args = array(
            'post_type' => 'atbdp_chat',
            'order' => 'ASC',
            'posts_per_page' => -1,
            'meta_key' => '_chatListing_id',
            'meta_value' => !empty($chatListing_id) ? (int)$chatListing_id : '',
            'compare' => '='
        );
        $chats = new WP_Query($args);
        if ($chats->have_posts()){
            $author_ids = array();
            foreach ($chats->get_posts() as $post){
                $author_id =  get_post_field('post_author', $post->ID);
                array_push($author_ids, $author_id);
            }
            return array_unique($author_ids);
        }
    }
}


if(!function_exists('send_email_notification')){
    function send_email_notification($listing_id, $user_id){
    

            $user                   = get_userdata( $user_id );
            $site_name              = get_bloginfo( 'name' );
            $site_url               = get_bloginfo( 'url' );
            $site_email		        = get_bloginfo( 'admin_email' );
            $current_user_email     = $user->user_email;
            $listing_title          = get_the_title( $listing_id );
            $listing_url            = get_permalink( $listing_id );
            $date_format            = get_option( 'date_format' );
            $time_format            = get_option( 'time_format' );
            $current_time           = current_time( 'timestamp' );
            $contact_email_subject  = 'New Chat!';
            $contact_email_body     = __('Dear ==USERNAME==,

            You have received a new chat from ==LISTING_LINK== and is awaiting for your reply. Visit the link to continue the conversation. 

            *This is an automated message please do not reply', 'directorist-live-chat');


        $placeholders = array(
            '==NAME==' => $user->display_name,
            '==USERNAME==' => $user->user_login,
            '==SITE_NAME==' => $site_name,
            '==SITE_LINK==' => sprintf('<a href="%s">%s</a>', $site_url, $site_name),
            '==SITE_URL==' => sprintf('<a href="%s">%s</a>', $site_url, $site_url),
            '==LISTING_TITLE==' => $listing_title,
            '==LISTING_LINK==' => sprintf('<a href="%s">%s</a>', $listing_url, $listing_title),
            '==LISTING_URL==' => sprintf('<a href="%s">%s</a>', $listing_url, $listing_url),
            '==TODAY==' => date_i18n($date_format, $current_time),
            '==NOW==' => date_i18n($date_format . ' ' . $time_format, $current_time)
        );

        $to = $current_user_email;

        $subject = strtr($contact_email_subject, $placeholders);

        $message = strtr($contact_email_body, $placeholders);
        $message = nl2br($message);
        $body = atbdp_email_html($subject, $message);
        $headers = "From: {$site_name} <{$current_user_email}>\r\n";
        $success = ATBDP()->email->send_mail($to, $subject, $body, $headers);
    }
}

function directorist_live_chat_get_template( $template_file, $args = array() ) {
    if ( is_array( $args ) ) {
        extract( $args );
    }

    $theme_template  = '/directorist-live-chat/' . $template_file . '.php';
    $plugin_template = DLC_TEMPLATES_DIR . $template_file . '.php';

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