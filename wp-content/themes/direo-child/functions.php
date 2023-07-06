<?php
add_action( 'wp_enqueue_scripts', 'direo_child_styles', 18 );
add_action( 'after_setup_theme', 'direo_child_theme_setup' );

// Customizations - START
// Remove default Actions & Filters
direo_remove_filters_actions( 'wp_ajax_atbdp_public_report_abuse', 'ajax_callback_report_abuse', 10 );
direo_remove_filters_actions( 'wp_ajax_nopriv_atbdp_public_report_abuse', 'ajax_callback_report_abuse', 10 );
direo_remove_filters_actions( 'wp_ajax_nopriv_direo_ajaxlogin', 'direo_ajax_login', 10 );
direo_remove_filters_actions( 'wp_ajax_ajaxlogin', 'atbdp_ajax_login', 10 );
direo_remove_filters_actions( 'wp_ajax_nopriv_ajaxlogin', 'atbdp_ajax_login', 10);
direo_remove_filters_actions( 'wp_loaded', 'handle_user_registration', 10 );
direo_remove_filters_actions( 'woocommerce_available_payment_gateways', 'wps_sfw_unset_offline_payment_gateway_for_subscription', 10 );
direo_remove_filters_actions( 'wp_ajax_atbdp_live_chat', 'atbdp_live_chat', 10 );
direo_remove_filters_actions( 'wp_ajax_nopriv_atbdp_live_chat', 'atbdp_live_chat', 10 );
direo_remove_filters_actions( 'wp_ajax_add_listing_action', 'atbdp_submit_listing', 10 );
direo_remove_filters_actions( 'wp_ajax_nopriv_add_listing_action', 'atbdp_submit_listing', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Customized Actions & Filters & Shortcodes
add_action( 'wp_head', 'diero_custom_css' );
add_action( 'wp_ajax_atbdp_public_report_abuse', 'direo_ajax_callback_report_abuse_custom' );
add_action( 'wp_ajax_nopriv_atbdp_public_report_abuse', 'direo_ajax_callback_report_abuse_custom' );
add_action( 'admin_menu', 'direo_custom_settings' );
add_action( 'wp', 'direo_blockusers_init' );
add_action( 'wp_loaded', 'direo_user_registration' );
add_action( 'wp_ajax_nopriv_direo_ajaxlogin', 'direo_ajaxlogin_custom' );
add_action( 'wp_ajax_ajaxlogin', 'diero_ajax_login_custom' );
add_action( 'wp_ajax_nopriv_ajaxlogin', 'diero_ajax_login_custom' );
add_action( 'init', 'diero_my_cpts_scammers_list' );
add_action( 'init', 'woocommerce_empty_cart_url' );
add_action( 'init', 'diero_user_has_capability' );
add_action( 'wp_ajax_nopriv_add_scammer', 'diero_add_scammer' );
add_action( 'wp_ajax_add_scammer', 'diero_add_scammer' );
add_action( 'wp_ajax_atbdp_live_chat', 'diero_live_chat_custom' );
add_action( 'wp_ajax_nopriv_atbdp_live_chat', 'diero_live_chat_custom' );
add_action( 'woocommerce_order_status_processing', 'diero_woocommerce_order_status' );
add_action( 'woocommerce_order_status_completed', 'diero_woocommerce_order_status' );
add_action( 'wp_footer', 'diero_add_custom_script' );
add_action( 'wp_ajax_add_listing_action', 'diero_submit_listing' );
add_action( 'wp_ajax_nopriv_add_listing_action', 'diero_submit_listing' );
add_filter( 'woocommerce_default_address_fields' , 'diero_wc_checkout_fields_values' );
add_action( 'admin_enqueue_scripts', 'diero_admin_script' );
add_action( 'admin_footer', 'diero_admin_footer' );
add_action( 'wp_ajax_delete_attachment', 'diero_delete_attachment' );
add_action( 'wp_ajax_nopriv_delete_attachment', 'diero_delete_attachment' );
add_action( 'woocommerce_loaded', function () { remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20); }, PHP_INT_MAX );
add_action( 'wp_ajax_delete_user_item', 'diero_delete_user_item' );
add_action( 'wp_ajax_nopriv_delete_user_item', 'diero_delete_user_item' );
add_action( 'woocommerce_after_shop_loop_item_title' , 'action_woocommerce_after_shop_loop_item_title' );
add_action( 'woocommerce_product_query', 'diero_hide_category_from_shop' );

add_action( 'wp_ajax_add_user_item', 'diero_add_user_item' );
add_action( 'wp_ajax_nopriv_add_user_item', 'diero_add_user_item' );

add_filter( 'authenticate', 'diero_ban_user_login', 999, 3 );
add_filter( 'manage_users_columns', 'diero_manage_users_columns', 99, 1 );
add_filter( 'manage_users_custom_column', 'direo_manage_users_custom_column_user_type', 99, 3 );
add_filter( 'manage_users_custom_column', 'direo_manage_users_custom_column_messages_available', 99, 3 );
add_filter( 'woocommerce_add_to_cart_redirect', 'diero_redirect_checkout_add_cart' );
add_filter( 'wc_add_to_cart_message_html', '__return_false' );
add_filter( 'woocommerce_account_menu_items', 'diero_remove_my_account_tabs', 999 );
add_filter( 'body_class', 'diero_add_class_in_body' );
add_filter( 'gettext', 'diero_change_translate_text', 20 );
add_filter( 'shortcode_atts_products', 'diero_shortcode_atts_products', 10, 4 );
add_filter( 'woocommerce_shortcode_products_query', 'diero_woocommerce_shortcode_products_query', 10, 2 );
add_filter( 'woocommerce_is_purchasable', 'diero_woocommerce_is_purchasable', 10, 2 );
add_filter( 'woocommerce_sale_flash', '__return_false' );
add_filter( 'woocommerce_product_tabs', 'diero_remove_product_tabs' );

add_shortcode( 'diero_scammer_list_sc', 'diero_scammer_list' );

function direo_child_styles() {
	wp_enqueue_media();
	wp_enqueue_style( 'direo-child-style', get_stylesheet_uri() );
	wp_enqueue_style( 'datatable-style', get_stylesheet_directory_uri() . '/assets/css/jquery.dataTables.min.css' );
	wp_enqueue_script( 'datatable', get_stylesheet_directory_uri() .'/assets/js/jquery.dataTables.min.js' );
	wp_enqueue_script( 'custom', get_stylesheet_directory_uri() .'/assets/js/custom.js' );
	wp_localize_script( 'custom', 'customScriptObj', array( 'site_url' => site_url(), 'productCategories' => get_categories(array('hide_empty' => 0, 'taxonomy' => 'product_cat','exclude' => array(93, 94)))));
}

function direo_child_theme_setup() {
    load_child_theme_textdomain( 'direo', get_stylesheet_directory() . '/languages' );
}

function direo_ajax_callback_report_abuse_custom() {
	$data = array(
		'error' => 0,
	);

	if ( ! directorist_verify_nonce() ) {
		$data['error']   = 1;
		$data['message'] = __( 'Something is wrong! Please refresh and retry.', 'directorist' );
		wp_send_json( $data );
	}

	$listing_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$message    = ! empty( $_POST['message'] ) ? trim( sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) ) : '';

	if ( empty( $listing_id ) || get_post_type( $listing_id ) !== ATBDP_POST_TYPE ) {
		$data['error']   = 1;
		$data['message'] = __( 'Trying to report invalid listing.', 'directorist' );
		wp_send_json( $data );
	}

	if ( empty( $message ) ) {
		$data['error']   = 1;
		$data['message'] = __( 'Report message cannot be empty.', 'directorist' );
		wp_send_json( $data );
	}

	$mail_sent = direo_send_listing_report_email_to_admin( get_current_user_id(), $listing_id, $message );
	if ( ! $mail_sent ) {
		$data['error']   = 1;
		$data['message'] = __( 'Sorry! Please try again.', 'directorist' );
		wp_send_json( $data );
	}

	$data['message'] = __( 'Your message sent successfully.', 'directorist' );
	do_action( 'directorist_listing_reported', $listing_id );
	wp_send_json( $data );
}

function direo_send_listing_report_email_to_admin( $user_id, $listing_id, $report_message ) {
	$message         = esc_textarea( $report_message );
	$user            = get_user_by( 'id', $user_id );
	$site_name       = get_bloginfo( 'name' );
	$site_url        = get_bloginfo( 'url' );
	$listing_title   = get_the_title( $listing_id );
	$listing_url     = get_permalink( $listing_id );
	$listing_details = get_post( $listing_id );
	$listing_author  = $listing_details->post_author;
	$author_details  = get_user_by('id', $listing_author);
	$count_reports 	 = direo_get_report_count($listing_author);
	$disable_login 	 = get_user_meta( $listing_author, 'disable_login', true );
	$report_limit    = !empty( get_option('report_limit') ) ? get_option('report_limit') : 25;

	$placeholders = array(
		'{site_name}'     => $site_name,
		'{site_link}'     => sprintf( '<a href="%s">%s</a>', esc_url( $site_url ), $site_name ),
		'{site_url}'      => sprintf( '<a href="%s">%s</a>', esc_url( $site_url ), $site_url ),

		'{listing_title}' => $listing_title,
		'{listing_link}'  => sprintf( '<a href="%s">%s</a>', esc_url( $listing_url ), $listing_title ),
		'{listing_url}'   => sprintf( '<a href="%s">%s</a>', esc_url( $listing_url ), $listing_url ),

		'{sender_name}'   => $user->display_name,
		'{sender_email}'  => $user->user_email,
		'{message}'       => $message,
	);

	$admin_email = get_directorist_option( 'admin_email_lists' );
	if ( ! $admin_email || ! is_email( $admin_email ) ) {
		$admin_email = get_bloginfo( 'admin_email' );
	}

	$subject = __( '{site_name} Report Abuse via "{listing_title}"', 'directorist' );
			$subject = strtr( $subject, $placeholders );

	$message = __( 'Dear '. $author_details->display_name .',<br /><br />This is an email abuse report for your listing at {listing_url}.<br /><br />Name: {sender_name}<br />Email: {sender_email}<br />Message: {message}', 'directorist' );
	$message = strtr( $message, $placeholders );
	$message = atbdp_email_html( $subject, $message );

	$message1 = __( 'Dear Administrator,<br /><br />This is an email abuse report for a listing at {listing_url}.<br /><br />Name: {sender_name}<br />Email: {sender_email}<br />Message: {message}', 'directorist' );
	$message1 = strtr( $message1, $placeholders );
	$message1 = atbdp_email_html( $subject, $message1 );

	$headers  = "From: {$user->display_name} <{$user->user_email}>\r\n";
	$headers .= "Reply-To: {$user->user_email}\r\n";

	update_user_meta( $listing_author, 'total_entries_of_reports', $count_reports + 1 );
	$count_reports 	 = direo_get_report_count($listing_author);

	if((int)$count_reports == (int)$report_limit) {
		update_user_meta( $listing_author, 'disable_login', 1 );
		$sessions = WP_Session_Tokens::get_instance( $listing_author );
		$sessions->destroy_all();
	}

	direo_send_report_email($author_details->user_email, $subject, $message, $headers);
	return direo_send_report_email($admin_email, $subject, $message1, $headers);
}

function direo_send_report_email($email, $subject, $message, $headers) {
	return ATBDP()->email->send_mail( $email, $subject, $message, $headers );
}

function direo_get_report_count($listing_author) {
	return get_user_meta( $listing_author, 'total_entries_of_reports', true );
}

