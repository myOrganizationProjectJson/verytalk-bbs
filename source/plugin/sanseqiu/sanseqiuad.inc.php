<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) { 
    exit('Access Denied'); 
}
 
if(!$_G['uid']){
	showmessage('error！', '', array(), array('login' => true));
	exit;
}

 
  

?>
