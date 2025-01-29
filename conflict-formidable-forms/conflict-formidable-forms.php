<?php
/*
 * Plugin Name: WPAdverts - Formidable Forms Conflict
 * Plugin URI: http://wpadverts.com/
 * Description: Resolves a conflict between WPAdverts and Formidable Forms plugin, .
 * Author: Greg Winiarski
 */

function fic_fix_wpadverts_galleries() {
    if ( ! is_admin() ) {
        return;
    }
    remove_action( 'pre_get_posts', 'FrmProFileField::filter_media_library', 99 );
}
add_action( 'pre_get_posts', 'fic_fix_wpadverts_galleries', 98 );