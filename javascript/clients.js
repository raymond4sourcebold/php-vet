
/**
 * Javascript for client and client phone manager
 */

{goCreateNew}
{strModelPhone}
{nPhoneInstance}
var limitPhone = 9;

var holdOptCh;
var holdSelPreferred	= 'NOT_SET';
var holdSelBackup	= 'NOT_SET';

// Phone type priority
var typePriority = Array();
typePriority['mobile']	= 0;
typePriority['homeph']	= 0;
typePriority['ofisph']	= 0;
typePriority['homefx']	= 0;
typePriority['ofisfx']	= 0;
typePriority['homepf']	= 0;
typePriority['ofispf']	= 0;

$(document).ready(function() {
	
	$('#addRowBtn').click(function(){
		// Clear update row Id when add button is clicked.
		$('#updateRowId').val('');
	});
	
	$('#frmEntryPane').click(function() {
		if ($('#boiteaddmsg').is(':visible')) {
			$('#frmEntryPane').text('{lang_click_new_client}');
			$('#boiteaddmsg').slideUp();
			resetForm();
		} else {
			$('#boiteaddmsg').slideDown();
			$('#frmEntryPane').html('<img class="commonImage" title="{hide_client_form}" src="/images/btn-delete.gif" />');
			$('#frmClient').focus();
		}
		return false;
	});
	
	// Show auto hide message if it contains something
	if (($('#autoHideMessage').text() != '')) {
		$('.autoHidePane').css('display', 'inline');
	}
	
	// Add submit button clears hidden: updateRowId so that it's understood to be an Add and not Update.
	$('#addRowBtn').click(function() {
		$('#updateRowId').attr('value', '')
	});
	
	// Cancel update button
	$('#updateRowCancelBtn').click(function() {
		$('#frmEntryPane').text('{lang_click_new_client}');
		$('#boiteaddmsg').slideUp();
		resetForm();
	});
	
	// Add a phone on button click
	$('#addPhoneBtn').click(function(){
		showOnePhoneClone();
	});
	
	// Add a phone on caption click
	$('#addPhoneSpan').click(function(){
		showOnePhoneClone();
	});
	
	// Hide add button if limit is reached
	if (nPhoneInstance >= limitPhone) {
		$('#divAddPhone').hide();
	}
	
	// Save options to variable
	holdOptCh	= $('#selPreferred').html();
	
	// Add a common class to channel selects
	$('#selPreferred').addClass('clsSelChannel');
	$('#selBackup').addClass('clsSelChannel');
	
	// Use onfocus event to remove unavailable channel options
	$('#selPreferred').focus(function(){
		holdSelPreferred	= $('#selPreferred option:selected').val();
		$('#selPreferred').html(holdOptCh);
		removedNaChannels();
	});
	// Enable checkbox for 'Use Preferred Channel Exclusively' when 'Preferred Channel' is selected.
	$('#selPreferred').change(function(){
		if ($('#selPreferred').val()) {
			$('#chkUsePreferredOnly').attr('disabled', false);
		} else {
			$('#chkUsePreferredOnly').attr('disabled', true);
		}
	});
	
	// Use onfocus event to remove unavailable channel options
	$('#selBackup').focus(function(){
		holdSelBackup		= $('#selBackup option:selected').val();
		$('#selBackup').html(holdOptCh);
		removedNaChannels();
	});
	
	// Hiding and showing of Backup and Nixed channels
	$('#chkUsePreferredOnly').click(function(){
		if (this.checked) {
			$('#divBackupNixChannels').slideUp();
		} else {
			$('#divBackupNixChannels').slideDown();
		}
	});
	
	// Padding of home and office address entry fields
	$('.divAddressEntry label').css({textAlign:"right", paddingRight:"10px", display:"block", width:"130px", float:"left", marginTop:"3px"});
	$('.divAddressEntry input').css({display:"block", float:"left", marginBottom:"1px"});
	$('.divAddressEntry br').css({clear:"left"});
	
	// Assign click on address clear buttons
	$('#btnClearHomeAddr').click(function(){
		clearHomeEntry();
	});
	$('#btnClearOfficeAddr').click(function(){
		clearOfficeEntry();
	});
	
	// Add optional text on Address Line 2
	if ($('#homeAddressLine2').val() == '') {
		$('#homeAddressLine2').val('{lang_optional_text_value}');
	}
	// Add optional text on Address Line 2
	if ($('#ofisAddressLine2').val() == '') {
		$('#ofisAddressLine2').val('{lang_optional_text_value}');
	}
	// Clear text: (optional) on click
	$('#homeAddressLine2').focus(function(){
		if ($('#homeAddressLine2').val() == '{lang_optional_text_value}') {
			$('#homeAddressLine2').val('');
		}
	});
	$('#ofisAddressLine2').focus(function(){
		if ($('#ofisAddressLine2').val() == '{lang_optional_text_value}') {
			$('#ofisAddressLine2').val('');
		}
	});
	// Put text: (optional) on blur if empty
	$('#homeAddressLine2').blur(function(){
		if ($('#homeAddressLine2').val() == '') {
			$('#homeAddressLine2').val('{lang_optional_text_value}');
		}
	});
	$('#ofisAddressLine2').blur(function(){
		if ($('#ofisAddressLine2').val() == '') {
			$('#ofisAddressLine2').val('{lang_optional_text_value}');
		}
	});
	
	$('#searchText').keyup(function(){
		goSearch();
	});
	
	$('#searchText').focus();
	
	$('#nMatchCount').css({float:"right", fontSize:"0.8em", fontFamily:"Arial", padding:"3px 5px 3px 10px", color:"#00FF00", backgroundColor:"#000000", display:"none"});
	
	if (goCreateNew) {
		$('#boiteaddmsg').show();
		$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_client_form}" src="/images/btn-delete.gif" />');
		$('#selHonorary').focus();
	}
});

