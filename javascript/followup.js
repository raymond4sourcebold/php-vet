
/**
 * Javascript for follow-up form
 */

{honorary}
{lastName}
{animalName}
{jsChannelArray}
{defaultChannel}
{aCatMsg}
var holdMessage = '';

$(document).ready(function() {
	$('#frmEntryPane').click(function(){
		if ($('#boiteaddmsg').is(':visible')) {
			$('#boiteaddmsg').slideUp();
			$('#frmEntryPane').text('{lang_click_new_followup}');
			resetForm();
		} else {
			$('#boiteaddmsg').slideDown();
			$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_followup_form}" src="/images/btn-delete.gif" />');
			$('#frmFollowUp').focus();
			
			// Set default
			setChannelReceiver('SMS');
		}
		return false;
	});
	
	// Show auto hide message if it contains something
	if (($('#autoHideMessage').text() != '')) {
		$('.autoHidePane').css('display', 'inline');
	}
	
	// Add submit button clears hidden: updateRowId so that it's understood to be an Add and not Update.
	$('#addRowBtn').click(function() {
		$('#updateRowId').val('');
	});
	
	// Cancel update button
	$('#updateRowCancelBtn').click(function() {
		$('#frmEntryPane').text('{lang_click_new_followup}');
		$('#boiteaddmsg').slideUp();
		resetForm();
	});
	
	// Set default
	setChannelReceiver(defaultChannel);
	
	// Assign change action to manage showing of channel receiver if any.
	$('#selChannel').change(function(){
		setChannelReceiver(this.value);
	});
	
	$('#selFupSendDate').change(function(){
		if (this.value == 'set') {
			$('.dp-choose-date').click();
		} else if (this.value == 'now') {
			$('#dteSendDate').val('{nowMmDdYyyy}');
			$('.date-pick').dpSetSelected('{nowMmDdYyyy}');
		} else {
			var numAndDate = this.value.split(':');
			var objDate	= new Date();
			var nowYear	= {nowYyyy};
			
			if (numAndDate[1] == 'w') {
				objDate.setDate(objDate.getDate() + (numAndDate[0] * 7));
				
			} else if (numAndDate[1] == 'm') {
				var nMonths	= objDate.getMonth() + parseInt(numAndDate[0]);
				if (nMonths > 12) {
					nMonths		-= 12;
					nowYear++;
				}
				objDate.setMonth(nMonths);
				
			} else if (numAndDate[1] == 'y') {
				nowYear		+= parseInt(numAndDate[0]);
			}
			
			// Format: add 0 in front to have a 2-digit value
			var compMm	= objDate.getMonth();
			
			if (++compMm < 10) {
				compMm	= '0' + compMm;
			}
			
			var compDd	= objDate.getDate();
			
			if (compDd < 10) {
				compDd	= '0' + compDd;
			}
			
			$('#dteSendDate').val(compMm + '/' + compDd + '/' + nowYear);
			$('.date-pick').dpSetSelected(compMm + '/' + compDd + '/' + nowYear);
		}
		
		$('#dteSendDate').focus().select();
	});
	
        Date.firstDayOfWeek = 7;
        Date.format = 'mm/dd/yyyy';

        $('.date-pick').datePicker({startDate:'{nowMmDdYyyy}'});
	
	
	/**
	 * Category, message selection
	 */
	$('#selCategory').change(function(){
		assignSelOptions(this.value, 'selMessage', 0, 'taMsgText');
	});
	
	$('#selMessage').change(function(){
		assign(this.value, 'taMsgText');
	});


	$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_followup_form}" src="/images/btn-delete.gif" />');
	
	$("#tblSortable").tablesorter({
		cssHeader: 'headSort',
		sortList:[[0,0],[1,0],[2,0],[3,0]],
		headers: {4:{sorter: false},5:{sorter: false}}
	});
});

/**
 * Sets channel receiver (cell, home, office)
 */
