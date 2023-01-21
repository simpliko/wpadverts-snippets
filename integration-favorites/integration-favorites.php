<?php

namespace Wpadverts\Integration;

class Favorites {

    public function run() {
        add_action( "wpadverts/block/details/tpl/contact-content", array( $this, "tpl_contact_content" ), 10, 2 );
    
        add_filter( "favorites/button/css_classes", array( $this, "favorites_css_classes" ), 10, 2 );
    }

    public function is_enabled() {
        return true;
    }

    public function favorites_css_classes( $classes, $post_id ) {
        $append = "wpa-btn-secondary atw-flex hover:atw-bg-none atw-bg-none atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-6 atw-py-2 atw-rounded atw-border atw-leading-loose";
        return $classes . " " . $append;
    }

    public function tpl_contact_content( $post_id, $atts ) {
        if( ! $this->is_enabled() ) {
            return;
        }

        $button = get_favorites_button( $post_id );

        $tpl = '<div class="">%s</div>';

        echo sprintf( $tpl, $button );
    }

}

$wpadverts_integration_favorites = new Favorites;
$wpadverts_integration_favorites->run();