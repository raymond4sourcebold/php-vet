
/**
 * Javascript for search page.
 */

var gridTemplateType	= '';
//~ var animalTemplate	= '<table style="border: 0px none ; margin: auto; width: 700px; border-spacing: 0px; text-align: center;" id="tblNotSortable">'
	//~ '	<thead><tr class="lightHead">'
	//~ '		<th>{lang_animal_name}</th>'
	//~ '		<th>Owner\'s name</th>'
	//~ '		<th>{animal_town}</th>'
	//~ '		<th>&nbsp;</th>'
	//~ '	</tr></thead>'
	//~ '	<tbody id="gridAnimalTbody"></tbody>'
	//~ '</table>';


$(document).ready(function() {
	// Hide search result grids
	$('#clientGrid').hide();
	$('#animalGrid').hide();
	
	// Search owner on key up and click events
	$('#ownerName').keyup(function(){
		goClientSearch(this.value);
	});
	$('#ownerName').click(function(){
		goClientSearch(this.value);
	});
	
	// Search animal on key up and click events
	$('#animalName').keyup(function(){
		goAnimalSearch(this.value);
	});
	$('#animalName').click(function(){
		goAnimalSearch(this.value);
	});
	
	// Select whole search field on focus events
	$('#ownerName').focus(function(){
		this.select();
	});
	$('#animalName').focus(function(){
		this.select();
	});
	
	// Focus on search owner
	$('#ownerName').focus();
});

/**
 * Ajax search for client matching search string
 */
function goClientSearch(strOwnerSearch)
{
	if (strOwnerSearch.length < 3) {
		$('#nMatchCount').hide();
		return;
	}
	
	$('#animalGrid').hide();
	$('#clientGrid').show();
	
        showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_matching_similar_owner}...</div>', '400');
        $.ajax({
                type: "GET",
                url: "/search/getJsonOwnerSearch/" + strOwnerSearch,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
                async: false,
                success: function(ajaxRetVal){
			window.aMatched	= ajaxRetVal;
                }
        });
	
	// Hide Ajax message
	hideMessage();
	
	// Clear grid
	$('.clsClientGridTr').remove();
	
	$('#nMatchCount').show();
	var nMatching		= 0;
	var holdOwnerId	= '';
	
	isOpenTr		= false;
	
	strGrid			= '';
	
	for (var i in aMatched) {
		if (holdOwnerId != aMatched[i]['clientId']) {
			if (isOpenTr) {
				isOpenTr	= false;
				strGrid	+= "\n" + '			</tbody>'
					+ "\n" + '		</table>'
					+ "\n" + '		<div id="tableaustop650" style="margin-right: 10px;"></div><br>'
					+ "\n" + '	</td>'
					+ "\n" + '</tr>';
			}
			strGrid	+= "\n"+ '<tr class="clsClientGridTr f0ShadeHover">'
					+ "\n" + '	<td>'
					+ "\n" + '		<div style="padding: 14px 0 3px 10px; font-weight: bold;">'
						+ aMatched[i]['lastName'] + ', ' + aMatched[i]['firstName'] + '</div>'
					+ "\n" + '		<div style="padding-left: 10px; font-size: 0.8em;">' + aMatched[i]['addr1'] 
						+ (aMatched[i]['addr2'] ? ', ' + aMatched[i]['addr2'] : '')
						+ (aMatched[i]['city'] ? ', ' + aMatched[i]['city'] : '')
						+ '</div>'
					+ "\n" + '		<div id="tableaustart650" style="margin-right: 10px;"></div>'
					+ "\n" + '		<table style="border: 0px none ; margin: auto 10px auto auto; width: 650px; border-spacing: 0px; text-align: center;">'
			
			$('#nMatchCount').text(++nMatching);
		}
		
		if (aMatched[i]['animalName'] === null) {
			/**
			 * No animal owned
			 */
			if (holdOwnerId != aMatched[i]['clientId']) {
				strGrid	+= "\n" + '			<tbody>';
				
				isOpenTr	= true;
			}
			strGrid	+= "\n\t\t\t" + '<tr><td colspan="2" style="text-align: center; font-style: italic; color: #999999; font-size: 0.8em;">No animal for this client</td></td></tr>';
			
		} else {
			/**
			 * Owner has animal
			 */
			if (holdOwnerId != aMatched[i]['clientId']) {
				strGrid	+= "\n" + '			<thead><tr class="lightHead"><th>{lang_animal_name}</th><th>&nbsp;</th></tr></thead>'
					+ "\n" + '			<tbody>';
				
				isOpenTr	= true;
			}
			
			strGrid	+= "\n\t\t\t" + '<tr class="f0ShadeHover"><td width="95%">' + aMatched[i]['animalName'] + '</td><td><a href="/followup/manage/' + aMatched[i]['animalId'] + '"><img src="/images/modiffier.png" alt="Follow-up creation" border="0"></a></td></tr>';
		}
		
		holdOwnerId	= aMatched[i]['clientId'];
	}
	
	if (isOpenTr) {
		strGrid	+= "\n" + '					</tbody>'
					+ "\n" + '		</table>'
					+ "\n" + '		<div class="clsRemove" id="tableaustop650" style="margin-right: 10px;"></div><br>'
					+ "\n" + '	</td>'
					+ "\n" + '</tr>';
	}
	
	$('#gridClientTbody').append(strGrid);
	
	$('#nMatchCount').text(nMatching);
}

/**
 * Ajax search for animal matching search string
 */
function goAnimalSearch(strAnimalSearch)
{
	if (strAnimalSearch.length < 3) {
		$('#nMatchCount').hide();
		return;
	}
	
	$('#clientGrid').hide();
	$('#animalGrid').show();
	
        showMessage('{lang_processing}...', '<div style="line-height:25px"><img style="vertical-align:middle; padding-right: 10px;" border="0" src="/images/loading.gif" />{lang_matching_similar_animal}...</div>', '400');
        $.ajax({
                type: "GET",
                url: "/search/getJsonAnimalSearch/" + strAnimalSearch,
		contentType: "application/json; charset=utf-8",
		dataType: "json",
                async: false,
                success: function(ajaxRetVal){
			window.aMatched	= ajaxRetVal;
                }
        });
	
	// Hide Ajax message
	hideMessage();
	
	// Clear grid
	$('.clsAnimalGridTr').remove();
	
	$('#nMatchCount').show();
	var nMatching	= 0;
	
	for (var i in aMatched) {
		$('#gridAnimalTbody').append('<tr class="f0ShadeHover clsAnimalGridTr">'
			+ '<td>' + aMatched[i]['animalName'] + '</td>'
			+ '<td>' + aMatched[i]['lastName'] + ', ' + aMatched[i]['firstName'] + '</td>'
			+ '<td>' + aMatched[i]['city'] + '</td>'
			+ '<td><a href="/followup/manage/' + aMatched[i]['animalId'] + '"><img src="/images/modiffier.png" alt="Follow-up creation" border="0"></a></td>'
			+ '</tr>'
		);
		
		$('#nMatchCount').text(++nMatching);
	}
	
	$('#nMatchCount').text(nMatching);
}

/**
 * Displays follow up creation form and follow grid
 */
function followUp(animalId, clientId)
{
	alert(animalId + ' : '+ clientId);
	return false;
}

// eof