function direo_custom_settings() {
    register_setting( 'general', 'report_limit' );
    register_setting( 'general', 'seller_icon' );
    register_setting( 'general', 'seller_icon_large' );
    register_setting( 'general', 'dispensary_icon' );
    register_setting( 'general', 'dispensary_icon_large' );
    register_setting( 'general', 'seller_default_product_id' );
    register_setting( 'general', 'dispensary_default_product_id' );

    add_settings_field(
    	'report_limit', 
        'User Report Limit', 
        'direo_custom_settings_callback',
        'general', 
        'default', 
        array( 
            'id' => 'report_limit', 
            'option_name' => 'report_limit'
        )
    );

    add_settings_field(
    	'seller_icon', 
        'Seller Icon', 
        'diero_seller_icon_callback',
        'general', 
        'default', 
        array( 
            'id' => 'seller_icon', 
            'option_name' => 'seller_icon'
        )
    );

    add_settings_field(
    	'seller_icon_large', 
        'Seller Icon large', 
        'diero_seller_icon_large_callback',
        'general', 
        'default', 
        array( 
            'id' => 'seller_icon_large', 
            'option_name' => 'seller_icon_large'
        )
    );

    add_settings_field(
    	'dispensary_icon', 
        'Dispensary Icon', 
        'diero_dispensary_icon_callback',
        'general', 
        'default', 
        array( 
            'id' => 'dispensary_icon', 
            'option_name' => 'dispensary_icon'
        )
    );

    add_settings_field(
    	'dispensary_icon_large', 
        'Dispensary Icon Large', 
        'diero_dispensary_icon_large_callback',
        'general', 
        'default', 
        array( 
            'id' => 'dispensary_icon_large', 
            'option_name' => 'dispensary_icon_large'
        )
    );

    add_settings_field(
    	'seller_default_product_id', 
        'Seller Default Product ID', 
        'seller_default_product_callback',
        'general', 
        'default', 
        array( 
            'id' => 'seller_default_product_id', 
            'option_name' => 'seller_default_product_id'
        )
    );

    add_settings_field(
    	'dispensary_default_product_id', 
        'Dispensary Default Product ID', 
        'dispensary_default_product_callback',
        'general', 
        'default', 
        array( 
            'id' => 'dispensary_default_product_id', 
            'option_name' => 'dispensary_default_product_id'
        )
    );
}

function seller_default_product_callback( $val ) {
    $id = $val['id'];
    $option_name = $val['option_name'];
    ?>
    <input type="number" name="<?php echo esc_attr( $option_name ) ?>" id="<?php echo esc_attr( $id ) ?>" value="<?php echo esc_attr( get_option( $option_name ) ) ?>" />
    <p class="description"><?php _e( "Add Package ID (Product ID) of the package. This package will be assigned to seller user while registration.", 'directorist' ); ?></p>
    <?php
}

function dispensary_default_product_callback( $val ) {
    $id = $val['id'];
    $option_name = $val['option_name'];
    ?>
    <input type="number" name="<?php echo esc_attr( $option_name ) ?>" id="<?php echo esc_attr( $id ) ?>" value="<?php echo esc_attr( get_option( $option_name ) ) ?>" />
    <p class="description"><?php _e( "Add Package ID (Product ID) of the package. This package will be assigned to dispensary user while registration.", 'directorist' ); ?></p>
    <?php
}

function direo_custom_settings_callback( $val ) {
    $id = $val['id'];
    $option_name = $val['option_name'];
    ?>
    <input type="number" name="<?php echo esc_attr( $option_name ) ?>" id="<?php echo esc_attr( $id ) ?>" value="<?php echo esc_attr( get_option( $option_name ) ) ?>" />
    <p class="description"><?php _e( "Here, you can mention the threshold at which a user's account will be deactivated. If you didn't specify the value, it will be set to 25 by default.", 'directorist' ); ?></p>
    <?php
}

function diero_seller_icon_callback( $val ) {
    $id = $val['id'];
    $option_name = $val['option_name'];
    ?>
    <input type="text" name="<?php echo esc_attr( $option_name ) ?>" id="<?php echo esc_attr( $id ) ?>" value="<?php echo esc_attr( get_option( $option_name ) ) ?>" placeholder="<?php _e( "Image URL", 'christian-grace' ); ?>"/><span><a href="javascript:void(0)" class="upload-icon button button-primary"data-upload-type="seller_icon"><?php _e( "Select Image", 'directorist' ); ?></a></span>
    <p class="description"><?php _e( "Enter the image URL or select the image", 'directorist' ); ?></p>

    <?php if(!empty($option_name)) { ?>
    <img src="<?php echo esc_attr( get_option( $option_name ) ) ?>" width="10%">
    <?php
	}
}

function diero_seller_icon_large_callback( $val ) {
    $id = $val['id'];
    $option_name = $val['option_name'];
    ?>
    <input type="text" name="<?php echo esc_attr( $option_name ) ?>" id="<?php echo esc_attr( $id ) ?>" value="<?php echo esc_attr( get_option( $option_name ) ) ?>" placeholder="<?php _e( "Image URL", 'christian-grace' ); ?>"/><span><a href="javascript:void(0)" class="upload-icon button button-primary"data-upload-type="seller_icon_large"><?php _e( "Select Image", 'directorist' ); ?></a></span>
    <p class="description"><?php _e( "Enter the image URL or select the image", 'directorist' ); ?></p>

    <?php if(!empty($option_name)) { ?>
    <img src="<?php echo esc_attr( get_option( $option_name ) ) ?>" width="10%">
    <?php
	}
}

function diero_dispensary_icon_callback( $val ) {
    $id = $val['id'];
    $option_name = $val['option_name'];
    ?>
    <input type="text" name="<?php echo esc_attr( $option_name ) ?>" id="<?php echo esc_attr( $id ) ?>" value="<?php echo esc_attr( get_option( $option_name ) ) ?>" placeholder="<?php _e( "Image URL", 'christian-grace' ); ?>"/><span><a href="javascript:void(0)" class="upload-icon button button-primary"data-upload-type="dispensary_icon"><?php _e( "Select Image", 'directorist' ); ?></a></span>
    <p class="description"><?php _e( "Enter the image URL or select the image", 'directorist' ); ?></p>

    <?php if(!empty($option_name)) { ?>
    <img src="<?php echo esc_attr( get_option( $option_name ) ) ?>" width="10%">
    <?php
	}
}

function diero_dispensary_icon_large_callback( $val ) {
    $id = $val['id'];
    $option_name = $val['option_name'];
    ?>
    <input type="text" name="<?php echo esc_attr( $option_name ) ?>" id="<?php echo esc_attr( $id ) ?>" value="<?php echo esc_attr( get_option( $option_name ) ) ?>" placeholder="<?php _e( "Image URL", 'christian-grace' ); ?>"/><span><a href="javascript:void(0)" class="upload-icon button button-primary"data-upload-type="dispensary_icon_large"><?php _e( "Select Image", 'directorist' ); ?></a></span>
    <p class="description"><?php _e( "Enter the image URL or select the image", 'directorist' ); ?></p>

    <?php if(!empty($option_name)) { ?>
    <img src="<?php echo esc_attr( get_option( $option_name ) ) ?>" width="10%">
    <?php
	}
}

function diero_admin_script( $hook ) {
    wp_enqueue_media();
}

function diero_admin_footer() {
	?>
	<script type="text/javascript">
		jQuery(document).on("click",".upload-icon",function() {
			var uploadType = jQuery(this).data('upload-type');
	    	openMediaModal(uploadType);
	   	});

		function openMediaModal(textURL) {
	   		var file_frame = '', attachment = '';
	        if ( file_frame ) { file_frame.open(); return; }

	        file_frame = wp.media.frames.file_frame = wp.media({
	            title: jQuery( this ).data( 'Select Item Images' ),
	            button: {
	                text: jQuery( this ).data( 'Select Image' ),
	            },
	            library: {
			       	type: ['image']
			    },
	            multiple: false,
	        });

	        file_frame.on( 'select', function() {
	            attachment = file_frame.state().get('selection').first().toJSON();
	            jQuery(`#${textURL}`).val(attachment.url);
	        });

	        file_frame.open();
	   	}
	</script>
	<?php
}

function direo_remove_filters_actions( $hook_name = '', $method_name = '', $priority = 0 ) {
    global $wp_filter;
    if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
        return false;
    }
    foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
        if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
            if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && $filter_array['function'][1] == $method_name ) {
                if ( is_a( $wp_filter[ $hook_name ], 'WP_Hook' ) ) {
                    unset( $wp_filter[ $hook_name ]->callbacks[ $priority ][ $unique_id ] );
                } else {
                    unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
                }
            }
        }
    }
    return false;
}

function direo_ajaxlogin_custom() {
	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-login-nonce', 'security' );

	$username       = $_POST['username'];
	$user_password  = $_POST['password'];
	$keep_signed_in = ! empty( $_POST['rememberme'] ) ? true : false;
	$user           = wp_authenticate( $username, $user_password );
	if ( is_wp_error( $user ) ) {
		echo json_encode(
			array(
				'loggedin' => false,
				'message'  => __(
					'Wrong username or password.',
					'direo'
				),
			)
		);
	} else {
		$disable_login 	 = get_user_meta( $user->ID, 'disable_login', true );

		if($disable_login != 1) {
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID, $keep_signed_in );
			echo json_encode(
				array(
					'loggedin' => true,
					'message'  => __(
						'Login successful, redirecting...',
						'direo'
					),
				)
			);
		} else {
			echo json_encode(
				array(
					'loggedin' => false,
					'message'  => __(
						'Your account has been deactivated.',
						'direo'
					),
				)
			);
		}

	}
	exit();
}

