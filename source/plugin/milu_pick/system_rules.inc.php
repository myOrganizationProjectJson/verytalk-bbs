<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once(DISCUZ_ROOT.'source/plugin/milu_pick/config.inc.php');
if($_G['adminid'] < 1 ) exit('Access Denied:0032');
$header_config = array('fastpick_manage', 'fastpick_add', 'fastpick_import', 'fastpick_share');
$head_url = '?'.PICK_GO.'system_rules&myac=';
pload('F:rules,F:spider,F:pick,F:copyright,C:pickOutput');
$myac = str_replace('fastpick', 'rules', $_GET['myac']);
$tpl = $_GET['tpl'];
if(empty($myac)) $myac = 'rules_list';
if($myac == 'rules_add'){
	$myac = 'rules_edit';
	
}else if($myac == 'rules_del'){
	$tpl = 'no';
}else if($myac == 'rules_manage'){
	$myac = 'rules_list';
	
}
if(!in_array($myac, array('rules_list', 'rules_edit', 'rules_import', 'rules_share', 'rules_del', 'rules_export', 'create_rules_html', 'get_rpc_windowhtml', 'ajax_func', 'show_rules_select', 'show_keyword_html', 'url_page_range_test', 'rules_update', 'rpcServer', 'create_variable', 'many_list_test', 'get_rss_url', 'check_web_type'))) exit('Access Denied:0032');
if(function_exists($myac)) $info = $myac();
$mytemp = $_REQUEST['mytemp'] ? $_REQUEST['mytemp'] : $myac;
if(!$tpl && $tpl!= 'no') include template('milu_pick:'.$mytemp);
?>