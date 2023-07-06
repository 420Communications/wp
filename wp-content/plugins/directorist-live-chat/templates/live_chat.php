<?php
// prevent direct access to the file
defined('ABSPATH') || die('No direct script access allowed!');
// Check Restriction
$restricted = atbdp_check_live_chat_restriction( get_the_ID() );
if ( $restricted ) { return; }

$enable_chat         = get_directorist_option('enable_live_chat', 1);
$chat_listing_author = get_post_field('post_author', get_the_ID());
$user                = get_userdata($chat_listing_author);
if ( ( get_current_user_id() == $chat_listing_author) || empty( $enable_chat ) ) {
    return;
}

// let's check user has chat in this listing
if (!get_chat_by_user($chat_listing_author, get_current_user_id(), get_the_ID())->get_posts()) {
    $chats = '';
} else {
    $chats = get_chat_by_user($chat_listing_author, get_current_user_id(), get_the_ID())->get_posts();
}
?>
<div class="directorist-chat-wrapper">
    <div class="directorist-start-chat">
        <!-- if user is not logged in -->
        <?php
        if (!atbdp_logged_in_user()) {
            $login = ATBDP_Permalink::get_login_page_link();
            $registration = ATBDP_Permalink::get_registration_page_link();
            ?>
            <div>
                <button type="submit" class="dcl_login_alert directorist-btn directorist-btn-primary"><i class="fa fa-comments"></i>
                    <?php
                    $start_chat_button = get_directorist_option('start_chat_button', __('Start Chatting', 'directorist-live-chat'));
                    echo esc_attr($start_chat_button); ?>
                </button>
                <div class="dcl_login_notice atbd_notice alert alert-info" role="alert">
                    <span class="fa fa-info-circle"
                          aria-hidden="true"></span><?php echo esc_attr(__('You need to', 'directorist-live-chat')); ?>
                    <a href="<?php echo esc_url($login); ?>"><?php echo esc_attr(__('Login', 'directorist-live-chat')); ?></a> <?php echo esc_attr(__('or', 'directorist-live-chat')); ?>
                    <a
                            href="<?php echo esc_url($registration); ?>"><?php echo esc_attr(__('Register', 'directorist-live-chat')); ?></a> <?php echo esc_attr(__('to chat with it\'s owner', 'directorist-live-chat')); ?>
                </div>
            </div>
            <?php
        } else { ?>
            <button class="directorist-start-chat-btn" type="submit"><i class="fa fa-comments"></i>
                <span><?php
                    $show_chat_button = get_directorist_option('show_chat_button', __('Show Chats', 'directorist-live-chat'));
                    echo esc_attr($show_chat_button); ?></span>
            </button>
            <?php
        } ?>
    </div>
    <div class="directorist-client-chat-content-area">
        <div class="directorist-manage-fees-wrapper">
            <div id="directorist-user-message-container">
                <div>
                    <div>
                        <input type="hidden" name="userId" value="<?php echo $user->user_login; ?>">
                        <ul id="directorist-user-message-box">
                            <?php
                            if (!empty($chats)) {
                                foreach ($chats as $chat) {
                                    $chat_id = $chat->ID;
                                    $chat_author = get_post_field('post_author', $chat_id);
                                    $image = get_avatar($chat_author, 32);
                                    $chat_msg = get_post_meta($chat_id, '_chatMsg', true);
                                    $admin_chat = get_current_user_id() === (int)$chat_author ? 'directorist-user-chat' : '';
                                    $author = get_user_by('id', (int)$chat_author);
                                    $author_name = $author->display_name;
                                    $date = new DateTime($chat->post_date);
                                    $chat_time = $date->format('h:i A'); ?>
                                    <li class="<?php echo $admin_chat; ?>">
                                        <?php echo $image; ?>
                                        <div class="directorist-chat-content-wrap">
                                            <div class="directorist-chat-un-time">
                                                <span class="directorist-chat-user-name"><?php echo ucwords($author_name); ?></span><span
                                                        class="directorist-chat-time"><?php echo $chat_time; ?></span>
                                            </div>
                                            <div class="directorist-listing-chat-content">
                                                <p><?php echo $chat_msg; ?></p>

                                            </div>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <?php
                            if(empty($chats)){
                                echo '<span class="directorist-atbdp-no-chat">' . __('No record found!', 'directorist-live-chat') . '</span>';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <form action="/" method="POST" id="ChatForm">
            <input type="text" id="txt" name="chatMsg"
                   placeholder="<?php echo esc_attr(__('type your message here...', 'directorist-live-chat')); ?>"
                   autocomplete="off" required>
            <input type="hidden" name="chatAuthor_id" value="<?php echo get_current_user_id(); ?>">
            <input type="hidden" name="chatListing_id" value="<?php echo get_the_ID(); ?>">
            <input type="hidden" name="public_chat_form">
            <button type="submit"><i class="la la-paper-plane"></i></button>
        </form>
    </div>
</div>