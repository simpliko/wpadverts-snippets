<?php
/*
Plugin Name: No Taxonomy Map
Plugin URI: http://wpadverts.com/
Description: Show a map even if no taxonomies are selected for an Advert.
Author: Grzegorz Winiarski
Version: 1.0
*/

add_action( "init", function() {
    add_filter( "adverts_tpl_single_location", "no_taxonomy_map", 10, 2 );
}, 100 );

function no_taxonomy_map( $location, $post_id ) {
    
    include_once ADVERTS_PATH . '/includes/class-html.php';

    $post = get_post( $post_id );

    $has_map = false;
    $map_is_visible = adverts_config( 'mal.ad_details_map_visible' );

    $post_lat = get_post_meta( $post_id, "_adverts_mal_latitude", true );
    $post_lng = get_post_meta( $post_id, "_adverts_mal_longitude", true );

    $span = new Adverts_Html( "span", array("class"=>"adverts-icon-down-open"), "");
    $span->forceLongClosing( true );
    
    if( $post_lat && $post_lng ) {
        $has_map = true;
        $link = new Adverts_Html( "a", array(
            "href" => "#",
            "class" => "wpadverts-mal-show-map",
            "data-map-is-visible" => absint( $map_is_visible ),
            "data-map-zoom" => apply_filters( "wpadverts_mal_map_default_zoom", 15 ),
            "data-map-center-lat" => $post_lat,
            "data-map-center-lng" => $post_lng,
            "data-map-marker-lat" => $post_lat,
            "data-map-marker-lng" => $post_lng,
            "data-map-marker-title" => $post->post_title,
            "data-map-marker-pin" => plugin_dir_url( __FILE__ ) . "assets/images/map-marker.png"
        ), sprintf( __( "(show on map %s)", "wpadverts-mal" ), $span->render() ) );
        $location.= $link;

    } 

    // add this action once
    if( $has_map && !has_action( "adverts_tpl_single_details", "wpadverts_mal_tpl_single_location_map" ) ) {
        add_action( "adverts_tpl_single_details", "wpadverts_mal_tpl_single_location_map", 5, 1 );
    }

    do_action( "wpadverts_mal_single_location_map", $has_map, $post_id );

    return $location;
}
