<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
wp_nonce_field( basename( __FILE__ ), 'hd_nonce' );
$token = esc_attr( get_option('api_key'));
if ( $token !== "" ) {
    KBApi::setToken($token);
    $kbFields       = new KBApi('fields');
    $fields         = $kbFields->get();
    $decodedResult  = json_decode(json_encode($fields), true);
    hd_available_fields($decodedResult);
} else {
    esc_html_e( 'Configure the API key first at the settings page to show the available fields', 'hellodialog' );
}