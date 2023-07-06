<?php
// get user email
$current_user           = wp_get_current_user();
$email                  = $current_user->user_email;
$first_name             =  $current_user->first_name;
$last_name              =  $current_user->last_name;
$full_name              =  $current_user->display_name;
// get meta of listing
$listing_data           = get_post_meta( $data['listing_id'] );
// get first images
$gallery                = get_post_meta( $data['listing_id'], '_listing_prv_img', true );
$prv_image_full         = wp_get_attachment_image_src(!empty($gallery) ? $gallery : '', 'full');
$payment_booking        = get_post_meta( $data['listing_id'], '_bdb_payment_booking', true );
$reservation_fee        = get_post_meta( $data['listing_id'], '_bdb_reservation_fee', true );
$checkout_page          = ( ! empty( $payment_booking ) ) ? true : false;
$url                    = ATBDP_Permalink::get_checkout_page_link($data['listing_id']);
$guest_booking           = get_directorist_option( 'bdb_guest_booking' );
?>
<div class="directorist-confirm-ticket">
    <div class="directorist-confirm-ticket__summary">
        <!-- Booking Summary -->
        <div class="directorist-confirm-top">
            <div class="directorist-confirm__img">
                <img src="<?php echo !empty($prv_image_full) ? $prv_image_full[0] : ''; ?>" alt="">
            </div>
            <div class="directorist-confirm__details">
                <h3><?php echo get_the_title($data['listing_id']); ?></h3>
                <?php
                    $currency = get_directorist_option('g_currency', 'USD');
                    $currency_position = get_directorist_option('g_currency_position', 'before');
                    $currency_symbol = atbdp_currency_symbol($currency);
                    ?>
                <ul>
                    <?php if('event' != $data['listing_type']) { ?>
                    <li>
                        <span class="la la-calendar-check-o"></span>
                        <?php esc_html_e('Date :', 'directorist-booking'); ?>
                        <span><?php echo date(get_option( 'date_format' ), strtotime($data['date_start'])); ?><?php if (isset($data['date_end']) && $data['date_start'] != $data['date_end']) echo '<b> - </b>' . date(get_option( 'date_format' ), strtotime($data['date_end'])); ?></span>
                    </li>
                    <?php if (isset($data['_hour'])) { ?>
                    <li><span class="la la-clock-o"></span> <?php esc_html_e('Time :', 'directorist-booking'); ?>
                        <span><?php echo $data['_hour']; ?></span></li><?php } ?>
                    <?php if (isset($data['adults']) && !empty($data['adults'])) {
                                $adults = trim($data['adults'], 'Guest');
                                ?>
                    <li>
                        <span class="la la-users"></span> <?php esc_html_e('Guests :', 'directorist-booking'); ?>
                        <span><?php if (!empty($adults)) echo $data['adults'];
                                        if (isset($data['childrens'])) echo $data['childrens'] . ' Childrens ';
                                        ?></span></li>
                    <?php }
                        }
                        ?>
                    <?php if ( isset( $data['tickets'] )) { ?>
                    <li><span class="la la-ticket-alt"></span><?php esc_html_e('Ticket :', 'directorist-booking'); ?>
                        <span><?php if ( isset( $data['tickets'] ) ) echo $data['tickets'];

                                    ?></span></li>
                    <?php } ?>
                    <?php if($data['price']>0): ?>
                    <li class="total-costs"><span class="la la-money"></span>
                        <?php esc_html_e('Total Cost', 'directorist-booking'); ?><span>
                            <?php if($currency_position == 'before') { echo $currency_symbol.' '; } echo $data['price']; if($currency_position == 'after') { echo ' '.$currency_symbol; } ?></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="directorist-confirm-ticket__contents">
        <h3><?php echo apply_filters('booking_confirmation_title',__('Personal Details', 'directorist-booking')) ?>
        </h3>
        <form id="booking-confirmation" action="<?php echo !empty($checkout_page) ? $url : '';?>" method="POST"
            role="form">
            <input type="hidden" name="confirmed" value="done" />
            <input type="hidden" name="price" value="<?php echo ! empty( $data['price'] ) ? $data['price'] : ''; ?>" />
            <input type="hidden" name="value" value="<?php echo $data['submitteddata']; ?>" />

            <div class="directorist-form-ticket">

                <div class="directorist-form-ticket__list">
                    <label><?php esc_html_e('Full Name', 'directorist-booking'); ?> *</label>
                    <input type="text" name="firstname" id="first_name" value="<?php esc_html_e($full_name); ?>">
                </div>

                <div class="directorist-form-ticket__list">
                    <div class="input-with-icon medium-icons">
                        <label><?php esc_html_e('E-Mail Address', 'directorist-booking'); ?> *</label>
                        <input type="text" name="email" id="email" value="<?php esc_html_e($email); ?>">
                        <i class="im im-icon-Mail"></i>
                    </div>
                </div>

                <div class="directorist-form-ticket__list">
                    <div class="input-with-icon medium-icons">
                        <label><?php esc_html_e('Phone', 'directorist-booking'); ?></label>
                        <input type="text" name="phone"
                            value="<?php esc_html_e( get_user_meta( $current_user->ID, 'atbdp_phone', true) ); ?> ">
                        <i class="im im-icon-Phone-2"></i>
                    </div>
                </div>

                <div class="directorist-form-ticket__list">
                    <label><?php esc_html_e('Message', 'directorist-booking'); ?></label>
                    <textarea maxlength="200" name="message"
                        placeholder="<?php esc_html_e('Your short message to the listing owner (optional)','directorist-booking'); ?>"
                        id="booking_message" cols="20" rows="3"></textarea>
                </div>

                <p class='directorist-confirmation-error'></p>

                <input type="hidden" name='guest_booking' id='guest_booking'
                    value='<?php echo ! empty( $guest_booking ) && ! is_user_logged_in() ? 'yes' : ''; ?>'>
                <a href="#" class="booking-confirmation-btn">
                    <div class="loadingspinner"></div><span class="book-now-text"><?php
                        !empty($payment_booking == 'on') ? esc_html_e('Confirm', 'directorist-booking') : esc_html_e('Confirm', 'directorist-booking') ;
                        ?></span>
                </a>
        </form>
    </div>
</div>