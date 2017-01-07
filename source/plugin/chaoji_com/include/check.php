<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// 得到配置信息
$settinginfo = chaojiapp_setting_info();
// 如果配置不存在
if(count($settinginfo) > 0){
}else{
	// 不存在，让用户去配置
	cpmsg(chaojiapp_lang('please_setting'), CJ_PLUGIN_URL . 'pmod=setting', 'error');
	exit;
}
$appid = $settinginfo['appid'];
$appsecret = $settinginfo['appsecret'];
$access_token_cookie = getcookie("chaojiapp_access_token");
if($access_token_cookie){
	$access_token = $access_token_cookie;
}else{
	$obj1 = chaojiapp_get_access_token($appid, $appsecret);
	// 判断结果，0表示正常  1，表示access_token到期，其他要显示错误信息
	if($obj1->code == '0'){
		$access_token = $obj1->data->accesstoken;
		// 保存到session中，别的页面如果没有到期就可以继续用，不用每次都去请求access_token。
		// var_dump($obj1->data);
		dsetcookie('chaojiapp_access_token', $access_token, strtotime($obj1->data->expiredtime) - TIMESTAMP);
		dsetcookie('chaojiapp_access_token1', str_replace('+', '|', $access_token), strtotime($obj1->data->expiredtime) - TIMESTAMP);
		
	}else{
		// var_dump($obj1);
		// 其他情况，显示错误信息。
		cpmsg(chaojiapp_code($obj1->msg), CJ_PLUGIN_URL . 'pmod=setting', 'error');
		exit;
	}
}
?>