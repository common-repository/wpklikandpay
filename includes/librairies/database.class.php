<?php
/**
 * Database management
 *
 * Define the different method to access to database, for database creation and update for the different version
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 * @package wp-klikandpay
 * @subpackage librairies
 */

/**
* Define the different method to access to database, for database creation and update for the different version
* @package wp-klikandpay
* @subpackage librairies
*/
class wpklikandpay_database
{

	/**
	* Define the different database element to create for each plugin's version
	*/
	function wpklikandpay_db_creation()
	{
		global $wpdb;
		global $wpklikandpay_db_option;

		/*	Check the current version	*/
		$currentVersion = $wpklikandpay_db_option->get_db_version();

		if($currentVersion == 0)
		{/*	Create the different table and add the data	. Check whether the table exist or not	*/

		/*	ORDERS	*/
			if( $wpdb->get_var("show tables like '" . WPKLIKANDPAY_DBT_ORDERS . "'") != WPKLIKANDPAY_DBT_ORDERS)
			{
				$query =
					"CREATE TABLE IF NOT EXISTS " . WPKLIKANDPAY_DBT_ORDERS . " (
						id int(10) unsigned NOT NULL auto_increment,
						status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						order_status enum('none','initialised','in_progress','error','closed') collate utf8_unicode_ci NOT NULL default 'none',
						creation_date datetime,
						last_update_date datetime,
						form_id int(10) unsigned NOT NULL,
						offer_id int(10) unsigned NOT NULL,
						payment_type enum('single_payment', 'multiple_payment', 'subscription_payment') collate utf8_unicode_ci NOT NULL default 'single_payment',
						payment_recurrent_amount char(10) default '0',
						payment_recurrent_number char(2),
						payment_recurrent_date char(2) default '0',
						payment_name char(255) collate utf8_unicode_ci NOT NULL,
						payment_amount char(10) collate utf8_unicode_ci NOT NULL,
						payment_reference_prefix char(255) collate utf8_unicode_ci NOT NULL,
						payment_subscription_reference char(255) collate utf8_unicode_ci NOT NULL,
						user_email char(255) collate utf8_unicode_ci NOT NULL,
						user_lastname char(255) collate utf8_unicode_ci NOT NULL,
						user_firstname char(255) collate utf8_unicode_ci NOT NULL,
						user_phone char(255) collate utf8_unicode_ci NOT NULL,
						user_adress char(255) collate utf8_unicode_ci NOT NULL,
						user_postal_code char(255) collate utf8_unicode_ci NOT NULL,
						user_postal_town char(255) collate utf8_unicode_ci NOT NULL,
						user_postal_state char(255) collate utf8_unicode_ci NOT NULL,
						user_postal_country char(255) collate utf8_unicode_ci NOT NULL,
						user_birthday char(255) collate utf8_unicode_ci NOT NULL,
						order_amount char(10) NOT NULL,
						order_transaction varchar(255) collate utf8_unicode_ci NOT NULL,
						order_transaction_nb varchar(255) collate utf8_unicode_ci NOT NULL,
						order_reference varchar(255) collate utf8_unicode_ci NOT NULL,
						PRIMARY KEY (id),
						KEY status (status),
						KEY form_id (form_id),
						KEY order_status (order_status),
						UNIQUE order_reference (order_reference)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The order list'; ";
				$wpdb->query($query);
			}

		/*	FORMS	*/
			if( $wpdb->get_var("show tables like '" . WPKLIKANDPAY_DBT_FORMS . "'") != WPKLIKANDPAY_DBT_FORMS)
			{
				$query =
					"CREATE TABLE IF NOT EXISTS " . WPKLIKANDPAY_DBT_FORMS . " (
						id int(10) unsigned NOT NULL auto_increment,
						status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						creation_date datetime,
						last_update_date datetime,
						payment_form_name char(255) collate utf8_unicode_ci NOT NULL,
						payment_form_button_content char(255) collate utf8_unicode_ci NOT NULL default '" . __('Valider mon paiement', 'wpklikandpay') . "',
						payment_form_mandatory_fields longtext collate utf8_unicode_ci NOT NULL,
						PRIMARY KEY (id),
						KEY status (status)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The form list'; ";
				$wpdb->query($query);
			}

		/*	OFFERS	*/
			if( $wpdb->get_var("show tables like '" . WPKLIKANDPAY_DBT_OFFERS . "'") != WPKLIKANDPAY_DBT_OFFERS)
			{
				$query =
					"CREATE TABLE IF NOT EXISTS " . WPKLIKANDPAY_DBT_OFFERS . " (
						id int(10) unsigned NOT NULL auto_increment,
						status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						creation_date datetime,
						last_update_date datetime,
						payment_type enum('single_payment', 'multiple_payment', 'subscription_payment') collate utf8_unicode_ci NOT NULL default 'single_payment',
						payment_recurrent_amount char(10) default '0',
						payment_recurrent_number char(2),
						payment_recurrent_date char(2) default '0',
						payment_name char(255) collate utf8_unicode_ci NOT NULL,
						payment_amount char(10) collate utf8_unicode_ci NOT NULL,
						payment_reference_prefix char(255) collate utf8_unicode_ci NOT NULL,
						payment_subscription_reference char(255) collate utf8_unicode_ci NOT NULL,
						PRIMARY KEY (id),
						KEY status (status),
						KEY payment_type (payment_type)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The offer list'; ";
				$wpdb->query($query);
			}

		/*	LINK FORMS OFFERS	*/
			if( $wpdb->get_var("show tables like '" . WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS . "'") != WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS)
			{
				$query =
					"CREATE TABLE IF NOT EXISTS " . WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS . " (
						id int(10) unsigned NOT NULL auto_increment,
						status enum('valid','moderated','deleted') collate utf8_unicode_ci NOT NULL default 'valid',
						creation_date datetime,
						last_update_date datetime,
						form_id int(10) unsigned NOT NULL,
						offer_id int(10) unsigned NOT NULL,
						PRIMARY KEY (id),
						KEY status (status),
						KEY form_id (form_id),
						KEY offer_id (offer_id)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='The link between forms and offers';";
				$wpdb->query($query);
			}

			$wpklikandpay_db_option->set_db_version($currentVersion + 1);
			$wpklikandpay_db_option->set_db_option();

			wpklikandpay_database::wpklikandpay_db_insert($currentVersion + 1);
		}
	}

