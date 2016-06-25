<?php
/*
 * Plugin Name: Replace Gallery
 * Plugin URI: http://wpadverts.com/
 * Description: Replaces WPAdverts Gallery with default WordPress Lightbox. <strong>Requires WPAdverts 1.0.7 or newer</strong>
 * Author: Greg Winiarski
 */

 add_action( "init", "replace_gallery_init", 1000 );

 function replace_gallery_init() {
     remove_action( "adverts_tpl_single_top", "adverts_single_rslides" );
     add_action( "adverts_tpl_single_top", "replace_gallery_with_lightbox" );
 }

 function replace_gallery_with_lightbox( $post_id ) {

     $images = get_children( array( 'post_parent' => $post_id ) );
     $list = array();

     if( empty( $images ) ) {
         return;
     }

     foreach( $images as $img ) {
         $list[] = $img->ID;
     }

     echo do_shortcode( sprintf( '[gallery size="large" ids="%s"]', join( ",", $list )  ) );
 }