/**
 * Gets matching rows to search string
 */
var aSearchResult;

function goSearch()
{
	if ($('#searchText').val().length < 3) {
		$('#nMatchCount').hide();
		return;
	}
	
	$('#nMatchCount').show();
	
        showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_matching_similar_client}...</div>', '400');
        $.ajax({
                type: "GET",
                url: "/clients/getJsonClientSearch/" + $('#searchText').val(),
		contentType: "application/json; charset=utf-8",
		dataType: "json",
                async: false,
                success: function(ajaxRetVal){
			window.aSearchResult	= ajaxRetVal;
                }
        });
	
	// Hide Ajax message
	hideMessage();
	
	// Clear grid rows
	$('.clsGridTr').remove();
	
	var nMatching	= 0;
	
	for (var i in aSearchResult) {
		$('#idTbody').append(
			"<tr class='f0ShadeHover clsGridTr' id='row_" + aSearchResult[i]['clientId'] + "'>" +
				"<td>" + aSearchResult[i]['lastName'] + "</td>" +
				"<td>" + aSearchResult[i]['firstName'] + "</td>" +
				"<td><div class='divinfo'>" +
					"<a class='info'>" + aSearchResult[i]['homeCity']
						+ "<span>" + aSearchResult[i]['homeAddress1'] 
							+ (aSearchResult[i]['homeAddress2'] ? ', ' + aSearchResult[i]['homeAddress2'] : '') 
						+ "</span>"
						+ "</a></div></td>" +
				"<td><a href='#' onclick='return rowDelete(" + aSearchResult[i]['clientId'] + ");'><img src='/images/supprimer.png' alt='{lang_delete}' border='0' /></a></td>" +
				"<td><a href='#' onclick='return rowEdit(" + aSearchResult[i]['clientId'] + ");'><img src='/images/modiffier.png' alt='{lang_modify}' border='0' /></a></td>" +
				"<td><a href='/animals/manage/" + aSearchResult[i]['clientId'] + "'><img src='/images/ani.png' alt='{lang_goto_animal_manager}' border='0' /></a></td>" +
				"<td><a href='/followup/all/" + aSearchResult[i]['clientId'] + "'><img src='/images/ani.png' alt='{lang_goto_animal_manager}' border='0' /></a></td>" +
			"</tr>"
		);
		
		$('#nMatchCount').text(++nMatching);
	}
	
	$('#nMatchCount').text(nMatching);
}


