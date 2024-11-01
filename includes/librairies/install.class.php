<?php
/**
 * Plugin Installer
 * 
 * Define the different action when activate the plugin. Create the different element as option and database, set the users' permissions
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 * @package wp-klikandpay
 * @subpackage librairies
 */

/**
 * Define the different action when activate the plugin. Create the different element as option and database, set the users' permissions
 * @package wp-klikandpay
 * @subpackage librairies
 */
class wpklikandpay_install
{

	/**
	*	Define actions lauched after plugin activation. Create the database, create the different option, call the permisssion setters
	*/
	function wpklikandpay_activate()
	{
		global $wpklikandpay_db_option;
	
		/*	Create an instance for the database option	*/
		$wpklikandpay_db_option = new wpklikandpay_db_option();
		$wpklikandpay_db_option->create_db_option();

		/*	Create 	*/
		wpklikandpay_database::wpklikandpay_db_creation();

		/*	Create the option for the store option	*/
		$wpklikandpay_store_mainoption = get_option('wpklikandpay_store_mainoption');
		if($wpklikandpay_store_mainoption == '')
		{
			unset($optionList);$optionList = array();
			$optionList['storeTpe'] = $testEnvironnement['tpe'];
			$optionList['storeRang'] = $testEnvironnement['rang'];
			$optionList['storeIdentifier'] = $testEnvironnement['identifier'];
			$optionList['environnement'] = 'test';
			wpklikandpay_option::saveStoreConfiguration('wpklikandpay_store_mainoption', $optionList, false);
		}

		/*	Create the option for the return url	*/
		$wpklikandpay_store_urloption = get_option('wpklikandpay_store_urloption');
		if($wpklikandpay_store_urloption == '')
		{
			unset($optionList);$optionList = array();
			$optionList['urlSuccess'] = get_bloginfo('siteurl') . '/';
			$optionList['urlDeclined'] = get_bloginfo('siteurl') . '/';
			$optionList['urlCanceled'] = get_bloginfo('siteurl') . '/';
			wpklikandpay_option::saveStoreConfiguration('wpklikandpay_store_urloption', $optionList, false);
		}

		/*	Set the different permissions	*/
		wpklikandpay_install::wpklikandpay_set_permissions();
	}

	/**
	*	Define actions launched when plugin is deactivate.
	*/
	function wpklikandpay_deactivate()
	{
		global $wpdb;

		// $wpdb->query("DROP TABLE " . WPKLIKANDPAY_DBT_ORDERS . ", " . WPKLIKANDPAY_DBT_FORMS . ", " . WPKLIKANDPAY_DBT_OFFERS . ", " . WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS . ";");
		// delete_option('wpklikandpay_store_urloption');
		// delete_option('wpklikandpay_store_urloption');
		// delete_option('wpklikandpay_db_option');
	}

	/**
	*	Define the different permissions affected to users.
	*/
	function wpklikandpay_set_permissions()
	{
		$wpklikandpay_permission_list = array();
		$wpklikandpay_permission_list[] = 'wpklikandpay_manage_options';

		$wpklikandpay_permission_list[] = 'wpklikandpay_view_orders';
		$wpklikandpay_permission_list[] = 'wpklikandpay_view_orders_details';
		$wpklikandpay_permission_list[] = 'wpklikandpay_delete_orders';

		$wpklikandpay_permission_list[] = 'wpklikandpay_view_forms';
		$wpklikandpay_permission_list[] = 'wpklikandpay_view_forms_details';
		$wpklikandpay_permission_list[] = 'wpklikandpay_add_forms';
		$wpklikandpay_permission_list[] = 'wpklikandpay_edit_forms';
		$wpklikandpay_permission_list[] = 'wpklikandpay_delete_forms';

		$wpklikandpay_permission_list[] = 'wpklikandpay_view_forms_offers_link';
		$wpklikandpay_permission_list[] = 'wpklikandpay_delete_forms_offers_link';

		$wpklikandpay_permission_list[] = 'wpklikandpay_view_offers';
		$wpklikandpay_permission_list[] = 'wpklikandpay_view_offers_details';
		$wpklikandpay_permission_list[] = 'wpklikandpay_add_offers';
		$wpklikandpay_permission_list[] = 'wpklikandpay_edit_offers';
		$wpklikandpay_permission_list[] = 'wpklikandpay_delete_offers';

		/**
		*	Add capabilities to the administrator role
		*/
		$role = get_role('administrator');
		foreach($wpklikandpay_permission_list as $permission)
		{
			if( ($role != null) && !$role->has_cap($permission) ) 
			{
				$role->add_cap($permission);
			}
		}
		unset($role);

		$wpklikandpay_permission_list = array();

		$wpklikandpay_permission_list[] = 'wpklikandpay_view_orders';
		$wpklikandpay_permission_list[] = 'wpklikandpay_view_orders_details';

		$wpklikandpay_permission_list[] = 'wpklikandpay_view_forms';
		$wpklikandpay_permission_list[] = 'wpklikandpay_view_forms_details';

		$wpklikandpay_permission_list[] = 'wpklikandpay_view_forms_offers_link';

		$wpklikandpay_permission_list[] = 'wpklikandpay_view_offers';
		$wpklikandpay_permission_list[] = 'wpklikandpay_view_offers_details';
		/**
		*	Add capabilities to the editor role
		*/
		$role = get_role('editor');
		foreach($wpklikandpay_permission_list as $permission)
		{
			if( ($role != null) && !$role->has_cap($permission) ) 
			{
				$role->add_cap($permission);
			}
		}
		unset($role);
	}

}