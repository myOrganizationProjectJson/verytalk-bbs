<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

// �õ�������Ϣ
$settinginfo = chaojiapp_setting_info();
// ������ò�����
if(count($settinginfo) > 0){
}else{
	// �����ڣ����û�ȥ����
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
	// �жϽ����0��ʾ����  1����ʾaccess_token���ڣ�����Ҫ��ʾ������Ϣ
	if($obj1->code == '0'){
		$access_token = $obj1->data->accesstoken;
		// ���浽session�У����ҳ�����û�е��ھͿ��Լ����ã�����ÿ�ζ�ȥ����access_token��
		// var_dump($obj1->data);
		dsetcookie('chaojiapp_access_token', $access_token, strtotime($obj1->data->expiredtime) - TIMESTAMP);
		dsetcookie('chaojiapp_access_token1', str_replace('+', '|', $access_token), strtotime($obj1->data->expiredtime) - TIMESTAMP);
		
	}else{
		// var_dump($obj1);
		// �����������ʾ������Ϣ��
		cpmsg(chaojiapp_code($obj1->msg), CJ_PLUGIN_URL . 'pmod=setting', 'error');
		exit;
	}
}
?>