/**
 * Removes n/a channels
 */
function removedNaChannels()
{
	var aCh = Array();
	aCh['mobile']	= false;
	aCh['homeph']	= false;
	aCh['ofisph']	= false;
	aCh['homefx']	= false;
	aCh['ofisfx']	= false;
	aCh['homepf']	= false;
	aCh['ofispf']	= false;
	
	var nCh = Array();
	nCh['mobile']	= 2;
	nCh['homeph']	= 4;
	nCh['ofisph']	= 5;
	nCh['homefx']	= 8;
	nCh['ofisfx']	= 9;
	
	// Check selected phone types
	$('.selPhoneType option:selected').each(function(){
		if (this.value) {
			aCh[this.value]		= true;
		}
	});
	
	if (aCh['homepf'] == true) {
		// Set home phone and fax if "home phone & fax" is selected
		aCh['homeph']		= true;
		aCh['homefx']		= true;
	}
	if (aCh['ofispf'] == true) {
		// Set office phone and fax if "office phone & fax" is selected
		aCh['ofisph']		= true;
		aCh['ofisfx']		= true;
	}
	
	// Removed unused channels
	for (var i in aCh) {
		if (i == 'mobile') {
			if (aCh['mobile'] == false) {
				$('.clsSelChannel option[value=2]').remove();
				$('.clsSelChannel option[value=3]').remove();
			}
		} else if (aCh[i] == false) {
			$('.clsSelChannel option[value=' + nCh[i] + ']').remove();
		}
	}
	
	// Remove email as an option if email is not supplied
	if ($('#frmEmail').val() == '') {
		$('.clsSelChannel option[value=1]').remove(); // email
	}
	
	// 6 Snail mail home
	if ($('#homeAddressLine1').val() == '' || $('#homeCity').val() == '' || $('#homePostalCode').val() == '' || $('#homeProvOrState').val() == '') {
		$('.clsSelChannel option[value=6]').remove();
	}
	
	// 7 Snail mail office
	if ($('#ofisAddressLine1').val() == '' || $('#ofisCity').val() == '' || $('#ofisPostalCode').val() == '' || $('#ofisProvOrState').val() == '') {
		$('.clsSelChannel option[value=7]').remove();
	}
	
	if (holdSelPreferred != 'NOT_SET') {
		$('#selPreferred').val(holdSelPreferred);
	}
	if (holdSelBackup != 'NOT_SET') {
		$('#selBackup').val(holdSelBackup);
	}
	
	holdSelPreferred	= 'NOT_SET';
	holdSelBackup		= 'NOT_SET';
}

/**
 * Ajax call to get an client
 */
var aClient;
var aPhone;

