<?php

namespace WPAdverts\Snippets\Demo\Data;

class Core {

    public $baseurl = null;

    public $basedir = null;

    public function __construct( $basedir, $baseurl ) {

        $this->basedir = $basedir;
        $this->baseurl = $baseurl;

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {
        load_plugin_textdomain("wpadverts-snippet-demo-data", false, $this->baseurl . "/languages/" );
    }

	public function admin_menu() {

        $hook = add_management_page( 
            __( 'Demo Data', 'wpadverts-snippet-demo-data' ), 
            __( 'Demo Data', 'wpadverts-snippet-demo-data' ), 
            'install_plugins', 
            'wpadverts-snippet-demo-data', 
            array( $this, 'admin_page' ), '' 
        );

        add_action( "load-$hook", array( $this, 'admin_page_load' ) );
    }


    public function admin_page_load() {
        // ...
    }

    public function admin_page() {
        $nonce = wp_create_nonce( "wpadverts-snp-demo-data-nonce" );
        $show = "welcome";


        if( isset( $_POST ) && isset( $_POST['wpadverts-snp-demo-data-start'] ) && $_POST['wpadverts-snp-demo-data-start'] == "1" ) {
            $show = $this->admin_page_import();
        }

        include_once $this->basedir . '/admin/options.php';
    }

    public function admin_page_import() {

        $nonce = $_POST['wpadverts-snp-demo-data-nonce'];

        if( ! wp_verify_nonce( $nonce, "wpadverts-snp-demo-data-nonce") ) {
            wp_die( __( "Cannot verify request nonce!", "wpadverts-snippet-demo-data" ) );
        }

        include_once $this->basedir . '/includes/class-import.php';

        $import = new Import( $this->basedir );
        $import->setup_terms( get_current_user_id() );
        $import->create_adverts( get_current_user_id(), $this->basedir );

        if( count( $import->get_saved_ads() ) == 10 ) {
            return 1;
        }  else {
            return 0;
        }
    }
}