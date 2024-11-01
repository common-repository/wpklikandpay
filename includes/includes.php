<?php
/**
* Here we include every common file needed for the plugin. Just the file used in the entire plugin
* 
* @author Eoxia <dev@eoxia.com>
* @version 1.1
* @package wp-klikandpay
* @subpackage includes
*/

/**
* Include the database option management file
*/
require_once('librairies/options/db_options.class.php');
/**
* Include the plugin option management file
*/
require_once('librairies/options/options.class.php');
/**
* Include the tools to manage plugin's display
*/
require_once('librairies/display.class.php');
/**
* Include the tools
*/
require_once('librairies/tools.class.php');
/**
* Include the tools to manage database plugin
*/
require_once('librairies/database.class.php');
/**
* Include the tools to manage form into the plugin
*/
require_once('librairies/form.class.php');

/**
* Include the tools to manage order forms
*/
require_once(WPKLIKANDPAY_LIB_PLUGIN_DIR . 'payment_forms.class.php');
/**
* Include the tools to manage orders
*/
require_once(WPKLIKANDPAY_LIB_PLUGIN_DIR . 'orders.class.php');
/**
* Include the tools to manage orders
*	@since v1.1
*/
require_once(WPKLIKANDPAY_LIB_PLUGIN_DIR . 'offers.class.php');