<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require_once dirname(__FILE__) . '/include/function.php';
require_once dirname(__FILE__) . '/include/check.php';
chaojiapp_top_menu();

$result = chaojiapp_soap('Seo_GetWebsiteSummary', array('actionType' => '11', 'accessToken' => $access_token));
$dataresult = $result['Seo_GetWebsiteSummaryResult'];
// var_dump($dataresult);
// 解析数据成数组
$objJsonData1 = $cjson->decode($dataresult, FALSE);

chaojiapp_api_error($objJsonData1->code, 'overview');

$websitehourlydata = json_encode($objJsonData1->data->websitehourlydata);

$objJsonData = $cjson->decode($dataresult, TRUE);

// 客户端信息
if($objJsonData1->data->clientinfo){
	$clientinfo = $objJsonData1->data->clientinfo;
	if($clientinfo->isonline){
		$clientinfo_status = 1;
	}else{
		$clientinfo_status = 0;
	}
	// var_dump($clientinfo->loginip);
	$clientinfo_str = chaojiapp_lang('xclientinfo-loginip') . $clientinfo->loginip . '\n' . chaojiapp_lang('xclientinfo-points') . $clientinfo->points . '\n' . chaojiapp_lang('xclientinfo-version') . $clientinfo->version;
}else{
	$clientinfo_status = 0;
	$clientinfo_str = '';
}


