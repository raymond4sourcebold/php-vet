
/**
 * pseltype.js
 */

{radIsSelected}

$(document).ready(function(){
	$('.radProcType').click(function(){
		radIsSelected		= true;
	});
});

/**
 * Validate user entry before submitting the form
 */
function validateEntry()
{
	if (radIsSelected) {
		return true;
	}
	
	var errMsg = "\n\t" + '{lang_pls_select_proctype}';
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
	return false;
}

// eof
