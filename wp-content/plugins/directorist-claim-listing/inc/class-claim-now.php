<?php
/**
 * Adds DCL_Claim_Now widget.
 */
class DCL_Claim_Now extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        $widget_options = array(
            'classname' => 'atbd_widget',
            'description' => __('You can show Claim Now button on the sidebar of every single listing ( listing details page ) by this widget ', 'directorist-claim-listing'),
        );
        parent::__construct(
            'dcl_widget', // Base ID, must be unique
            __( 'Directorist - Claim Listing', 'directorist-claim-listing' ), // Name
            $widget_options // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        global $post;
        $listing_id = $post->ID;
        if (!get_directorist_option('enable_claim_listing',1)) return; // vail if the business hour is not enabled
        if (get_directorist_option('non_widger_claim_button',0)) return; // vail if the business hour is not enabled
        $claim_header = get_directorist_option('claim_widget_title',esc_html__('Is this your business?', 'directorist-claim-listing'));
        $claim_description = get_directorist_option('claim_widget_description',esc_html__('Claim listing is the best way to manage and protect your business.', 'directorist-claim-listing'));
        $claim_now = get_directorist_option('claim_now',esc_html__('Claim Now!', 'directorist-claim-listing'));
        $claimed_by_admin = get_post_meta($listing_id, '_claimed_by_admin',true);
        $claim_fee = get_post_meta($listing_id, '_claim_fee',true);
        if ($claimed_by_admin || ('claim_approved' === $claim_fee))return;
        if( is_singular(ATBDP_POST_TYPE)) {
            $title = !empty($instance['title']) ? esc_html($instance['title']) : esc_html__('Title', 'directorist-claim-listing');
            echo $args['before_widget'];
            echo '<div class="atbd_widget_title">';
            echo $args['before_title'] . esc_html(apply_filters('widget_submit_item_title', $title)) . $args['after_title'];
            echo '</div>';
            $claim_class = apply_filters('atbdp_claim_data_target_id', 'directorist-claim-listing-modal');
            ?>
            <div class="directorist-claim-listing-widget">
                <?php if (is_user_logged_in()) { ?>
                    <div class="directorist-claim-listing">
                        <h4 class="directorist-claim-listing__title"><?php _e("$claim_header", 'directorist-claim-listing')?></h4>
                        <p class="directorist-claim-listing__description"><?php _e("$claim_description", 'directorist-claim-listing')?></p>
                        <a href="#" class=" directorist-btn directorist-btn-primary directorist-btn-modal directorist-btn-modal-js directorist-btn-white" data-directorist_target="directorist-claim-listing-modal">
                            <i class="<?php atbdp_icon_type(true);?>-check-square-o"></i>&nbsp; <?php _e("$claim_now", 'directorist-claim-listing')?>
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="directorist-claim-listing">
                        <h3 class="directorist-claim-listing__title"><?php _e("$claim_header", 'directorist-claim-listing')?></h3>
                        <p class="directorist-claim-listing__description"><?php _e("$claim_description", 'directorist-claim-listing')?></p>
                        <a href="#" class="directorist-claim-listing__login-alert directorist-btn directorist-btn-primary directorist-btn-modal directorist-btn-modal-js directorist-btn-white"><?php _e("$claim_now", 'directorist-claim-listing')?></a>
                        <div class="directorist-claim-listing__login-notice directorist_notice directorist-alert directorist-alert-info" role="alert">
                            <span class="fa fa-info-circle" aria-hidden="true"></span>
                            <?php
                            // get the custom registration page id from the db and create a permalink
                            $reg_link_custom = ATBDP_Permalink::get_registration_page_link();
                            //if we have custom registration page, use it, else use the default registration url.
                            $reg_link = !empty($reg_link_custom) ? $reg_link_custom : wp_registration_url();

                            $login_url = apply_filters('atbdp_claim_now_login_link', '<a href="' . ATBDP_Permalink::get_login_page_link() . '">' . __('Login', 'directorist-claim-listing') . '</a>');
                            $register_url = apply_filters('atbdp_claim_now_registration_link', '<a href="' . esc_url($reg_link) . '">' . __('Register', 'directorist-claim-listing') . '</a>');

                            printf(__('You need to %s or %s to claim this listing', 'directorist-claim-listing'), $login_url, $register_url);
                            ?>
                        </div>
                    </div>
                <?php } ?>

                <input type="hidden" id="directorist__post-id" value="<?php echo get_the_ID(); ?>"/>
            </div>
            <?php
            echo $args['after_widget'];
            ?>
            <div class="directorist-modal directorist-modal-js directorist-fade directorist-claim-listing-modal directorist-claimer">
                <div class="directorist-modal__dialog directorist-modal__dialog-lg">
                    <div class="directorist-modal__content">
                        <div class="row align-items-center">
                            <div class="col-lg-12">
                                <form id="directorist-claimer__form" class="directorist-claimer__form">
                                    <div class="directorist-modal__header">
                                        <h3 class="directorist-modal-title" id="directorist-claimer__claim-label"><?php _e('Claim This Listing', 'directorist-claim-listing'); ?></h3>
                                            <a href="#" class="directorist-modal-close directorist-modal-close-js"><span aria-hidden="true">&times;</span></a>
                                    </div>

                                    <div class="directorist-modal__body">
                                        <div class="directorist-form-group">
                                            <label for="directorist-claimer__name" class="directorist-claimer__name"><?php _e('Full Name', 'directorist-claim-listing'); ?>
                                                <span class="directorist-claimer__star-red">*</span></label>
                                            <input type="text" class="directorist-form-element" id="directorist-claimer__name"  placeholder="<?php _e('Full Name', 'directorist-claim-listing'); ?>" required>
                                        </div>
                                        <div class="directorist-form-group">
                                            <label for="directorist-claimer__phone" class="directorist-claimer__phone"><?php _e('Phone', 'directorist-claim-listing'); ?>
                                                <span class="directorist-claimer__star-red">*</span></label>
                                            <input type="tel" class="directorist-form-element" id="directorist-claimer__phone"  placeholder="<?php _e('111-111-235', 'directorist-claim-listing'); ?>" required>
                                        </div>
                                        <div class="directorist-form-group">
                                            <label for="directorist-claimer__details" class="directorist-claimer__details"><?php _e('Verification Details', 'directorist-claim-listing'); ?>
                                                <span class="directorist-claimer__star-red">*</span></label>
                                            <textarea class="directorist-form-element" id="directorist-claimer__details"
                                                      rows="3"
                                                      placeholder="<?php _e('Details description about your business', 'directorist-claim-listing'); ?>..."
                                                      required></textarea>
                                        </div>
                                        <div class="directorist-form-group directorist-pricing-plan">
                                            <?php
                                            $claim_charge_by = get_directorist_option('claim_charge_by');
                                            $charged_by = get_post_meta($listing_id, '_claim_fee', true);
                                            $directory_type = get_post_meta($listing_id, '_directory_type', true);
                                            $charged_by = ($charged_by !== '')?$charged_by:$claim_charge_by;
                                            $has_plans = is_pricing_plans_active();
                                            if (!empty($has_plans) && ('pricing_plan' === $charged_by)){
                                                if (class_exists('ATBDP_Pricing_Plans')){
                                                    $args = array(
                                                        'post_type'      => 'atbdp_pricing_plans',
                                                        'posts_per_page' => -1,
                                                        'status'         => 'publish',
                                                    );

                                                    $metas = [];
                                                    $metas['exclude'] = [
                                                        'relation' => 'OR',
                                                            array(
                                                                'key'       => '_hide_from_plans',
                                                                'compare'   => 'NOT EXISTS',
                                                            ),
                                                            array(
                                                                'key'       => '_hide_from_plans',
                                                                'value'     => 1,
                                                                'compare'   => '!=',
                                                            ),
                                                        ];
                                                    
                                                    if ( ! empty( $directory_type ) ) {
                                                        $metas['directory'] = [
                                                        'key'       => '_assign_to_directory',
                                                        'value'     => $directory_type,
                                                        'compare'   => '=',
                                                        ];
                                                    }

                                                    $args['meta_query'] = array_merge( array('relation' => 'AND'), $metas );

                                                    $atbdp_query = new WP_Query( $args );

                                                    if ($atbdp_query->have_posts()){
                                                        global $post;

                                                        $plans = $atbdp_query->posts;
                                                        printf('<label for="select_plans">%s</label>', __('Select Plan', 'directorist-claim-listing'));
                                                        printf('<select name="claimer_plan" id="directorist-claimer_plan">');
                                                        printf('<option>%s</option>',__('Select Plan', 'directorist-claim-listing'));
                                                        foreach ($plans as $key => $value) {
                                                            $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed',$value->ID);
                                                            $plan_type = get_post_meta($value->ID, 'plan_type', true);
                                                            printf('<option %s value="%s">%s %s</option>', (!empty($active_plan) && ('package' === $plan_type))?'class="directorist__active-plan"':'', $value->ID, $value->post_title, !empty($active_plan)&& ('package' === $plan_type)?'<span class="atbd_badge">'.__('- Active', 'directorist-claim-listing').'</span>':'');
                                                        }
                                                        printf('</select>');

                                                        ?>
                                                        <div id="directorist__plan-allowances" data-author_id="<?php echo get_current_user_id(); ?>">
                                                        </div>
                                                        <?php

                                                        printf('<a target="_blank" href="%s" class="directorist__plans">%s</a>',esc_url(ATBDP_Permalink::get_fee_plan_page_link()), __('Show plan details', 'directorist-claim-listing'));
                                                    }
                                                }else{
                                                    global $product;
                                                    $query_args = array(
                                                        'post_type' => 'product',
                                                        'tax_query' => array(
                                                            array(
                                                                'taxonomy' => 'product_type',
                                                                'field'    => 'slug',
                                                                'terms'    => 'listing_pricing_plans',
                                                            ),
                                                        ),
                                                    );

                                                    $metas = [];
                                                    $metas['exclude'] = [
                                                        'relation' => 'OR',
                                                            array(
                                                                'key'       => '_hide_from_plans',
                                                                'compare'   => 'NOT EXISTS',
                                                            ),
                                                            array(
                                                                'key'       => '_hide_from_plans',
                                                                'value'     => 1,
                                                                'compare'   => '!=',
                                                            ),
                                                        ];
                                                    
                                                    if ( ! empty( $directory_type ) ) {
                                                        $metas['directory'] = [
                                                        'key'       => '_assign_to_directory',
                                                        'value'     => $directory_type,
                                                        'compare'   => '=',
                                                        ];
                                                    }

                                                    $query_args['meta_query'] = array_merge( array('relation' => 'AND'), $metas );

                                                    $atbdp_query = new WP_Query( $query_args );

                                                    if ($atbdp_query->have_posts()){
                                                        global $post;
                                                        $plans = $atbdp_query->posts;
                                                        printf('<label for="select_plans">%s</label>', __('Select Plan', 'directorist-claim-listing'));
                                                        printf('<select name="claimer_plan" id="directorist-claimer_plan">');
                                                        printf('<option>%s</option>',__('Select Plan', 'directorist-claim-listing'));
                                                        foreach ($plans as $key => $value) {
                                                            $active_plan = subscribed_package_or_PPL_plans(get_current_user_id(), 'completed',$value->ID);
                                                            $plan_type = get_post_meta($value->ID, 'plan_type', true);
                                                            printf('<option %s value="%s">%s %s</option>',(!empty($active_plan) && ('package' === $plan_type))?'class="directorist__active-plan"':'', $value->ID, $value->post_title, !empty($active_plan) && ('package' === $plan_type)?'<span class="atbd_badge">'.__('- Active', 'directorist-claim-listing').'</span>':'');
                                                        }
                                                        printf('</select>');
                                                        ?>
                                                        <div id="directorist__plan-allowances" data-author_id="<?php echo get_current_user_id(); ?>">
                                                        </div>
                                                        <?php
                                                        printf('<a target="_blank" href="%s">%s</a>',esc_url(ATBDP_Permalink::get_fee_plan_page_link()), __(' Show plan details', 'directorist-claim-listing'));
                                                    }
                                                }

                                            }
                                            ?>

                                        </div>
                                        <div id="directorist-claimer__submit-notification"></div>
                                        <div id="directorist-claimer__warning-notification"></div>
                                    </div>

                                    <div class="directorist-modal__footer">
                                        <button type="submit" class="directorist-btn directorist-btn-primary"><?php esc_html_e('Submit', 'directorist-claim-listing'); ?></button>
                                        <span><i class="<?php atbdp_icon_type(true);?>-lock"></i><?php esc_html_e('Secure Claim Process', 'directorist-claim-listing'); ?></span>
                                    </div>
                                </form>
                            </div><!-- ends: .col-lg-125 -->
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }}

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return void
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? esc_html($instance['title']) : esc_html__( 'Claim Now', 'directorist-claim-listing' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'directorist-claim-listing' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }

} // class DCL_Claim_Now