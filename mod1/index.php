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

require_once(t3lib_extMgm::extPath('rk_exportdoc').'lib/lib.tx_rkexportdoc_export.php'); // load export class

$LANG->includeLLFile('EXT:rk_exportdoc/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Export Document' for the 'rk_exportdoc' extension.
 *
 * @author	Benjamin Serfhos <serfhos@redkiwi.nl>
 * @package	TYPO3
 * @subpackage	tx_rkexportdoc
 */
class  tx_rkexportdoc_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $o_treeview;
	var $o_doc;
	var $a_treeIds = array();
	var $a_menu = array();
	var $a_rteConf = array();
	
	/* Default GET variables */
	var $a_variables = array (
		'language' => 0,
		'depth' => 999,
	);
	
	
	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
		$allowTags = explode(',', 'b,i,u,a,br,div,center,pre,font,hr,sub,sup,p,strong,em,li,ul,ol,blockquote,strike'); // rte doesnt allow images
		foreach($allowTags as $allowedTag) { 
			$this->a_rteConf['keepTags'][$allowedTag] = array (
				'allowedAttribs' => 'id, class'
			);
			if($allowedTag == 'a') {
				$this->a_rteConf['keepTags'][$allowedTag] = array (
				'allowedAttribs' => 'id, class, href'
			);
			}
		}
		
		$this->renderSelectsMenu();
		parent::init();

		/*
		if (t3lib_div::_GP('clear_all_cache'))	{
			$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
		}
		*/
	}
	
	function renderSelectsMenu() {
		$this->a_menu = array (
			'language' => array (
				0 => 'Default',
				1 => 'Arabic'
			),
			'depth' => array (
				0 => '0',
				1 => '1',
				2 => '2',
				3 => '3',
				4 => '4',
				999 => 'infinite',
			)
		);
		
		
		if(isset($_GET['VAR']) && !empty($_GET['VAR'])) {
			foreach($_GET['VAR'] as $key => $selected) {
				$this->a_variables[$key] = $selected;
			}
		}
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		$this->o_treeview = t3lib_div::makeInstance('t3lib_pageTree');
		$this->o_treeview->init('AND no_search = 0 AND hidden != 1 AND deleted != 1 AND doktype NOT IN (199, 254, 255, 5)' . $s_addWhere); 
		$this->localHTMLParser = t3lib_div::makeInstance('t3lib_parsehtml_proc');
		$this->o_doc = new rk_exportdoc_export();
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		
		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->bodyTagId = 'typo3-rkexportdoc-mod-php';
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="post" enctype="multipart/form-data">';

				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';
		
			$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);
			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$menu = '';
			if(!empty($this->a_menu)) {
				foreach($this->a_menu as $menuselect => $items) {
					// get the current parameters
					$var = array (
						'id' => $this->id,
						'VAR' => $this->a_variables
					);
					unset($var['VAR'][$menuselect]);
					$menu .= t3lib_BEfunc::getFuncMenu($var, 'VAR['.$menuselect.']',$this->a_variables[$menuselect],$items);
				}
			}
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,$menu));
			$this->content.=$this->doc->divider(5);


			// Render content:
			if(intval($this->id) > 0) {
				$this->renderContent();
			} else {
				$this->content .= $LANG->getLL('error_nopidselected');
			}
				


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function renderContent()	{
		global $LANG;
		
		if(isset($_POST['download']) && !empty($_POST['download'])) {
			$content = $this->renderExport();
		} else {
			$content = $this->renderExample();
			$content .= $this->downloadButton();
		}
		
		$this->content.=$this->doc->section('Example:',$content,0,1);
		
	}
	
	function downloadButton() {
		global $LANG;
		$content = '<input type="submit" name="download" value="'.$LANG->getLL('downloadbutton').'"/>';
		$languageid = intval($this->a_variables['language']);
		
		// count of found tt_content elements..
		$where['uid'] = 'uid IN ('. implode(',', $this->o_treeview->ids) .')';
		$where['language'] = 'sys_language_uid = '.$languageid;
		$where['deleted'] = 'deleted = 0';
		
		$resultSet = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'COUNT(uid)', 
			'tt_content', 
			implode(' AND ', $where) . '' . t3lib_BEfunc::BEenableFields('tt_content')
		);
		if ($resultSet !== false) {
			list($count) = $GLOBALS['TYPO3_DB']->sql_fetch_row($resultSet);
			$GLOBALS['TYPO3_DB']->sql_free_result($resultSet);
		}
		
		$content .= '<p class="count">'.$count.' '.$LANG->getLL('count_results') .'</p>';
		return $content;
	}
	
	function renderExample() {
		$content = $this->setExampleFromPID($this->id);
		return $content;
	}
	
	function renderExport() {		
		if($_GET['debug'] == 'true') { 
			$this->o_doc->isDebugging = true;
			$this->o_doc->init();
			$content = $this->setContentFromPID($this->id);
		} else {
			$content = $this->setContentFromPID($this->id);
			echo($content);
			exit();
		}
		
		
		return $content;
	}
	
	function setContentFromPID($id) {
		$depth = intval($this->a_variables['depth']);
		$languageid = intval($this->a_variables['language']);
		
		$a_treeIds = $this->getRecursive($id, $depth);
	
		$where['ctype'] = 'CType IN ("text","textpic","header")';
		$this->setContentFromArray($a_treeIds, $where, $languageid);
		
		$content = $this->o_doc->downloadDocumentFromHTML();
		return $content;
	}
	
	function setContentFromArray($array, $where, $languageid = 0, $depth = 0) {
		foreach($array as $page) {
			// first render the content
			$where['pid'] = 'pid='.intval($page['uid']);
			$where['language'] = 'sys_language_uid = '.$languageid;
			$where['deleted'] = 'deleted = 0';
			
			$query = array (
				'SELECT' => 'header, header_layout, bodytext, image',
				'FROM' => 'tt_content',
				'WHERE' => implode(' AND ', $where) . '' . t3lib_BEfunc::BEenableFields('tt_content'),
				'GROUPBY' => '',
				'ORDERBY' => 'sorting',
				'LIMIT' => ''
			);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// render header
				if($row['header_layout'] == 1) { // 1 = h1, 2 = contenttitle.. 100 = header is not visible
					$this->o_doc->addHeader($row['header'], $depth + 1);
				} elseif($row['header_layout'] == 2) {
					$this->o_doc->addParagraph($row['header'], array('text-align' => 'center'));
				}
				
				// render text
				if( !empty($row['bodytext']) ) {
					$specConf['rte_transform']['parameters']['mode'] = 'ts_css';
					$value = $row['bodytext'];
					$value = str_replace('&amp;', '&', $value); // quickfix for the &amp;amp;
					$value = $this->localHTMLParser->RTE_transform($value, $specConf);
					$value = $this->localHTMLParser->HTMLcleaner($value, $this->a_rteConf['keepTags'], 0, $addConfig = array('xhtml' => 1));
					
					$this->o_doc->RTEtoParagraphs($value);
				}
				
				// render image
				if( !empty($row['image']) ) {
					$allimages = explode(',', $row['image']);
					foreach($allimages as $image) {
						$imagelocation = array (
							'link' => 'uploads/pics/'. $image,
							'base' => 'http://'.$_SERVER['HTTP_HOST'].'/',
							'relative' => PATH_site
						);
						if($this->o_doc->isDebugging) {
							$value = '<img src="/'.$imagelocation['link'].'" />';
							$this->o_doc->addParagraph($value);
						} else {
							$this->o_doc->addImage($imagelocation);
						}
					}
				}
			}
			
			// then check if there is any subpage
			if(isset($page['subrow']) && !empty($page['subrow']) && is_array($page['subrow'])) {
				$newdepth = $depth + 1;
				$this->setContentFromArray($page['subrow'], $where, $languageid, $newdepth);
			}
		}
	}
	
	function setExampleFromPID($id) {
		$depth = intval($this->a_variables['depth']);
		$languageid = intval($this->a_variables['language']);
		
		$a_treeIds = $this->getRecursive($id, $depth);
		$where = array();
		
		$content = $this->setExampleFromArray($a_treeIds, $where, $languageid);
		return $content;
	}
	
	function setExampleFromArray($array, $where, $depth = 0) {
		$content .= '<ul>';
		foreach($array as $page) {
			// first render the content
			$where['uid'] = 'uid='.intval($page['uid']);
			$where['deleted'] = 'deleted = 0';
			
			$query = array (
				'SELECT' => 'title',
				'FROM' => 'pages',
				'WHERE' => implode(' AND ', $where) . '' . t3lib_BEfunc::BEenableFields('pages'),
				'GROUPBY' => '',
				'ORDERBY' => 'sorting',
				'LIMIT' => ''
			);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($query);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// render title
				$content .= '<li>';
				$content .= $row['title'];
				// then check if there is any subpage
				if(isset($page['subrow']) && !empty($page['subrow']) && is_array($page['subrow'])) {
					$newdepth = $depth + 1;
					$content .= $this->setExampleFromArray($page['subrow'], $where, $newdepth);
				}
				
				$content .= '</li>';
			}	
		}
		$content .= '</ul>';
		
		return $content;
	}

	
	function getRecursive($pid, $depth) {
		$this->o_treeview->getTree($pid, $depth);
		$subpages = ($depth > 0) ? $this->o_treeview->buffer_idH : '';
		$a_treeIds[$pid] = array (
			'uid' => $pid,
			'subrow' => $subpages
		);
		return $a_treeIds;
	}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rk_exportdoc/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rk_exportdoc/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_rkexportdoc_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>