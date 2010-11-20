<?php
# Extension:EasyGallery{{Category:Extensions}}{{php}}{{Category:Extensions created with Template:SpecialPage}}
# - Licenced under LGPL (http://www.gnu.org/copyleft/lesser.html)
# - Author: [http://www.flashkiwi.com Jack Henderson]

if (!defined('MEDIAWIKI')) die('Not an entry point.');

define('EASYGALLERY_VERSION','1.0.0, 2009-06-15');

$wgExtensionFunctions[] = 'wfSetupEasyGallery';

$wgExtensionCredits['specialpage'][] = array(
	'name'        => 'Special:EasyGallery',
	'author'      => '[http://www.flashkiwi.com Jack Henderson]',
	'description' => 'An special page for creating a gallery, made with [http://www.organicdesign.co.nz/Template:SpecialPage Template:SpecialPage].',
	'url'         => 'http://www.organicdesign.co.nz/Extension:EasyGallery',
	'version'     => EASYGALLERY_VERSION
	);

require_once "$IP/includes/SpecialPage.php";

# Define a new class based on the SpecialPage class

class SpecialEasyGallery extends SpecialPage {

	# Constructor
	function __construct() {
		global $wgHooks, $wgParser;
		SpecialPage::SpecialPage(
			'EasyGallery',     # name as seen in links etc
			'sysop',       # user rights required
			true,          # listed in special:specialpages
			false,         # function called by execute() - defaults to wfSpecial{$name}
			false,         # file included by execute() - defaults to Special{$name}.php, only used if no function
			false          # includable
			);
	}

	# Override SpecialPage::execute()
	# $param is from the URL, eg Special:{{{name}}}/param

