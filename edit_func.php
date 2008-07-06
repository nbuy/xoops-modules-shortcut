<?php
# $Id: edit_func.php,v 1.2 2008/07/06 07:36:56 nobu Exp $

// toplevel
function root_links($uid, $scid) {
    global $xoopsDB;
    $res = $xoopsDB->query("SELECT scid,title,url FROM ".SHORTCUT." WHERE pscref=0 AND uid=$uid AND scid<>$scid");
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

function store_entry(&$data, $scid, $uid) {
    global $xoopsDB;
    if (empty($data['weight'])) {
	$res = $xoopsDB->query("SELECT MAX(weight) FROM ".SHORTCUT." WHERE uid=$uid AND pscref=".$data['pscref']." GROUP BY pscref");
	list($weight)=$xoopsDB->fetchRow($res);
	if (empty($weight)) $weight = 0;
	$data['weight']=$weight+1; // add tail list
    } else {			// conflict check
	$res = $xoopsDB->query("SELECT count(scid) FROM ".SHORTCUT." WHERE scid<>$scid AND weight=".$data['weight']." AND pscref=".$data['pscref']);
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
    return $xoopsDB->query($sql);
}
?>
