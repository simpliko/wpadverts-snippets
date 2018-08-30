<?php
/*
 * Plugin Name: Force Featured Image.
 * Plugin URI: https://wpadverts.com/
 * Description: This code snippet/plugin forces user to select a featured image or selects it for the user automatically.
 * Author: Greg Winiarski
 */

add_filter( "adverts_action_preview", "force_featured_image_add" );
add_filter( "adverts_template_load", "force_featured_image_edit" );

/**
 * Forces featured image when adding new Ad using [adverts_add].
 * 
 * @uses force_featured_image()
 * 
 * @param string $content Page content
 * @return string
 */
function force_featured_image_add( $content ) {
    
    $post_id = adverts_request("_post_id", null);
    force_featured_image( $post_id );
    
    return $content;
}

/**
 * Forces featured image when editing and Ad in [adverts_manage].
 * 
 * @uses force_featured_image()
 * 
 * @param string $path Path to template file
 * @return string
 */
function force_featured_image_edit( $path ) {
    
    if( basename( $path ) == 'manage-edit.php' && isset( $_POST['_post_id'] ) && is_numeric( $_POST['_post_id' ] ) ) {
        force_featured_image( $_POST['_post_id'] );
    }
    return $path;
}

/**
 * Sets featured image for $post_id
 * 
 * @param int $post_id  ID of a post for which we wish to force featured image
 * @return int          1 if success less or equal to 0 on failure
 */
function force_featured_image( $post_id ) {
    if( $post_id < 1 ) {
        // No images uploaded
        return -1;
    } else if( $post_id > 0 && get_post_thumbnail_id( $post_id ) ) {
        // Has main image selected
        return -2;
    } 
    
    $children = get_children( array( 'post_parent' => $post_id ) );
    
    if( isset( $children[0] ) ) {
        update_post_meta( $post_id, '_thumbnail_id', $children[0]->ID );
        return 1;
    }
    
    return 0;
}