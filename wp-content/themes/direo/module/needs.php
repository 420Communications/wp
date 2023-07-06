<?php
/*==========================================
    Element Name: All Needs
    Author URI: https://wpwax.com
============================================*/

if (function_exists('kc_add_map')) {
    kc_add_map(
        array(
            'needs' => array(
                'name' => esc_html__('Needs', 'direo'),
                'description' => esc_html__('Display your all needs.', 'direo'),
                'icon' => 'fa fa-question-circle',
                'category' => 'Need',
                'priority' => 110,
                'params' => array(
                    'general' => array(
                        array(
                            'name' => 'avatar',
                            'label' => esc_html__('Show Author Avatar?', 'direo'),
                            'value' => 'yes',
                            'type' => 'toggle',
                        ),
                        array(
                            'name' => 'category',
                            'label' => esc_html__('Show Category?', 'direo'),
                            'value' => 'yes',
                            'type' => 'toggle',
                        ),
                        array(
                            'name' => 'budget',
                            'label' => esc_html__('Show Budget Amount?', 'direo'),
                            'value' => 'yes',
                            'type' => 'toggle',
                        ),
                        array(
                            'name' => 'columns',
                            'label' => esc_html__('Needs Per Row', 'direo'),
                            'type' => 'select',
                            'value' => '3',
                            'options' => array(
                                '5' => esc_html__('5 Items / Row', 'direo'),
                                '4' => esc_html__('4 Items / Row', 'direo'),
                                '3' => esc_html__('3 Items / Row', 'direo'),
                                '2' => esc_html__('2 Items / Row', 'direo'),
                            ),
                            'admin_label' => true,
                        ),
                        array(
                            'name' => 'order',
                            'type' => 'dropdown',
                            'label' => esc_html__('Order by', 'direo'),
                            'admin_label' => true,
                            'value' => 'date',
                            'options' => array(
                                'title' => esc_html__('Title', 'direo'),
                                'date' => esc_html__('Date', 'direo'),
                                'price' => esc_html__('Price', 'direo'),
                            ),
                        ),
                        array(
                            'name' => 'order_list',
                            'type' => 'dropdown',
                            'label' => esc_html__('Needs Order', 'direo'),
                            'admin_label' => true,
                            'options' => array(
                                'asc' => esc_html__('ASC', 'direo'),
                                'desc' => esc_html__('DESC', 'direo'),
                            ),
                            'value' => 'desc',
                        ),
                        array(
                            'name' => 'number',
                            'type' => 'number_slider',
                            'label' => esc_html__('Needs Per Page', 'direo'),
                            'description' => esc_html__('The number of needs you want to show. Set -1 for all needs', 'direo'),
                            'value' => '3',
                            'admin_label' => true,
                            'options' => array(
                                'min' => -1,
                                'max' => 1000
                            )
                        ),
                        array(
                            'name' => 'pagination',
                            'label' => esc_html__('Show Pagination', 'direo'),
                            'type' => 'toggle',
                            'value' => 'yes',
                        ),
                    ),
                    'styling' => array(
                        array(
                            'name' => 'css_custom',
                            'type' => 'css',
                            'options' => array(
                                array(
                                    "screens" => "any,1024,999,767,479",
                                    'Box' => array(
                                        array('property' => 'margin', 'label' => 'Margin'),
                                        array('property' => 'padding', 'label' => 'Padding'),
                                    ),
                                )
                            )
                        )
                    ),
                    'animate' => array(
                        array(
                            'name' => 'animate',
                            'type' => 'animate'
                        )
                    ),
                )
            )
        )
    );
}