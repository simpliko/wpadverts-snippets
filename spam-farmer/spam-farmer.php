<?php
/**
Plugin Name: WPAdverts Snippets - SPAM Farmer
Version: 1.0.0
Author: Greg Winiarski
Description: Collects information about the data submitted via the [adverts_add]. It can be further analyzed to better protect websites from spam.
*/

class WPAdverts_Spam_Farmer {

    public function __construct() {
        add_filter( "adverts_action_preview", array( $this, "pre_save" ), 10, 1 );
        add_filter( "adverts_action_save-ff", array( $this, "pre_save" ), 10, 1 );
        add_filter( "admin_menu", array( $this, "init" ), 1000 );
    }

    public function pre_save( $content ) {

        $remove = array(
            "SERVER_SOFTWARE", "PATH", "LD_LIBRARY_PATH", "DOCUMENT_ROOT", "CONTEXT_DOCUMENT_ROOT", 
            "SERVER_ADMIN", "SCRIPT_FILENAME", 
        );

        $server = array();

        if( isset( $_SERVER ) && is_array( $_SERVER ) ) {
            foreach( $_SERVER as $k => $v ) {
                if( in_array( $k, $remove ) ) {
                    continue;
                }
                if( stripos( $k, "REDIRECT_") === 0 ) {
                    continue;
                }                
                if( stripos( $k, "ORIG_") === 0 ) {
                    continue;
                }


                $server[ $k ] = $v;
            }
        }

        include_once ADVERTS_PATH . '/includes/class-timetrap.php';
        $timetrap = new WPAdverts_Timetrap();

        $env = array(
            "get" => isset( $_GET ) ? $_GET : array(),
            "post" => isset( $_POST ) ? $_POST : array(),
            "server" => $server,
            "other" => array(
                "t2" => time(),
                "t1" => $timetrap->decrypt( adverts_request( $timetrap->get_field_name() ) ),
                "honeypot_name" => adverts_config( "moderate.honeypot_name")
            )
        );

        $this->save( $env );
        
        return $content;
    }

    public function save( $entry ) {

        $options = get_option( "spam_farmer", false );

        if( $options === false ) {
            $options = array();
        }

        $options[] = $entry;

        update_option( "spam_farmer", $options );
    }

    public function init( ) {
        add_submenu_page(
            'edit.php?post_type=advert',
            __( 'SPAM Farmer', 'textdomain' ),
            __( 'SPAM Farmer', 'textdomain' ),
            'manage_options',
            'spam-farmer',
            array( $this, "content" ),
            1000
        );
    }

    public function content() {

        if( adverts_request( "clear-farmer" ) == "1" ) {
            delete_option( "spam_farmer" );
            wp_redirect( remove_query_arg( array( "clear-farmer", "noheader" ) ) );
            exit;
        } else if( adverts_request( "download-farmer" ) == "1" ) {
            header('Content-type: text/plain');
            header('Content-Disposition: attachment; filename="spam-farmer.txt"');
            echo var_export( get_option( "spam_farmer" ) );
            exit;
        }

        echo '<div class="wrap">';
        echo '<h2>SPAM Farmer</h2>';
        echo sprintf( '<a href="%s" class="button-secondary">Download logs</a>', add_query_arg( array( "download-farmer" => 1, 'noheader' => 1 ) ) );
        echo "&nbsp;";
        echo sprintf( '<a href="%s" class="button-secondary">Clear logs</a>', add_query_arg( array( "clear-farmer" => 1, 'noheader' => 1 ) ) );
        echo '<hr />';
        $options = array_slice( array_reverse( get_option( "spam_farmer", array() ) ), 0, 100 );

        foreach( $options as $option ) {
            echo '<div class="spam-farmer-item">';
            echo sprintf( '<a href="#">%s</a>', $option["post"]["post_title"] );
            echo "<pre style='display:none; font-size:0.75rem;background: rgba(0, 0, 0, 0.07)'>";
            print_r($option);
            echo "</pre>";
            echo "</div>";
        }

        echo '</div>';

        $this->jquery();
    }

    public function jquery() {
        ?>
        <script type="text/javascript">
        jQuery(function($) {
            $( ".spam-farmer-item a").on("click", function(e) {
                e.preventDefault();
                $(this).closest( ".spam-farmer-item").find("pre").slideToggle("fast");

            });
        });
        </script>
        <?php
    }

}

$wpadverts_spam_farmer = new WPAdverts_Spam_Farmer;