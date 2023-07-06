<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;
?>

<div class="directorist-select-type-wrap directorist-text-center">
    <h4 class="directorist-select-type-title"><?php _e( 'Select Directory Type', 'directorist-pricing-plans' ); ?></h4>
    <input type="hidden" name="post_id" value="<?php echo $post->ID; ?>">
    <ul class="directorist-selection-box-wrap">
    <?php

if( !empty( $directory_types ) ) { 
    foreach( $directory_types as $term ) {
            ?>
        <li>
            <a class="directorist-radio directorist-radio-theme-admin directorist-radio-circle <?php echo ( $term->slug == $value ) ? 'active' : '' ; ?>">
                <input type="radio" <?php echo checked( $term->term_id , $value ); ?> name="assign_to_directory" id="<?php echo esc_attr( $term->slug ); ?>" value="<?php echo esc_attr( $term->term_id ); ?>">
                <label class="directorist-radio__label" for="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_attr( $term->name ); ?></label>
                <span class="directorist-active-line"></span>
            </a>
        </li>
       <?php }}?>
    </ul>

    <div id="directorist-type-preloader" style="display: none;">
	<div></div><div></div><div></div><div></div>
	</div>
</div>