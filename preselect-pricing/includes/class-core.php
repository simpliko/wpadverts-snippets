<?php

namespace Wpadverts\Snippets\PreselectPricing;

class Core {

    public $baseurl = null;

    public $basedir = null;

    public $admin = null;

    protected $_pricing_id = null;

    protected $_form_scheme = null;

    public function __construct( $basedir, $baseurl ) {

        $this->basedir = $basedir;
        $this->baseurl = $baseurl;

        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'init', array( $this, 'init' ) );

        add_filter( 'shortcode_atts_adverts_add', array( $this, 'publish_atts' ), 100, 3 );

        add_filter( 'wpadverts/block/publish/action', array( $this, 'publish_action' ) );
        add_filter( 'wpadverts/block/publish/possible-actions', array( $this, 'possible_actions' ) );
        
    }

    public function init() {
        load_plugin_textdomain("wpadverts-snippet-preselect-pricing", false, $this->baseurl . "/languages/" );
    }

    public function admin_init() {
        include_once $this->basedir . "/includes/class-admin.php";
        $this->admin = new Admin();
    }

    public function publish_atts( $out, $pairs, $atts ) {
        $pricing_id = adverts_request( "preselected_pricing" );
        $form_scheme_id = null;

        if( $pricing_id > 0 ) {
            $form_scheme_id = get_post_meta( $pricing_id, "_pricing_form_scheme", true );
        }

        if( $form_scheme_id ) {
            $form_scheme = get_post( $form_scheme_id );
            $out["form_scheme_id"] = $form_scheme->ID;
            $out["form_scheme"] = $form_scheme->post_name;

            $this->_pricing_id = $pricing_id;
            $this->_form_scheme = $form_scheme;

            add_filter( 'adverts_form_load', array( $this, "hide_pricings" ) );
            add_filter( 'adverts_form_bind', array( $this, "bind_pricing" ) );
        }

        return $out;
    }

    public function hide_pricings( $form ) {
        if( $form["name"] != "advert" ) {
            return $form;
        }

        foreach( $form["field"] as $k => $field ) {
            if( $field["name"] != "payments_listing_type" ) {
                continue;
            }

            $form["field"][$k]["options"] = $this->filter_options( $field["options"] );
            $form["field"][$k]["value"] = $this->_pricing_id;
        }
        
        return $form;
    }

    public function filter_options( $options ) {
        foreach( $options as $option ) {
            if( $option["value"] == $this->_pricing_id ) {
                return array( $option );
            }
        }
        return $options;
    }

    public function bind_pricing( $form ) {
        $form->set_value( "payments_listing_type", $this->_pricing_id );
        return $form;
    }

    public function publish_action( $action ) {
        if( $this->_pricing_id < 1 ) {
            $action = "not_preselected";
        }
        return $action;
    }

    public function possible_actions( $actions ) {
        $actions["not_preselected"] = array( 
            "order" => -1, 
            "name" => "adverts_action_not_preselected", 
            "callback" => array( $this, "action_not_preselected" ) 
        );
        return $actions;
    }

    public function action_not_preselected() {
        return "NOT OK";
    }
}