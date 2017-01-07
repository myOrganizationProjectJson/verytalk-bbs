<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once(DISCUZ_ROOT.'source/plugin/milu_pick/config.inc.php');
if($_G['adminid'] < 1 ) exit('Access Denied:0032');
pload('F:seo,F:copyright,C:pickOutput');
$head_url = '?'.PICK_GO.'seo&myac=';
$myac = $_GET['myac'];
$tpl = $_GET['tpl'];
if(empty($myac)) $myac = 'seo_set';
if(!in_array($myac, array('seo_set'))) exit('Access Denied:0032');
if(function_exists($myac)) $info = $myac();
$mytemp = $_REQUEST['mytemp'] ? $_REQUEST['mytemp'] : $myac;
if(!$tpl && $tpl!= 'no') include template('milu_pick:'.$mytemp);
?>