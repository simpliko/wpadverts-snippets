<?php

namespace Wpadverts\Snippets\PreselectPricing;

class Admin {
    public function __construct() {
        add_filter( 'adverts_form_load', array( $this, "pricing_form_load" ) );
    }

    public function pricing_form_load( $form ) {
        if( $form["name"] != "payment" ) {
            return $form;
        }
        
        $form["field"][] = array(            
            "name" => "_pricing_form_scheme",
            "type" => "adverts_field_select",
            "order" => 35,
            "label" => __( "Form Scheme", "wpadverts-snippet-preselect-pricing" ),
            "is_required" => false,
            "validator" => array( ),
            "max_choices" => 1,
            "options" => $this->form_scheme_options()
        );
        
        return $form;
    }

    public function form_scheme_options() {

        $options = array( 
            array( "value" => 0, "text" => __( "None", "wpadverts-snippet-preselect-pricing" ) )
        );

        $loop = new \WP_Query(array(
            'post_type' => 'wpadverts-form',
            'post_status' => array('wpad-form-add'),
            'posts_per_page' => -1
        ));
        
        foreach( $loop->posts as $form_scheme ) {
            $form_title = $form_scheme->post_title;
            $options[] = array(
                "value" => $form_scheme->ID,
                "text" => $form_title
            );
        }

        return $options;
    }
}