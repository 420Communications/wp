<?php global $bdmv_listings; 
$display_image = 'yes';
?>

<div id="directorist" class="directorist-wrapper">

    <?php bdmv_get_template( 'view/listings/header' );

    /**
     * @since 5.0
     * It fires before the listings columns
     * It only fires if the parameter [directorist_all_listing action_before_after_loop="yes"]
     */
    if ( $bdmv_listings->get_boolean( 'action_before_after_loop' ) ) {
        do_action('bdm_before_list_listings_loop');
    }
    $atbdp_legacy_template = get_directorist_option( 'atbdp_legacy_template', false );
    $row_class = 'directorist-row';
    if ( ! $atbdp_legacy_template ) {
        ob_start();
        Directorist\Helper::directorist_row();

        $row_class = ob_get_clean();
    }
    ?>
    <div class="<?php echo !empty($container) ? $container : 'directorist-container-fluid'; ?>">
        <div class="<?php echo $row_class ?>">
            <?php $atbdp_legacy_template = get_directorist_option( 'atbdp_legacy_template', false );
                if ( $atbdp_legacy_template ) {
                    $bdmv_listings->data['listings']->setup_loop( ['template' => 'list'] );
                } else {
                    foreach ( $bdmv_listings->data['listings']->post_ids() as $listing_id ): ?>
                        <div class="<?php Directorist\Helper::directorist_column( 12 ); ?>">
                            <?php $bdmv_listings->data['listings']->loop_template( 'list', $listing_id ); ?>
                        </div>
                    <?php endforeach;
                }
            ?>
        </div>


        <div class="directorist-row">
            <div class="col-lg-12">
                <?php
                /**
                 * @since 5.0
                 */
                do_action('atbdp_before_listings_pagination');
                if ( $bdmv_listings->get_boolean( 'show_pagination' ) ) {
                    echo bdmv_load_more_filter( $bdmv_listings->data['query_results'], $bdmv_listings->data['paged'], $bdmv_listings->data['defSquery'] );
                } ?>
            </div>
        </div>
    </div>
    <?php
    

    /**
     * @since 5.0
     * It fires before the listings columns
     * It only fires if the parameter [directorist_all_listing action_before_after_loop="yes"]
     */
    
    if ( $bdmv_listings->get_boolean( 'action_before_after_loop' ) ) {
        do_action('bdm_after_list_listings_loop');
    }
    ?>

</div>
<!--ends .row -->