// 是否显示数据
if($objJsonData1->data->isviewdata){
	session_start();
	$_SESSION['isviewdata'] = $objJsonData1->data->isviewdata;
	unset($_SESSION['isviewdata_data']);

	// 得分
	if($objJsonData['data']['website']['scoreinfo']){
		$todayscore = $objJsonData['data']['website']['scoreinfo']['todayscore'];
		$scorerate = sprintf(chaojiapp_lang('scorerate'), $objJsonData['data']['website']['scoreinfo']['scorerate']);
		$baiduscore = sprintf(chaojiapp_lang('baiduscore'), $objJsonData['data']['website']['scoreinfo']['baiduscore']);
		$googlescore = sprintf(chaojiapp_lang('googlescore'), $objJsonData['data']['website']['scoreinfo']['googlescore']);
	}else{
		$todayscore = '--';
		$scorerate = sprintf(chaojiapp_lang('scorerate'), '--');
		$baiduscore = sprintf(chaojiapp_lang('baiduscore'), '--');
		$googlescore = sprintf(chaojiapp_lang('googlescore'), '--');
	}

	$userwebsiteid = $objJsonData['data']['website']['userwebsiteid'];

	dsetcookie('chaojiapp_websiteid', $userwebsiteid);

	$sitename = chaojiapp_code($objJsonData['data']['website']['websitename']);

	dsetcookie('chaojiapp_sitename', $sitename);

	$domain = $objJsonData['data']['website']['host'];

	dsetcookie('chaojiapp_domain', $domain);

	$starttime = $objJsonData['data']['website']['createdate'];
	$domainage = sprintf(chaojiapp_lang('test_data1'), chaojiapp_code($objJsonData['data']['website']['domainage']), $objJsonData['data']['website']['domainregdate']);
	$domainexpire = $objJsonData['data']['website']['domainexpdate'];
	$domainexpire1 = str_replace('www.', '', $domain);
	$baidupr = $objJsonData['data']['websitetodaydata']['baidupr'];
	$googlepr = $objJsonData['data']['websitetodaydata']['googlepr'];
	$sogoupr = $objJsonData['data']['websitetodaydata']['sogoupr'];
	$alexarank = $objJsonData['data']['websitetodaydata']['alexarank'];
	// alexa趋势
	$alexaranktrend = chaojiapp_trend('alexa1', $objJsonData['data']['websitetodaydata']['alexaranktrend']);

	$packagename = chaojiapp_code($objJsonData['data']['website']['packagename']);
	$typeid = $objJsonData['data']['website']['typeid'];
	$expireddate = $objJsonData['data']['website']['serverexpireddate'];

	// 百度相关数据
	$baidukeywords = $objJsonData['data']['websitetodaydata']['baidukeywords'];
	$baiduflow = chaojiapp_code($objJsonData['data']['websitetodaydata']['baiduflow']);
	$baidusnap = chaojiapp_code($objJsonData['data']['websitetodaydata']['baidusnap']);
	$homeposition = $objJsonData['data']['websitetodaydata']['homeposition'];
	$baidusiteindex = chaojiapp_code($objJsonData['data']['websitetodaydata']['baidusiteindex']);
	$baidusiteindextrend = chaojiapp_trend('', $objJsonData['data']['websitetodaydata']['baidusiteindextrend']);
	$outlinkcount = chaojiapp_code($objJsonData['data']['websitetodaydata']['outlinkcount']);
	// 各大搜索引擎收录/反链情况
	$baidupages = chaojiapp_code($objJsonData['data']['websitetodaydata']['baidupages']);
	$baidupagestrend = chaojiapp_trend('', $objJsonData['data']['websitetodaydata']['baidupagestrend']);
	$baidulink = chaojiapp_code($objJsonData['data']['websitetodaydata']['baidulink']);
	$baidulinktrend = chaojiapp_trend('', $objJsonData['data']['websitetodaydata']['baidulinktrend']);
	$googlepages = chaojiapp_code($objJsonData['data']['websitetodaydata']['googlepages']);
	$googlepagestrend = chaojiapp_trend('', $objJsonData['data']['websitetodaydata']['googlepagestrend']);
	$googlelink = chaojiapp_code($objJsonData['data']['websitetodaydata']['googlelink']);
	$sopages = chaojiapp_code($objJsonData['data']['websitetodaydata']['sopages']);
	$sopagestrend = chaojiapp_trend('', $objJsonData['data']['websitetodaydata']['sopagestrend']);
	$solink = chaojiapp_code($objJsonData['data']['websitetodaydata']['solink']);
	$solinktrend = chaojiapp_trend('', $objJsonData['data']['websitetodaydata']['solinktrend']);
	$sogoupages = chaojiapp_code($objJsonData['data']['websitetodaydata']['sogoupages']);
	$sogoupagestrend = chaojiapp_trend('', $objJsonData['data']['websitetodaydata']['sogoupagestrend']);
	$sogoulink = '';
	$sogoulinktrend = '';
	// 24小时走势
	$pageslastuptime = chaojiapp_code($objJsonData1->data->pageslastuptime);
	// $websitehourlydata = $objJsonData1['data']->websitehourlydata;
	// alexa走势
	// var_dump($objJsonData1);
	$flowrank = $objJsonData1->data->websitetodaydata->flowrank;
	$flowranktrend = chaojiapp_trend('alexa', $objJsonData1->data->websitetodaydata->flowranktrend);
	$visitrank = $objJsonData1->data->websitetodaydata->visitrank;
	$visitranktrend = chaojiapp_trend('alexa', $objJsonData1->data->websitetodaydata->visitranktrend);
	$pagevisitrank = $objJsonData1->data->websitetodaydata->pagevisitrank;
	$pagevisitranktrend = chaojiapp_trend('alexa', $objJsonData1->data->websitetodaydata->pagevisitranktrend);

	include template('chaoji_com:overview');
}else{
	session_start();
	
	$jsondata = $objJsonData['data'];
	// 把状态缓存起来
	$_SESSION['isviewdata'] = $jsondata['isviewdata'];
	$_SESSION['isviewdata_data'] = $jsondata;
	if($objJsonData['data']['typeid'] == '1'){
		if($objJsonData['data']['downloadurl'] == ''){
			$textarr = explode(chr(29), $objJsonData['data']['showtext']);
		}
	}
	
	include template('chaoji_com:cannotviewdata');
}
?>