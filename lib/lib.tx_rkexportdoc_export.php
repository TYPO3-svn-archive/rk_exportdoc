<?php
require_once(t3lib_extMgm::extPath('rk_exportdoc').'lib/lib.tx_rkexportdoc_MsDocGenerator.php'); // load export class

class rk_exportdoc_export {
	private $latin1_to_utf8;
  private $utf8_to_latin1;
    
	var $doc;
	var $filename = '';
	var $isDebugging = false;
	
	// on class initiation..
	function rk_exportdoc_export() {
		$this->doc = new clsMsDocGenerator();
		$this->init();
	}
	
	// global initation
	function init() {
		$this->doc->setDocumentCharset('utf-8');
		
    for($i=32; $i<=255; $i++) {
        $this->latin1_to_utf8[chr($i)] = utf8_encode(chr($i));
        $this->utf8_to_latin1[utf8_encode(chr($i))] = chr($i);
    }
	}
	
	function setDocumentLang($lang){
		$this->doc->setDocumentLang($lang);
	}
	
	function setFileName($filename){
		$this->filename = $filename;
	}
	
	function downloadDocumentFromHTML($html) {
		if($this->isDebugging) {
			return nl2br($this->doc->documentBuffer);
		}
		
		$output = $this->doc->output($this->filename);
		if( !empty( $output ) ) {
			return $output;
		} else {
			return false;
		}
	}
	
	function addImage($imagePath, $title = ''){
		list($width, $height, $type, $attr) = getimagesize($imagePath['relative'].$imagePath['link']);
		$max_width = 575;
		$max_height = 500;
		
		$x_ratio = $max_width / $width;
		$y_ratio = $max_height / $height;
		
		if( ($width <= $max_width) && ($height <= $max_height) ){
		  $tn_width = $width;
		  $tn_height = $height;
	  } elseif (($x_ratio * $height) < $max_height){
      $tn_height = ceil($x_ratio * $height);
      $tn_width = $max_width;
	  } else {
      $tn_width = ceil($y_ratio * $width);
      $tn_height = $max_height;
		}

		$imagelocation = $imagePath['base'].$imagePath['link'];
		
		$this->doc->addImage($imagelocation, $tn_width, $tn_height, $title);
	}
	
	function addParagraph($content, $inlineStyle = NULL, $className = 'normalText') {
		$content = $this->mixed_to_latin1($content); // strangely this works..
		$this->doc->addParagraph($content, $inlineStyle, $className);
	}
	
	function RTEtoParagraphs($content) {
		preg_match_all("/<p>(.*)<\/p>/U", $content, $matches);
		$contentelements = $matches[1];
		
		foreach ( $contentelements as $element ) {
			$this->doc->addParagraph($element);
		}
		return;
	}
	
	function addHeader($titel, $headerstyle = 1) {
		$s_header = 'h' . intval($headerstyle);
		$this->doc->addHeader($titel, $s_header);
		return;
	}
	
	function mixed_to_latin1($text) {
		foreach( $this->utf8_to_latin1 as $key => $val ) {
			$text = str_replace($key, $val, $text);
		}
		return $text;
	}

	function mixed_to_utf8($text) {
		return utf8_encode($this->mixed_to_latin1($text));
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rk_exportdoc/lib/lib.tx_rkexportdoc_export.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rk_exportdoc/lib/lib.tx_rkexportdoc_export.php']);
}
?>