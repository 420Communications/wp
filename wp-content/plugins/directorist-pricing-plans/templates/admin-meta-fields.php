<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;
$post_meta = get_post_meta(get_the_ID());
?>
<table class="directorist-input-box widefat directorist-table-responsive" id="directorist-field-details">

    <tbody>
        <tr class="directorist-field-type">
            <td class="directorist-label">
                <label class="directorist-input-box__title widefat"><?php _e('Select Plan Type', 'directorist-pricing-plans'); ?></label>
            </td>
            <td class="directorist-field-lable">
                <?php $selected_plan_type = isset($post_meta['plan_type']) ? esc_attr($post_meta['plan_type'][0]) : ''; ?>
                <ul class="directorist-radio-list">
                    <li>
                        <div class="directorist-radio directorist-radio-theme-admin directorist-radio-circle">
                            <input id="directorist-pay-per-listing" type="radio" name="plan_type" value="pay_per_listng" <?php echo checked($selected_plan_type, 'pay_per_listng', false); ?>>
                            <label class="directorist-radio__label" for="directorist-pay-per-listing"><?php _e('Pay Per Listing', 'directorist-pricing-plans'); ?></label>
                        </div>
                    </li>
                    <li>
                        <div class="directorist-radio directorist-radio-theme-admin directorist-radio-circle">
                            <input id="directorist-pay-per-package" type="radio" name="plan_type" class='directorist-cptm-modal-toggle' data-target="directorist-seelct-plan-modal" value="package" <?php echo checked($selected_plan_type, 'package', false); ?> <?php echo !$selected_plan_type ? 'checked' : ''; ?>>
                            <label class="directorist-radio__label" for="directorist-pay-per-package"><?php _e('Package', 'directorist-pricing-plans'); ?></label>
                        </div>
                    </li>
                </ul>
            </td>
        </tr>


        <tr class="directorist-field-type">
            <td class="directorist-label">
                <label class="directorist-input-box__title widefat"><?php _e('Price', 'directorist-pricing-plans'); ?></label>
            </td>
            <td class="directorist-field-lable">
                <?php
                $tax_type = !empty($post_meta['plan_tax_type']) ? esc_attr($post_meta['plan_tax_type'][0]) : '';
                $tax_placeholder = get_directorist_option('tax_placeholder', 'tax'); ?>
                <div class="directorist-form-group directorist_price-input">
                    <input name="fm_price" type="number" step="any" id="fm_price" value="<?php if (isset($post_meta['fm_price'])) echo esc_attr($post_meta['fm_price'][0]); ?>" placeholder="<?php _e('Plan price', 'directorist-pricing-plans'); ?>"></label>
                </div>
                <div class="directorist_tax-wrap directorist_visible">
                    <div class="plan_tax_area atpp_add-tax-check directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-15">
                        <input type="checkbox" id="plan_tax" name="plan_tax" value="1" <?php echo (!empty($post_meta['plan_tax'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-checkbox__label" for="plan_tax"><?php printf(__('Add %s Rate', 'directorist-pricing-plans'), ucwords($tax_placeholder)); ?></label>
                    </div>
                    <div class="directorist-tax-area-hidden" style="display: none;">
                        <div class="directorist-tax-type-selection directorist-form-group ">
                            <div class="directorist_select directorist_select-primary directorist_select-circle">
                                <select name="plan_tax_type" id="">
                                    <option value="flat" <?php echo 'flat' === $tax_type ? 'selected' : ''; ?>><?php esc_html_e('Flat Rate', 'directorist-pricing-plans'); ?></option>
                                    <option value="percent" <?php echo 'percent' === $tax_type ? 'selected' : ''; ?>><?php esc_html_e('Percentage', 'directorist-pricing-plans'); ?></option>
                                </select>
                                <input type="number" step="any" name="fm_tax" value="<?php if (isset($post_meta['fm_tax'])) echo esc_attr($post_meta['fm_tax'][0]); ?>" placeholder="<?php _e('Example 2', 'directorist-pricing-plans'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="directorist_free-plan directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-15">
                    <input type="checkbox" id="free_plan" name="free_plan" value="1" <?php echo (!empty($post_meta['free_plan'][0])) ? 'checked' : ''; ?>>
                    <label class="directorist-checkbox__label" for="free_plan"><?php echo __('Free', 'directorist-pricing-plans'); ?></label>
                </div>
            </td>
        </tr>

        <tr class="directorist-field-instructions">
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Plan Short Description', 'directorist-pricing-plans'); ?></label>

                <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square">
                    <input id="hide_description" type="checkbox" value="1" name="hide_description" <?php echo (!empty($post_meta['hide_description'][0])) ? 'checked' : ''; ?>>
                    <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_description"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                </div>
            </td>
            <td>
                <div class="directorist-form-group ">
                    <textarea class="textarea" name="fm_description" placeholder="Description" rows="6" cols="64"><?php if (isset($post_meta['fm_description'])) echo esc_textarea($post_meta['fm_description'][0]); ?></textarea>
                </div>
            </td>
        </tr>

        <tr class="directorist-field-instructions">
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Listing Duration', 'directorist-pricing-plans'); ?></label>
            </td>
            <td>
                <div class="directorist-renew-check-wrap directorist_handle-input directorist_visible">
                    <div class="directorist-renew-check-content">

                        <div class="directorist-recurring-time-period-from">
                            <?php
                            $period_term = !empty($post_meta['_recurrence_period_term']) ? $post_meta['_recurrence_period_term'][0] : 'day';
                            ?>
                            <div class="directorist-form-group directorist_renew-time">
                                <input type="number" name="fm_length" id="recurring_period" value="<?php echo !empty($post_meta['fm_length']) ? (int)$post_meta['fm_length'][0] : 30; ?>">
                            </div>
                            <div class="directorist-form-group ">
                                <select name="recurrence_period_term">
                                    <option value="day" <?php echo 'day' === $period_term ? 'selected' : ''; ?>><?php _e('Day(s)', 'directorist-pricing-plans'); ?></option>
                                    <option value="week" <?php echo 'week' === $period_term ? 'selected' : ''; ?>><?php _e('Week(s)', 'directorist-pricing-plans'); ?></option>
                                    <option value="month" <?php echo 'month' === $period_term ? 'selected' : ''; ?>><?php _e('Month(s)', 'directorist-pricing-plans'); ?></option>
                                    <option value="year" <?php echo 'year' === $period_term ? 'selected' : ''; ?>><?php _e('Year(s)', 'directorist-pricing-plans'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="directorist_input-control directorist_input-control directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-15">
                    <input id="fm_length_unl" type="checkbox" value="1" name="fm_length_unl" <?php echo (!empty($post_meta['fm_length_unl'][0])) ? 'checked' : ''; ?>>
                    <label class="directorist-fm-unlimited directorist-checkbox__label" for="fm_length_unl"><?php _e('Never expire', 'directorist-pricing-plans'); ?></label>
                </div>
            </td>
        </tr>

        <tr class="directorist-field-instructions">
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Enable Subscription', 'directorist-pricing-plans'); ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide  directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_recurring" type="checkbox" value="1" name="hide_recurring" <?php echo (!empty($post_meta['hide_recurring'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_recurring"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary">
                    <input type="checkbox" name="atpp_recurring" id="atpp_claim-badge" value="yes" <?php echo (!empty($post_meta['_atpp_recurring'][0])) ? 'checked' : ''; ?>>
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
                <label for="directorist_auto-renew-check" class="directorist_auto-renew-check"><?php _e('Requires Directorist PayPal or Stripe payment gateway', 'directorist-pricing-plans'); ?></label>
                <div class="directorist-switch-Yn-content">
                    <div class="atpp_recurring_time_period-text">
                        <?php _e('<p>(PayPal Allowed Range: Days 1-90, Weeks 1-52, Months 1-24, Years 1-5)<br>(Stripe Allowed Range: Days 1-365, Weeks 1-52, Months 1-12, Year 1)</p>', 'directorist-pricing-plans'); ?>
                    </div>
                </div>
            </td>
        </tr>




        <tr id="is_listing_featured" class="directorist-field-instructions">
            <td class="directorist-label">
                <label class='directorist-input-box__title' for="is_featured_listing"><?php _e('Featured the Listing', 'directorist-pricing-plans'); ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide">
                        <div class="directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                            <input id="hide_listing_featured" type="checkbox" value="1" name="hide_listing_featured" <?php echo (!empty($post_meta['hide_listing_featured'][0])) ? 'checked' : ''; ?>>
                            <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_listing_featured"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                        </div>
                    </div>
                </div>
            </td>
            <td class="directorist-label">
                <label class="directorist-switch-Yn directorist-switch-Yn-primary">
                    <input id="is_featured_listing" type="checkbox" value="1" name="is_featured_listing" <?php echo (!empty($post_meta['is_featured_listing'][0])) ? 'checked' : ''; ?>>
                    <span class="directorist-switch-yes"><?php _e('Yes', 'directorist-pricing-plans'); ?></span>
                    <span class="directorist-switch-no"><?php _e('No', 'directorist-pricing-plans'); ?></span>
                </label>
            </td>
        </tr>
        <tr id="regular_listing" class="directorist-field-instructions">
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Number of Listings', 'directorist-pricing-plans'); ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_listings" type="checkbox" value="1" name="hide_listings" <?php echo (!empty($post_meta['hide_listings'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_listings"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <div class="directorist-form-group directorist_handle-input directorist_visible">
                    <input type="number" name="num_regular" value="<?php if (isset($post_meta['num_regular'])) echo esc_attr($post_meta['num_regular'][0]); ?>" placeholder="<?php _e('Example 100', 'directorist-pricing-plans'); ?>">
                </div>
                <div class="directorist_input-control directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-15">
                    <input id="num_regular_unl" type="checkbox" value="1" name="num_regular_unl" <?php echo (!empty($post_meta['num_regular_unl'][0])) ? 'checked' : ''; ?>>
                    <label class="directorist-fm-unlimited directorist-checkbox__label" for="num_regular_unl"><?php _e('Or mark as unlimited ', 'directorist-pricing-plans'); ?></label>
                </div>
            </td>
        </tr>

        <tr id="featured_listing" class="directorist-field-instructions">
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Number of Featured Listings', 'directorist-pricing-plans'); ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_featured" type="checkbox" value="1" name="hide_featured" <?php echo (!empty($post_meta['hide_featured'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_featured"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <div class="directorist-form-group directorist_handle-input directorist_visible">
                    <input type="number" name="num_featured" value="<?php if (isset($post_meta['num_featured'])) echo esc_attr($post_meta['num_featured'][0]); ?>" placeholder="<?php _e('Example 5', 'directorist-pricing-plans'); ?>">
                </div>
                <div class="directorist_input-control directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-15">
                    <input id="num_featured_unl" type="checkbox" value="1" name="num_featured_unl" <?php echo (!empty($post_meta['num_featured_unl'][0])) ? 'checked' : ''; ?>>
                    <label class="directorist-fm-unlimited directorist-checkbox__label" for="num_featured_unl"><?php _e('Or mark as unlimited ', 'directorist-pricing-plans'); ?></label>
                </div>
            </td>
        </tr>

        <tr class="directorist-field-instructions">
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Contact Listing Owner', 'directorist-pricing-plans'); ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide  directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_Cowner" type="checkbox" value="1" name="hide_Cowner" <?php echo (!empty($post_meta['hide_Cowner'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_Cowner"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary">
                    <input type="checkbox" name="cf_owner" id="atpp_claim-badge" <?php echo (!empty($post_meta['cf_owner'][0])) ? 'checked' : ''; ?> value="yes">
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
            </td>
        </tr>


        <tr class="directorist-field-instructions" id="new_plan_review_area">
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Customer Reviews', 'directorist-pricing-plans'); ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_review" type="checkbox" value="1" name="hide_review" <?php echo (!empty($post_meta['hide_review'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_review"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary">
                    <input type="checkbox" name="fm_cs_review" id="atpp_claim-badge" <?php echo (!empty($post_meta['fm_cs_review'][0])) ? 'checked' : ''; ?> value="yes">
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
            </td>
        </tr>

        <tr class="directorist-field-instructions" id="new_plan_claim_area">
            <?php
            $extension_url = '<a style="text-decoration: underline" href="https://directorist.com/product/directorist-booking/" target="_blank">Claim Listing</a>';
            $required = !class_exists('DCL_Base') ? '(It requires ' . $extension_url . ' extension)' : '';
            ?>
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Claim Badge Included', 'directorist-pricing-plans') . $required; ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_claim" type="checkbox" value="1" name="hide_claim" <?php echo (!empty($post_meta['_hide_claim'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_claim"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary <?php echo empty( $required ) ? 'disabled' : ''; ?>">
                    <input type="checkbox" name="fm_claim" <?php echo (!empty($post_meta['_fm_claim'][0])) ? 'checked' : ''; ?> id="atpp_claim-badge" value="yes">
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
            </td>
        </tr>

        <tr class="directorist-field-instructions" id="new_plan_booking_area">
            <?php
            $extension_url = '<a style="text-decoration: underline" href="https://directorist.com/product/directorist-booking/" target="_blank">Booking (Reservation & Appointment)</a>';
            $required = !class_exists('BD_Booking') ? '(It requires ' . $extension_url . ' extension)' : '';
            ?>
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Booking ', 'directorist-pricing-plans') . $required; ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_booking" type="checkbox" value="1" name="hide_booking" <?php echo (!empty($post_meta['_hide_booking'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_booking"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary <?php echo empty( $required ) ? 'disabled' : ''; ?>">
                    <input type="checkbox" name="fm_booking" <?php echo (!empty($post_meta['_fm_booking'][0])) ? 'checked' : ''; ?> id="atpp_booking-badge" value="yes">
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
            </td>
        </tr>

        <tr class="directorist-field-instructions" id="new_plan_live_chat_area">
            <?php
            $extension_url = '<a style="text-decoration: underline" href="https://directorist.com/product/directorist-live-chat/" target="_blank">Live Chat</a>';
            $required = !class_exists('Directorist_Live_Chat') ? '(It requires ' . $extension_url . ' extension)' : '';
            ?>
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php echo __('Live Chat ', 'directorist-pricing-plans') . $required; ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_live_chat" type="checkbox" value="1" name="hide_live_chat" <?php echo (!empty($post_meta['_hide_live_chat'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_live_chat"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary <?php echo empty( $required ) ? 'disabled' : ''; ?>">
                    <input type="checkbox" name="fm_live_chat" <?php echo (!empty($post_meta['_fm_live_chat'][0])) ? 'checked' : ''; ?> id="atpp_chat-badge" value="yes">
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
            </td>
        </tr>

        <tr class="directorist-field-instructions" id="new_plan_live_chat_area">
            <?php
            $extension_url = '<a style="text-decoration: underline" href="https://directorist.com/product/directorist-mark-as-sold/" target="_blank">Mark as Sold</a>';
            $required = !class_exists('Directorist_Mark_as_Sold') ? '(It requires ' . $extension_url . ' extension)' : '';
            ?>
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php echo __('Mark as Sold ', 'directorist-pricing-plans') . $required; ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_mark_as_sold" type="checkbox" value="1" name="hide_mark_as_sold" <?php echo (!empty($post_meta['_hide_mark_as_sold'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_mark_as_sold"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary <?php echo empty( $required ) ? 'disabled' : ''; ?>">
                    <input type="checkbox" name="fm_mark_as_sold" <?php echo (!empty($post_meta['_fm_mark_as_sold'][0])) ? 'checked' : ''; ?> id="atpp_sold-badge" value="yes">
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
            </td>
        </tr>


        <?php
        $current_val = !empty(get_post_meta(get_the_ID(), 'exclude_cat', true)) ? get_post_meta(get_the_ID(), 'exclude_cat', true) : array();
        $categories = get_terms(ATBDP_CATEGORY, array('hide_empty' => 0, 'parent' => 0));
        ?>
        <tr class="directorist-field-instructions">
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Exclude Categories', 'directorist-pricing-plans'); ?></label>
                <div class="directorist-input-box__selection">
                    <div class="directorist-hide directorist-checkbox directorist-checkbox-blue directorist-checkbox-square directorist-mt-10">
                        <input id="hide_categories" type="checkbox" value="1" name="hide_categories" <?php echo (!empty($post_meta['hide_categories'][0])) ? 'checked' : ''; ?>>
                        <label class="directorist-fm-hide-option directorist-checkbox__label" for="hide_categories"><?php _e('Hide this from pricing plan page ', 'directorist-pricing-plans'); ?></label>
                    </div>
                </div>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary directorist-mb-20">
                    <input type="checkbox" <?php echo !empty($current_val) ? 'checked' : ''; ?> name="exclude_categories" id="atpp_claim-badge" value="yes">
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
                <div class="directorist-switch-Yn-content directorist-checkbox-list-wrap">
                    <?php
                    foreach ($categories as $key => $cat_title) {
                        $checked = in_array($cat_title->term_id, $current_val) ? 'checked' : '';
                        printf('
                    <div class="directorist_input-control directorist-checkbox directorist-checkbox-blue directorist-checkbox-square">
                        <input name="exclude_cat[]" id="%s" type="checkbox" value="%s" %s>
                        <label for="%s" class="directorist-fm-unlimited directorist-checkbox__label">%s</label>
                    </div>', $cat_title->term_id, $cat_title->term_id, $checked, $cat_title->term_id, $cat_title->name);
                    }
                    ?>
                </div>

            </td>
        </tr>

        <tr class="directorist-field-instructions">

            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Recommend this Plan', 'directorist-pricing-plans'); ?></label>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary">
                    <input type="checkbox" <?php echo (!empty($post_meta['default_pln'][0])) ? 'checked' : ''; ?> name="default_pln" id="atpp_claim-badge" value="yes">
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
            </td>
        </tr>

        <tr class="directorist-field-instructions">
            <td class="directorist-label">
                <label class="directorist-input-box__title"><?php _e('Hide form All Plans', 'directorist-pricing-plans'); ?></label>
            </td>
            <td>
                <label class="directorist-switch-Yn directorist-switch-Yn-primary">
                    <input type="checkbox" <?php echo (!empty($post_meta['_hide_from_plans'][0])) ? 'checked' : ''; ?> name="hide_from_plans" id="atpp_claim-badge" value="yes">
                    <span class="directorist-switch-yes">Yes</span>
                    <span class="directorist-switch-no">No</span>
                </label>
            </td>
        </tr>

    </tbody>
</table>

<!-- Model : Select Plan Alert -->
<div class="directorist-cptm-modal-container directorist-seelct-plan-modal">
    <div class="directorist-cptm-modal-wrap">
        <div class="directorist-cptm-modal">
            <div class="directorist-cptm-modal-content">
                <div class="directorist-cptm-modal-header">
                    <h3 class="directorist-cptm-modal-header-title"><?php _e('Change Plan Type', 'directorist-pricing-plans'); ?></h3>
                </div>

                <div class="directorist-cptm-modal-body directorist-cptm-center-content directorist-cptm-content-wide">
                    <form action="#" method="post" class="directorist-cptm-import-directory-form">
                        <div class="directorist-cptm-form-group-feedback atpp_cptm-text-center atpp_cptm-mb-10"></div>

                        <h2 class="directorist-cptm-title"><?php _e('Please Make sure to add number of Regular and Featured listings in this package', 'directorist-pricing-plans') ?></h2>


                    </form>
                </div>
                <div class="directorist-cptm-modal-footer">
                    <div class="directorist-cptm-file-input-wrap">
                        <a href="#" class="directorist-btn directorist-btn-xs directorist-btn-danger directorist-cptm-modal-toggle directorist-modal-cancel" data-target="directorist-seelct-plan-modal">
                            <?php _e('Cancel', 'directorist-pricing-plans'); ?>
                        </a>

                        <a href="#" class="directorist-btn directorist-btn-xs directorist-btn-secondary directorist-cptm-modal-toggle atpp_modal-ok" data-target="directorist-seelct-plan-modal">
                            <?php _e('Ok', 'directorist-pricing-plans'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>