function setChannelReceiver(strChannel, nSelected)
{
	var hideIt	= true;
	
	$('.clsChRecOption').remove();
	
	for (var i in jsChannelArray[strChannel]) {
		if (jsChannelArray[strChannel][i] != '') {
			hideIt	= false;
		}
		$('#selChannelReceiver').append('<option class="clsChRecOption" value="' 
			+ i + '"'
			+ (nSelected == i ? ' selected="selected" ' : '') + '>' 
			+ jsChannelArray[strChannel][i] + '</option>');
	}
	
	if (hideIt) {
		$('#divChannelReceiver').hide();
	} else {
		$('#divChannelReceiver').show();
	}
}

/**
 * Validation
 */
function entryValidate()
{
	var errMsg = '';
	
	if ($('#taMsgText').val() == '') {
		errMsg		+= "\n\t" + '{lang_invalid_message}';
		$('#selCategory').focus();
	}
	
	if (errMsg == '') {
		if (holdMessage != $('#taMsgText').val()) {
			// Changed
			$('#isCustomMessage').val('1');
		}
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
	return false;
}

/**
 * Calls Ajax to delete a follow-up
 */
function rowDelete(rKey, messageId)
{
	var ans = confirm('{lang_confirm_delete_followup}');
	
	if (ans) {
		showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{wait_deleting_followup}...</div>', 'middle');
		$.ajax({
			type: "POST",
			url: "/followup/delete/" + rKey + "/" + messageId,
			success: function(msg){
				if (msg == 'SUCCESS') {
					showMessage('{lang_success}', '{lang_followup_deleted}', 'top');
					setTimeout('hideMessage();', 3000);
					$('#row_' + rKey).hide(); // Hide from grid
				} else {
					showMessage('{lang_failure}', '{lang_followup_deletion_error}', '240', true);
				}
			}
		});
	}
	return false;
}

/**
 * Ajax call to get an followup
 */
function rowEdit(rKey)
{
	// Assign value to updateRowId
	$('#updateRowId').val(rKey);
	
	// Clear custom message Id
	$('#customMessageId').val('');
	
	$('.updateButtonFamily').show();
	$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_followup_form}" src="/images/btn-delete.gif" />');
	$('#boiteaddmsg').slideDown();
	
	
        showMessage('Processing...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_reading_database}...</div>', '400');
        $.ajax({
                type: "GET",
                url: "/followup/getJsonFollowUp/" + rKey,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
                async: false,
                success: function(ajaxData){
			window.aMatched	= ajaxData;
                }
        });
	
	// Hide Ajax message
	hideMessage();
	
	// Assign data from DB
	if (aMatched['isCustomMessage'] == '1') {
		/**
		 * This was a customized message
		 */
		$('#selCategory').val(aMatched['customMsgCategoryId']);
		assignSelOptions(aMatched['customMsgCategoryId'], 'selMessage', 0, 'taMsgText');
		
		$('#selMessage').val(aMatched['modelFromMsgId']);
		$('#taMsgText').val(aMatched['customMsgBody']);
		
		/**
		 * Assign custom messageId to POST.  This will be deleted before saving the updated message
		 */
		$('#customMessageId').val(aMatched['messageId']);
		
	} else {
		/**
		 * This was a standard message
		 */
		stopLoop	= false;
		
		for (var i in aCatMsg) {
			for (var j in aCatMsg[i]) {
				if (j == aMatched['messageId']) {
					stopLoop	= true;
					break; // now we can use i and j
				}
			}
			if (stopLoop) {
				break;
			}
		}
		
		$('#selCategory').val(i);
		assignSelOptions(i, 'selMessage', 0, 'taMsgText');
		
		$('#selMessage').val(j);
		assign(j, 'taMsgText');
	}
	
	// Format and assign the date
	$('#selFupSendDate').val('set');
	var strDate	= aMatched['sendDate'].substring(0, 10);
	var aDate	= strDate.split('-');
	var fmtDate	= aDate[1] + '/' + aDate[2] + '/' + aDate[0];
	
	$('#dteSendDate').val(fmtDate);
	$('.date-pick').dpSetSelected(fmtDate);
	
	
	// This loop will get value for selChannel
	stopLoop	= false;
	
	for (var k in jsChannelArray) {
		for (var l in jsChannelArray[k]) {
			if (l == aMatched['overrideChannelId']) {
				stopLoop	= true;
				break; // Now we can use k
			}
		}
		if (stopLoop) {
			break;
		}
	}
	$('#selChannel').val(k);
	setChannelReceiver(k, l);
	
	
	// Disable enter key which submits the page for Add action
	$(document).keypress(function(){
		var keyp = [event.keyCode||event.which];
		if (keyp == 13) {
			event.preventDefault? event.preventDefault() : event.returnValue = false;
		}
	});
	
	$('#taMsgText').focus(function(){
		if ($('#updateRowId').val()) {
			// We're on edit mode: Restore enter key's submit functionality
			$(document).unbind('keypress');
		}
	});
	
	$('#taMsgText').blur(function(){
		if ($('#updateRowId').val()) {
			// We're on edit mode: Disable enter key which submits the page for Add action
			$(document).keypress(function(){
				var keyp = [event.keyCode||event.which];
				if (keyp == 13) {
					event.preventDefault? event.preventDefault() : event.returnValue = false;
				}
			});
		}
	});

	return false;
}

