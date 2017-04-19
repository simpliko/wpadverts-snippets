<?php
/*
 * Plugin Name: Moderate Once
 * Plugin URI: https://wpadverts.com/
 * Description: Disable moderation for users who already have at least one ad with status 'publish' or 'expired'
 * Author: Greg Winiarski
 */

add_filter( "shortcode_atts_adverts_add", "moderate_once", 10, 3 );

/** 
 * Extends allowed [adverts_add] attributes.
 *
 * Uses 'shortcode_atts_adverts_add' filter to inject new parameters.
 * 
 * @since 1.0.0
 *
 * @param array  $out       The output array of shortcode attributes.
 * @param array  $pairs     The supported attributes and their defaults.
 * @param array  $atts      The user defined shortcode attributes.
 * @return array            The output array of shortcode attributes.
 */
function moderate_once( $out, $pairs, $atts ) {

    $out["moderate"] = 1;

    // 1. check if user is logged in, if not return $out;
    if( get_current_user_id() < 1 ) {
        return $out;
    }

    // 2. check if user has at least one Advert with status publish or expired, if not return $out;

if(get_posts(array('author'=>$current_user->ID,'post_type'=>'advert','post_status'=>'publish'))) {
 return $out;
}





    $out["moderate"] = 0;


    return $out;
}
