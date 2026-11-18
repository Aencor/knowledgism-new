<?php
/**
 * Plugin Name: Knowledgism - Local Dev & WP Migrate Helper
 * Description: Deshabilita la verificación SSL estricta para certificados autofirmados locales y optimiza las conexiones cURL para WP Migrate DB Pro.
 * Version: 1.0.0
 * Author: Antigravity
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Deshabilitar verificación SSL para entornos de desarrollo local
add_filter( 'https_ssl_verify', '__return_false', PHP_INT_MAX );
add_filter( 'https_local_ssl_verify', '__return_false', PHP_INT_MAX );

add_filter( 'http_request_args', function( $args, $url = '' ) {
    $args['sslverify']   = false;
    $args['timeout']     = max( (int) ( $args['timeout'] ?? 5 ), 60 );
    return $args;
}, PHP_INT_MAX, 2 );

// Optimización específica para WP Migrate DB Pro
add_filter( 'wpmdb_sslverify', '__return_false', PHP_INT_MAX );
add_filter( 'wpmdb_http_request_args', function( $args ) {
    $args['sslverify'] = false;
    $args['timeout']   = 120;
    return $args;
}, PHP_INT_MAX );

// Mock WP Engine constants and prevent Varnish socket errors in local environment
if ( ! defined( 'WPE_CLUSTER_TYPE' ) ) {
    define( 'WPE_CLUSTER_TYPE', 'pod' );
}
if ( ! defined( 'WPE_CLUSTER_ID' ) ) {
    define( 'WPE_CLUSTER_ID', 1 );
}
if ( ! defined( 'WPE_APIKEY' ) ) {
    define( 'WPE_APIKEY', 'local' );
}
$wpe_varnish_servers = array();

