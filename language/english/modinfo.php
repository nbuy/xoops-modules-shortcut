<?php
// Module Info
// $Id: modinfo.php,v 1.2 2008/06/24 14:29:22 nobu Exp $

// The name of this module
define("_MI_SHORTCUT_NAME","ShortCut");

// A brief description of this module
define("_MI_SHORTCUT_DESC","URL redirects for shorter strings");

define("_MI_SHORTCUT_BLOCK_MENU", "My Menu");
define("_MI_SHORTCUT_BLOCK_MENU_DESC", "Links show in menu");

// for altsys 
if (!defined('_MD_A_MYMENU_MYTPLSADMIN')) {
    define('_MD_A_MYMENU_MYTPLSADMIN','Templates');
    define('_MD_A_MYMENU_MYBLOCKSADMIN','Block/Access');
    define('_MD_A_MYMENU_MYPREFERENCES','Prefercenes');
}
?>