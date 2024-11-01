<?php
/**
* Define the different method to access or create orders
*
*	Define the different method to access or create orders
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-klikandpay
* @subpackage librairies
*/

/**
* Define the different method to access or create orders
* @package wp-klikandpay
* @subpackage librairies
*/
class wpklikandpay_orders
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wpklikandpay_orders';
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
		return WPKLIKANDPAY_URL_SLUG_ORDERS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPKLIKANDPAY_URL_SLUG_ORDERS_EDITION;
	}
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
	{
		return WPKLIKANDPAY_DBT_ORDERS;
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

		$title = __('Liste des commandes', 'wpklikandpay' );
		if($action != '')
		{
			if(($action == 'view') || ($action == 'delete'))
			{
				$editedItem = wpklikandpay_orders::getElement($objectInEdition);
				$title = sprintf(__('Voir la commande "%s"', 'wpklikandpay'), $editedItem->order_reference);
			}
		}
		return $title;
	}

	/**
	*	Define the different message and action after an action is send through the element interface
	*/
	function elementAction()
	{
		$pageAction = isset($_REQUEST[wpklikandpay_orders::getDbTable() . '_action']) ? wpklikandpay_tools::varSanitizer($_REQUEST[wpklikandpay_orders::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wpklikandpay_orders::getDbTable()]['id']) ? wpklikandpay_tools::varSanitizer($_REQUEST[wpklikandpay_orders::getDbTable()]['id']) : '';

		/*	Start definition of output message when action is doing on another page	*/
		/************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
		/****************************************************************************/
		$action = isset($_REQUEST['action']) ? wpklikandpay_tools::varSanitizer($_REQUEST['action']) : '';
		$saveditem = isset($_REQUEST['saveditem']) ? wpklikandpay_tools::varSanitizer($_REQUEST['saveditem']) : '';
		if(($action != '') && ($action == 'deleteok') && ($saveditem > 0))
		{
			$editedElement = wpklikandpay_orders::getElement($saveditem, "'deleted'");
			$pageMessage = '<img src="' . WPKLIKANDPAY_SUCCES_ICON . '" alt="action success" class="wpklikandpayPageMessage_Icon" />' . sprintf(__('La commande "%s" a &eacute;t&eacute; supprim&eacute;e avec succ&eacute;s', 'wpklikandpay'), '<span class="bold" >' . $editedElement->order_reference . '</span>');
		}

		if($pageAction == 'delete')
		{
			if(current_user_can('wpklikandpay_delete_orders'))
			{
				$_REQUEST[wpklikandpay_orders::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wpklikandpay_orders::getDbTable()]['status'] = 'deleted';
				$actionResult = wpklikandpay_database::update($_REQUEST[wpklikandpay_orders::getDbTable()], $id, wpklikandpay_orders::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionDelete';
			}
		}

		/*	When an action is launched and there is a result message	*/
		/************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
		/************		CHANGE ERROR MESSAGE FOR SPECIFIC CASE					*************/
		/****************************************************************************/
		if($actionResult != '')
		{
			$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wpklikandpay_orders::getDbTable()]['name'] . '</span>';
			if($actionResult == 'error')
			{/*	CHANGE HERE FOR SPECIFIC CASE	*/
				$pageMessage .= '<img src="' . wpklikandpay_ERROR_ICON . '" alt="action error" class="wpklikandpayPageMessage_Icon" />' . sprintf(__('Une erreur est survenue lors de la suppression de %s', 'wpklikandpay'), $elementIdentifierForMessage);
				if(WPKLIKANDPAY_DEBUG)
				{
					$pageMessage .= '<br/>' . $wpdb->last_error;
				}
			}
			elseif(($actionResult == 'done') || ($actionResult == 'nothingToUpdate'))
			{
				/*************************			GENERIC				****************************/
				/*************************************************************************/
				$pageMessage .= '<img src="' . wpklikandpay_SUCCES_ICON . '" alt="action success" class="wpklikandpayPageMessage_Icon" />' . sprintf(__('L\'enregistrement de %s s\'est d&eacute;roul&eacute; avec succ&eacute;s', 'wpklikandpay'), $elementIdentifierForMessage);
				if($pageAction == 'delete')
				{
					wp_redirect(admin_url('admin.php?page=' . wpklikandpay_orders::getListingSlug() . "&action=deleteok&saveditem=" . $id));
				}
			}
			elseif(($actionResult == 'userNotAllowedForActionEdit') || ($actionResult == 'userNotAllowedForActionAdd') || ($actionResult == 'userNotAllowedForActionDelete'))
			{
				$pageMessage .= '<img src="' . wpklikandpay_ERROR_ICON . '" alt="action error" class="wpklikandpayPageMessage_Icon" />' . __('Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action.', 'wpklikandpay');
			}
		}
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
		$tableId = wpklikandpay_orders::getDbTable() . '_list';
		$tableSummary = __('orders listing', 'wpklikandpay');
		$tableTitles = array();
		$tableTitles[] = __('R&eacute;f&eacute;rence', 'wpklikandpay');
		$tableTitles[] = __('Date', 'wpklikandpay');
		$tableTitles[] = __('Montant', 'wpklikandpay');
		$tableTitles[] = __('Statut', 'wpklikandpay');
		$tableTitles[] = __('Informations compl&eacute;mentaires', 'wpklikandpay');
		$tableClasses = array();
		$tableClasses[] = 'wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_reference_column';
		$tableClasses[] = 'wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_date_column';
		$tableClasses[] = 'wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_ammount_column';
		$tableClasses[] = 'wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_order_status_column';
		$tableClasses[] = 'wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_order_infos_column';

		$line = 0;
		$elementList = wpklikandpay_orders::getElement('', "'valid', 'moderated'", '', " ORDER BY O.creation_date DESC");
		if(count($elementList) > 0)
		{
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wpklikandpay_orders::getDbTable() . '_' . $element->id;

				$elementLabel = $element->order_reference;
				$subRowActions = '';
				if(current_user_can('wpklikandpay_view_orders_details'))
				{
					$editAction = admin_url('admin.php?page=' . wpklikandpay_orders::getEditionSlug() . '&amp;action=view&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wpklikandpay') . '</a>';
					$elementLabel = '<a href="' . $editAction . '" >' . $element->order_reference  . '</a>';
				}
				if(current_user_can('wpklikandpay_delete_orders'))
				{
					if($subRowActions != '')
					{
						$subRowActions .= '&nbsp;|&nbsp;';
					}
					$subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wpklikandpay_orders::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id). '" >' . __('Supprimer', 'wpklikandpay') . '</a>';
				}
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wpklikandpayRowAction" >' . $subRowActions . '
	</div>';

				$orderAmount = '';
				$orderAmount = ($element->order_amount / 100);

				unset($tableRowValue);
				$tableRowValue[] = array('class' => wpklikandpay_orders::getCurrentPageCode() . '_reference_cell', 'value' => $elementLabel . $rowActions);
				$tableRowValue[] = array('class' => wpklikandpay_orders::getCurrentPageCode() . '_date_cell', 'value' => mysql2date('d M Y H:i:s', $element->creation_date, true));
				$tableRowValue[] = array('class' => wpklikandpay_orders::getCurrentPageCode() . '_amount_cell', 'value' => $orderAmount . '&nbsp;' . $currencyIconList[$element->order_currency]);
				$tableRowValue[] = array('class' => wpklikandpay_orders::getCurrentPageCode() . '_order_status_cell', 'value' => __($element->order_status, 'wpklikandpay'));
				$complementaryInformations = '-';
				if($element->payment_type == 'subscription_payment')
				{
					$complementaryInformations = sprintf(__('Dur&eacute;e abonnement : %s mois', 'wpklikandpay'), $element->payment_subscription_length) . '<br/><span class="alignleft" >' . __('Date de fin', 'wpklikandpay') . '&nbsp;:&nbsp;' . mysql2date('d M Y H:i:s', $element->ABOEND, true) . '</span>';
					/*	Ajout d'une icone pour indiquer qu'il faut apporter une attention particuli&eacute;re &agrave; cet &eacute;l&eacute;ment	*/
					if(strtotime($element->ABOEND) <= mktime(date('H'),  date('i'),  date('s'), date('m'), date('d'), date('Y')))
					{
						$complementaryInformations .= '
			<span class="subscriptionPaymentOver ui-icon alignleft" >&nbsp;</span>';
					}
				}
				$tableRowValue[] = array('class' => wpklikandpay_orders::getCurrentPageCode() . '_order_status_cell', 'value' => $complementaryInformations);
				$tableRows[] = $tableRowValue;

				$line++;
			}
		}
		else
		{
			$tableRowsId[] = wpklikandpay_orders::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wpklikandpay_orders::getCurrentPageCode() . '_label_cell', 'value' => __('Aucune commande n\'a encore &eacute;t&eacute; pass&eacute;e', 'wpklikandpay'));
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wpklikandpay_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $listItemOutput;
	}
	/**
	*	Return the page content to add a new item
	*
	*	@return string The html code that output the interface for adding a nem item
	*/
	function elementEdition($itemToEdit = '')
	{
		global $currencyIconList;
		$dbFieldList = wpklikandpay_database::fields_to_input(wpklikandpay_orders::getDbTable());

		$editedItem = '';
		if($itemToEdit != '')
		{
			$editedItem = wpklikandpay_orders::getElement($itemToEdit);
		}

		$the_form_content_hidden = $the_form_general_content = '';
		$the_form_payment_content = $the_form_user_content = $the_form_order_content = '';
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];

			$pageAction = isset($_REQUEST[wpklikandpay_orders::getDbTable() . '_action']) ? wpklikandpay_tools::varSanitizer($_REQUEST[wpklikandpay_orders::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wpklikandpay_orders::getDbTable()][$input_name]) ? wpklikandpay_tools::varSanitizer($_REQUEST[wpklikandpay_orders::getDbTable()][$input_name]) : '';
			$currentFieldValue = $input_value;
			if(is_object($editedItem))
			{
				$currentFieldValue = $editedItem->$input_name;
			}
			elseif(($pageAction != '') && ($requestFormValue != ''))
			{
				$currentFieldValue = $requestFormValue;
			}

			/*	Translate the field value	*/
			$input_def['value'] = __($currentFieldValue, 'wpklikandpay');

			/*	Store the payment definition fields	*/
			if(substr($input_name, 0, 8) == 'payment_')
			{
				if($input_name == 'payment_currency')
				{
					$input_def['value'] = $currencyIconList[$currentFieldValue];
				}
				elseif($input_name == 'payment_amount')
				{
					$input_def['value'] = ($currentFieldValue / 100);
				}
				elseif($input_name == 'payment_recurrent_amount')
				{
					if($currentFieldValue > 0)
					{
						$input_def['value'] = ($currentFieldValue / 100);
					}
					else
					{
						$input_def['value'] = ($editedItem->payment_amount / 100);
					}
				}
				elseif($input_name == 'payment_recurrent_start_delay')
				{
					if($currentFieldValue == 0)
					{
						$input_def['value'] =  __('D&eacute;but imm&eacute;diat', 'wpklikandpay');
					}
					else
					{
						$input_def['value'] =  sprintf(__('%d jours apr&eacute;s l\'inscription', 'wpklikandpay'), $currentFieldValue);
					}
				}
				elseif($input_name == 'payment_recurrent_day_of_month')
				{
					if($currentFieldValue == 0)
					{
						$currentFieldValue = mysql2date('d', $editedItem->creation_date);
					}
					$input_def['value'] =  sprintf(__('Le %d de chaque mois', 'wpklikandpay'), $currentFieldValue);
				}

				if((substr($input_name, 0, 18) != 'payment_recurrent_') || ($editedItem->payment_type == 'multiple_payment'))
				{
					$the_form_payment_content .= '
		<div class="clear" >
			<div class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wpklikandpay') . '
			</div>
			<div class="wpklikandpay_form_input wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $input_def['value'] . '
			</div>
		</div>';
				}
			}

			/*	Store the user fields	*/
			elseif(substr($input_name, 0, 5) == 'user_')
			{
				$input_name = $input_name . '_admin_side';
				$the_form_user_content .= '
		<div class="clear" >
			<div class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wpklikandpay') . '
			</div>
			<div class="wpklikandpay_form_input wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $input_def['value'] . '
			</div>
		</div>';
			}

			/*	Store the payment return fields	*/
			elseif((substr($input_name, 0, 6) == 'order_') || ($input_name == 'offer_id'))
			{
				if($input_name == 'order_currency')
				{
					$input_def['value'] = $currencyIconList[$currentFieldValue];
				}
				elseif($input_name == 'order_amount')
				{
					$input_def['value'] = ($currentFieldValue / 100);
				}

				$the_form_order_content .= '
		<div class="clear" >
			<div class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wpklikandpay') . '
			</div>
			<div class="wpklikandpay_form_input wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $input_def['value'] . '
			</div>
		</div>';
			}

			/*	For all the other field	*/
			else
			{
				if($input_name == 'creation_date')
				{
					$input_name = 'order_creation_date';
					$input_def['value'] = mysql2date('d M Y H:i', $currentFieldValue, true);
				}

				if(($input_name == 'status') || ($input_name == 'last_update_date') || ($input_name == 'form_id'))
				{
					$input_def['type'] = 'hidden';
				}

				if($input_def['type'] != 'hidden')
				{
					$the_form_general_content .= '
			<div class="clear" >
				<div class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					' . __($input_name, 'wpklikandpay') . '
				</div>
				<div class="wpklikandpay_form_input wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
					' . $input_def['value'] . '
				</div>
			</div>';

					/*	Ajout de la date de fin pour les abonnements	*/
					if(($input_name == 'order_creation_date') && ($editedItem->payment_type == 'subscription_payment'))
					{
						$the_form_general_content .= '
			<div class="clear" >
				<div class="wpklikandpay_form_label wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					' . __('Date de fin', 'wpklikandpay') . '
				</div>
				<div class="wpklikandpay_form_input wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
					<span class="alignleft" >' . mysql2date('d M Y H:i', $editedItem->ABOEND, true) . '</span>';

						/*	Ajout d'une icone pour indiquer qu'il faut apporter une attention particuli&eacute;re &agrave; cet &eacute;l&eacute;ment	*/
						if(strtotime($editedItem->ABOEND) <= mktime(date('H'),  date('i'),  date('s'), date('m'), date('d'), date('Y')))
						{
							$the_form_general_content .= '
				<span class="subscriptionPaymentOver ui-icon alignleft" >&nbsp;</span>';
						}

						$the_form_general_content .= '
				</div>
			</div>';
					}
				}
				else
				{
					$the_form_content_hidden .= '
			' . wpklikandpay_form::check_input_type($input_def, wpklikandpay_orders::getDbTable());
				}
			}
		}

		/*	Build the general output with the different order's element	*/
		$the_form_general_content .= '
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Informations commandes', 'wpklikandpay') . '</legend>
			<div>' . $the_form_order_content . '</div>
		</fieldset>
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Informations client', 'wpklikandpay') . '</legend>
			<div>' . $the_form_user_content . '</div>
		</fieldset>
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Informations paiement', 'wpklikandpay') . '</legend>
			<div>' . $the_form_payment_content	. '</div>
		</fieldset>';

		$the_form = '
