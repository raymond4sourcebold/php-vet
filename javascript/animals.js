
/**
 * Javascript for animal page
 */

{aBreedsVar}

$(document).ready(function() {
	
	$('#selSpecie').change(function(){
		setBreedOptions(this.value);
	});
	
	$('#frmEntryPane').click(function() {
		if ($('#boiteaddmsg').is(':visible')) {
			$('#boiteaddmsg').slideUp();
			$('#frmEntryPane').text('{lang_click_new_animal}');
			resetForm();
		} else {
			$('#boiteaddmsg').slideDown();
			$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_new_animal_form}" src="/images/btn-delete.gif" />');
			$('#frmAnimal').focus();
		}
		return false;
	});
	
	// Padding of entry fields
	$('.divAnimalEntry label').css({textAlign:"right", paddingRight:"10px", display:"block", width:"110px", float:"left", marginTop:"3px"});
	$('.divAnimalEntry input,select').css({display:"block", float:"left", marginBottom:"1px"});
	$('.divAnimalEntry br').css({clear:"left"});
	
	// Padding of entry fields on the right
	$('.divAnimalEntryR label').css({textAlign:"right", paddingRight:"10px", display:"block", width:"80px", float:"left", marginTop:"3px"});
	$('.divAnimalEntryR input[type=text]').css({display:"block", float:"left", marginBottom:"1px"});
	
	$('.divAnimalEntryR input[type=checkbox]').css({textAlign:"left", float:"left", display:"block", margin:"1px 0px 0px 65px"});
	$('.lblChkR').css({display:"block", textAlign:"left", float:"left", width:"210px", margin:"2px 0px 0px 0px"});
	
	$('.divAnimalEntryR br').css({clear:"left"});
	
	
	Date.firstDayOfWeek = 7;
	Date.format = 'mm/dd/yyyy';

	$('.date-pick').datePicker({
		startDate:'01/01/1990'
	});
	
	// Default N/A for animal death date
	$('#frmDeathDate').focus(function(){
		if (this.value == 'n/a') {
			this.value	= '';
		}
	});
	$('#frmDeathDate').blur(function(){
		if (this.value == '') {
			this.value	= 'n/a';
		}
	});

	
	// Show auto hide message if it contains something
	if (($('#autoHideMessage').text() != '')) {
		$('.autoHidePane').css('display', 'inline');
	}
	
	// Add submit button clears hidden: updateRowId so that it's understood to be an Add and not Update.
	$('#addRowBtn').click(function() {
		$('#updateRowId').val('')
	});
	
	// Cancel update button
	$('#updateRowCancelBtn').click(function() {
		$('#frmEntryPane').text('{lang_click_new_animal}');
		$('#boiteaddmsg').hide();
		resetForm();
	});
	
	$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_new_animal_form}" src="/images/btn-delete.gif" />');
	
	$("#tblSortable").tablesorter({
		cssHeader: 'headSort',
		sortList:[[0,0]],
		headers: {
			3: { 
				sorter: false 
			},
			4: { 
				sorter: false 
			} 
		} 
	});
});

function setBreedOptions(specieId)
{
	var strOptions		= '';
	var isEmpty		= true;
	
	for (var i in aBreedsVar[specieId]) {
		isEmpty		= false;
		strOptions	+= "<option value='" + i + "'>" + aBreedsVar[specieId][i] + "</option>";
	}
	
	if (isEmpty) {
		$('#selBreed').html('<option value="">{lang_dropdown_empty}</option>');
	} else {
		$('#selBreed').html('<option value="">{lang_dropdown_select}</option>' + strOptions);
	}
}

// Validation
function entryValidate()
{
	var errMsg = '';
	
	if ($('#frmDeathDate').val() != 'n/a') {
		if (isDate($('#frmDeathDate').val()) == false) {
			errMsg		= "\n\t" + '{lang_pls_enter_valid_deathdate}' + errMsg;
		}
	}
	
	if ($('#frmBirthDate').val() != "") {
		if (isDate($('#frmBirthDate').val()) == false) {
			errMsg		= "\n\t" + '{lang_pls_enter_valid_birthdate}' + errMsg;
		}
	}
	
	if ($('#selSpecie').val() == 0) {
		errMsg		= "\n\t" + '{lang_pls_select_specie}' + errMsg;
		$('#selSpecie').focus();
	}
	
	if ($('#selGender').val() == 0) {
		errMsg		= "\n\t" + '{lang_pls_select_animal_gender}' + errMsg;
		$('#selGender').focus();
	}
	
	if ($('#frmAnimal').val() == "") {
		errMsg		= "\n\t" + '{lang_pls_enter_animal_name}' + errMsg;
		$('#frmAnimal').focus();
	}
	
	if (errMsg == '') {
		if ($('#frmDeathDate').val() == 'n/a') {
			$('#frmDeathDate').val('');
		}
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
	return false;
}

function rowDelete(rKey)
{
	var ans = confirm('{lang_confirm_delete_animal}');
	
	if (ans) {
		showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_deleting_animal}...</div>', 'middle');
		$.ajax({
			type: "POST",
			url: "/animals/delete/" + rKey,
			success: function(msg){
				if (msg == 'SUCCESS') {
					showMessage('{lang_success}', '{lang_animal_deletion_ok}', 'top');
					setTimeout('hideMessage();', 3000);
					$('#row_' + rKey).hide(); // Hide from grid
				} else {
					showMessage('{lang_failure}', '{lang_animal_deletion_error}', 'middle');
				}
			}
		});
	}
	return false;
}

