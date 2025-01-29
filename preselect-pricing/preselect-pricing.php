<?php
/*
 * Plugin Name: Preselect Pricing
 * Plugin URI: https://wpadverts.com/
 * Description: Allows preselcting pricing in the [adverts_add] shortcode and the Classifieds / Publish block
 * Author: Greg Winiarski
 * Version: 1.0
 */

include_once dirname( __FILE__ ) . "/includes/class-core.php";

$dd_basedir = dirname( __FILE__ );
$dd_baseurl = plugins_url( __FILE__ );

$wpadverts_snippets_preselect_pricing = new WPAdverts\Snippets\PreselectPricing\Core( $dd_basedir, $dd_baseurl );