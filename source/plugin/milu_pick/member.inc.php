<?php
if(!defined('IN_DISCUZ') ) {
	exit('Access Denied');
} 
require_once(DISCUZ_ROOT.'source/plugin/milu_pick/config.inc.php');
pload('F:member,F:copyright,C:pickOutput');
if($_G['adminid'] < 1 ) exit('Access Denied:0032');
$header_config = array('member_set', 'member_get', 'member_list', 'member_public_set', 'member_public', 'avatar_set', 'avatar_get');
$head_url = '?'.PICK_GO.'member&myac=';
$myac = $_GET['myac'];
$tpl = $_GET['tpl'];
if(empty($myac)) $myac = 'member_set';
if(!in_array($myac, array('member_set', 'member_get', 'member_list', 'member_public_set', 'member_public', 'avatar_set', 'avatar_get', 'member_del', 'member_edit', 'member_public_info', 'member_public_set', 'ajax_func', 'download', 'member_import_online', 'member_export'))) exit('Access Denied:0032');
if(function_exists($myac)) $info = $myac();
if($myac == 'member_set' || $myac == 'member_list') $info['tips'] = no_member_tips();
$mytemp = $_REQUEST['mytemp'] ? $_REQUEST['mytemp'] : $myac;
if(!$tpl && $tpl!= 'no') include template('milu_pick:'.$mytemp);
?>