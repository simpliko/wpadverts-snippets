<?php
/*
 * Plugin Name: Limit Listings.
 * Plugin URI: http://wpadverts.com/
 * Description: Limit the number of ads per day/week/month for listings.
 * Author: Greg Winiarski
 */

add_filter( "adverts_form_load", "limit_listings_form_load", 20 );

/**
 * Adds Listing Limit validator to the Advert form and "Max Listings" and Listing Interval"
 * fields to Pricing configuration form.
 * 
 * This function is applied to "adverts_form_load" filter in Adverts_Form::load()
 * when Advert form is being loaded.
 * 
 * @see Adverts_Form::load()
 * 
 * @param array $form
 * @return array
 */
function limit_listings_form_load( $form ) {
    
    if( $form["name"] == "payment" ) {
        $form["field"][] = array(
            "name" => "limit_listing_max",
            "type" => "adverts_field_text",
            "label" => __("Max Listings", "limit_listings"),
            "order" => 100,
            "validator" => array(
                array( "name" => "is_integer" )
            )
        );

        $form["field"][] = array(
            "name" => "limit_listing_interval",
            "type" => "adverts_field_text",
            "label" => __("Interval", "limit_listings"),
            "hint" => __("Number of days in which the listings can be used.", "limit_listing" ),
            "order" => 100,
            "validator" => array(
                array( "name" => "is_integer" )
            )
        );
    }
    
    if( $form["name"] == "advert" && !is_admin() ) {
        foreach( $form["field"] as $k => $f ) {
            if( $f["name"] == "payments_listing_type" ) {
                
                if( ! isset( $f["validator"] ) || ! is_array( $f["validator"] ) ) {
                    $f["validator"] = array();
                }
                
                adverts_form_add_validator("limit_listings", array(
                    "callback" => "limit_listings_validator",
                    "label" => __( "Listings Limit", "limit_listings" ),
                    "params" => array(),
                    "default_error" => __( "You reached maximum postings limit for this listing type.", "limit_listings" ),
                    "on_failure" => "break",
                    "validate_empty" => false
                ));
                
                $f["validator"][] = array( "name" => "limit_listings" );
                
                $form["field"][$k] = $f;
            }
        }
    }
    
    return $form;
}

/**
 * Listings Limit VALIDATOR
 * 
 * Checks if current user posted less Adverts than allowed max limit in configuration.
 * 
 * Note. This validator works only for registered users.
 * 
 * @param string $value
 * @return boolean|string
 */
function limit_listings_validator( $value ) {
    
    $max = get_post_meta( $value, "limit_listing_max", true );
    $interval = get_post_meta( $value, "limit_listing_interval", true );
    
    if( get_current_user_id() <= 0 ) {
        return true;
    }
    
    if( !is_numeric($max) || !is_numeric($interval) || $max <= 0 || $interval <= 0 ) {
        return true;
    }
    
    $args = array(
        'post_type' => 'advert',
        'post_status' => 'any',

        // Using the date_query to filter posts from last week
        'date_query' => array(
            array(
                'after' => $interval . ' days ago'
            )
        ),
        
        'meta_query' => array(
            array(
                'key'     => 'payments_listing_type',
                'value'   => intval( $value )
            ),
        ),
    ); 
    
    $query = new WP_Query( $args );
    
    if( $query->found_posts < $max ) {
        return true;
    } else {
        return "invalid";
    }
}