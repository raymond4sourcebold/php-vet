/**
 * Common JavaScript
 */

$(document).ready(function() {
	$(".autoHidePane .autoHidePaneDelete").click(function(){
		$(this).parents(".autoHidePane").animate({ opacity: "hide" }, "fast");
	});
	
	// Show auto hide message if it contains something
	if (($('#autoHideMessage').text() != '')) {
		$('.autoHidePane').css('display', 'inline');
		// Set hiding if autoHide contains something
		setTimeout('$(".autoHidePane").animate({ opacity: "hide" }, "slow"); ', 3000);
	}

	// Numeric entry
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
	
	$('.telephone').attr('maxlength', 14).attr('size', 14);
	$('.telephone').keypress(function(event){
		return telephoneKeyPress(event, this);
	});
	
	// Set onclick action of info which display on hover of "?"
	$('.info').click(function(){
		return false;
	});
	// Set href value to '#'
	$('.info').attr('href', '#');
	
	tooltip();
	
	// Call autohide message if there's a message on session
	if (autoHideTitle) {
		showMessage(autoHideTitle, autoHideMessage, 0, true, true);
	}
});

/**
 * Processes class telephone keypress event
 */
function telephoneKeyPress(event, element)
{
	var keyp = [event.keyCode||event.which];
	
	//var strFormat = '(###) ###-####';
	var strFormat   = '0#-##-##-##-##';
	
	var ctrlKeys = Array(
		35, 36, 37, 39,
		46, 8,
		9
	);
	
	for (var i in ctrlKeys) {
		if (ctrlKeys[i] == keyp) {
			return true;
		}
	}
	
	var allowKeys = Array(
		48, 49, 50, 51, 52, 53, 54, 55, 56, 57, // numeric
		40, 41, 45, 32 // (, ), -, space
	);
	
	var invalidKey	= true;
	
	for (var i in allowKeys) {
		if (allowKeys[i] == keyp) {
			invalidKey	= false;
			break;
		}
	}
	
	if (invalidKey) {
		return false;
	}
	
	for (i = 0; i < 14; i++) {
		if (strFormat.charAt(i) == "#") {
			// The current character must be a digit.
			
			if(element.value.length > i ) {
				if(isNaN(element.value.charAt(i)) || element.value.charAt(i) == " ") { 
					// if its not a number, it's erased and the loop is set back one
					element.value = element.value.substring(0,i) + element.value.substring(i + 1,element.value.length)
					i--;
				}
			}
			
		} else if (strFormat.charAt(i) == "a") { 
			// The current character must be a letter (case insensitive).
			
			if(element.value.length > i ) {
				// if its not a letter, it's erased
				if(element.value.charAt(i).toUpperCase() < "A" || element.value.charAt(i).toUpperCase() > "Z" ) {
					element.value = element.value.substring(0,i) + element.value.substring(i + 1,element.value.length)
					i--;
				}
			}
			
		} else if (strFormat.charAt(i) == "A") { 
			// The current character must be an uppercase letter.
			
			if(element.value.length > i ) {
				// if its not a letter, it's removed
				if(element.value.charAt(i).toUpperCase() < "A" || element.value.charAt(i).toUpperCase() > "Z" ) {
					element.value = element.value.substring(0,i) + element.value.substring(i + 1,element.value.length)
					i--;
				} else { 
					// otherwise, it is set to uppercase
					element.value = element.value.substring(0,i) + element.value.charAt(i).toUpperCase() + element.value.substring(i + 1,element.value.length)
				}
			}
			
		} else {
			// The current character must be the same as the one in the format string.
			
			if(element.value.length > i ) {
				// if it isn't already the correct character, insert the character
				if(element.value.charAt(i) != strFormat.charAt(i)) {
					element.value = element.value.substring(0,i) + strFormat.charAt(i) + element.value.substring(i,element.value.length)
				}
			}
		}
	}
}

/**
 * Show the message
 */