function rowEdit(rKey)
{
	// Reset entry form first.
	resetForm();
	
	$('#updateRowId').val(rKey);
	
	nPhoneInstance	= 0;
	
	$('#boiteaddmsg').slideDown();
	$('#frmEntryPane').html('<img class="commonImage" title="Hide new client entry form" src="/images/btn-delete.gif" />');
	
	// Ajax call to get client data
        showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_reading_client}...</div>', '400');
        $.ajax({
                type: "GET",
                url: "/clients/getJsonClient/" + rKey,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
                async: false,
                success: function(ajaxRetVal){
			window.aClient	= ajaxRetVal;
                }
        });
        
	
	// General Information
	$('#selHonorary').val(aClient['honoraryId']);
	$('#frmLastname').val(aClient['lastName']);
	$('#frmFirstname').val(aClient['firstName']);
	$('#frmExternalId').val(aClient['clientExternalId']);
	
	// Control Flow
	$('.radNoMessage[value="' + aClient['noMessage'] + '"]').check('on');
	$('#selPriority').val(aClient['messageThreshold']);
	
	// Web
	var aEmail = aClient['email'].split(',');
	$('#frmEmail').val(aEmail[0]);
	$('#frmSecondaryEmail').val(aEmail[1]);
	
	// Address
	$('#homeAddressLine1').val(aClient['homeAddress1']);
	$('#homeAddressLine2').val(aClient['homeAddress2']);
	$('#homeCity').val(aClient['homeCity']);
	$('#homePostalCode').val(aClient['homePostalCode']);
	$('#homeProvOrState').val(aClient['homeProvinceOrState']);
	
	$('#ofisAddressLine1').val(aClient['officeAddress1']);
	$('#ofisAddressLine2').val(aClient['officeAddress2']);
	$('#ofisCity').val(aClient['officeCity']);
	$('#ofisPostalCode').val(aClient['officePostalCode']);
	$('#ofisProvOrState').val(aClient['officeProvinceOrState']);
	
	$('#country').val(aClient['country']);
	
	
	/**
	 * Channel Settings
	 */
	
	// Restore all options
	$('#selPreferred').html(holdOptCh);
	$('#selBackup').html(holdOptCh);
	
	// Preferred Channel
	$('#selPreferred').val(aClient['preferredChannelId']);
	
	if (aClient['preferredChannelId']) {
		$('#chkUsePreferredOnly').attr('disabled', false);
	} else {
		$('#chkUsePreferredOnly').attr('disabled', true);
	}
	
	// Backup Channel
	$('#selBackup').val(aClient['backupChannelId']);
	
	if (aClient['usePreferredExclusively'] == 1) {
		$('#chkUsePreferredOnly').check('on');
		$('#divBackupNixChannels').slideUp();
	} else {
		$('#chkUsePreferredOnly').check('off');
		$('#divBackupNixChannels').slideDown();
	}
	
	// Set all channel to off
	$('#chkNix_1').check('off');
	$('#chkNix_2').check('off');
	$('#chkNix_3').check('off');
	$('#chkNix_4').check('off');
	$('#chkNix_5').check('off');
	$('#chkNix_6').check('off');
	$('#chkNix_7').check('off');
	$('#chkNix_8').check('off');
	$('#chkNix_9').check('off');
	
	
	// Saved nixed Csv to array
	if (aClient['nixedChannelIdCsv'].length) {
		var aNixedCh = aClient['nixedChannelIdCsv'].split(',');
		// Set nixed channels to on
		for (var i in aNixedCh) {
			$('#chkNix_' + aNixedCh[i]).check('on');
		}
	}
	
	/**
	 * Display client phone
	 */
	
	// Ajax call to get client phone numbers
        showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{wait_reading_phone_numbers}...</div>', '400');
        $.ajax({
                type: "GET",
                url: "/clients/getJsonCliPhone/" + rKey,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
                async: false,
                success: function(ajaxRetVal){
			window.aPhone	= ajaxRetVal;
                }
        });
	
	for (var i in aPhone) {
		showOnePhoneClone(aPhone[i]['phoneNumber'], aPhone[i]['phoneType'], aPhone[i]['priority']);		
	}
	
	// Hide Ajax message
	hideMessage();
	
	// Disable enter key which submits the page for Add action
	$(document).keypress(function(){
		var keyp = [event.keyCode||event.which];
		if (keyp == 13) {
			event.preventDefault? event.preventDefault() : event.returnValue = false;
		}
	});
	
	$('#selHonorary').focus();
	$('.updateButtonFamily').show();
	
	return false;
}

/**
 * Displays one line of phone entry
 */
