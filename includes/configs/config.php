<?php
/**
* Main config file for the pluging
* 
* The non-specific config will be found in this file, other config files includes too
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-klikandpay
* @subpackage config
*/


/**
*	Start main plugin variable definition
*/
{
	DEFINE('WPKLIKANDPAY_VERSION', '1.0');
	DEFINE('WPKLIKANDPAY_DEBUG', false);

	DEFINE('WPKLIKANDPAY_OPTION_MENU', 'wpklikandpay_options');

	DEFINE('WPKLIKANDPAY_URL_SLUG_ORDERS_LISTING', 'wpklikandpay_orders');
	DEFINE('WPKLIKANDPAY_URL_SLUG_ORDERS_EDITION', 'wpklikandpay_orders');

	DEFINE('WPKLIKANDPAY_URL_SLUG_FORMS_LISTING', 'wpklikandpay_forms');
	DEFINE('WPKLIKANDPAY_URL_SLUG_FORMS_EDITION', 'wpklikandpay_forms');

	DEFINE('WPKLIKANDPAY_URL_SLUG_OFFERS_LISTING', 'wpklikandpay_offers');
	DEFINE('WPKLIKANDPAY_URL_SLUG_OFFERS_EDITION', 'wpklikandpay_offers');
}


/**
*	Start plugin path definition
*/
{
	DEFINE('WPKLIKANDPAY_HOME_URL', WP_PLUGIN_URL . '/' . WPKLIKANDPAY_PLUGIN_DIR . '/');
	DEFINE('WPKLIKANDPAY_HOME_DIR', WP_PLUGIN_DIR . '/' . WPKLIKANDPAY_PLUGIN_DIR . '/');
	
	DEFINE('WPKLIKANDPAY_INC_PLUGIN_DIR', WPKLIKANDPAY_HOME_DIR . 'includes/');
	DEFINE('WPKLIKANDPAY_LIB_PLUGIN_DIR', WPKLIKANDPAY_INC_PLUGIN_DIR . 'librairies/');

	DEFINE('WPKLIKANDPAY_CSS_URL', WPKLIKANDPAY_HOME_URL . 'css/');
	DEFINE('WPKLIKANDPAY_JS_URL', WPKLIKANDPAY_HOME_URL . 'js/');
}


/**
*	Start database definition
*/
{
	/**
	* Get the global wordpress prefix for database table
	*/
	global $wpdb;
	/**
	* Define the main plugin prefix
	*/
	DEFINE('WPKLIKANDPAY_DB_PREFIX', $wpdb->prefix . "klikandpay__");
	/**
	*	Define the table wich will contain the different informations about the user and its payment
	*/
	DEFINE('WPKLIKANDPAY_DBT_ORDERS', WPKLIKANDPAY_DB_PREFIX . 'orders');
	/**
	*	Define the table wich will contain the different informations about the form to create
	*/
	DEFINE('WPKLIKANDPAY_DBT_FORMS', WPKLIKANDPAY_DB_PREFIX . 'forms');
	/**
	*	Define the table wich will contain the different existing offers
	*/
	DEFINE('WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS', WPKLIKANDPAY_DB_PREFIX . 'forms_offers_link');
	/**
	*	Define the table wich will contain the different existing offers
	*/
	DEFINE('WPKLIKANDPAY_DBT_OFFERS', WPKLIKANDPAY_DB_PREFIX . 'offers');
}


/**
*	Start picture definition
*/
{
	DEFINE('WPKLIKANDPAY_SUCCES_ICON', admin_url('images/yes.png'));
	DEFINE('WPKLIKANDPAY_ERROR_ICON', admin_url('images/no.png'));
}

/**
*	Define the currency list
*/
{
	$currencyList = array();
	$currencyList[978] = __('Euro', 'wpklikandpay');
	$currencyList[840] = __('US Dollar', 'wpklikandpay');

	$currencyIconList = array();
	$currencyIconList[978] = '&euro;';
	$currencyIconList[840] = '&dollar;';
}

/**
*	Define the test environnement vars
*/
{
	$testEnvironnement['single_payment']['url'] = 'https://www.klikandpay.com/paiementtest/check.pl';
	$testEnvironnement['multiple_payment']['url'] = 'https://www.klikandpay.com/paiementtest/checkxfois.pl';
	$testEnvironnement['subscription_payment']['url'] = 'https://www.klikandpay.com/paiementtest/checkabon.pl';

	$productionEnvironnement['single_payment']['url'] = 'https://www.klikandpay.com/paiement/check.pl';
	$productionEnvironnement['multiple_payment']['url'] = 'https://www.klikandpay.com/paiement/checkxfois.pl';
	$productionEnvironnement['subscription_payment']['url'] = 'https://www.klikandpay.com/paiement/checkabon.pl';
}

/**
*	Define the field to hide into a combobox
*/
{
	$comboxOptionToHide = array('deleted');
}