
/**
 * pmessage.js
 */

{aCatMsg}

$(document).ready(function(){
	$('#selCategory').change(function(){
		assignSelOptions(this.value, 'selMessage', 0, 'taMsgText');
	});
	
	$('#selMessage').change(function(){
		assign(this.value, 'taMsgText');
	});
	
	$('#chkSendAnimalDead').click(function(){
		if ($('#chkSendAnimalDead').attr('checked') == true) {
			var ans	= confirm('{lang_confirm_are_you_sure}');
			if (!ans) {
				return false;
			}
		}
	});
	
	$('#frmProcedureName').blur(function(){
		checkProcName();
	});
	
	$('#frmProcedureName').keypress(function(event){
		var keyp = [event.keyCode||event.which];
		if (keyp == 13) {
			checkProcName();
		}
		return true;
	});
});

/**
 * Does Ajax call to check if procedure name is available.
 */
function checkProcName()
{
	if ($('#frmProcedureName').attr('value') == '') {
		$('#ajaxMsg').html('');
		return;
	}
	showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_procname_availability}...</div>', '400');
	$.ajax({
		type: "POST",
		url: '/pmessage/checkprocname/' + $('#frmProcedureName').attr('value'),
		async: false,
		success: function(ajaxRetVal){
			if (ajaxRetVal == 'NOT_FOUND') {
				$('#ajaxMsg').html('<span class="ajaxOk">{lang_proc_name_available}</span>');
			} else if (ajaxRetVal == 'FOUND_SELF') {
				$('#ajaxMsg').html('<span class="ajaxOk">{lang_proc_name_not_changed}</span>');
			} else if (ajaxRetVal == 'RENAME_TO_EXIST') {
				$('#ajaxMsg').html('<span class="ajaxNotOk">{lang_proc_name_you_already_use}</span>');
			} else {
				$('#ajaxMsg').html('<span class="ajaxNotOk">{lang_proc_name_exists_already}</span>');
			}
		}
	});
	hideMessage();
}

/**
 * Validate user entry before submitting the form
 */
function validateEntry()
{
	var errMsg = '';
	
	/**
	 * validation for procedure name
	 */
	if ($('#frmProcedureName').attr('value') == '') {
		errMsg		+= "\n\t" + '{lang_invalid_proc_name}';
	} else {
		if ($('#ajaxMsg').text() == '') {
			checkProcName();
		}
		if ($('#ajaxMsg').text() == '{lang_proc_name_exists_already}'
		 || $('#ajaxMsg').text() == '{lang_proc_name_you_already_use}') {
			errMsg		+= "\n\t" + '{lang_try_another_proc_name}';
		}
	}
	
	/**
	 * validation for message
	 */
	if ($('#taMsgText').attr('value') == '') {
		errMsg		+= "\n\t" + '{lang_invalid_message}';
	}
		
	if (errMsg == '') {
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
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
		var msg		= replaceAll(aMessages[msgId], String.fromCharCode(27), "\n");
		$('#' + divMsgTextArea).html(msg);
	}
}

// eof
