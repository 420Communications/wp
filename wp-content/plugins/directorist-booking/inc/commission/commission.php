<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * BDB_Commission Class
 */
class BDB_Commission
{
    public function __construct()
    {
        add_action( 'init', array( $this, 'bdb_booking_commission') );
        //Commission columns
        add_filter( 'manage_bdb_commission_posts_columns', array( $this, 'bdb_commission_add_new_plan_columns' ) );
        add_action( 'manage_bdb_commission_posts_custom_column', array( $this, 'bdb_commission_column_content' ), 10, 2 );
        //Payout columns
        add_filter( 'manage_bdb_payout_posts_columns', array( $this, 'bdb_payout_add_new_plan_columns' ) );
        add_action( 'manage_bdb_payout_posts_custom_column', array( $this, 'bdb_payout_column_content' ), 10, 2 );
        //create meta boxes
        add_action( 'add_meta_boxes', array( $this, 'create_meta_box_for_commission' ) );
        add_action( 'admin_footer-edit.php', array( $this, 'admin_footer_edit' ) );
        add_action( 'load-edit.php', array( $this, 'load_edit' ) );
        add_action( 'wp_ajax_bdb_payment_method', array( $this, 'bdb_payment_method' ) );
    }

    //custom post type for bdb_booking_commission
    public function bdb_booking_commission() {
        $labels = array(
            'name'                  => _x( 'Users balances', 'Post type general name', 'directorist-booking' ),
            'singular_name'         => _x( 'Commission', 'Post type singular name', 'directorist-booking' ),
            'menu_name'             => _x( 'Commissions', 'Admin Menu text', 'directorist-booking' ),
            'name_admin_bar'        => _x( 'Commission', 'Add New on Toolbar', 'directorist-booking' ),
            'new_item'              => __( 'New Commission', 'directorist-booking' ),
            'edit_item'             => __( 'Edit Commission', 'directorist-booking' ),
            'view_item'             => __( 'View Commission', 'directorist-booking' ),
            'all_items'             => __( 'All Commissions', 'directorist-booking' ),
            'search_items'          => __( 'Search Commissions', 'directorist-booking' ),
            'parent_item_colon'     => __( 'Parent Commissions:', 'directorist-booking' ),
            'not_found'             => __( 'No Commissions found.', 'directorist-booking' ),
            'not_found_in_trash'    => __( 'No Commissions found in Trash.', 'directorist-booking' ),
        );

        $args = array(
            'labels'             => $labels,
            'supports'           => array( 'title' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'commission' ),
            'capabilities' => array(
                'create_posts'   => false,
            ),
            'menu_icon' => 'dashicons-money-alt',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' ),
            'show_in_nav_menus' => true,
            'can_export' => true,
            'exclude_from_search' => true,
            //'capability_type' => 'at_biz_dir',
            'map_meta_cap' => true,
        );

        $labels_two = array(
            'name'                  => _x( 'Payouts History', 'Post type general name', 'directorist-booking' ),
            'singular_name'         => _x( 'Payout', 'Post type singular name', 'directorist-booking' ),
            'menu_name'             => _x( 'Payouts History', 'Admin Menu text', 'directorist-booking' ),
            'name_admin_bar'        => _x( 'Payouts History', 'Add New on Toolbar', 'directorist-booking' ),
            'new_item'              => __( 'New Payouts History', 'directorist-booking' ),
            'edit_item'             => __( 'Edit Payouts History', 'directorist-booking' ),
            'view_item'             => __( 'View Payouts History', 'directorist-booking' ),
            'all_items'             => __( 'Payouts History', 'directorist-booking' ),
            'search_items'          => __( 'Search Payouts History', 'directorist-booking' ),
            'parent_item_colon'     => __( 'Parent Payouts History:', 'directorist-booking' ),
            'not_found'             => __( 'No Payout History found.', 'directorist-booking' ),
            'not_found_in_trash'    => __( 'No Payouts History found in Trash.', 'directorist-booking' ),
        );

        $args_two = array(
            'labels'             => $labels_two,
            'supports'           => array('title'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=bdb_commission',
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'commission' ),
            'capabilities' => array(
                'create_posts'   => false,
            ),
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' ),
            'show_in_nav_menus' => true,
            'can_export' => true,
            'exclude_from_search' => true,
            //'capability_type' => 'at_biz_dir',
            'map_meta_cap' => true,
        );

        register_post_type( 'bdb_commission', $args );
        register_post_type( 'bdb_payout', $args_two );

    }

