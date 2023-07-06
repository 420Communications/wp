<?php global $bdmv_listings;
/**
 * This template displays the Directorist listings in map view.
 */
?>
<div id="directorist" class="directorist-wrapper">
    <div class="directorist-divider"></div>
    <div class="<?php $bdmv_listings->get_the_data( 'map_container' ); ?>">
    <?php
        $atbdp_legacy_template = get_directorist_option( 'atbdp_legacy_template', false );

        if ( $atbdp_legacy_template ) {
            if ( 'google' == $bdmv_listings->options['select_listing_map'] ) {
                bdmv_get_template('view/maps/google/google-map');
            } else {
                bdmv_get_template('view/maps/openstreet/openstreet-map');
            }
        } else {
            $bdmv_listings->data['listings']->render_map();
        }

    ?>
    </div>
</div>



