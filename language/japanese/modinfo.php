<?php
// Module Info
// $Id: modinfo.php,v 1.4 2008/07/06 07:36:56 nobu Exp $

// The name of this module
define("_MI_SHORTCUT_NAME","���硼�ȥ��å�");

// A brief description of this module
define("_MI_SHORTCUT_DESC","URL�������쥯�Ȥ���û������");

// Sub menus
define("_MI_SHORTCUT_REGISTER","���硼�ȥ��åȤ���Ͽ");

define("_MI_SHORTCUT_HELP","Shortcut �ˤĤ���");

// Names of blocks for this module (Not all module has blocks)
define("_MI_SHORTCUT_BLOCK_MENU", "��󥯥�˥塼");
define("_MI_SHORTCUT_BLOCK_MENU_DESC", "�����ƥ�Υ��硼�ȥ��åȤ��˥塼��ɽ������");
define("_MI_SHORTCUT_BLOCK_MYMENU", "�ޥ���˥塼");
define("_MI_SHORTCUT_BLOCK_MYMENU_DESC", "�ĿͤΥ��硼�ȥ��åȤ��˥塼ɽ������");

// for altsys 
if (!defined('_MD_A_MYMENU_MYTPLSADMIN')) {
    define('_MD_A_MYMENU_MYTPLSADMIN','�ƥ�ץ졼�ȴ���');
    define('_MD_A_MYMENU_MYBLOCKSADMIN','�֥�å�/������������');
    define('_MD_A_MYMENU_MYPREFERENCES','��������');
}
?>