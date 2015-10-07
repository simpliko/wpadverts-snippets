<?php
/*
 * Plugin Name: Enable Adverts Comments.
 * Plugin URI: http://wpadverts.com/
 * Description: This code snippet/plugin enables comments on ads.
 * Author: Greg Winiarski
 */

add_action("adverts_post_type", "enable_adverts_comments");

add_action("adverts_insert_post", "enable_adverts_comments_check");
add_action("adverts_update_post", "enable_adverts_comments_check");

/**
 * Enables comments in Ads
 * 
 * This will enable support for comments in wp-admin panel when editing Ad.
 * 
 * Note you can use this function to further customize Adverts Custom Post Type the
 * $args argument is "$args" used in register_post_type function.  In other words
 * this function (or rather "customize_post_type" filter) gives you an opportunity
 * to customize adverts Custom Post Type parameters before 
 * register_post_type function will be called.
 * 
 * @see register_post_type()
 * @link https://codex.wordpress.org/Function_Reference/register_post_type
 * 
 * @param array $args Custom Post Type init params
 * @return array
 */
function enable_adverts_comments( $args ) {
   $args["supports"][] = "comments";
   return $args;
}

/**
 * Set Ad comment status to "open".
 * 
 * Sets comment_status field to open for a single Ad, this will allow users
 * to post comment below an Ad.
 * 
 * @param array $data
 * @return array Updated $data
 */
function enable_adverts_comments_check( $data ) {
    $data["comment_status"] = "open";
    return $data;
}