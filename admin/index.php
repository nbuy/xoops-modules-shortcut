<?php
// $Id: index.php,v 1.5 2008/06/22 08:28:40 nobu Exp $

include '../../../include/cp_header.php';
include_once '../functions.php';

$op = isset($_GET['op'])?$_GET['op']:'list';

define('SHORT', $xoopsDB->prefix("shortcut"));
$myts =& MyTextSanitizer::getInstance();
function q($x) { return "'".addslashes($x)."'"; }
if ($op == 'store') {
    $data = array();
    foreach (array('cutid', 'title', 'url') as $key) {
	$data[$key] = $xoopsDB->quoteString($myts->stripSlashesGPC($_POST[$key]));
    }
    $data['active'] = empty($_POST['active'])?0:1;
    $scid = intval($_POST['scid']);
    $cutid = $myts->stripSlashesGPC($_POST['cutid']);
    $data['mdate'] = time();
    if (!$scid) {		// new
	$res = $xoopsDB->query("INSERT INTO ".SHORT." (".join(',', array_keys($data)).") VALUES(".join(',', $data).")");
    } elseif (!empty($cutid)) {	// update
	foreach ($data as $key => $val) {
	    $data[$key] = $key.'='.$val;
	}
	$res = $xoopsDB->query("UPDATE ".SHORT." SET ".join(',', $data)." WHERE scid=".$scid);
    }
    if ($res) {
	redirect_header("index.php", 1, _AM_DBUPDATED);
	exit;
    }
    $op = 'reg';
} elseif ($op == 'delete') {
    $xoopsDB->query("DELETE FROM ".SHORT." WHERE scid=".intval($_POST['scid']));
    redirect_header("index.php", 1, _AM_DBUPDATED);
    exit;
}

if( ! empty( $_GET['lib'] ) ) {
    global $mydirpath;
    $mydirpath = dirname(dirname(__FILE__));
    $mydirname = basename($mydirpath);
    // common libs (eg. altsys)
    $lib = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $_GET['lib'] ) ;
    $page = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] ) ;
    
    if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ) ) {
	include XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ;
	} else if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ) ) {
	include XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ;
    } else {
	die( 'wrong request' ) ;
    }
    exit;
}

xoops_cp_header();

include "mymenu.php";

switch ($op) {

 case 'list':
     echo "<h4>"._AM_SHORTCUT_LIST."</h4>\n";

     $ents = array('cutid'=>_AM_SHORTCUT_ID, 'uid'=>_AM_SHORTCUT_USER,
		   'mdate'=>_AM_UPDATE_TIME, 'title'=>_AM_SHORTCUT_TITLE,
		   'url'=>_AM_SHORTCUT_URL,  'active'=>_AM_SHORTCUT_ACT,
		   'refer'=>_AM_SHORTCUT_REF,'weight'=>_AM_SHORTCUT_WEIGHT);
     $res = $xoopsDB->query('SELECT * FROM '.SHORT.' ORDER BY cutid');
     $total = $xoopsDB->getRowsNum($res);
     echo "<table class='outer' cellpadding='4' border='0' cellspacing='1'>\n";
     echo "<tr><th>".join('</th><th>', $ents)."</th><th>"._AM_SHORTCUT_OP."</th></tr>\n";
     if ($total) {
	 $n = 0;
	 if (file_exists(XOOPS_ROOT_PATH._AM_SHORTCUT_HOOK)) {
	     $base = XOOPS_URL._AM_SHORTCUT_HOOK."/%s";
	 } elseif (file_exists(XOOPS_ROOT_PATH._AM_SHORTCUT_BASE.'/index.php')) {
	     $base = XOOPS_URL._AM_SHORTCUT_BASE."?%s";
	 } else {
	     $base = XOOPS_URL.'/modules/'.basename(dirname(dirname(__FILE__)))."?%s";
	 }
	 while($data = $xoopsDB->fetchArray($res)) {
	     $scid = $data['scid'];
	     $cutid = $data['cutid'];
	     $link = sprintf($base, $cutid);
	     $data['cutid'] = "<a href='$link'>$cutid</a>";
	     $data['mdate'] = formatTimestamp($data['mdate']);
	     $data['uid'] = $data['uid']?xoops_getLinkedUnameFromId($data['uid']):_AM_SHORTCUT_GLOBAL;
	     $op = "<a href='index.php?op=edit&scid=$scid'/>".
		  _EDIT."</a> | <a href='index.php?op=del&scid=$scid'>".
		  _DELETE."</a>";
	     $data['active'] = $data['active']?_YES:_NO;
	     $url = $data['url'];
	     $aurl = eval_url($url);
	     if (strlen($url)>40) $url = substr($url, 0, 38)."..";
	     $data['url']="<a href='$aurl'>$url</a>";
	     $bg = ++$n%2?"odd":"even";
	     echo "<tr class='$bg'>";
	     foreach ($ents as $key=>$lab) {
		 echo "<td>".$data[$key]."</td>";
	     }
	     echo "<td>$op</td></td></tr>\n";
	 }
     }
     echo "</table>\n";
     echo "<hr/>\n";

 case 'edit':
     $scid = isset($_GET['scid'])?intval($_GET['scid']):0;
     $res = $xoopsDB->query('SELECT * FROM '.SHORT.' WHERE scid='.$scid);
     $data = $xoopsDB->fetchArray($res);
     echo "<h4>".(empty($data)?_AM_SHORTCUT_NEW:_AM_SHORTCUT_EDIT)."</h4>";
     echo "<form action='index.php?op=store' method='post'>\n";
     echo "<table cellpadding='4' cellspacing='1' border='0' class='outer'>\n";
     echo "<tr><td class='head'>"._AM_SHORTCUT_ID."</td><td class='even'>".
	 "<input type='hidden' name='scid' value='$scid'/>".
	 "<input name='cutid' value='".$data['cutid']."' size='16'/>".
	 "</td></tr>\n";
     echo "<tr><td class='head'>"._AM_SHORTCUT_TITLE."</td><td class='even'>".
	 "<input name='title' value='".$data['title']."' size='40'/>".
	 "</td></tr>\n";
     echo "<tr><td class='head'>"._AM_SHORTCUT_URL."</td><td class='even'><input name='url' value='".$data['url']."' size='60'/></td></tr>\n";
     if (isset($data['active']) && !$data['active']) {
	 $yes = "";
	 $no = " checked";
     } else {
	 $yes = " checked";
	 $no = "";
     }
     echo "<tr><td class='head'>"._AM_SHORTCUT_ACT."</td><td class='even'><input type='radio' name='active' value='1'$yes/>"._YES." &nbsp;<input type='radio' name='active' value='0'$no/>"._NO."</td></tr>\n";
     if (isset($data['refer'])) {
	 echo "<tr><td class='head'>"._AM_SHORTCUT_REF."</td><td class='even'>".$data['refer']."</td></tr>\n";
     }
     echo "</table>\n";
     echo "<div><input type='submit'/></div>\n";
     echo "</form>\n";
     break;

 case 'del':
     $scid = intval($_GET['scid']);
     $res=$xoopsDB->query('SELECT url FROM '.SHORT.' WHERE scid='.$scid);
     list($url)=$xoopsDB->fetchRow($res);
     echo "<div class='confirmMsg'><p>$id --&gt; ".htmlspecialchars($url).
	 "<br/>"._AM_SHORTCUT_DEL."</p>
<form action='index.php?op=delete' method='POST'>
<input type='submit' value='"._DELETE."'/>
<input type='hidden' name='scid' value='$scid'/>
</form><br/>
</div>";
     break;
}

xoops_cp_footer();
?>