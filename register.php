<?php
# register bookmark
# $Id: register.php,v 1.6 2008/07/13 12:21:14 nobu Exp $

include "../../mainfile.php";
include "functions.php";
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
$msgs = array();

if (isset($_POST['save'])) {
    if (store_entry($data, $scid, $uid)) {
	redirect_header("index.php", 1, _MD_SHORTCUT_STOREOK);
	exit;
    }
    $msgs[] = _MD_SHORTCUT_STORENG.' - '.$xoopsDB->error();
}

include XOOPS_ROOT_PATH."/header.php";

$xoopsTpl->assign('errors', $msgs);
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
	$data['cutid'] = cutid_default($data['url'], $xoopsUser->getVar('uid'));
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

$xoopsTpl->assign('pscrefs', root_links($uid, $scid));
$xoopsTpl->assign('active_status', explode(',', _MD_FORM_ACTIVE_VALUE));

include XOOPS_ROOT_PATH."/footer.php";

?>
