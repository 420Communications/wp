<?php

/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

 
/*==========================================
    Shortcode : Listing Categories
    Author URI: https://wpwax.com
============================================*/
$title_wrap_class = $number_cat = $order_by = $order_list = $row = $slug = $cat_style = '';

extract($atts);
$slug = ('slug' == $order_by) ? $slug : '';

$wrap_class = apply_filters('kc-el-class', $atts);
unset($wrap_class[0]);
$class_title = array('kc_title');

$wrap_class[] = 'kc-title-wrap'; ?>

<div class="<?php echo implode(' ', $wrap_class); ?>" id="<?php echo esc_attr($cat_style); ?>">
    <?php echo do_shortcode('[directorist_all_categories view="grid" orderby="' . esc_attr($order_by) . '" order="' . esc_attr($order_list) . '" cat_per_page="' . esc_attr($number_cat) . '" columns="' . esc_attr($row) . '" slug="' . esc_attr($slug) . '"]'); ?>
</div>