	/**
	* Optimize the different database table
	*/
	function wpklikandpay_db_optimisation()
	{
		global $wpdb;

		$query = "OPTIMIZE TABLE " . WPKLIKANDPAY_DBT_ORDERS;
		$wpdb->query($query);

		$query = "OPTIMIZE TABLE " . WPKLIKANDPAY_DBT_FORMS;
		$wpdb->query($query);

		$query = "OPTIMIZE TABLE " . WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS;
		$wpdb->query($query);

		$query = "OPTIMIZE TABLE " . WPKLIKANDPAY_DBT_OFFERS;
		$wpdb->query($query);
	}

	/**
	* Define the different database element to create for each plugin's version
	*/
	function wpklikandpay_db_update()
	{
		global $wpdb;
		global $wpklikandpay_db_option;

		/*	Check the current version	*/
		$currentVersion = $wpklikandpay_db_option->get_db_version();

		if($currentVersion == 1)
		{/*	Add a field to specify a specific title for an offer into a given form 	*/
			$query = $wpdb->prepare("ALTER TABLE " . WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS . " ADD offer_title CHAR( 255 ) NOT NULL", "");
			$wpdb->query($query);

			$wpklikandpay_db_option->set_db_version($currentVersion + 1);
			$wpklikandpay_db_option->set_db_option();

			wpklikandpay_database::wpklikandpay_db_insert($currentVersion);
		}
		elseif($currentVersion == 2)
		{/*	Add a field to specify a specific title for an offer into a given form 	*/
			$query = $wpdb->prepare("ALTER TABLE " . WPKLIKANDPAY_DBT_FORMS . " ADD payment_form_cgv_url CHAR( 255 ) NOT NULL AFTER payment_form_button_content", "");
			$wpdb->query($query);

			$wpklikandpay_db_option->set_db_version($currentVersion + 1);
			$wpklikandpay_db_option->set_db_option();

			wpklikandpay_database::wpklikandpay_db_insert($currentVersion);
		}
		elseif($currentVersion == 3)
		{/*	Add a field to specify the length of an offer, just for information 	*/
			$query = $wpdb->prepare("ALTER TABLE " . WPKLIKANDPAY_DBT_OFFERS . " ADD payment_subscription_length CHAR( 255 ) NOT NULL AFTER payment_subscription_reference", "");
			$wpdb->query($query);

			$query = $wpdb->prepare("ALTER TABLE " . WPKLIKANDPAY_DBT_ORDERS . " ADD payment_subscription_length CHAR( 255 ) NOT NULL AFTER payment_subscription_reference", "");
			$wpdb->query($query);

			$wpklikandpay_db_option->set_db_version($currentVersion + 1);
			$wpklikandpay_db_option->set_db_option();

			wpklikandpay_database::wpklikandpay_db_insert($currentVersion);
		}
	}

