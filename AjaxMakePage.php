<?php
/**
 * AjaxMakePage extension enables users to create a new page and populate it with text 
(or replace the text of an existing page) using an input, a textbox and two buttons -  
made with [http://www.organicdesign.co.nz/Template:Extension Template:Extension]
 *
 * See http://www.mediawiki.org/wiki/Extension:AjaxMakePage for installation and usage details
 *
# - Licenced under LGPL (http://www.gnu.org/copyleft/lesser.html)
# - Author: [http://organicdesign.co.nz/User:Jack Jack Henderson]

 */
if (!defined('MEDIAWIKI')) die('Not an entry point.');
 
define('AJAXMAKEPAGE_VERSION', '0.0.1, 2008-12-20');
 
$wgExtensionFunctions[]        = 'efSetupAjaxMakePage';

$wgAjaxExportList[] = "crpage";
$wgAjaxExportList[] = "crdummy1";
$wgAjaxExportList[] = "crdummy2";

$wgExtensionCredits['parserhook'][] = array(
	'name'        => 'AjaxMakePage',
	'author'      => '[http://www.flashkiwi.com Jack Henderson]',
	'description' => 'An extension making it possible to enter a new or existing pages name 
and its contents in textboxes at the bottom of the existing page, without leaving the page, 
made with [http://www.organicdesign.co.nz/Template:Extension Template:Extension]',
	'url'         => 'http://www.organicdesign.co.nz/Extension:AjaxMakePage',
	'version'     => AJAXMAKEPAGE_VERSION
	);
 
function efAjaxMakePage (&$out,$file) {
	global $wgOut,$wgServer,$wgScriptPath;
	$wgOut->addHTML('<input id="yajax" type="hidden" value=""/>');
	$wgOut->addHTML('<input id="zajax" type="hidden" value=""/>');
	$wgOut->addHTML('<br /><b>Generate a New Page</b><br /><br />');
	$wgOut->addHTML('<b><em>Enter New Page Title</em></b><br />
<input id="newname" value="" onblur="sajax_do_call(\'crdummy1\',
[document.getElementById(\'newname\').value], 
document.getElementById(\'yajax\'))" /><br /><br />');
	$wgOut->addHTML('<b><em>Enter Page Contents</em></b><br />
<textarea rows="5" cols="40" id="newcontent" onblur="sajax_do_call(\'crdummy2\',
[document.getElementById(\'newcontent\').value], 
document.getElementById(\'zajax\'))"></textarea><br /><br />');
	$wgOut->addHTML('<input type="button" onclick="sajax_do_call(\'crdummy2\',
[document.getElementById(\'newcontent\').value], 
document.getElementById(\'zajax\'))" id="setupbutton" value="Press This First (Setup)" /><br /><br />');
	$wgOut->addHTML('<input type="button" onclick="sajax_do_call(\'crpage\',
[document.getElementById(\'yajax\').value,document.getElementById(\'zajax\').value],
document.getElementById(\'tajax\'))" id="newbutton" value="Then Press This (Create)" /><br /><br />');
  	$wgOut->addHTML('<label id="tajax" /<br />');
	return true;
}

function crpage($arg1,$arg2) {
	$title = Title::newFromText($arg1);
	$article = new Article($title);
	$arg2 = htmlspecialchars($arg2);
	$article->doEdit($arg2,EDIT_UPDATE|EDIT_MINOR);
	return "Page $arg1 created";
}

function crdummy1($file1) {
	return $file1;
}

function crdummy2($file2) {
	return $file2;
}

function efSetupAjaxMakePage() {
	global $wgHooks;
	$wgHooks['BeforePageDisplay'][] = 'efAjaxMakePage';
}
