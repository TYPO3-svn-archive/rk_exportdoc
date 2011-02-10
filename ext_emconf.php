<?php

########################################################################
# Extension Manager/Repository config file for ext "rk_exportdoc".
#
# Auto generated 10-02-2011 16:00
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Export to Word',
	'description' => 'An extension that exports a page (and subpages) to a word document.',
	'category' => 'module',
	'author' => 'Benjamin Serfhos',
	'author_email' => 'serfhos@redkiwi.nl',
	'author_company' => 'Redkiwi',
	'shy' => '',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => 'cm1',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.1.0-0.0.0',
			'typo3' => '4.0.0-4.4.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:20:{s:9:"ChangeLog";s:4:"e35a";s:10:"README.txt";s:4:"ee2d";s:28:"class.tx_rkexportdoc_cm1.php";s:4:"a490";s:12:"ext_icon.gif";s:4:"c872";s:17:"ext_localconf.php";s:4:"c2ad";s:14:"ext_tables.php";s:4:"caa8";s:13:"locallang.xml";s:4:"4d2e";s:13:"cm1/clear.gif";s:4:"cc11";s:15:"cm1/cm_icon.gif";s:4:"b115";s:12:"cm1/conf.php";s:4:"e4dd";s:13:"cm1/index.php";s:4:"c9bf";s:17:"cm1/locallang.xml";s:4:"ad09";s:14:"doc/manual.sxw";s:4:"6ac6";s:41:"lib/lib.tx_rkexportdoc_MsDocGenerator.php";s:4:"6af5";s:33:"lib/lib.tx_rkexportdoc_export.php";s:4:"5a97";s:13:"mod1/conf.php";s:4:"fb08";s:14:"mod1/index.php";s:4:"7721";s:18:"mod1/locallang.xml";s:4:"3334";s:22:"mod1/locallang_mod.xml";s:4:"3d4d";s:19:"mod1/moduleicon.gif";s:4:"8074";}',
	'suggests' => array(
	),
);

?>