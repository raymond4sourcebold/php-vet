
/**
 * punless.js
 */

{strModelCriteria}
{nCritInstance}

var limitCriteria	= 5; // 20;

$(document).ready(function(){
	// Add a criterion on button click
	$('#addCriterionBtn').click(function(){
		showOneCritClone();
	});
	
	// Add a criterion on caption click
	$('#addCriterionSpan').click(function(){
		showOneCritClone();
	});
	
	// Hide add button if limit is reached
	if (countCriteria >= limitCriteria) {
		$('#divAddCriterion').hide();
	}
});

function showOneCritClone()
{
	var clone	= strModelCriteria;
	clone		= replaceAll(clone, '~0~', countCriteria);
	$('#divCloneUnlessCrit').append(clone);
	
	/**
	 * The following keypress and focus events for class: numericEntry needs to be executed 
	 *	here since the same lines on common.js document.ready is already executed on load 
	 *	and did not have any effect on future instances of the CSS class.
	 */
	$('.numericEntry').keypress(function(event){
		var allowKeys = Array(
			48, 49, 50, 51, 52, 53, 54, 55, 56, 57,
			35, 36, 37, 39,
			46, 8,
			9
		);
		var keyp = [event.keyCode||event.which];
		
		for (var i in allowKeys) {
			if (allowKeys[i] == keyp) {
				return true;
			}
		}
		event.preventDefault? event.preventDefault() : event.returnValue = false;
	});
	$('.numericEntry').focus(function(){
		this.select();
	});
	
	countCriteria++;
	
	if (countCriteria >= limitCriteria) {
		$('#divAddCriterion').hide();
	}
}

function changeOperator(nClone) {
	$('#nYears_' + nClone).focus();
}

function removeCriteria(nClone) {
	$('#divUnlessCrit_' + nClone).remove();
}

/**
 * Validate user entry before submitting the form
 */
function validateEntry()
{
	var errMsg	= '';
	var aEmpty	= new Array();
	var nEmp	= 0;
	
	/**
	 * validation for criteria lines
	 */
	for (clone = 0; clone < limitCriteria; clone++) {
		dt1	= $('#selRefId1_' + clone).attr('value');
		
		if (typeof dt1 == 'undefined') {
			continue;
		}
		
		dt2	= $('#selRefId2_' + clone).attr('value');
		oper	= $('#frmOperator_' + clone).attr('value');
		
		if (dt1 == '' && dt2 == '' && oper == '') {
			// If all is blank, disregard it.
			$('#divUnlessCrit_' + clone).css('color', '#333333');
			aEmpty[nEmp++]		= clone;
			continue;
			
		} else if (dt1 != '' && dt2 != '' && oper != '') {
			// This one is a complete entry.
			$('#divUnlessCrit_' + clone).css('color', '#333333');
			continue;
			
		} else {
			errMsg		+= "\n\t" + '{lang_invalid_criteria_number} ' + (clone + 1) + '.';
			$('#divUnlessCrit_' + clone).css('color', '#FF0000');
		}
	}
	
	if (errMsg == '') {
		// Entries are ok.  Let's delete empty criteria.
		for (var i in aEmpty) {
			$('#divUnlessCrit_' + aEmpty[i]).remove();
		}
		
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
	return false;
}

// eof
