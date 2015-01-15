
/**
 * pifcrita.js
 */

{strModelCriteria}
{nValidatorSpecieSw}
{nValidatorGenderSw}
{nCritInstance}

var tmp;
var showOnceQtyOperHelp	= true;
var limitCriteria	= 5; // 20;

$(document).ready(function()
{
        $('#chkAllSpecies').click(function(){
                if ($('#chkAllSpecies').attr('checked')) {
                        $('.clsSpecies').check('on');
			nValidatorSpecieSw	= $('.clsSpecies').size();
                } else {
                        $('.clsSpecies').check('off');
			nValidatorSpecieSw	= 0
                }
        });

        $('#chkAllGenders').click(function(){
                if ($('#chkAllGenders').attr('checked')) {
                        $('.clsGenders').check('on');
			nValidatorGenderSw	= $('.clsGenders').size();
                } else {
                        $('.clsGenders').check('off');
			nValidatorGenderSw	= 0
                }
        });

        $('.clsSpecies').click(function(){
                $('#chkAllSpecies').check('off');
		nValidatorSpecieSw	+= ($(this).attr('checked') ? 1 : -1);
        });

        $('.clsGenders').click(function(){
                $('#chkAllGenders').check('off');
		nValidatorGenderSw	+= ($(this).attr('checked') ? 1 : -1);
        });
	
	// Hide criterion selections
	$('#divCritBool').hide();
	$('#divCritQty').hide();
	$('#divCritBoolQty').hide();

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
	$('#divCloneCritBoolQty').append(clone);
	
	countCriteria++;
	
	if (countCriteria >= limitCriteria) {
		$('#divAddCriterion').hide();
	}
}

function changeCriterionType(nClone) {
	tmp	= $('#criterionType_' + nClone).attr('value');
	
	if (tmp == 'boolean') {
		$('#divCritBool_' + nClone).show();
		$('#divCritQty_' + nClone).hide();
		
	} else if (tmp == 'quantity') {
		$('#divCritBool_' + nClone).hide();
		$('#divCritQty_' + nClone).show();
		
	} else {
		$('#divCritBool_' + nClone).hide();
		$('#divCritQty_' + nClone).hide();
	}
}

function changeOperator(nClone) {
	$('#frmQty_' + nClone).focus();
	$('#frmQty_' + nClone).select();
}

function removeCriteria(nClone) {
	$('#divCritBoolQty_' + nClone).remove();
}

/**
 * Validate user entry before submitting the form
 */
function validateEntry()
{
	var errMsg = '';
	var clone;
	var cri;
	var sel;
	var aEmpty	= new Array();
	var nEmp	= 0;
	
	/**
	 * validation for specie
	 */
	if (nValidatorSpecieSw == 0) {
		errMsg		+= "\n\t" + '{lang_invalid_specie}';
	}
	
	/**
	 * validation for gender
	 */
	if (nValidatorGenderSw == 0) {
		errMsg		+= "\n\t" + '{lang_invalid_gender}';
	}
	
	/**
	 * validation for criteria lines
	 */
	for (clone = 0; clone < limitCriteria; clone++) {
		cri	= $('#criterionType_' + clone).attr('value');
		
		if (typeof cri == 'undefined') {
			// not shown
			$('#divCritBoolQty_' + clone).css('color', '#333333');
			continue;
		}
		
		if (cri == '') {
			aEmpty[nEmp++]		= clone;
			$('#divCritBoolQty_' + clone).css('color', '#333333');
			continue;
		}
		
		if (cri == 'boolean') {
			// boolean
			sel	= $('#selBoolCrit_' + clone).attr('value');
			if (sel == '') {
				errMsg		+= "\n\t" + '{lang_invalid_criteria_number} ' + (clone + 1) + '.';
				$('#divCritBoolQty_' + clone).css('color', '#FF0000');
				continue;
			}
			
			if ($('#frmBoolean_' + clone).attr('value') == '') {
				$('#divCritBoolQty_' + clone).css('color', '#FF0000');
				errMsg		+= "\n\t" + '{lang_invalid_criteria_number} ' + (clone + 1) + '.';
				continue;
			}
			
			// valid
			$('#divCritBoolQty_' + clone).css('color', '#333333');
		} else { 
			// quantity
			sel	= $('#selQtyCrit_' + clone).attr('value');
			if (sel == '') {
				errMsg		+= "\n\t" + '{lang_invalid_criteria_number} ' + (clone + 1) + '.';
				$('#divCritBoolQty_' + clone).css('color', '#FF0000');
				continue;
			}
			if ($('#frmOperator_' + clone).attr('value') == '') {
				errMsg		+= "\n\t" + '{lang_invalid_criteria_number} ' + (clone + 1) + '.';
				$('#divCritBoolQty_' + clone).css('color', '#FF0000');
				continue;
			}
			
			// valid
			$('#divCritBoolQty_' + clone).css('color', '#333333');
		}
	}
	
	if (errMsg == '') {
		// The entries are ok.  Let's remove empty criterion lines now before submitting the page.
		for (var i in aEmpty) {
			$('#divCritBoolQty_' + aEmpty[i]).remove();
		}
		
		return true;
	}
	
	alert('{lang_correct_the_following}' + "\n" + errMsg);
	
	return false;
}

// eof