function diero_ajax_login_custom() {
	// Nonce is checked, get the POST data and sign user on
	$keep_signed_in = ( isset( $_POST['rememberme'] ) && ( $_POST['rememberme'] === 1 || $_POST['rememberme'] === '1' ) ) ? true : false;

	$info                  = array();
	$info['user_login']    = ( ! empty( $_POST['username'] ) ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
	$info['user_password'] = ( ! empty( $_POST['password'] ) ) ? $_POST['password'] : ''; // phpcs:ignore
	$info['remember']      = $keep_signed_in;

	$user_signon = wp_signon( $info, $keep_signed_in );
	if ( is_wp_error( $user_signon ) ) {
		echo json_encode(
			array(
				'loggedin' => false,
				'message'  => __( 'Wrong username or password.', 'directorist' ),
			)
		);
	} else {
		$disable_login 	 = get_user_meta( $user_signon->ID, 'disable_login', true );

		if($disable_login != 1) {
			wp_set_current_user( $user_signon->ID );

			echo json_encode(
				array(
					'loggedin' => true,
					'message'  => __( 'Login successful, redirecting...', 'directorist' ),
				)
			);
		} else {
			echo json_encode(
				array(
					'loggedin' => false,
					'message'  => __( 'Your account has been deactivated.', 'directorist' ),
				)
			);
		}

	}

	die();
}

function diero_ban_user_login( $user, $username, $password ) {
    if ( $user instanceof WP_User ) {
    	$disable_login 	 = get_user_meta( $user->ID, 'disable_login', true );

        if ( (int)$disable_login === 1 && $GLOBALS['pagenow'] === 'wp-login.php') {
            return new WP_Error( 'user_banned', 'Your account has been deactivated.' );
        }
    }
    return $user;
}

function diero_my_cpts_scammers_list() {
	$labels = [
		"name" => __( "Scammers List", "direo" ),
		"singular_name" => __( "Scammer", "direo" ),
	];

	$args = [
		"label" => __( "Scammers List", "direo" ),
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "scammers-list", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title" ],
		"show_in_graphql" => false,
	];

	register_post_type( "scammers-list", $args );
}

function diero_scammer_list( $atts ) {
	$table = '';
	$scammer_list = '';
	ob_start();

	if(is_user_logged_in()) {
		$table .= '<div class="scammer-table-wrapper">';
		$table .= '<button class="btn btn-sm btn-icon btn-gradient btn-gradient-two icon-left add-new-scammer">Add New Scammer</button><br/><br/>';
		$table .= '<table id="scammer-table" class="row-border">';
		$table .= '<thead>';
		$table .= '<th>'. __( 'Scammer Alias', 'directorist' ) .'</th>';
		$table .= '<th>'. __( 'Region', 'directorist' ) .'</th>';
		$table .= '<th>'. __( 'Known Contact Info', 'directorist' ) .'</th>';
		$table .= '</thead>';
		$table .= '</tbody>';

		$args = array(
	  		'numberposts' => 10,
	  		'post_type'   => 'scammers-list',
	  		'status' => 'publish',
		);

		$scammers = get_posts( $args );

		if ( $scammers ) { 
			foreach ( $scammers as $post ) : 
				setup_postdata( $post );
				$scammer_region = get_post_meta( $post->ID, 'scammer_region', true );
				$known_information = get_post_meta( $post->ID, 'known_information', true );
				$comments = get_post_meta( $post->ID, 'comments', true );

				$table .= '<tr>';
					$table .= '<td>'. $post->post_title .'</td>';
					$table .= '<td>'. $scammer_region .'</td>';
					$table .= '<td>'. $known_information .'</td>';
				$table .= '</tr>';
			endforeach;

			$table .= '</tbody>';
			$table .= '</table>';
			$table .= '</div>';

			wp_reset_postdata();
		} else {
			_e( 'There are no scammers', 'directorist' );
		}

		echo $table;
	}

	$scammer_list = ob_get_clean();
	return $scammer_list;
}

function diero_add_scammer() {
	$params = array();
	parse_str($_POST['formData'], $params);

	$scammer_name = $params['scammer_name'];
	$scammer_region = $params['scammer_region'];
	$known_information = $params['known_information'];
	$comments = $params['comments'];

	if ( 0 === post_exists( sanitize_text_field($scammer_name) ) ) {
		$post_data = array(
		    'post_title' 	=> sanitize_text_field($scammer_name),
		    'post_type' 	=> 'scammers-list',
		    'post_status' 	=> 'draft'
		);

		$post_id = wp_insert_post( $post_data );

		if($post_id) {
			update_post_meta( $post_id, 'scammer_region', sanitize_text_field($scammer_region) );
			update_post_meta( $post_id, 'known_information', $known_information );
			update_post_meta( $post_id, 'comments', $comments );
			$response = array('status' => 'success', 'msg' => __( 'Scammer has been submitted.', 'directorist') );
		} else {
			$response = array('status' => 'failed', 'msg' => __( 'Something went wrong. Please try again.', 'directorist') );
		}
	} else {
		$response = array('status' => 'success', 'msg' => __( 'Scammer is already added to the list.', 'directorist') );
	}

	wp_send_json($response);
}

function direo_blockusers_init() {
	global $post, $wp_query;
	$post_slug = $post->post_name;
    $userHasSubscription = diero_has_subscription();

    if(!current_user_can( 'administrator' )) {
	    if( ($post_slug == 'scammers' && !is_user_logged_in()) || (is_shop()) ) {
		  	$wp_query->set_404();
		  	status_header( 404 );
		  	get_template_part( 404 );
	        exit;
	    } else if ( $post_slug == 'edit-location' && is_user_logged_in() && empty($userHasSubscription) ) {
	    	wp_redirect(site_url('not-allowed'));
	    }

	    if(empty($userHasSubscription)) {
	    	update_user_meta( get_current_user_id(), 'messages_available', 0 );
	    }
	}
}

function direo_user_registration() {
	$ATBDP_User = new \ATBDP_User();
	$new_user_registration = get_directorist_option( 'new_user_registration', true );
	if ( ! directorist_verify_nonce() || ! isset( $_POST['atbdp_user_submit'] ) || ! $new_user_registration ) {
		return;
	}

	// if the form is submitted then save the form
	$require_website      = get_directorist_option( 'require_website_reg', 0 );
	$display_website      = get_directorist_option( 'display_website_reg', 1 );
	$display_fname        = get_directorist_option( 'display_fname_reg', 1 );
	$require_fname        = get_directorist_option( 'require_fname_reg', 0 );
	$display_lname        = get_directorist_option( 'display_lname_reg', 1 );
	$require_lname        = get_directorist_option( 'require_lname_reg', 0 );
	$display_password     = get_directorist_option( 'display_password_reg', 1 );
	$require_password     = get_directorist_option( 'require_password_reg', 0 );
	$display_user_type    = get_directorist_option(  'display_user_type', 0   );
	$display_bio          = get_directorist_option( 'display_bio_reg', 1 );
	$require_bio          = get_directorist_option( 'require_bio_reg', 0 );
	$registration_privacy = get_directorist_option( 'registration_privacy', 1 );
	$terms_condition      = get_directorist_option( 'regi_terms_condition', 1 );

	/**
	 * It fires before processing a submitted registration from the front end
	 * @param array $_POST the array containing the submitted listing data.
	 * @since 4.4.0
	 * */
	do_action( 'atbdp_before_processing_submitted_user_registration', $_POST ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

	$username       = ! empty( $_POST['username'] ) ? directorist_clean( wp_unslash( $_POST['username'] ) ) : '';
	$password       = ! empty( $_POST['password'] ) ? $_POST['password'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	$email          = ! empty( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$website        = ! empty( $_POST['website'] ) ? directorist_clean( wp_unslash( $_POST['website'] ) ) : '';
	$first_name     = ! empty( $_POST['fname'] ) ? directorist_clean( wp_unslash( $_POST['fname'] ) ) : '';
	$last_name      = ! empty( $_POST['lname'] ) ? directorist_clean( wp_unslash( $_POST['lname'] ) ) : '';
	$user_type      = ! empty( $_POST['user_type'] ) ? directorist_clean( wp_unslash( $_POST['user_type'] ) ) : '';
	$bio            = ! empty( $_POST['bio'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bio'] ) ) : '';
	$privacy_policy = ! empty( $_POST['privacy_policy'] ) ? directorist_clean( wp_unslash( $_POST['privacy_policy'] ) ) : '';
	$t_c_check      = ! empty( $_POST['t_c_check'] ) ? directorist_clean( wp_unslash( $_POST['t_c_check'] ) ) : '';

	//password validation
	if ( ! empty( $require_password ) && ! empty( $display_password ) && empty( $password ) ) {
		$password_validation = 'yes';
	}

	//website validation
	if ( ! empty( $require_website ) && ! empty( $display_website ) && empty( $website ) ) {
		$website_validation = 'yes';
	}

	//first name validation
	if ( ! empty( $require_fname ) && ! empty( $display_fname ) && empty( $first_name ) ) {
		$fname_validation = 'yes';
	}

	//last name validation
	if ( ! empty( $require_lname ) && !empty( $display_lname ) && empty( $last_name ) ) {
		$lname_validation = 'yes';
	}

	//bio validation
	if(!empty($require_bio) && !empty($display_bio) && empty($bio)){
		$bio_validation = 'yes';
	}
	if( ! empty( $display_user_type ) && empty( $user_type) ) {
		$user_type_validation = 'yes';
	}
	//privacy validation
	if(!empty($registration_privacy) && empty($privacy_policy)){
		$privacy_validation = 'yes';
	}
	//terms & conditions validation
	if(!empty($terms_condition) && empty($t_c_check)){
		$t_c_validation = 'yes';
	}
	// validate all the inputs
	$validation = $ATBDP_User->registration_validation( $username, $password, $email, $website, $first_name, $last_name, $bio, $user_type, $privacy_policy, $t_c_check );
	if ('passed' !== $validation){
		if (empty( $username ) || !empty( $password_validation ) || empty( $email ) || !empty($website_validation) || !empty($fname_validation) || !empty($lname_validation) || !empty($bio_validation)|| !empty($privacy_validation)|| !empty($t_c_validation)){
			wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => 1)));
			exit();
		}elseif(email_exists($email)){
			wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => 2)));
			exit();
		}elseif(!empty( $username ) && 4 > strlen( $username ) ){
			wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => 3)));
			exit();
		}elseif(!empty( $username ) && preg_match('/\s/',$username) ){
			wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => 7)));
			exit();
		}elseif( username_exists( $username )){
			wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => 4)));
			exit();
		}elseif(! empty( $password ) && 5 > strlen( $password )){
			wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => 5)));
			exit();
		}elseif(!is_email( $email )){
			wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => 6)));
			exit();
		} elseif( ! empty( $user_type_validation ) ) {
			wp_safe_redirect(ATBDP_Permalink::get_registration_page_link(array('errors' => 8)));
			exit();
		}
	}

	// sanitize user form input
	global $username, $password, $email, $website, $first_name, $last_name, $bio;
	$username   =   directorist_clean( wp_unslash( $_POST['username'] ) );
	if (empty($display_password)){
		$password   =   wp_generate_password( 12, false );
	}elseif (empty($_POST['password'])){
		$password   =   wp_generate_password( 12, false );
	}else{
		$password   =  $_POST['password']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	}
	$email            =   !empty($_POST['email']) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$website          =   !empty($_POST['website']) ? directorist_clean( $_POST['website'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	$first_name       =   !empty($_POST['fname']) ? directorist_clean( wp_unslash( $_POST['fname'] ) ) : '';
	$last_name        =   !empty($_POST['lname']) ? directorist_clean( wp_unslash( $_POST['lname'] ) ) : '';
	$user_type        =   !empty($_POST['user_type']) ? directorist_clean( wp_unslash( $_POST['user_type'] ) ) : '';
	$bio              =   !empty($_POST['bio']) ? sanitize_textarea_field( wp_unslash( $_POST['bio'] ) ) : '';
	$previous_page    =   !empty($_POST['previous_page']) ? directorist_clean( $_POST['previous_page'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
	// call @function complete_registration to create the user
	// only when no WP_error is found
	$user_id = direo_complete_registration($username, $password, $email, $website, $first_name, $last_name, $bio);
	if ($user_id && !is_wp_error( $user_id )) {
		$redirection_after_reg = get_directorist_option( 'redirection_after_reg');
		$auto_login = get_directorist_option( 'auto_login' );
		/*
		* @since 6.3.0
		* If fires after completed user registration
		*/
		do_action('atbdp_user_registration_completed', $user_id);
		update_user_meta($user_id, '_atbdp_generated_password', $password);
		update_user_meta($user_id, '_atbdp_privacy', $privacy_policy);
		update_user_meta($user_id, '_user_type', $user_type);
		update_user_meta($user_id, '_atbdp_terms_and_conditions', $t_c_check);
		// user has been created successfully, now work on activation process
		wp_new_user_notification($user_id, null, 'admin'); // send activation to the admin

		if($user_type == 'author') {
			$seller_package = get_option('seller_default_product_id');

			if(!empty($seller_package) && get_post_type($seller_package) == 'product') {
				create_auto_subscription($user_id, $seller_package);
			}
		} else if($user_type == 'dispensary') {
			$dispensary_package = get_option('dispensary_default_product_id');

			if(!empty($dispensary_package) && get_post_type($dispensary_package) == 'product') {
				create_auto_subscription($user_id, $dispensary_package);
			}
		}

		ATBDP()->email->custom_wp_new_user_notification_email($user_id);
		if( ! empty( $auto_login ) ) {
			wp_set_current_user( $user_id, $email );
			wp_set_auth_cookie( $user_id );
		}
		// wp_get_referer();
		if( ! empty( $redirection_after_reg ) ) {
			wp_safe_redirect( esc_url_raw( ATBDP_Permalink::get_reg_redirection_page_link( $previous_page ) ) );
		} else {
			wp_safe_redirect( esc_url_raw( ATBDP_Permalink::get_registration_page_link( array( 'registration_status' => true ) ) ) );
		}
		exit();
	} else {
		wp_safe_redirect( esc_url_raw( ATBDP_Permalink::get_registration_page_link(array('errors' => true ) ) ) );
		exit();
	}
}

function direo_complete_registration($username, $password, $email, $website, $first_name, $last_name, $bio) {
	global $reg_errors, $username, $password, $email, $website, $first_name, $last_name,  $bio;
	$reg_errors = new WP_Error;
	if ( 1 > count( $reg_errors->get_error_messages() ) ) {
		$userdata = array(
			'user_login'  => $username,
			'user_email'  => $email,
			'user_pass'   => $password,
			'user_url'    => $website,
			'first_name'  => $first_name,
			'last_name'   => $last_name,
			'description' => $bio,
			'role'        => 'subscriber', // @since 7.0.6.3
		);

		return wp_insert_user( $userdata ); // return inserted user id or a WP_Error
	}

	return false;
}

function diero_manage_users_columns( $columns ) {
	$columns['user_type'] = esc_html__( 'User Type', 'directorist' );
	$columns['user_messages'] = esc_html__( 'Messages Available', 'directorist' );
	return $columns;
}

function direo_manage_users_custom_column_user_type( $column_value, $column_name, $user_id ) {
	if ( $column_name !== 'user_type' ) {
		return $column_value;
	}

	$user_type = (string) diero_get_current_user_meta($user_id, '_user_type');

	if ( 'author' === $user_type ) {
		$column_value = esc_html__( 'Seller', 'directorist' );
	} else if ( 'general' === $user_type ) {
		$column_value = esc_html__( 'Buyer', 'directorist' );
	} else if ( 'dispensary' === $user_type ) {
		$column_value = esc_html__( 'Dispensary', 'directorist' );
	} else if ( 'become_author' === $user_type ) {
		$author_pending = wp_kses_post( "<p>Author <span style='color:red;'>( Pending )</span></p>" );
		$approve        = wp_kses_post( "<a href='' id='atbdp-user-type-approve' style='color: #388E3C' data-userId={$user_id} data-nonce=". wp_create_nonce( 'atbdp_user_type_approve' ) ."><span>Approve </span></a> | " );
		$deny           = wp_kses_post( "<a href='' id='atbdp-user-type-deny' style='color: red' data-userId={$user_id} data-nonce=". wp_create_nonce( 'atbdp_user_type_deny' ) ."><span>Deny</span></a>" );
		$column_value   = wp_kses_post( "<div class='atbdp-user-type' id='user-type-". $user_id ."'>" .$author_pending . $approve . $deny . "</div>" );
	}

	return $column_value;
}

function direo_manage_users_custom_column_messages_available( $column_value, $column_name, $user_id ) {
	if ( $column_name !== 'user_messages' ) {
		return $column_value;
	}

	if( user_can( $user_id, 'manage_options' ) ) {
		$column_value = __('Unlimited', 'directorist');
	} else {
		$user_type = (string) diero_get_current_user_meta($user_id, '_user_type');
		if ( 'author' === $user_type || 'dispensary' === $user_type ) { 
			$diero_has_unlimited_msg = diero_has_subscription_for_unlimited_messages($user_id);
			if( !empty($diero_has_unlimited_msg) ) {
				$column_value = __('Unlimited', 'directorist');
			} else {
				$column_value = diero_get_current_user_meta( $user_id, 'messages_available' );
			}
		} else {
			$column_value = __('Free', 'directorist');
		}
	}

	return $column_value;
}

function diero_custom_css() {
	$seller_icon_url = !empty(get_option('seller_icon')) ? get_option('seller_icon') : get_stylesheet_directory_uri() .'/assets/img/seller-icon.png';
	$dispensary_icon_url = !empty(get_option('dispensary_icon')) ? get_option('dispensary_icon') : get_stylesheet_directory_uri() .'/assets/img/dispensary-icon.png';
	$seller_icon_large_url = !empty(get_option('seller_icon_large')) ? get_option('seller_icon_large') : get_stylesheet_directory_uri() .'/assets/img/seller-icon-large.png';
	$dispensary_icon_large_url = !empty(get_option('dispensary_icon_large')) ? get_option('dispensary_icon_large') : get_stylesheet_directory_uri() .'/assets/img/dispensary-icon-large.png';
	$spinner_image = get_stylesheet_directory_uri() .'/assets/img/spinner.gif';
	?>
	<style type="text/css">
		.directorist-signle-listing-top { margin-bottom: 0 !important; }
		.seller-icon .atbd_map_shape span:before, .dispensary-icon .atbd_map_shape span:before { display: none !important; }
		i.seller-icon, span.seller-icon, .seller-icon .atbd_map_shape span { background-image: url('<?php echo $seller_icon_url; ?>') !important; background-size: contain; background-repeat: no-repeat; }
		i.dispensary-icon, span.dispensary-icon, .dispensary-icon .atbd_map_shape span { background-image: url('<?php echo $dispensary_icon_url; ?>') !important; background-size: contain; background-repeat: no-repeat; }
		span.seller-icon.larger-icon, .seller-icon.larger-icon .atbd_map_shape span { background-image: url('<?php echo $seller_icon_large_url; ?>') !important; }
		span.dispensary-icon.larger-icon, .dispensary-icon.larger-icon .atbd_map_shape span { background-image: url('<?php echo $dispensary_icon_large_url; ?>') !important; }
		.atbd_map_shape { background: transparent !important; }
		.atbd_map_shape:before { bottom: -6px !important; border-top-color: black !important; }
		.atbd_map_shape .larger-icon, .seller-icon.larger-icon .atbd_map_shape span, .dispensary-icon.larger-icon .atbd_map_shape span { height: 49px !important; width: 100px !important; }
		li.directorist-tab-nav--content-link:nth-child(2), li.directorist-tab-nav--content-link:nth-child(3), li.directorist-tab-nav--content-link:nth-child(4) { display: none; }
		.woocommerce .woocommerce-MyAccount-navigation { display: none; }
		.woocommerce .woocommerce-MyAccount-content { width: 100% !important; }
		#wc_subscription .directorist-notfound-subscription { padding: 22px; }
		.wps_sfw_account_col.woocommerce-orders-table__cell.woocommerce-orders-table__cell-order-status.danger { color: red; }
		.wps_sfw_account_col.woocommerce-orders-table__cell.woocommerce-orders-table__cell-order-status.success { color: green; }
		button.directorist-chat-submit.disabled { background: #d3dce6 !important; }
		.stripe-card-group, div#stripe-exp-element, div#stripe-cvc-element { width: 100%; }
		.woocommerce-checkout #payment div.form-row { padding: 0em; }
		.message-packages { margin-top: 15px; }
		#mc4wp-form-1 input[type="email"] { width: 100% !important; max-width: 100% !important; border-radius: 15px !important; }
		input.subscribe-btn-footer { background: linear-gradient(to right, var(--color-primary), var(--color-secondary)) !important; color: #ffffff !important; border-radius: 25px !important; }
		#mc4wp-form-1 input[type="email"] { border-radius: 25px !important; height: 48px; }
		#mc4wp-form-1 .subscribe-btn-footer { position: absolute; right: 5px; top: 5px; }
		#mc4wp-form-1 .mc4wp-form-fields { position: relative; }
		#dashboard_my_listings .directorist-user-dashboard-tab__nav { display: none; }
		#dashboard_my_listings .directorist-user-dashboard-tabcontent { margin-top: 0; }
		.directorist-listing-table-listing-info { align-items: center; }
		.directorist-dashboard-mylistings .directorist-actions { float: left; margin-top: 15px; }
		span.directorist_badge.dashboard-badge { margin-left: 5px; }
		.directorist-add-listing-form__action .directorist-form-submit__btn.atbd_loading:after { top: 0 !important; }
		span.diero-message-limit { font-weight: 800; }
		.directory_regi_btn p a { color: var(--color-primary) !important; }
		span.diero-messages-left { font-size: 11px; }
		h3.items-table-title { padding: 25px; }
		.add-new-item { margin-left: 10px; }
		.directorist-user-dashboard { padding-top: 180px; }
		.gallery img { width: 150px; margin: 20px; }
		button#upload_item_images { width: 100%; border: 1px solid #EBECEF; }
		.selected-item-images { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); grid-gap: 20px; align-items: stretch; }
		.selected-item-images img { border: 1px solid #ccc; box-shadow: 2px 2px 6px 0px rgb(0 0 0 / 30%); max-width: 100%; }
		.image_wrapper_remove { display: block; text-align: center; margin-top: 5px; }
		.user-items-products .woocommerce.columns-4 { margin: 35px; }
		.apf-main-col .apf-box-container.apf-product-box-container:nth-child(3) { display: none; }
		.atbdp.atbd_author_info_widget a.btn.btn-primary { display: none !important; }
		.directorist-card.directorist-card-general-section .product-meta { display: none; }
		.menu--transparent.headroom--not-top { z-index: 1000000 !important; }
		.directorist-favourite-items-wrap .directorist-dashboard-items-list__single .directorist-listing-img { margin-right: 0; display: none; }
		.directorist-listing-single .directorist-listing-single__thumb .directorist-thumnail-card .directorist-thumnail-card-front-img { object-fit: contain; }
		.directorist-thumb-listing-author.directorist-alignment-right, .directorist-basic-search-fields-each:first-child, .directorist-listing-category, a.directorist-dropdown__links--single.sort-price-asc, a.directorist-dropdown__links--single.sort-price-desc { display: none; }
		.woocommerce-checkout.processing .blockUI.blockOverlay { background-position: center 50% !important; background-repeat: no-repeat !important; position: fixed !important;  }
		.form-group.product-cat ul { padding: 0px; list-style: none; }
        i.directorist-icon-mask { display: none; }
	</style>
	<?php
}

function diero_get_user_listing_count() {
	$listingData = new WP_Query( array( 
		'post_type' => 'at_biz_dir',
		'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'), 
		'author' => get_current_user_id() ) );

	if(!empty($listingData)) {
		$userPostCount = $listingData->found_posts;
	} else {
		$userPostCount = 0;
	}

	return $userPostCount;
}
 
function diero_redirect_checkout_add_cart() {
   return wc_get_checkout_url();
}

function diero_remove_my_account_tabs($items) {
    unset($items['dashboard']);
    unset($items['orders']);
    unset($items['downloads']);
    unset($items['edit-address']);
    unset($items['payment-methods']);
    unset($items['edit-account']);
    unset($items['wps_subscriptions']);
    unset($items['customer-logout']);
    return $items;
}

function woocommerce_empty_cart_url() {
  	global $woocommerce;
	
	if ( isset($_GET['empty-cart']) && isset($_GET['package-to-purchase']) ) {
		$woocommerce->cart->empty_cart(); 
		wp_redirect('?add-to-cart=' . $_GET['package-to-purchase'] );
	}
}

function diero_live_chat_custom() {
	$diero_has_unlimited_msg = diero_has_subscription_for_unlimited_messages(get_current_user_id());
	$diero_has_subscription = diero_has_subscription();
	$is_messages_available = diero_get_messages_count();
	$user_type = diero_get_current_user_meta(null, '_user_type');

	if( !empty($diero_has_unlimited_msg) ) {
		$allow = true;
		$updateMessage = false;
	} else {
		if(!empty($diero_has_subscription) && $is_messages_available != 0 && $user_type != 'general') {
			$allow = true;
			$updateMessage = true;
		} else if ($user_type == 'general' || current_user_can('administrator') || $user_type == 'become_author') {
			$allow = true;
			$updateMessage = false;
		} else {
			$allow = false;
			$updateMessage = false;
		}
	}

	if( $allow == true ) {
		$chatListing_id = !empty($_POST['chatListing_id']) ? sanitize_text_field($_POST['chatListing_id']) : '';
	    $chatAuthor_id = !empty($_POST['chatAuthor_id']) ? sanitize_text_field($_POST['chatAuthor_id']) : '';
	    $chatMsg = !empty($_POST['chatMsg']) ? sanitize_text_field($_POST['chatMsg']) : '';
	    $chat_listing_author = get_post_field('post_author', $chatListing_id);
	    if (!get_chat_by_user($chat_listing_author, $chatAuthor_id, $chatListing_id)->get_posts()) {
	        // fresh user so send email notification
	        if ($chatAuthor_id != $chat_listing_author) {
	            send_email_notification($chatListing_id, $chat_listing_author);
	        }
	    }
	    // process chat to database
	    $chat_id = wp_insert_post(array(
	        'post_content' => '',
	        'post_title' => 'Chat for ' . get_the_title($chatListing_id),
	        'post_status' => 'publish',
	        'post_type' => 'atbdp_chat',
	        'comment_status' => false,
	    ));
	    update_post_meta($chat_id, '_chatListing_id', $chatListing_id);
	    update_post_meta($chat_id, '_chatAuthor_id', $chatAuthor_id);
	    update_post_meta($chat_id, '_chatMsg', $chatMsg);
	    update_post_meta($chat_id, '_chat_listing_author', $chat_listing_author);
	    if ($chat_id) {
	    	if($updateMessage == true) {
	    		update_user_meta( get_current_user_id(), 'messages_available', (int) $is_messages_available - 1 );
	    	}
	        $data['status'] = 'success';
	        $data['listing_id'] = $chatListing_id;
	        $data['chat_author_id'] = $chatAuthor_id;
	        $data['image'] = get_avatar($chatAuthor_id, 32);
	        $data['remaining_messages'] = ($updateMessage == true) ? diero_get_messages_count() : 'Unlimited';
	    } else {
	        $data['status'] = 'fail';
	    }
	} else {
		$data['status'] = 'fail';
		$data['remaining_messages'] = ($updateMessage == true) ? diero_get_messages_count() : 'Unlimited';
		$data['reason'] = 'limit_over';
		$data['message'] = __( 'You do not have any active subscription(s) or your message limit is over.', 'directorist');
	}

    wp_send_json($data);
    die();
}

function diero_has_subscription() {
	$subsProductArr = array();
	$subsProducts = get_posts( array(
	    'post_type' => 'product',
	    'numberposts' => -1,
	    'post_status' => 'publish',
	    'tax_query' => array(
	        array(
	            'taxonomy' => 'product_cat',
	            'field' => 'slug',
	            'terms' => diero_get_current_user_meta(null, '_user_type'),
	            'operator' => 'IN',
			)
	    ),
    ));

    foreach ($subsProducts as $key => $product) { 
    	array_push($subsProductArr, $product->ID);
    }

    if(!empty($subsProductArr)) {
		return get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'wps_subscriptions',
			'post_status' => 'wc-wps_renewal',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'   => 'wps_customer_id',
					'value' => get_current_user_id(),
				),
				array(
					'key'   => 'wps_subscription_status',
					'value' => 'active',
				),
				array(
					'key'     => 'product_id',
					'value'   => $subsProductArr,
					'compare' => 'IN',
		        	'type'	  => 'NUMERIC'
				),
			),
		) );
    }
}

