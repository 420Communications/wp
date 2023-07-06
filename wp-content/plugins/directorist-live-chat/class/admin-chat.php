<?php
// it handles directorist Help and Support Page
class Admin_Chat{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_chat'), 11);
    }

    /**
     * It adds a submenu for showing all the documentation and details support
     */
    public function admin_chat()
    {
        add_submenu_page('edit.php?post_type=at_biz_dir', __('Message', 'directorist'), __('Message', 'directorist'), 'manage_options', 'directorist-help-and-support', array($this, 'display_chat_menu'));
    }

    /**
     * It displays settings page markup
     */
    public function display_chat_menu()
    {
        echo 'Hi';
    }

}