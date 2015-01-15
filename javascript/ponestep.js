
/**
 * ponestep.js
 */

{isRecurring}

$(document).ready(function(){
	$('#selOffset').change(function(){
		var str = $('#selOffset').attr('value');
		
		if (str == '0:d') {
			$('#selAnticipation').attr('value', '1:d');
		} else if (str == '1:w') {
			$('#selAnticipation').attr('value', '2:d');
		} else if (str == '2:w') {
			$('#selAnticipation').attr('value', '2:d');
		} else if (str == '3:w') {
			$('#selAnticipation').attr('value', '2:d');
		} else if (str == '1:m') {
			$('#selAnticipation').attr('value', '1:w');
		} else if (str == '2:m') {
			$('#selAnticipation').attr('value', '1:w');
		} else if (str == '3:m') {
			$('#selAnticipation').attr('value', '2:w');
		} else if (str == '6:m') {
			$('#selAnticipation').attr('value', '2:w');
		} else if (str == '1:y') {
			$('#selAnticipation').attr('value', '1:m');
		}
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
	if ($('#selReferenceDateId').attr('value') != '0') {
		return true;
	}
	
	var errMsg = "\n\t" + '{lang_pls_select_refdate}';
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
	return false;
}

// eof
