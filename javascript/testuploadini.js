
/**
 * Javascript for INI uploading
 */

function validateEntry()
{
	if ($('#frmUpload').val() == '') {
		alert('{lang_pls_choose_ini_to_upload}');
		return false;
	}
	
	return true;
}

// eof
