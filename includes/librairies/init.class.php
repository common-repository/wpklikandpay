<?php
/**
* Plugin Loader
* 
* Define the different element usefull for the plugin usage. The menus, includes script, start launch script, css, translations
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-klikandpay
* @subpackage librairies
*/

/**
* Define the different element usefull for the plugin usage. The menus, includes script, start launch script, css, translations
* @package wp-klikandpay
* @subpackage librairies
*/
class wpklikandpay_init
{

	/**
	*	Load the different element need to create the plugin environnement
	*/
	function wpklikandpay_plugin_load()
	{
		global $wpklikandpay_db_option;

		/*	Call function to create the main left menu	*/
		add_action('admin_menu', array('wpklikandpay_init', 'wpklikandpay_menu') );

		/*	Get the current language to translate the different text in plugin	*/
		$locale = get_locale();
		$moFile = WPKLIKANDPAY_INC_PLUGIN_DIR . 'languages/wp-klikandpay-' . $locale . '.mo';
		if( !empty($locale) && (is_file($moFile)) )
		{
			load_textdomain('wpklikandpay', $moFile);
		}

		/*	Do the update on the database	*/
		wpklikandpay_database::wpklikandpay_db_update();

		/*	Check the last optimisation date if it was not perform today weoptimise the database	*/
		if($wpklikandpay_db_option->get_db_optimisation_date() != date('Y-m-d'))
		{
			wpklikandpay_database::wpklikandpay_db_optimisation();

			$wpklikandpay_db_option->set_db_optimisation_date(date('Y-m-d'));
			$wpklikandpay_db_option->set_db_option();
		}

		/*	Include the different css	*/
		add_action('init', array('wpklikandpay_init', 'wpklikandpay_front_css') );
		/*	Include the different css	*/
		add_action('init', array('wpklikandpay_init', 'wpklikandpay_front_js') );
		/*	Include the different css	*/
		add_action('admin_init', array('wpklikandpay_init', 'wpklikandpay_admin_css') );
		/*	Include the different js	*/
		add_action('admin_init', array('wpklikandpay_init', 'wpklikandpay_admin_js') );
	}

	/**
	*	Create the main left menu with different parts
	*/
	function wpklikandpay_menu() 
	{
		/*	Add the options menu in the options section	*/
		add_options_page(__('Options principale du module de paiement Klik and pay', 'wpklikandpay'), __('Klik and Pay', 'wpklikandpay'), 'wpklikandpay_manage_options', WPKLIKANDPAY_OPTION_MENU, array('wpklikandpay_option', 'doOptionsPage'));

		/*	Main menu */
		add_menu_page(__('Liste des commandes', 'wpklikandpay' ), __('Klik and Pay', 'wpklikandpay' ), 'wpklikandpay_view_orders', WPKLIKANDPAY_URL_SLUG_ORDERS_LISTING, array('wpklikandpay_display', 'displayPage'));

		/*	Redefine the dashboard page	*/
		add_submenu_page( WPKLIKANDPAY_URL_SLUG_ORDERS_LISTING, wpklikandpay_orders::pageTitle(), __('Commandes', 'wpklikandpay' ), 'wpklikandpay_view_orders', WPKLIKANDPAY_URL_SLUG_ORDERS_LISTING, array('wpklikandpay_display','displayPage'));
		add_submenu_page( WPKLIKANDPAY_URL_SLUG_ORDERS_LISTING, wpklikandpay_payment_form::pageTitle(), __('Formulaires', 'wpklikandpay' ), 'wpklikandpay_view_forms', WPKLIKANDPAY_URL_SLUG_FORMS_LISTING, array('wpklikandpay_display','displayPage'));
		add_submenu_page( WPKLIKANDPAY_URL_SLUG_ORDERS_LISTING, wpklikandpay_offers::pageTitle(), __('Offres', 'wpklikandpay' ), 'wpklikandpay_view_offers', WPKLIKANDPAY_URL_SLUG_OFFERS_LISTING, array('wpklikandpay_display','displayPage'));
	}

	/**
	*	Define the javascript to include in each page
	*/
	function wpklikandpay_admin_js()
	{
		if(!wp_script_is('jquery', 'queue'))
		{
			wp_enqueue_script('jquery');
		}
		if(!wp_script_is('jquery-ui-core', 'queue'))
		{
			wp_enqueue_script('jquery-ui-core');
		}
		wp_enqueue_script('wpklikandpay_main_js', WPKLIKANDPAY_JS_URL . 'wpklikandpay.js');
	}

	/**
	*	Define the javascript to include in each page
	*/
	function wpklikandpay_front_js()
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('wpklikandpay_main_js', WPKLIKANDPAY_JS_URL . 'wpklikandpay.js');
	}

	/**
	*	Define the css to include in each page
	*/
	function wpklikandpay_admin_css()
	{
		wp_register_style('wpklikandpay_jquery-ui', WPKLIKANDPAY_CSS_URL . 'jquery-ui.css');
		wp_enqueue_style('wpklikandpay_jquery-ui');
		wp_register_style('wpklikandpay_main_css', WPKLIKANDPAY_CSS_URL . 'wpklikandpay.css');
		wp_enqueue_style('wpklikandpay_main_css');
	}

	/**
	*	Define the css to include in frontend
	*/
	function wpklikandpay_front_css()
	{
		wp_register_style('wpklikandpay_front_main_css', WPKLIKANDPAY_CSS_URL . 'wpklikandpay_front.css');
		wp_enqueue_style('wpklikandpay_front_main_css');
	}

}