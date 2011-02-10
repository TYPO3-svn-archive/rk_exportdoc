<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
/*
 * The extension cmaction needs to be installed for this configuration..
 *
$GLOBALS['TYPO3_CONF_VARS']['BE']['defaultUserTSconfig'] .= '
	options.contextMenu {
		table.pages.items {
			740 = ITEM
			740 {
				name = rk_exportdoc
				label = LLL:EXT:rk_exportdoc/locallang.xml:cm1_title
				icon = ' . t3lib_div::locationHeaderUrl(t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif') . '
				spriteIcon =
				displayCondition =
				callbackAction = openCustomUrlInContentFrame
				customAttributes.contentUrl = ' . t3lib_extMgm::extRelPath('rk_exportdoc').'cm1/index.php?id=###ID###
			}
		}
	}
';
*/
?>