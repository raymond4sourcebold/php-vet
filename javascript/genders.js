/**
 * Add onclick action to gender variable buttons
 */

var frmEntryPaneSw = true;

$(document).ready(function() {

	$('#frmEntryPane').click(function() {
		if (frmEntryPaneSw == true) {
			$('#boiteaddmsg').show('slow');
			$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_gender_form}" src="/images/btn-delete.gif" />');
			$('#frmGender').focus();
		} else {
			$('#boiteaddmsg').hide('slow');
			$('#frmEntryPane').text('{lang_click_new_gender}');
			resetForm();
		}
		frmEntryPaneSw	= !frmEntryPaneSw;
		return false;
	});
	
	$("#tblSortable").tablesorter({
		cssHeader: 'headSort',
		sortList:[[0,0]],
		headers: { 
			1: { 
				sorter: false 
			},
			2: { 
				sorter: false 
			} 
		} 
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
		frmEntryPaneSw = true;
		$('#frmEntryPane').text('{lang_click_new_gender}');
		$('#boiteaddmsg').hide();
		resetForm();
	});
});

/**
 * Validates user entry before add or update.
 */
function entryValidate()
{
	var errMsg = '';
	
	if ($('#frmGender').attr('value') == "") {
		errMsg		= "\t" + '{lang_invalid_gender}';
		$('#frmGender').focus();
	}
	
	if (errMsg == '') {
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n\n" + errMsg);
	
	return false;
}

/**
 * Ajax call to delete a row.
 */
function rowDelete(rKey)
{
	var ans = confirm('{lang_confirm_delete_gender}');
	
	if (ans) {
		showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_deleting_gender}...</div>', 'middle');
		$.ajax({
			type: "POST",
			url: "/genders/delete/" + rKey,
			success: function(msg){
				if (msg == 'SUCCESS') {
					showMessage('{lang_success}', '{lang_gender_deletion_ok}', 'top');
					setTimeout('hideMessage();', 3000);
					$('#row_' + rKey).hide(); // Hide from grid
				} else {
					showMessage('{lang_failure}', '{lang_gender_deletion_error}', 'middle');
				}
			}
		});
	}
	return false;
}

/**
 * Ajax call to get an gender
 */
function rowEdit(rKey, gender)
{
	$('#frmGender').attr('value', gender);
	$('#frmGender').focus();
	
	$('#updateRowId').attr('value', rKey);
	
	$('.updateButtonFamily').show();
	$('#boiteaddmsg').show();
	
	$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_gender_form}" src="/images/btn-delete.gif" />');
	
	frmEntryPaneSw	= false;
	
	return false;
}

/**
 * Initializes message entry form
 */
function resetForm()
{
	$('#updateRowId').attr('value', ''); // hidden field
	$('#frmGender').attr('value', '');
	$('.updateButtonFamily').hide();
}

// eof
