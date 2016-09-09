<?php
/*
 * Plugin Name: Custom Contact Form Email
 * Plugin URI: https://wpadverts.com/
 * Description: This plugin allows to customize the email that will be sent by WPAdverts Contact Form.
 * Author: Greg Winiarski
 */

add_filter( "adverts_contact_form_email", "contact_form_email", 10, 3 );

/**
 * Customize the email before it will be sent.
 * 
 * The $mail variable is an array of data that will be used in wp_mail function like this:
 * wp_mail($mail["to"], $mail["subject"], $mail["message"], $mail["headers"] );
 * 
 * This function will do a couple of operations on the data to customize the email
 * feel free to use only the ones you like.
 * 
 * @param array $mail          Data that will be passed to wp_mail() function. The array has following keys: to, subject, message, headers.
 * @param int $post_id         ID of an Advert
 * @param Adverts_Form $form   Form that was submitted along with its data     
 * @return array               Updated $mail data
 */
function contact_form_email( $mail, $post_id, $form ) {
    
    // get the post
    $post = get_post( $post_id );
    
    // Include Advert title in the subject
    $mail["subject"] = $post->post_title . " - " . $mail["subject"];
    
    // Send all the emails to Administrator
    $mail["to"] = get_option( "admin_email" );
    
    // Send a BBC copy of the email to Administrator
    $mail["headers"][] = "BBC: " . get_option( "admin_email" );
    
    // Include Advert price and location at the end of message
    $mail["message"] .= "\r\n---\r\n";
    $mail["message"] .= adverts_price( get_post_meta( $post_id, "adverts_price", true ) ) . "\r\n";
    $mail["message"] .= get_post_meta( $post_id, "adverts_location", true ) . "\r\n";
    
    // Allways make sure to return $mail
    return $mail;
}