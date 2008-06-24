<?php
# $Id: functions.php,v 1.2 2008/06/24 14:29:22 nobu Exp $

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
}
?>
