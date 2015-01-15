
/**
 * pgroup.js
 */

{isRecurring}

$(document).ready(function(){
	Date.firstDayOfWeek = 7;
	Date.format = 'mm/dd/yyyy';

	$('.date-pick').datePicker({
		startDate:'{nowMmDdYyyy}'
	});
	
	hideOrShow(isRecurring);
	
	$('.radRecurring').click(function(){
		hideOrShow(this.value);
	});
});

function hideOrShow(nYesNo)
{
	if (nYesNo == 1) {
		isRecurring	= true;
		$('#divSelect').show();
	} else { // 0
		isRecurring	= false;
		$('#divSelect').hide();
	}
}

/**
 * Validate user entry before submitting the form
 */
function validateEntry()
{
	var errMsg = '';
	
	if ($('#frmSendDate').val() == "") {
		errMsg		+= "\n\t" + '{lang_invalid_date}';
		$('#frmSendDate').focus().select();
	} else {
		if (isDate($('#frmSendDate').val()) == false) {
			errMsg		+= "\n\t" + '{lang_invalid_date}';
			$('#frmSendDate').focus().select();
		}
	}
	
	if (errMsg == '') {
		if (isRecurring == false) {
			$('#divSelect').empty().remove();
		}
		return true;
	}
	
	alert('{correct_the_following}' + "\n" + errMsg);
	
	return false;
}

// eof
