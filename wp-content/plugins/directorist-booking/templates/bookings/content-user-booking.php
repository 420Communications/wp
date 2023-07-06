<?php
if('owner reservations' == $value['comment']){
    return;
}
$class = array();
$tag = array();
$show_approve = false;
$show_reject = false;
switch ($value['status']) {
    case 'waiting' :
        $class[] = 'waiting-booking';
        $tag[] = '<span class="directorist-booking-status directorist-badge directorist-badge-warning directorist-booking-status-pending">'.esc_html__('Waiting', 'directorist-booking').'</span>';
        $show_approve = true;
        $show_reject = true;
        $show_delete = false;
        break;

    case 'confirmed' :
        $class[] = 'approved-booking';
        $tag[] = '<span  class="directorist-badge directorist-badge-success directorist-booking-status-approved">'.esc_html__('Approved', 'directorist-booking').'</span>';
        if($value['price']>0){
            $tag[] = '<span class="directorist-booking-status directorist-badge directorist-badge-light directorist-booking-status-unpaid">'.esc_html__('Processing Payment', 'directorist-booking').'</span>';
        }
        $show_approve = false;
        $show_reject = true;
        $show_delete = false;
        break;

    case 'paid' :

        $class[] = 'approved-booking';
        $tag[] = '<span class="directorist-booking-status directorist-badge directorist-badge-success directorist-booking-status-approved">'.esc_html__('Approved', 'directorist-booking').'</span>';
        if($value['price']>0){
            $tag[] = '<span class="directorist-booking-status directorist-badge directorist-badge-primary directorist-booking-status-paid">'.esc_html__('Paid', 'directorist-booking').'</span>';
        }
        $show_approve = false;
        $show_reject  = false;
        $show_delete  = false;
        break;

    case 'cancelled' :

        $class[] = 'canceled-booking';
        $tag[] = '<span class="directorist-booking-status directorist-badge directorist-badge-danger directorist-booking-status-rejected">'.esc_html__('Cancelled', 'directorist-booking').'</span>';
        $show_approve = false;
        $show_reject  = false;
        $show_delete  = true;
        break;

    default:
        # code...
        break;
}
$author_pro_pic_id = get_user_meta($value['bookings_author'], 'pro_pic', true);
$author_profile_pic = wp_get_attachment_image_src($author_pro_pic_id, 'thumbnail');
$listing_img_id = get_post_meta($value['listing_id'], '_listing_prv_img', true);
if(!empty($listing_img_id)) {
    $listing_img = wp_get_attachment_image_src($listing_img_id, 'thumbnail');
}
$default_image_src = get_directorist_option('default_preview_image', ATBDP_PUBLIC_ASSETS . 'images/grid.jpg');
$listing_type                 = get_post_meta($value['listing_id'],'_bdb_booking_type',true);
$booking_type                 = get_directorist_option('booking_type','all');
$booking_type                 = !empty($booking_type) ? $booking_type : 'service';
$listing_type                 = ( !empty($listing_type) && 'undefined' !== $listing_type ) ? $listing_type : $booking_type;
?>
<div class="directorist-booking-single <?php echo implode(' ',$class); ?>" id="booking-list-<?php echo esc_attr($value['ID']);?>">
    <div class="directorist-list-box-listing">
        <div class="directorist-list-box-listing__img"><a href="<?php echo get_permalink($value['listing_id']); ?>"><?php
                if(empty($listing_img_id)) { ?>

                <?php   } else { ?>
                <img src="<?php echo esc_url($listing_img[0]); ?>" alt="Listing Image">
                <?php }
                ?></a>
        </div>
        <div class="directorist-list-box-listing__content">
            <div class="directorist-inner">
                <div class="directorist-inner__top directorist-flex">
                    <h3 id="title" class="directorist-inner__h3title">
                        <a href="<?php echo get_permalink($value['listing_id']); ?>" class="directorist-inner__h3title--link">
                            <?php echo get_the_title($value['listing_id']); ?>
                        </a>
                    </h3>

                    <div class="directorist-booking-status-list">
                        <?php echo implode(' ',$tag); ?>
                    </div>
                </div>

                <div class="directorist-inner__booking-wrapper">
                    <?php if( 'event' != $listing_type ) { ?>
                    <div class="directorist-inner__booking-list">
                        <h5 class="directorist-inner__h5title"><?php esc_html_e('Booking Date:', 'directorist-booking'); ?></h5>
                        <ul class="directorist-booking-list">
                            <?php
                        //get post type to show proper date
                        //$listing_type = 'service';

                        if($listing_type == 'rent') { ?>
                            <li class="directorist-highlighted" id="date">
                                <?php echo date(get_option( 'date_format' ), strtotime($value['date_start'])); ?> -
                                <?php echo date(get_option( 'date_format' ), strtotime($value['date_end'])); ?></li>
                            <?php } else if( $listing_type == 'service' ) { ?>
                            <li class="directorist-highlighted" id="date">
                                <?php echo date(get_option( 'date_format' ), strtotime($value['date_start'])); ?>
                                <?php esc_html_e('at','directorist-booking'); ?> <?php
                                if( $value['date_start'] != $value['date_end'] ) {
                                    echo date(get_option('time_format'), strtotime($value['date_start'])); ?> - <?php echo date(get_option('time_format'), strtotime($value['date_end']));
                                } else {
                                    echo date(get_option('time_format'), strtotime($value['date_start']));
                                }

                                ?></li>
                            <?php } else { //event?>
                            <li class="directorist-highlighted" id="date">
                                <?php echo date(get_option( 'date_format' ), strtotime($value['date_start'])); ?>
                                <?php esc_html_e('at','directorist-booking'); ?>
                                <?php echo date(get_option( 'time_format' ), strtotime($value['date_start'])); ?>
                            </li>
                            <?php }
                        ?>

                        </ul>
                    </div>
                    <?php } ?>
                    <?php $details = json_decode($value['comment']);
                if (
                    (isset($details->childrens) && $details->childrens > 0)
                    ||
                    (isset($details->adults) && $details->adults > 0)
                    ||
                    (isset($details->tickets) && $details->tickets > 0)
                ) { ?>
                    <div class="directorist-inner__booking-list">
                        <h5 class="directorist-inner__h5title"><?php esc_html_e('Booking Details:', 'directorist-booking'); ?></h5>
                        <ul class="directorist-booking-list">
                            <li class="directorist-highlighted" id="details">
                                <?php
                                if( isset($details->childrens) && $details->childrens > 0) : ?>
                                <?php printf( _n( '%d Child', '%s Children', $details->childrens, 'directorist-booking' ), $details->childrens ) ?>
                                <?php endif; ?>
                                <?php if( isset($details->adults)  && $details->adults > 0) : ?>
                                <?php printf( _n( '%d Guest', '%s Guests', trim($details->adults,' Person'), 'directorist-booking' ), trim($details->adults,' Person') ) ?>
                                <?php endif; ?>
                                <?php if( isset($details->tickets)  && $details->tickets > 0) : ?>
                                <?php printf( _n( '%d Ticket', '%s Tickets', $details->tickets, 'directorist-booking' ), $details->tickets ) ?>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                    <?php } ?>

                    <?php
                $currency = get_directorist_option('g_currency', 'USD');
                $currency_postion = get_directorist_option('g_currency_position', 'before');
                $currency_symbol = atbdp_currency_symbol($currency);

                /*if($value['price']): */?>
                    <!--
                    <div class="directorist-inner__booking-list">
                        <h5 class="directorist-inner__h5title"><?php /*esc_html_e('Price:', 'directorist-booking'); */?></h5>
                        <ul class="directorist-booking-list">
                            <li class="directorist-highlighted" id="price">
                                <?php /*if($currency_postion == 'before') { echo $currency_symbol.' '; }  */?>
                                <?php /*echo $value['price']; */?>
                                <?php /*if($currency_postion == 'after') { echo ' '.$currency_symbol; }  */?></li>
                        </ul>
                    </div>
                --><?php /*endif; */?>

                    <div class="directorist-inner__booking-list">

                        <h5 class="directorist-inner__h5title"><?php esc_html_e('Client:', 'directorist-booking'); ?></h5>
                        <ul class="directorist-booking-list" id="client">
                            <?php if( isset($details->first_name) || isset($details->last_name) ) : ?>
                            <li id="name"><?php if(isset($details->first_name)) echo $details->first_name; ?>
                                <?php if(isset($details->last_name)) echo $details->last_name; ?></li>
                            <?php endif; ?>
                            <?php if( isset($details->email)) : ?><li id="email"><a
                                    href="mailto:<?php echo esc_attr($details->email) ?>"><?php echo $details->email; ?></a>
                            </li>
                            <?php endif; ?>
                            <?php if( isset($details->phone)) : ?><li id="phone"><a
                                    href="tel:<?php echo esc_attr($details->phone) ?>"><?php echo $details->phone; ?></a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php if( isset($details->service) && !empty($details->service)) : ?>
                    <div class="directorist-inner__booking-list">
                        <h5 class="directorist-inner__h5title"><?php esc_html_e('Extra Services:', 'directorist-booking'); ?></h5>
                        <?php echo wpautop( $details->service); ?>
                    </div>
                    <?php endif; ?>
                    <?php if( isset($details->message) && !empty($details->message)) : ?>
                    <div class="directorist-inner__booking-list">
                        <h5 class="directorist-inner__h5title"><?php esc_html_e('Message:', 'directorist-booking'); ?></h5>
                        <div class="directorist-inner__msg">
                            <?php echo wpautop( $details->message); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="directorist-inner__booking-list">
                        <h5 class="directorist-inner__h5title"><?php esc_html_e('Booking requested on:', 'directorist-booking'); ?></h5>
                        <ul class="directorist-booking-list">
                            <li class="directorist-highlighted" id="price"><?php echo $value['created'] ?> <z</li> </ul> </div>
                                    </div> <div class="directorist-booking-list-actions">
                                    <?php if($show_reject) : ?>
                                    <a href="#" class="directorist-btn directorist-btn-sm directorist-btn-danger directorist-booking-list-action-reject"
                                        id="reject_user_booking"
                                        data-booking_id="<?php echo esc_attr($value['ID']); ?>"><i
                                            class="sl sl-icon-close"></i>
                                        <?php esc_html_e('Cancel', 'directorist-booking'); ?></a>
                                    <?php endif; ?>
                                    <?php /*if($show_delete) : */?>
                                    <!--
                        <a href="#" class="button gray delete directorist-booking-list-actions__reject" id="delete_user_booking" data-booking_id="<?php /*echo esc_attr($value['ID']); */?>"><i class="sl sl-icon-close"></i> <?php /*esc_html_e('Delete', 'directorist-booking'); */?></a>
                    --><?php /*endif; */?>
                    </div>
                </div>
            </div>
        </div>
    </div>