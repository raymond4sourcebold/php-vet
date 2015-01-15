/**
 * login.js
 */

$(document).ready(function(){
	$('label').css({marginTop:"3px", marginBottom:"5px"});
	
	$('#login').focus();
});

/**
 * This function requires entry of username and password on the login form.
 */
function loginValidate()
{
	var errcode = 0;
	
	if ($('#login').attr('value') == "") {
		errcode = 10;
	}
	
	if ($('#password').attr('value') == "") {
		errcode++;
	}
	
	if (errcode == 0) {
		return true;
	}
	
	if (errcode == 10) {
		alert('{lang_enter_user}');
		$('#login').focus();
	} else if (errcode == 11) {
		alert('{lang_enter_user_password}');
		$('#login').focus();
	} else {
		alert('{lang_enter_password}');
		$('#password').focus();
	}
	
	return false;
}

// eof
