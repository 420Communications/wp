<?php
wp_enqueue_style('bdb-style');
wp_enqueue_script('bdb-main-js');
$current_user = wp_get_current_user();
$roles = $current_user->roles;
$role = array_shift($roles);
$type = '';
if (isset($data['type']) && 'user_booking' == $data['type']) {
    if ('user_booking' == $data['type']) {
        $type = "user";
    }
}

$booking_status = 'All';
if (!empty($data['bookings'])) {
    foreach ($data['bookings'] as $value) {
        $booking_status = $value['status'];
    }
    wp_reset_postdata();
    switch ($booking_status) {
        case 'confirmed';
            $booking_status = 'Approved';
            break;
        case 'waiting';
            $booking_status = 'Pending';
            break;
    }
}

$status = 'user' == $type ? 'My Bookings' : $booking_status . ' Bookings';

/**
 * @since 1.0.0
 */
do_action('atbdp_user_dashboard_booking_before_content', $status); ?>

<div class="row">
    <!-- Listings -->
    <div class="col-lg-12 col-md-12">
        <div class="dashboard-list-box  margin-top-0">

            <!-- Booking Requests Filters  -->
            <div class="booking-requests-filter">
                <?php if ($type == "user") : ?>
                    <input type="hidden" id="dashboard_type" name="dashboard_type" value="user">
                <?php endif; ?>
            </div>

            <!-- Reply to review popup -->

            <?php
            $booking_area_title = $type == "user" ? __('Your Bookings', 'directorist-booking') : __('Booking Requests', 'directorist-booking');
            $html = sprintf('<h4 class="directorist-bookings-title">%s</h4>', $booking_area_title);

            echo apply_filters('atbdp_user_dashboard_booking_header_area', $html);

            if ($type != "user") { ?>
                <div class="directorist-booking-content-inner">
                    <?php /*if(  ( $type!== "user" && isset($_GET['status']) && $_GET['status'] == 'approved')) : */ ?><!--

                    <ul class="db-booking-parent" id="bdb_listing_status" data-status="">
                        <li class="db-booking-parent__child active" id="bdb_all_status"><a class="bdb-approved" data-status="approved"><?php /*_e("All Statuses","directorist-booking"); */ ?></a></li>
                        <li class="db-booking-parent__child" id="bdb_paid"><a class="bdb-approved" data-status="paid"><?php /*_e("Paid","directorist-booking"); */ ?></a></li>
                        <li class="db-booking-parent__child" id="bdb_confirmed"><a class="bdb-approved" data-status="confirmed"><?php /*_e("Unpaid","directorist-booking"); */ ?></a></li>
                    </ul>
                 --><?php /*endif; */ ?>
                    <div class="directorist-booking-filter">
                        <?php if (($type !== "user" && isset($_GET['status']) && $_GET['status'] == 'approved')) : ?>
                            <!-- Sort by -->
                            <!--<div class="db-sort-by">
                                <div class="db-sort-by-select">
                                    <div class="atbd-dropdown atbd-drop-select with-sort bd-dropdown">
                                        <a href="#" class="atbd-dropdown-toggle" id="bdb_listing_status" data-status="" data-drop-toggle="atbd-toggle"><?php /*echo esc_html__( 'All Statuses', 'directorist-booking'); */ ?></a>
                                        <div class="atbd-dropdown-items">
                                            <a href="#" class="atbd-dropdown-item bdb-approved" data-status="confirmed"><?php /*echo esc_html__( 'Unpaid', 'directorist-booking') */ ?></a>
                                            <a href="#" class="atbd-dropdown-item bdb-approved" data-status="paid"><?php /*echo esc_html__( 'Paid', 'directorist-booking') */ ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                        <?php endif; ?>

                        <?php if (isset($_GET['status']) && 'approved' != $_GET['status']) {
                            ?>
                            <input type="hidden" class="listing_status" value="<?php echo $_GET['status']; ?>">
                        <?php } ?>
                        <?php
                        if ( $type !== "user" && isset( $data['listings'] ) && !empty( $data['listings'] ) && !empty( $data['bookings'] ) ) : ?>
                            <!-- Sort by -->
                            <div class="directorist-booking-sortby">
                                <div class="directorist-booking-sortby-select">
                                    <div class="atbd-dropdown atbd-drop-select with-sort bd-dropdown">
                                        <a href="#" class="atbd-dropdown-toggle"
                                           id="bdb_listing_id_<?php echo !empty($_GET['status']) ? $_GET['status'] : ''; ?>"
                                           data-status=""
                                           data-drop-toggle="atbd-toggle"><?php echo esc_html__('All Listings', 'directorist-booking'); ?></a>
                                        <div class="atbd-dropdown-items">
                                            <?php foreach ($data['listings'] as $listing_id) { ?>
                                                <a href="" data-status="<?php echo $listing_id; ?>"
                                                   class="atbd-dropdown-item bdb-listing-<?php echo !empty($_GET['status']) ? $_GET['status'] : ''; ?>"><?php echo get_the_title($listing_id); ?></a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                </div><!-- ends: db-booking-tabs -->
            <?php } ?>
            <p class="no-bookings-information-<?php echo !empty($type) ? $type : ''; ?><?php echo !empty($_GET['status']) ? $_GET['status'] : ''; ?>"
                style="display: none">
                <?php esc_html_e('We haven\'t found any bookings for that criteria', 'directorist-booking'); ?>
            </p>
            <?php if (isset($data['bookings']) && empty($data['bookings'])) { ?>
                <p class="no-bookings-information-<?php echo !empty($type) ? $type : ''; ?><?php echo !empty($_GET['status']) ? $_GET['status'] : ''; ?>">
                    <?php esc_html_e('You don\'t have any bookings yet', 'directorist-booking'); ?>
                </p>
            <?php } else { ?>
                <div id="booking-requests-<?php echo !empty($type) ? $type : ''; ?><?php echo !empty($_GET['status']) ? $_GET['status'] : ''; ?>"
                     class="booking-requests">
                    <?php
                    foreach ($data['bookings'] as $key => $value) {
                        $value['listing_title'] = get_the_title($value['listing_id']);
                        if ($type == "user") {
                            include BDB_TEMPLATES_DIR . '/bookings/content-user-booking.php';
                        } else {
                            include BDB_TEMPLATES_DIR . '/bookings/content-booking.php';
                        }

                    } ?>
                </div>
            <?php } ?>

        </div>

        <div class="pagination-container-<?php echo !empty($type) ? $type : ''; ?><?php echo !empty($_GET['status']) ? $_GET['status'] : ''; ?>">
            <?php echo bdb_ajax_pagination($data['pages'], 1); ?>
        </div>

    </div>
</div>

<?php
/**
 * @since 1.0.0
 */
do_action('atbdp_user_dashboard_booking_after_content'); ?>
