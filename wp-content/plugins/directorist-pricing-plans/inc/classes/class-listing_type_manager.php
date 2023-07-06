<?php
if (!class_exists('ATPP_Post_Type_Manager')) :
    class ATPP_Post_Type_Manager
    {
        public $plans = [];
        public $max_char = 0;
        public function __construct()
        {
            add_filter('atbdp_form_preset_widgets', array($this, 'atbdp_form_builder_widgets'));
            add_filter('atbdp_add_listing_page_template', array( $this, 'plans_page' ), 10, 2 );
            add_filter( 'directorist_form_field_data', array( $this, 'directorist_form_field_data' ) );
            add_filter( 'directorist_field_template', array( $this, 'directorist_field_template' ), 10, 2 );
            add_filter( 'directorist_submission_field_module', array( $this, 'submission_field_module' ), 10, 2 );
            add_filter( 'directorist_section_template', array( $this, 'directorist_section_template' ), 10, 2 );
            add_filter( 'directorist_single_item_template', array( $this, 'directorist_single_item_template' ), 10, 2 );
            add_filter( 'directorist_single_section_template', array( $this, 'directorist_single_section_template' ), 10, 2 );
            add_filter( 'atbdp_add_listing_form_validation_logic', array( $this, 'atbdp_add_listing_form_validation_logic' ), 10, 3 );

            if( is_admin() ) {
                add_action( 'add_meta_boxes_' . ATBDP_POST_TYPE, array( $this, 'atpp_admin_metabox' ) );
            }

            if( ! get_option('directory_type_update_in_plan') ){
                add_action( 'template_redirect', array( $this, 'directorist_bulk_plan_update' ) );
            }
        }

        public function atbdp_form_builder_widgets($widgets)
        {
            $widgets['listing_type'] = [
                'label' => 'Listing Type',
                'icon' => 'la la-toggle-on',
                'show' => true,
                'options' => [
                    'type' => [
                        'type'  => 'hidden',
                        'value' => 'radio',
                    ],
                    'field_key' => [
                        'type'  => 'hidden',
                        'value' => 'listing_type',
                    ],
                    'label' => [
                        'type'  => 'text',
                        'label' => 'Label',
                        'value' => 'Select Listing Type',
                    ],
                    'general_label' => [
                        'type'  => 'text',
                        'label' => 'General label',
                        'value' => 'General',
                    ],
                    'featured_label' => [
                        'type'  => 'text',
                        'label' => 'Featured label',
                        'value' => 'Featured',
                    ],
                    'required' => [
                        'type'  => 'toggle',
                        'label'  => 'Required',
                        'value' => true,
                    ],
                ],
            ];
            return $widgets;
        }

        public function directorist_bulk_plan_update(){

            if( '7.0' > ATBDP_VERSION  ) return;

            $term = get_term_by( 'slug', 'need-listings', ATBDP_TYPE );
            $need_term = !empty( $term->term_id ) ? $term->term_id : default_directory_type();

            $args = array(
                'post_type' => 'atbdp_pricing_plans',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'fields'      => 'ids',
            );

            $atbdp_query = new WP_Query($args);
            if( $atbdp_query->have_posts() ){
                foreach( $atbdp_query->posts as $post_id ){
                    $need = get_post_meta( $post_id, '_need_post', true );
                    if( $need === 'yes' ){
                        update_post_meta( $post_id, '_assign_to_directory', $need_term );
                    }else{
                        update_post_meta( $post_id, '_assign_to_directory', default_directory_type() );
                    }


                    update_post_meta( $post_id, '_listing_title', 1 );
                    update_post_meta( $post_id, '_listing_content', 1 );
                    update_post_meta( $post_id, '_map', 1 );
                    update_post_meta( $post_id, '_zip', 1 );
                    update_post_meta( $post_id, '_fax', 1 );
                    update_post_meta( $post_id, '_address', 1 );

                    //pricing
                    if( get_post_meta( $post_id, 'fm_allow_price', true ) ){
                        update_post_meta( $post_id, '_pricing', 1 );
                    }
                    $price_range = get_post_meta( $post_id, 'price_range', true );
                    if( $price_range ){
                        update_post_meta( $post_id, '_max_pricing', $price_range );
                    }

                    // slider
                    if( get_post_meta( $post_id, 'fm_allow_slider', true ) ){
                        update_post_meta( $post_id, '_listing_img', 1 );
                    }
                    $num_image = get_post_meta( $post_id, 'num_image', true );
                    if( $num_image ){
                        update_post_meta( $post_id, '_max_listing_img', $num_image );
                    }

                    // tag
                    if( get_post_meta( $post_id, 'fm_allow_tag', true ) ){
                        update_post_meta( $post_id, '_tag', 1 );
                    }
                    $fm_tag_limit = get_post_meta( $post_id, 'fm_tag_limit', true );
                    if( $fm_tag_limit ){
                        update_post_meta( $post_id, '_max_tag', $fm_tag_limit );
                    }

                    // category
                    if( get_post_meta( $post_id, 'fm_allow_category', true ) ){
                        update_post_meta( $post_id, '_category', 1 );
                    }
                    $fm_category_limit = get_post_meta( $post_id, 'fm_category_limit', true );
                    if( $fm_category_limit ){
                        update_post_meta( $post_id, '_max_category', $fm_category_limit );
                    }

                    // video
                    if( get_post_meta( $post_id, 'l_video', true ) ){
                        update_post_meta( $post_id, '_videourl', 1 );
                    }
                    if( get_post_meta( $post_id, 'hide_video', true ) ){
                        update_post_meta( $post_id, '_hide_videourl', 1 );
                    }

                    // email
                    if( get_post_meta( $post_id, 'fm_email', true ) ){
                        update_post_meta( $post_id, '_email', 1 );
                    }
                    if( get_post_meta( $post_id, 'hide_email', true ) ){
                        update_post_meta( $post_id, '_hide_email', 1 );
                    }

                    // phone
                    if( get_post_meta( $post_id, 'fm_phone', true ) ){
                        update_post_meta( $post_id, '_phone', 1 );
                        update_post_meta( $post_id, '_phone2', 1 );
                    }
                    if( get_post_meta( $post_id, 'hide_phone', true ) ){
                        update_post_meta( $post_id, '_hide_phone', 1 );
                        update_post_meta( $post_id, '_hide_phone2', 1 );
                    }

                    // website
                    if( get_post_meta( $post_id, 'fm_web_link', true ) ){
                        update_post_meta( $post_id, '_website', 1 );
                    }
                    if( get_post_meta( $post_id, 'hide_webLink', true ) ){
                        update_post_meta( $post_id, '_hide_website', 1 );
                    }

                    // social
                    if( get_post_meta( $post_id, 'fm_social_network', true ) ){
                        update_post_meta( $post_id, '_social', 1 );
                    }
                    if( get_post_meta( $post_id, 'hide_Snetwork', true ) ){
                        update_post_meta( $post_id, '_hide_social', 1 );
                    }


                }
                wp_reset_postdata();
            }
            update_option( 'directory_type_update_in_plan', 1 );
        }

        /**
         * @since 1.3.2
         */

        public function atpp_admin_metabox()
        {
            if (!get_directorist_option('fee_manager_enable', 1)) return; // vail if the business hour is not enabled
            add_meta_box('_listing_admin_plan',
                __('Belongs to Plan', 'directorist-pricing-plans'),
                array($this, 'atpp_admin_plan'),
                ATBDP_POST_TYPE,
                'side', 'high');
        }


        /**
         * @since 1.3.2
         */
        public function atpp_admin_plan($post)
        {
            $current_val = get_post_meta($post->ID, '_fm_plans', true);
            $user_id = $post->post_author;
            $args = array(
                'post_type' => 'atbdp_pricing_plans',
                'posts_per_page' => -1,
                'post_status' => 'publish',
            );

            $atbdp_query = new WP_Query($args);

            if ($atbdp_query->have_posts()) {
                global $post;
                $plans = $atbdp_query->posts; ?>

                <div class="directorist-admin-form-plan-container">
                    <div class="directorist-admin-form-plan-selection">
                        <label for=""><?php echo __('Select Plan', 'directorist-pricing-plans'); ?></label>
                        <div class="directorist-admin-form-plan-selection__content">
                            <select name="admin_plan">
                                <option value="null"><?php echo __('- Select -', 'directorist-pricing-plans'); ?></option>
                                <?php
                                foreach ($plans as $key => $value) {
                                    $class = apply_filters('atbdp_admin_plan_select_option_class', 'listing_plan', $value->ID);
                                    $active_plan = subscribed_package_or_PPL_plans($user_id, 'completed', $value->ID);
                                    $plan_metas = get_post_meta($value->ID);
                                    $plan_type = esc_attr($plan_metas['plan_type'][0]);
                                    $active = '';
                                    if ('package' === $plan_type && $active_plan) {
                                        $active = ' <span class="atbd_badge atbd_badge_open">' . __(' - Active', 'directorist-pricing-plans') . '</span>';
                                    }
                                    ?>
                                    <option class="<?php echo $class.' '.$plan_type ?>" value="<?php echo $value->ID; ?>" <?php echo selected($value->ID, $current_val); ?>><?php echo $value->post_title.$active; ?></option>
                                    <?php
                                }

                                ?>
                            </select>
                            <span class="directorist_loader"></span>
                        </div>
                    </div>

                    <div id="directorist-allowances"></div>
                    <div id="directorist-claim-submit-notification"></div>
                    <div id="directorist-claim-warning-notification"></div>
                    <div class="directorist-admin-form-plan-action">
                        <a target="_blank" href="<?php echo esc_url(ATBDP_Permalink::get_fee_plan_page_link()); ?>" class="directorist-plans"><?php echo __('Details', 'directorist-pricing-plans'); ?></a>
                        <a href="#" id="confirm_plan" class="button"><?php echo __('Save', 'directorist-pricing-plans'); ?></a>
                    </div>
                </div>
                <?php
            }
        }

        public function directorist_form_field_data( $field_data ) {
            $listing_id = ! empty( get_query_var('atbdp_listing_id' ) ) ? get_query_var('atbdp_listing_id') : '';
            $assigned_plan = get_post_meta( $listing_id, '_fm_plans', true );
            $plan_id = !empty( $_GET['plan'] ) ? sanitize_text_field( $_GET['plan'] ) : $assigned_plan;
            $field_key = ! empty( $field_data['field_key'] ) ? $field_data['field_key'] : '';
            if( 'tax_input[at_biz_dir-location][]'  == $field_key ) { $field_key = 'location'; }
            if( 'admin_category_select[]'           == $field_key ) { $field_key = 'category'; }
            if( 'tax_input[at_biz_dir-tags][]'      == $field_key ) { $field_key = 'tag'; }
            if( 'pricing' == $field_data['widget_name'] ) {   $field_key  = 'pricing'; }
            $max_field_value = get_post_meta( $plan_id, '_max_'. $field_key, true );
            $unlimited = get_post_meta( $plan_id, '_unlimited_'. $field_key, true );
            if( $max_field_value && ! $unlimited ) {
                $this->max_char = $max_field_value;
                if( 'listing_content' === $field_key ){
                    ?>
                    <input type="hidden" id="directorist_listing_content_max" value="<?php echo esc_attr( $max_field_value ); ?>">
                    <?php                }
                if( 'location' === $field_key ){
                    $field_data['max_location_creation'] = $max_field_value;
                }else{
                    $field_data['max'] = $max_field_value;
                }

            }
            if( $unlimited ){
                $field_data['unlimited'] = true;
            }
            return $field_data;
        }

        public function tiny_mce_before_init( $initArray ) {

            if ( ! is_admin() ) {   
                $initArray['setup'] = <<<JS
            [function(ed) {
                ed.on('keypress', function(e) {
                    if ( ed.id == "listing_content" ) {
                        var content = ed.getContent().replace(/(<[a-zA-Z\/][^<>]*>|\[([^\]]+)\])|(\s+)/ig,''); 
                        var max = `$this->max_char`;
                        var len = content.length;
                
                        var diff = max - len; 
                        if ( diff === 0 ) {
                            tinymce.dom.Event.cancel(e);
                        }    
                        document.getElementById("directorist_listing_description_indicator").innerHTML = "Characters Left: " + diff;    
                    }
                });
            
            }][0]
JS;
              }
                return $initArray;
        }

        public function atbdp_add_listing_form_validation_logic( $default_logic, $field_data, $info ) {
            $plan_id = !empty( $info['plan_id'] ) ? sanitize_text_field( $info['plan_id'] ) : '';
            $field_key = !empty( $field_data['field_key'] ) ? $field_data['field_key'] : '';
            $listing_id = !empty( $info['listing_id'] ) ? $info['listing_id'] : '';
            if( !empty( $listing_id ) && ( 'listing_type' === $field_key ) ) return false;

            $plan_id = !empty( $info['plan_id'] ) ? sanitize_text_field( $info['plan_id'] ) : '';
            $field_key = $field_data['field_key'];
            if( 'tax_input[at_biz_dir-location][]'  == $field_key ) {  $field_key = 'location'; }
            if( 'admin_category_select[]'           == $field_key ) {  $field_key = 'category';  }
            if( 'tax_input[at_biz_dir-tags][]'      == $field_key ) { $field_key = 'tag'; }
            if( 'pricing' == $field_data['widget_name'] ) {   $field_key  = 'pricing'; }
            $field = get_post_meta( $plan_id, '_'. $field_key, true );

            if( empty( $field ) ) {
                return false;
            }
            return $default_logic;
        }

        public function directorist_single_item_template( $template, $args ) {

          
            $widget_name = !empty( $args['widget_name'] ) ? $args['widget_name'] : '';
            $plan_id = get_post_meta( get_the_ID(), '_fm_plans', true );
        
            switch( $widget_name ){
                case 'reviews':
                case 'ratings_count':
                    $plan_review = get_post_meta( $plan_id, 'fm_cs_review', true );
                    if( ! $plan_review ){
                        $template = '';
                    }
                    break;
                case 'contact_listings_owner':
                    $plan_contact = get_post_meta( $plan_id, 'cf_owner', true );
                    if( ! $plan_contact ){
                        $template = '';
                    }
                    break;
            }
            
            return $template;

        }

        public function directorist_single_section_template( $template, $args ) {

            $widget_name = !empty( $args['widget_name'] ) ? $args['widget_name'] : '';
            $plan_id = get_post_meta( get_the_ID(), '_fm_plans', true );
        

            switch( $widget_name ){
                case 'review':
                    $plan_review = get_post_meta( $plan_id, 'fm_cs_review', true );
                    if( ! $plan_review ){
                        $template = '';
                    }
                    break;
                case 'contact_listings_owner':
                    $plan_contact = get_post_meta( $plan_id, 'cf_owner', true );
                    if( ! $plan_contact ){
                        $template = '';
                    }
                    break;
            }
            
            return $template;

        }

        public function directorist_section_template( $statue, $args ) {

            if( is_admin() ) return $statue;
            $section_data = $args['section_data'];
            $form = directorist_legacy_mode() ? $args['form'] : $args['listing_form'];
            $listing_id = $form->get_add_listing_id();
            $assigned_plan = get_post_meta( $listing_id, '_fm_plans', true );
            $plan_id = !empty( $_GET['plan'] ) ? sanitize_text_field( $_GET['plan'] ) : $assigned_plan;
            $load_section = false;
            foreach( $section_data['fields'] as $field_data ) {
                $field_key = ( isset( $field_data['field_key'] ) ) ? $field_data['field_key'] : '';
                if( 'tax_input[at_biz_dir-location][]'  == $field_key ) { $field_key = 'location'; }
                if( 'admin_category_select[]'           == $field_key ) { $field_key = 'category'; }
                if( 'tax_input[at_biz_dir-tags][]'      == $field_key ) { $field_key = 'tag'; }
                if( 'pricing' == $field_data['widget_name'] ) {   $field_key  = 'pricing'; }
                $field = get_post_meta( $plan_id, '_'. $field_key, true );
                if( apply_filters( 'directorist_plan_allowed_field', $field, $field_data, $plan_id ) ) {
                    $load_section = true;
                }
            }

            if( $load_section ) {
                return $statue;
            }

        }

        public function directorist_field_template( $template, $field_data ) {

            if( is_admin() ) return $template;

            $form = $field_data['form'];
            $listing_id = $form->add_listing_id;
            $db_plan = get_post_meta( $listing_id, '_fm_plans', true );
            $plan_id = !empty( $_GET['plan'] ) ? sanitize_text_field( $_GET['plan'] ) : $db_plan;

            $field_key = ! empty( $field_data['field_key'] ) ? $field_data['field_key'] : '';

            if( 'tax_input[at_biz_dir-location][]'  == $field_key ) { $field_key = 'location'; }
            if( 'admin_category_select[]'           == $field_key ) { $field_key = 'category'; }
            if( 'tax_input[at_biz_dir-tags][]'      == $field_key ) { $field_key = 'tag'; }

            if( 'pricing' == $field_data['widget_name'] ) { $field_key  = 'pricing'; }

            $field = get_post_meta( $plan_id, '_'. $field_key, true );
            if ( 'listing_type' === $field_data['widget_name'] && $plan_id && isset( $_GET['plan'] ) ) {
                $template .= ATBDP_Pricing_Plans()->load_template('listing_type', array('field_data' => $field_data));
            }

            
            if ( $field && ( '0' != $field ) ) {

                return $template;
            }

        }

      // submission_field_module
      public function submission_field_module( $content, $args = [] ) {

        if ( is_admin() ) return $content;

        $form = $args['data']['form'];
        $listing_id = $form->add_listing_id;
        $db_plan = get_post_meta( $listing_id, '_fm_plans', true );
        $plan_id   = ! empty( $_GET['plan'] ) ? sanitize_text_field( $_GET['plan'] ) : $db_plan;
        $field_key = ( ! empty( $args['module'] ) ) ? $args['module'] : '';
        $field     = get_post_meta( $plan_id, '_'. $field_key, true );

        if ( '0' == $field ) { return ''; }
        
        return $content;
    }

        public function plans_page( $template, $data)
        {
            $listing_type_count = count( $data['listing_form']->get_listing_types() );
            $listing_id = $data['listing_form']->get_add_listing_id();
            $is_edit_mode = ! empty( $data['is_edit_mode'] ) ? $data['is_edit_mode'] : '';
            $plan         = get_post_meta( $listing_id, '_fm_plans', true );


            if( $is_edit_mode && ! $plan ){
                ob_start();

                ATBDP_Pricing_Plans()->load_template('no-plan-assigned', array( 'listing_id' => $listing_id ) );

                return ob_get_clean();
            }

            if( ( isset( $_GET['directory_type'] ) || ( $listing_type_count < 2 ) ) && ( !isset( $_GET['plan'] ) ) ) {
                ob_start();

                do_action( 'directorist_before_pricing_plan_page', $data );

                ATBDP_Pricing_Plans()->load_template('fee-plans', array('data' => $data));
                return ob_get_clean();
            }else {
                return $template;
            }

        }

    }
endif;