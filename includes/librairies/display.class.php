<?php
/**
 * Template manager
 * 
 * Define the different method to create a form dynamically from a database table field list
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 * @package wp-klikandpay
 * @subpackage librairies
 */

/**
 * Define the different method to manage the plugin template
 * @package wp-klikandpay
 * @subpackage librairies
 */
class wpklikandpay_display
{

	/**
	*	Returns the header display of a classical HTML page.
	*
	*	@see afficherFinPage
	*
	*	@param string $pageTitle Title of the page.
	*	@param string $pageIcon Path of the icon.
	*	@param string $iconTitle Title attribute of the icon.
	*	@param string $iconAlt Alt attribute of the icon.
	*	@param boolean $hasAddButton Define if there must be a "add" button for this page
	*	@param string $actionInformationMessage A message to display in case of action is send
	*
	*	@return string Html code composing the page header
	*/
	function displayPageHeader($pageTitle, $pageIcon, $iconTitle, $iconAlt, $hasAddButton = true, $addButtonLink = '', $formActionButton = '', $actionInformationMessage = '')
	{
?>
<div class="wrap wpklikandpayMainWrap" >
	<div id="wpklikandpayMessage" class="fade below-h2 wpklikandpayPageMessage <?php echo (($actionInformationMessage != '') ? 'wpklikandpayPageMessage_Updated' : ''); ?>" ><?php _e($actionInformationMessage); ?></div>
<?php
	if($pageIcon != '')
	{
?>
	<div class="icon32 wpklikandpayPageIcon" ><img alt="<?php _e($iconAlt); ?>" src="<?php _e($pageIcon); ?>" title="<?php _e($iconTitle); ?>" /></div>
<?php
	}
?>
	<div class="pageTitle" id="pageTitleContainer" >
		<h2 class="alignleft" ><?php _e($pageTitle);
		if($hasAddButton)
		{
?>
			<a href="<?php echo $addButtonLink ?>" class="button add-new-h2" ><?php _e('Ajouter', 'wpklikandpay') ?></a>
<?php
		}
?>
		</h2>
		<div id="wpklikandpayPageHeaderButtonContainer" class="wpklikandpayPageHeaderButton" ><?php _e($formActionButton); ?></div>
	</div>
	<div id="champsCaches" class="clear wpklikandpayHide" ></div>
	<div class="clear" id="wpklikandpayMainContent" >
<?php
	}

	/**
	*	Returns the end of a classical page
	*
	*	@see displayPageHeader
	*
	*	@return string Html code composing the page footer
	*/
	function displayPageFooter()
	{
?>
	</div>
	<div class="clear wpklikandpayHide" id="ajax-response"></div>
</div>
<?php
	}

	/**
	*	Return The complete output page code
	*
	*	@return string The complete html page output
	*/
	function displayPage()
	{
		global $id;

		$pageAddButton = false;
		$pageMessage = $addButtonLink = $pageFormButton = $pageIcon = $pageIconTitle = $pageIconAlt = $objectType = '';
		$outputType = 'listing';
		$objectToEdit = isset($_REQUEST['id']) ? wpklikandpay_tools::varSanitizer($_REQUEST['id']) : '';
		$pageSlug = isset($_REQUEST['page']) ? wpklikandpay_tools::varSanitizer($_REQUEST['page']) : '';
		$action = isset($_REQUEST['action']) ? wpklikandpay_tools::varSanitizer($_REQUEST['action']) : '';

		/*	Select the content to add to the page looking for the parameter	*/
		switch($pageSlug)
		{
			case WPKLIKANDPAY_URL_SLUG_ORDERS_LISTING:
			case WPKLIKANDPAY_URL_SLUG_ORDERS_EDITION:
				$objectType = new wpklikandpay_orders();
			break;
			case WPKLIKANDPAY_URL_SLUG_FORMS_LISTING:
			case WPKLIKANDPAY_URL_SLUG_FORMS_EDITION:
				$objectType = new wpklikandpay_payment_form();
				if(current_user_can('wpklikandpay_add_forms'))
				{
					$pageAddButton = true;
				}
			break;
			case WPKLIKANDPAY_URL_SLUG_OFFERS_LISTING:
			case WPKLIKANDPAY_URL_SLUG_OFFERS_EDITION:
				$objectType = new wpklikandpay_offers();
				if(current_user_can('wpklikandpay_add_offers'))
				{
					$pageAddButton = true;
				}
			break;

			default:
			{
				$pageTitle = sprintf(__('Cette page doit &ecirc;tre cr&eacute;&eacute; dans %s &agrave; la ligne %d', 'wpklikandpay'), __FILE__, (__LINE__ - 3));
			}
			break;
		}

		if($objectType != '')
		{
			if(($action != '') && (($action == 'edit') || ($action == 'add') || ($action == 'delete')))
			{
				$outputType = 'adding';
			}
			elseif($action == 'view')
			{
				$outputType = 'view';
			}
			$pageMessage = $objectType->elementAction();

			$pageIcon = wpklikandpay_display::getPageIconInformation('path', $objectType);
			$pageIconTitle = wpklikandpay_display::getPageIconInformation('title', $objectType);
			$pageIconAlt = wpklikandpay_display::getPageIconInformation('alt', $objectType);

			if($outputType == 'listing')
			{
				$pageContent = $objectType->elementList();
			}
			elseif($outputType == 'adding')
			{
				if(($objectToEdit == '') && ($id != ''))
				{
					$objectToEdit = $id;
				}

				$pageAddButton = false;

				$pageFormButton = $objectType->getPageFormButton();

				$pageContent = $objectType->elementEdition($objectToEdit);
			}			
			elseif($outputType == 'view')
			{
				$pageAddButton = false;

				$pageFormButton = $objectType->getPageFormButton();

				$pageContent = $objectType->elementEdition($objectToEdit);
			}

			$pageTitle = $objectType->pageTitle();
			$addButtonLink = admin_url('admin.php?page=' . $objectType->getEditionSlug() . '&amp;action=add');
		}

		/*	Page content header	*/
		wpklikandpay_display::displayPageHeader($pageTitle, $pageIcon, $pageIconTitle, $pageIconAlt, $pageAddButton, $addButtonLink, $pageFormButton, $pageMessage);

		/*	Page content	*/
		echo $pageContent;

		/*	Page content footer	*/
		wpklikandpay_display::displayPageFooter();
	}


