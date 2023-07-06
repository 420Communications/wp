<?php
if( isset( $error ) && $error == true ){  ?>
    <div class="booking-confirmation-page booking-confrimation-error db-confirmation-error">
    <i class="fa fa-exclamation"></i>
        <h2><?php esc_html_e('Oops, we have some problem.','directorist-booking'); ?></h2>
        <p><?php echo  !empty($message) ? $message : '';  ?></p>
    </div>

<?php } else {
    ?>
    <div class="booking-confirmation-page directorist-confirmation-success">
        <i class="fa fa-check"></i>
        <h2 ><?php esc_html_e('Thank you for your booking!','directorist-booking'); ?></h2>
        <p><?php echo  !empty($message) ? $message : '';  ?></p>


        <?php /*$user_bookings_page = get_option('bdb_user_bookings_page');
        if( $user_bookings_page ) : */?><!--
            <a href="<?php /*echo esc_url(get_permalink($user_bookings_page)); */?>" class="button"><?php /*esc_html_e('Go to My Bookings','directorist-booking'); */?></a>
        --><?php /*endif; */?>
    </div>
<?php } ?>

