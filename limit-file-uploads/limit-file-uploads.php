<?php
/**
Plugin Name: WPAdverts Snippets - Limit File Uploads
Version: 1.1
Author: Greg Winiarski
Description: Sets various validators on the Gallery field. This snippet requires WPAdverts 1.2 or newer.
*/

// register validators in the [adverts_add] form.
add_filter( "adverts_form_load", "limit_file_uploads" );

/**
 * Adds file upload validators
 * 
 * This function is executed using "adverts_form_load" and registers file
 * upload validators in the [adverts_add] form in the Gallery field.
 * 
 * @see adverts_form_load
 * 
 * @param array $form   Form scheme
 * @return array        Customize form scheme
 */
function limit_file_uploads( $form ) {
    
    if( $form['name'] != "advert" ) {
        return $form;
    }

    foreach( $form["field"] as $key => $field ) {
        if( $field["name"] != "gallery" ) {
            continue;
        }
        
        $form["field"][$key]["validator"] = array();
        
        // Set minimum and maximum nuber of files user can upload.
        // Note. setting the "min" value basically makes the gallery a required field.
        $form["field"][$key]["validator"][] = array(
            "name" => "upload_limit",
            "params" => array( 
                "min" => 1,     // minimum files to upload
                "max" => 20     // maximum file uploads
            )
        );

        // Set minimum and maximum file upload size.
        // Note. this is a limit for an individual file, not whole gallery field.
        $form["field"][$key]["validator"][] = array(
            "name" => "upload_size",
            "params" => array( 
                "min" => null,     // minimum file size
                "max" => "4MB"     // maximum file size
            )
        );
        
        // Set minimum and maximum total files upload size.
        // Note. this is a limit for a whole gallery field.
        $form["field"][$key]["validator"][] = array(
            "name" => "upload_size_global",
            "params" => array( 
                "min" => null,     // minimum files size
                "max" => null      // maximum files size
            )
        );

        // Set allowed file types
        // The "allowed" param accepts only: image, video and audio.
        $form["field"][$key]["validator"][] = array(
            "name" => "upload_type",
            "params" => array( 
                "allowed" => array( "image", "video", "audio" ), // file groups 
                "extensions" => array( "pdf", "doc", "docx" )    // individaul file extensions if different than "allowed"
            )
        );
        
        $form["field"][$key]["validator"][] = array(
            "name" => "upload_dimensions",
            "params" => array( 
                "strict" => 1,          // disallow file upload if WP cannot get image dimensions
                "min_width" => 64,      // minimum image width
                "max_width" => null,    // maximum image width
                "min_height" => 64,     // minimum image height
                "max_height" => null    // maximum image height
            )
        );
        
        break;
    }

    return $form;
}

add_action( "init", function() {
    adverts_form_add_validator("upload_size_global", array(
        "callback" => "limit_file_uploads_size_global",
        "label" => "",
        "params" => array(),
        "default_error" => __( "The total file size it too big.", "wpadverts" ),
        "validate_empty" => false
    ));
    
} );

function limit_file_uploads_size_global( $file, $params ) {
    if ( !isset($file["name"]) || !isset($file["size"]) ) {
        return true;
    }

    $max_size = 25*pow(1024,2); // 25MB
    
    $size = $file["size"];
    
    $post_id = intval( adverts_request( "post_id" ) );
    
    if( $post_id === 0 ) {
        $post_id = intval( adverts_request( "_post_id" ) );
    } 
    
    if( $post_id === 0 ) {
        $post_id = intval( adverts_request( "advert_id" ) );
    }
    
    if( $post_id > 0) {
        $atts = get_posts( array(
            "post_parent" => $post_id,
            "post_type" => "attachment",
        ) );
        foreach( $atts as $att ) {
            $size += filesize( get_attached_file( $att->ID ) );
        }
    }
    
    if( $size >= $max_size ) {
        return "invalid";
    }
    
    return true;
}