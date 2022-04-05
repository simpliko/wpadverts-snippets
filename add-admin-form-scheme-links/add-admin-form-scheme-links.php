<?php
/*
 * Plugin Name: WPAdverts Snippets - Add Admin Form Scheme Links
 * Plugin URI: http://wpadverts.com/
 * Description: Adds a link to WPAdverts Admin Menu for each form scheme. Requires "WPAdverts Snippets - Return Form Scheme"
 * Author: Laurie Greysky with guidence from Greg Winiarski
 */

add_action( "admin_menu", function() {
   global $submenu;
   
   if (!function_exists('rfs_get_wpadverts_forms')) return;
   
   $forms = rfs_get_wpadverts_forms();
   
   foreach ($forms as $form_id => $form) {
		$submenu["edit.php?post_type=advert"][] = array('Add ' . $form['title'],
														'manage_options',
       													admin_url( 'post-new.php?post_type=advert&_wpacf_form_scheme_id=' . $form_id ) );
   }
}, 1000 );