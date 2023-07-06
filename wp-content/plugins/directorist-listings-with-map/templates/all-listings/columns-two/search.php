<?php 

global $bdmv_listings; 

$type = $bdmv_listings->data['listings']->type;

$current_type = ! empty( $_POST['directory_type'] ) ? $_POST['directory_type'] :  $bdmv_listings->data['listings']->current_listing_type;;

$searchform = new \Directorist\Directorist_Listing_Search_Form( 'listing', $current_type );

$map_location = isset($_GET['location']) ? $_GET['location'] : '';

$manual_lat = isset($_GET['manual_lat']) ? $_GET['manual_lat'] : '';

$manual_lng = isset($_GET['manual_lng']) ? $_GET['manual_lng'] : '';

?>

<div class=" search-area directorist-ad-search directorist-ads-form" data-nonce="<?php $bdmv_listings->get_the_nonce(); ?>" id="directorist-search-area">

    <form id="directorist-search-area-form">

        <?php

        // Custom code added by ASP - 12-09-2022 | If condition 

        foreach ( $searchform->form_data[0]['fields'] as $field ) { if($field['widget_name'] == 'title') { ?>

        <div class="directorist-basic-search-fields-each"><?php $searchform->field_template( $field ); ?></div>

        <?php } } ?>



        <!-- Custom code added by ASP - 12-09-2022 -->

        <div class="directorist-basic-search-fields-each">

            <div class="directorist-search-field directorist-form-group directorist-search-query directorist-form-address-field">

                <input type="text" id="address" name="location" class="directorist-form-element directorist-location-js" placeholder="Search a location" value="<?php echo !empty($_SESSION['map_location']) ? $_SESSION['map_location'] : $map_location; ?>" />

                <input type="hidden" name="manual_lat" id="manual_lat" value="<?php echo !empty($_SESSION['manual_lat']) ? $_SESSION['manual_lat'] : $manual_lat; ?>" />

                <input type="hidden" name="manual_lng" id="manual_lng" value="<?php echo !empty($_SESSION['manual_lng']) ? $_SESSION['manual_lng'] : $manual_lng; ?>" />

                <div class="address_result"><ul></ul></div>

            </div>

        </div>

        <div class="directorist-basic-search-fields-each">

            <div class="directorist-search-field">

                <div class="directorist-select directorist-search-category">

                    <label><?php _e('Listing By: ', 'directorist'); ?></label>

                    <label for="author_type_author"><input type="checkbox" id="author_type_author" name="author_type" value="author" <?php echo !empty($_SESSION['author_type_author']) ? 'checked' : ''; ?> /> <?php _e('Seller', 'directorist'); ?></label>

                    <label for="author_type_dispensary"><input type="checkbox" id="author_type_dispensary" name="author_type" value="dispensary" <?php echo !empty($_SESSION['author_type_dispensary']) ? 'checked' : ''; ?> /> <?php _e('Dispensary', 'directorist'); ?></label>

                </div>

            </div>

        </div>

        <!-- Custom code added by ASP - 12-09-2022 -->



        <div class="dlm-action-wrapper dlm-filter-slide dlm-filter-dropdown">

            <!-- .dlm-filter-slide / .dlm-filter-dropdown -->

            <button type="submit" class="directorist-btn directorist-btn-sm directorist-btn-dark"><?php _e('Search', 'directorist'); ?></button>

            <?php if ( $bdmv_listings->has_any_hidden_fields() ) { ?>

                <button type="button" class="directorist-btn directorist-btn-sm directorist-btn-outline-dark dlm_filter-btn"><?php _e('More Filters', 'directorist'); ?></button>

            <?php } ?>

        </div>

        <div class="dlm-filter-slide dlm-filter-slide-wrapper">

            <div class="directorist-more-filter-contents">

            <?php

            // Custom code added by ASP - 12-09-2022 | If condition

            foreach ( $searchform->form_data[1]['fields'] as $field ) { if($field['widget_name'] == 'radius_search' || $field['widget_name'] == 'review' ) { ?>

                <div class="form-group directorist-search-field-<?php echo esc_attr( $field['widget_name'] )?>"><?php $searchform->field_template( $field ); ?></div>

            <?php } } ?>

        <?php $searchform->buttons_template(); ?>

            </div>

        </div>

    </form>

</div><!-- ends: .directorist-ad-search -->

