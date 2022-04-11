<?php
/*
 * Plugin Name: WPAdverts Snippets - Fusion Image & Gallery
 * Plugin URI: http://wpadverts.com/
 * Description: Replaces WPAdverts Gallery with fusion gallery
 * Author: Laurie Greysky
 */

// Remove automatic Gallery
add_action( "init", function() {
	remove_action( "adverts_tpl_single_top", "adverts_single_rslides" );
}, 1000 );

function return_post_attachments( $post_id ) {

    include_once ADVERTS_PATH . "/includes/class-gallery-helper.php";
    
    $gallery_helper = new Adverts_Gallery_Helper( $post_id );
    
    // get post attachments from cache or load using return_post_attachments
	$post_attachments = ( isset( $GLOBALS['_post_attachments'][$post_id] ) ) ? $GLOBALS['_post_attachments'][$post_id] : $GLOBALS['_post_attachments'][$post_id] = $gallery_helper->load_attachments();

	return $post_attachments;
}

function return_image_src( $post_id , $image_id , $type = 'full' ) {
	// If cache does not contain image URL
	if ( !isset( $GLOBALS['_post_attachments'][$post_id][$image_id]->{$type . '_url'} ) ) {
		// fetch img 
		$image = wp_get_attachment_image_src( $image_id , $type );
		
		// set to url to cached WP_Post Object
		$GLOBALS['_post_attachments'][$post_id][$image_id]->{$type . '_url'} = $image[0];
	} 
	// return cache URL
	return  $GLOBALS['_post_attachments'][$post_id][$image_id]->{$type . '_url'};
}

function return_featured_image_src( $post_id ) {
	// get post attachments
	$post_attachments = return_post_attachments( $post_id );
	
	return return_image_src( $post_id , key($post_attachments) , 'full' );
}

function return_fusion_imageframe( $post_id , $index = 1 , $fusion_args = array()) {
	// get post attachments
	$post_attachments = return_post_attachments( $post_id );
	
	if (is_array($post_attachments) && count($post_attachments) >= $index) {

		// Configure Image
		$fusion_imageframe_args 		= array();
		$fusion_imageframe_default_args = array(	
									"image_id" => "%s",
									"max_width" => "",
									"sticky_max_width" => "",
									"style_type" => "none",
									"blur" => "",
									"stylecolor" => "",
									"hover_type" => "none",
									"bordersize" => "",
									"bordercolor" => "",
									"borderradius" => "",
									"align_medium" => "none",
									"align_small" => "none",
									"align" => "none",
									"margin_top" => "",
									"margin_right" => "",
									"margin_bottom" => "",
									"margin_left" => "",
									"lightbox" => "no",
									"gallery_id" => "",
									"lightbox_image" => "",
									"lightbox_image_id" => "",
									"alt" => "",
									"link" => "",
									"linktarget" => "_self",
									"hide_on_mobile" => "small-visibility,medium-visibility,large-visibility",
									"sticky_display" => "normal,sticky",
									"class" => "",
									"id" => "",
									"animation_type" => "",
									"animation_direction" => "left",
									"animation_speed" => "0.3",
									"animation_offset" => "",
									"filter_hue" => "0",
									"filter_saturation" => "100",
									"filter_brightness" => "100",
									"filter_contrast" => "100",
									"filter_invert" => "0",
									"filter_sepia" => "0",
									"filter_opacity" => "100",
									"filter_blur" => "0",
									"filter_hue_hover" => "0",
									"filter_saturation_hover" => "100",
									"filter_brightness_hover" => "100",
									"filter_contrast_hover" => "100",
									"filter_invert_hover" => "0",
									"filter_sepia_hover" => "0",
									"filter_opacity_hover" => "100",
									"filter_blur_hover" => "0",
									"dynamic_params" => "");
									
		// populate fusion_imageframe_args from defauklt and supplied								
		foreach ($fusion_imageframe_default_args as $key => $default_value) {
			$fusion_imageframe_args[$key] = $default_value;
			if ( isset( $fusion_args[$key] ) ) $fusion_imageframe_args[$key] = $fusion_args[$key];
		}
		
		// Set to start
		$array_index = 1;
		// 
		foreach ($post_attachments as $attachment) {
			
			if ($array_index == $index) {

				$fusion_imageframe_shortcode = '[fusion_imageframe ';
			
				foreach ($fusion_imageframe_args as $key => $value) {
					$fusion_imageframe_shortcode .= $key . '="' . $value . '" ';
				}
		
				$fusion_imageframe_shortcode .= ']%s[/fusion_imageframe]';
			
				$image_url = return_image_src( $post_id , $attachment->ID , 'full' );
						
				return do_shortcode( sprintf( $fusion_imageframe_shortcode , $attachment->ID . '|full' , $image_url ) );
			}
			
			// increment
			$array_index++;
		}
	}
}

function return_fusion_gallery( $post_id , $start = 1 , $end = null , $fusion_args = array()) {
	// get post attachments
	$post_attachments = return_post_attachments( $post_id );
	
	if (is_array($post_attachments)) {
		// Set end
		if (is_null($end) || !$end) $end = count($post_attachments);
		// Set to start
		$array_index = 1;
		// gallery_images
		$gallery_images = '';
		// 
		foreach ($post_attachments as $image_id => $attachment) {
			if ($array_index >= (int) $start && $array_index <= (int) $end) {
				$gallery_images .= '[fusion_gallery_image image="' . return_image_src( $post_id , $image_id , 'full' ) . '" image_id="' . $image_id . '|full" link="" linktarget="_self" /]' . "\n";
			}
			
			// increment
			$array_index++;
		}
			
		if ($gallery_images) {
			// Configure Gallery
			$fusion_gallery_args 			= array();
			$fusion_gallery_default_args	= array(
									"layout" => "grid",
									"picture_size" => "",
									"columns" => "3",
									"column_spacing" => "",
									"gallery_masonry_grid_ratio" => "",
									"gallery_masonry_width_double" => "",
									"hover_type" => "",
									"lightbox" => "yes",
									"lightbox_content" => "",
									"bordersize" => "",
									"bordercolor" => "",
									"border_radius" => "",
									"hide_on_mobile" => "small-visibility,medium-visibility,large-visibility",
									"class" => "",
									"id" => "");
									
			// populate fusion_gallery_default_args from defauklt and supplied								
			foreach ($fusion_gallery_default_args as $key => $default_value) {
				$fusion_gallery_args[$key] = $default_value;
				if ( isset( $fusion_args[$key] ) ) $fusion_gallery_args[$key] = $fusion_args[$key];
			}
		
			$fusion_gallery_shortcode = '[fusion_gallery ';
			
			foreach ($fusion_gallery_args as $key => $value) {
				$fusion_gallery_shortcode .= $key . '="' . $value . '" ';
			}
		
			$fusion_gallery_shortcode .= ']%s[/fusion_gallery]';
		
			return do_shortcode( sprintf( $fusion_gallery_shortcode , $gallery_images ) );
		}						
	}
}