function diero_has_subscription_for_larger_map($author_id) {
	$subsProductArr = array();
	$subsProducts = get_posts( array(
	    'post_type' => 'product',
	    'numberposts' => -1,
	    'post_status' => 'publish',
	    'tax_query' => array(
	        array(
	            'taxonomy' => 'product_cat',
	            'field' => 'slug',
	            'terms' => diero_get_current_user_meta($author_id, '_user_type'),
	            'operator' => 'IN',
			)
	    ),
	    'meta_query' => array(
			array(
				'key'   => 'larger_icon',
				'value' => 1,
				'compare' => 'IN',
	        	'type'	  => 'NUMERIC'
			)
		),
    ));

    foreach ($subsProducts as $key => $product) { 
    	array_push($subsProductArr, $product->ID);
    }

    if(!empty($subsProductArr)) {
		return get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'wps_subscriptions',
			'post_status' => 'wc-wps_renewal',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'   => 'wps_customer_id',
					'value' => $author_id,
				),
				array(
					'key'   => 'wps_subscription_status',
					'value' => 'active',
				),
				array(
					'key'     => 'product_id',
					'value'   => $subsProductArr,
					'compare' => 'IN',
		        	'type'	  => 'NUMERIC'
				),
			),
		) );
    }
}

function diero_has_subscription_for_unlimited_messages($author_id) {
	$subsProductArr = array();
	$subsProducts = get_posts( array(
	    'post_type' => 'product',
	    'numberposts' => -1,
	    'post_status' => 'publish',
	    'tax_query' => array(
	        array(
	            'taxonomy' => 'product_cat',
	            'field' => 'slug',
	            'terms' => diero_get_current_user_meta(null, '_user_type'),
	            'operator' => 'IN',
			)
	    ),
	    'meta_query' => array(
			array(
				'key'   => 'unlimited_messages',
				'value' => 1,
				'compare' => 'IN',
	        	'type'	  => 'NUMERIC'
			)
		),
    ));

    foreach ($subsProducts as $key => $product) { 
    	array_push($subsProductArr, $product->ID);
    }

    if(!empty($subsProductArr)) {
    	return get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'wps_subscriptions',
			'post_status' => 'wc-wps_renewal',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'   => 'wps_customer_id',
					'value' => $author_id,
				),
				array(
					'key'   => 'wps_subscription_status',
					'value' => 'active',
				),
				array(
					'key'     => 'product_id',
					'value'   => $subsProductArr,
					'compare' => 'IN',
		        	'type'	  => 'NUMERIC'
				),
			),
		) );
    }
}

