/*	Define the plugin jquery var in order to avoid conflict with other plugin and scripts	*/
var wpklikandpay = jQuery.noConflict();

wpklikandpay(document).ready(function(){

	/*	Hide the message container if not empty	*/
	if(wpklikandpay("#wpklikandpayMessage").html != ''){
		setTimeout(function(){
			wpklikandpay("#wpklikandpayMessage").removeClass("wpklikandpayPageMessage_Updated");
			wpklikandpay("#wpklikandpayMessage").html("");
		}, 5000);
	}

	/*	Start the script that allows to make the header part of a page following the scroll	*/
	if(wpklikandpay("#pageTitleContainer").offset()){
		var pageTitleContainerOffset = wpklikandpay("#pageTitleContainer").offset().top;
		wpklikandpay(window).scroll(function(){
			if((wpklikandpay(window).scrollTop() > pageTitleContainerOffset) && !(wpklikandpay("#pageTitleContainer").hasClass("wpklikandpayPageTitle_Fixed"))){
				wpklikandpay("#pageTitleContainer").removeClass("pageTitle");
				wpklikandpay("#pageTitleContainer").addClass("wpklikandpayPageTitle_Fixed");
				wpklikandpay("#wpklikandpayPageHeaderButtonContainer").removeClass("wpklikandpayPageHeaderButton");
				wpklikandpay("#wpklikandpayPageHeaderButtonContainer").addClass("wpklikandpayPageHeaderButton_Fixed");
				wpklikandpay("#wpklikandpayMainContent").addClass("wpklikandpayContent_Fixed");
			}
			else if((wpklikandpay(window).scrollTop() <= pageTitleContainerOffset)  && (wpklikandpay("#pageTitleContainer").hasClass("wpklikandpayPageTitle_Fixed"))){
				wpklikandpay("#pageTitleContainer").addClass("pageTitle");
				wpklikandpay("#pageTitleContainer").removeClass("wpklikandpayPageTitle_Fixed");
				wpklikandpay("#wpklikandpayPageHeaderButtonContainer").addClass("wpklikandpayPageHeaderButton");
				wpklikandpay("#wpklikandpayPageHeaderButtonContainer").removeClass("wpklikandpayPageHeaderButton_Fixed");
				wpklikandpay("#wpklikandpayMainContent").removeClass("wpklikandpayContent_Fixed");
			}
		});
	}

	/*	Start the script that allows to make the message container following the scroll	*/
	if(wpklikandpay("#wpklikandpayMessage").offset()){
		var pageTitleContainerOffset = wpklikandpay("#wpklikandpayMessage").offset().top;
		wpklikandpay(window).scroll(function(){
			if((wpklikandpay(window).scrollTop() > pageTitleContainerOffset) && !(wpklikandpay("#wpklikandpayMessage").hasClass("wpklikandpayPageMessage_Fixed"))){
				wpklikandpay("#wpklikandpayMessage").addClass("wpklikandpayPageMessage_Fixed");
			}
			else if((wpklikandpay(window).scrollTop() <= pageTitleContainerOffset)  && (wpklikandpay("#wpklikandpayMessage").hasClass("wpklikandpayPageMessage_Fixed"))){
				wpklikandpay("#wpklikandpayMessage").removeClass("wpklikandpayPageMessage_Fixed");
			}
		});
	}

});

/*	Function called by default into form interface	*/
function wpklikandpayFormsInterface(deletionConfirmMessage){
	wpklikandpayFormsPaymentTypeSelection();
	wpklikandpay("#payment_type").change(function(){
		wpklikandpayFormsPaymentTypeSelection();
	});
	wpklikandpay("#wpklikandpayAssociateOffer").click(function(){
		var addNewOffer = true;
		var alreadyAffected = wpklikandpay("#associatedOfferList").val().split(", ");
		for(var i=0; i<alreadyAffected.length; i++){
			if(alreadyAffected[i] == wpklikandpay("#existingOffers").val()){
				addNewOffer = false;
			}
		}
		if(addNewOffer){
			wpklikandpay("#associatedOfferList").val(wpklikandpay("#associatedOfferList").val() + wpklikandpay("#existingOffers").val() + ", ");
			wpklikandpay("#associatedOfferListOutput").html(wpklikandpay("#associatedOfferListOutput").html() + '<div id="selectedOffer' + wpklikandpay("#existingOffers").val() + '" ><div class="ui-icon deleteOfferAssociation alignleft" >&nsp;</div>&nbsp;' + wpklikandpayConvertAccentTojs(offerList[wpklikandpay("#existingOffers").val()]) + '</div>');
		}
	});
	wpklikandpay(".deleteOfferAssociation").click(function(){
		if(confirm(wpklikandpayConvertAccentTojs(deletionConfirmMessage))){
			var currentOfferToDelete = wpklikandpay(this).attr("id").replace("offer", "");
			wpklikandpay("#associatedOfferList").val(wpklikandpay("#associatedOfferList").val().replace(currentOfferToDelete + ", ", ""));
			wpklikandpay("#selectedOffer" + currentOfferToDelete).remove();
		}
	});
}

/*	When changing the payment form type, display or hide complementary element into the form	*/
function wpklikandpayFormsPaymentTypeSelection(){
	if(wpklikandpay("#payment_type").val() == "single_payment"){
		wpklikandpay("#wpklikandpayMultiplePaymentFieldContainer").hide();
		wpklikandpay("#wpklikandpaySubscriptionPaymentFieldContainer").hide();
	}
	else if(wpklikandpay("#payment_type").val() == "multiple_payment"){
		wpklikandpay("#wpklikandpayMultiplePaymentFieldContainer").show();
		wpklikandpay("#wpklikandpaySubscriptionPaymentFieldContainer").hide();
	}
	else if(wpklikandpay("#payment_type").val() == "subscription_payment"){
		wpklikandpay("#wpklikandpaySubscriptionPaymentFieldContainer").show();
		wpklikandpay("#wpklikandpayMultiplePaymentFieldContainer").hide();
	}
}

