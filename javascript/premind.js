
/**
 * premind.js
 */

{aCatMsg}
{nReminder}

$(document).ready(function() {
	$('#selCategory').change(function(){
		assignSelOptions(this.value, 'selMessage', 0, 'taMsgText');
	});
	
	$('#selMessage').change(function(){
		assign(this.value, 'taMsgText');
	});
	
	hideOrShow(nReminder);
	
	$('.radReminder').click(function(){
		hideOrShow(this.value);
	});
});

function hideOrShow(nRem)
{
	if (nRem == 1) {
		nReminder	= 1;
		$('#reminderAfterNdays2').hide();
		$('#reminderAfterNdays2').attr('value', 0);
		$('#divSelect').show();
	} else if (nRem == 2) {
		nReminder	= 2;
		$('#reminderAfterNdays2').show();
		$('#divSelect').show();
	} else { // 0
		nReminder	= 0;
		$('#divSelect').hide();
	}
}

function validateEntry()
{
	var errMsg = '';
	
	if (nReminder == 0) {
		return true;
	}
	
	/**
	 * validation for message
	 */
	if ($('#taMsgText').html() == '') {
		errMsg		+= "\n\t" + '{lang_invalid_message}';
	}
	
	if ($('#eventDate').attr('value') == '0') {
		errMsg		+= "\n\t" + '{lang_pls_select_evtdate}';
	}
	
	if (nReminder == 1) {
		if ($('#reminderAfterNdays1').attr('value') == '0') {
			errMsg		+= "\n\t" + '{lang_pls_select_rem_duration_1}';
		}
	} else { // 2 Reschedules
		if ($('#reminderAfterNdays1').attr('value') == '0') {
			errMsg		+= "\n\t" + '{lang_pls_select_rem_duration_1}';
		}
		
		if ($('#reminderAfterNdays2').attr('value') == '0') {
			errMsg		+= "\n\t" + '{lang_pls_select_rem_duration_2}';
		}
	}
	
	if (errMsg == '') {
		return true;
	}
	
	alert('Correct the following:' + "\n" + errMsg);
	
	return false;
}

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
	$('#' + divMsgTextArea).html('');
}

function assign(msgId, divMsgTextArea)
{
	if (typeof aMessages[msgId] == 'undefined') {
		$('#' + divMsgTextArea).html('');
	} else {
		var msg		= replaceAll(aMessages[msgId], String.fromCharCode(27), "\n<br />");
		$('#' + divMsgTextArea).html(msg);
	}
}

// eof
