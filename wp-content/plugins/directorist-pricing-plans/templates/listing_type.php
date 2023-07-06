<?php
$plan_id = selected_plan_id();
$order_id = !empty( $_GET['order'] ) ? $_GET['order'] : '';
$field_data = !empty($args['field_data']) ? $args['field_data'] : '';
if( ( package_or_PPL( $plan_id ) === 'pay_per_listng' ) ) {
    $type = 'regular';
    if( PPL_with_featured() ) {  $type = 'featured';  } ?>
    <input type="hidden" name="listing_type" value="<?php echo esc_attr( $type );?>">
    <?php
    return;
}


$active_orders = directorist_active_orders( $plan_id );
if( count( $active_orders ) > 1 ){ ?>

    <div class="dpp-order-select-wrapper">
        <form action="">
            <div class="directorist-form-group">
                <div class="directorist-form-label"><span><?php echo __( 'Active Orders', 'directorist-pricing-plans' )?></span></div>

                <div class="directorist-dropdown dpp-order-select-dropdown" data-label="<?php echo $field_data['label']; ?>" data-general_label="<?php echo $field_data['general_label']; ?>" data-featured_label="<?php echo $field_data['featured_label']; ?>">
                    <a href="" class="directorist-dropdown-toggle"><span class="directorist-dropdown-toggle__text"><?php echo __( 'Select an order', 'directorist-pricing-plans' )?></span></a>
                    <div class="directorist-dropdown-option">
                        <ul>
                            <?php
                            foreach( $active_orders as $order ){
                                $plan_id = get_post_meta( $order, '_fm_plan_ordered'. true );
                                ?>
                                <li><a href="" data-value="<?php echo $order; ?>"><?php echo '#' . esc_attr( $order ); ?></a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                
            </div>
        </form>

    </div>


<?php }else{

$remaining  = plans_remaining( $plan_id, $order_id );
$featured   = $remaining['featured'];
$regular    = $remaining['regular'];

?>
            <div class="directorist-listing-type">
                <h4 class="directorist-option-title"><?php echo esc_attr( $field_data['label'] ); ?></h4>
                <div class="directorist-listing-type_list">
                    <?php
                    if ( 'Unlimited' === $regular ) {
                    ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php echo esc_attr( $field_data['general_label'] ); ?>
                                <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-pricing-plans') ?></span>
                            </label>
                        </div>
                    <?php } else { ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="regular" type="radio" class="atbdp_radio_input" name="listing_type" value="regular" checked>
                            <label for="regular"><?php echo esc_attr( $field_data['general_label'] ); ?>
                                <span class="<?php echo $regular > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                    <?php echo '(' . $regular . __('Remaining', 'directorist-pricing-plans') . ')'; ?></span>
                            </label>
                        </div>
                    <?php } ?>

                    <?php
                    if ( 'Unlimited' === $featured ) {
                    ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="featured" type="radio" class="atbdp_radio_input" name="listing_type" value="featured">
                            <label for="featured" class="featured_listing_type_select">
                                <?php echo esc_attr( $field_data['featured_label'] ); ?>
                                <span class="atbdp_make_str_green"><?php _e(" (Unlimited)", 'directorist-pricing-plans') ?></span>
                            </label>
                        </div>
                    <?php
                    } else { ?>
                        <div class="directorist-input-group --atbdp_inline">
                            <input id="featured" type="radio" class="atbdp_radio_input" name="listing_type" value="featured">
                            <label for="featured" class="featured_listing_type_select">
                                <?php echo esc_attr( $field_data['featured_label'] ); ?>
                                <span class="<?php echo $featured > 0 ? 'atbdp_make_str_green' : 'atbdp_make_str_red' ?>">
                                    <?php echo '(' . $featured . __('Remaining', 'directorist-pricing-plans') . ')'; ?></span>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
<?php
}