<?php
/**
 * Plugin Name:     Wubtitle
 * Plugin URI:      https://www.wubtitle.com
 * Description:     Automatically generates subtitle for your videos
 * Author:          CTMobi
 * Author URI:      https://www.ctmobi.it
 * Text Domain:     wubtitle
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wubtitle
 */

// Your code starts here.
//
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'WUBTITLE_FILE_URL', __FILE__ );
define( 'WUBTITLE_DIR', plugin_dir_path( __FILE__ ) );
define( 'WUBTITLE_URL', plugin_dir_url( __FILE__ ) );
define( 'WUBTITLE_NAME', dirname( plugin_basename( __FILE__ ) ) );
define( 'WUBTITLE_VER', '0.1.0' );
define( 'WUBTITLE_ENDPOINT', 'https://api.wubtitle.com/' );
if ( defined( 'WP_WUBTITLE_ENV' ) && 'development' === WP_WUBTITLE_ENV ) {
	define( 'WUBTITLE_ENDPOINT', 'https://dev.api.wubtitle.com/' );
}
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

if ( class_exists( 'Wubtitle\\Loader' ) ) {
	Wubtitle\Loader::init();
}
