<?php

/**
 * @author  WpWax
 * @since   1.10.05
 * @version 1.0
 */

 
/*==========================================
    Shortcode : Dashboard
    Author URI: https://wpwax.com
============================================*/

extract($atts);
$wrap_class = apply_filters('kc-el-class', $atts);
unset($wrap_class[0]);
?>

<div class="direo-directorist_user_dashboard <?php echo implode(' ', $wrap_class); ?>">
    <?php echo do_shortcode('[directorist_user_dashboard]'); ?>
</div>