    public function bdb_commission_add_new_plan_columns() {
        $columns = array(
            'cb'                => '<input type="checkbox" />', // Render a checkbox instead of text
            'user_name'         => __('User Name', 'directorist'),
            'balance_pay'       => __('Balance to Pay', 'directorist'),
            'order_count'       => __('Orders Count', 'directorist'),
            'payment_method'    => __('Payment Method', 'directorist'),
        );

        return $columns;
    }

    public function bdb_commission_column_content( $column, $post_id ) {
        echo '</select>';
        switch ( $column ) {
            case 'user_name' :
                $post_meta = get_post_meta( $post_id );
                $username = isset( $post_meta['_username'] ) ? esc_attr( $post_meta['_username'][0] ) : '';
                printf('<p><a href="%s"> %s</a></p>', get_edit_post_link($post_id), $username);
                break;
            case 'balance_pay' :
                $post_meta = get_post_meta( $post_id );
                $total_balance_pay = isset( $post_meta['_total_balance_pay'] ) ? esc_attr( $post_meta['_total_balance_pay'][0] ) : '';
                echo atbdp_display_price( $total_balance_pay, false,  '', '', '', false );
                break;
            case 'order_count' :
                    $post_meta = get_post_meta( $post_id );
                    $order_count = isset( $post_meta['_order_count'] ) ? esc_attr( $post_meta['_order_count'][0] ) : '';
                    echo $order_count ;
                    break;
            case 'payment_method' :
                $post_meta = get_post_meta( $post_id );
                $username  = isset( $post_meta['_username'] ) ? esc_attr( $post_meta['_username'][0] ) : '';
                $get_user  = get_user_by( 'slug', $username);
                $user_id   = $get_user->ID;
                $payment_method = get_user_meta( $user_id , 'bdb_payment_method', true );
                $payment_method = !empty( $payment_method ) ? esc_attr( $payment_method ) : 'No Payment Method';
                if( 'paypal' == $payment_method ) {
                    $payment_method = 'PayPal';
                } elseif( 'bank_transfer' == $payment_method ) {
                    $payment_method = 'Bank Transfer';
                } elseif( 'bdb_other' == $payment_method ) {
                    $payment_method = 'Other';
                }
                echo $payment_method ;
                break;

        }
    }

    public function bdb_payout_add_new_plan_columns() {
        $columns = array(
            'cb'                => '<input type="checkbox" />', // Render a checkbox instead of text
            'user_name'         => __('User Name', 'directorist-booking'),
            'amount'            => __('Amount', 'directorist-booking'),
            'payment_method'    => __('Payment Method', 'directorist-booking'),
            'bdb_date'              => __('Date', 'directorist-booking'),
        );

        return $columns;
    }

    public function bdb_payout_column_content( $column, $post_id ) {
        echo '</select>';
        switch ( $column ) {
            case 'user_name' :
                $post_meta = get_post_meta( $post_id );
                $username = isset( $post_meta['_username'] ) ? esc_attr( $post_meta['_username'][0] ) : '';
                printf('<p><a href="%s"> %s</a></p>', get_edit_post_link( $post_id ), $username );
                break;
            case 'amount' :
                $post_meta = get_post_meta( $post_id );
                $balance_pay = isset( $post_meta['_total_balance_pay'] ) ? atbdp_display_price( esc_attr( $post_meta['_total_balance_pay'][0] ), false,  '', '', '', false ) : '';
                echo $balance_pay ;
                break;
            case 'payment_method' :
                $post_meta = get_post_meta( $post_id );
                $payment_method = isset( $post_meta['_payment_method'] ) ? esc_attr( $post_meta['_payment_method'][0] ) : 'No Payment Method';
                if( 'paypal' == $payment_method ) {
                    $payment_method = 'PayPal';
                } elseif( 'bank_transfer' == $payment_method ) {
                    $payment_method = 'Bank Transfer';
                }
                echo $payment_method ;
                break;
            case 'bdb_date' :
                $t           = get_the_time('U');
                $date_format = get_option('date_format');
                echo date_i18n($date_format, $t);
                break;

        }
    }

    public function create_meta_box_for_commission() {
        add_meta_box( 'dbb_commission_details', __('Details', 'directorist-booking'), array( $this, 'commission_details_meta_box' ), 'bdb_commission', 'normal', 'high' );
    }

