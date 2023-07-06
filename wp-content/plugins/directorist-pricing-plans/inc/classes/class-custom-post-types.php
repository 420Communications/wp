<?php
defined('ABSPATH') || die('Direct access is not allowed.');
/**
 * @since 1.7.4
 * @package Directorist
 */
if (!class_exists('ATPP_CPTs')) :

    class ATPP_CPTs
    {
        public function __construct()
        {
            add_action('init', array($this, 'register_custom_post_type_for_FM'));
            add_filter('manage_atbdp_pricing_plans_posts_columns', array($this, 'atpp_add_new_plan_columns'));
            add_action('manage_atbdp_pricing_plans_posts_custom_column', array($this, 'atpp_custom_field_column_content'), 10, 2);
            add_filter('post_row_actions', array($this, 'atpp_remove_row_actions_for_quick_view'), 10, 2);
            add_filter('atbdp_add_new_order_column', array($this, 'atbdp_add_new_order_column'));
            add_action('atbdp_custom_order_column_content', array($this, 'atbdp_custom_order_column_content'), 10, 3);
            add_filter('atbdp_add_new_listing_column', array($this, 'atpp_add_new_listing_column'));
            add_filter('atbdp_add_new_listing_column_content', array($this, 'atpp_add_new_listing_column_content'), 10, 2);

            add_filter( 'directorist_before_featured_expire_metabox', array( $this, 'directorist_before_featured_expire_metabox'),10, 2 );

            
        }

        /**
         * @return bollean
         * @param $post     Edited post
         */

        public function directorist_before_featured_expire_metabox( $load, $post ){

            $plan_id = get_post_meta( $post->ID, '_fm_plans', true );
            
            if( !empty( $plan_id ) && is_numeric( $plan_id ) ){
                $load = false;
            }                

            return $load;
        }


        public function atpp_add_new_listing_column_content($column = null, $listing_id = null)
        {
            switch ($column) {
                case 'active_plan' :
                    $user_id = get_post_field('post_author', $listing_id);
                    $selected_plan_id = get_post_meta($listing_id, '_fm_plans', true);
                    $plans_by_admin = get_post_meta($listing_id, '_fm_plans_by_admin', true);
                    $order_id = get_post_meta($listing_id, '_plan_order_id', true);
                    $is_active = subscribed_package_or_PPL_plans( $user_id, 'completed', $selected_plan_id, $listing_id );
                    $listing_plan_details = package_or_PPL_with_listing($user_id, 'completed', $listing_id);
                    $plan_name = !empty($selected_plan_id) ? get_the_title($selected_plan_id) : __('No Plan!', 'directorist-pricing-plans');
                    $active = '';
                    $plan_type = package_or_PPL($selected_plan_id);
                    
                    if (package_or_PPL($selected_plan_id) == 'package') {
                        if ( !empty( $plans_by_admin )  || ( $is_active ) ) {
                            $plan_type = __('Package', 'directorist-pricing-plans');
                            $active = 'green';
                        }
                    }
                    if (package_or_PPL($selected_plan_id) == 'pay_per_listng') {
                        $plan_type = __('Pay Per Listing', 'directorist-pricing-plans');
                        if ($listing_plan_details || !empty($plans_by_admin)) {
                            $active = 'green';
                        }
                    }
                    ?>
                    <span style='color: <?php echo !empty($active) ? $active : 'red' ?>;'><?php echo !empty($plan_name) ? $plan_name : ''; ?></span>
                    <span><?php echo(!empty($selected_plan_id) ? ' - ' : '');
                        echo ucfirst($plan_type); ?></span>
                    <?php
                    //subscribed_package_or_PPL_plans($user_id, $order_status, $plan_id);
                    break;
            }
        }
        
        public function atpp_add_new_listing_column($column_name)
        {
            $column_name['active_plan'] = __('Plan', 'directorist-pricing-plans');
            return $column_name;
        }

        public function atbdp_custom_order_column_content($column, $post_id, $listing_id)
        {
            switch ($column) {

                case 'active_plan' :
                    $value = get_post_meta($post_id, '_payment_status', true);
                    $status = get_post_meta($post_id, '_order_status', true);
                    $gateway = get_post_meta($post_id, '_payment_gateway', true);
                    $plan_id = get_post_meta($post_id, '_fm_plan_ordered', true);
                    $plan_name = $plan_id ? get_the_title($plan_id) . ' - ' : __( 'No plan!', 'directorist-pricing-plans' );
                    $valid = ( ( ( 'free' === $gateway ) || ( 'completed' === $value ) ) && ( $status !== 'exit' ) ) ? true : false;
                    $status_text = ( $status === 'exit' ) ? __('Limit Exceeded', 'directorist-pricing-plans') : ( $valid ? __('Active', 'directorist-pricing-plans') : __('Inactive', 'directorist-pricing-plans') );
                    //is the payment type is free submission then activate the plan
                    if ( $valid ) { 
                        echo esc_attr( $plan_name );
                        ?>
                        <span style="color: green"><?php echo esc_attr( $status_text ); ?></span>
                        <?php
                    }else { 
                        echo esc_attr( $plan_name );
                        ?>
                        <span style="color: red"><?php echo esc_attr( $status_text ); ?></span>
                        <?php     
                    }
                    break;
            }
        }

        
        public function atbdp_add_new_order_column($column)
        {
            $column['active_plan']  = __('Plan', 'directorist-pricing-plans');
            return $column;
        }

         /**
         * Remove quick edit.
         *
         * @param array $actions An array of row action links.
         * @param WP_Post $post The post object.
         * @return     array      $actions    Updated array of row action links.
         * @since     1.0.0
         * @access   public
         *
         */
        public function atpp_remove_row_actions_for_quick_view($actions, $post)
        {

            global $current_screen;

            if ($current_screen->post_type != 'atbdp_pricing_plans') return $actions;

            unset($actions['view']);
            unset($actions['inline hide-if-no-js']);

            return $actions;

        }

                /**
         * Retrieve the table columns.
         *
         * @param array $column all the column
         * @param array $post_id post id
         * @since    1.0.0
         * @access   public
         */

        public function atpp_custom_field_column_content($column, $post_id)
        {
            $type = esc_attr(get_post_meta($post_id, 'plan_type', true));
            $featured = esc_attr(get_post_meta($post_id, 'is_featured_listing', true));
            echo '</select>';
            switch ($column) {
                // case 'directory_type' :
                //     echo 'Test';
                //     break;
                case 'price' :
                    $value = esc_attr(atpp_total_price($post_id));
                    $value2 = esc_attr(get_post_meta($post_id, 'price_decimal', true));
                    echo atbdp_get_payment_currency() . ' ' . $value;
                    if ($value2) echo '.' . $value2;
                    break;
                case 'length' :
                    $value      = esc_attr(get_post_meta($post_id, 'fm_length', true));
                    $unlimited  = esc_attr(get_post_meta($post_id, 'fm_length_unl', true));
                    $period     = esc_attr(get_post_meta($post_id, '_recurrence_period_term', true));
                    $pluran     = 1 < $value ? 's' : '';
                    $langth     = !empty( $unlimited ) ? 'Unlimited days' : $value .' '. $period . $pluran;
                    echo $langth;
                    break;
                case 'num_listing' :
                    $num_regular = esc_attr(get_post_meta($post_id, 'num_regular', true));
                    $num_featured = esc_attr(get_post_meta($post_id, 'num_featured', true));
                    $num_regular_unl = esc_attr(get_post_meta($post_id, 'num_regular_unl', true));
                    $num_featured_unl = esc_attr(get_post_meta($post_id, 'num_featured_unl', true));
                    $reg = !empty( $num_regular_unl ) ? ' Unlimited' : $num_regular;
                    $fea = !empty( $num_featured_unl ) ? ' Unlimited' : $num_featured;
                    $featured = !empty( $featured ) ? '1 featured lisitng' : '1 regular listing';
                    if( 'package' === $type ){
                        echo 'Regular-' . $reg . '<br>';
                        echo 'Featured-' . $fea . '<br>';
                    }else{
                        echo $featured;
                    }
               
                    break;
                case 'price_range' :
                    $value = esc_attr(get_post_meta($post_id, 'price_range', true));
                    echo 'Until-' . atbdp_get_payment_currency() . ' ' . $value;
                    break;
                case 'plan_type' :
                    echo ('package' === $type) ? __('Package', 'directorist-pricing-plans') : __('Pay Per Listing', 'directorist-pricing-plans');
                    break;
                case 'directory_type':
                    $term_id = get_post_meta( $post_id, '_assign_to_directory', true );
                    $term = get_term_by( is_numeric($term_id) ? 'id' : 'slug', $term_id, ATBDP_TYPE );
                    $config = get_term_meta( $term_id, 'general_config', true );
                    $icon   = is_array( $config ) ? $config['icon'] : '';
                    $term_name = !empty( $term  ) ? $term->name : '';
                    if( !empty( $icon ) ) { ?>
                    <span class="<?php echo esc_html( $icon );?>"></span>
                    <?php } ?>
                    <span><?php echo esc_attr( $term_name ); ?></span>
                    <?php
                    break;
                        

            }
        }

                /**
         * Register a custom post type "atbdp_pricing_plans".
         *
         * @since    3.1.0
         * @access   public
         */
        public function register_custom_post_type_for_FM()
        {

            $labels = array(
                'name' => _x('Pricing Plans', 'Post Type General Name', 'directorist-pricing-plans'),
                'singular_name' => _x('Pricing Plans', 'Post Type Singular Name', 'directorist-pricing-plans'),
                'menu_name' => __('Pricing Plans', 'directorist-pricing-plans'),
                'name_admin_bar' => __('Pricing Plan', 'directorist-pricing-plans'),
                'all_items' => __('Pricing Plans', 'directorist-pricing-plans'),
                'add_new_item' => __('Add New Plan', 'directorist-pricing-plans'),
                'add_new' => __('Add New Plan', 'directorist-pricing-plans'),
                'new_item' => __('New Plan', 'directorist-pricing-plans'),
                'edit_item' => __('Edit Plan', 'directorist-pricing-plans'),
                'update_item' => __('Update Plan', 'directorist-pricing-plans'),
                'view_item' => __('View Plan', 'directorist-pricing-plans'),
                'search_items' => __('Search Plan', 'directorist-pricing-plans'),
                'not_found' => __('No Plan found', 'directorist-pricing-plans'),
                'not_found_in_trash' => __('No Plan found in Trash', 'directorist-pricing-plans'),
            );

            $args = array(
                'labels' => $labels,
                'description' => __('This order post type will keep track of admin fee plans', 'directorist-pricing-plans'),
                'supports' => array('title'),
                'taxonomies' => array(''),
                'hierarchical' => false,
                'public' => true,
                'show_ui' => current_user_can('manage_atbdp_options') ? true : false, // show the menu only to the admin
                'show_in_menu' => current_user_can('manage_atbdp_options') ? 'edit.php?post_type=' . ATBDP_POST_TYPE : false,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => true,
                'exclude_from_search' => true,
                'publicly_queryable' => true,
                'capability_type' => 'at_biz_dir',
                'map_meta_cap' => true,
            );

            register_post_type('atbdp_pricing_plans', $args);

        }

                /**
         * Retrieve the table columns.
         *
         * @param array $columns
         *
         * @return   array    $columns    Array of all the list table columns.
         * @since    1.0.0
         * @access   public
         */
        public function atpp_add_new_plan_columns($columns)
        {
            $enable_multi_directory = get_directorist_option( 'enable_multi_directory', false );

            $columns = array(
                'cb'             => '<input type="checkbox" />',                         // Render a checkbox instead of text
                'title'          => __('Title', 'directorist-pricing-plans'),
                'directory_type' => __('Directory Type', 'directorist-pricing-plans'),
                // 'directory_type' => __('Directory Type', 'directorist-pricing-plans'),
                'price'       => __('Price', 'directorist-pricing-plans'),
                'length'      => __('Length', 'directorist-pricing-plans'),
                'num_listing' => __('Listings', 'directorist-pricing-plans'),
                'plan_type'   => __('Plan Type', 'directorist-pricing-plans'),
                'date'        => __('Date', 'directorist-pricing-plans')

            );

            if ( !atbdp_is_truthy( $enable_multi_directory ) ) {
                unset( $columns['directory_type'] );
            }

            return $columns;

        }

    }
endif;