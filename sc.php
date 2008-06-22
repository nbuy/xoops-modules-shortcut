<?php
// $Id: sc.php,v 1.6 2008/06/22 08:28:40 nobu Exp $

// NOTE: this file put(copied) on short path length directory
// e.g.  XOOPS_ROOT_PATH/sc.php

if (file_exists('./mainfile.php')) include './mainfile.php';
elseif (file_exists('../mainfile.php')) include '../mainfile.php';
else include '../../mainfile.php';
include XOOPS_ROOT_PATH.'/modules/shortcut/functions.php';

if (empty($_GET)) {
    $key = basename($_SERVER["REQUEST_URI"]);
} else {
    list($key) = array_keys($_GET);
}
$qkey = "'".addslashes(substr($key,0,16))."'";
$res = $xoopsDB->query('SELECT url FROM '.$xoopsDB->prefix('shortcut').' WHERE active AND cutid='.$qkey);
list($url) = $xoopsDB->fetchRow($res);
if (empty($url)) redirect_header(XOOPS_URL.'/', 3, _NOPERM);
$xoopsDB->queryF('UPDATE '.$xoopsDB->prefix('shortcut').' SET refer=refer+1 WHERE cutid='.$qkey);
header("Location: ".eval_url($url));
?>
