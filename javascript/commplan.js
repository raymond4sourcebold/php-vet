
/**
 * Javascript for CommPlan
 */

// Validation
function entryValidate()
{
	var errMsg = '';
	
	if ($('#frmNoOfDays').val() == "") {
		errMsg		= "\t" + '{lang_invalid_quantity_client_quota}';
		$('#frmNoOfDays').focus();
	}
	
	if (errMsg == '') {
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n\n" + errMsg);
	
	return false;
}

// eof