<form name="' . wpklikandpay_orders::getDbTable() . '_form" id="' . wpklikandpay_orders::getDbTable() . '_form" method="post" action="" >
' . wpklikandpay_form::form_input(wpklikandpay_orders::getDbTable() . '_action', wpklikandpay_orders::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wpklikandpay_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
<div id="wpklikandpayFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wpklikandpay_' . wpklikandpay_orders::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wpklikandpay(document).ready(function(){
		wpklikandpay("#delete").click(function(){
			wpklikandpay("#' . wpklikandpay_orders::getDbTable() . '_action").val("delete");
			deleteOrder();
		});
		if(wpklikandpay("#' . wpklikandpay_orders::getDbTable() . '_action").val() == "delete"){
			deleteOrder();
		}
		function deleteOrder(){
			if(confirm(wpklikandpayConvertAccentTojs("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer cette commande?', 'wpklikandpay') . '"))){
				wpklikandpay("#' . wpklikandpay_orders::getDbTable() . '_form").submit();
			}
			else{
				wpklikandpay("#' . wpklikandpay_orders::getDbTable() . '_action").val("edit");
			}
		}
	});
</script>';

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

		if(current_user_can('wpklikandpay_delete_orders') && ($action != 'add'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="' . __('Supprimer', 'wpklikandpay') . '" />';
		}

		$currentPageButton .= '<h2 class="alignright wpklikandpayCancelButton" ><a href="' . admin_url('admin.php?page=' . wpklikandpay_orders::getListingSlug()) . '" class="button add-new-h2" >' . __('Retour', 'wpklikandpay') . '</a></h2>';

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
	function getElement($elementId = '', $elementStatus = "'valid', 'moderated'", $whatToGet = 'id', $orderByStatement = '')
	{
		global $wpdb;
		$elements = array();
		$moreQuery = "";

		if($elementId != '')
		{
			$moreQuery = "
			AND O." . $whatToGet . " = '" . $elementId . "' ";
		}

		$query = $wpdb->prepare(
		"SELECT O.*, DATE_ADD(O.creation_date, INTERVAL O.payment_subscription_length MONTH) AS ABOEND
		FROM " . wpklikandpay_orders::getDbTable() . " AS O
		WHERE O.status IN (".$elementStatus.") " . $moreQuery . "
		" . $orderByStatement, ""
		);

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
	*	Save a new order into database from the given informations
	*
	*	@param array $orderInformations The informations sent by the user through the form and from the payment form definition
	*
	*	@return integer $orderReference The last order Identifier to create an unique
	*/
	function saveNewOrder($orderInformations)
	{
		global $wpdb;
		$orderReference = 0;

		/*	Get the last order identifier	*/
		$offer = wpklikandpay_offers::getElement($_POST['selectedOffer']);

		/*	Create the new order	*/
		$orderMoreInformations['id'] = '';
		$orderMoreInformations['form_id'] = $orderInformations['formIdentifier'];
		$orderMoreInformations['offer_id'] = $orderInformations['selectedOffer'];
		$orderMoreInformations['status'] = 'valid';
		$orderMoreInformations['order_status'] = 'initialised';
		$orderMoreInformations['creation_date'] = date('Y-m-d H:i:s');
		foreach($orderInformations['order_user'] as $orderUserField => $orderUserFieldValue)
		{
			$orderMoreInformations[$orderUserField] = $orderUserFieldValue;
		}

		/*	Save offer informations in case of modification in future	*/
		$orderMoreInformations['payment_type'] = $offer->payment_type;
		$orderMoreInformations['payment_recurrent_amount'] = $offer->payment_recurrent_amount;
		$orderMoreInformations['payment_recurrent_number'] = $offer->payment_recurrent_number;
		$orderMoreInformations['payment_recurrent_date'] = $offer->payment_recurrent_date;
		$orderMoreInformations['payment_reference_prefix'] = $offer->payment_reference_prefix;
		$orderMoreInformations['payment_subscription_reference'] = $offer->payment_subscription_reference;
		$orderMoreInformations['payment_subscription_length'] = $offer->payment_subscription_length;
		$orderMoreInformations['payment_name'] = $offer->payment_name;
		$orderMoreInformations['payment_amount'] = $offer->payment_amount;
		$orderMoreInformations['order_amount'] = $offer->payment_amount;
		$actionResult = wpklikandpay_database::save($orderMoreInformations, wpklikandpay_orders::getDbTable());
		if($actionResult == 'done')
		{
			$orderReference = $wpdb->insert_id;
			/*	Update the new order reference	*/
			$orderMoreInformations['last_update_date'] = date('Y-m-d H:i:s');
			$orderMoreInformations['order_reference'] = $offer->payment_reference_prefix . $orderReference;
			$actionResult = wpklikandpay_database::update($orderMoreInformations, $orderReference, wpklikandpay_orders::getDbTable());
		}

		return $orderReference;
	}

	/**
	*	Output the result of a transaction we return from klik and pay. Called by a shortcode on the return page (success/canceled/declined)
	*
	*	@return string $outputMessage A message to output to the end-user when transaction is finished
	*/
	function paymentReturn()
	{
		global $currencyIconList;

		$NUMXKP = isset($_REQUEST['NUMXKP']) ? wpklikandpay_tools::varSanitizer($_REQUEST['NUMXKP']) : '';
		$commande = isset($_REQUEST['commande']) ? wpklikandpay_tools::varSanitizer($_REQUEST['commande']) : '';
		$ABONNEMENTTYPE = isset($_REQUEST['ABONNEMENTTYPE']) ? wpklikandpay_tools::varSanitizer($_REQUEST['ABONNEMENTTYPE']) : '';
		$NUMERO = isset($_REQUEST['NUMERO']) ? wpklikandpay_tools::varSanitizer($_REQUEST['NUMERO']) : '';

		if($commande != '')
		{
			/*	Get the orders informations to update with the payment return infos	*/
			$currentOrder = wpklikandpay_orders::getElement($commande, "'valid'", 'order_reference');

			/*	Update the current order	*/
			$orderMoreInformations['last_update_date'] = date('Y-m-d H:i:s');
			$orderMoreInformations['order_transaction'] = $NUMXKP;
			$orderMoreInformations['order_transaction_nb'] = $NUMERO;

			$order_status = 'closed';
			/*	Get the orders informations to update with the payment return infos	*/
			$amout = ($currentOrder->order_amount / 100);
			$outputMessage = sprintf(__('Votre paiement de %s &euro; a bien &eacute;t&eacute; effectu&eacute;', 'wpklikandpay'), $amout);

			$orderMoreInformations['order_status'] = $order_status;
			$actionResult = wpklikandpay_database::update($orderMoreInformations, $currentOrder->id, wpklikandpay_orders::getDbTable());
		}
		else
		{
			$outputMessage = '';
		}

		return '<div class="paymentReturnResponse" >' . $outputMessage . '</div>';
	}

}