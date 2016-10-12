<?php
/**
Plugin Name: WPAdverts Snippets - BitCoin
Version: 1.0
Author: Greg Winiarski
Description: Displays Advert price as bitcoin.
*/

// The code below you can paste in your theme functions.php or create
// new plugin and paste the code there.

add_filter( "adverts_get_the_price", "price_as_bitcoin", 10, 3);

global $price_as_bitcoin;

$price_as_bitcoin = null;

/**
 * Convert price in USD to BTC
 * 
 * @var $list Array list of currencies
 * @return Array updated list of currencies
 */ 
function price_as_bitcoin( $formatted_price, $price, $post_id ) {
    global $price_as_bitcoin;
    
    if( $price_as_bitcoin === null ) {
        $response = wp_remote_get( "http://api.coindesk.com/v1/bpi/currentprice.json" );
        $price_as_bitcoin = json_decode( $response["body"] );
    }
    
    $exchange_rate = $price_as_bitcoin->bpi->USD->rate_float;
    
    $btc = round( $price / $exchange_rate, 4);
    
    return '฿' . $btc;
    
    // 1 BTC * 617.1 = USD
    // 1 USD / 617.1 = BTC
}