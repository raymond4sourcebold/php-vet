/**
 * Add onclick action to specie variable buttons
 */

var nBreedInstance	= 0;
var nBreedLimit		= 30;

var modelUpdateBreed	= "<div class='clsDivBreed' id='divBreed_~0~'><label for='breed_~0~'>{lang_breed} ~0~</label>"
	+ "<input style='color: #999999;' type='text' id='frmOldBreed_~0~' name='frmOldBreed[~0~]' size='20' readonly='readonly' />"
	+ "<input style='text-align: center;' type='text' size='7' value='{lang_change_to}' disabled='disabled' />"
	+ "<input type='text' id='frmUpdateBreed_~0~' name='frmUpdateBreed[~0~]' maxlength='35' size='25' onclick='this.select();' />"
	+ "<input type='button' onclick=\"if(confirm('{lang_confirm_delete_breed}')){removeBreed(~0~);}\" value='x' />"
	+ "<br /></div>";

var modelInsertBreed	= "<div class='clsDivBreed' id='divBreed_~0~'><label for='breed_~0~'>{lang_breed} ~0~</label>"
	+ "<input type='text' id='frmInsertBreed_~0~' name='frmInsertBreed[~0~]' maxlength='35' size='25' />"
	+ "<input type='button' onclick='removeBreed(~0~);' value='x' />"
	+ "<br /></div>";

$(document).ready(function() {

	// Add a breed on button click
	$('#addBreedBtn').click(function(){
		showOneBreedClone();
	});
	
	// Add a breed on caption click
	$('#addBreedSpan').click(function(){
		showOneBreedClone();
	});
	
	
	// Padding of entry fields
	$('.divSpecieEntry label').css({textAlign:"right", paddingRight:"10px", display:"block", width:"110px", float:"left", marginTop:"3px"});
	$('.divSpecieEntry input,select').css({display:"block", float:"left", marginBottom:"1px"});
	$('.divSpecieEntry br').css({clear:"left"});
	
	$('#frmEntryPane').click(function() {
		if ($('#boiteaddmsg').is(':visible')) {
			$('#boiteaddmsg').slideUp();
			$('#frmEntryPane').text('{lang_click_new_specie}');
			resetForm();
		} else {
			$('#boiteaddmsg').slideDown();
			$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_new_specie_form}" src="/images/btn-delete.gif" />');
			$('#frmSpecie').focus();
		}
		return false;
	});
	
	$("#tblSortable").tablesorter({
		cssHeader: 'headSort',
		sortList:[[0,0],[1,0]],
		headers: { 
			2: { 
				sorter: false 
			},
			3: { 
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
		$('#frmEntryPane').text('{lang_click_new_specie}');
		$('#boiteaddmsg').slideUp();
		resetForm();
	});
});

// Validation
function entryValidate()
{
	var errMsg = '';
	
	if ($('#frmSpecie').attr('value') == "") {
		errMsg		= "\t" + '{lang_pls_enter_specie}';
		$('#frmSpecie').focus();
	}
	
	if (errMsg == '') {
		// Remove empty breeds
		for (var i = 1; i <= 30; i++) {
			if ($('#frmBreed_' + i).val() == '') {
				removeBreed(i);
			}
		}
		
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n\n" + errMsg);
	
	return false;
}

function rowDelete(rKey)
{
	var ans = confirm('{lang_confirm_delete_specie}');
	
	if (ans) {
		showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_deleting_specie}...</div>', 'middle');
		$.ajax({
			type: "POST",
			url: "/species/delete/" + rKey,
			success: function(msg){
				if (msg == 'SUCCESS') {
					showMessage('{lang_success}', '{lang_specie_deletion_ok}', 'top');
					setTimeout('hideMessage();', 3000);
					$('#row_' + rKey).hide(); // Hide from grid
				} else {
					showMessage('{lang_failure}', '{lang_specie_deletion_error}', 'middle');
				}
			}
		});
	}
	return false;
}

/**
 * Ajax call to get an specie
 */
function rowEdit(rKey, specie, raceCsv)
{
	resetForm();
	
	$('#updateRowId').attr('value', rKey);
	$('#frmSpecie').attr('value', specie);
	
	if (raceCsv != '') {
		// There are existing breeds for this specie
		var aBreeds	= raceCsv.split(',');
		for (var i in aBreeds) {
			showOneBreedClone(aBreeds[i]);
		}
	}
	
	$('.updateButtonFamily').show();
	$('#boiteaddmsg').slideDown();
	
	$('#frmEntryPane').html('<img class="commonImage" title="{lang_hide_new_specie_form}" src="/images/btn-delete.gif" />');
	
	return false;
}

/**
 * Displays one line of breed (race) entry
 */
function showOneBreedClone(updateBreedName)
{
	var clone;
	
	nBreedInstance++;
	
	if (typeof updateBreedName == 'undefined') {
		updateBreedName		= false;
		clone			= replaceAll(modelInsertBreed, '~0~', nBreedInstance);
		
	} else {
		clone			= replaceAll(modelUpdateBreed, '~0~', nBreedInstance);
	}
	
	$('#divCloneBreed').append(clone);
	
	$('.divSpecieEntry label').css({textAlign:"right", paddingRight:"10px", display:"block", width:"110px", float:"left", marginTop:"3px"});
	$('.divSpecieEntry input').css({display:"block", float:"left", marginBottom:"1px"});
	$('.divSpecieEntry br').css({clear:"left"});
	
	if (nBreedInstance >= nBreedLimit) {
		$('#divAddBreed').hide();
	}
	
	if (updateBreedName) {
		// Assign value to edit
		$('#frmOldBreed_' + nBreedInstance).val(updateBreedName);
		$('#frmUpdateBreed_' + nBreedInstance).val(updateBreedName);
		
		$('#frmUpdateBreed_' + nBreedInstance).focus();
	} else {
		$('#frmInsertBreed_' + nBreedInstance).focus();
	}
}

/**
 * Removes one line of breed (race) entry
 */
function removeBreed(nClone) {
	$('#divBreed_' + nClone).remove();
}

/**
 * Initializes message entry form
 */
function resetForm()
{
	$("#form1")[0].reset();
	
	nBreedInstance	= 0;
	$('.clsDivBreed').remove();
	
	$('.updateButtonFamily').hide();
}

// eof
