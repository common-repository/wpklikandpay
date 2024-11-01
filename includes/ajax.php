<?php
/**
* Plugin ajax request management. Every ajax request are send to this file which send the response back
* 
* @author Eoxia <dev@eoxia.com>
* @version 1.1
* @package wp-klikandpay
* @subpackage includes
*/

/**
*	Include wordpress tools
*/
DEFINE('DOING_AJAX', true);
DEFINE('WP_ADMIN', true);
require_once('../../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/admin.php');

/**
*	First thing we define the main directory for our plugin in a super global var	
*/
DEFINE('WPKLIKANDPAY_PLUGIN_DIR', basename(dirname(__FILE__)));
/**
*	Include the different common classes and scripts
*/
require_once( './includes.php' );


/*	START OF FILE CONTENT	*/