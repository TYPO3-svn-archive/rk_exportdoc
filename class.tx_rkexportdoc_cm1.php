<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Benjamin Serfhos <serfhos@redkiwi.nl>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */




/**
 * Addition of an item to the clickmenu
 *
 * @author	Benjamin Serfhos <serfhos@redkiwi.nl>
 * @package	TYPO3
 * @subpackage	tx_rkexportdoc
 */
class tx_rkexportdoc_cm1 {
	function main(&$backRef,$menuItems,$table,$uid)	{
		global $BE_USER,$TCA,$LANG;
		
		$localItems = Array();
		if (!$backRef->cmLevel)	{
			
				// Returns directly, because the clicked item was not from the pages table 
			if ($table!="pages")	return $menuItems;
			
				// Adds the regular item:
			$LL = $this->includeLL();
			
				// Repeat this (below) for as many items you want to add!
				// Remember to add entries in the localconf.php file for additional titles.
			$url = t3lib_extMgm::extRelPath('rk_exportdoc').'cm1/index.php?id='.$uid;
			$localItems[] = $backRef->linkItem(
				$GLOBALS['LANG']->makeEntities(($LANG->getLLL("cm1_title",$LL)),
				$backRef->excludeIcon('<img src="'.t3lib_extMgm::extRelPath("rk_exportdoc").'cm1/cm_icon.gif" width="15" height="12" border="0" align="top" />'),
				$backRef->urlRefForCM($url),
				1	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
			);

			// Find delete element among the input menu items and insert the local items just before that:
			$c=0;
			$deleteFound = FALSE;
			foreach ($menuItems as $k => $value) {
				$c++;
				if (!strcmp($k,'delete'))	{
					$deleteFound = TRUE;
					break;
				}
			}

			if ($deleteFound)	{
					// .. subtract two... (delete item + its spacer element...)
				$c-=2;
					// and insert the items just before the delete element.
				array_splice(
					$menuItems,
					$c,
					0,
					$localItems
				);
			} else {	// If no delete item was found, then just merge in the items:
				$menuItems=array_merge($menuItems,$localItems);
			}
		}
		return $menuItems;
	}
	
	/**
	 * Reads the [extDir]/locallang.xml and returns the \$LOCAL_LANG array found in that file.
	 *
	 * @return	[type]		...
	 */
	function includeLL()	{
		global $LANG;

		$LOCAL_LANG = $LANG->includeLLFile('EXT:rk_exportdoc/locallang.php',FALSE);
		return $LOCAL_LANG;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rk_exportdoc/class.tx_rkexportdoc_cm1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rk_exportdoc/class.tx_rkexportdoc_cm1.php']);
}

?>