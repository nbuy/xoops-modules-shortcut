<?php
// $Id: xoops_version.php,v 1.1 2006/02/23 12:38:43 nobu Exp $
// 
$modversion =
      array('name' => _MI_SHORTCUT_NAME,
	    'version' => 0.1,
	    'description' => _MI_SHORTCUT_DESC,
	    'author' => "Nobuhiro YASUTOMI <nobuhiro.yasutomi@nifty.ne.jp>",
	    'credits' => "Nobuhiro YASUTOMI <br/>http://mysite.ddo.jp/",
	    'help' => '',
	    'license' => "modified BSD",
	    'official' => 0,
	    'image' => "shortcut_slogo.png",
	    'dirname' => "shortcut");

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
//$modversion['sqlfile']['postgresql'] = "sql/pgsql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "shortcut";
//$modversion['tables'][1] = "";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
//$modversion['adminmenu'] = "admin/menu.php";
?>