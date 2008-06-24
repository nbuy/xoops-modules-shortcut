<?php
// Module Info
// $Id: modinfo.php,v 1.3 2008/06/24 14:29:22 nobu Exp $

// The name of this module
define("_MI_SHORTCUT_NAME","ショートカット");

// A brief description of this module
define("_MI_SHORTCUT_DESC","URLをリダイレクトして短くする");

define("_MI_SHORTCUT_BLOCK_MENU", "マイメニュー");
define("_MI_SHORTCUT_BLOCK_MENU_DESC", "リンクをメニュー表示する");

// for altsys 
if (!defined('_MD_A_MYMENU_MYTPLSADMIN')) {
    define('_MD_A_MYMENU_MYTPLSADMIN','テンプレート管理');
    define('_MD_A_MYMENU_MYBLOCKSADMIN','ブロック/アクセス管理');
    define('_MD_A_MYMENU_MYPREFERENCES','一般設定');
}
?>