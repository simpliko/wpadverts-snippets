<?php
/**
Plugin Name: WPAdverts Snippets - GDPR Compliance
Version: 1.0
Author: Greg Winiarski
Description: This snippet helps to make sure your WPAdverts installation is compliant with Euorepean Union GDPR Regulations introduced in May 2018
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_filter( "adverts_form_load", "gdpr_compliance_for_wpadverts" );

/**
 * Adds Terms and Conditions field to the [adverts_add] form.
 * 
 * This function is executed by "adverts_form_load" filter.
 * 
 * @see adverts_form_load filter
 * 
 * @param   array   $form   Form scheme array.
 * @return  array           Updated form scheme
 */
function gdpr_compliance_for_wpadverts( $form ) {
    if( $form["name"] != "advert" ) {
        return $form;
    }

    $form["field"][] = array(
        "name" => "_terms_and_conditions_header",
        "type" => "adverts_field_header",
        "order" => 25000,
        "label" => __( 'Terms and Conditions', 'adverts' )
    );

    $form["field"][] = array(            
        "name" => "_terms_and_conditions",
        "type" => "adverts_field_checkbox",
        "order" => 25000,
        "label" => "Terms and Conditions",
        "max_choices" => 100,
        "options" => array(
            // List all privacy policies, terms and conditions below ...
            // 
            // If a user is required to check the option add at the end of "text" <span class=\"adverts-form-required\">*</span>
            // It will help the user visually identify required items.
            
            array( 
                "value" => "1", 
                "text" => "I have read and agree to the <a href=\"https://example.com/tos/\" target=\"_blank\">Terms and Conditions</a>. <span class=\"adverts-form-required\">*</span>"
            ),
            array( 
                "value" => "2", 
                "text" => "I have read and agree to the <a href=\"https://example.com/pp/\" target=\"_blank\">Privacy Policy</a>. <span class=\"adverts-form-required\">*</span>"
            ),
            array( 
                "value" => "3", 
                "text" => "Subscribe me to a weekly newsletter."
            ),
        ),
        "validator" => array( 
            array( 
                "name"=>"gdpr_compliance_for_wpadverts", 
                "default_error" => "You have to check required fields to continue.",
                "params" => array(
                    // Set which checkboxes are required, by default the checkboxes with values "1" and "2".
                    "required" => "1,2"
                )
            ),
        )
    ); 

    adverts_form_add_validator("gdpr_compliance_for_wpadverts", array(
        "callback" => "gdpr_compliance_for_wpadverts_validator",
        "label" => __( "GDPR", "adverts" ),
        "params" => array(),
        "default_error" => __( "You have to check required fields to continue.", "adverts" ),
        "validate_empty" => true
    ));

    return $form;
}

/**
 * Terms and conditions validator
 * 
 * This validator checks if all required options were checked.
 * 
 * @param   mixed     $data         Values for options checked in the terms and conditions field.   
 * @param   mixed     $params       Field params.
 * @return  mixed                   Either boolean true or string identifying error code
 */
function gdpr_compliance_for_wpadverts_validator( $data, $params ) {

    $required = array();

    if( isset( $params["required"] ) ) {
        $required = array_map( "trim", explode(",", $params["required"] ) );
    }

    if( is_string( $data ) && $data == "" ) {
        $values = array();
    } elseif( ! is_array( $data ) ) {
        $values = array( $data );
    } else {
        $values = $data;
    }

    foreach( $required as $r ) {
        if( ! in_array( $r, $values ) ) {
            return "default_error";
        }
    }

    return true;
}