function diero_get_messages_count() {
	$count = diero_get_current_user_meta( get_current_user_id(), 'messages_available' );

	if(!empty($count)) {
		return $count;
	} else {
		return 0;
	}
}

function diero_woocommerce_order_status($order_id) {
	if ( ! $order_id ) return;

	$order = wc_get_order( $order_id );
	$items = $order->get_items();
	$customerId = $order->get_user_id();

	foreach ( $items as $item ) {
		if(get_post_meta( $item->get_product_id(), '_wps_sfw_product', true ) == 'yes') {
			$subscriptionId = get_post_meta( $order_id, 'wps_subscription_id', true );
			$productId 		= get_post_meta( $subscriptionId, 'product_id', true );
			update_messages($productId, $customerId, $order, $subscriptionId);
		} else {
			update_messages($item->get_product_id(), $customerId, $order, null);
		}
	}
}

function update_messages($productId, $customerId, $order, $subscriptionId) {
	$message_limit 	= get_post_meta( $productId, 'message_limit', true );
	$count 			= diero_get_current_user_meta( $customerId, 'messages_available' );
	
	$order->update_status( 'completed' );
	$order->save();
	update_user_meta( $customerId, 'messages_available', (int) $count + (int) $message_limit );

	if($subscriptionId != null) {
		apply_filters( 'wps_sfw_set_subscription_status', 'active', $subscriptionId );
	}
}

function diero_add_class_in_body( $classes ) {
	$diero_has_subscription = diero_has_subscription();
	$hasUnlimitedPackage = diero_has_subscription_for_unlimited_messages(get_current_user_id());
	$messageCount = diero_get_messages_count();

	if ( ( $messageCount == 0 || empty($diero_has_subscription) ) && empty($hasUnlimitedPackage) ) {
        $classes[] = 'message-limit-over';
    }

	return $classes;
}

function diero_add_custom_script() {
		get_template_part( 'template-parts/add-item-modal', 'modal' );
	?>

	<?php
	if( is_page( 'dashboard' ) ) :
	?>
		<script type="text/javascript">
			$(document).ready(function() {  
				if($('body').hasClass("message-limit-over")) {
					$('input[name="chatMsg"]').prop('disabled', true);
		            $('input[name="chatMsg"]').attr('placeholder', 'You do not have any active subscription(s) or your message limit is over.');
		            $('.directorist-chat-submit').prop('disabled', true);
		            $('.directorist-chat-submit').addClass('disabled');
				}
			});
		</script>
	<?php
	endif;

	?>
		<script type="text/javascript">
			jQuery("#show-chat-history-btn").click(function() {
				var $container = jQuery("html,body");
				var $scrollTo = jQuery('.directorist-chat-wrapper');

				jQuery('.directorist-chat-wrapper').click();
				jQuery('.directorist-chat-wrapper').addClass('active');
				jQuery('.directorist-client-chat-content-area').addClass('atbd-show');
				
				$container.animate({scrollTop: $scrollTo.offset().top - $container.offset().top + $container.scrollTop(), scrollLeft: 0}, 'slow'); 
			});
		</script>
	<?php
}

function diero_change_translate_text( $translated_text ) {
	if ( 'No chat history found as an author!'  === $translated_text ) {
		$translated_text = 'No chat history found!';
	} else if ( 'Listings'  === $translated_text ) {
		$translated_text = 'Location';
	}
	return $translated_text;
}