    public function commission_details_meta_box( $post ) {
        // Add a nonce field so we can check for it later
        wp_nonce_field('atbdp_review_save_details', 'atbdp_review_details_nonce');
        wp_enqueue_style('bdb-admin-css');
        $post_meta           = get_post_meta( $post->ID ) ? get_post_meta( $post->ID ) : '' ;
        $listing_name        = isset( $post_meta['_listing_name'] ) ? esc_attr( $post_meta['_listing_name'][0] ) : '';
        $publish_date        = isset( $post_meta['_publish_date'] ) ? esc_attr( $post_meta['_publish_date'][0] ) : '';
        $order_id            = isset( $post_meta['_order_id'] ) ? esc_attr( $post_meta['_order_id'][0] ) : '';
        $username            = isset( $post_meta['_username'] ) ? esc_attr( $post_meta['_username'][0] ) : '';
        $amount              = isset( $post_meta['_amount'] ) ? esc_attr( $post_meta['_amount'][0] ) : '';
        $site_fee            = isset( $post_meta['_site_fee'] ) ? esc_attr( $post_meta['_site_fee'][0] ) : '';
        $balance_pay         = isset( $post_meta['_balance_pay'] ) ? esc_attr( $post_meta['_balance_pay'][0] ) : '';
        $total_balance_pay   = isset( $post_meta['_total_balance_pay'] ) ? esc_attr( $post_meta['_total_balance_pay'][0] ) : '';
        $get_user        = get_user_by( 'slug', $username);
        $user_id         = $get_user->ID;
        $payment_method  = get_user_meta( $user_id, 'bdb_payment_method', true );
        $payment_details = get_user_meta( $user_id, 'bdb_payment_details', true );
        $payment_method  = !empty( $payment_method ) ? esc_attr( $payment_method ) : __( 'No Payment Method', 'directorist-booking' );
        $paypal_email    = get_user_meta( $user_id, 'bdb_paypal_email', true );
        $bank_details    = get_user_meta( $user_id, 'bdb_bank_details', true );
        $other_details   = get_user_meta( $user_id, 'bdb_other_details', true );
        if( 'paypal' == $payment_method ) {
            $payment_method = 'PayPal';
        } elseif( 'bank_transfer' == $payment_method ) {
            $payment_method = 'Bank Transfer';
        } elseif( 'bdb_other' == $payment_method ) {
            $payment_method = 'Other';
        }
        if( 'PayPal' == $payment_method ) {
            $payment_details   = ! empty( $paypal_email ) ? esc_attr( $paypal_email )  : '';
        } elseif( 'Bank Transfer' == $payment_method ) {
            $payment_details   = ! empty( $bank_details ) ? esc_attr( $bank_details )  : '';
        } elseif( 'Other' == $payment_method ) {
            $payment_details   = ! empty( $other_details ) ? esc_attr( $other_details )  : '';
        }
        $listing_names       = unserialize( base64_decode( $listing_name ) );
        $publish_date        = unserialize( base64_decode( $publish_date ) );
        $order_id            = unserialize( base64_decode( $order_id     ) );
        $amount              = unserialize( base64_decode( $amount ) );
        $site_fee            = unserialize( base64_decode( $site_fee ) );
        $balance_pay         = unserialize( base64_decode( $balance_pay ) );
        $commissions         = [];
        foreach ( $listing_names as $index => $name ) {
            $commissions[] = [
                'listing_name'  => $name,
                'date'          => $publish_date[ $index ],
                'order_id'      => $order_id[ $index ],
                'amount'        => $amount[ $index ],
                'site_fee'      => $site_fee[ $index ],
                'balance_pay'   => $balance_pay[ $index ],
            ];
        }
        ?>
        <ul class="atbd-commission-details">
            <li><span class="atbd-commission-details__label"><?php _e( 'Payment for', 'directorist-booking' ); ?></span> : <?php echo ! empty( $username ) ? esc_attr( $username ) : ''; ?></li>
            <li><span class="atbd-commission-details__label"><?php _e( 'Payment Amount', 'directorist-booking' ); ?></span> : <?php echo ! empty( $total_balance_pay ) ? atbdp_display_price( esc_attr( $total_balance_pay ), false,  '', '', '', false ) : ''; ?></li>
            <li><span class="atbd-commission-details__label"><?php _e( 'Payment Method', 'directorist-booking' ); ?></span> : <?php echo ! empty( $payment_method ) ? esc_attr( $payment_method ) : ''; ?></li>
            <li><span class="atbd-commission-details__label"><?php _e( 'Payment Details', 'directorist-booking' ); ?></span> : <?php echo ! empty( $payment_details ) ? esc_textarea( $payment_details ) : ''; ?></li>
        </ul>
        <div class="directorist-table-responsive">
            <table class="atbd-commission-table">
                <thead>
                    <tr>
                        <th><?php _e( 'Listing Name', 'directorist-booking' );?></th>
                        <th><?php _e( 'Total Order value', 'directorist-booking' );?></th>
                        <th><?php _e( 'Site Fee', 'directorist-booking' );?></th>
                        <th><?php _e( 'User Earning', 'directorist-booking' );?></th>
                        <th><?php _e( 'Order ID', 'directorist-booking' );?></th>
                        <th><?php _e( 'Date', 'directorist-booking' );?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach( array_reverse( $commissions ) as $commission ) :
                    ?>
                    <tr>
                        <td><?php echo !empty( $commission['listing_name'] ) ? $commission['listing_name'] : ''; ?></td>
                        <td><?php echo !empty( $commission['amount'] ) ? atbdp_display_price( $commission['amount'], false,  '', '', '', false ) : ''; ?></td>
                        <td><?php echo !empty( $commission['site_fee'] ) ? atbdp_display_price( $commission['site_fee'], false,  '', '', '', false ) : ''; ?></td>
                        <td><?php echo !empty( $commission['balance_pay'] ) ? atbdp_display_price( $commission['balance_pay'], false,  '', '', '', false ) : ''; ?></td>
                        <td><?php echo !empty( $commission['order_id'] ) ? $commission['order_id'] : ''; ?></td>
                        <td><?php echo !empty( $commission['date'] ) ? $commission['date'] : ''; ?></td>
                    </tr>
                    <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function admin_footer_edit() {
        global $post_type;
        if ('bdb_commission' == $post_type) {

            ?>
            <script type="text/javascript">
                var atbdp_bulk_actions = <?php echo json_encode( array( 'set_to_payment' => __( "Payment" ) ) ); ?>;

                jQuery(document).ready(function () {
                    for (var key in atbdp_bulk_actions) {
                        if (atbdp_bulk_actions.hasOwnProperty(key)) {
                            var $option = jQuery('<option>').val(key).text(atbdp_bulk_actions[key]);
                            $option.appendTo('#bulk-action-selector-top', '#bulk-action-selector-bottom');
                            //$option.appendTo('#bulk-action-selector-bottom');
                        }
                    }

                    jQuery('select[name="action"]').find('option[value="edit"]').remove();
                    jQuery('select[name="action2"]').find('option[value="edit"]').remove();
                });
            </script>
            <?php
        }
    }

    public function load_edit()
    {
        // Handle the custom bulk action
        global $typenow;
        $post_type = $typenow;

        if ( 'bdb_commission' == $typenow ) {

            // Get the action
            $wp_list_table = _get_list_table('WP_Posts_List_Table');
            $action = $wp_list_table->current_action();

            $allowed_actions = array_keys( array( 'set_to_payment' => __( "Payment" ) ) );
            if (  !in_array( $action, $allowed_actions ) ) return;

            // Security check
            check_admin_referer('bulk-posts');

            // Make sure ids are submitted
            if (isset($_REQUEST['post'])) {
                $post_ids = array_map('intval', $_REQUEST['post']);
            }

            if (empty($post_ids)) return;

            // This is based on wp-admin/edit.php
            $sendback = remove_query_arg(array_merge($allowed_actions, array('untrashed', 'deleted', 'ids')), wp_get_referer());
            if (!$sendback) $sendback = admin_url("edit.php?post_type=$post_type");

            $pagenum = $wp_list_table->get_pagenum();
            $sendback = add_query_arg('paged', $pagenum, $sendback);

            $modified = 0;
            foreach ($post_ids as $post_id) {
                if ( ! $this->update_payment( $action, $post_id ) ) wp_die(__('Error updating post.', 'directorist-booking'));
                $modified++;
            }

        }

    }

    public function update_payment( $action, $post_id ) {
        if( 'set_to_payment' == $action ) {
            $username                    = get_post_meta( $post_id, '_username', true );
            $listing_name                = get_post_meta( $post_id, '_listing_name', true );
            $publish_date                = get_post_meta( $post_id, '_publish_date', true );
            $order_id                    = get_post_meta( $post_id, '_order_id', true );
            $amount                      = get_post_meta( $post_id, '_amount', true );
            $site_fee                    = get_post_meta( $post_id, '_site_fee', true );
            $balance_pay                 = get_post_meta( $post_id, '_balance_pay', true );
            $total_balance_pay           = get_post_meta( $post_id, '_total_balance_pay', true );
            $total                       = get_post_meta( $post_id, '_total', true );
            $order_count                 = get_post_meta( $post_id, '_order_count', true );
            $get_user  = get_user_by( 'slug', $username);
            $user_id   = $get_user->ID;
            $payment_method  = get_user_meta( $user_id , 'bdb_payment_method', true );
            $payment_details = get_user_meta( $user_id , 'bdb_payment_details', true );
            $payment_method  = !empty( $payment_method ) ? esc_attr( $payment_method ) : __( 'No Payment Method', 'directorist-booking' );
            $paypal_email    = get_user_meta( $user_id, 'bdb_paypal_email', true);
            $bank_details    = get_user_meta( $user_id, 'bdb_bank_details', true);
            $other_details   = get_user_meta( $user_id, 'bdb_other_details', true);
            if( 'paypal' == $payment_method ) {
                $payment_method = 'PayPal';
            } elseif( 'bank_transfer' == $payment_method ) {
                $payment_method = 'Bank Transfer';
            } elseif( 'bdb_other' == $payment_method ) {
                $payment_method = 'Other';
            }
            if( 'PayPal' == $payment_method ) {
                $payment_details   = ! empty( $paypal_email ) ? esc_attr( $paypal_email )  : '';
            } elseif( 'Bank Transfer' == $payment_method ) {
                $payment_details   = ! empty( $bank_details ) ? esc_attr( $bank_details )  : '';
            } elseif( 'Other' == $payment_method ) {
                $payment_details   = ! empty( $other_details ) ? esc_attr( $other_details )  : '';
            }

            $payout_id  = wp_insert_post( array(
                'post_content'      => '',
                'post_title'        => $username,
                'post_status'       => 'publish',
                'post_type'         => 'bdb_payout',
                'comment_status'    => false,
            ) );
            update_post_meta( $payout_id, '_username', $username );
            update_post_meta( $payout_id, '_listing_name', $listing_name );
            update_post_meta( $payout_id, '_publish_date', $publish_date );
            update_post_meta( $payout_id, '_order_id', $order_id );
            update_post_meta( $payout_id, '_amount', $amount );
            update_post_meta( $payout_id, '_site_fee', $site_fee );
            update_post_meta( $payout_id, '_balance_pay', $balance_pay );
            update_post_meta( $payout_id, '_total_balance_pay', $total_balance_pay );
            update_post_meta( $payout_id, '_total', $total );
            update_post_meta( $payout_id, '_order_count', $order_count );
            update_post_meta( $payout_id, '_payment_method', $payment_method );
            update_post_meta( $payout_id, '_payment_details', $payment_details );
            wp_delete_post( $post_id );
        }
        return true;
    }

    public function bdb_payment_method() {
        $nonce = isset( $_POST['_nonce'] ) ? $_POST['_nonce'] : '';
        if ( ! wp_verify_nonce( $nonce, '_bdb_payment_method_nonce' ) ) {
            die ( 'huh!');
        }
        $current_user           = wp_get_current_user();
        $payment_method         = isset( $_POST['payment_method'] ) ? $_POST['payment_method'] : '';
        $paypal_email           = isset( $_POST['paypal_email'] ) ? $_POST['paypal_email'] : '';
        $bank_details           = isset( $_POST['bank_details'] ) ? $_POST['bank_details'] : '';
        $other                  = isset( $_POST['other'] ) ? $_POST['other'] : '';
        if( $payment_method ) {
            if( 'paypal' == $payment_method &&  $paypal_email ) {
                update_user_meta( $current_user->ID, 'bdb_payment_method', $payment_method );
                update_user_meta( $current_user->ID, 'bdb_paypal_email', $paypal_email );
            } elseif( 'bank_transfer' == $payment_method &&  $bank_details ) {
                update_user_meta( $current_user->ID, 'bdb_payment_method', $payment_method );
                update_user_meta( $current_user->ID, 'bdb_bank_details', $bank_details );
            } elseif( 'bdb_other' == $payment_method &&  $other ) {
                update_user_meta( $current_user->ID, 'bdb_payment_method', $payment_method );
                update_user_meta( $current_user->ID, 'bdb_other_details', $other );
            } else {
                if( 'paypal' == $payment_method ) {
                 $notice = __( 'Paypal email field is required', 'directorist-booking' );
                } elseif( 'bdb_other' == $payment_method ) {
                 $notice = __( 'Other details field is required', 'directorist-booking' );
                } else {
                    $notice = __( 'Bank details field is required', 'directorist-booking' );
                }
            }
        } else {
            $notice = __( 'Please select any payment method.', 'directorist-booking' );
        }

        if ( ! empty( $notice ) ) {
            $notice = $notice;
        } else {
            $notice = __( 'Successfully Saved', 'directorist-booking' );
        }
        wp_send_json_success( array(
            'success' => $notice
        ) );

    }
}