	/**
	* Define the different database element to insert for each plugin's version
	*/
	function wpklikandpay_db_insert($versionNumber)
	{
		global $wpdb;
		global $currencyIconList;

		switch($versionNumber)
		{
			case 1:
			{	/* Insert the different offer title into the link database	*/
				/*	Get the existing list of link between offers and form	*/
				$query = $wpdb->prepare("SELECT * FROM " . WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS, "");
				$formOffersLink = $wpdb->get_results($query);
				foreach($formOffersLink as $offer)
				{
					/*	Get the offer information in order to create a basic title in first time form the current offers	*/
					$offerInfo = wpklikandpay_offers::getElement($offer->offer_id);
					$offerTitleForForm = wpklikandpay_offers::generateOfferTitle($offerInfo);

					/*	Set the new offer title	*/
					$linkBetweenOfferAndForm = array();
					$linkBetweenOfferAndForm['offer_title'] = $offerTitleForForm;
					wpklikandpay_database::update($linkBetweenOfferAndForm, $offer->id, WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS);
				}
			}
			break;
		}

	}

	/**
	*	Prepare the different field before use them in the query
	*
	*	@param array $prm An array containing the fields to prepare
	*	@param mixed $operation The type of query we are preparing the vars for
	*
	*	@return mixed $preparedFields The fields ready to be injected in the query
	*/
	function prepare_query($prm, $operation = 'creation')
	{
		$preparedFields = array();

		foreach($prm as $field => $value)
		{
			if($field != 'id')
			{
				if($operation == 'creation')
				{
					$preparedFields['fields'][] = $field;
					$preparedFields['values'][] = "'" . mysql_real_escape_string($value) . "'";
				}
				elseif($operation == 'update')
				{
					$preparedFields['values'][] = $field . " = '" . mysql_real_escape_string($value) . "'";
				}
			}
		}

		return $preparedFields;
	}

	/**
	*	Get the field list into a database table
	*
	*	@param string $table_name The name of the table we want to retrieve field list for
	*
	*	@return object $field_list A wordpress database object containing the different field of the table
	*/
	function get_field_list($table_name)
	{
		global $wpdb;

		$query = $wpdb->prepare("SHOW COLUMNS FROM " . $table_name, "");
		$field_list = $wpdb->get_results($query);

		return $field_list;
	}
	/**
	*	Get a field defintion into a database table
	*
	*	@param string $table_name The name of the table we want to retrieve field list for
	*
	*	@return object $field A wordpress database object containing the field definition into the database table
	*/
	function get_field_definition($table_name, $field)
	{
		global $wpdb;

		$query = $wpdb->prepare("SHOW COLUMNS FROM " . $table_name . " WHERE Field = %s", $field);
		$fieldDefinition = $wpdb->get_results($query);

		return $fieldDefinition;
	}

