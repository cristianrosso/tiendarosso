<?php
/**
 * Plugin Name: TIENDA ROSSO Commerce
 * Plugin URI: https://github.com/cristianrosso/tiendarosso
 * Description: Planes, solicitudes comerciales, WhatsApp configurable y base desacoplada para el marketplace.
 * Version: 0.2.0
 * Requires at least: 7.1
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * Author: TIENDA ROSSO
 * Text Domain: tiendarosso-commerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TR_COMMERCE_VERSION', '0.2.0' );
define( 'TR_COMMERCE_FILE', __FILE__ );
define( 'TR_COMMERCE_DIR', plugin_dir_path( __FILE__ ) );
define( 'TR_COMMERCE_URL', plugin_dir_url( __FILE__ ) );

require_once TR_COMMERCE_DIR . 'includes/class-tr-roles.php';
require_once TR_COMMERCE_DIR . 'includes/class-tr-post-types.php';
require_once TR_COMMERCE_DIR . 'includes/class-tr-settings.php';
require_once TR_COMMERCE_DIR . 'includes/class-tr-forms.php';
require_once TR_COMMERCE_DIR . 'includes/class-tr-blocks.php';
require_once TR_COMMERCE_DIR . 'includes/class-tr-commerce.php';

register_activation_hook( __FILE__, array( 'TR_Commerce', 'activate' ) );
add_action( 'plugins_loaded', array( 'TR_Commerce', 'boot' ) );
