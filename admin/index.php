<?php
// $Id: index.php,v 1.8 2008/07/06 07:47:21 nobu Exp $

include '../../../include/cp_header.php';
include_once '../functions.php';
include_once '../edit_func.php';

$op = isset($_GET['op'])?$_GET['op']:'list';

define('SHORT', $xoopsDB->prefix("shortcut"));

if (isset($_POST['save'])) {
    $data = post_vars();
    $scid = intval($_POST['scid']);
    if (store_entry($data, $scid, 0)) {
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

     $ents = array('title'=>_AM_SHORTCUT_TITLE, 'url'=>_AM_SHORTCUT_URL,
		   'modified'=>_AM_UPDATE_TIME, 'cutid'=>_AM_SHORTCUT_ID,
		   'active'=>_AM_SHORTCUT_ACT,  'refer'=>_AM_SHORTCUT_REF,
		   'weight'=>_AM_SHORTCUT_WEIGHT);
     echo "<table class='outer' cellpadding='4' border='0' cellspacing='1'>\n";
     echo "<tr><th>".join('</th><th>', $ents)."</th><th>"._AM_SHORTCUT_OP."</th></tr>\n";

     $links = shortcut_links(0, $thispage, _SC_ACTIVE_NONE.","._SC_ACTIVE_PUBLIC.","._SC_ACTIVE_PRIVATE);
     foreach ($links as $k=>$data) {
	 echo display_entry('', $data, $ents);
	 if (!empty($data['sub'])) {
	     foreach ($data['sub'] as $data) {
		 echo display_entry(' &nbsp;&nbsp; ',  $data, $ents);
	     }
	 }
     }
     echo "</table>\n";
     echo "<hr/>\n";

 case 'edit':
     $scid = isset($_GET['scid'])?intval($_GET['scid']):0;
     $res = $xoopsDB->query('SELECT * FROM '.SHORT.' WHERE scid='.$scid);
     if ($res && $xoopsDB->getRowsNum($res)) {
	 $data = $xoopsDB->fetchArray($res);
     } else {
	 $data = post_vars();
     }
     $xoopsTpl->assign('link', $data);
     $xoopsTpl->assign('pscrefs', root_links(0, $scid));
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

function display_entry($pre, &$data, &$ents) {
    static $n=0;
    $scid = $data['scid'];
    $cutid = $data['cutid'];
    $link = shortcut_script().$cutid;
    $data['cutid'] = "<a href='$link'>$cutid</a>";
    $op = "<a href='index.php?op=edit&scid=$scid'/>".
	_EDIT."</a> | <a href='index.php?op=del&scid=$scid'>".
	_DELETE."</a>";
    $data['active'] = $data['active']?_YES:_NO;
    $url = $data['url'];
    $aurl = eval_url($url);
    if (strlen($url)>40) $url = substr($url, 0, 38)."..";
    $data['url']="<a href='$aurl'>$url</a>";
    $bg = ++$n%2?"odd":"even";
    $buf = "<tr class='$bg'>";
    foreach ($ents as $key=>$lab) {
	$buf .= "<td>".($key=='title'?$pre:'').$data[$key]."</td>";
    }
    $buf .= "<td>$op</td></td></tr>\n";
    return $buf;
}
?>
