<?php
if (!class_exists('BDRR_Widget_Tempate')) {
    /**
     * Adds BDRR_Widget_Tempate widget.
     */
    class BDB_Widget_Template extends WP_Widget
    {

        /**
         * Register widget with WordPress.
         */
        function __construct()
        {

            $widget_options = array(
                'classname' => 'bdb_widget',
                'description' => esc_html__('You can show booking system by this widget', 'directorist-booking'),
            );
            parent::__construct(
                'bdb_widget', // Base ID
                esc_html__('Directorist - Booking', 'directorist-booking'), // Name
                $widget_options // Args
            );
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget($args, $instance)
        {
            // Stop if pricing plan dosen't allows booking
            // Check Restriction
            $restricted = atbdp_check_booking_restriction( get_the_ID() );
            if ( $restricted ) { return; }

            $title = ! empty($instance['title']) ? esc_html($instance['title']) : esc_html__('Directorist Booking', 'directorist-booking');
            if ( is_singular( ATBDP_POST_TYPE ) ) {
                $hide_booking                 = get_post_meta( get_the_ID(), '_bdb_hide_booking', true );
                $bdb_value                    = get_post_meta( get_the_ID(), '_bdb', true );
                $event_ticket                 = get_post_meta( get_the_ID(), '_bdb_event_ticket', true );
                if ( empty( $hide_booking ) && ( ! empty( $bdb_value ) || ! empty( $event_ticket ) ) ) {
                    include BDB_TEMPLATES_DIR . "widget-template.php";
                }
            }
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         * @return void
         */
        public function form($instance)
        {
            $title              = !empty($instance['title']) ? esc_html($instance['title']) : esc_html__('Directorist Booking', 'directorist-booking');
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'directorist-booking'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                       value="<?php echo esc_attr($title); ?>">
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
        public function update($new_instance, $old_instance)
        {
            $instance                       = array();
            $instance['title']              = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
            return $instance;
        }

    } // class BDRR_Widget_Tempate


}
