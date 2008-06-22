<?php
# shortcut user main page
# $Id: index.php,v 1.1 2008/06/22 08:28:40 nobu Exp $

include "../../mainfile.php";
include "functions.php";

include XOOPS_ROOT_PATH."/header.php";

$xoopsOption['template_main'] = 'shortcut_index.html';

$uid = is_object($xoopsUser)?$xoopsUser->getVar('uid'):0;
$cond = "active="._SC_ACTIVE_PUBLIC;
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
while ($row = $xoopsDB->fetchArray($result)) {
    $pscref = $row['pscref'];
    $row['url'] = htmlspecialchars(eval_url($row['url']));
    $row['title'] = htmlspecialchars($row['title']);
    $row['mdate'] = formatTimestamp($row['mdate']);
    $row['owner'] = $uid?($row['uid']==$uid):false;
    $row['description'] = $myts->displayTarea($row['description'], 0);
    $id = $row['scid'];
    $items[$id] = $row;
    if ($row['pscref']) {
	$pid = $row['pscref'];
	$items[$pid]['sub'][] = &$items[$id];
    } else {
	$links[] = &$items[$id];
    }
}

if (file_exists(XOOPS_ROOT_PATH."/sc.php")) {
    $script = XOOPS_URL."/sc.php/";
} else {
    $script = "sc.php/";
}
$xoopsTpl->assign('links', $links);
$xoopsTpl->assign('sc_script', $script);

include XOOPS_ROOT_PATH."/footer.php";
?>
