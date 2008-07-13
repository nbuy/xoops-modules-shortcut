<?php
// $Id: index.php,v 1.9 2008/07/13 12:21:14 nobu Exp $

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
} elseif (isset($_POST['import'])) {
    $myts =& MyTextSanitizer::getInstance();
    if (import_module_menu($myts->stripSlashesGPC($_POST['import']), intval($_POST['weight']))) {
	redirect_header("index.php", 1, _AM_DBUPDATED);
	exit;
    } else {
	// import error
    }
} elseif ($op == 'delete') {
    $scid = intval($_POST['scid']);
    if ($scid) {
	$xoopsDB->query("DELETE FROM ".SHORT." WHERE scid=$scid");
	$xoopsDB->query("DELETE FROM ".SHORT." WHERE pscref=$scid");
    }
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

     $ents = array('title'=>_AM_SHORTCUT_TITLE,
		   'weight'=>_AM_SHORTCUT_WEIGHT, 'url'=>_AM_SHORTCUT_URL,
		   'modified'=>_AM_UPDATE_TIME, 'cutid'=>_AM_SHORTCUT_ID,
		   'active'=>_AM_SHORTCUT_ACT,  'refer'=>_AM_SHORTCUT_REF,
		   );
     $acts = explode(',', _MD_FORM_ACTIVE_VALUE);
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
     echo import_module_select_form();
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
     $res=$xoopsDB->query('SELECT cutid, url, title FROM '.SHORT.' WHERE scid='.$scid);
     list($cutid, $url, $title)=$xoopsDB->fetchRow($res);
     $res=$xoopsDB->query('SELECT count(scid) FROM '.SHORT.' WHERE pscref='.$scid);
     list($sub) = $xoopsDB->fetchRow($res);
     echo "<div class='confirm'><p>"._AM_SHORTCUT_TITLE." ".htmlspecialchars($title)." ($cutid)<br />"._AM_SHORTCUT_URL.' '.htmlspecialchars(eval_url($url))."</p>";
     if ($sub) echo "<p>"._AM_SHORTCUT_SUBLINKS." $sub</p>";
     echo "<p>"._AM_SHORTCUT_DEL."</p>";

     echo "<form action='index.php?op=delete' method='POST'>
<input type='submit' value='"._DELETE."'/>
<input type='hidden' name='scid' value='$scid'/>
</form><br/>
</div>";
     break;
}

xoops_cp_footer();

function display_entry($pre, &$data, &$ents) {
    static $n=0;
    global $acts;
    $scid = $data['scid'];
    $cutid = $data['cutid'];
    $link = shortcut_script().$cutid;
    $data['cutid'] = "<a href='$link'>$cutid</a>";
    $op = "<a href='index.php?op=edit&scid=$scid'/>".
	_EDIT."</a> | <a href='index.php?op=del&scid=$scid'>".
	_DELETE."</a>";
    $data['active'] = $acts[$data['active']];
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

function import_module_select_form() {
    $module_handler =& xoops_gethandler('module');
    $criteria = new Criteria('hasmain', 1);
    $criteria->setSort('weight');
    $modules =& $module_handler->getObjects($criteria);
    $buf = "<select name='import'>\n";
    foreach ($modules as $module) {
	$dirname = htmlspecialchars($module->getVar('dirname'));
	$name = htmlspecialchars($module->getVar('name'));
	$buf .= "<option value='$dirname'>$name</option>\n";
    }
    $buf .= "</select>";
    $form = "<form method='post'>"._AM_SHORTCUT_IMPORT." $buf ".
	_AM_SHORTCUT_WEIGHT." <input name='weight' size='2' value='0' /> &nbsp; <input type='submit' value='"._SUBMIT."' /></form>";
    return $form;
}

function import_module_menu($dirname, $weight) {
    global $xoopsConfig;
    $module_handler =& xoops_gethandler('module');
    $module =& $module_handler->getByDirname($dirname);
    if (!is_object($module) || !$module->getVar('hasmain')) return false;
    $path = XOOPS_ROOT_PATH.'/modules/'.$dirname;
    $lang = $path.'/language/'.$xoopsConfig['language'].'/modinfo.php';
    if (!file_exists($lang)) $lang = $path.'/language/english/modinfo.php';
    include_once $lang;

    global $modversion;
    include $path.'/xoops_version.php';
    $sub = $modversion['sub'];
    $url = XOOPS_URL."/modules/$dirname/";
    $id = store_new_links($url, $module->getVar('name'), 0, $weight);
    $n = 0;
    foreach ($sub as $k => $v) {
	store_new_links($url.$v['url'], $v['name'], $id, ++$n);
    }
    return true;
}

function store_new_links($url, $name, $pid, $weight) {
    global $xoopsDB;
    $url = normal_url($url);
    $now = time();
    $data = array('cutid'=>cutid_default($url),
		  'url'=>$url, 'uid'=>0,
		  'title'=>$name,
		  'pscref'=>$pid);
    foreach ($data as $k => $v) {
	$data[$k] = $xoopsDB->quoteString($v);
    }
    if (!$pid) {
	if (!$weight) {
	    $res = $xoopsDB->query("SELECT max(weight) FROM ".SHORT." WHERE pscref=0 AND uid=0");
	    list($weight) = $xoopsDB->fetchRow($res);
	    $weight++;
	}
    } else {
	if ($weight) {
	    $res = $xoopsDB->query("SELECT weight FROM ".SHORT." WHERE pscref=$pid AND weight=$weight AND uid=0");
	    if ($xoopsDB->getRowsNum($res)) {
		$res = $xoopsDB->query("UPDATE ".SHORT." SET weight=weight+1 WHERE pscref=$pid AND weight>=$weight AND uid=0");
	    }
	}
    }
    $data['weight'] = $weight;
    $data['mdate']=time();
    $res = $xoopsDB->query("INSERT INTO ".SHORT."(".join(',', array_keys($data)).")VALUES(".join(',', $data).") ");
    return $xoopsDB->getInsertID($res);
}
?>
