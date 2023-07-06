<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;
?>

<div class="directorist-form-fields">
        <div class="directorist-directory-type-fields" data-term-id="<?php echo esc_attr( $default_type ); ?>">
            <table class="directorist-input-box atbdp-input widefat">
            <?php
            $submission_form = get_term_meta( $default_type, 'submission_form_fields', true );
            $submission_form_fields = !empty( $submission_form['fields'] ) ? $submission_form['fields'] : [];
            $submission_form_fields = swbd_pricing_plan__include_additional_submission_fields( [ 'submission_form_fields' => $submission_form_fields ] );

            if( ! empty( $submission_form_fields ) ) {
                foreach( $submission_form_fields as $field ){
                    $label       = !empty( $field['label'] ) ? $field['label'] : '';
                    $type        = !empty( $field['type'] ) ? $field['type'] : '';
                    $field_key   = !empty( $field['field_key'] ) ? $field['field_key'] : '';
                    $value       = get_post_meta( $post->ID, '_' . $field_key, true );
                    $widget_name = ( isset( $field['widget_name'] ) ) ? $field['widget_name'] : '';
                    if( 'pricing' == $widget_name ) {  
                        $field_key  = 'pricing';
                        $type       = 'text';
                    }
                    //if( 'faqs' == $widget_name ) continue;
                    if( 'booking' == $widget_name ) continue;
                    if( 'listing_type' == $widget_name ) continue;
                    // e_var_dump([
                    //     'name' => $label,
                    //     'key'  => $field_key,
                    //     'type' => $field,
                    // ]);
                    if( 'tax_input[at_biz_dir-location][]' == $field_key ) {  $field_key = 'location'; }
                    if( 'admin_category_select[]' == $field_key ) {  $field_key = 'category';  }
                    if( 'tax_input[at_biz_dir-tags][]' == $field_key ) { $field_key = 'tag'; }

                    if( ( 'wp_editor' == $type ) || ( 'number' == $type ) || ( 'multiple' == $type ) || ( 'textarea' == $type ) || ( 'media' == $type ) ) { ?>

                     <tr class="directorist-field-instructions">
                        <td class="directorist-label">
                            <div class="directorist-input-box__title">
                                <label for="cf_owner"><?php echo esc_attr( $label ); ?></label>
                            </div>
                            <div class="directorist-input-box__selection">
                                <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square">
                                    <input type="hidden" value="0" name="form_fields[hide_<?php echo esc_attr( $field_key ); ?>]">
                                    <input type="checkbox" id="hide_<?php echo esc_attr( $field_key ); ?>" value="1"
                                        name="form_fields[hide_<?php echo esc_attr( $field_key ); ?>]" <?php echo (!empty($post_meta[ '_hide_'.$field_key ][0])) ? 'checked' : ''; ?>
                                    >
                                        
                                    <label class="directorist-fm-hide-option directorist-checkbox__label"
                                        for="hide_<?php echo esc_attr( $field_key ); ?>"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?>
                                    </label>
                                </div>
                            <div>
                        </td>
                        <td>
                            <label class="directorist-switch-Yn directorist-switch-Yn-primary">
                                <input type="hidden" name="form_fields[<?php echo esc_attr( $field_key ); ?>]" value="0">
                                <input type="checkbox" name="form_fields[<?php echo esc_attr( $field_key ); ?>]" value="1" <?php echo (!empty($post_meta[ '_'.$field_key ][0])) ? 'checked' : ''; ?>>
                                <span class="directorist-switch-yes"><?php _e('Yes', 'directorist-pricing-plans'); ?></span>
                                <span class="directorist-switch-no"><?php _e('No', 'directorist-pricing-plans'); ?></span>
                            </label>
                            <div class="directorist-switch-Yn-content">
                                <div class="directorist-form-group directorist_handle-input directorist_visible">
                                    <input type="number" name="form_fields[max_<?php echo esc_attr( $field_key ); ?>]"
                                        value="<?php echo !empty( $post_meta[ '_max_' . $field_key ][0] ) ? esc_attr( $post_meta[ '_max_' . $field_key ][0] ) : ''; ?>"
                                        placeholder="<?php _e('Set a limit', 'directorist-pricing-plans'); ?>">
                                </div>
                                <div class="directorist_input-control directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-15">
                                    <input type="hidden" value="0" name="form_fields[unlimited_<?php echo esc_attr( $field_key ); ?>]">
                                    <input id="unlimited_<?php echo esc_attr( $field_key ); ?>" type="checkbox" value="1" name="form_fields[unlimited_<?php echo esc_attr( $field_key ); ?>]" <?php echo (!empty( $post_meta[ '_unlimited_' . $field_key ][0] )) ? 'checked' : ''; ?>>
                                        
                                    <label class="directorist-fm-unlimited directorist-checkbox__label" for="unlimited_<?php echo esc_attr( $field_key ); ?>"><?php _e('Or mark as unlimited ', 'directorist-pricing-plans'); ?></label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php 
                    }else {?>      
                    <tr class="directorist-field-instructions">
                        <td class="directorist-label">
                            <div class="directorist-input-box__title">
                                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo esc_attr( $label ); ?></label>
                            </div>
                            <div class="directorist-input-box__selection">
                                <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square">
                                    <input type="hidden" value="0" name="form_fields[hide_<?php echo esc_attr( $field_key ); ?>]">
                                    <input 
                                        id="hide_<?php echo esc_attr( $field_key ); ?>" 
                                        type="checkbox" 
                                        value="1" 
                                        name="form_fields[hide_<?php echo esc_attr( $field_key ); ?>]" <?php echo (!empty($post_meta[ '_hide_' . $field_key ][0])) ? 'checked' : ''; ?>
                                    >

                                    <label 
                                        class="directorist-fm-hide-option directorist-checkbox__label"
                                        for="hide_<?php echo esc_attr( $field_key ); ?>"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?>
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td>
                            <label class="directorist-switch-Yn directorist-switch-Yn-primary">
                                <input type="hidden" name="form_fields[<?php echo esc_attr( $field_key ); ?>]" value="0">
                                <input type="checkbox" name="form_fields[<?php echo esc_attr( $field_key ); ?>]" 
                                    value="1" <?php echo (!empty($post_meta[ '_'.$field_key ][0])) ? 'checked' : ''; ?>
                                >
                                <span class="directorist-switch-yes"><?php _e('Yes', 'directorist-pricing-plans'); ?></span>
                                <span class="directorist-switch-no"><?php _e('No', 'directorist-pricing-plans'); ?></span>
                            </label>
                        </td>
                    </tr>
                    <?php
                    }
                    }
            } else { 
                ?>
                <div><?php _e( 'No fields found in this directory', 'directorist-pricing-plans' );?></div>
                <?php
            }
            ?>
            </table>
        
            <div class="directorist-shortcode directorist-theme-2">
                <h2 class="directorist-shortcode__title"><?php esc_html_e('Shortcode', 'directorist-pricing-plans'); ?> </h2>
                <div class="directorist-shortcode-content">
                    <div class="directorist-shortcode-content__left directorist-text-center">
                        <p><?php esc_html_e('Use following shortcode to display the Plan anywhere:', 'directorist-pricing-plans'); ?></p>
                        <textarea cols="50" rows="1" onClick="this.select();">[directorist_pricing_plans id=<?php echo $post->ID; ?>]</textarea> <br/>
                    </div>
                    <div class="directorist-shortcode-content__right directorist-text-center">
                        <p><?php esc_html_e('If you need to put the shortcode inside php code/template file, use this:', 'directorist-pricing-plans'); ?></p>
                        <textarea cols="63" rows="1"
                                onClick="this.select();"><?php echo '<?php echo do_shortcode("[directorist_pricing_plans id=';
                            echo $post->ID . "]";
                            echo '"); ?>'; ?></textarea>
                    </div>
                </div>
            </div>

        </div>
</div>