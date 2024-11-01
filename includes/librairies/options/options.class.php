<?php
/**
 * Plugin options
 * 
 * Allows to manage the different option for the plugin
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 * @package wp-klikandpay
 * @subpackage librairies
 */

/**
 * Allows to manage the different option for the plugin
 * @package wp-klikandpay
 * @subpackage librairies
 */
class wpklikandpay_option
{
	/**
	*	Function to get the different value for a given option
	*
	*	@param string $optionToGet The option we want to get the value for
	*	@param string $fieldToGet The specific value we want to get
	*
	*	@return mixed $optionValue The option value we want to get
	*/
	function getStoreConfigOption($optionToGet, $fieldToGet)
	{
		$optionValue = '';

		$option = get_option($optionToGet);
		if(!is_array($option))
		{
			$option = unserialize($option);
		}
		if(isset($option[$fieldToGet]))
		{
			$optionValue = $option[$fieldToGet];
		}

		return $optionValue;
	}
	/**
	*	Function to save an option into wordpress option database table
	*/
	function saveStoreConfiguration($optionToGet, $optionList, $outputMessage = true)
	{
		$updateOptionResult = update_option($optionToGet, serialize($optionList));
		if((($updateOptionResult == 1) || ($updateOptionResult == '')) && ($outputMessage))
		{
			echo '<div class="updated optionMessage" >' . __('Les options ont bien &eacute;t&eacute; enregistr&eacute;es', 'wpklikandpay') . '</div>';
		}
	}

	/**
	*	Create the main option page for the plugin
	*/
	function doOptionsPage()
	{
		/*	Declare the different settings	*/
		register_setting('wpklikandpay_store_config_group', 'storeTpe', '' );
		// register_setting('wpklikandpay_store_config_group', 'storeRang', '' );
		// register_setting('wpklikandpay_store_config_group', 'storeIdentifier', '' );
		register_setting('wpklikandpay_store_config_group', 'environnement', '' );
		// register_setting('wpklikandpay_store_config_group', 'urlSuccess', '' );
		// register_setting('wpklikandpay_store_config_group', 'urlDeclined', '' );
		// register_setting('wpklikandpay_store_config_group', 'urlCanceled', '' );
		settings_fields( 'wpklikandpay_url_config_group' );

		/*	Add the section about the store main configuration	*/
		add_settings_section('wpklikandpay_store_config', __('Informations de la boutique', 'wpklikandpay'), array('wpklikandpay_option', 'storeConfigForm'), 'wpklikandpayStoreConfig');

		/*	Add the section about the back url	*/
		// add_settings_section('wpklikandpay_url_config', __('Urls de retour apr&eacute;s un paiement', 'wpklikandpay'), array('wpklikandpay_option', 'urlConfigForm'), 'wpklikandpayUrlConfig');
?>
<form action="" method="post" >
<input type="hidden" name="saveOption" id="saveOption" value="save" />
	<?php 
		do_settings_sections('wpklikandpayStoreConfig'); 
 
		/*	Save the configuration in case that the form has been send with "save" action	*/
		if(isset($_POST['saveOption']) && ($_POST['saveOption'] == 'save'))
		{
			/*	Save the store main configuration	*/
			unset($optionList);$optionList = array();
			$optionList['storeTpe'] = $_POST['storeTpe'];
			$optionList['storeRang'] = $_POST['storeRang'];
			$optionList['storeIdentifier'] = $_POST['storeIdentifier'];
			$optionList['environnement'] = $_POST['environnement'];
			wpklikandpay_option::saveStoreConfiguration('wpklikandpay_store_mainoption', $optionList);
		}
	?>
	<table summary="Store main configuration form" cellpadding="0" cellspacing="0" class="storeMainConfiguration" >
		<?php do_settings_fields('wpklikandpayStoreConfig', 'mainWKlikAndPayStoreConfig'); ?>
	</table>
	<br/><br/><br/>
<!--
	<?php 
		// do_settings_sections('wpklikandpayUrlConfig');

		/*	Save the configuration in case that the form has been send with "save" action	*/
		if(isset($_POST['saveOption']) && ($_POST['saveOption'] == 'save'))
		{
			/*	Save the configuration for bakc url after payment	*/
			unset($optionList);$optionList = array();
			$optionList['urlSuccess'] = $_POST['urlSuccess'];
			$optionList['urlDeclined'] = $_POST['urlDeclined'];
			$optionList['urlCanceled'] = $_POST['urlCanceled'];
			wpklikandpay_option::saveStoreConfiguration('wpklikandpay_store_urloption', $optionList);
		}
	?>
	<table summary="Back url main configuration form" cellpadding="0" cellspacing="0" class="storeMainConfiguration" >
		<tr>
			<td colspan="2" >
		<?php echo sprintf(__('Ajouter : %s dans les pages que vous allez cr&eacute;er.', 'wpklikandpay'), '<span class=" bold" >[wp-klikandpay_payment_return title="KlikAndPay return page" ]</span>'); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" >&nbsp;</td>
		</tr>
<?php 
		do_settings_fields('wpklikandpayUrlConfig', 'backUrlConfig'); 
?>
	</table>
-->
	<br/><br/><br/>
	<input type="submit" class="button-primary" value="<?php _e('Enregistrer les options', 'wpklikandpay'); ?>" />
</form>
<?php
	}

