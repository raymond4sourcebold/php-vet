<?php
/**
 * th_animals.php
 * This file is included inside of /system/template.php on function show().
 * Variables here are accessible on the template file with the same names.
 *
 * LICENSE: All rights reserved.
 *
 * @copyright  Copyright (c) 2008, ContactMaster, Inc.
 * @license    Proprietary Software
 * @version    2.0.1
 * @link       http://dev.contactmaster.biz
 * @since      File available since Release 2.0.1
 * @package    animals
 * @subpackage template_helper
 */

$selSpecie	= $this->arrayToChoice('selSpecie', $aSpecies, 'select', lang_dropdown_select);
$selBreed	= $this->arrayToChoice('selBreed', array(), 'select', lang_dropdown_empty);
$selGender	= $this->arrayToChoice('selGender', $aGenders, 'select', lang_dropdown_select);


$strBreedsVar	= "\n" . 'var aBreedsVar = Array();';
$holdSpecie	= NULL;
foreach ($aBreeds as $nSpecieId => $aSpecie) {
	foreach ($aSpecie as $nBreedId => $breedName) {
		if ($nSpecieId != $holdSpecie) {
			$holdSpecie	= $nSpecieId;
			$strBreedsVar	.= "\n\t" . 'aBreedsVar[' . $nSpecieId . '] = Array();';
		}
		$strBreedsVar		.= "\n\t\t" . 'aBreedsVar[' . $nSpecieId . '][' . $nBreedId . '] = "' . $breedName . '";';
	}
}

$aJsReplace	= array(
	'aBreedsVar'		=> $strBreedsVar
);

/**
 * Animal Grid
 */
$tbody_animal_grid		= '';

if (empty($aRows) || !$aRows || is_array($aRows) == false) {
	$tbody_animal_grid	.= "<tbody><tr class='dShade'><td>" . lang_empty . "</td></tr></tbody>";
} else {
	$strRows	= '';
	foreach ($aRows as $aAnimal) {
		$isAlive	= ($aAnimal['deathDate'] == '' || is_null($aAnimal['deathDate']) || $aAnimal['deathDate'] == '0000-00-00 00:00:00');
		$rowId		= $aAnimal['animalId'];
		$strRows	.= "
		<tr id='row_{$rowId}' class='f0ShadeHover'" . ($isAlive ? NULL : " style='color:#999999;' ") . ">
			<td>" . $aAnimal['animalName'] . ($isAlive ? NULL : " <i>(" . lang_deceased . ")</i> ") . "</td>
			<td>" . $aAnimal['specieName'] . "</td>
			<td>" . $aAnimal['raceName'] . "</td>
			<td><a href='#' onclick='return rowDelete(\"{$rowId}\");'><img src='/images/supprimer.png' alt='Delete' border='0' /></a></td>
			<td><a href='#' onclick='return rowEdit(\"{$rowId}\", \"{$aAnimal['animalName']}\");'><img src='/images/modiffier.png' alt='Modify' border='0' /></a></td>
		</tr>";
	}
	$tbody_animal_grid	.= '<tbody>' . $strRows . '</tbody>';
}

// eof
