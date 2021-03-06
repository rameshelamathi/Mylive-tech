<?php 
/*#################################################################*\
|# Licence Number 0E18-0225-0OLK-0210
|# -------------------------------------------------------------   #|
|# Copyright (c)2014 PHP Link Directory.                           #|
|# http://www.phplinkdirectory.com                                 #|
\*#################################################################*/
	 

/**
 # ################################################################################
 # Project:   PHP Link Directory
 #
 # **********************************************************************
 # Copyright (C) 2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 #
 # This software is for use only to those who have purchased a license.
 # A license must be purchased for EACH installation of the software.
 #
 # By using the software you agree to the terms:
 #
 #    - You may not redistribute, sell or otherwise share this software
 #      in whole or in part without the consent of the the ownership
 #      of PHP Link Directory. Please contact david@david-duval.com
 #      if you need more information.
 #
 #    - You agree to retain a link back to http://www.phplinkdirectory.com/
 #      on all pages of your directory if you purchased any of our "link back" 
 #      versions of the software.
 #
 #
 # In some cases, license holders may be required to agree to changes
 # in the software license before receiving updates to the software.
 # **********************************************************************
 #
 # For questions, help, comments, discussion, etc., please join the
 # PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
 #
 # @link           http://www.phplinkdirectory.com/
 # @copyright      2004-2013 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.1.0 Phoenix Release
 # ################################################################################
 */
 
require_once 'init.php';

$error   = 0;

if (empty ($_REQUEST['submit']) && !empty ($_SERVER['HTTP_REFERER'])) {
   $_SESSION['return'] = $_SERVER['HTTP_REFERER'];
}

if (isset($_SESSION['wid_message'])) {
	$tpl->assign('wid_message', $_SESSION['wid_message']);
	unset($_SESSION['wid_message']);
}

if (isset($_SESSION['wid_error'])) {
	$tpl->assign('wid_error', $_SESSION['wid_error']);
	unset($_SESSION['wid_error']);
}

$columns = array ('ID'=> 'ID', 'ORDER_ID' => _L('Order ID'),  'NAME' => _L('Name'), 'PRICE' => _L('Price'), 'STATUS'=>_L('Status'), 'LIST_TEMPLATE'=>_L('List Template'), 'DETAILS_TEMPLATE'=>_L('Details Template'), 'ACTION'=>_L('Action'), );
$tpl->assign('columns', $columns);

$content = $tpl->fetch(ADMIN_TEMPLATE.'/dir_link_types.tpl');

$tpl->assign('error'    , $error);

$tpl->assign('content', $content);

//Clean whitespace
$tpl->load_filter('output', 'trimwhitespace');

//Make output
echo $tpl->fetch(ADMIN_TEMPLATE.'/main.tpl');
?>