function showOnePhoneClone(dbPhoneNum, dbPhoneType, dbPhonePriority)
{
	var clone	= strModelPhone;
	clone		= replaceAll(clone, '~0~', nPhoneInstance);
	$('#divClonePhone').append(clone);
	
	$('#phoneNumber_' + nPhoneInstance)
		.attr('maxlength', 14)
		.attr('size', 14)
		.keypress(function(event){
			return telephoneKeyPress(event, this);
		})
		.focus();
	
	// Set values
	if (typeof dbPhoneNum != 'undefined') {
		$('#phoneNumber_' + nPhoneInstance).val(intToPhoneFormat(dbPhoneNum));
	}
	if (typeof dbPhoneType != 'undefined') {
		$('#selPhoneType_' + nPhoneInstance).val(dbPhoneType);
	}
	if (typeof dbPhonePriority != 'undefined') {
		typePriority[dbPhoneType]	= dbPhonePriority - 1; // Less one here because setPhonePriority() pre increments it.
		setPhonePriority(dbPhoneType, nPhoneInstance);
	}
	
	nPhoneInstance++;
	
	if (nPhoneInstance >= limitPhone) {
		$('#divAddPhone').hide();
	}
}

/**
 * Enables phone priority and sets it's default value
 */
function setPhonePriority(val, phoneIdx)
{
	if (typePriority[val] < limitPhone) {
		$('#selPhonePriority_' + phoneIdx).val(++typePriority[val]);
	}
	
	// enable phone priority
	$('#selPhonePriority_' + phoneIdx).attr('disabled', false);
}

/**
 * Removes one line of phone entry
 */
function removePhone(nClone) {
	$('#divPhone_' + nClone).remove();
}

/**
 * Validation of client and phone entries
 */
function entryValidate()
{
	var errMsg = '';
	var checkCountry	= false;
	
	// Validation for home address
	var h1 = $('#homeAddressLine1').val();
	var h2 = $('#homeCity').val();
	var h3 = $('#homePostalCode').val();
	var h4 = $('#homeProvOrState').val();
	if (h1 == '' && h2 == '' && h3 == '' & h4 == '' && ($('#homeAddressLine2').val() == '' || $('#homeAddressLine2').val() == '{lang_optional_text_value}')) {
		// No entry, disregard
	} else if (h1 != '' && h2 != '' && h3 != '' && h4 != '') {
		// Complete entry, disregard
		checkCountry	= true;
	} else {
		errMsg		= "\n\t" + "{lang_invalid_client_home_address}" + errMsg;
	}
	
	// Validation for office address
	var h1 = $('#ofisAddressLine1').val();
	var h2 = $('#ofisCity').val();
	var h3 = $('#ofisPostalCode').val();
	var h4 = $('#ofisProvOrState').val();
	if (h1 == '' && h2 == '' && h3 == '' & h4 == '' && ($('#ofisAddressLine2').val() == '' || $('#ofisAddressLine2').val() == '{lang_optional_text_value}')) {
		// No entry, disregard
	} else if (h1 != '' && h2 != '' && h3 != '' && h4 != '') {
		// Complete entry, disregard
		checkCountry	= true;
	} else {
		errMsg		= "\n\t" + "{lang_invalid_client_office_address}" + errMsg;
	}
	
	if (checkCountry) {
		if ($('#country').val() == "") {
			errMsg		= "\n\t" + "{lang_invalid_client_country}" + errMsg;
			$('#country').focus();
		}
	}
	
	if ($('#frmFirstname').val() == "") {
		errMsg		= "\n\t" + '{lang_invalid_first_name}' + errMsg;
		$('#frmFirstname').focus();
	}
	if ($('#frmLastname').val() == "") {
		errMsg		= "\n\t" + '{lang_invalid_last_name}' + errMsg;
		$('#frmLastname').focus();
	}
	if ($('#selHonorary option:selected').val() == 0) {
		errMsg		= "\n\t" + '{lang_invalid_honorary}' + errMsg;
		$('#selHonorary').focus();
	}
	
	/**
	 * validation for phone lines
	 */
	var aEmptyPhone		= new Array();
	var nEmpPhone		= 0;
	
	for (clone = 0; clone < limitPhone; clone++) {
		phoneNumber	= $('#phoneNumber_' + clone).val();
		
		if (typeof phoneNumber == 'undefined') {
			continue;
		}
		
		phoneType	= $('#selPhoneType_' + clone).val();
		phonePriority	= $('#selPhonePriority_' + clone).val();
		
		if (phoneNumber == '' && phoneType == '' && phonePriority == '') {
			// If all is blank, disregard it.
			$('#divPhone_' + clone).css('color', '#333333');
			aEmptyPhone[nEmpPhone++]		= clone;
			continue;
			
		} else if (phoneNumber != '' && phoneType != '' && phonePriority != '') {
			// This one is a complete entry.
			$('#divPhone_' + clone).css('color', '#333333');
			continue;
			
		} else {
			errMsg		+= "\n\t" + '{lang_invalid_phone}' + ' ' + (clone + 1) + '.';
			$('#divPhone_' + clone).css('color', '#FF0000');
		}
	}
	
	if (errMsg == '') {
		// Entries are ok.  Let's delete empty criteria.
		for (var i in aEmptyPhone) {
			$('#divPhone_' + aEmptyPhone[i]).remove();
		}
		
		if ($('#homeAddressLine2').val() == '{lang_optional_text_value}') {
			$('#homeAddressLine2').val('');
		}
		if ($('#ofisAddressLine2').val() == '{lang_optional_text_value}') {
			$('#ofisAddressLine2').val('');
		}
		
		// If secondary email is supplied but not primary email, make it the primary email.
		if ($('#frmEmail').val() == '' && $('#frmSecondaryEmail').val() != '') {
			$('#frmEmail').val($('#frmSecondaryEmail').val());
			$('#frmSecondaryEmail').val('');
		}
		
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
	return false;
}

/**
 * Delete a client
 */
function rowDelete(rKey)
{
	var ans = confirm('{lang_confirm_delete_client}');
	
	if (ans) {
		showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_deleting_client}...</div>', '400');
		$.ajax({
			type: "GET",
			url: "/clients/delete/" + rKey,
			success: function(msg){
				if (msg == 'SUCCESS') {
					showMessage('{lang_success}', '{lang_client_delete_ok}', '400');
					setTimeout('hideMessage();', 3000);
					$('#row_' + rKey).hide(); // Hide from grid
				} else {
					showMessage('{lang_failure}', '{lang_client_delete_error}', '400', true);
				}
			}
		});
	}
	return false;
}

