<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE')	{
	// Compatibility 
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
		'name' => 'tx_rkexportdoc_cm1',
		'path' => t3lib_extMgm::extPath($_EXTKEY).'class.tx_rkexportdoc_cm1.php'
	);
	
	t3lib_extMgm::insertModuleFunction(
        'web_func',        
        'tx_rkexportdoc_modfunc1',
        t3lib_extMgm::extPath($_EXTKEY).'modfunc1/class.tx_rkexportdoc_modfunc1.php',
        'LLL:EXT:rk_exportdoc/locallang.xml:modfunc1_title',
        'wiz'
    );
}
?>