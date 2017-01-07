<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: uninstall.php 24473 2011-09-21 03:53:05Z monkey $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE IF EXISTS `pre_chaojiapp_setting`;

EOF;

runquery($sql);

// 回传数据
include(dirname(__FILE__) . '/include/function.php');
$result = chaojiapp_soap('App_PluginUninstallNotify', array('accessToken' => getcookie('chaojiapp_access_token')));

$finish = TRUE;
?>