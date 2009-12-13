<?php
// $Id: shortcut_block.php,v 1.4 2009/12/13 11:25:00 nobu Exp $

function b_shortcut_show($options) {
    global $xoopsDB, $xoopsUser, $xoopsModule;
    $myts =& MyTextSanitizer::getInstance();
    include_once dirname(dirname(__FILE__))."/functions.php";
    $mydirname = basename(dirname(dirname(__FILE__)));

    $block = array();
    $action = '';

    if ($options[0]) {
	if (!is_object($xoopsUser)) return $block;
	$uid = $xoopsUser->getVar('uid');
	if (is_object($xoopsModule) && $xoopsModule->getVar('dirname')==$mydirname) {
	    $module =& $xoopsModule;
	} else {
	    $module_handler =& xoops_gethandler('module');
	    $module =& $module_handler->getByDirname($mydirname);
	}
	if ($xoopsUser->isAdmin($module->getVar('mid'))) {
	    $action = XOOPS_URL."/modules/$mydirname/register.php";
	}
    } else {
	$uid = 0;
	$action = XOOPS_URL."/modules/$mydirname/admin/index.php?op=edit";
    }
    
    $block['links'] = shortcut_links($uid, $thispage);
    $block['action'] = $action;
    $block['thispage'] = htmlspecialchars($thispage);
    return $block;
}

function b_shortcut_edit($options) {
    $buf = _BL_SHORTCUT_TYPE." <select name='options[]'>\n";
    foreach (explode(',', _BL_SHORTCUT_TYPE_SELECT) as $k=>$v) {
	$buf .= "<option value='$k'".($k==$options[0]?" selected='selected'":'').">$v</option>\n";
    }
    $buf .= "</select>\n";
    return $buf;
}
?>