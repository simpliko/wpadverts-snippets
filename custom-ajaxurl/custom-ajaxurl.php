<?php
/**
Plugin Name: WPAdverts Snippets - Custom AJAX URL
Version: 1.0
Author: Greg Winiarski
Description: Forces WPAdverts to use custom AJAX URL instead of default /wp-admin/admin-ajax.php
*/

/**
 * __ INSTALLATION __
 * - APACHE
 * 
 * 
 * - OTHER SERVERS
 * 
 */


class Custom_AJAX_Url {

    protected string $_rewrite;

    protected bool $_debug;

    protected bool $_apache;

    public function __construct( string $rewrite, bool $debug = false )
    {
        $this->_rewrite = trim( $rewrite, "/" );
        $this->_debug = $debug;

        $this->_get_server_info();

        add_filter( "admin_url", [ $this, "admin_url" ], 10, 4 );

        if( $this->is_apache() ) {
            add_action( "generate_rewrite_rules", [$this, "generate_rewrite_rules"]);
        }

        if( $this->_debug ) {
            add_action( "wp_footer", [ $this, "debug" ] );
        }
    }

    protected function _get_server_info():void {
        if(isset($_SERVER['SERVER_SOFTWARE'])) {
            $ss = $_SERVER['SERVER_SOFTWARE'];
        } else {
            $ss = "";
        }
    
        if(stripos( $ss, 'Apache' )) {
            $this->_apache = true;
        } else {
            $this->_apache = false;
        }
    }

    public function is_apache():bool {
        return $this->_apache;
    }

    public function admin_url( string $url, string $path = '', string $blog_id = null, string $scheme = 'admin' ): string {
    
        if( is_admin() || $path !== 'admin-ajax.php' ) {
            return $url;
        }

        if( $this->is_apache() ) {
            $postfix = "/";
        } else {
            $postfix = ".php";
        }

        $url = get_site_url( $blog_id, $this->_rewrite . $postfix, $scheme );

        return $url;
    }

    public function generate_rewrite_rules() {
        global $wp_rewrite;
        
        $non_wp_rules = array(
            "^" . $this->_rewrite . '/?$' => 'wp-admin/admin-ajax.php'
        );
 
        $wp_rewrite->non_wp_rules = $non_wp_rules + $wp_rewrite->non_wp_rules;
    }

    public function debug() {
        global $wp_rewrite, $wp;
        echo "<pre>";print_r($wp_rewrite); print_r($wp); echo "</pre>";
    }

}

$custom_ajax_url = new Custom_AJAX_Url("ajax-request", false);