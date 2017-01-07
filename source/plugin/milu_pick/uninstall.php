<?php

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$sql = <<<EOF
DROP TABLE IF EXISTS `cdb_strayer_evo_log`, `cdb_strayer_article_content`, `cdb_strayer_article_title`, `cdb_strayer_category`, `cdb_strayer_evo`, `cdb_strayer_fastpick`, `cdb_strayer_member`, `cdb_strayer_picker`, `cdb_strayer_rules`, `cdb_strayer_searchindex`, `cdb_strayer_setting`, `cdb_strayer_timing`, `cdb_strayer_url`, `cdb_strayer_typeoptionvar`, `cdb_strayer_attach`;
EOF;
runquery($sql);

//и╬ЁЩ╩╨╢Фнд╪Ч
$cache_dir = DISCUZ_VERSION != 'X2' ? 'sysdata' : 'cache';
$cachefile = DISCUZ_ROOT.'./data/'.$cache_dir.'/milu_pick_vir_online.php';
$key_file = DISCUZ_ROOT.'./data/'.$cache_dir.'/milu_pick_key.php';
@unlink($cachefile);
@unlink($key_file);

$finish = TRUE;
?>