<?php
/*
 * Plugin Name: WPAdverts Snippets - Return Form Scheme
 * Plugin URI: http://wpadverts.com/
 * Description: Implements functions to allow conditional display based on form scheme
 * Author: Laurie Greysky with guidence from Greg Winiarski
 */

// Returns Form Scheme Id for Post
function rfs_get_wpadverts_post_form_id( $post_id ) {

	$form_scheme_id = get_post_meta( $post_id, "_wpacf_form_scheme_id", true );
	
	return (int) $form_scheme_id;	
}

// Returns Form Scheme Object for Post
function rfs_get_wpadverts_post_form( $post_id ) {

	if ( !$form_scheme_id = rfs_get_wpadverts_post_form_id( $post_id ) ) return '';

	$form_scheme = get_post( $form_scheme_id );
	
	return $form_scheme;
}

// Returns Form Scheme Name for Post
function rfs_get_wpadverts_post_form_name( $post_id ) {

	if ( !$form_scheme = rfs_get_wpadverts_post_form( $post_id ) ) return '';

	return $form_scheme->post_name;
}

// Returns Form Scheme Title for Post
function rfs_get_wpadverts_post_form_title( $post_id ) {

	if ( !$form_scheme = rfs_get_wpadverts_post_form( $post_id ) ) return '';

	return $form_scheme->post_title;
}

// Returns All Form Schemes Configured
function rfs_get_wpadverts_forms() {

	$form = array();

	$args = array('post_status'	=> 'wpad-form-add',
				  'post_type' 	=> 'wpadverts-form');
 
	$form_schemes = get_posts( $args );
		
	if ( is_array( $form_schemes ) ) {
		foreach ( $form_schemes as $form_scheme ) {
			$form[$form_scheme->ID] = array('name'	=> $form_scheme->post_name,
											'title'	=> $form_scheme->post_title);
		}
	}
	
	return $form;
}