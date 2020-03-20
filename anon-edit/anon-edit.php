<?php
/**
Plugin Name: WPAdverts Snippets - Anon Edit
Version: 1.0
Author: Greg Winiarski
Description: Allow anonymous users to edit the Adverts.
*/

class AnonEdit {
    
    /**
     * Constructor
     * 
     * @since 1.0
     * @return void
     */
    public function __construct() {
        add_action( "init", array( $this, "init" ), 200 );
        add_filter( "the_content", array( $this, "the_content" ), 2000 );
        
        add_action('wp_ajax_nopriv_adverts_delete', 'adverts_delete');
        
    }
    
    /**
     * The_conent filter
     * 
     * This function is run by the_content filter registered in self::__construct.
     * 
     * @since   1.0
     * @param   string  $content     Page Content
     * @return  string
     */
    public function the_content( $content ) {
        if ( ! is_singular('advert') || ! in_the_loop() ) {
            return $content;
        }
        
        $advert_id = intval( adverts_request( "advert_id" ) );
        
        if( $advert_id < 1 ) {
            return $content;
        }
        
        $hash = trim( adverts_request( "advert_hash" ) );
        $hash_saved = get_post_meta( $advert_id, "_adverts_frontend_hash", true );

        if( empty( $hash ) || $hash_saved != $hash ) {
            return $content;
        }

        wp_dequeue_script( "adverts-single" );
        wp_print_styles( 'editor-buttons' );
        

        // Remove adverts_the_content filter so the Advert details page will not be shown.
        remove_filter( 'the_content', 'adverts_the_content', 9999 );
        
        // You can remove the line below if Yoast SEO Link Watchers function is disabled.
        remove_all_filters( 'save_post' );

        
        $message = 'You are editing Ad <a href="%s">%s</a>. No longer actual? <a href="%s" class="wpad-delete-now adverts-icon-trash">delete now</a>.';
        
        $delete_url = adverts_ajax_url();
        $delete_params = array(
            "action" => "adverts_delete",
            "id" => $advert_id,
            "redirect_to" => urlencode( get_permalink( adverts_config( "ads_list_id" ) ) . "?ad-deleted=1" ),
            "_ajax_nonce" => wp_create_nonce( sprintf( 'wpadverts-delete-%d', $advert_id ) )
        );
        
        $delete_link = add_query_arg( $delete_params, $delete_url );
        
        $adverts_flash = array(
            "error" => array(),
            "info" => array(
                array(
                    "icon" => "adverts-icon-edit",
                    "message" => sprintf( $message, get_permalink( $advert_id ), get_post( $advert_id )->post_title, $delete_link )
                )
            )
        );
        
        ob_start();
        adverts_flash( $adverts_flash );
        echo _adverts_manage_edit(array());
        echo '<link rel="stylesheet" id="editor-buttons-css" href="'.get_site_url().'/wp-includes/css/editor.min.css" type="text/css" media="all">';
        echo '<style type="text/css">.entry-content > p {display: none !important }</style>';
        
        add_action( "wp_footer", array( $this, "print_delete_js" ) );
        
        return ob_get_clean();
    }
    
    /**
     * Init action
     * 
     * Run by init action registered in self::__construct()
     * 
     * @since   1.0
     * @return  void
     */
    public function init() {
        if( class_exists( "Adext_Emails" ) ) {
            Adext_Emails::instance()->get_parser()->add_function( "my_advert_edit_url", array( $this, "my_advert_edit_url") );
        }

    }
    
    /**
     * my_advert_edit_url function for use in email templates
     * 
     * Function can be used in email body or title usage example:
     * {$advert|my_advert_edit_url}
     * 
     * @param type $post
     * @return type
     */
    public function my_advert_edit_url( $post ) {
        
        if( $post instanceof WP_Post ) {
            $post_id = $post->ID;
        } else {
            $post_id = $post;
        }
        
        $url = get_permalink( $post_id );
        $args = array(
            "advert_id" => $post_id,
            "advert_hash" => get_post_meta( $post_id, "_adverts_frontend_hash", true )
        );
        
        return add_query_arg( $args, $url );
    }
    
    public function print_delete_js() {
        ?>
        <script type="text/javascript">
            jQuery(function($) {
                $(".wpad-delete-now").click(function(e) {
                    if( ! confirm("Are you sure?") ) {
                        e.preventDefault();
                    }
                });
            });
        </script>
        <?php
    }
}

$anonedit = new AnonEdit();