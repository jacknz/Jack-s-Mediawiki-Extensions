<?php
# Extension:SuperGallery{{Category:Extensions}}{{php}}{{Category:Extensions created with Template:SpecialPage}}
# - Licenced under LGPL (http://www.gnu.org/copyleft/lesser.html)
# - Author: [http://www.flashkiwi.com Jack Henderson]
 
if (!defined('MEDIAWIKI')) die('Not an entry point.');
 
define('SUPERGALLERY_VERSION','1.0.0, 2008-12-15');
 
$wgExtensionFunctions[] = 'wfSetupSuperGallery';

$egSuperGalleryTag = "supergallery";

$wgExtensionCredits['specialpage'][] = array(
	'name'        => 'Special:SuperGallery',
	'author'      => '[http://www.flashkiwi.com Jack Henderson]',
	'description' => 'An special page for creating a supergallery of galleries, made with [http://www.organicdesign.co.nz/Template:SpecialPage Template:SpecialPage].',
	'url'         => 'http://www.organicdesign.co.nz/Extension:SuperGallery',
	'version'     => SUPERGALLERY_VERSION
	);
 
require_once "$IP/includes/SpecialPage.php";
 
# Define a new class based on the SpecialPage class
class SpecialSuperGallery extends SpecialPage {
 
	# Constructor
	function __construct() {
		global $wgHooks, $wgParser, $egSuperGalleryTag;
		SpecialPage::SpecialPage(
			'SuperGallery',     # name as seen in links etc
			'sysop',       # user rights required
			true,          # listed in special:specialpages
			false,         # function called by execute() - defaults to wfSpecial{$name}
			false,         # file included by execute() - defaults to Special{$name}.php, only used if no function
			false          # includable
			);
		# Add the tagHook
		$wgParser->setHook($egSuperGalleryTag, array($this, 'tagSupergallery'));

	}
		
