<?php
// Module Info
// $Id: modinfo.php,v 1.3 2008/07/06 07:36:56 nobu Exp $

// The name of this module
define("_MI_SHORTCUT_NAME","ShortCut");

// A brief description of this module
define("_MI_SHORTCUT_DESC","URL redirects for shorter strings");

// Sub menus
define("_MI_SHORTCUT_REGISTER","Register Shortcut");

define("_MI_SHORTCUT_HELP","Abuut shortcut");

// Names of blocks for this module (Not all module has blocks)
define("_MI_SHORTCUT_BLOCK_MENU", "Link menu");
define("_MI_SHORTCUT_BLOCK_MENU_DESC", "Shortcut links show in menu");
define("_MI_SHORTCUT_BLOCK_MYMENU", "My menu");
define("_MI_SHORTCUT_BLOCK_MYMENU_DESC", "Personal shortcut in menu");

// for altsys 
if (!defined('_MD_A_MYMENU_MYTPLSADMIN')) {
    define('_MD_A_MYMENU_MYTPLSADMIN','Templates');
    define('_MD_A_MYMENU_MYBLOCKSADMIN','Block/Access');
    define('_MD_A_MYMENU_MYPREFERENCES','Prefercenes');
}
?>