function diero_submit_listing() {
	$data = array();

	if ( ! directorist_verify_nonce() ) {
		$data['error']     = true;
		$data['error_msg'] = __( 'Something is wrong! Please refresh and retry.', 'directorist' );

		return wp_send_json( $data );
	}

	$info = wp_unslash( $_POST );

	/**
	 * It fires before processing a submitted listing from the front end
	 *
	 * @param array $_POST the array containing the submitted listing data.
	 * */
	do_action( 'atbdp_before_processing_submitted_listing_frontend', $info );

	$guest            = get_directorist_option( 'guest_listings', 0 );
	$featured_enabled = get_directorist_option( 'enable_featured_listing' );

	// data validation
	$directory              = ! empty( $info['directory_type'] ) ? sanitize_text_field( $info['directory_type'] ) : '';
	$submission_form_fields = array();
	$metas                  = array();

	if ( $directory ) {
		$term                   = get_term_by( ( is_numeric( $directory ) ? 'id' : 'slug' ), $directory, ATBDP_TYPE );
		$directory_type         = $term->term_id;
		$submission_form        = get_term_meta( $directory_type, 'submission_form_fields', true );
		$new_l_status           = get_term_meta( $directory_type, 'new_listing_status', true );
		$edit_l_status          = get_term_meta( $directory_type, 'edit_listing_status', true );
		$default_expiration     = get_term_meta( $directory_type, 'default_expiration', true );
		$preview_enable         = atbdp_is_truthy( get_term_meta( $directory_type, 'preview_mode', true ) );
		$submission_form_fields = $submission_form['fields'];
	}

	// isolate data
	$error = array();
	$dummy = array();

	$tag                       = ! empty( $info['tax_input']['at_biz_dir-tags'] ) ? ( $info['tax_input']['at_biz_dir-tags'] ) : array();
	$location                  = ! empty( $info['tax_input']['at_biz_dir-location'] ) ? ( $info['tax_input']['at_biz_dir-location'] ) : array();
	$admin_category_select     = ! empty( $info['tax_input']['at_biz_dir-category'] ) ? ( $info['tax_input']['at_biz_dir-category'] ) : array();
	$images                    = ! empty( $info['files_meta'] ) ? $info['files_meta'] : array();
	$manual_lat                = ! empty( $info['manual_lat'] ) ? $info['manual_lat'] : array();
	$manual_lng                = ! empty( $info['manual_lng'] ) ? $info['manual_lng'] : array();
	$map                       = ! empty( $manual_lat ) && ! empty( $manual_lng ) ? true : false;
	$attatchemt_only_for_admin = false;

	// meta input
	foreach ( $submission_form_fields as $key => $value ) {
		$field_key        = ! empty( $value['field_key'] ) ? $value['field_key'] : '';
		$submitted_data   = ! empty( $info[ $field_key ] ) ? $info[ $field_key ] : '';
		$required         = ! empty( $value['required'] ) ? $value['required'] : '';
		$only_for_admin   = ! empty( $value['only_for_admin'] ) ? $value['only_for_admin'] : '';
		$label            = ! empty( $value['label'] ) ? $value['label'] : '';
		$additional_logic = apply_filters( 'atbdp_add_listing_form_validation_logic', true, $value, $info );

		$field_category = ! empty( $value['category'] ) ? $value['category'] : '';
		if ( $field_category && ! in_array( $field_category, $admin_category_select ) ) {
			$additional_logic = false;
		}

		if ( $additional_logic ) {
			// error handling
			if ( ( 'category' === $key ) && $required && ! $only_for_admin && ! $admin_category_select ) {
				$msg = $label . __( ' field is required!', 'directorist' );
				array_push( $error, $msg );
			}

			if ( ( 'location' === $key ) && $required && ! $only_for_admin && ! $location ) {
				$msg = $label . __( ' field is required!', 'directorist' );
				array_push( $error, $msg );
			}

			if ( ( 'tag' === $key ) && $required && ! $only_for_admin && ! $tag ) {
				$msg = $label . __( ' field is required!', 'directorist' );
				array_push( $error, $msg );
			}

			if ( ( 'image_upload' === $key ) && $required && ! $only_for_admin && ! $images ) {
				$msg = $label . __( ' field is required!', 'directorist' );
				array_push( $error, $msg );
			}

			if ( ( 'map' === $key ) && $required && ! $only_for_admin && ! $map ) {
				$msg = $label . __( ' field is required!', 'directorist' );
				array_push( $error, $msg );
			}

			if ( ( 'category' !== $key ) && ( 'tag' !== $key ) && ( 'location' !== $key ) && ( 'image_upload' !== $key ) && ( 'map' !== $key ) ) {
				if ( $required && ! $submitted_data && ! $only_for_admin ) {
					$msg = $label . __( ' field is required!', 'directorist' );
					array_push( $error, $msg );
				}
			}
		}

		if ( ( 'image_upload' == $key ) && $only_for_admin ) {
			$attatchemt_only_for_admin = true;
		}

		// process meta
		if ( 'pricing' === $key ) {
			$metas['_atbd_listing_pricing'] = ! empty( $info['atbd_listing_pricing'] ) ? $info['atbd_listing_pricing'] : '';
			$metas['_price']                = ! empty( $info['price'] ) ? $info['price'] : '';
			$metas['_price_range']          = ! empty( $info['price_range'] ) ? $info['price_range'] : '';
		}
		if ( 'map' === $key ) {
			$metas['_hide_map']   = ! empty( $info['hide_map'] ) ? $info['hide_map'] : '';
			$metas['_manual_lat'] = ! empty( $info['manual_lat'] ) ? $info['manual_lat'] : '';
			$metas['_manual_lng'] = ! empty( $info['manual_lng'] ) ? $info['manual_lng'] : '';
		}
		if ( ( $field_key !== 'listing_title' ) && ( $field_key !== 'listing_content' ) && ( $field_key !== 'tax_input' ) ) {
			$key           = '_' . $field_key;
			$metas[ $key ] = ! empty( $info[ $field_key ] ) ? $info[ $field_key ] : '';
		}
	}

	// wp_send_json( $error );
	$title   = ! empty( $info['listing_title'] ) ? sanitize_text_field( $info['listing_title'] ) : '';
	$content = ! empty( $info['listing_content'] ) ? wp_kses( $info['listing_content'], wp_kses_allowed_html( 'post' ) ) : '';

	if ( ! empty( $info['privacy_policy'] ) ) {
		$metas['_privacy_policy'] = $info['privacy_policy'] ? $info['privacy_policy'] : '';
	}
	if ( ! empty( $info['t_c_check'] ) ) {
		$metas['_t_c_check'] = $info['t_c_check'] ? $info['t_c_check'] : '';
	}
		$metas['_directory_type'] = $directory_type;
		// guest user
	if ( ! is_user_logged_in() ) {
		$guest_email = isset( $info['guest_user_email'] ) ? esc_attr( $info['guest_user_email'] ) : '';
		if ( ! empty( $guest && $guest_email ) ) {
			atbdp_guest_submission( $guest_email );
		}
	}

	if ( $error ) {
		$data['error_msg'] = $error;
		$data['error']     = true;
	}
	/**
	 * It applies a filter to the meta values that are going to be saved with the listing submitted from the front end
	 *
	 * @param array $metas the array of meta keys and meta values
	 */

	$metas = apply_filters( 'atbdp_listing_meta_user_submission', $metas );
	// wp_send_json($metas);
	$args = array(
		'post_content' => $content,
		'post_title'   => $title,
		'post_type'    => ATBDP_POST_TYPE,
		'tax_input'    => ! empty( $info['tax_input'] ) ? directorist_clean( $info['tax_input'] ) : array(),
		'meta_input'   => apply_filters( 'atbdp_ultimate_listing_meta_user_submission', $metas, $info ),
	);
	// is it update post ? @todo; change listing_id to atbdp_listing_id later for consistency with rewrite tags
	if ( ! empty( $info['listing_id'] ) ) {
		/**
		 * @since 5.4.0
		 */
		do_action( 'atbdp_before_processing_to_update_listing' );

		$listing_id  = absint( $info['listing_id'] );
		$_args       = array(
			'id'            => $listing_id,
			'edited'        => true,
			'new_l_status'  => $new_l_status,
			'edit_l_status' => $edit_l_status,
		);
		$post_status = $edit_l_status;

		$args['post_status'] = $post_status;

		if ( 'pending' === $post_status ) {
			$data['pending'] = true;
		}

		// update the post
		$args['ID'] = $listing_id; // set the ID of the post to update the post

		if ( ! empty( $preview_enable ) ) {
			$args['post_status'] = 'private';
		}

		// Check if the current user is the owner of the post
		$post = get_post( $args['ID'] );
		// update the post if the current user own the listing he is trying to edit. or we and give access to the editor or the admin of the post.
		if ( get_current_user_id() == $post->post_author || current_user_can( 'edit_others_at_biz_dirs' ) ) {
			// Convert taxonomy input to term IDs, to avoid ambiguity.
			if ( isset( $args['tax_input'] ) ) {
				foreach ( (array) $args['tax_input'] as $taxonomy => $terms ) {
					// Hierarchical taxonomy data is already sent as term IDs, so no conversion is necessary.
					if ( is_taxonomy_hierarchical( $taxonomy ) ) {
						continue;
					}

					/*
					 * Assume that a 'tax_input' string is a comma-separated list of term names.
					 * Some languages may use a character other than a comma as a delimiter, so we standardize on
					 * commas before parsing the list.
					 */
					if ( ! is_array( $terms ) ) {
						$comma = _x( ',', 'tag delimiter', 'directorist' );
						if ( ',' !== $comma ) {
							$terms = str_replace( $comma, ',', $terms );
						}
						$terms = explode( ',', trim( $terms, " \n\t\r\0\x0B," ) );
					}

					$clean_terms = array();
					foreach ( $terms as $term ) {
						// Empty terms are invalid input.
						if ( empty( $term ) ) {
							continue;
						}

						$_term = get_terms(
							$taxonomy,
							array(
								'name'       => $term,
								'fields'     => 'ids',
								'hide_empty' => false,
							)
						);

						if ( ! empty( $_term ) ) {
							$clean_terms[] = intval( $_term[0] );
						} else {
							// No existing term was found, so pass the string. A new term will be created.
							$clean_terms[] = $term;
						}
					}

					$args['tax_input'][ $taxonomy ] = $clean_terms;
				}
			}

			$post_id = wp_update_post( $args );
			update_post_meta( $post_id, '_directory_type', $directory_type );

			if ( ! empty( $directory_type ) ) {
				wp_set_object_terms( $post_id, (int) $directory_type, 'atbdp_listing_types' );
			}

			if ( ! empty( $location ) ) {
				$append = false;
				if ( count( $location ) > 1 ) {
					$append = true;
				}
				foreach ( $location as $single_loc ) {
					$locations = get_term_by( 'term_id', $single_loc, ATBDP_LOCATION );
					if ( ! $locations ) {
						$result = wp_insert_term( $single_loc, ATBDP_LOCATION );
						if ( ! is_wp_error( $result ) ) {
							$term_id = $result['term_id'];
							wp_set_object_terms( $post_id, $term_id, ATBDP_LOCATION, $append );
							update_term_meta( $term_id, '_directory_type', array( $directory_type ) );

						}
					} else {
						wp_set_object_terms( $post_id, $locations->name, ATBDP_LOCATION, $append );
					}
				}
			} else {
				wp_set_object_terms( $post_id, '', ATBDP_LOCATION );
			}
			if ( ! empty( $tag ) ) {
				if ( count( $tag ) > 1 ) {
					foreach ( $tag as $single_tag ) {
						$tag = get_term_by( 'slug', $single_tag, ATBDP_TAGS );
						wp_set_object_terms( $post_id, $tag->name, ATBDP_TAGS, true );
					}
				} else {
					wp_set_object_terms( $post_id, $tag[0], ATBDP_TAGS );// update the term relationship when a listing updated by author
				}
			} else {
				wp_set_object_terms( $post_id, '', ATBDP_TAGS );
			}

			if ( ! empty( $admin_category_select ) ) {
				update_post_meta( $post_id, '_admin_category_select', $admin_category_select );
				$append = false;
				if ( count( $admin_category_select ) > 1 ) {
					$append = true;
				}
				foreach ( $admin_category_select as $single_category ) {
					$cat = get_term_by( 'term_id', $single_category, ATBDP_CATEGORY );
					if ( ! $cat ) {
						$result = wp_insert_term( $single_category, ATBDP_CATEGORY );
						if ( ! is_wp_error( $result ) ) {
							$term_id = $result['term_id'];
							wp_set_object_terms( $post_id, $term_id, ATBDP_CATEGORY, $append );
							update_term_meta( $term_id, '_directory_type', array( $directory_type ) );
						}
					} else {
						wp_set_object_terms( $post_id, $cat->name, ATBDP_CATEGORY, $append );
					}
				}
			} else {
				wp_set_object_terms( $post_id, '', ATBDP_CATEGORY );
			}

			// for dev
			do_action( 'atbdp_listing_updated', $post_id );// for sending email notification
		} else {
			// kick the user out because he is trying to modify the listing of other user.
			$data['redirect_url'] = esc_url_raw( directorist_get_request_uri() . '?error=true' );
			$data['error']        = true;
		}
	} else {

		// the post is a new post, so insert it as new post.
		if ( current_user_can( 'publish_at_biz_dirs' ) && ( ! isset( $data['error'] ) ) ) {
			// $_args = [ 'id' => '', 'new_l_status' => $new_l_status, 'edit_l_status' => $edit_l_status];
			$post_status = $new_l_status;

			$args['post_status'] = $post_status;

			if ( 'pending' === $post_status ) {
				$data['pending'] = true;
			}

			if ( ! empty( $preview_enable ) ) {
				$args['post_status'] = 'private';
			}

			if ( isset( $args['tax_input'] ) ) {
				foreach ( (array) $args['tax_input'] as $taxonomy => $terms ) {
					// Hierarchical taxonomy data is already sent as term IDs, so no conversion is necessary.
					if ( is_taxonomy_hierarchical( $taxonomy ) ) {
						continue;
					}

					/*
					 * Assume that a 'tax_input' string is a comma-separated list of term names.
					 * Some languages may use a character other than a comma as a delimiter, so we standardize on
					 * commas before parsing the list.
					 */
					if ( ! is_array( $terms ) ) {
						$comma = _x( ',', 'tag delimiter', 'directorist' );
						if ( ',' !== $comma ) {
							$terms = str_replace( $comma, ',', $terms );
						}
						$terms = explode( ',', trim( $terms, " \n\t\r\0\x0B," ) );
					}

					$clean_terms = array();
					foreach ( $terms as $term ) {
						// Empty terms are invalid input.
						if ( empty( $term ) ) {
							continue;
						}

						$_term = get_terms(
							$taxonomy,
							array(
								'name'       => $term,
								'fields'     => 'ids',
								'hide_empty' => false,
							)
						);

						if ( ! empty( $_term ) ) {
							$clean_terms[] = intval( $_term[0] );
						} else {
							// No existing term was found, so pass the string. A new term will be created.
							$clean_terms[] = $term;
						}
					}

					$args['tax_input'][ $taxonomy ] = $clean_terms;
				}
			}

			$post_id = wp_insert_post( $args );

			update_post_meta( $post_id, '_directory_type', $directory_type );
			do_action( 'atbdp_listing_inserted', $post_id );// for sending email notification

			// Every post with the published status should contain all the post meta keys so that we can include them in query.
			if ( 'publish' == $new_l_status || 'pending' == $new_l_status ) {

				if ( ! $default_expiration ) {
					update_post_meta( $post_id, '_never_expire', 1 );
				} else {
					$exp_dt = calc_listing_expiry_date( '', $default_expiration );
					update_post_meta( $post_id, '_expiry_date', $exp_dt );
				}

				update_post_meta( $post_id, '_featured', 0 );
				update_post_meta( $post_id, '_listing_status', 'post_status' );
				update_post_meta( $post_id, '_admin_category_select', $admin_category_select );
				/*
				  * It fires before processing a listing from the front end
				  * @param array $_POST the array containing the submitted fee data.
				  * */
				do_action( 'atbdp_before_processing_listing_frontend', $post_id );

				// set up terms
				if ( ! empty( $directory_type ) ) {
					wp_set_object_terms( $post_id, (int) $directory_type, 'atbdp_listing_types' );
				}
				// location
				if ( ! empty( $location ) ) {
					$append = false;
					if ( count( $location ) > 1 ) {
						$append = true;
					}
					foreach ( $location as $single_loc ) {
						$locations = get_term_by( 'term_id', $single_loc, ATBDP_LOCATION );
						if ( ! $locations ) {
							$result = wp_insert_term( $single_loc, ATBDP_LOCATION );
							if ( ! is_wp_error( $result ) ) {
								$term_id = $result['term_id'];
								wp_set_object_terms( $post_id, $term_id, ATBDP_LOCATION, $append );
								update_term_meta( $term_id, '_directory_type', array( $directory_type ) );
							}
						} else {
							wp_set_object_terms( $post_id, $locations->name, ATBDP_LOCATION, $append );
						}
					}
				} else {
					wp_set_object_terms( $post_id, '', ATBDP_LOCATION );
				}
				// tag
				if ( ! empty( $tag ) ) {
					if ( count( $tag ) > 1 ) {
						foreach ( $tag as $single_tag ) {
							$tag = get_term_by( 'slug', $single_tag, ATBDP_TAGS );
							wp_set_object_terms( $post_id, $tag->name, ATBDP_TAGS, true );
						}
					} else {
						wp_set_object_terms( $post_id, $tag[0], ATBDP_TAGS );// update the term relationship when a listing updated by author
					}
				} else {
					wp_set_object_terms( $post_id, '', ATBDP_TAGS );
				}
				// category
				if ( ! empty( $admin_category_select ) ) {
					update_post_meta( $post_id, '_admin_category_select', $admin_category_select );
					$append = false;
					if ( count( $admin_category_select ) > 1 ) {
						$append = true;
					}
					foreach ( $admin_category_select as $single_category ) {
						$cat = get_term_by( 'term_id', $single_category, ATBDP_CATEGORY );
						if ( ! $cat ) {
							$result = wp_insert_term( $single_category, ATBDP_CATEGORY );
							if ( ! is_wp_error( $result ) ) {
								$term_id = $result['term_id'];
								wp_set_object_terms( $post_id, $term_id, ATBDP_CATEGORY, $append );
								update_term_meta( $term_id, '_directory_type', array( $directory_type ) );
							}
						} else {
							wp_set_object_terms( $post_id, $cat->name, ATBDP_CATEGORY, $append );
						}
					}
				} else {
					wp_set_object_terms( $post_id, '', ATBDP_CATEGORY );
				}
			}
			if ( 'publish' == $new_l_status ) {
				do_action( 'atbdp_listing_published', $post_id );// for sending email notification
			}
		}
	}

	if ( ! empty( $post_id ) ) {
		do_action( 'atbdp_after_created_listing', $post_id );
		$data['id'] = $post_id;

		// handling media files
		if ( ! $attatchemt_only_for_admin ) {
			$listing_images = atbdp_get_listing_attachment_ids( $post_id );
			$files          = ! empty( $_FILES['listing_img'] ) ? directorist_clean( wp_unslash(  $_FILES['listing_img'] ) ) : array();
			$files_meta     = ! empty( $_POST['files_meta'] ) ? directorist_clean( wp_unslash( $_POST['files_meta'] ) ) : array();

			if ( ! empty( $listing_images ) ) {
				foreach ( $listing_images as $__old_id ) {
					$match_found = false;
					if ( ! empty( $files_meta ) ) {
						foreach ( $files_meta as $__new_id ) {
							$new_id = isset( $__new_id['attachmentID'] ) ? (int) $__new_id['attachmentID'] : '';
							if ( $new_id === (int) $__old_id ) {
								$match_found = true;
								break;
							}
						}
					}
					if ( ! $match_found ) {
						wp_delete_attachment( (int) $__old_id, true );
					}
				}
			}
			$attach_data = array();
			if ( $files ) {
				foreach ( $files['name'] as $key => $value ) {

					$filetype = wp_check_filetype( $files['name'][ $key ] );

					if ( empty( $filetype['ext'] ) ) {
						continue;
					}

					if ( $files['name'][ $key ] ) {
						$file                     = array(
							'name'     => $files['name'][ $key ],
							'type'     => $files['type'][ $key ],
							'tmp_name' => $files['tmp_name'][ $key ],
							'error'    => $files['error'][ $key ],
							'size'     => $files['size'][ $key ],
						);
						$_FILES['my_file_upload'] = $file;
						$meta_data                = array();
						$meta_data['name']        = $files['name'][ $key ];
						$meta_data['id']          = atbdp_handle_attachment( 'my_file_upload', $post_id );
						array_push( $attach_data, $meta_data );
					}
				}
			}

			$new_files_meta = array();
			foreach ( $files_meta as $key => $value ) {
				if ( $key === 0 && $value['oldFile'] === 'true' ) {
					update_post_meta( $post_id, '_listing_prv_img', $value['attachmentID'] );
					set_post_thumbnail( $post_id, $value['attachmentID'] );
				}
				if ( $key === 0 && $value['oldFile'] !== 'true' ) {
					foreach ( $attach_data as $item ) {
						if ( $item['name'] === $value['name'] ) {
							$id = $item['id'];
							update_post_meta( $post_id, '_listing_prv_img', $id );
							set_post_thumbnail( $post_id, $id );
						}
					}
				}
				if ( $key !== 0 && $value['oldFile'] === 'true' ) {
					array_push( $new_files_meta, $value['attachmentID'] );
				}
				if ( $key !== 0 && $value['oldFile'] !== 'true' ) {
					foreach ( $attach_data as $item ) {
						if ( $item['name'] === $value['name'] ) {
							$id = $item['id'];
							array_push( $new_files_meta, $id );
						}
					}
				}
			}
			update_post_meta( $post_id, '_listing_img', $new_files_meta );
		}
		$permalink = get_permalink( $post_id );
		// no pay extension own yet let treat as general user

		$submission_notice = get_directorist_option( 'submission_confirmation', 1 );
		$redirect_page     = get_directorist_option( 'edit_listing_redirect', 'view_listing' );

		if ( 'view_listing' == $redirect_page ) {
			$data['redirect_url'] = $submission_notice ? add_query_arg( 'notice', true, $permalink ) : $permalink;
		} else {
			$data['redirect_url'] = $submission_notice ? add_query_arg( 'notice', true, ATBDP_Permalink::get_dashboard_page_link() ) : ATBDP_Permalink::get_dashboard_page_link();
		}

		$states                           = array();
		$states['monetization_is_enable'] = get_directorist_option( 'enable_monetization' );
		$states['featured_enabled']       = $featured_enabled;
		$states['listing_is_featured']    = ( ! empty( $info['listing_type'] ) && ( 'featured' === $info['listing_type'] ) ) ? true : false;
		$states['is_monetizable']         = ( $states['monetization_is_enable'] && $states['featured_enabled'] && $states['listing_is_featured'] ) ? true : false;

		if ( $states['is_monetizable'] ) {
			$payment_status            = Directorist\Helper::get_listing_payment_status( $post_id );
			$rejectable_payment_status = array( 'failed', 'cancelled', 'refunded' );

			if ( empty( $payment_status ) || in_array( $payment_status, $rejectable_payment_status ) ) {
				$data['redirect_url'] = ATBDP_Permalink::get_checkout_page_link( $post_id );
				$data['need_payment'] = true;

				wp_update_post(
					array(
						'ID'          => $post_id,
						'post_status' => 'pending',
					)
				);
			}
		}

		$data['success'] = true;

	} else {
		$data['redirect_url'] = site_url() . '?error=true';
		$data['error']        = true;
	}

	if ( ! empty( $data['success'] ) && $data['success'] === true ) {
		$data['success_msg'] = __( 'Your Submission is Completed! redirecting..', 'directorist' );
	}

	if ( ! empty( $data['error'] ) && $data['error'] === true ) {
		$data['error_msg'] = isset( $data['error_msg'] ) ? $data['error_msg'] : __( 'Sorry! Something Wrong with Your Submission', 'directorist' );
	} else {
		$data['preview_url'] = $permalink;
	}

	if ( ! empty( $data['need_payment'] ) && $data['need_payment'] === true ) {
		$data['success_msg'] = __( 'Payment Required! redirecting to checkout..', 'directorist' );
	}

	if ( $preview_enable ) {
		$data['preview_mode'] = true;
	}

	if ( ! empty( $info['listing_id'] ) ) {
		$data['edited_listing'] = true;
	}

	if ( ! empty( $info['preview_url'] ) ) {
		$data['preview_url'] = Directorist\Helper::escape_query_strings_from_url( $info['preview_url'] );
	}

	if ( ! empty( $info['redirect_url'] ) ) {
		$data['redirect_url'] = Directorist\Helper::escape_query_strings_from_url( $info['redirect_url'] );
	}

	$redirect_url = $data['redirect_url'];
	$data = apply_filters( 'atbdp_listing_form_submission_info', $data ) ;
	$data['redirect_url'] = $redirect_url;
	wp_update_post(array( 'ID' =>  $data['id'], 'post_status' => 'publish' ));

	wp_send_json($data);
}