/*	Define the different behavior for the main interface	*/
function wpklikandpayMainInterface(currentType, confirmCancelMessage, listingSlugUrl){
	wpklikandpay("#" + currentType + "_form input, #" + currentType + "_form textarea").keypress(function(){
		wpklikandpay("#" + currentType + "_form_has_modification").val("yes");
	});
	wpklikandpay("#" + currentType + "_form select").change(function(){
		wpklikandpay("#" + currentType + "_form_has_modification").val("yes");
	});
	wpklikandpay("#save").click(function(){
		wpklikandpay("#" + currentType + "_form").attr("action", listingSlugUrl);
		wpklikandpay("#" + currentType + "_form").submit();
	});
	wpklikandpay("#add").click(function(){
		wpklikandpay("#" + currentType + "_form").submit();
	});
	wpklikandpay("#saveandcontinue").click(function(){
		wpklikandpay("#" + currentType + "_action").val(wpklikandpay("#" + currentType + "_action").val() + "andcontinue");
		wpklikandpay("#" + currentType + "_form").submit();
	});
	wpklikandpay(".wpklikandpayCancelButton").click(function(){
		if((wpklikandpay("#" + currentType + "_form_has_modification").val() == "yes")){
			if(!confirm(wpklikandpayConvertAccentTojs(confirmCancelMessage))){
				return false;
			}
		}
	});
}

/*	Allows to output special characters into javascript	*/
function wpklikandpayConvertAccentTojs(text){
	text = text.replace(/&Agrave;/g, "\300");
	text = text.replace(/&Aacute;/g, "\301");
	text = text.replace(/&Acirc;/g, "\302");
	text = text.replace(/&Atilde;/g, "\303");
	text = text.replace(/&Auml;/g, "\304");
	text = text.replace(/&Aring;/g, "\305");
	text = text.replace(/&AElig;/g, "\306");
	text = text.replace(/&Ccedil;/g, "\307");
	text = text.replace(/&Egrave;/g, "\310");
	text = text.replace(/&Eacute;/g, "\311");
	text = text.replace(/&Ecirc;/g, "\312");
	text = text.replace(/&Euml;/g, "\313");
	text = text.replace(/&Igrave;/g, "\314");
	text = text.replace(/&Iacute;/g, "\315");
	text = text.replace(/&Icirc;/g, "\316");
	text = text.replace(/&Iuml;/g, "\317");
	text = text.replace(/&Eth;/g, "\320");
	text = text.replace(/&Ntilde;/g, "\321");
	text = text.replace(/&Ograve;/g, "\322");
	text = text.replace(/&Oacute;/g, "\323");
	text = text.replace(/&Ocirc;/g, "\324");
	text = text.replace(/&Otilde;/g, "\325");
	text = text.replace(/&Ouml;/g, "\326");
	text = text.replace(/&Oslash;/g, "\330");
	text = text.replace(/&Ugrave;/g, "\331");
	text = text.replace(/&Uacute;/g, "\332");
	text = text.replace(/&Ucirc;/g, "\333");
	text = text.replace(/&Uuml;/g, "\334");
	text = text.replace(/&Yacute;/g, "\335");
	text = text.replace(/&THORN;/g, "\336");
	text = text.replace(/&Yuml;/g, "\570");
	text = text.replace(/&szlig;/g, "\337");
	text = text.replace(/&agrave;/g, "\340");
	text = text.replace(/&aacute;/g, "\341");
	text = text.replace(/&acirc;/g, "\342");
	text = text.replace(/&atilde;/g, "\343");
	text = text.replace(/&auml;/g, "\344");
	text = text.replace(/&aring;/g, "\345");
	text = text.replace(/&aelig;/g, "\346");
	text = text.replace(/&ccedil;/g, "\347");
	text = text.replace(/&egrave;/g, "\350");
	text = text.replace(/&eacute;/g, "\351");
	text = text.replace(/&ecirc;/g, "\352");
	text = text.replace(/&euml;/g, "\353");
	text = text.replace(/&igrave;/g, "\354");
	text = text.replace(/&iacute;/g, "\355");
	text = text.replace(/&icirc;/g, "\356");
	text = text.replace(/&iuml;/g, "\357");
	text = text.replace(/&eth;/g, "\360");
	text = text.replace(/&ntilde;/g, "\361");
	text = text.replace(/&ograve;/g, "\362");
	text = text.replace(/&oacute;/g, "\363");
	text = text.replace(/&ocirc;/g, "\364");
	text = text.replace(/&otilde;/g, "\365");
	text = text.replace(/&ouml;/g, "\366");
	text = text.replace(/&oslash;/g, "\370");
	text = text.replace(/&ugrave;/g, "\371");
	text = text.replace(/&uacute;/g, "\372");
	text = text.replace(/&ucirc;/g, "\373");
	text = text.replace(/&uuml;/g, "\374");
	text = text.replace(/&yacute;/g, "\375");
	text = text.replace(/&thorn;/g, "\376");
	text = text.replace(/&yuml;/g, "\377");
	text = text.replace(/&oelig;/g, "\523");
	text = text.replace(/&OElig;/g, "\522");
	return text;
}