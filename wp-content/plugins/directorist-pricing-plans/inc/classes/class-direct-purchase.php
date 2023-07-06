<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.7.4
 * @package Directorist
 */
if (!class_exists('Directorist_Direct_Purchase')) :

    class Directorist_Direct_Purchase
    {
        public function __construct()
        {
           add_filter('directorist_checkout_guard', array($this, 'directorist_checkout_guard'));
           
        }

       public function directorist_checkout_guard( $guard ){

        if( directorist_direct_purchase() ){
            $guard = false;
        }
        return $guard;
       }

    }
endif;