/**
 * Initializes message entry form
 */
function resetForm()
{
	$("#form1")[0].reset();
	
	// Initialize selected message
	$('#taMsgText').html('');
	
	$('.updateButtonFamily').hide();
	
	// Restore enter key's submit functionality
	$(document).unbind('keypress');
}

/**
 * Sets options based on selected category
 */
function assignSelOptions(nCat, idSel, save, divMsgTextArea)
{
	$('#' + idSel).children("option").each(function(x){
		if (typeof save == 'undefined') {
			$(this).remove();
		} else {
			// save this id
			if ($(this).attr('value') != save) {
				$(this).remove();
			}
		}
	})
	
	for (var i in aCatMsg[nCat]) {
		$('#' + idSel).append("<option value=" + i + ">" + aCatMsg[nCat][i] +"</option>");
	}
	
	// Initialize selected message
	$('#' + divMsgTextArea).val('');
}

/**
 * Assign selected message
 */
function assign(msgId, divMsgTextArea)
{
	if (typeof aMessages[msgId] == 'undefined') {
		$('#' + divMsgTextArea).val('');
		holdMessage	= '';
	} else {
		var msg		= replaceAll(aMessages[msgId], String.fromCharCode(27), "\n");
		var strTmp	= '';
		
		// This loop changes problematic characters: [/] into {:}
		for (var i=0; i < msg.length; i++) {
			aChr	= msg[i];
			if (aChr == '[') {strTmp += '{';continue;}
			if (aChr == '/') {strTmp += ':';continue;}
			if (aChr == ']') {strTmp += '}';continue;}
			strTmp	+= aChr;
		}
		
		// Now we can do replacement here safely
		msg	= replaceAll(strTmp, "{honorary:}", honorary + ".");
		msg	= replaceAll(msg, "{owner:}", lastName);
		msg	= replaceAll(msg, "{animal:}", animalName);
		
		// This loop restores characters [/]
		for (var i=0; i < msg.length; i++) {
			aChr	= msg[i];
			if (aChr == '{') {strTmp += '[';continue;}
			if (aChr == ':') {strTmp += '/';continue;}
			if (aChr == '}') {strTmp += ']';continue;}
			strTmp	+= aChr;
		}
		
		$('#' + divMsgTextArea).val(msg);
		
		holdMessage	= msg;
	}
}

// eof
