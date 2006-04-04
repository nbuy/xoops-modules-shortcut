<?php
// $Id: index.php,v 1.2 2006/04/04 15:01:51 nobu Exp $

include("../../../mainfile.php");
include(XOOPS_ROOT_PATH."/include/cp_functions.php");
if ( file_exists( "../language/" . $xoopsConfig['language'] . "/admin.php" ) ) {
    include "../language/" . $xoopsConfig['language'] . "/admin.php";
}

$op = isset($_GET['op'])?$_GET['op']:'list';

define('SHORT', $xoopsDB->prefix("shortcut"));
$myts =& MyTextSanitizer::getInstance();
function q($x) { return "'".addslashes($x)."'"; }
if ($op == 'store') {
    $cutid = q(substr($myts->oopsStripSlashesGPC($_POST['cutid']),0,16));
    $prev = q($myts->oopsStripSlashesGPC($_POST['prev']));
    $url = q($myts->oopsStripSlashesGPC($_POST['url']));
    $active = empty($_POST['active'])?0:1;
    $now = time();
    if (empty($_POST['prev'])) { // new
	$res = $xoopsDB->query("INSERT INTO ".SHORT." (cutid, url, active, mdate) VALUES($cutid, $url, $active, $now)");
    } elseif (!empty($cutid)) {	// update
	$values = "url=$url, mdate=$now";
	if ($cutid != $prev) $values .= ", cutid=$cutid";
	$res = $xoopsDB->query("UPDATE ".SHORT." SET $values WHERE cutid=$prev");
    }
    echo $xoopsDB->error();
    if ($res) {
	redirect_header("index.php", 1, _AM_DBUPDATED);
	exit;
    }
    $op = 'reg';
} elseif ($op == 'cdel') {
     $id = intval($_POST['id']);
     $xoopsDB->query("DELETE FROM $tbl WHERE hookid=$id");
     redirect_header("index.php", 1, _AM_DBUPDATED);
     exit;
}

xoops_cp_header();

switch ($op) {

 case 'list':
     echo "<h4>"._AM_SHORTCUT_LIST."</h4>\n";
     $res = $xoopsDB->query('SELECT * FROM '.SHORT.' ORDER BY cutid');
     $total = $xoopsDB->getRowsNum($res);
     echo "<table class='outer' cellpadding='4' border='0' cellspacing='1'>\n";
     echo "<tr><th>".join('</th><th>', array(_AM_SHORTCUT_ID,_AM_UPDATE_TIME,_AM_SHORTCUT_URL,_AM_SHORTCUT_ACT,_AM_SHORTCUT_REF,_AM_SHORTCUT_OP))."</th></tr>\n";
     if ($total) {
	 $n = 0;
	 $n = 0;
	 if (file_exists(XOOPS_ROOT_PATH._AM_SHORTCUT_BASE.'/index.php')) {
	     $base = XOOPS_URL._AM_SHORTCUT_BASE;
	 } else {
	     $base = XOOPS_URL.'/modules/'.basename(dirname(dirname(__FILE__))).'/';
	 }
	 XOOPS_URL._AM_SHORTCUT_BASE;
	 while($data = $xoopsDB->fetchArray($res)) {
	     $cutid = $data['cutid'];
	     $link = "$base/?$cutid";
	     $data['cutid'] = "<a href='$link'>$cutid</a>";
	     $data['mdate'] = formatTimestamp($data['mdate']);
	     $data['op'] = "<a href='index.php?op=edit&cutid=$cutid'/>".
		  _EDIT."</a> | <a href='index.php?op=del&cutid=$cutid'>".
		  _DELETE."</a>";
	     $data['active'] = $data['active']?_YES:_NO;
	     $url = $data['url'];
	     if (strlen($url)>40) {
		 $data['url']="<a href='$url' alt='$url'>".
		     substr($url, 0, 38)."..</a>";
	     } else {
		 $data['url']="<a href='$url'>$url</a>";
	     }
	     $bg = ++$n%2?"odd":"even";
	     echo "<tr class='$bg'><td>".join('</td><td>', $data)."</td></tr>\n";
	 }
     }
     echo "</table>\n";
     echo "<hr/>\n";

 case 'edit':
     $cutid = isset($_GET['cutid'])?substr($myts->oopsStripSlashesGPC($_GET['cutid']),0,16):'';
     $res = $xoopsDB->query('SELECT * FROM '.SHORT.' WHERE cutid='.q($cutid));
     $data = $xoopsDB->fetchArray($res);
     echo "<h4>".(empty($data)?_AM_SHORTCUT_NEW:_AM_SHORTCUT_EDIT)."</h4>";
     echo "<form action='index.php?op=store' method='post'>\n";
     echo "<table cellpadding='4' cellspacing='1' border='0' class='outer'>\n";
     echo "<tr><th>"._AM_SHORTCUT_ID."</th><td class='even'>".
	 "<input name='cutid' value='$cutid' size='16'/>".
	 "<input type='hidden' name='prev' value='$cutid'/>".
	 "</td></tr>\n";
     echo "<tr><th>"._AM_SHORTCUT_URL."</th><td class='even'><input name='url' value='".$data['url']."' size='60'/></td></tr>\n";
     if (isset($data['active']) && !$data['active']) {
	 $yes = "";
	 $no = " checked";
     } else {
	 $yes = " checked";
	 $no = "";
     }
     echo "<tr><th>"._AM_SHORTCUT_ACT."</th><td class='even'><input type='radio' name='active' value='1'$yes/>"._YES." &nbsp;<input type='radio' name='active' value='0'$no/>"._NO."</td></tr>\n";
     if (isset($data['refer'])) {
	 echo "<tr><th>"._AM_SHORTCUT_REF."</th><td class='even'>".$data['refer']."</td></tr>\n";
     }
     echo "</table>\n";
     echo "<div><input type='submit'/></div>\n";
     echo "</form>\n";
     break;

 case 'del':
     echo "<div class='confirmMsg'>Are you sure?</div>";
     break;
}

xoops_cp_footer();
?>