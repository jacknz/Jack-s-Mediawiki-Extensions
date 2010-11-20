<?php
/**
 * FileHistoryClear extension - An extension to strip links from file page views. Made with [http://www.organicdesign.co.nz/Template:Extension Template:Extension].
 *{{php}}{{Category:Extensions|FileHistoryClear}}{{Category:Jack}}
 * See http://www.mediawiki.org/wiki/Extension:FileHistoryClear for installation and usage details
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author [http://www.organicdesign.co.nz/wiki/User:Jack User:Jack]
 * @copyright © 2009 [http://www.organicdesign.co.nz/wiki/User:Jack User:Jack]
 * @licence GNU General Public Licence 2.0 or later
 */
if (!defined('MEDIAWIKI')) die('Not an entry point.');
 
define('FILEHISTORYCLEAR_VERSION', '1.0.0, 2009-07-16');
 
$wgExtensionFunctions[]        = 'efSetupFileHistoryClear';

$wgExtensionCredits['parserhook'][] = array(
	'name'        => 'FileHistoryClear',
	'author'      => '[http://www.organicdesign.co.nz/wiki/User:Jack User:Jack]',
	'description' => 'An extension to strip links from file page views. Made with [http://www.organicdesign.co.nz/Template:Extension Template:Extension].',
	'url'         => 'http://www.organicdesign.co.nz/Extension:FileHistoryClear.php',
	'version'     => FILEHISTORYCLEAR_VERSION
	);
 
/**
 * Function called from the hook BeforePageDisplay, with a regular expression to replace links.
 */

function efFileHistoryClear (&$out) {
	$out->mBodytext = preg_replace ('%(<div class="fullImageLink" id="file">)(.*)(<img.+?/>)(</a.+?)(</div>)%s','<div class="fullImageLink" id="file">\3</div>',$out->mBodytext);
	return true;
}
 
/**
 * Setup function specifies a condition for the page being an file page.
 */
 
function efSetupFileHistoryClear() {
	global $wgHooks,$wgUser,$wgRequest;
	$title = Title::newFromText($wgRequest->getText('title'));
	if (is_object($title) && $title->getNamespace()==NS_FILE) 
		$wgHooks['BeforePageDisplay'][] = 'efFileHistoryClear';
}
