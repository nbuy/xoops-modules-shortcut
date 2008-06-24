<?php
# register bookmark
# $Id: register.php,v 1.2 2008/06/24 14:29:22 nobu Exp $

include "../../mainfile.php";
include "edit_func.php";

if (!is_object($xoopsUser)) {
    $back = empty($_SERVER['HTTP_REFERER'])?XOOPS_URL.'/':$_SERVER['HTTP_REFERER'];
    redirect_header($back, 3, _NOPERM);
    exit;
}
$myts =& MyTextSanitizer::getInstance();
$uid = $xoopsUser->getVar('uid');

$scid = isset($_POST['scid'])?intval($_POST['scid']):0;
$data = post_vars();

if (isset($_POST['save'])) {
    if (empty($data['weight'])) {
	$res = $xoopsDB->query("SELECT MAX(weight) FROM ".SHORTCUT." WHERE pscref=".$data['pscref']." GROUP BY pscref");
	list($weight)=$xoopsDB->fetchRow($res);
	if (empty($weight)) $weight = 0;
	$data['weight']=$weight+1; // add tail list
    } else {			// conflict check
	$res = $xoopsDB->query("SELECT count(scid) FROM ".SHORTCUT." WHERE weight=".$data['weight']." AND pscref=".$data['pscref']);
	list($count)=$xoopsDB->fetchRow($res);
	if ($count) {		// insert order
	    $res = $xoopsDB->query("UPDATE ".SHORTCUT." SET weight=weight+1 WHERE weight>=".$data['weight']." AND pscref=".$data['pscref']);
	}
    }

    if (empty($scid)) {
	$data['uid']=$uid;
	$sql = "INSERT INTO ".SHORTCUT."(".join(',',array_keys($data)).")VALUES(".join_vars($data).")";
    } else {
	$sql = "UPDATE ".SHORTCUT." SET ".join_vars($data, 1)." WHERE scid=".$scid;
    }
    $res = $xoopsDB->query($sql);
    if ($res) {
	redirect_header("index.php", 1, _MD_SHORTCUT_STOREOK);
	exit;
    }
}

include XOOPS_ROOT_PATH."/header.php";

$data['url'] = normal_url($data['url']);

if (!isset($_POST['scid'])) {		// generate uniq id if empty
    if (isset($_GET['scid'])) {
	$scid = intval($_GET['scid']);
	$cond = "scid=".$scid;
	if (!$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
	    $cond .= " AND uid=".$xoopsUser->getVar('uid');
	}
	$res = $xoopsDB->query('SELECT * FROM '.SHORTCUT.' WHERE '.$cond);
	$data = $xoopsDB->fetchArray($res);
    } else {
	$rand = "";
	do {
	    $cutid = substr(base64_encode(md5($xoopsUser->getVar('uid').'-'.$data['url'].$rand, true)), 0, 5);
	    $rand = rand(0,1000);
	    $res = $xoopsDB->query("SELECT cutid WHERE cutid=".$xoopsDB->quoteString($cutid));
	} while ($res && $xoopsDB->getRowsNum($res));
	$data['active'] = _SC_ACTIVE_PUBLIC;
	$data['cutid'] = $cutid;
    }
}

$xoopsOption['template_main'] = 'shortcut_register.html';

$xoopsTpl->assign('link', $data);
$xoopsTpl->assign('active_status', explode(',', _MD_FORM_ACTIVE_VALUE));
$res = $xoopsDB->query("SELECT scid,title,url FROM ".SHORTCUT." WHERE pscref=0 AND uid=$uid");
echo $xoopsDB->error();
$pscrefs = array();
while (list($id,$title,$url) = $xoopsDB->fetchRow($res)) {
    $pscrefs[$id] = array('title'=>$title, 'url'=>$url);
}

$xoopsTpl->assign('pscrefs', root_links($uid));
$xoopsTpl->assign('active_status', explode(',', _MD_FORM_ACTIVE_VALUE));

include XOOPS_ROOT_PATH."/footer.php";

// make normalized URL
function normal_url($url) {
    $url = preg_replace("/^".preg_quote(XOOPS_URL, '/').'/', '', $url); // relative path
    if (preg_match('/^\/modules\/([^\/]+)\//', $url, $d)) {
	$dirname = $d[1];
	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->getByDirname($dirname);
	if ($module && $module->getVar('isactive')) {
	    $url = preg_replace('/^\/modules\/([^\/]+)\//', "[$dirname]", $url);
	}
    }
    return $url;
}
?>
