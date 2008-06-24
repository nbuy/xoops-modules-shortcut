<?php
# $Id: edit_func.php,v 1.1 2008/06/24 14:29:22 nobu Exp $

// toplevel
function root_links($uid) {
    global $xoopsDB;
    $res = $xoopsDB->query("SELECT scid,title,url FROM ".SHORTCUT." WHERE pscref=0 AND uid=$uid");
    $ret = array();
    while (list($id,$title,$url) = $xoopsDB->fetchRow($res)) {
	$ret[$id] = array('title'=>$title, 'url'=>$url);
    }
    return $ret;
}

function post_vars() {
    $myts =& MyTextSanitizer::getInstance();
    $data = array('mdate'=>time());
    foreach (explode(',', 'title,url,cutid,description,active,pscref,weight') as $key) {
	switch ($key) {
	case 'weight':
	case 'pscref':
	    $data[$key] = isset($_POST[$key])?intval($_POST[$key]):0;
	break;
	default:
	    $data[$key] = isset($_POST[$key])?$myts->stripSlashesGPC($_POST[$key]):'';
	    break;
	}
    }
    return $data;
}

function join_vars($data, $fname=false) {
    global $xoopsDB;
    $result = array();
    foreach ($data as $k=>$v) {
	if ($fname) {
	    $result[] = "`$k`=".$xoopsDB->quoteString($v);
	} else {
	    $result[] = $xoopsDB->quoteString($v);
	}
    }
    return join(',', $result);
}
?>