function diero_get_current_user_meta($user_id, $meta_key) {
	$user_id = !empty($user_id) ? $user_id : get_current_user_id();
	return get_user_meta( $user_id, $meta_key, true );
}

function diero_user_has_access() {
	$userHasSubscription = diero_has_subscription();
	return !empty($userHasSubscription) ? true : false;
}

function diero_wc_checkout_fields_values( $address_fields ) {
     $address_fields['first_name']['default'] = diero_get_current_user_meta(null, 'first_name');
     $address_fields['last_name']['default']  = diero_get_current_user_meta(null, 'last_name');
     return $address_fields;
}

function diero_shortcode_atts_products( $out, $pairs, $atts, $shortcode ) {
    if ( isset ( $atts['author'] ) ) $out['author'] = $atts['author'];
    return $out;
}

function diero_woocommerce_shortcode_products_query( $query_args, $attributes ) {       
    if ( isset( $attributes['author'] ) ) $query_args['author'] = $attributes['author'];
    return $query_args;
}

function diero_woocommerce_is_purchasable($is_purchasable, $product) {
	if(!user_can( $product->post->post_author, 'manage_options' )) {
		return false;
	} else {
		return true;
	}	     
}

function action_woocommerce_after_shop_loop_item_title(){
	global $product;
	?>
		<div class='product-meta'>
			<a href="<?php echo admin_url('post.php?post='. $product->get_id() .'&action=edit'); ?>" target="_blank"><?php _e('Edit', 'directorist'); ?></a> | <a href="javascript:void()" class="delete-user-item" data-id="<?php echo $product->get_id(); ?>"><?php _e('Delete', 'directorist'); ?></a>
		</div>
	<?php
}