	function tagSupergallery($text,$argv,&$parser) {
		global $egSuperGalleryTag, $wgServer, $wgScript;
		
		# Make an array of the content
		$nz = 9; # All the styles
		$ny = count ($argv) - $nz;
		$par = $ny / 4;
		$conarr = array_slice($argv,9); # Array of content
		$uarr[] = array();
		$iarr[] = array();
		$barr[] = array();
		$carr[] = array();
		
		foreach ($conarr as $k => $v){
			if (preg_replace ('%(.*)([0-9]{1,2})%','\1',$k) == "url") $uarr[preg_replace ('%(.*)([0-9]{1,2})%','\2',$k)] = $v;
			elseif (preg_replace ('%(.*)([0-9]{1,2})%','\1',$k) == "image") $iarr[preg_replace ('%(.*)([0-9]{1,2})%','\2',$k)] = $v;
			elseif (preg_replace ('%(.*)([0-9]{1,2})%','\1',$k) == "bkpre") $barr[preg_replace ('%(.*)([0-9]{1,2})%','\2',$k)] = $v;
			else $carr[preg_replace ('%(.*)([0-9]{1,2})%','\2',$k)] = "<a href='" . $wgServer . $wgScript . "/" . $v . "'>" . $v . "</a>";
		}

		# Table css			
		$gtext = "<style type='text/css'>table.supergallery" . $argv['gtitle'] . "{ border:" . $argv['border'] . "px solid " . $argv['bordercolor'] . ";margin:" . $argv['tablemargin'] . "px;padding:" . $argv['tablemargin'] . "px;background-color:" . $argv['backgroundcolor'] .";}</style>";

		for ($i = 1; $i <= $par; $i++) {

			# Set up rightsized thumbs silently by using the wiki thumbnail renderer. 
			# These are used as backgrounds for the gallery cells.
			$gtext .= "<div style='display:none'>[[Image:" . $iarr[$i] . "|" . $argv['width'] . "px]]</div>";

			# Set up css for the backgrounds
			$gtext .= "<style type='text/css'>.sgclass" . $i . "{background:url(" . $barr[$i] . $argv['width'] . "px-" . $iarr[$i] . ") center center no-repeat}</style>";
		}

		# Render table code for copying
		$gtext .= "<table cellpadding='" . $argv['cellpadding'] . "' class ='supergallery" . $argv['gtitle'] . "'>";
		if ($argv['column'] == 1) {
			for ($j = 1; $j <= $par; $j++) {
				$caption = $carr[$j];
				if ($caption != "") {
					$gtext .= "<tr valign = 'center'>
					<td class = 'sgclass" . $j . "' height = '" . $argv['height'] . "' width = '" . $argv['width'] . "'>
					</td></tr>
					<tr valign = 'center'>
					<td><center>$caption</center>
					</td></tr>";
				}
			}
		}
		elseif ($argv['column'] == 2) {
			$z = 1;
			while ($z <= $par) {
				$m = $z;
				$caption = $carr[$m];
				if ($caption != "") {
					$gtext .= "<tr valign = 'center'>
					<td class = 'sgclass" . $z . "' height = '" . $argv['height'] . "' width = '" . $argv['width'] . "'>
					</td>";
					$z++;
					if ($z <= $par) {
						$gtext .= "<td class = 'sgclass" . $z . "' height = '" . $argv['height'] . "' width = '" . $argv['width'] . "'>
						</td>";
						$z++;
					}
					$gtext .= "</tr><tr valign = 'center'>
					<td><center>$caption</center>
					</td>";
					$m++;
				}
				if ($m <= $par) {
					$caption = $carr[$m];
					if ($caption != "") {				
						$gtext .= "<td><center>$caption</center>
						</td>";
					}
				}
				$m++;
				$gtext .= "</tr>";
			}
		}
		else { 
			$z = 1;
			while ($z <= $par) {
				$m = $z;
				$gtext .= "<tr valign = 'center'>";
				$gtext .= "<td class = 'sgclass" . $z . "' height = '" . $argv['height'] . "' width = '" . $argv['width'] . "'>
				</td>";
				$z++;
				if ($z <= $par) {
					$gtext .= "<td class = 'sgclass" . $z . "' height = '" . $argv['height'] . "' width = '" . $argv['width'] . "'>
					</td>";
					$z++;
				}
				if ($z <= $par) {
					$gtext .= "<td class = 'sgclass" . $z . "' height = '" . $argv['height'] . "' width = '" . $argv['width'] . "'>
					</td>";
					$z++;
				}
				$gtext .= "</tr>";
				$gtext .= "<tr valign = 'center'>";
				if ($m <= $par) {
				$caption = $carr[$m];
					if ($caption != "") {
						$gtext .= "<td><center>$caption</center>
						</td>";
						$m++;
					}
				}
				if ($m <= $par) {
				$caption = $carr[$m];
					if ($caption != "") {
						$gtext .= "<td><center>$caption</center>
						</td>";
						$m++;
					}
				}
				if ($m <= $par) {
				$caption = $carr[$m];
					if ($caption != "") {
						$gtext .= "<td><center>$caption</center>
						</td>";
						$m++;
					}
				}
				
				$gtext .= "</tr>";
			}
		}
		$gtext .= "</table>";
		return $gtext;

	}
 
