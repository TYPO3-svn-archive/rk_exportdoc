<?php

########################################################################
# Extension Manager/Repository config file for ext "rk_exportdoc".
#
# Auto generated 18-05-2011 11:11
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Export to Word',
	'description' => 'An extension that exports a page (and subpages) to a word document.',
	'category' => 'module',
	'shy' => 0,
	'version' => '2.1.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'cm1',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Benjamin Serfhos',
	'author_email' => 'serfhos@redkiwi.nl',
	'author_company' => 'Redkiwi',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
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
	'_md5_values_when_last_written' => 'a:12:{s:9:"ChangeLog";s:4:"e35a";s:10:"README.txt";s:4:"ee2d";s:28:"class.tx_rkexportdoc_cm1.php";s:4:"33e5";s:12:"ext_icon.gif";s:4:"c872";s:17:"ext_localconf.php";s:4:"c2ad";s:14:"ext_tables.php";s:4:"45a2";s:13:"locallang.xml";s:4:"7423";s:14:"doc/manual.sxw";s:4:"ffb2";s:41:"lib/lib.tx_rkexportdoc_MsDocGenerator.php";s:4:"e69d";s:33:"lib/lib.tx_rkexportdoc_export.php";s:4:"9dcb";s:42:"modfunc1/class.tx_rkexportdoc_modfunc1.php";s:4:"6f87";s:22:"modfunc1/locallang.xml";s:4:"e9f9";}',
	'suggests' => array(
	),
);

?>