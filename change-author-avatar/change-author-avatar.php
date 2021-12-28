<?php
/**
Plugin Name: WPAdverts Snippets - Change Author Avatar
Version: 1.0.1
Author: Greg Winiarski
Description: This extension integrates with WP User Avatar plugin to insert a "Change Avatar" menu item in the [adverts_author_manage] shortcode.
*/

add_filter( "adverts_author_manage_menu", "change_author_avatar_menu_item", 10, 2 );

function change_author_avatar_menu_item( $menu, $url ) {
    
    $menu["user-avatar"] = array(
        "label" => __("Edit Avatar", "change-author-avatar"),
        "href"  => add_query_arg( "author-panel", "user-avatar", $url ),
        "icon"  => "adverts-icon-user-circle",
        "callback" => "change_author_avatar",
        "order" => 35
    );
    
    return $menu;
}

function change_author_avatar() {
    if( class_exists( "WP_User_Avatar_Setup" ) ) {
        return do_shortcode( "[avatar_upload]" );
    } else if( class_exists( "basic_user_avatars" ) ) {
        return str_replace( get_the_permalink(), "", do_shortcode( "[basic-user-avatars]" ) );
    } else {
        return "Install a WP User Avatar or Basic User Avatars plugin to allow users to change the avatars.";
    }
}