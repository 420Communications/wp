<?php
/*
 * Class: Business Directory Multiple Image = ATPP
 * */
if (!class_exists('DLC_Directory_Type_Manager')) :
    class DLC_Directory_Type_Manager
    {
        public function __construct()
        {
            add_filter('atbdp_single_listing_other_fields_widget', array($this, 'atbdp_single_listing_other_fields_widget'));
            add_filter( 'directorist_single_section_template', array( $this, 'directorist_single_section_template' ), 10, 2 );

        }


        public function directorist_single_section_template( $template, $section_data ) {
            if( 'live_chat' === $section_data['widget_name'] ) {
                $load = true;
                if( is_fee_manager_active() ) {
                    $plan_id = get_post_meta( get_the_ID(), '_fm_plans', true );
                    $plan_chat = get_post_meta( $plan_id, '_fm_live_chat', true );
                    if( ! $plan_chat ){ $load = false; }
                }
                if( $load ) {
                    $template .= Directorist_Live_Chat()->load_template('live_chat', [ 'section_data' => $section_data ]);
                }
             }
 
             return $template;

        }


        public function atbdp_single_listing_other_fields_widget($widgets)
        {
            $widgets['live_chat'] = [
                'type' => 'section',
                'label' => 'Live Chat',
                'icon' => 'la la-phone',
                'options' => [
                    'label' => [
                        'type'  => 'text',
                        'label' => 'Label',
                        'value' => 'Chat with Listings Owner',
                    ],
                    'icon' => [
                        'type'  => 'icon',
                        'label' => 'Icon',
                        'value' => 'la la-phone',
                    ],
                    'custom_block_id' => [
                        'type'  => 'text',
                        'label'  => 'Custom block ID',
                        'value' => '',
                    ],
                    'custom_block_classes' => [
                        'type'  => 'text',
                        'label'  => 'Custom block Classes',
                        'value' => '',
                    ],
                ],
            ];
            return $widgets;
        }
      

    }
endif;