	/**
	*	Create the form for store configuration
	*/
	function storeConfigForm()
	{
		/*	Add the field for the store configuration	*/
		add_settings_field('wpklikandpay_store_tpe', __('Num&eacute;ro de TPE de la boutique', 'wpklikandpay'), array('wpklikandpay_option', 'storeTpe'), 'wpklikandpayStoreConfig', 'mainWKlikAndPayStoreConfig');
		// add_settings_field('wpklikandpay_store_rang', __('Num&eacute;ro de rang de la boutique', 'wpklikandpay'), array('wpklikandpay_option', 'storeRang'), 'wpklikandpayStoreConfig', 'mainWKlikAndPayStoreConfig');
		// add_settings_field('wpklikandpay_store_id', __('Identifiant de la boutique', 'wpklikandpay'), array('wpklikandpay_option', 'storeIdentifier'), 'wpklikandpayStoreConfig', 'mainWKlikAndPayStoreConfig');
		add_settings_field('wpklikandpay_environnement', __('Environnement de la boutique', 'wpklikandpay'), array('wpklikandpay_option', 'environnement'), 'wpklikandpayStoreConfig', 'mainWKlikAndPayStoreConfig');
	}
	/**
	*	Create an input for the store TPE number
	*/
	function storeTpe()
	{
		$input_def['id'] = 'storeTpe';
		$input_def['name'] = 'storeTpe';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']) == ''))
		{
			$inputValue = wpklikandpay_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wpklikandpay_form::check_input_type($input_def);
	}
	/**
	*	Create an input for the store "Rang" number
	*/
	function storeRang()
	{
		$input_def['id'] = 'storeRang';
		$input_def['name'] = 'storeRang';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']) == ''))
		{
			$inputValue = wpklikandpay_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wpklikandpay_form::check_input_type($input_def);
	}
	/**
	*	Create an input for the store indentifier
	*/
	function storeIdentifier()
	{
		$input_def['id'] = 'storeIdentifier';
		$input_def['name'] = 'storeIdentifier';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']) == ''))
		{
			$inputValue = wpklikandpay_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wpklikandpay_form::check_input_type($input_def);
	}	/**
	*	Create an input for the store indentifier
	*/
	function environnement()
	{
		$input_def['id'] = 'environnement';
		$input_def['name'] = 'environnement';
		$input_def['type'] = 'select';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']) == ''))
		{
			$inputValue = wpklikandpay_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;
		$environnement['test'] = __('Mode test', 'wpklikandpay');
		$environnement['production'] = __('Mode Production', 'wpklikandpay');
		$input_def['possible_value'] = $environnement;
		$input_def['valueToPut'] = 'index';

		echo wpklikandpay_form::check_input_type($input_def);
	}

	/**
	*	Create the form for back url configuration
	*/
	function urlConfigForm()
	{
		/*	Add the field for the back link configuration	*/
		add_settings_field('wpklikandpay_payment_success', __('Url de retour pour un paiement accept&eacute;', 'wpklikandpay'), array('wpklikandpay_option', 'urlSuccess'), 'wpklikandpayUrlConfig', 'backUrlConfig');
		add_settings_field('wpklikandpay_payment_canceled', __('Url de retour pour un paiement annul&eacute;', 'wpklikandpay'), array('wpklikandpay_option', 'urlCanceled'), 'wpklikandpayUrlConfig', 'backUrlConfig');
		add_settings_field('wpklikandpay_payment_declined', __('Url de retour pour un paiement refus&eacute;', 'wpklikandpay'), array('wpklikandpay_option', 'urlDeclined'), 'wpklikandpayUrlConfig', 'backUrlConfig');
	}
	/**
	*	Create an input for the store indentifier
	*/
	function urlSuccess()
	{
		$input_def['id'] = 'urlSuccess';
		$input_def['name'] = 'urlSuccess';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_urloption', $input_def['name']) == ''))
		{
			$inputValue = wpklikandpay_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_urloption', $input_def['name']) != '')
		{
			$inputValue = wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_urloption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wpklikandpay_form::check_input_type($input_def);
	}
	/**
	*	Create an input for the store indentifier
	*/
	function urlCanceled()
	{
		$input_def['id'] = 'urlCanceled';
		$input_def['name'] = 'urlCanceled';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_urloption', $input_def['name']) == ''))
		{
			$inputValue = wpklikandpay_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_urloption', $input_def['name']) != '')
		{
			$inputValue = wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_urloption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wpklikandpay_form::check_input_type($input_def);
	}	
	/**
	*	Create an input for the store indentifier
	*/
	function urlDeclined()
	{
		$input_def['id'] = 'urlDeclined';
		$input_def['name'] = 'urlDeclined';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_urloption', $input_def['name']) == ''))
		{
			$inputValue = wpklikandpay_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_urloption', $input_def['name']) != '')
		{
			$inputValue = wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_urloption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wpklikandpay_form::check_input_type($input_def);
	}

}