	/**
	*	Return the page help content
	*
	*	@return void
	*/
	function addContextualHelp()
	{
		$pageSlug = isset($_REQUEST['page']) ? wpklikandpay_tools::varSanitizer($_REQUEST['page']) : '';

		/*	Select the content to add to the page looking for the parameter	*/
		switch($pageSlug)
		{
			default:
				$pageHelpContent = __('Aucune aide n\'est disponible pour cette page.', 'wpklikandpay');
			break;
		}

		add_contextual_help('boutique_page_' . $pageSlug , __($pageHelpContent, 'wpklikandpay') );
	}

	/*
	* Return a complete html table with header, body and content
	*
	*	@param string $tableId The unique identifier of the table in the document
	*	@param array $tableTitles An array with the different element to put into the table's header and footer
	*	@param array $tableRows An array with the different value to put into the table's body
	*	@param array $tableClasses An array with the different class to affect to table rows and cols
	*	@param array $tableRowsId An array with the different identifier for table lines
	*	@param string $tableSummary A summary for the table
	*	@param boolean $withFooter Allow to define if the table must be create with a footer or not
	*
	*	@return string $table The html code of the table to output
	*/
	function getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, $withFooter = true)
	{
		$tableTitleBar = $tableBody = '';

		/*	Create the header and footer row	*/
		for($i=0; $i<count($tableTitles); $i++)
		{
			$tableTitleBar .= '
				<th class="' . $tableClasses[$i] . '" scope="col" >' . $tableTitles[$i] . '</th>';
		}
		
		/*	Create each table row	*/
		for($lineNumber=0; $lineNumber<count($tableRows); $lineNumber++)
		{
			$tableRow = $tableRows[$lineNumber];
			$tableBody .= '
		<tr id="' . $tableRowsId[$lineNumber] . '" class="tableRow" >';
			for($i=0; $i<count($tableRow); $i++)
			{
				$tableBody .= '
			<td class="' . $tableClasses[$i] . ' ' . $tableRow[$i]['class'] . '" >' . $tableRow[$i]['value'] . '</td>';
			}
			$tableBody .= '
		</tr>';
		}

		/*	Create the table output	*/
		$table = '
<table id="' . $tableId . '" cellspacing="0" cellpadding="0" class="widefat post fixed" summary="' . $tableSummary . '" >';
		if($tableTitleBar != '')
		{
			$table .= '
	<thead>
			<tr class="tableTitleHeader" >' . $tableTitleBar . '
			</tr>
	</thead>';
			if($withFooter)
			{
				$table .= '
	<tfoot>
			<tr class="tableTitleFooter" >' . $tableTitleBar . '
			</tr>
	</tfoot>';
			}
		}
		$table .= '
	<tbody>' . $tableBody . '
	</tbody>
</table>';

		return $table;
	}

	/**
	*	Define the icon informations for the page
	*
	*	@param string $infoType The information type we want to get Could be path / alt / title
	*
	*	@return string $pageIconInformation The information to output in the page
	*/
	function getPageIconInformation($infoType, $object)
	{
		switch($infoType)
		{
			case 'path':
				$pageIconInformation = $object->getPageIcon();
			break;
			case 'alt':
			case 'title':
			default:
				$pageIconInformation = $object->pageTitle();
			break;
		}

		return $pageIconInformation;
	}

}