<?php
/**
# Extension:ListDrag
# - Licenced under LGPL (http://www.gnu.org/copyleft/lesser.html)
# - Author: [http://organicdesign.co.nz/User:Jack Jack Henderson]
 */

if (!defined('MEDIAWIKI')) die('Not an entry point.');
 
define('LISTDRAG_VERSION', '0.0.3, 2009-02-17');
 
$wgExtensionFunctions[]        = 'efSetupListDrag';
 
$wgExtensionCredits['parserhook'][] = array(
	'name'        => 'ListDrag',
	'author'      => '[http://organicdesign.co.nz/User:Jack Jack Henderson]',
	'description' => 'An extension enabling the authorized user to relocate bullet points without manually saving the page',
	'url'         => 'http://www.organicdesign.co.nz/Extension:ListDrag',
	'version'     => LISTDRAG_VERSION
	);
 
function efListDrag (&$out,$file) {
	global $wgOut,$wgServer,$wgScriptPath;
	
	# Count the uls and ols
	preg_match_all('%>\s*\n\s*<ul>%s',$out->mBodytext,$uls);
	preg_match_all('%>\s*\n\s*<ol>%s',$out->mBodytext,$ols);

	# Loop through instances and give them IDs
	# Give all of each type the same Name to use the getElementsByName object in the Javascript
	for ($p = 1; $p <= count($uls[0]); $p++) {
		$out->mBodytext = preg_replace('%>\s*\n\s*<ul>%se','"><ul name=\"listdragul\" id=\"listdragul".$p."\">"',$out->mBodytext,1);
	}
	for ($q = 1; $q <= count($ols[0]); $q++) {
		$out->mBodytext = preg_replace('%>\s*\n\s*<ol>%se','"><ol name=\"listdragol\" id=\"listdragol".$q."\">"',$out->mBodytext,1);
	}
	
	# Add the list-saving Javascript	
	$wgOut->addHTML ('<script language="JavaScript" type="text/javascript" src="' . $wgServer . $wgScriptPath . '/extensions/ListDrag/autoedit.js"></script>');
	
	# Add the item-moving Javascript
	$wgOut->addHTML ('<script language="JavaScript" type="text/javascript" src="' . $wgServer . $wgScriptPath . '/extensions/ListDrag/Tool-Man.js"></script>');
	$wgOut->addHTML ('<script language="JavaScript" type="text/javascript">

	// Uses the Tool-man objects to serialize and deserialize the state of lists
	var dragsort = ToolMan.dragsort();
	var junkdrawer = ToolMan.junkdrawer();

	// Uses the Tool-man objects to serialize and deserialize the state of lists
	window.onload = function() {
		var len = document.getElementsByName("listdragul");
		var jen = document.getElementsByName("listdragol");
		for (i=1; i <= len.length; i++) {
			var ul_nm = "listdragul" + i;
			junkdrawer.restoreListOrder(ul_nm);
			dragsort.makeListSortable(document.getElementById(ul_nm),
			saveOrder);
		}
		for (j=1; j <= jen.length; j++) {
			var ol_nm = "listdragol" + j;
			junkdrawer.restoreListOrder(ol_nm);
			dragsort.makeListSortable(document.getElementById(ol_nm),
			saveOrder);
		}
	}

	function saveOrder(item) {
		//Construct the autoedit.js query string
		var group = item.toolManDragGroup;
		var list = group.element.parentNode;
		var id = list.getAttribute("id");
		if (id == null) return;
		var maval = window.location;
		var zpatt = new RegExp("(.*)(/)(.*)");
		var zzresult = zpatt.exec(maval);
		var zresult = zzresult[3];
		var yresult = zzresult[1];
		var xfirst = list.innerHTML;
		xfirst = xfirst.replace(/<li.*?>/g,"\\\*");
		xfirst = xfirst.replace(/<\/li>/g,"\n");
	
		//This is the autoedit.js query string
		var xresult = yresult + "?title=" + zresult + "&action=edit&autoclick=wpSave&autominor=true&autoedit=s~" + xfirst + "~";
		group.register("dragend", function() {
			var tsecond = junkdrawer.serializeList(list);
			xresult += tsecond + "~g";
			xresult = xresult.replace(/\n\n/g,"\n");
			xresult = xresult.replace(/\n/g,"\\\n");
			
			//Refresh with the new autoedit.js query string
			window.location = xresult;
			})
	}
	</script>');
	return true;
}
 
function efSetupListDrag() {
	global $wgHooks;
	$wgHooks['BeforePageDisplay'][] = 'efListDrag';
}
