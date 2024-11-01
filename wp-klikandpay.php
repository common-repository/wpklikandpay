<?php
/*
Plugin Name: wpklikandpay
Description: This plugin allows to add a klik and pay payment form on a wordpress website
Version: 1.2
Author: Eoxia
Author URI: http://www.eoxia.com
*/

/**
* Plugin main file.
*
*	This file is the main file called by wordpress for our plugin use. It define the basic vars and include the different file needed to use the plugin
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-klikandpay
*/

/**
*	First thing we define the main directory for our plugin in a super global var
*/
DEFINE('WPKLIKANDPAY_PLUGIN_DIR', basename(dirname(__FILE__)));
/**
*	Include the different config for the plugin
*/
require_once(WP_PLUGIN_DIR . '/' . WPKLIKANDPAY_PLUGIN_DIR . '/includes/configs/config.php' );
/**
*	Define the path where to get the config file for the plugin
*/
DEFINE('WPKLIKANDPAY_CONFIG_FILE', WPKLIKANDPAY_INC_PLUGIN_DIR . 'configs/config.php');
/**
*	Include the file which includes the different files used by all the plugin
*/
require_once(	WPKLIKANDPAY_INC_PLUGIN_DIR . 'includes.php' );

/*	Create an instance for the database option	*/
$wpklikandpay_db_option = new wpklikandpay_db_option();

/**
*	Include tools that will launch different action when plugin will be loaded
*/
require_once(WPKLIKANDPAY_LIB_PLUGIN_DIR . 'install.class.php' );
/**
*	On plugin loading, call the different element for creation output for our plugin
*/
register_activation_hook( __FILE__ , array('wpklikandpay_install', 'wpklikandpay_activate') );
register_deactivation_hook( __FILE__ , array('wpklikandpay_install', 'wpklikandpay_deactivate') );

/**
*	Include tools that will launch different action when plugin will be loaded
*/
require_once(WPKLIKANDPAY_LIB_PLUGIN_DIR . 'init.class.php' );
/**
*	On plugin loading, call the different element for creation output for our plugin
*/
add_action('plugins_loaded', array('wpklikandpay_init', 'wpklikandpay_plugin_load'));

add_shortcode('wp-klikandpay_payment_return', array('wpklikandpay_orders', 'paymentReturn'));
add_shortcode('wpklikandpay_payment_form', array('wpklikandpay_payment_form', 'displayForm'));