/**
 * Ajax call to get an animal
 */
function rowEdit(rKey, paramAnimalName)
{
	// Reset entry form first.
	resetForm();
	
	$('#updateRowId').val(rKey);
	
	nPhoneInstance	= 0;
	
	$('#boiteaddmsg').slideDown();
	$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_new_animal_form}" src="/images/btn-delete.gif" />');
	
	// Ajax call to get client data
        showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_reading_animal}...</div>', '400');
        $.ajax({
                type: "GET",
                url: "/animals/getJsonAnimal/" + rKey,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
                async: false,
                success: function(ajaxRetVal){
			window.aAnimal	= ajaxRetVal;
                }
        });

	//~ for(var i in aAnimal) {
		//~ // Loop to decode Db values
//~ var chars = new Array ('&','à','á','â','ã','ä','å','æ','ç','è','é',
	 //~ 'ê','ë','ì','í','î','ï','ð','ñ','ò','ó','ô',
	 //~ 'õ','ö','ø','ù','ú','û','ü','ý','þ','ÿ','À',
	 //~ 'Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë',
	 //~ 'Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö',
	 //~ 'Ø','Ù','Ú','Û','Ü','Ý','Þ','€','\"','ß','<',
	 //~ '>','¢','£','¤','¥','¦','§','¨','©','ª','«',
	 //~ '¬','*','®','¯','°','±','²','³','´','µ','¶',
	 //~ '·','¸','¹','º','»','¼','½','¾');

//~ var entities = new Array ('&amp;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;',
	//~ 'aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;',
	//~ 'iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;',
	//~ 'ocirc;','&otilde;','&ouml;','&oslash;','&ugrave;','&uacute;','&ucirc;',
	//~ 'uuml;','&yacute;','&thorn;','&yuml;','&Agrave;','&Aacute;','&Acirc;',
	//~ 'Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;',
	//~ 'Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;',
	//~ 'Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&Oslash;','&Ugrave;',
	//~ 'Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&euro;','&quot;','&szlig;',
	//~ 'lt;','&gt;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;',
	//~ 'copy;','&ordf;','&laquo;','&not;','&shy;','&reg;','&macr;','&deg;','&plusmn;',
	//~ 'sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;',
	//~ 'ordm;','&raquo;','&frac14;','&frac12;','&frac34;');
//~ var from = chars;	
//~ var to = entities;	
//~ for (var j = 0; j < from.length; j++) {
	//~ myRegExp = new RegExp();
	//~ myRegExp.compile(to[j],'g')
	//~ aAnimal[i] = aAnimal[i].replace (myRegExp,from[j]);
//~ }
		//~ aAnimal[i]	= texte;
		//aAnimal[i] = aAnimal[i].replace(/&#(\d+);/g,
		
	//~ }
	
	// Assign values from DB
	$('#frmAnimal').val(paramAnimalName);
	$('#frmExternalId').val(aAnimal['animalExternalId']);
	$('#selGender').val(aAnimal['genderId']);
	$('#selSpecie').val(aAnimal['specieId']);
	
	setBreedOptions(aAnimal['specieId']);
	$('#selBreed').val(aAnimal['raceId']);
	
	if (aAnimal['birthDate']) {
		// Get the date stripping the time.
		aAnimal['birthDate']	= aAnimal['birthDate'].substring(0, 10);
		
		if (aAnimal['birthDate'] == '0000-00-00') {
			$('#frmBirthDate').val('');
		} else {
			var aBirthDate = aAnimal['birthDate'].split('-');
			$('#frmBirthDate').val(aBirthDate[1] + '/' + aBirthDate[2] + '/' + aBirthDate[0]);
			$('#frmBirthDate').dpSetSelected($('#frmBirthDate').val());
		}
	}
	
	if (aAnimal['deathDate']) {
		// Get the date stripping the time.
		aAnimal['deathDate']	= aAnimal['deathDate'].substring(0, 10);
		
		if (aAnimal['deathDate'] == '0000-00-00') {
			$('#frmDeathDate').val('n/a');
		} else {
			var aDeathDate = aAnimal['deathDate'].split('-');
			$('#frmDeathDate').val(aDeathDate[1] + '/' + aDeathDate[2] + '/' + aDeathDate[0]);
			$('#frmDeathDate').dpSetSelected($('#frmDeathDate').val());
		}
	}
	
	$('#chkIdentified').check(aAnimal['identifiedBoolean'] == 0 ? 'off' : 'on');
	$('#chkActive').check(aAnimal['activeBoolean'] == 0 ? 'off' : 'on');
	$('#chkVaccinated').check(aAnimal['vaccinatedBoolean'] == 0 ? 'off' : 'on');
	$('#chkInsured').check(aAnimal['insuredBoolean'] == 0 ? 'off' : 'on');
	
	// Hide Ajax message
	hideMessage();
	
	$('.updateButtonFamily').show();
	$('#frmAnimal').focus();
	
	return false;
}

/**
 * Initializes message entry form
 */
function resetForm()
{
	$("#form1")[0].reset();
	$('.updateButtonFamily').hide();
}

// eof
