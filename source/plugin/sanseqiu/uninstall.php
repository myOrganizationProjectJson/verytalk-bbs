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
//DROP TABLE IF EXISTS `cdb_caipiao_bp`;
//DROP TABLE IF EXISTS `cdb_caipiao_buyitem`;
//DROP TABLE IF EXISTS `cdb_caipiao_pub`;
EOF;
 


$finish = TRUE;