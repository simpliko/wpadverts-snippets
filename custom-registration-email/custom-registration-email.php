<?php

function custom_new_user_notification( $user_id, $deprecated = null, $notify = '', $password = null ) {
    if ( $deprecated !== null ) {
        _deprecated_argument( __FUNCTION__, '4.3.1' );
    }

    global $wpdb, $wp_hasher;
    $user = get_userdata( $user_id );

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

    @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

    if ( 'admin' === $notify || empty( $notify ) ) {
        return;
    }

    if ( $password === null ) {
        $password = wp_generate_password( 12, false );
    }

    // change the URL below to actual page with [adverts_manage] shortcode.
    $manage_url = home_url() . "/adverts/manage/";

    $message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
    $message .= sprintf(__('Password: %s'), $password ) . "\r\n";
    $message .= 'To manage your Ads please use the following address ' . $manage_url . "\r\n";

    wp_mail($user->user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);

}

if ( !function_exists('wp_new_user_notification') ) :
    function wp_new_user_notification() {
    
    }
endif;