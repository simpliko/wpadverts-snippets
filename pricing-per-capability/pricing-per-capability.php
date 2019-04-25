<?php
/**
Plugin Name: WPAdverts Snippets - Pricing Per Capability
Version: 1.1
Author: Greg Winiarski
Description: Allows users to use pricing only if they have required capabilities.
*/

class Pricing_Per_Capability {
    
    /**
     * Class constructor
     * 
     * @since 1.0
     * @return void
     */
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        
    }

    /**
     * Init function
     * 
     * This function 
     */
    public function init() {
        add_filter( "wpadverts_filter_pricings", array( $this, "filter_pricings" ) );
        add_filter( "wpadverts_filter_renewals", array( $this, "filter_renewals" ) );
    }
    
    /**
     * Admin Init
     * 
     * Initiates wp-admin actions and filters
     * 
     * @since 1.0
     * @return void
     */
    public function admin_init() {
        
        add_filter( 'adverts_form_load', array( $this, 'form_load' ) );
    
    }
    
    /**
     * 
     * 
     * @param   array   $form   Form scheme
     * @return  array
     */
    public function form_load( $form ) {
        if( $form["name"] != "payment" ) {
            return $form;
        }
        
        $form["field"][] = array(
            "type" => "adverts_field_text",
            "name" => "pricing_caps",
            "label" => "Capabilities",
            "hint" => "List of capabilities (separated by comma) which can use this pricing.",
            "order" => 20
        );
        
        return $form;
    }
    
    public function can_use( $pricing ) {
        $caps = get_post_meta( $pricing->ID, 'pricing_caps', true );
        
        if( $caps === '' ) {
            return true;
        }
        
        $caps = array_map( "trim", explode( ",", $caps ) );
        
        foreach( $caps as $cap_pattern ) {
            $cap = null;
            $max_uses = null;
            
            if( stripos( $cap_pattern, ":" ) !== false ) {
                list( $cap, $max_uses ) = explode(":", $cap_pattern );
            } else {
                $cap = $cap_pattern;
            }
            
            $cap = trim( $cap );
            
            if( ! current_user_can( $cap ) ) {
                continue;
            }
            
            return true;
        }
        
        return false;
    }

    public function filter_pricings( WP_Query $pricings ) {
        
        foreach( $pricings->posts as $key => $pricing ) {
            if( ! $this->can_use( $pricing ) ) {
                unset( $pricings->posts[$key] );
                $pricings->post_count--;
                $pricings->found_posts--;
            }
        }
        
        return $pricings;
    }
    
    public function filter_renewals( $pricings ) {
        
        foreach( $pricings as $key => $pricing ) {
            if( ! $this->can_use( $pricing ) ) {
                unset( $pricings[$key] );
            }
        }
        
        return $pricings;
    }
    
}


$pricing_per_capability = new Pricing_Per_Capability();