/**
 * Clears the entire form
 */
function resetForm()
{
	$('#updateRowId').val('');
	
	// Remove all phone div lines
	$('.clsDivPhone').remove();
	nPhoneInstance	= 0;
	
	$('#chkUsePreferredOnly').attr('disabled', true);
	
	typePriority['mobile']	= 0;
	typePriority['homeph']	= 0;
	typePriority['ofisph']	= 0;
	typePriority['homefx']	= 0;
	typePriority['ofisfx']	= 0;
	typePriority['homepf']	= 0;
	typePriority['ofispf']	= 0;

	$("#form1")[0].reset();
	$('#homeAddressLine2').val('{lang_optional_text_value}');
	$('#ofisAddressLine2').val('{lang_optional_text_value}');
	
	$('.updateButtonFamily').hide();
	
	$('#divBackupNixChannels').slideUp();
	
	// Restore enter key's submit functionality
	$(document).unbind('keypress');
}

/**
 * Clears home entry fields
 */
function clearHomeEntry()
{
	$('#homeAddressLine1').val('');
	$('#homeAddressLine2').val('{lang_optional_text_value}');
	$('#homeCity').val('');
	$('#homePostalCode').val('');
	$('#homeProvOrState').val('');
	
	$('#homeAddressLine1').focus();
}

/**
 * Clears office entry fields
 */
function clearOfficeEntry()
{
	$('#ofisAddressLine1').val('');
	$('#ofisAddressLine2').val('{lang_optional_text_value}');
	$('#ofisCity').val('');
	$('#ofisPostalCode').val('');
	$('#ofisProvOrState').val('');
	
	$('#ofisAddressLine1').focus();
}

// eof