	/**
	*	Make a translation of the different database field type into a form input type
	*
	*	@param string $table_name The name of the table we want to retrieve field input type for
	*
	*	@return array $field_to_form An array with the list of field with its type, name and value
	*/
	function fields_to_input($table_name)
	{

		$list_of_field_to_convert = wpklikandpay_database::get_field_list($table_name);

		$field_to_form = wpklikandpay_database::fields_type($list_of_field_to_convert);

		return $field_to_form;
	}

	function fields_type($list_of_field_to_convert)
	{
		$field_to_form = array();
		$i = 0;
		foreach ($list_of_field_to_convert as $Key => $field_definition){

			$field_to_form[$i]['name'] = $field_definition->Field;
			$field_to_form[$i]['value'] = $field_definition->Default;

			$type = 'text';
			if(($field_definition->Key == 'PRI'))
			{
				$type =  'hidden';
			}
			else
			{
				$fieldtype = explode('(',$field_definition->Type);
				if($fieldtype[1] != '')$fieldtype[1] = str_replace(')','',$fieldtype[1]);

				if(($fieldtype[0] == 'char') || ($fieldtype[0] == 'varchar') || ($fieldtype[0] == 'int'))
				{
					$type = 'text';
				}
				elseif($fieldtype[0] == 'text')
				{
					$type = 'textarea';
				}
				elseif($fieldtype[0] == 'enum')
				{
					$fieldtype[1] = str_replace("'","",$fieldtype[1]);
					$possible_value = explode(",",$fieldtype[1]);

					if(count($possible_value) > 1)
					{
						$type = 'select';
					}
					else
					{
						$type = 'radio';
					}

					$field_to_form[$i]['possible_value'] = $possible_value;
				}
			}
			$field_to_form[$i]['type'] = $type;

			$i++;
		}
		return $field_to_form;
	}

	/**
	*	Save a new attribute in database
	*
	*	@param array $informationsToSet An array with the different information we want to set
	*
	*	@return string $requestResponse A message that allows to know if the creation has been done correctly or not
	*/
	function save($informationsToSet, $dataBaseTable)
	{
		global $wpdb;
		$requestResponse = '';

		$whatToUpdate = wpklikandpay_database::prepare_query($informationsToSet, 'creation');
		$query = $wpdb->prepare(
			"INSERT INTO " . $dataBaseTable . "
			(" . implode(', ', $whatToUpdate['fields']) . ")
			VALUES
			(" . implode(', ', $whatToUpdate['values']) . ") "
		, "");

		if( $wpdb->query($query) )
		{
			$requestResponse = 'done';
		}
		else
		{
			$requestResponse = 'error';
		}

		return $requestResponse;
	}
	/**
	*	Update an existing attribute in database
	*
	*	@param array $informationsToSet An array with the different information we want to set
	*
	*	@return string $requestResponse A message that allows to know if the update has been done correctly or not
	*/
	function update($informationsToSet, $id, $dataBaseTable)
	{
		global $wpdb;
		$requestResponse = '';

		$whatToUpdate = wpklikandpay_database::prepare_query($informationsToSet, 'update');
		$query = $wpdb->prepare(
			"UPDATE " . $dataBaseTable . "
			SET " . implode(', ', $whatToUpdate['values']) . "
			WHERE id = '%s' ",
			$id
		, "");
		if( $wpdb->query($query) )
		{
			$requestResponse = 'done';
		}
		elseif( $wpdb->query($query) == 0 )
		{
			$requestResponse = 'nothingToUpdate';
		}
		else
		{
			$requestResponse = 'error';
		}

		return $requestResponse;
	}

}