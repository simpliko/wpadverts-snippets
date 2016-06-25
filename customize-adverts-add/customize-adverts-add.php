<?php
/**
Plugin Name: WPAdverts Snippets - Customize [adverts_add]
Version: 1.0
Author: Greg Winiarski
Description: This plugin allows to customize [adverts_add] form fields.
*/

/**
 *  Default fields in [adverts_add] shortcode
 *
 *  - _contact_information  Contact Information header
 *  - _adverts_account      Account
 *  - adverts_person        Contact Person
 *  - adverts_email         Email
 *  - adverts_phone         Phone Number
 *  - _item_information     Item Information header
 *  - post_title            Title
 *  - advert_category       Category
 *  - gallery               Gallery
 *  - post_content          Descritpion
 *  - adverts_price         Price
 *  - adverts_location      Location
 *
 *
 */

$fields_to_remove = array();
$fields_required = array();

add_filter( "adverts_form_load", "customize_adverts_add" );

function customize_adverts_add( $form ) {
  if( $form['name'] != "advert" ) {
    return $form;
  }

  foreach( $form["field"] as $key => $field ) {
    
  }
}
