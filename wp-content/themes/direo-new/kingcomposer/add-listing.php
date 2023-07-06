<?php

/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

 
/*==========================================
    Shortcode : Add Listing
    Author URI: https://wpwax.com
============================================*/

$wrap_class = [];

extract($atts);
$wrap_class = apply_filters('kc-el-class', $atts);
unset($wrap_class[0]);?>


<div class="row <?php echo implode(' ', $wrap_class); ?>">
    <div class="col-md-12">
        <?php echo do_shortcode('[directorist_add_listing]'); ?>
    </div>
</div>