function showMessage(title, message, pos, withCloseControl, manageAutoHide)
{
	if (pos == 'middle') {
		$('.autoHidePane').css('top', '200px');
		$('.autoHidePaneDelete').css('display', 'none');
		
	} else if (pos == 'top') {
		$('.autoHidePane').css('top', '0px');
		$('.autoHidePaneDelete').css('display', 'inline').css('top', '10px');
		
	} else {
		$('.autoHidePane').css('top', pos + 'px');
		if (typeof withCloseControl == 'undefined') {
			$('.autoHidePaneDelete').hide();
		} else {
			$('.autoHidePaneDelete').show();
		}
	}
	
	$('#autoHideTitle').html(title);
	$('#autoHideMessage').html(message);
	
	if (title == 'Failure' || title == 'Echec') {
		$('.autoHidePane').css({
			display:"inline",
			backgroundColor:"#FFC4C4",
			border:"1px solid #FF6363",
			borderTop:"solid 2px #FF6363"
		});
		$('.autoHidePaneDelete').show();
	} else {
		$('.autoHidePane').css({
			display:"inline",
			backgroundColor:"#edf5e1",
			border:"1px solid #c4df9b",
			borderTop:"solid 2px #c4df9b"
		});
		
		if (manageAutoHide == true) {
			setTimeout('hideMessage("slow");', 3000);
		}
	}

	return false;
}

/**
 * Hide the message
 */
function hideMessage(strSpeed)
{
	if (typeof strSpeed == "undefined") {
		$(".autoHidePane").hide();
	} else {
		$(".autoHidePane").animate({ opacity: "hide" }, strSpeed);
	}
	return false;
}

/**
 * Use this function to toggle the attribute: checked
 * Sample calls:
	$("input[@type='checkbox']").check();
	$("input[@type='checkbox']").check('on');
	$("input[@type='checkbox']").check('off');
	$("input[@type='checkbox']").check('toggle');
 */
$.fn.check = function(mode) {
	var mode = mode || 'on'; // if mode is undefined, use 'on' as default
	return this.each(function() {
		switch(mode) {
		case 'on':
			this.checked = true;
			break;
		case 'off':
			this.checked = false;
			break;
		case 'toggle':
			this.checked = !this.checked;
			break;
		}
	});
};

/**
 * This function will replace instances of searchTerm with replaceWith in str.  Pass ignoreCase if needed.
 */
function replaceAll( str, searchTerm, replaceWith, ignoreCase )
{
	var regex = "/"+searchTerm+"/g";
	if( ignoreCase ) regex += "i";
	return str.replace( eval(regex), replaceWith );
}

/**
 * Tooltip script 
 * powered by jQuery (http://www.jquery.com)
 * written by Alen Grakalic (http://cssglobe.com)
 * for more info visit http://cssglobe.com/post/1695/easiest-tooltip-and-image-preview-using-jquery
 */
this.tooltip = function(){	
	/* CONFIG */		
	xOffset = -15;
	yOffset = 14;		
	// these 2 variable determine popup's distance from the cursor
	// you might want to adjust to get the right result		
	/* END CONFIG */
	
	$("a.tooltip").hover(function(e){											  
		this.t = this.title;
		this.title = "";									  
		$("body").append("<p id='tooltip'>"+ this.t +"</p>");
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");		
	},
	function(){
		this.title = this.t;		
		$("#tooltip").remove();
	});	
	$("a.tooltip").mousemove(function(e){
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});
};

/**
 * Check for valid numeric strings
 */
function isNumeric(strString)
{
	var strValidChars = "0123456789";
	var strChar;
	var blnResult = true;

	if (strString.length == 0) return false;

	//  test strString consists of valid characters listed above
	for (i = 0; i < strString.length && blnResult == true; i++) {
		strChar = strString.charAt(i);
		if (strValidChars.indexOf(strChar) == -1) {
			blnResult = false;
		}
	}
	return blnResult;
}

/**
 * This will format 0123456789 to 01-23-45-67-89
 */
function intToPhoneFormat(intPhone) {
	if (isNumeric(intPhone) == false) {
		// If not entirely numeric, don't alter it.
		return intPhone;
	}
	
	if (intPhone.length > 10) {
		// Too long, don't alter it.
		return intPhone;
		
	} else if (intPhone.length < 9) {
		// Too short, don't alter it.
		return intPhone;
		
	} else if (intPhone.length == 9) {
		// Add '0' as phone prefix.
		intPhone	= '0' + intPhone;
	}
	
	return intPhone.substring(0, 2) 
		+ '-' + intPhone.substring(2, 4) 
		+ '-' + intPhone.substring(4, 6) 
		+ '-' + intPhone.substring(6, 8) 
		+ '-' + intPhone.substring(8, 10);
}

// eof
