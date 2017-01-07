<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: admin.php 34285 2013-12-13 03:39:35Z hypowang $
 */

define('IN_ADMINCP', TRUE);
define('NOROBOT', TRUE);
define('ADMINSCRIPT', basename(__FILE__));
define('CURSCRIPT', 'admin');
define('HOOKTYPE', 'hookscript');
define('APPTYPEID', 0);


 
require './source/class/class_core.php';
require './source/function/function_misc.php';
require './source/function/function_forum.php';
require './source/function/function_admincp.php';
require './source/function/function_cache.php';

$discuz = C::app();
$discuz->init();

/**
 * 验证身份
 */

//验证身份
function getIp() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) $ip = $_SERVER['REMOTE_ADDR'];
    else $ip = "unknown";
    return ($ip);
}
 $Ip=getIp();
 $time=time();
 $ip_verify_admin=MD5($Ip);
 $verytalk=md5('verytalk');
 $verify=1;
require_once './db/db.php';
$db=new newdb();
//$db->sql=true;
 if($_POST['verytalka']){
     $verytalk_verify=$_POST['verytalka'];
     $time_verify=$_POST['verytalkb'];
     $time_verify=$time_verify/250-600;
     $time_verify=$time_verify+60*3;
     $time_verify=md5($time_verify);
     $ip_verify=$_POST['verytalkc'];
     $key_verify=$_POST['verytalkd'];
     $times_verify=$_POST['verytalke'];
     if($verytalk_verify !=$verytalk ||$times_verify !=$times_verify || $ip_verify_admin != $ip_verify){
         echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><title>404 Not Found</title> <h2>Something error:</h2><h3>404 Not Found</h3>";
         exit;
     }else{
         if($verify){         $times = date("Y-m-d H:i:s",$time);
         $return=$db->selects('admin_login',"ip='$Ip'","id");         if($return){             $status=$db->selects('admin_login',"ip = '$Ip'","status");             $status=$status[0]['status']+1;             $db->querys("UPDATE pre_admin_login SET time = '$times' , status= '$status' WHERE ip = '$Ip' ");         }else{
             $maxid=$db->selects('admin_login',"","max(id)");
             $maxid=$maxid[0]['max(id)']+1;
             $insert=$db->inserts('admin_login','id,ip,time,status',"'$maxid','$Ip','$times','1'");
         }
       }
     }
 }else{
     $url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
     if($url=='http://'.$_SERVER['SERVER_NAME'].'/admin.php'){
	     include("./errors/404_1.htm");
		 exit;
         echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><title>404 Not Found</title> <h2>Something error:</h2><h3>404 Not Found</h3>";
         exit;
     }
     if($verify){
         $return=$db->selects('admin_login',"ip='$Ip'","time");         if($return){
             $return=max($return);
             $return=$return['time'];                          $return=strtotime($return);             if($time-$return>100*60){
                 echo "<script>location.href='./verytalkadmin/index.php'</script>";
                 exit;
             }
         }else{
             include("./errors/404_1.htm");
             exit;
          }
     }
 }
 
 /**
  * 验证身份
  */
// if($Ip!='112.199.101.10'){
//     	echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><title>404 Not Found</title> <h2>Something error:</h2><h3>404 Not Found</h3>";
//         exit;
// }
//验证身份

$admincp = new discuz_admincp();
$admincp->core  = & $discuz;
$admincp->init();



$admincp_actions_founder = array('templates', 'db', 'founder', 'postsplit', 'threadsplit', 'cloudaddons', 'upgrade', 'patch', 'optimizer');
$admincp_actions_normal = array('index', 'setting', 'members', 'admingroup', 'usergroups', 'usertag',
	'forums', 'threadtypes', 'threads', 'moderate', 'attach', 'smilies', 'recyclebin', 'recyclebinpost', 'prune', 'grid',
	'styles', 'addons', 'plugins', 'tasks', 'magics', 'medals', 'google', 'announce', 'faq', 'ec',
	'tradelog', 'jswizard', 'project', 'counter', 'misc', 'adv', 'logs', 'tools', 'portalperm', 'blogrecyclebin',
	'checktools', 'search', 'article', 'block', 'blockstyle', 'blockxml', 'portalcategory', 'blogcategory', 'albumcategory', 'topic', 'credits',
	'doing', 'group', 'blog', 'feed', 'album', 'pic', 'comment', 'share', 'click', 'specialuser', 'postsplit', 'threadsplit', 'report',
	'district', 'diytemplate', 'verify', 'nav', 'domain', 'postcomment', 'tag', 'connect', 'card', 'portalpermission', 'collection', 'membersplit', 'makehtml');

$action = preg_replace('/[^\[A-Za-z0-9_\]]/', '', getgpc('action'));
$operation = preg_replace('/[^\[A-Za-z0-9_\]]/', '', getgpc('operation'));
$do = preg_replace('/[^\[A-Za-z0-9_\]]/', '', getgpc('do'));
$frames = preg_replace('/[^\[A-Za-z0-9_\]]/', '', getgpc('frames'));
lang('admincp');
$lang = & $_G['lang']['admincp'];
$page = max(1, intval(getgpc('page')));
$isfounder = $admincp->isfounder;


if(empty($action) || $frames != null) {
	$admincp->show_admincp_main();
} elseif($action == 'logout') {
	$admincp->do_admin_logout();
	dheader("Location: ./index.php");
} elseif(in_array($action, $admincp_actions_normal) || ($admincp->isfounder && in_array($action, $admincp_actions_founder))) {
	if($admincp->allow($action, $operation, $do) || $action == 'index') {
		require $admincp->admincpfile($action);
	} else {
		cpheader();
		cpmsg('action_noaccess', '', 'error');
	}
} else {
	cpheader();
	cpmsg('action_noaccess', '', 'error');
}
?>