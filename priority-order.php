<?php

/**
 * @link              https://syntac.co.id
 * @since             1.0.0
 * @package           Priority_Order
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Priority Order
 * Plugin URI:        https://wordpress.org/plugins/priority-order/
 * Description:       Push WooCommerce orders to order priority
 * Version:           1.0.2
 * Author:            Syntac Studio
 * Author URI:        https://syntac.co.id
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       priority-order
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

function activate_priority_order() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-priority-order-activator.php';
	Priority_Order_Activator::activate();
}

function deactivate_priority_order() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-priority-order-deactivator.php';
	Priority_Order_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_priority_order' );
register_deactivation_hook( __FILE__, 'deactivate_priority_order' );


require plugin_dir_path( __FILE__ ) . 'includes/class-priority-order.php';

/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */
function run_priority_order() {

	$plugin = new Priority_Order();
	$plugin->run();

}
run_priority_order();