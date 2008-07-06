<?php
# shortcut user main page
# $Id: index.php,v 1.2 2008/07/06 07:36:56 nobu Exp $

include "../../mainfile.php";
include "functions.php";

include XOOPS_ROOT_PATH."/header.php";

$xoopsOption['template_main'] = 'shortcut_index.html';

$uid = is_object($xoopsUser)?$xoopsUser->getVar('uid'):0;

$xoopsTpl->assign('links', shortcut_links($uid, $thispage));
$xoopsTpl->assign('sc_script', shortcut_script());

include XOOPS_ROOT_PATH."/footer.php";
?>