function diero_delete_user_item() {
	$post = !empty($_POST['post']) ? $_POST['post'] : '';
	$authorId = get_post_field( 'post_author', $post );

	if(!empty($post) && !empty($authorId) && $authorId == get_current_user_id()) {
		$is_deleted = wp_delete_post( $post, true);

		if($is_deleted != null || $is_deleted != false) {
			$response = array('status' => 'success', 'msg' => __( 'Item has been deleted.', 'directorist') );
		} else {
			$response = array('status' => 'failed', 'msg' => __( 'Something went wrong. Please try again.', 'directorist') );
		}
	} else {
		$response = array('status' => 'failed', 'msg' => __( 'Something went wrong. Please try again.', 'directorist') );
	}
	
	wp_send_json($response);
}

function diero_user_has_capability() {
	$userType = diero_get_current_user_meta( get_current_user_id(), '_user_type');
	if(is_user_logged_in() && ($userType == 'author' || $userType == 'dispensary') && (current_user_can( 'assign_product_terms' ) && current_user_can( 'delete_private_products' ) && current_user_can( 'edit_private_products' ) && current_user_can( 'edit_products' ) && current_user_can( 'edit_published_products' ) && current_user_can( 'publish_products' ) && current_user_can( 'read_private_products' ))) {
		add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
		add_filter( 'woocommerce_disable_admin_bar', '__return_false' );
	}
}

function diero_hide_category_from_shop( $q ) {
	$tax_query = (array) $q->get( 'tax_query' );
    $tax_query[] = array(
    	'taxonomy' => 'product_cat',
    	'field' => 'slug',
    	'terms' => array( 'dispensary', 'author', 'uncategorized' ),
    	'operator' => 'NOT IN'
    );

    $q->set( 'tax_query', $tax_query );
}

function diero_add_user_item() {
	$params = array();
	parse_str($_POST['formData'], $params);

	$product = new WC_Product_Simple();

	if($params['action'] == 'edit') {
		$product->set_id($params['product_id']);
	}

	$product->set_name( $params['product_title'] );
	$product->set_short_description( $params['product_description'] );
	$product->set_description( $params['product_description'] );
	$product->set_image_id( $params['featured_image'] );
	$product->set_category_ids( $params['product_category'] );
	$productId = $product->save();

	if($productId) {
		if(!empty($params['gallery_images'])) {
			update_post_meta( $productId, '_product_image_gallery', $params['gallery_images'] );
		}

		if($params['action'] == 'add') {
			$response = array('status' => 'success', 'msg' => __( 'Product has been added.', 'directorist'));
		} else {
			$response = array('status' => 'success', 'msg' => __( 'Product has been updated.', 'directorist'));
		}
	} else {
		$response = array('status' => 'failed', 'msg' => __( 'Unexpected Error.', 'directorist'));
	}
	
	wp_send_json($response);
}

function diero_delete_attachment() {
	$attachment = !empty($_POST['attachment']) ? $_POST['attachment'] : '';
	$post = !empty($_POST['post']) ? $_POST['post'] : '';

	if(!empty($attachment) && !empty($post)) {
		$product_gallery_images = get_post_meta( $post, '_product_image_gallery', true );
		$product_gallery_images = explode(",",$product_gallery_images);

		if (($key = array_search($attachment, $product_gallery_images)) !== false) {
		    unset($product_gallery_images[$key]);
		}

		$product_gallery_images = implode(",", $product_gallery_images);
		$isUpdated = update_post_meta( $post, '_product_image_gallery', $product_gallery_images );

		if($isUpdated != false) {
			$response = array('status' => 'success', 'msg' => __( 'Item has been deleted.', 'directorist') );
		} else {
			$response = array('status' => 'failed', 'msg' => __( 'Something went wrong. Please try again.', 'directorist') );
		}
	} else {
		$response = array('status' => 'failed', 'msg' => __( 'Something went wrong. Please try again.', 'directorist') );
	}

	wp_send_json($response);
}

function diero_remove_product_tabs( $tabs ) {	
	unset( $tabs[ 'description' ] );
	unset( $tabs[ 'additional_information' ] );
	return $tabs;
}

function create_auto_subscription($user_id, $package_id) {
	$order = wc_create_order(
		array(
			'customer_note' => sanitize_text_field($customer_note)
		)
	);

	// Set customer ID
	$order->set_customer_id( $user_id );

	// Set Currenncy
	$order->set_currency( get_woocommerce_currency() );

	// Add product
	$order->add_product( get_product( $package_id ), 1);

	$billing_address = array(
	    'first_name' => fetch_user_meta($user_id, 'first_name'),
	    'last_name' => fetch_user_meta($user_id, 'last_name'),
	    'address_1' => fetch_user_meta($user_id, 'billing_address_1'),
	    'address_2' => fetch_user_meta($user_id, 'billing_address_2'),
	    'city' => fetch_user_meta($user_id, 'billing_city'),
	    'state' => fetch_user_meta($user_id, 'billing_state'),
	    'postcode' => fetch_user_meta($user_id, 'billing_postcode'),
	    'country' => fetch_user_meta($user_id, 'billing_country'),
	    'email' => fetch_user_meta($user_id, 'billing_email'),
	    'phone' => fetch_user_meta($user_id, 'billing_phone')
	);

	$shipping_address = array(
	    'first_name' => fetch_user_meta($user_id, 'shipping_first_name'),
	    'last_name' => fetch_user_meta($user_id, 'shipping_last_name'),
	    'address_1' => fetch_user_meta($user_id, 'shipping_address_1'),
	    'address_2' => fetch_user_meta($user_id, 'shipping_address_2'),
	    'city' => fetch_user_meta($user_id, 'shipping_city'),
	    'state' => fetch_user_meta($user_id, 'shipping_state'),
	    'postcode' => fetch_user_meta($user_id, 'shipping_postcode'),
	    'country' => fetch_user_meta($user_id, 'shipping_country')
	);

	// Set addresses
	$order->set_address( $billing_address, 'billing' );
	$order->set_address( $shipping_address, 'shipping' );

	// Set payment method
	$order->set_payment_method( 'stripe' );
	$order->set_payment_method_title( 'Credit/Debit card' );

	// Calculate and save
	$order->calculate_totals();
	$order->set_status( 'wc-processing', 'Basic package is processing' );
	$order_id = $order->save();

	if($order_id) {
		// Create post object
		$subscription = array(
		  'post_title'  => 'This subscription has been auto generated while user registration',
		  'post_status' => 'wc-wps_renewal',
		  'post_author' => $user_id,
		  'post_parent' => $order_id,
		  'post_type' => 'wps_subscriptions'
		);

		// Insert the post into the database
		$subscription_id = wp_insert_post( $subscription );

		if($subscription_id) {
            update_post_meta($order_id, 'wps_subscription_id', $subscription_id);
            update_post_meta( $subscription_id, 'product_id', $package_id );
            update_post_meta( $subscription_id, 'product_name', get_the_title($package_id) );
            update_post_meta( $subscription_id, 'product_qty', 1 );

			update_post_meta( $subscription_id, 'wps_susbcription_trial_end', '0' );
			update_post_meta( $subscription_id, 'wps_susbcription_end', '0' );
			update_post_meta( $subscription_id, 'wps_next_payment_date', fetch_next_payment_date($package_id) );
			update_post_meta( $subscription_id, 'wps_parent_order', $order_id );
			update_post_meta( $subscription_id, 'wps_customer_id', $user_id );

			$current_time = apply_filters( 'wps_sfw_subs_curent_time', current_time( 'timestamp' ), $subscription_id );
			update_post_meta( $subscription_id, 'wps_schedule_start', $current_time );

			update_post_meta( $subscription_id, 'wps_sfw_subscription_number', '1' );
			update_post_meta( $subscription_id, 'wps_sfw_subscription_interval', fetch_post_meta($package_id, 'wps_sfw_subscription_interval') );
			update_post_meta( $subscription_id, 'wps_sfw_subscription_expiry_interval', fetch_post_meta($package_id, 'wps_sfw_subscription_expiry_interval') );
			update_post_meta( $subscription_id, 'wps_sfw_subscription_initial_signup_price', '' );
			update_post_meta( $subscription_id, 'wps_sfw_subscription_free_trial_interval', fetch_post_meta($package_id, 'wps_sfw_subscription_free_trial_interval') );
			update_post_meta( $subscription_id, 'wps_recurring_total', '0' );
			update_post_meta( $subscription_id, 'wps_order_currency', get_woocommerce_currency() );
			update_post_meta( $subscription_id, 'wps_subscription_status', 'active' );
		}

        diero_woocommerce_order_status($order_id);
	}
}

function fetch_user_meta($user_id, $key) {
    return get_user_meta( $user_id, $key, true );
}

function fetch_next_payment_date($product_id) {
	$interval = fetch_post_meta($product_id, 'wps_sfw_subscription_interval');
	$time = strtotime(date("Y-m-d"));
	if($interval == 'month') {
		return date(strtotime("+1 month", $time));
	} else if($interval == 'day') {
		return date(strtotime("+1 day", $time));
	} else if($interval == 'week') {
		return date(strtotime("+7 day", $time));
	} else if($interval == 'year') {
		return date(strtotime("+1 year", $time));
	}
}

function fetch_post_meta($post_id, $key) {
    return get_post_meta( $post_id, $key, true );
}