
/**
 * Add onclick action to criterion variable buttons
 */

var strCriteriaType	= '';

$(document).ready(function() {

	$('#frmEntryPane').click(function() {
		if ($('#boiteaddmsg').is(':visible')) {
			$('#boiteaddmsg').slideUp();
			$('#frmEntryPane').text('{lang_click_new_criteria}');
			resetForm();
		} else {
			$('#boiteaddmsg').slideDown();
			$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_new_criterion_form}" src="/images/btn-delete.gif" />');
			$('#frmCaption').focus();
			
			$('input.frmCriterionType[@value=Event]').check('on');
		}
		return false;
	});
	
	$('.frmCriterionType').click(function(){
		$('#frmCaption').focus();
	});
	
	// Add submit button clears hidden: updateRowId so that it's understood to be an Add and not Update.
	$('#addRowBtn').click(function() {
		$('#updateRowId').attr('value', '')
	});
	
	// Cancel update button
	$('#updateRowCancelBtn').click(function() {
		$('#frmEntryPane').text('{lang_click_new_criteria}');
		$('#boiteaddmsg').hide();
		resetForm();
	});
	
	// Hide caption2
	$('#divCaption2').hide();

	// Define sort options
	$("#tblSortable").tablesorter({
		cssHeader: 'headSort',
		sortList:[[0,0],[1,0]],
		headers: { 4: { sorter: false },5: { sorter: false } } 
	});	
});

/**
 * Validates user entry before add or update.
 */
function entryValidate()
{
	var errMsg = '';
	
	if ($('#frmCaption').val() == "") {
		errMsg		= "\n\t" + '{lang_pls_enter_caption}';
		$('#frmCaption').focus();
	}
	
	if (errMsg == '') {
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
	return false;
}

/**
 * Ajax call to delete a row.
 */
function rowDelete(rKey)
{
	var ans = confirm('{lang_confirm_delete_criterion}');
	
	if (ans) {
		showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_deleting_criterion}...</div>', 'middle');
		$.ajax({
			type: "POST",
			url: "/criteria/delete/" + rKey,
			success: function(msg){
				if (msg == 'SUCCESS') {
					showMessage('{lang_success}', '{lang_criterion_deletion_ok}', 'top');
					setTimeout('hideMessage();', 3000);
					$('#row_' + rKey).hide(); // Hide from grid
				} else {
					showMessage('{lang_failure}', '{lang_criterion_deletion_error}', '250', true);
				}
			}
		});
	}
	return false;
}

/**
 * Ajax call to get an criterion
 */
function rowEdit(rKey, criterionType, criterionCaptionValue, criterionCaptionValue2)
{
	$('#updateRowId').val(rKey);

	$('#frmCaption').val(criterionCaptionValue);
	
	$('input.frmCriterionType[@value=' + criterionType + ']').check('on');
	
	$('#frmCaption').removeAttr('disabled');
	$('#frmCaption').focus();
	
	$('.updateButtonFamily').show();
	$('#boiteaddmsg').show();
	
	$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_new_criterion_form}" src="/images/btn-delete.gif" />');
	
	// Disable enter key which submits the page for Add action
	$(document).keypress(function(){
		var keyp = [event.keyCode||event.which];
		if (keyp == 13) {
			event.preventDefault? event.preventDefault() : event.returnValue = false;
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
	
	$('.updateButtonFamily').hide();
	
	// Restore enter key's submit functionality
	$(document).unbind('keypress');
}

// eof
