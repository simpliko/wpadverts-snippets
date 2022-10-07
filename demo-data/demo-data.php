<?php
/**
Plugin Name: WPAdverts Snippets - Demo Data
Version: 1.0
Author: Greg Winiarski
Description: Go to the wp-admin / Tools / Demo Data in order to import WPAdverts classifieds and categories.
*/

include_once dirname( __FILE__ ) . "/includes/class-core.php";

$dd_basedir = dirname( __FILE__ );
$dd_baseurl = plugins_url( __FILE__ );

$wpadverts_snippets_demo_data = new WPAdverts\Snippets\Demo\Data\Core( $dd_basedir, $dd_baseurl );