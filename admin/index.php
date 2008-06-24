<?php
// $Id: index.php,v 1.6 2008/06/24 14:29:22 nobu Exp $

include '../../../include/cp_header.php';
include_once '../functions.php';
include_once '../edit_func.php';

$op = isset($_GET['op'])?$_GET['op']:'list';

define('SHORT', $xoopsDB->prefix("shortcut"));

if (isset($_POST['save'])) {
    $data = post_vars();
    $scid = intval($_POST['scid']);
    if (!$scid) {		// new
	$data['uid'] = 0;
	$sql = "INSERT INTO ".SHORTCUT."(".join(',',array_keys($data)).")VALUES(".join_vars($data).")";
    } else {	// update
	$sql = "UPDATE ".SHORTCUT." SET ".join_vars($data, 1)." WHERE scid=".$scid;
    }
    $res = $xoopsDB->query($sql);
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
	 if (file_exists(XOOPS_ROOT_PATH._SC_SCRIPT_HOOK)) {
	     $base = XOOPS_URL._SC_SCRIPT_HOOK."/%s";
	 } elseif (file_exists(XOOPS_ROOT_PATH._SC_SCRIPT_BASE.'/index.php')) {
	     $base = XOOPS_URL._SC_SCRIPT_BASE."?%s";
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
     $xoopsTpl->assign('link', $data);
     $xoopsTpl->assign('pscrefs', root_links(0));
     $xoopsTpl->assign('active_status', explode(',', _MD_FORM_ACTIVE_VALUE));
     echo $xoopsTpl->fetch("db:shortcut_register.html");
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