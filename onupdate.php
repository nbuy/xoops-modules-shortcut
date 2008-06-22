<?php
# eguide module onUpdate proceeding.
# $Id: onupdate.php,v 1.1 2008/06/22 08:28:40 nobu Exp $

global $xoopsDB;

define("SHORTCUT", $xoopsDB->prefix('shortcut'));
// shortcut add in 1.0
$res = $xoopsDB->query("SELECT scid FROM ".SHORTCUT, 1); // change primary
if (empty($res) && $xoopsDB->errno()) { // check exists
    $xoopsDB->query("ALTER TABLE ".SHORTCUT." DROP PRIMARY KEY");
    $xoopsDB->query("ALTER TABLE ".SHORTCUT." ADD scid INT NOT NULL FIRST");
    $res = $xoopsDB->query("SELECT cutid FROM ".SHORTCUT);
    $n = 0;
    while (list($cutid) = $xoopsDB->fetchRow($res)) {
	$xoopsDB->query("UPDATE ".SHORTCUT." SET scid=".(++$n)." WHERE cutid=".$xoopsDB->quoteString($cutid));
    }
    $xoopsDB->query("ALTER TABLE ".SHORTCUT." ADD PRIMARY KEY (scid)");
    $xoopsDB->query("ALTER TABLE ".SHORTCUT." CHANGE scid scid integer NOT NULL AUTO_INCREMENT");
}
if (add_field(SHORTCUT, 'pscref', "integer NOT NULL default 0", 'cutid')) {
    add_field(SHORTCUT, 'uid', "integer NOT NULL default 0", 'pscref');
    add_field(SHORTCUT, 'title', "varchar(80) NOT NULL default ''", 'mdate');
    add_field(SHORTCUT, 'description', "text NOT NULL default ''", 'title');
    add_field(SHORTCUT, 'weight', "integer NOT NULL default 0", 'refer');
    $xoopsDB->query("ALTER TABLE ".SHORTCUT." MODIFY url TEXT");
    report_message(" Change field define to text: <b>".SHORTCUT.".url</b>");
}

function report_message($msg) {
    global $msgs;		// module manager's variable
    static $first = true;
    if ($first) {
	$msgs[] = "Update Database...";
	$first = false;
    }
    $msgs[] = "&nbsp;&nbsp; $msg";
}

function add_field($table, $field, $type, $after) {
    global $xoopsDB, $myprefix;
    $res = $xoopsDB->query("SELECT $field FROM $table", 1);
    if (empty($res) && $xoopsDB->errno()) { // check exists
	if ($after) $after = "AFTER $after";
	$res = $xoopsDB->query("ALTER TABLE $table ADD $field $type $after");
    } else return false;
    report_message(" Add new field: <b>$table.$field</b>");
    if (!$res) {
	echo "<span style='color: red; font: bold;'>".$xoopsDB->error()."</span>\n";
    }
    return $res;
}
?>