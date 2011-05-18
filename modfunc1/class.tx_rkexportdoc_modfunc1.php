<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011  <>
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


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
	
	// Library import
require_once(t3lib_extMgm::extPath('rk_exportdoc').'lib/lib.tx_rkexportdoc_export.php'); // load export class

/**
 * Module extension (addition to function menu) 'Export (sub)pages to document' for the 'rk_exportdoc' extension.
 *
 * @author	Benjamin Serfhos <serfhos@redkiwi.nl>
 * @package	TYPO3
 * @subpackage	tx_rkexportdoc
 */
class tx_rkexportdoc_modfunc1 extends t3lib_extobjbase {
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
	* Returns the module menu
	*
	* @return    Array with menuitems
	*/
	function modMenu()    {
		global $LANG;
	
		return Array (
		"tx_rkexportdoc_modfunc1_check" => "",
		);
	}
	
	/**
	* Main method of the module
	*
	* @return    HTML
	*/
	function main()    {
		// Initializes the module. Done in this function because we may need to re-initialize if data is submitted!
		global $SOBE,$BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		$theOutput = '';
		$this->classInit();
		
		$this->pObj->doc->form = '<form action="" method="post">';
		$theOutput .= $this->pObj->doc->sectionHeader($LANG->getLL("title"));
		// select languages and depth		
		$menu = '';
			if(!empty($this->a_menu)) {
				foreach($this->a_menu as $menuselect => $items) {
					// get the current parameters
					$var = array (
						'id' => $this->pObj->id,
						'VAR' => $this->a_variables
					);
					unset($var['VAR'][$menuselect]);
					$menu[] = $this->getFuncMenu($var, 'VAR['.$menuselect.']',$this->a_variables[$menuselect],$items);
				}
			}
		$theOutput.=$this->pObj->doc->spacer(5);
		$theOutput.=$this->pObj->doc->section("",implode(" - ",$menu),0,1);
		
		// show selected elements
		$theOutput.=$this->pObj->doc->spacer(5);
		$theOutput.=$this->pObj->doc->section("",$this->renderContent(),0,1);
	
		return $theOutput;
	}
	
	function classInit() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
		$this->o_treeview = t3lib_div::makeInstance('t3lib_pageTree');
		$this->o_treeview->init('AND no_search = 0 AND hidden != 1 AND deleted != 1 AND doktype NOT IN (199, 254, 255, 5)' . $s_addWhere); 
		$this->localHTMLParser = t3lib_div::makeInstance('t3lib_parsehtml_proc');
		$this->o_doc = new rk_exportdoc_export();
		
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
	}
	
	function renderSelectsMenu() {
		global $LANG;
		
		$qry_languages = t3lib_BEfunc::getSystemLanguages();
		foreach($qry_languages as $language) {
			$a_languages[$language[1]] = $language[0]; // sets to array[$language_uid] = $language_title
		}
		$this->a_menu = array (
			'language' => array (
				'title' => $LANG->getLL('select_language'),
				'items' => $a_languages
			),
			'depth' => array (
				'title' => $LANG->getLL('select_depth'),
				'items' => array (
					0 => '0',
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					999 => 'infinite'
				),
			)
		);
				
		if(isset($_GET['VAR']) && !empty($_GET['VAR'])) {
			foreach($_GET['VAR'] as $key => $selected) {
				$this->a_variables[$key] = $selected;
			}
		}
	}
	
	/**
	 * Basic funcionality that can be found in the t3lib_BEfunc
	 * but this disables the first option and adds a title
	 *
	 * @return	string
	 */
	function getFuncMenu($mainParams, $elementName, $currentValue, $menuItems, $script = '', $addparams = '') {
		
		if (is_array($menuItems)) {
			if (!is_array($mainParams)) {
				$mainParams = array('id' => $mainParams);
			}
			$mainParams = t3lib_div::implodeArrayForUrl('', $mainParams);
			
			if (!$script) {
				$script = basename(PATH_thisScript);
				$mainParams.= (t3lib_div::_GET('M') ? '&M='.rawurlencode(t3lib_div::_GET('M')) : '');
			}
			
			$options = array();
			foreach($menuItems as $key => $res) {
				if($key == 'title') {
					$options[] = '<option disabled="disabled">'.
						t3lib_div::deHSCentities(htmlspecialchars($res)).
						'</option>';
				}
				if($key == 'items') {
					foreach($res as $value => $label) {
						$options[] = '<option value="'.htmlspecialchars($value).'"'.(!strcmp($currentValue, $value)?' selected="selected"':'').(($value === 'title') ? ' disabled="disabled"' : '').'>'.
							t3lib_div::deHSCentities(htmlspecialchars($label)).
							'</option>';
					}
				}
			}
			
			if (count($options)) {
				$onChange = 'jumpToUrl(\''.$script.'?'.$mainParams.$addparams.'&'.$elementName.'=\'+this.options[this.selectedIndex].value,this);';
				return '
					<!-- Function Menu of module -->
					<select name="'.$elementName.'" onchange="'.htmlspecialchars($onChange).'">
					'.implode('
					',$options).'
					</select>
				';
			}
		}
		return '';
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
			$content .= $this->pObj->doc->spacer(5);
			$content .= $this->downloadButton();
		}
		
		return $this->pObj->doc->section($LANG->getLL('example'),$content,0,1);
	}
	
	
	/**
	 * Render download button + results.
	 *
	 * @return	void
	 */
	function downloadButton() {
		global $LANG;
		$content = '<input type="submit" name="download" value="'.$LANG->getLL('downloadbutton').'"/>';
		$languageid = intval($this->a_variables['language']);
		
		// count of found tt_content elements..
		$where['pid'] = 'pid IN ('. implode(',', $this->o_treeview->ids) .')';
		$where['language'] = 'sys_language_uid = '.$languageid;
		$where['ctype'] = 'CType IN ("text","textpic","header")';
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
		$content = $this->setExampleFromPID($this->pObj->id);
		return $content;
	}
	
	
	function renderDocumentTitle() {
		$title = $this->pageinfo['title'];
		
		$website = $_SERVER['HTTP_HOST'];
		$website = str_replace('www.', '', $website);
		
    $table = array(
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj', 'Ž'=>'Z', 'ž'=>'z', 'ÿ'=>'y', ' '=>'-'
    );
   	$title = strtr($title, $table);
   	$title = strtolower($title);
   
   	$website = strtr($website, $table);
   	$website = strtolower($website);
   
   	$filename = 'TYPO3GENERATED-'.$website.'-'.$title.'.doc';
    return $filename;

	}
	
	function renderExport() {
		$filename = $this->renderDocumentTitle();
		$this->o_doc->setFileName($filename);
		if($_GET['debug'] == 'true') { 
			$this->o_doc->isDebugging = true;
			$this->o_doc->init();
			$content = $this->setContentFromPID($this->pObj->id);
		} else {
			$content = $this->setContentFromPID($this->pObj->id);
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
		$content .= '<ul style="padding-left: 10px; list-style: inherit;">';
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rk_exportdoc/modfunc1/class.tx_rkexportdoc_modfunc1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rk_exportdoc/modfunc1/class.tx_rkexportdoc_modfunc1.php']);
}
?>