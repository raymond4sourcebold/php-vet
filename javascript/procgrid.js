
/**
 * procgrid.js
 */

$(document).ready(function(){
	$("#tblSortable").tablesorter({
		cssHeader: 'headSort',
		sortList:[[0,1],[1,0],[2,0]],
		headers: { 
			5: { 
				sorter: false 
			},
			6: { 
				sorter: false 
			} 
		} 
	});
	
	$('#btnCancel').click(function(){
		$('#copyProcId').attr('value', '');
		$('#divProcCopy').hide();
	});
});

function rowDelete(rowId)
{
	var ans = confirm('{lang_confirm_delete_proc}');
	if (ans) {
		showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_wait_deleting_proc}...</div>', 'middle');
		$.ajax({
			type: "POST",
			url: "/procgrid/delete/" + rowId,
			success: function(msg){
				if (msg == 'SUCCESS') {
					showMessage('{lang_success}', '{lang_proc_delete_ok}', 'top');
					setTimeout('hideMessage();', 3000);
					$('#row_' + rowId).hide(); // Hide from grid
				} else {
					showMessage('{lang_failure}', '{lang_proc_delete_error}', 'middle');
				}
			}
		});
	}
	return false;
}

function rowCopy(rowId, name, procMsgId)
{
	$('#copyProcId').val(rowId);
	$('#divProcCopy').show();
	$('#frmProcedureName').val(name);
	$('#procMsgId').val(procMsgId);
	
	return false;
}

// Validation
function entryValidate()
{
	if ($('#frmProcedureName').val() != '') {
		return true;
	}
	
	errMsg		= "\n\t" + '{lang_pls_enter_procname}';
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	$('#frmProcedureName').focus();
	
	return false;
}

// eof
