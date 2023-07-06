<?php
/**
 * @package Directorist Pricing Plans
 */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
// Access the database via SQL
global $wpdb;
include_once("directorist-pricing-plans.php");
$enable_uninstall = get_directorist_option('enable_uninstall',0);
if(!empty($enable_uninstall)) {
    wp_delete_post(get_directorist_option('pricing_plans'), true);

    // Delete posts + data.
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'atbdp_pricing_plans';");

    //Delete all metabox
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id Not IN  (SELECT id FROM {$wpdb->posts})");
}