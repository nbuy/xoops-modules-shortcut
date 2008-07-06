<?php
# $Id: functions.php,v 1.3 2008/07/06 07:36:56 nobu Exp $

if (!defined('_SC_ACTIVE_PUBLIC')) {

    define('_SC_ACTIVE_NONE', 0);
    define('_SC_ACTIVE_PUBLIC', 1);
    define('_SC_ACTIVE_PRIVATE', 2);
    define("_SC_SCRIPT_BASE","/sc");
    define("_SC_SCRIPT_HOOK","/sc.php");
    define("SHORTCUT", $xoopsDB->prefix('shortcut'));

    function eval_url($url)
    {
	if (!preg_match('/^\w+:\/\//', $url)) { // relative path
	    $url = XOOPS_URL.preg_replace('/^\[(\w+)\]/', '/modules/$1/', $url);
	}
	return $url;
    }

    function shortcut_script() {
    	 if (file_exists(XOOPS_ROOT_PATH._SC_SCRIPT_HOOK)) {
	     $base = XOOPS_URL._SC_SCRIPT_HOOK."/";
	 } elseif (file_exists(XOOPS_ROOT_PATH._SC_SCRIPT_BASE.'/index.php')) {
	     $base = XOOPS_URL._SC_SCRIPT_BASE."?";
	 } else {
	     $base = XOOPS_URL.'/modules/'.basename(dirname(__FILE__))."/sc.php/";
	 }
	 return $base;
    }

    function shortcut_links($uid, &$thispage, $active=_SC_ACTIVE_PUBLIC) {
	global $xoopsDB;
	$port = $_SERVER['SERVER_PORT'];
	$thispage = ($port==443?'https':'http').'://'.$_SERVER['SERVER_NAME'].(($port==80||$port==443)?'':$port).$_SERVER['REQUEST_URI'];	$cond = "active IN ($active)";
	if (isset($_GET['uid'])) {
	    $cond .= " AND uid=".intval($_GET['uid']);
	} elseif ($uid) {
	    $cond = "uid=$uid";
	} else {
	    $cond .= " AND uid=0";
	}
	$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("shortcut")." WHERE ".$cond." ORDER BY pscref,weight");
	$myts =& MyTextSanitizer::getInstance();

	$items = array();
	$links = array();
	$marked = false;
	while ($row = $xoopsDB->fetchArray($result)) {
	    $pscref = $row['pscref'];
	    $url = $row['uri'] = htmlspecialchars(eval_url($row['url']));
	    $current = ($url==$thispage);
	    if (!$current && substr($url, strlen($url)-1, 1)=='/' &&
		$url.'index.php' == $thispage) $current = true;
	    $row['title'] = htmlspecialchars($row['title']);
	    $row['modified'] = formatTimestamp($row['mdate']);
	    $row['owner'] = $uid?($row['uid']==$uid):false;
	    $row['description'] = $myts->displayTarea($row['description'], 0);
	    $row['current'] = $row['selected'] =$current;
	    $id = $row['scid'];
	    $items[$id] = $row;
	    if ($row['pscref']) {
		$pid = $row['pscref'];
		$items[$pid]['sub'][] = &$items[$id];
		if ($current) $items[$pid]['selected'] = true;
	    } else {
		$links[] = &$items[$id];
	    }
	    $marked |= $current;
	}
	if ($marked) $thispage = ''; // bookmarked this page
	return $links;
    }
}
?>