	# Override SpecialPage::execute()
	# - $param is from the URL, eg Special:{{{name}}}/param
	function execute($param) {
		global $wgOut, $wgRequest, $wgTitle, $wgUser, $wgMessageCache;
		$this->setHeaders();
		$title = Title::makeTitle(NS_SPECIAL,'SuperGallery');
		
		# The $param is the number of text inputs to be replicated in the form
		if (isset($param)) $par = $param;
		
		# If posted, we want to keep $par from the previous execution so we extract data from the hidden field
		else if ($wgRequest->getText('wpParam', "")) $par = $wgRequest->getText('wpParam', "");
		
		# A low default to encourage users to use the switch
		else $par = 4;
		 
		# Extract any posted data
		$posted = $wgRequest->getText('wpSubmit', false);
		
		# Extract any contents of fields
		$gtitle = $wgRequest->getText('wpGtitle', "");
		$width = $wgRequest->getText('wpWidth', "");
		$height = $wgRequest->getText('wpHeight', "");
		$column = $wgRequest->getText('wpColumn', "");
		$border = $wgRequest->getText('wpBorder', "");
		$tablemargin = $wgRequest->getText('wpTableMargin', "");
		$bordercolor = $wgRequest->getText('wpBorderColor', "");
		$backgroundcolor = $wgRequest->getText('wpBackgroundColor', "");
		$cellpadding = $wgRequest->getText('wpCellPadding', "");
				
		# Render the form
		$wgOut->addWikiText(wfMsg('supergalleryMessage', 'supergalleryParameter'));
		$wgOut->addHTML(
		wfElement('form', array('action' => $title->getLocalURL('action=submit'), 'method' => 'post'), null)
		. "<br /><p><b>Usage</b></p>
		<p>Fill out the form below for each gallery you want to include, with a unique name for your supergallery.</p>
		<p>This extension defaults to four pairs of gallery inputs, unless the number of galleries in your intended supergallery is passed as this page's parameter, for example for five galleries:
		<pre>http://localhost/wiki/index.php/Special:SuperGallery/5</pre></p>
		<p>The galleries you link to will be created if they do not exist, and can themselves be made as usual using the gallery tag.</p>
		<p>Here is the expected form of the picture url and the article link:
		<pre>http://localhost/wiki/images/d/de/Boat.jpg

[[Pictures_of_Auckland|Pictures of Auckland]]</pre></p>
		<p>Press the Submit Query button. A new page will be generated under your unique title, with a supergallery tag and your posted information, which you can edit.</p>
		<br/ >
		<p><b>Page Setup</b></p>
		<p>What is the title of your supergallery? This is a compulsory field.</p>
		<input name='wpGtitle' size = '35' />
		<p>How many columns do you want the supergallery to have (up to 3)? If you leave the field empty it is 2.</p>
		<input name='wpColumn' size = '10' />
		<p>How wide do you want each thumbnail to be? Empty field is 120.</p>
		<input name='wpWidth' size = '10' />
		<p>How high do you want each thumbnail's box to be? Empty field is 200.</p>
		<input name='wpHeight' size = '10' />
		<p>How wide do you want the supergallery's border to be? Empty field is 1. No border is 0.</p>
		<input name='wpBorder' size = '10' />
		<p>What color do you want the supergallery's border to be? Empty field is #cccccc.</p>
		<input name='wpBorderColor' size = '10' />
		<p>What do you want the supergallery's margin to be? Empty field is 2.</p>
		<input name='wpTableMargin' size = '10' />
		<p>How much cellpadding to you want? Empty field is 20.</p>
		<input name='wpCellPadding' size = '10' />
		<p>What color do you want the supergallery's background to be? Empty field is #ffffff.</p>
		<input name='wpBackgroundColor' size = '10' />
		<input type='hidden' name='wpParam' value=$par><br /><br />"
		);
		
		# Render multiple inputs using the /$par from the url
		for ($ctr = 1; $ctr <= $par; $ctr++) {
			$wgOut->addHTML(
			"<p><b>Gallery " . $ctr ."</b></p>
			<p>The full URL of the image you want for the thumbnail.</p>
			<input name='wpUrl" . $ctr . "' size = '50' />
			<p>The caption for this gallery, which is also its link.</p>
			<input name='wpCaption" . $ctr . "' size = '50' />"
			);
		}
		
		# Post 
		$wgOut->addHTML(
		"<br /><br />"
		. wfElement('input', array('name' => 'wpSubmit', 'type' => 'submit'))
		. "</form><br /><p>When you submit this form, a supergallery page will be created with the name you specified, unless the name already exists. You can edit the tag in that page if you wish to change the supergallery.</p>"
		);
		
		# Process results if data posted
		if ($posted) {
			# Defaults for posted style elements
			if ($width == "") $width = 120;
			if ($height == "") $height = 200;
			if ($column == "") $column = 2;
			if ($border == "") $border = 1;
			if ($tablemargin == "") $tablemargin = 2;
			if ($bordercolor == "") $bordercolor = "#cccccc";
			if ($backgroundcolor == "") $backgroundcolor = "#ffffff";
			if ($cellpadding == "") $cellpadding = 10;
			
			# Build tag
			$tag = '<supergallery 
			gtitle = "'.$gtitle.'"
			width = "'.$width.'"
			height = "'.$height.'"
			column = "'.$column.'"
			border = "'.$border.'"
			tablemargin = "'.$tablemargin.'"
			bordercolor = "'.$bordercolor.'"
			backgroundcolor	= "'.$backgroundcolor.'"
			cellpadding = "'.$cellpadding.'"';
			
			# Some arrays to populate with loops
			$url[] = array();
			$image[] = array();
			$bkpre[] = array();
			$caption[] = array();

			# Make some variables out of the posted fields
			for ($n = 1; $n <= $par; $n++) {
				$url[$n] = $wgRequest->getText('wpUrl' . $n, "");
				$image[$n] = preg_replace ('%(.*/images/)(.*/)(.*)(\.)(.*)%','\3\4\5',trim($url[$n]));
				$bkpre[$n] = preg_replace ('%(.*/images/)(.*/)(.*)(\.)(.*)%','\1thumb/\2\3\4\5/',trim($url[$n]));
				$caption[$n] = $wgRequest->getText('wpCaption' . $n, "");
			}
			for ($n = 1; $n <= $par; $n++) {
				if ($url[$n] != "") {
					$tag .= ' url' .$n. ' = "' . $url[$n] . '"';
					$tag .= ' image' .$n. ' = "' . $image[$n] . '"';
					$tag .= ' bkpre' .$n. ' = "' . $bkpre[$n] . '"';
					$tag .= ' caption' .$n. ' = "' . $caption[$n] . '"';
				}
			}
			$tag .= '>';
			if ($wgUser->isAllowed('edit')) {
				$title = Title::newFromText( $gtitle );
				if (trim($wgRequest->getText('wpGtitle'))=='') {
					$wgTitle = Title::newFromText( wfMsgForContent( 'badtitle' ) );
					$wgOut->errorpage( 'badtitle', 'badtitletext');
				}
				if((isset($title)) && ($title->getArticleID() == 0)) {
					$article  = new Article($title);
					$article->doEdit($tag, "", EDIT_NEW);
					$wgOut->addHTML(wfMsg('supergallerySuccessMessage'));
				} 
				elseif (!isset($title)) {
					$wgTitle = Title::newFromText( wfMsgForContent( 'badtitle' ) );
					$wgOut->errorpage( 'badtitle', 'badtitletext');
				}
				else {
					$wgTitle = Title::newFromText( wfMsgForContent( 'badtitle' ) );
					$wgOut->errorpage( 'error', 'articleexists');
				}
			}
			
			else {
				$wgTitle = Title::newFromText( wfMsgForContent( 'badtitle' ) );
				$wgOut->errorpage( 'error', 'badarticleerror');
			}
		}
	}
}
 
# Called from $wgExtensionFunctions array when initialising extensions
function wfSetupSuperGallery() {
	global $wgLanguageCode,$wgMessageCache,$wgUser,$wgRequest;
	
	# Ensure the code only runs if it is a Supergallery and the user is logged in
	$sgnewtitle = Title::newFromText($wgRequest->getText('title'));
	if (preg_match('%(SuperGallery)(.*)%',$sgnewtitle) && !$wgUser->isAnon()) {

		# Add the messages used by the specialpage
		if ($wgLanguageCode == 'en') {
			$wgMessageCache->addMessages(array(
			'supergallery' => 'SuperGallery Specialpage',        # The friendly page title
			'supergalleryMessage' => "SuperGallery: <tt>Use this page to create a supergallery of galleries</tt>",
			'supergallerySuccessMessage' => "<br /><b>Your SuperGallery has been successfully created.</b><br />")
			);
			
		}

		# Add the specialpage to the environment
		SpecialPage::addPage(new SpecialSuperGallery());
	}
}