	function execute($param) {
		global $wgOut, $wgRequest, $wgTitle, $wgUser, $wgMessageCache;
		$this->setHeaders();
		$title = Title::makeTitle(NS_SPECIAL,'EasyGallery');
		$par = 8;

		# The $param is the number of text inputs to be replicated in the form
		if (isset($param)) $par = $param;

		# If posted, we want to keep $par from the previous execution so we extract data from the hidden field
		else if ($wgRequest->getText('wpParam', "")) $par = $wgRequest->getText('wpParam', "");

		# A low default to encourage users to use the switch
		else $par = 8;

		# Extract any posted data
		$posted = $wgRequest->getText('wpSubmit', false);

		# Extract any contents of fields
		$gtitle = $wgRequest->getText('wpGtitle', "");
		$width = $wgRequest->getText('wpWidth', "");
		$column = $wgRequest->getText('wpColumn', "");

		# Render the form
		$wgOut->addWikiText(wfMsg('easygalleryMessage', 'easygalleryParameter'));
		$wgOut->addHTML(
		wfElement('form', array('action' => $title->getLocalURL('action=submit'), 'method' => 'post'), null)
		. "<br /><p><b>Usage</b></p>
		<p>This form creates a gallery in the page you enter below, whether the page exists or not.</p>
		<p>If there is already text in the page it will be retained but an existing gallery will be replaced by this one. You can have one gallery per page.</p>
		<p>This extension defaults to 8 pictures (plus captions), unless the number of pictures in your intended gallery is added to the address bar title, for example for five images:
		http://localhost/wiki/index.php/Special:EasyGallery/5</p>
		<p>Here is the expected form of the picture and the caption:
		Image:Example.jpg
                Example Caption</p>
		<p>You can upload your pictures by clicking <a href='" . $wgServer . $wgScript . "/Special:Upload' target='_blank'>Special:Upload</a>.</p>
		<p>You can access the names of images already uploaded by clicking <a href='" . $wgServer . $wgScript . "/Special:ImageList' target='_blank'>Special:ImageList</a>.</p>
		<p>Press the Submit Query button, then you can go to the page and edit it normally if you want.</p>
		<br/ >
		<p><b>Page Setup</b></p>
		<p>What is the title of the page your gallery will be in? This is a compulsory field.</p>
		<input name='wpGtitle' size = '35' />
		<p>How many columns do you want the easygallery to have? If you leave the field empty it is 2.</p>
		<input name='wpColumn' size = '10' />
		<p>How wide do you want each thumbnail to be (in pixels)? Empty field is 150.</p>
		<input name='wpWidth' size = '10' />
		<input type='hidden' name='wpParam' value=$par><br /><br />"
		);

		# Render multiple inputs using the /$par from the url
		for ($ctr = 1; $ctr <= $par; $ctr++) {
			$wgOut->addHTML(
			"<p><b>Gallery " . $ctr ."</b></p>
			<p>The image you want for the thumbnail.</p>
			<input name='wpUrl" . $ctr . "' size = '50' />
			<p>The caption for this image.</p>
			<input name='wpCaption" . $ctr . "' size = '50' />"
			);
		}

		# Post 
		$wgOut->addHTML(
		"<br /><br />"
		. wfElement('input', array('name' => 'wpSubmit', 'type' => 'submit'))
		. "<br /><br />"
		);

		# Process results if data posted
		if ($posted) {
			# Defaults for posted style elements
			if ($width == "") $width = 200;
			if ($column == "") $column = 2;

			# Build tag
			$tag = "<
gallery widths='".$width."px' perrow='".$column."'>\n";

			# Some arrays to populate with loops
			$url[] = array();
			$caption[] = array();

			# Make some variables out of the posted fields
			for ($n = 1; $n <= $par; $n++) {
				$url[$n] = $wgRequest->getText('wpUrl' . $n, "");
				$caption[$n] = $wgRequest->getText('wpCaption' . $n, "");
			}
			for ($n = 1; $n <= $par; $n++) {
				if ($url[$n] != "") {
					$tag .= $url[$n] . "|<center>". $caption[$n] ."</center>\n";
				}
			}
			$tag .= "</gallery>";
			if ($wgUser->isAllowed('edit')) {
				$title = Title::newFromText( $gtitle );
				if (trim($wgRequest->getText('wpGtitle'))=='') {
					$wgTitle = Title::newFromText( wfMsgForContent( 'badtitle' ) );
					$wgOut->errorpage( 'badtitle', 'badtitletext');
				}

				# Make the new page if no page exists.
				if ((isset($title)) && ($title->getArticleID() == 0)) {
					$article  = new Article($title);
					$article->doEdit($tag, "", EDIT_NEW);
					$wgOut->addHTML(wfMsg('easygallerySuccessMessage'));
				}
				
				#If the page exists, extract the existing text and replace
				elseif ((isset($title)) && ($title->getArticleID() != 0)) {
					$article = new Article($title, 0);
					$original = $article->fetchContent(0);
					$intro = preg_replace("%<gallery.*%is","",$original);
					$outro = preg_replace("%.*</gallery>%is","",$original);
					$tag = $intro.$tag.$outro;
					$article->doEdit($tag, "", EDIT_UPDATE);
					$wgOut->addHTML(wfMsg('easygallerySuccessMessage'));
				}

				else {
					$wgTitle = Title::newFromText( wfMsgForContent( 'badtitle' ) );
					$wgOut->errorpage( 'error', 'badtitletext');
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
function wfSetupEasyGallery() {
	global $wgLanguageCode,$wgMessageCache,$wgUser,$wgRequest;

	# Ensure the code only runs if it is a Easygallery and the user is logged in
	$sgnewtitle = Title::newFromText($wgRequest->getText('title'));
	if (preg_match('%(EasyGallery)(.*)%',$sgnewtitle) && !$wgUser->isAnon()) {

		# Add the messages used by the specialpage
	#	if ($wgLanguageCode == 'en') {
			$wgMessageCache->addMessages(array(
			'easygallery' => 'EasyGallery Specialpage',        # The friendly page title
			'easygalleryMessage' => "EasyGallery: <tt>Use this special page to create a gallery</tt>",
			'easygallerySuccessMessage' => "<br /><b>EasyGallery has successfully posted your data.</b><br />")
			);
	#	}

		# Add the special page to the environment
		SpecialPage::addPage(new SpecialEasyGallery());
	}
}
