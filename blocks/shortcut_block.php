<?php
// $Id: shortcut_block.php,v 1.1 2008/06/22 08:28:40 nobu Exp $

function b_shortcut_show($options) {
    global $xoopsDB, $xoopsUser;
    $myts =& MyTextSanitizer::getInstance();
    include_once dirname(dirname(__FILE__))."/functions.php";

    $port = $_SERVER['SERVER_PORT'];
    $thispage = ($port==443?'https':'http').'://'.$_SERVER['SERVER_NAME'].(($port==80||$port==443)?'':$port).$_SERVER['REQUEST_URI'];

    $mydirname = basename(dirname(dirname(__FILE__)));
    $cond = "active="._SC_ACTIVE_PUBLIC." AND uid=0";
    if (is_object($xoopsUser)) {
	$cond = "($cond) OR (active<>"._SC_ACTIVE_NONE." AND uid=".$xoopsUser->getVar('uid').")";
    }
    $result = $xoopsDB->query("SELECT scid,pscref,url,title,uid FROM ".$xoopsDB->prefix("shortcut")." WHERE $cond ORDER BY uid,pscref,weight");
    $items = array();
    $links = array();
    while ($row = $xoopsDB->fetchArray($result)) {
	$row['uri'] = $url = eval_url($row['url']);
	$current = ($url==$thispage);
	if (!$current && substr($url, strlen($url)-1, 1)=='/' &&
	    $url.'index.php' == $thispage) $current = true;
	$id = $row['scid'];
	$row['current'] = $current;
	$items[$id] = $row;

	if ($row['pscref']) {
	    $pid = $row['pscref'];
	    $items[$pid]['sub'][] = &$items[$id];
	    if ($current) $items[$pid]['selected'] = true;
	} else {
	    $links[] = &$items[$id];
	}
	if ($row['uid'] && $current) $thispage = ''; // already marked
    }
    return array('links'=>$links, 'thispage'=>$thispage,
		 'action'=>XOOPS_URL."/modules/$mydirname/register.php");
}

function b_shortcut_edit($options) {
    return '';
}
?>