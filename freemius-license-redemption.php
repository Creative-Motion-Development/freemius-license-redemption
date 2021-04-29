<?php
/**
 * Plugin Name: Freemius License Redemption
 * Description: Freemius License Redemption
 * Version:     1.0.0
 * Author:      creativemotion
 * Author URI:  https://cm-wp.com
 * Text Domain: freemius-license-redemption
 * Domain Path: /languages/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WFLR_PLUGIN_DIR', __DIR__ );
define( 'WFLR_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'WFLR_PLUGIN_URL', plugins_url( null, __FILE__ ) );
define( 'WFLR_PLUGIN_PREFIX', 'wflr' );

load_plugin_textdomain( 'freemius-license-redemption', false, dirname( WFLR_PLUGIN_BASE ) );

require_once WFLR_PLUGIN_DIR . '/includes/sdk/boot.php';
require_once WFLR_PLUGIN_DIR . "/includes/class.settings.php";
require_once WFLR_PLUGIN_DIR . "/includes/class.plugin.php";

try {
	new \WFLR\Plugin();
} catch ( Exception $e ) {
	$mpn_plugin_error_func = function () use ( $e ) {
		$error = sprintf( __( "The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'freemius-license-redemption' ), 'Freemius License Redemption', $e->getMessage(), $e->getCode() );
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action( 'admin_notices', $mpn_plugin_error_func );
	add_action( 'network_admin_notices', $mpn_plugin_error_func );
}
