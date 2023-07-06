<?php
$args  = array(
    'author'         => get_current_user_id(),
    'post_type'      => ATBDP_POST_TYPE,
    'order'          => 'DESC',
    'orderby'        => 'date',
    'meta_key'       => '_bdb_booking_type',
    'meta_value'     => 'rent'
);
$user_listings = new WP_Query( $args );
$dashboard_price = dashboardPrice();
$normal_price = $dashboard_price['normal_price'];
$weekend_price = $dashboard_price['weekend_price'];
$calender_price = $dashboard_price['calender_price'];
?>
<div class='directorist-booking-calender'>
    <div class="directorist-booking-modal">
        <div class="directorist-booking-modal__wrapper">
            <div class="directorist-booking-modal__header">
                <h6><?php _e( 'Add New Booking', 'directorist-booking' );?></h6>
                <div class="directorist-booking-modal__cross">
                    <i class="las la-times"></i>
                </div>
            </div>
            <div class="directorist-booking-modal__body">
                <form action="#">
                    <div class="directorist-booking-modal__cols">
                        <div class="directorist-booking-modal__col directorist-select">
                            <label class="directorist-booking-modal__label"
                                for="inlineFormCustomSelect7"><?php _e( 'Preference', 'directorist-booking' );?></label>
                            <select class="custom-select" id="inlineFormCustomSelect7">
                                <option selected><?php _e( 'Choose...', 'directorist-booking' );?></option>
                                <option value="1"><?php _e( 'One', 'directorist-booking' );?></option>
                                <option value="2"><?php _e( 'Two', 'directorist-booking' );?></option>
                                <option value="3"><?php _e( 'Three', 'directorist-booking' );?></option>
                            </select>
                        </div>
                        <div class="directorist-booking-modal__col-50">
                            <label class="directorist-booking-modal__label" for="validationCustom02in"><?php _e( 'Check In', 'directorist-booking' );?></label>
                            <input type="date"
                                class="directorist-booking-form-control directorist-booking-form-control-date"
                                id="validationCustom02in" placeholder="date" value="Mark" required>
                        </div>
                        <div class="directorist-booking-modal__col-50">
                            <label class="directorist-booking-modal__label" for="validationCustom02out"><?php _e( 'Check Out', 'directorist-booking' );?></label>
                            <input type="date"
                                class="directorist-booking-form-control directorist-booking-form-control-date"
                                id="validationCustom02out" placeholder="date" value="Mark" required>
                        </div>
                        <div class="directorist-booking-modal__col-50 directorist-select">
                            <label class="directorist-booking-modal__label" for="inlineFormCustomSelect8"><?php _e( 'Adult', 'directorist-booking' );?></label>
                            <select class="custom-select" id="inlineFormCustomSelect8">
                                <option selected><?php _e( 'Choose...', 'directorist-booking' );?></option>
                                <option value="1"><?php _e( 'One', 'directorist-booking' );?></option>
                                <option value="2"><?php _e( 'Two', 'directorist-booking' );?></option>
                                <option value="3"><?php _e( 'Three', 'directorist-booking' );?></option>
                            </select>
                        </div>
                        <div class="directorist-booking-modal__col-50 directorist-select">
                            <label class="directorist-booking-modal__label"
                                for="inlineFormCustomSelect2"><?php _e( 'Children', 'directorist-booking' );?></label>
                            <select class="custom-select" id="inlineFormCustomSelect2">
                                <option selected><?php _e( 'Choose...', 'directorist-booking' );?></option>
                                <option value="1"><?php _e( 'One', 'directorist-booking' );?></option>
                                <option value="2"><?php _e( 'Two', 'directorist-booking' );?></option>
                                <option value="3"><?php _e( 'Three', 'directorist-booking' );?></option>
                            </select>
                        </div>
                        <div class="directorist-booking-modal__col">
                            <label class="directorist-booking-modal__label" for="validationCustom01"><?php _e( 'Name', 'directorist-booking' );?></label>
                            <input type="text" class="directorist-booking-form-control" id="validationCustom01"
                                placeholder="name" value="Mark" required>
                        </div>
                        <div class="directorist-booking-modal__col-50">
                            <label class="directorist-booking-modal__label" for="validationCustom0email"><?php _e( 'Email', 'directorist-booking' );?></label>
                            <input type="email" class="directorist-booking-form-control" id="validationCustom0email"
                                placeholder="email" value="Mark" required>
                        </div>
                        <div class="directorist-booking-modal__col-50">
                            <label class="directorist-booking-modal__label" for="validationCustom01"><?php _e( 'Phone Number', 'directorist-booking' );?></label>
                            <input type="number" class="directorist-booking-form-control" id="validationCustom01"
                                placeholder="phone number" value="Mark" required>
                        </div>
                        <div class="directorist-booking-modal__col">
                            <label class="directorist-booking-modal__label" for="validationCustom02"><?php _e( 'Address', 'directorist-booking' );?></label>
                            <input type="text" class="directorist-booking-form-control" id="validationCustom02"
                                placeholder="address" value="Mark" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="directorist-booking-modal__footer">
                <button class="directorist-booking-modal__footer-cancel"><?php _e( 'Cancel', 'directorist-booking' );?></button>
                <button class="directorist-booking-modal__footer-add-booking"><?php _e( 'Add Booking', 'directorist-booking' );?></button>
            </div>
        </div>
    </div>
    <h4 class="directorist-booking-calender__main-title">
    <?php _e( 'Booking Calendar', 'directorist-booking' );?>
    </h4>
    <div class="directorist-booking-calender__calender-top">
        <div class="directorist-booking-calender__calender-top-left">
            <div class="directorist-booking-month-picker-wrapper">
                <input class="form-control directorist-booking-month-picker" placeholder="MM/YYYY">
            </div>
        </div>
        <!-- <div class="directorist-booking-calender__calender-top-right">
            <button type="button"><i class="lar la-calendar-plus"></i>Add Booking</button>
        </div> -->
    </div>
    <div class="directorist-booking-calender__adv">
        <div class="directorist-booking-calender__adv-left">
            <div class="directorist-booking-calender__search">
                <i class="las la-search"></i>
                <input type="text" placeholder="Search listing" />
                <input type="hidden" class="directorist-listing-id" value="">
                <input type="hidden" class="directorist-price" value="<?php echo $normal_price; ?>">
                <input type="hidden" class="directorist-weekend-price" value="<?php echo $weekend_price; ?>">
                <input type="hidden" class="bdb_calender_price" value="<?php echo esc_attr( $calender_price ); ?>">
            </div>
            <div class="directorist-booking-calender__search-content">
                <ul>
                    <?php
                    if ( $user_listings->have_posts() ) {

                        while ( $user_listings->have_posts() ) {
                            $user_listings->the_post();
                    ?>
                            <li class="directorist-booking-calender__listing-title-wrapper directorist-user-listing" data-id="<?php echo get_the_ID(); ?>">
                                <?php echo get_the_title(); ?>
                            </li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <div class="directorist-booking-calender__adv-right">
            <div class="directorist-booking-calender__wrapper">
                <!-- <div class="directorist-booking-calender__wrapper-tap">
                    <ul>
                        <li class="active">
                            <a href="#">Available</a>
                        </li>
                        <li>
                            <a href="#">Booked</a>
                        </li>
                    </ul>
                    <div class="directorist-booking-calender__booking-clear">
                        clear
                    </div>
                </div> -->
                <?php
                echo BD_Booking()->bdb_rent_calendar->dashboard_calendar(); ?>
            </div>
        </div>
    </div>
</div>