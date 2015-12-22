<?php
/**
Plugin Name: WPAdverts Snippets - Limit File Uploads
Version: 1.0
Author: Greg Winiarski
Description: Limits number of files user can upload to gallery.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

// Set maximum number of file uploads allowed below.
// Default is five which means users can upload max 5 images per ad.
define("LIMIT_FILE_UPLOADS", 5);

// Apply this validation only to WPAdverts file uploads.
add_filter("adverts_gallery_upload_prefilter", "limit_file_uploads");

/**
 * Checks if current Ad reached max file uploads
 * 
 * The $file variable should be an item from $_FILES array.
 * 
 * @param array $file Item from $_FILES array
 * @return array
 */
function limit_file_uploads( $file ) {

    if ( !isset($file["name"]) || !isset($file["type"]) ) {
        return $file;
    }

    if ( !isset( $_POST["post_id"] ) ) {
        $post_id = 0;
    } else {
        $post_id = intval($_POST["post_id"]);
    }
    
    if( $post_id < 1 ) {
        // first image upload.
        return $file;
    }
    
    $attachments = get_children( array( 'post_parent' => $post_id ) );
    $images = count( $attachments );
    
    if( $images >= LIMIT_FILE_UPLOADS ) {
        $file["error"] = sprintf( "You cannot upload more than %d images.", LIMIT_FILE_UPLOADS );
    }
    
    return $file;
}
