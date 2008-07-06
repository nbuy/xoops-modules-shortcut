<?php
// Module Info
// $Id: modinfo.php,v 1.4 2008/07/06 07:36:56 nobu Exp $

// The name of this module
define("_MI_SHORTCUT_NAME","ショートカット");

// A brief description of this module
define("_MI_SHORTCUT_DESC","URLをリダイレクトして短くする");

// Sub menus
define("_MI_SHORTCUT_REGISTER","ショートカットの登録");

define("_MI_SHORTCUT_HELP","Shortcut について");

// Names of blocks for this module (Not all module has blocks)
define("_MI_SHORTCUT_BLOCK_MENU", "リンクメニュー");
define("_MI_SHORTCUT_BLOCK_MENU_DESC", "システムのショートカットをメニューに表示する");
define("_MI_SHORTCUT_BLOCK_MYMENU", "マイメニュー");
define("_MI_SHORTCUT_BLOCK_MYMENU_DESC", "個人のショートカットをメニュー表示する");

// for altsys 
if (!defined('_MD_A_MYMENU_MYTPLSADMIN')) {
    define('_MD_A_MYMENU_MYTPLSADMIN','テンプレート管理');
    define('_MD_A_MYMENU_MYBLOCKSADMIN','ブロック/アクセス管理');
    define('_MD_A_MYMENU_MYPREFERENCES','一般設定');
}
?>