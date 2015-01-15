/**
 * Add onclick action to message variable buttons
 */

{goCreateNew}

$(document).ready(function() {
	// For insertion of message variables
	$('.btnMessageVariable').click(function() {
		addText('[' + this.value + '/]', 'textemessage');
	});
	
	$('#frmMessagePane').click(function() {
		if ($('#boiteaddmsg').is(':visible')) {
			$('#boiteaddmsg').slideUp();
			$('#frmMessagePane').text('{lang_click_new_message}');
			resetMessageForm();
		} else {
			$('#boiteaddmsg').slideDown();
			$('#frmMessagePane').html('<img class="commonImage" title="{lang_hide_message_form}" src="/images/btn-delete.gif" />');
			$('#newtype').focus();
		}
		return false;
	});
	
	// Add submit button clears hidden: updateMessageId
	$('#addMsgButton').click(function() {
		$('#updateMessageId').attr('value', '');
	});
	
	// Cancel update button
	$('#updateMsgCancelButton').click(function() {
		$('#frmMessagePane').text('{lang_click_new_message}');
		$('#boiteaddmsg').hide();
		resetMessageForm();
	});
	
	$('#selMsgCategory').change(function(){
		choixchange("selMsgCategory", "newtype");
	});
	
	if (goCreateNew) {
		$('#boiteaddmsg').show();
		$('#frmMessagePane').html('<img class="commonImage" title="{lang_hide_message_form}" src="/images/btn-delete.gif" />');
		$('#newtype').focus();
	}

	$("#tblSortable").tablesorter({
		cssHeader: 'headSort',
		sortList:[[2,0],[3,0],[0,0]],
		headers: { 
			4: { 
				sorter: false 
			},
			5: { 
				sorter: false 
			} 
		} 
	});
});

// Ajout du mini bbcode
function addText(instext, nomarea) {
	var mess = document.getElementById(nomarea);
	//IE support
	if (document.selection) {
		mess.focus();
		sel = document.selection.createRange();
		sel.text = instext;
		document.guestbook.focus();
	} else if (mess.selectionStart || mess.selectionStart == "0") { //MOZILLA/NETSCAPE support
		var startPos = mess.selectionStart;
		var endPos = mess.selectionEnd;
		var chaine = mess.value;
		
		mess.value = chaine.substring(0, startPos) + instext + chaine.substring(endPos, chaine.length);
		
		mess.selectionStart = startPos + instext.length;
		mess.selectionEnd = endPos + instext.length;
		mess.focus();
	} else {
		mess.value += instext;
		mess.focus();
	}
}

// Javascript pour le nouveau type
function choixchange(idchoix, nomchamp) {
	if(document.getElementById(idchoix).value == 0) {
		document.getElementById(nomchamp).style.display = 'inline';
	} else {
		document.getElementById(nomchamp).style.display = 'none';
	}
}

// Validation
function messageValidate()
{
	var errMsg = '';
	
	if ($('#textemessage').attr('value') == "") {
		errMsg		= "\n\t" + '{lang_invalid_message}';
		$('#textemessage').focus();
	}
	
	if ($('#textedescription').attr('value') == "") {
		errMsg		= "\n\t" + '{lang_invalid_description}' + errMsg;
		$('#textedescription').focus();
	}
	
	if ($('#selMsgCategory').attr('value') == "0") {
		if ($('#newtype').attr('value') == "") {
			errMsg		= "\n\t" + '{lang_invalid_category}' + errMsg;
			$('#newtype').focus();
		}
	}
	
	if (errMsg == '') {
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
	return false;
}

function messagesDelete(rKey)
{
	var ans = confirm('{lang_confirm_delete_message}');
	
	if (ans) {
		showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_deleting_message}...</div>', 'middle');
		$.ajax({
			type: "POST",
			url: "/messages/delete/" + rKey,
			success: function(msg){
				if (msg == 'SUCCESS') {
					showMessage('{lang_success}', '{lang_message_deletion_ok}', 'top');
					setTimeout('hideMessage();', 3000);
					$('#row_' + rKey).hide(); // Hide from grid
				} else {
					showMessage('{lang_failure}', '{lang_message_deletion_error}', 'middle');
				}
			}
		});
	}
	return false;
}

/**
 * Ajax call to get a message
 */
function messagesEdit(rKey)
{
	showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_reading_database}...</div>', '400');
	
	$.getScript('/messages/fetch2js/message/' + rKey, function(){
		hideMessage();
		$('#newtype').hide();
		$('.updateButtonFamily').show();
		$('#updateMessageId').attr('value', rKey);
		$('#boiteaddmsg').show();
		$('#frmMessagePane').html('<img class="commonImage" title="{lang_hide_message_form}" src="/images/btn-delete.gif" />');
	});
	return false;
}

function fetch2js_message(selMsgCategory, textedescription, textchannel, textemessage, chkActive)
{
	$('#selMsgCategory').val(selMsgCategory);
	$('#textedescription').val(textedescription);
	$('.radioChannel[@value="' + textchannel + '"]').check('on');
	$('#textemessage').val(replaceAll(textemessage, String.fromCharCode(27), '\n'));
	$('#chkActive').check(chkActive ? 'on' : 'off');
}

/**
 * Initializes message entry form
 */
function resetMessageForm()
{
	$('#updateMessageId').attr('value', ''); // hidden field
	
	$('#selMsgCategory').attr('value', 0);
	$('#newtype').show();
	$('#textedescription').attr('value', '');
	$('#textemessage').attr('value', '');
	$('#chkActive').check('off');
	
	$('.updateButtonFamily').hide();
}

// eof
