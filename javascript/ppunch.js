
/**
 * ppunch.js
 */

function fcnConfirm()
{
	if ($('#isActiveProc').attr('checked') == true) {	
		var ans = confirm('{lang_confirm_are_you_sure}');
		
		if (!ans) {
			return false;
		}
	}
}

// eof
