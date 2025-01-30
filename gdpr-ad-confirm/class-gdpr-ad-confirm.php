<?php

namespace WPAdverts\Snippets\Gdpr;

use WP_Post;

class AdConfirm {

    protected string $_after_confirm = "publish";

    public function __construct() {
        add_action( "advert_tmp_to_pending", [ $this, "advert_saved" ], 5 );

        add_action( "wp_ajax_gdpr-ad-confirm", [ $this, "ajax_confirm"] );
        add_action( "wp_ajax_nopriv_gdpr-ad-confirm", [ $this, "ajax_confirm"] );

        add_action( "init", [$this, "init"], 2000 );

        add_action( 'post_submitbox_misc_actions', [$this, "misc_actions"], 500 );
        add_filter( 'display_post_states', [$this, "post_states"], 200 );
    }

    /**
     * Generates a link with hash
     * 
     * Email -> core::on_draft_to_pending_notify_user
     * Function -> {$advert|gdpr_hash_link}
     */
    public static function get_hash_link( $post ) {
        if( $post instanceof \WP_Post ) {
            $post_id = $post->ID;
        } else {
            $post_id = $post;
        }

        $url = admin_url( 'admin-ajax.php' );
        $query = http_build_query([
            "action" => "gdpr-ad-confirm",
            "id" => absint( $post_id ),
            "hash" => get_post_meta( $post_id, "gdpr_ad_confirm__hash", true )
        ]);

        return $url . "?" . $query;
    }

    public function init() {
        if( ! class_exists( "Adext_Emails" ) ) {
            return;
        }

        \Adext_Emails::instance()->get_parser()->add_function("gdpr_hash_link", [__CLASS__, "get_hash_link"]);
    }

    public function advert_saved( $post ) {
        $hash = md5( time() . "|" . $post->ID );

        add_post_meta( $post->ID, "gdpr_ad_confirm__hash", $hash );
    }

    public function ajax_confirm() {

        $post_id = absint( \adverts_request( "id" ) );
        $post_hash = trim( \adverts_request( "hash" ) );

        $this->_if_error($post_id < 1, __( "Invalid Post ID") );
        $this->_if_error(strlen( $post_hash ) !== 32, __( "Invalid Hash Format") );

        $saved_hash = get_post_meta( $post_id, "gdpr_ad_confirm__hash", true );

        $this->_if_error( $post_hash != $saved_hash, __( "The provided hash does not match any value in our database.") );

        $hash_date = get_post_meta( $post_id, "gdpr_ad_confirm__date", true );

        $this->_if_error( $hash_date, __( "The Ad has already been veirified." ) );

        add_post_meta( $post_id, "gdpr_ad_confirm__date", current_time('timestamp', 0));

        if( $this->_after_confirm == "publish" ) { 
            $success = __( "Your ad has been verified and published." ) . "<br/>";
            $success.= sprintf( __( "You can <a href=\"%s\">view the ad here</a>." ), get_permalink( $post_id ) );
            wp_update_post( [ "ID" => $post_id, "post_status" => "publish" ] );
        } else {
            $success = __( "Your ad has been verified. We will notify you via email once the administrator will publish it." );
        }

        $html = "<h2>" . __( "Success" ) . "</h2>";
        $html.= "<p style='font-size:18px; color:darkgreen; font-weight:bold'>" . $success . "</p>";


        _default_wp_die_handler($html, "Ad Confirmation" );
    }

    protected function _if_error( $c, $error ) {

        if( ! $c ) {
            return;
        }

        $html = "<h2>" . __( "Error while validating hash." ) . "</h2>";
        $html.= "<p style='font-size:18px; color:crimson; font-weight:bold'>" . $error . "</p>";

        _default_wp_die_handler($html, "Error" );
        exit;
    }

    public function misc_actions() {
        global $post, $pagenow;
    
        // Do this for adverts only.
        if( ! \wpadverts_post_type( $post->post_type ) ) {
            return;
        }

        $confirm_date = get_post_meta( $post->ID, "gdpr_ad_confirm__date", true );
        $format = get_option( 'date_format') . " @ " . get_option( "time_format" );

        
        ?>
        
            <div class="misc-pub-section curtime misc-pub-curtime">
            <?php if( $confirm_date ): ?>
                <span class="dashicons dashicons-email" style="opacity:0.65"></span>
                <span>Verified: <?php echo date_i18n( $format, $confirm_date ) ?></span>
                <?php else: ?>
                <span class="dashicons dashicons-hourglass" style="opacity:0.65"></span>
                <span>Waiting for email verification.</span>
                <?php endif; ?>
            </div><?php // /misc-pub-section ?>

        <?php
    }

    public function post_states( $states ) {
        global $post;

        if( $post && \wpadverts_post_type( $post->post_type ) ) {
    
            $post_id = $post->ID;

            $saved_hash = get_post_meta( $post_id, "gdpr_ad_confirm__hash", true );
            $hash_date = get_post_meta( $post_id, "gdpr_ad_confirm__date", true );


            if( $saved_hash && !$hash_date ) {
                $span = new \Adverts_Html("span", array(
                    "class" => "dashicons dashicons-email",
                    "title" => __( "Waiting for email verrification", "wpadverts" ),
                    "style" => "font-size: 18px"
                ));
                $span->forceLongClosing(true);
                
                $states[] = $span->render();
            }
        }
        
        return $states;
    }
}