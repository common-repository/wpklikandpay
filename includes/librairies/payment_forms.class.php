<?php
/**
* Payment form utilities
*
* Define the method and element to manage the different payment form
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-klikandpay
* @subpackage librairies
*/

/**
* Define the method and element to manage the different payment form
* @package wp-klikandpay
* @subpackage librairies
*/
class wpklikandpay_payment_form
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'payment_form';
	}
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getPageIcon()
	{
		return '';
	}
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getListingSlug()
	{
		return WPKLIKANDPAY_URL_SLUG_FORMS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPKLIKANDPAY_URL_SLUG_FORMS_EDITION;
	}
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
	{
		return WPKLIKANDPAY_DBT_FORMS;
	}

	/**
	*	Define the title of the page
	*
	*	@return string $title The title of the page looking at the environnement
	*/
	function pageTitle()
	{
		$action = isset($_REQUEST['action']) ? wpklikandpay_tools::varSanitizer($_REQUEST['action']) : '';
		$objectInEdition = isset($_REQUEST['id']) ? wpklikandpay_tools::varSanitizer($_REQUEST['id']) : '';

		$title = __('Liste des formulaires', 'wpklikandpay' );
		if($action != '')
		{
			if($action == 'edit')
			{
				$editedItem = wpklikandpay_payment_form::getElement($objectInEdition);
				$title = sprintf(__('&Eacute;diter le formulaire "%s"', 'wpklikandpay'), $editedItem->payment_form_name);
			}
			elseif($action == 'add')
			{
				$title = __('Ajouter un formulaire', 'wpklikandpay');
			}
		}
		return $title;
	}

	/**
	*	Define the different message and action after an action is send through the element interface
	*
	*	@return string $actionResultMessage The message to output after an action is launched to advise the user what append
	*/
	function elementAction()
	{
		global $wpdb;
		global $id;
		$actionResultMessage = '';

		$pageMessage = $actionResult = '';
		$pageAction = isset($_REQUEST[wpklikandpay_payment_form::getDbTable() . '_action']) ? wpklikandpay_tools::varSanitizer($_REQUEST[wpklikandpay_payment_form::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wpklikandpay_payment_form::getDbTable()]['id']) ? wpklikandpay_tools::varSanitizer($_REQUEST[wpklikandpay_payment_form::getDbTable()]['id']) : '';

		/*	Add the list of mandatory field in serialsed array shape	*/
		$_POST['user_mandatory_fields']['user_email'] = 'user_email';
		$_POST['user_mandatory_fields']['user_firstname'] = 'user_firstname';
		$_POST['user_mandatory_fields']['user_lastname'] = 'user_lastname';
		$_POST['user_mandatory_fields']['user_phone'] = 'user_phone';
		$_POST['user_mandatory_fields']['user_adress'] = 'user_adress';
		$_POST['user_mandatory_fields']['user_postal_code'] = 'user_postal_code';
		$_POST['user_mandatory_fields']['user_postal_country'] = 'user_postal_country';
		$_POST['user_mandatory_fields']['user_postal_state'] = 'user_postal_state';
		$_POST['user_mandatory_fields']['user_postal_town'] = 'user_postal_town';
		$_REQUEST[wpklikandpay_payment_form::getDbTable()]['payment_form_mandatory_fields'] = serialize($_POST['user_mandatory_fields']);

		/*	Define the database operation type from action launched by the user	 */
		/*************************				GENERIC				**************************/
		/*************************************************************************/
		if(($pageAction != '') && (($pageAction == 'edit') || ($pageAction == 'editandcontinue') || ($pageAction == 'delete')))
		{
			if(current_user_can('wpklikandpay_edit_forms'))
			{
				$_REQUEST[wpklikandpay_payment_form::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				if($pageAction == 'delete')
				{
					if(current_user_can('wpklikandpay_delete_forms'))
					{
						$_REQUEST[wpklikandpay_payment_form::getDbTable()]['status'] = 'deleted';
					}
					else
					{
						$actionResult = 'userNotAllowedForActionDelete';
					}
				}
				$actionResult = wpklikandpay_database::update($_REQUEST[wpklikandpay_payment_form::getDbTable()], $id, wpklikandpay_payment_form::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionEdit';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'delete')))
		{
			if(current_user_can('wpklikandpay_delete_forms'))
			{
				$_REQUEST[wpklikandpay_payment_form::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wpklikandpay_payment_form::getDbTable()]['status'] = 'deleted';
				$actionResult = wpklikandpay_database::update($_REQUEST[wpklikandpay_payment_form::getDbTable()], $id, wpklikandpay_payment_form::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionDelete';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'save') || ($pageAction == 'saveandcontinue') || ($pageAction == 'add')))
		{
			if(current_user_can('wpklikandpay_add_forms'))
			{
				$_REQUEST[wpklikandpay_payment_form::getDbTable()]['creation_date'] = date('Y-m-d H:i:s');
				$actionResult = wpklikandpay_database::save($_REQUEST[wpklikandpay_payment_form::getDbTable()], wpklikandpay_payment_form::getDbTable());
				$id = $wpdb->insert_id;
			}
			else
			{
				$actionResult = 'userNotAllowedForActionAdd';
			}
		}

		/*	When an action is launched and there is a result message	*/
		/************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
		/************		CHANGE ERROR MESSAGE FOR SPECIFIC CASE					*************/
		/****************************************************************************/
		if($actionResult != '')
		{
			$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wpklikandpay_payment_form::getDbTable()]['payment_form_name'] . '</span>';
			if($actionResult == 'error')
			{/*	CHANGE HERE FOR SPECIFIC CASE	*/
				$actionResultMessage = '<img src="' . WPKLIKANDPAY_ERROR_ICON . '" alt="action error" class="wpklikandpayPageMessage_Icon" />' . sprintf(__('Une erreur est survenue lors de l\'enregistrement de %s', 'wpklikandpay'), $elementIdentifierForMessage);
				if(WPKLIKANDPAY_DEBUG)
				{
					$actionResultMessage .= '<br/>' . $wpdb->last_error;
				}
			}
			elseif(($actionResult == 'done') || ($actionResult == 'nothingToUpdate'))
			{
				/*****************************************************************************************************************/
				/*************************			CHANGE FOR SPECIFIC ACTION FOR CURRENT ELEMENT				****************************/
				/*****************************************************************************************************************/
				if(isset($_REQUEST['associatedOfferList']) && ($_REQUEST['associatedOfferList'] != ''))
				{
					/*	Get and read the new offer list to associate to the form	*/
					$offersToAssociate = explode(', ', $_REQUEST['associatedOfferList']);

					/*	Get the already associated to check if there are no element to unassociate before associate new one	*/
					$associatedOffers = wpklikandpay_offers::getOffersOfForm($id);
					$storedOffers = array();
					foreach($associatedOffers as $associatedOffer)
					{
						$storedOffers[] = $associatedOffer->offer_id;
						if((!isset($offersToAssociate) && !is_array($offersToAssociate)) || !in_array($associatedOffer->offer_id, $offersToAssociate))
						{
							$associateNewOffer['status'] = 'deleted';
							$associateNewOffer['last_update_date'] = date('Y-m-d H:i:s');
							$actionResult = wpklikandpay_database::update($associateNewOffer, $associatedOffer->LINK_ID, WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS);
						}
					}

					foreach($offersToAssociate as $offerId)
					{
						if(($offerId > 0) && (!in_array($offerId, $storedOffers)))
						{
							$associateNewOffer['id'] = '';
							$associateNewOffer['status'] = 'valid';
							$associateNewOffer['creation_date'] = date('Y-m-d H:i:s');
							$associateNewOffer['form_id'] = $id;
							$associateNewOffer['offer_id'] = $offerId;
							$actionResult = wpklikandpay_database::save($associateNewOffer, WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS);
						}

						/*	Define a specific title for the offer in this form	*/
						if($offerId > 0)
						{
							$offerLinkToChangeToTitle = wpklikandpay_offers::getElement($offerId, "'valid'");
							$associateOffer = array();
							if(isset($_REQUEST['associatedOfferTitle'][$offerLinkToChangeToTitle->id]) && ($_REQUEST['associatedOfferTitle'][$offerLinkToChangeToTitle->id] != ''))
							{
								$associateOffer['offer_title'] = $_REQUEST['associatedOfferTitle'][$offerLinkToChangeToTitle->id];
							}
							$associateOffer['last_update_date'] = date('Y-m-d H:i:s');
							$query = $wpdb->prepare("SELECT id FROM " . WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS . " WHERE offer_id = '" . $offerId . "' AND form_id = '" . $id . "' AND status = 'valid' ", "");
							$linkOfferForm = $wpdb->get_row($query);
							$actionResult = wpklikandpay_database::update($associateOffer, $linkOfferForm->id, WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS);
						}
					}
				}
				else
				{/*	In case that we delete all the offer of the form	*/
					/*	Get the already associated to check if there are no element to unassociate before associate new one	*/
					$associatedOffers = wpklikandpay_offers::getOffersOfForm($id);

					foreach($associatedOffers as $associatedOffer)
					{
						$associateNewOffer['status'] = 'deleted';
						$associateNewOffer['last_update_date'] = date('Y-m-d H:i:s');
						$actionResult = wpklikandpay_database::update($associateNewOffer, $associatedOffer->LINK_ID, WPKLIKANDPAY_DBT_LINK_FORMS_OFFERS);
					}
				}

				/*************************			GENERIC				****************************/
				/*************************************************************************/
				$actionResultMessage = '<img src="' . WPKLIKANDPAY_SUCCES_ICON . '" alt="action success" class="wpklikandpayPageMessage_Icon" />' . sprintf(__('L\'enregistrement de %s s\'est d&eacute;roul&eacute; avec succ&eacute;s', 'wpklikandpay'), $elementIdentifierForMessage);
			}
			elseif(($actionResult == 'userNotAllowedForActionEdit') || ($actionResult == 'userNotAllowedForActionAdd') || ($actionResult == 'userNotAllowedForActionDelete'))
			{
				$actionResultMessage = '<img src="' . WPKLIKANDPAY_ERROR_ICON . '" alt="action error" class="wpklikandpayPageMessage_Icon" />' . __('Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action.', 'wpklikandpay');
			}
		}

		return $actionResultMessage;
	}
	/**
	*	Return the list page content, containing the table that present the item list
	*
	*	@return string $listItemOutput The html code that output the item list
	*/
	function elementList()
	{
		global $currencyIconList;
		$listItemOutput = '';

		/*	Start the table definition	*/
		$tableId = wpklikandpay_payment_form::getDbTable() . '_list';
		$tableSummary = __('Existing payment forms listing', 'wpklikandpay');
		$tableTitles = array();
		$tableTitles[] = __('Nom du formulaire', 'wpklikandpay');
		$tableClasses = array();
		$tableClasses[] = 'wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_name_column';

		$line = 0;
		$elementList = wpklikandpay_payment_form::getElement();
		if(count($elementList) > 0)
		{
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wpklikandpay_payment_form::getDbTable() . '_' . $element->id;

				$elementLabel = $element->payment_form_name;
				$subRowActions = '';
				if(current_user_can('wpklikandpay_edit_forms'))
				{
					$editAction = admin_url('admin.php?page=' . wpklikandpay_payment_form::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Modifier', 'wpklikandpay') . '</a>';
					$elementLabel = '<a href="' . $editAction . '" >' . $element->payment_form_name  . '</a>';
				}
				elseif(current_user_can('wpklikandpay_view_forms_details'))
				{
					$editAction = admin_url('admin.php?page=' . wpklikandpay_payment_form::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wpklikandpay') . '</a>';
					$elementLabel = '<a href="' . $editAction . '" >' . $element->payment_form_name  . '</a>';
				}
				if(current_user_can('wpklikandpay_delete_forms'))
				{
					if($subRowActions != '')
					{
						$subRowActions .= '&nbsp;|&nbsp;';
					}
					$subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wpklikandpay_payment_form::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id). '" >' . __('Supprimer', 'wpklikandpay') . '</a>';
				}
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wpklikandpayRowAction" >' . $subRowActions . '
	</div>';

				$elementAmount = $element->initial_amount / 100;
				unset($tableRowValue);
				$tableRowValue[] = array('class' => wpklikandpay_payment_form::getCurrentPageCode() . '_label_cell', 'value' => $elementLabel . $rowActions);
				$tableRows[] = $tableRowValue;

				$line++;
			}
		}
		else
		{
			$subRowActions = '';
			if(current_user_can('wpklikandpay_add_forms'))
			{
				$subRowActions .= '
	<a href="' . admin_url('admin.php?page=' . wpklikandpay_payment_form::getEditionSlug() . '&amp;action=add') . '" >' . __('Ajouter', 'wpklikandpay') . '</a>';
			}
			$rowActions = '
	<div id="rowAction' . $element->id . '" class="wpklikandpayRowAction" >' . $subRowActions . '
	</div>';
			$tableRowsId[] = wpklikandpay_payment_form::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wpklikandpay_payment_form::getCurrentPageCode() . '_name_cell', 'value' => __('Aucun formulaire n\'a encore &eacute;t&eacute; cr&eacute;&eacute;', 'wpklikandpay') . $rowActions);
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wpklikandpay_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $listItemOutput . '
<br/><br/>' .sprintf(__('Ajouter : %s dans les pages que vous allez cr&eacute;er pour les retour du client lorsqu\'il a termin&eacute; le paiement sur le site de klikandpay.', 'wpklikandpay'), '<span class=" bold" >[wp-klikandpay_payment_return title="KlikAndPay return page" ]</span>');
	}
	/**
	*	Return the page content to add a new item
	*
	*	@return string The html code that output the interface for adding a nem item
	*/
	function elementEdition($itemToEdit = '')
	{
		$dbFieldList = wpklikandpay_database::fields_to_input(wpklikandpay_payment_form::getDbTable());

		$editedItem = '';
		$mandatoryFieldList = array();
		if($itemToEdit != '')
		{
			$editedItem = wpklikandpay_payment_form::getElement($itemToEdit);
			$mandatoryFieldList = unserialize($editedItem->payment_form_mandatory_fields);
		}

		$the_form_content_hidden = $the_form_general_content = '';
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];

			$pageAction = isset($_REQUEST[wpklikandpay_payment_form::getDbTable() . '_action']) ? wpklikandpay_tools::varSanitizer($_REQUEST[wpklikandpay_payment_form::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wpklikandpay_payment_form::getDbTable()][$input_name]) ? wpklikandpay_tools::varSanitizer($_REQUEST[wpklikandpay_payment_form::getDbTable()][$input_name]) : '';
			$currentFieldValue = $input_value;
			if(is_object($editedItem))
			{
				$currentFieldValue = $editedItem->$input_name;
			}
			elseif(($pageAction != '') && ($requestFormValue != ''))
			{
				$currentFieldValue = $requestFormValue;
			}

			if(($input_name == 'creation_date') || ($input_name == 'last_update_date'))
			{
				$input_def['type'] = 'hidden';
			}

			$input_def['value'] = $currentFieldValue;
			$the_input = wpklikandpay_form::check_input_type($input_def, wpklikandpay_payment_form::getDbTable());

			$helpForField = '';
			if($input_name == 'initial_amount')
			{
				$helpForField = '<div class="wpklikandpayFormFieldHelp" >' . __('Le montant est exprim&eacute; en centimes.<br/>exemple: pour 1&euro; mettre 100', 'wpklikandpay') . '</div>';
			}

			if(($input_name != 'payment_form_mandatory_fields'))
			{
				if(($input_def['type'] != 'hidden'))
				{
					$label = 'for="' . $input_name . '"';
					if(($input_def['type'] == 'radio') || ($input_def['type'] == 'checkbox'))
					{
						$label = '';
					}
					$input = '
			<div class="clear" >
				<div class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					<label ' . $label . ' >' . __($input_name, 'wpklikandpay') . '</label>
					' . $helpForField . '
				</div>
				<div class="wpklikandpay_form_input wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
					' . $the_input . '
				</div>
			</div>';
					if(($editedItem->is_default != 'yes') || (($editedItem->is_default == 'yes') && ($input_name != 'status')))
					{
						$the_form_general_content .= $input;
					}
				}
				else
				{
					$the_form_content_hidden .= '
			' . $the_input;
				}
			}
			else
			{
				/*	Get the fields from the order table concerning the user	*/
				$dbFieldList = wpklikandpay_database::fields_to_input(WPKLIKANDPAY_DBT_ORDERS);

				$userFieldList = '';
				foreach($dbFieldList as $input_key => $input_def)
				{
					$input_def['option'] = '';
					$input_def['type'] = 'checkbox';
					if(substr($input_def['name'], 0, 5) == 'user_')
					{
						if(in_array($input_def['name'], $mandatoryFieldList))
						{
							$input_def['value'] = $input_def['name'];
						}
						if(($input_def['name'] == 'user_email') || ($input_def['name'] == 'user_lastname') || ($input_def['name'] == 'user_firstname') || ($input_def['name'] == 'user_phone') || ($input_def['name'] == 'user_adress') || ($input_def['name'] == 'user_postal_code') || ($input_def['name'] == 'user_postal_town') || ($input_def['name'] == 'user_postal_state') || ($input_def['name'] == 'user_postal_country'))
						{
							$input_def['value'] = $input_def['name'];
							$input_def['option'] .= ' disabled="disabled" ';
						}
						$input_def['possible_value'] = $input_def['name'];
						$inputOutputName = $input_def['name'] . '_admin_side';
						$the_input = wpklikandpay_form::check_input_type($input_def, 'user_mandatory_fields');
					$userFieldList .=
	'	<div class="clear" >
			' . $the_input . '
			<label class=" wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_label" for="' . $input_def['name'] . '" >' . __($inputOutputName, 'wpklikandpay') . '</label>
		</div>
	';
					}
				}

				$helpForField = '<div class="wpklikandpayFormFieldHelp" >' . __('Cochez les champs que vous souhaitez d&eacute;finir comme obligatoire pour ce formulaire', 'wpklikandpay') . '</div>';
				$the_form_general_content .= '
		<div class="clear" >
			<div class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				<label >' . __($input_name, 'wpklikandpay') . '</label>
				' . $helpForField . '
			</div>
			<div class="wpklikandpay_form_input wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $userFieldList . '
			</div>
		</div>';
			}
		}

		/*	Add the offer list for the form	*/
		{
			/*	get the offer list	*/
			$the_form_general_content .= wpklikandpay_offers::getOfferListOutput($itemToEdit);
		}

		/*	Define the different action available for the edition form	*/
		$formAddAction = admin_url('admin.php?page=' . wpklikandpay_payment_form::getEditionSlug() . '&amp;action=edit');
		$formEditAction = admin_url('admin.php?page=' . wpklikandpay_payment_form::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit);
		$formAction = $formAddAction;
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit'))
		{
			$formAction = $formEditAction;
		}

		$the_form = '
<form name="' . wpklikandpay_payment_form::getDbTable() . '_form" id="' . wpklikandpay_payment_form::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data" >
' . wpklikandpay_form::form_input(wpklikandpay_payment_form::getDbTable() . '_action', wpklikandpay_payment_form::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wpklikandpay_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
' . wpklikandpay_form::form_input(wpklikandpay_payment_form::getDbTable() . '_form_has_modification', wpklikandpay_payment_form::getDbTable() . '_form_has_modification', 'no' , 'hidden') . '
<div id="wpklikandpayFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wpklikandpay(document).ready(function(){
		wpklikandpayMainInterface("' . wpklikandpay_payment_form::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wpklikandpay_payment_form::getEditionSlug()) . '");

		wpklikandpayFormsInterface("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer l\'association entre cette offre et ce formulaire?', 'wpklikandpay') . '");

		wpklikandpay("#delete").click(function(){
			wpklikandpay("#' . wpklikandpay_payment_form::getDbTable() . '_action").val("delete");
			deletePaymentForm();
		});
		if(wpklikandpay("#' . wpklikandpay_payment_form::getDbTable() . '_action").val() == "delete"){
			deletePaymentForm();
		}
		function deletePaymentForm(){
			if(confirm(wpklikandpayConvertAccentTojs("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?', 'wpklikandpay') . '"))){
				wpklikandpay("#' . wpklikandpay_payment_form::getDbTable() . '_form").submit();
			}
			else{
				wpklikandpay("#' . wpklikandpay_payment_form::getDbTable() . '_action").val("edit");
			}
		}
	});
</script>';

		if($itemToEdit != '')
		{
			ob_start();
			wpklikandpay_payment_form::getInitPaymentForm($itemToEdit);
			$userFormCode = ob_get_contents();
			ob_end_clean();
			$the_form .= '<div class="clear paymentFormContainer" ><br/><br/><br/><hr/>' . __('Pour utiliser ce formulaire, ins&eacute;rer le code ci-dessous &agrave; l\'endroit que vous souhaitez', 'wpklikandpay') . '<div class="clear payment_form_code" >' . wpklikandpay_payment_form::getPaymentFormShortCode($itemToEdit) . '</div></div>';
		}

		return $the_form;
	}
	/**
	*	Return the different button to save the item currently being added or edited
	*
	*	@return string $currentPageButton The html output code with the different button to add to the interface
	*/
	function getPageFormButton()
	{
		$action = isset($_REQUEST['action']) ? wpklikandpay_tools::varSanitizer($_REQUEST['action']) : 'add';
		$currentPageButton = '';

		if($action == 'add')
		{
			if(current_user_can('wpklikandpay_add_forms'))
			{
				$currentPageButton .= '<input type="button" class="button-primary" id="add" name="add" value="' . __('Ajouter', 'wpklikandpay') . '" />';
			}
		}
		elseif(current_user_can('wpklikandpay_edit_forms'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="' . __('Enregistrer', 'wpklikandpay') . '" /><input type="button" class="button-primary" id="saveandcontinue" name="saveandcontinue" value="' . __('Enregistrer et continuer l\'&eacute;dition', 'wpklikandpay') . '" />';
		}
		if(current_user_can('wpklikandpay_delete_forms') && ($action != 'add'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="' . __('Supprimer', 'wpklikandpay') . '" />';
		}

		$currentPageButton .= '<h2 class="alignright wpklikandpayCancelButton" ><a href="' . admin_url('admin.php?page=' . wpklikandpay_payment_form::getListingSlug()) . '" class="button add-new-h2" >' . __('Retour', 'wpklikandpay') . '</a></h2>';

		return $currentPageButton;
	}
	/**
	*	Get the existing element list into database
	*
	*	@param integer $elementId optionnal The element identifier we want to get. If not specify the entire list will be returned
	*	@param string $elementStatus optionnal The status of element to get into database. Default is set to valid element
	*
	*	@return object $elements A wordpress database object containing the element list
	*/
	function getElement($elementId = '', $elementStatus = "'valid', 'moderated'")
	{
		global $wpdb;
		$elements = array();
		$moreQuery = "";

		if($elementId != '')
		{
			$moreQuery = "
			AND PFORM.id = '" . $elementId . "' ";
		}

		$query = $wpdb->prepare(
		"SELECT PFORM.*
		FROM " . wpklikandpay_payment_form::getDbTable() . " AS PFORM
		WHERE PFORM.status IN (".$elementStatus.") " . $moreQuery
		, "");

		/*	Get the query result regarding on the function parameters. If there must be only one result or a collection	*/
		if($elementId == '')
		{
			$elements = $wpdb->get_results($query);
		}
		else
		{
			$elements = $wpdb->get_row($query);
		}

		return $elements;
	}

	/**
	*	Return the short code to put into the page for displaying a form
	*
	*	@param integer $formIdentifier The identifier of the form we want to output the shortcode for
	*
	*	@return string The shortcode to put directly into a page to output a form
	*/
	function getPaymentFormShortCode($formIdentifier)
	{
		return '[wpklikandpay_payment_form id="' . $formIdentifier . '" ]';
	}

	/**
	*	Function to decode the shortcode to output a payment fom into a page
	*
	*	@param mixed $atts optionnal The attributes list of the shortcode
	*
	*	@return string $formContent THe html code of the form to display according to the shortcode parameters
	*/
	function displayForm($atts = '')
	{
		$formContent = '';

		$formIdentifier = isset($_POST['formIdentifier']) ? wpklikandpay_tools::varSanitizer($_POST['formIdentifier']) : '';
		if($formIdentifier != '')
		{
			$mandatoryUserField = array();

			/*	Get the informations about the current form	*/
			$currentForm = wpklikandpay_payment_form::getElement($formIdentifier, "'valid'");

			/*	Set the mandatory fiel list	*/
			$mandatoryUserField = unserialize($currentForm->payment_form_mandatory_fields);

			$orderIdentifier = wpklikandpay_orders::saveNewOrder($_POST);

			$formIsComplete = true;
			foreach($mandatoryUserField as $field)
			{
				$testField = isset($_POST['order_user'][$field]) ? wpklikandpay_tools::varSanitizer($_POST['order_user'][$field]) : '';
				if($testField == '')
				{
					$formIsComplete = false;
					break;
				}
			}

			$formHasError = false;
			$error = '';
			/*	Check if the given email is a good email	*/
			if(!is_email($_POST['order_user']['user_email']))
			{
				$formHasError = true;
				$error .= __('L\'adresse email fournie n\'est pas une adresse email valable', 'wpklikandpay') . '<br/>';
			}
			/*	Check if the cgv box is checked or not */
			if($_POST['cgvAccept'] != 'yes')
			{
				$formHasError = true;
				$error .= __('Vous devez accepter les conditions g&eacute;n&eacute;rales de vente', 'wpklikandpay') . '<br/>';
			}

			if($formIsComplete && !$formHasError)
			{
				/*	Get the form to ouput	*/
				ob_start();
				wpklikandpay_payment_form::getPaymentFormTemplate($formIdentifier);
				$formContent = ob_get_contents();
				ob_end_clean();

				/*	Replace the full dynamic vars into the form	*/
				$formContent = str_replace('#PBXPORTEUR#', $_POST['order_user']['user_email'], $formContent);
				$formContent = str_replace('#PBXCMDIDENTIANT#', $orderIdentifier, $formContent);
				$formContent .= '<script type="text/javascript" >jQuery("#klikAndPayPayment").submit();</script>';
			}
			elseif(!$formIsComplete)
			{
				$formContent .= '<div class="mandatoryFieldAlert" >' . __('Tous les champs marqu&eacute;s d\'une &eacute;toile sont obligatoires', 'wpklikandpay') . '</div>';
			}
			elseif($formHasError)
			{
				$formContent .= '<div class="errorFieldAlert" >' . $error . '</div>';
			}
		}

		/*	Get the shortcode parameter to know which form to output	*/
		extract(shortcode_atts(array('id' => ''), $atts));

		/*	Get the current form informations	*/
		$currentForm = wpklikandpay_payment_form::getElement($id);
		if($currentForm->status == 'valid')
		{
			ob_start();
			wpklikandpay_payment_form::getInitPaymentForm($id);
			$formContent .= ob_get_contents();
			ob_end_clean();
		}
		else
		{/*	If the current form is no longer valid we output a message	*/
			$formContent .= sprintf(__('Une erreur est survenue. Merci de nous contacter en pr&eacute;cisant le code d\'erreur suivant: Form%dInvalid', 'wpklikandpay'), $id);
		}

		return $formContent;
	}

	/**
	*	Return the form to display before the user is sending on the payment page. In order to collect informations about the user
	*
	*	@return mixed The html code of the form that contains the different fields for the user enter its informations
	*/
	function getInitPaymentForm($formIdentifier)
	{
		global $currencyIconList;
		$mandatoryUserField = array();

		/*	Get the informations about the current form	*/
		$currentForm = wpklikandpay_payment_form::getElement($formIdentifier, "'valid'");

		/*	Set the mandatory fiel list	*/
		$mandatoryUserField = unserialize($currentForm->payment_form_mandatory_fields);

		/*	Get the fields from the order table concerning the user	*/
		$dbFieldList = wpklikandpay_database::fields_to_input(WPKLIKANDPAY_DBT_ORDERS);
?>
<form action="" method="post" >
	<input type="hidden" name="formIdentifier" id="formIdentifier" value="<?php echo $formIdentifier; ?>" />
<?php
			/*	Put the different input form the order	*/
			foreach($dbFieldList as $input_key => $input_def)
			{
				if(substr($input_def['name'], 0, 5) == 'user_')
				{
					$mandatoryField = '';
					if(in_array($input_def['name'], $mandatoryUserField))
					{
						$mandatoryField = '<span class="isMandatoryField" >*</span>';
					}
					if(isset($_POST['order_user'][$input_def['name']]))
					{
						$input_def['value'] = $_POST['order_user'][$input_def['name']];
					}
					$input_def['option'] = ' class="wpklikandpay_form_input" ';
					$the_input = wpklikandpay_form::check_input_type($input_def, 'order_user');
					if($input_def['name'] == 'user_postal_country')
					{
						$locale = substr(get_locale(), 0, 2);
						$locale = 'de';
						if($locale != 'fr')
						{
							$locale = 'en';
						}
						ob_start();
						include(WPKLIKANDPAY_INC_PLUGIN_DIR . 'templates/countryList_' . $locale . '.html');
						$the_input = ob_get_contents();
						ob_end_clean();
					}
				echo
'	<div class="clear" >
		<label class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_label" for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wpklikandpay') . $mandatoryField . '</label>
		' . $the_input . '
	</div>
';
				}
			}

			/*	Add the offer list for the current form	*/
			$associatedOffers = wpklikandpay_offers::getOffersOfForm($formIdentifier);
			if(count($associatedOffers) > 0)
			{
				$storedOffers = array();
				foreach($associatedOffers as $associatedOffer)
				{
					if($associatedOffer->offer_title != '')
					{
						$storedOffers[$associatedOffer->offer_id] = $associatedOffer->offer_title;
					}
					else
					{
						$storedOffers[$associatedOffer->offer_id] = wpklikandpay_offers::generateOfferTitle($associatedOffer);
					}
				}
				$input_def['name'] = 'selectedOffer';
				$input_def['type'] = 'select';
				$input_def['valueToPut'] = 'index';
				$input_def['value'] = $_POST['selectedOffer'];
				$input_def['possible_value'] = $storedOffers;
				$input_def['option'] = ' class="wpklikandpay_form_input" ';
				$the_input = wpklikandpay_form::check_input_type($input_def);
				echo
'	<div class="clear" >
		<label class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_label" for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wpklikandpay') . $mandatoryField . '</label>
		' . $the_input . '
	</div>
';
?>
	<label class="wpklikandpay_form_label wpklikandpay_cgvAccept_label" for="cgvAccept" >&nbsp;</label><input type="checkbox" name="cgvAccept" id="cgvAccept" value="yes" />&nbsp;
	<?php
		$cgvUrlStart = $cgvUrlEnd = '';
		if($currentForm->payment_form_cgv_url != '')
		{
			$cgvUrlStart = '<a href="' . $currentForm->payment_form_cgv_url . '" target="cgv" >';
			$cgvUrlEnd = '</a>';
		}
		_e(sprintf(__('J\'accepte les %sconditions g&eacute;n&eacute;rale de vente%s', 'wpklikandpay'), $cgvUrlStart, $cgvUrlEnd));
	?>
	<br/>
	<input type="submit" name="bouton_paiement" id="bouton_paiement" value="<?php _e($currentForm->payment_form_button_content); ?>" class="wpKlikAndPayButtonFormPrePayment" />
	<script type="text/javascript" >jQuery("#bouton_paiement").click(function(){if(!jQuery("#cgvAccept").is(":checked")){alert(wpklikandpayConvertAccentTojs("<?php _e('Vous devez accepter les conditions g&eacute;n&eacute;rales de vente', 'wpklikandpay') ?>"));return false;}});</script>
<?php
			}
			else
			{
				echo
'	<div class="clear" >
		<label class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_payment_form::getCurrentPageCode() . '_selectedOffer_label" >' . __('selectedOffer', 'wpklikandpay') . $mandatoryField . '</label>
		' . sprintf(__('Aucune offre n\'est associ&eacute;e &agrave; ce formulaire. Nous ne pouvons donner suite &agrave; votre demande. Pour plus d\'informations, contactez-nous en indiquant le code d\'erreur suivant: ErrorNOF#%d', 'wpklikandpay'), $formIdentifier) . '
	</div>
';
			}
?>
</form>
<?php
	}

	/**
	*	Return the payment form
	*
	*	@param integer $formIdentifier The form identifier to get the different information about the payment like amount, currency, and so on
	*
	*	@return mixed The html code representing the payment form
	*/
	function getPaymentFormTemplate($formIdentifier)
	{
		/*	Define the test environnement vars*/
		global $testEnvironnement;
		global $productionEnvironnement;

		/*	Get the last order identifier	*/
		$offer = wpklikandpay_offers::getElement($_POST['selectedOffer']);

		/*	Get tje current form informations	*/
		$formInformations = wpklikandpay_payment_form::getElement($formIdentifier);

		if(wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', 'environnement') == 'test')
		{
			$paymentUrl = $testEnvironnement[$offer->payment_type]['url'];
		}
		else
		{
			$paymentUrl = $productionEnvironnement[$offer->payment_type]['url'];
		}
?>
<form action="<?php echo $paymentUrl; ?>" method="post" id="klikAndPayPayment" >
	<input type="hidden" name="ID" value="<?php echo wpklikandpay_option::getStoreConfigOption('wpklikandpay_store_mainoption', 'storeTpe'); ?>" />

	<!-- Mandatory user field	-->
	<input type="hidden" name="NOM" value="<?php echo $_POST['order_user']['user_lastname']; ?>" />
	<input type="hidden" name="PRENOM" value="<?php echo $_POST['order_user']['user_firstname']; ?>" />
	<input type="hidden" name="ADRESSE" value="<?php echo $_POST['order_user']['user_adress']; ?>" />
	<input type="hidden" name="CODEPOSTAL" value="<?php echo $_POST['order_user']['user_postal_code']; ?>" />
	<input type="hidden" name="VILLE" value="<?php echo $_POST['order_user']['user_postal_town']; ?>" />
	<input type="hidden" name="PAYS" value="<?php echo $_POST['order_user']['user_postal_country']; ?>" />
	<input type="hidden" name="TEL" value="<?php echo $_POST['order_user']['user_phone']; ?>" />
	<input type="hidden" name="EMAIL" value="<?php echo $_POST['order_user']['user_email']; ?>" />
	<input type="hidden" name="REGION" value="<?php echo $_POST['order_user']['user_postal_state']; ?>" />

	<input type="hidden" name="RETOUR" value="<?php echo $offer->payment_reference_prefix ?>#PBXCMDIDENTIANT#" />

	<input type="hidden" name="DETAIL" value="REF:<?php echo $offer->payment_reference_prefix; ?>%Q:1%PRIX:<?php echo ($offer->payment_amount / 100); ?>%PROD:<?php echo $offer->payment_name; ?>|" />

<?php
	if($offer->payment_type == 'subscription_payment')
	{
?>
	<input type="hidden" name="ABONNEMENT" value="<?php echo $offer->payment_subscription_reference; ?>" />
<?php
	}
	else
	{
?>
	<input type="hidden" name="MONTANT" value="<?php echo ($offer->payment_amount / 100); ?>" />
<?php
		if($offer->payment_type == 'multiple_payment')
		{
			if($offer->payment_recurrent_amount != $offer->payment_amount)
			{
?>
	<input type="hidden" name="MONTANT2" value="<?php echo ($offer->payment_recurrent_amount / 100); ?>" />
<?php
			}
			if($offer->payment_recurrent_number > 1)
			{
?>
	<input type="hidden" name="EXTRA" value="<?php echo $offer->payment_recurrent_number; ?>FOIS" />
<?php
			}
		}
	}
?>

	<input type="submit" name="bouton_paiement" value="paiement" class="wpKlikAndPayButtonFormPayment" />
</form>
<?php
	}

}