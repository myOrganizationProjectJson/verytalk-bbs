<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
session_start();
require_once(dirname(__FILE__) . '/include/function.php');
require_once dirname(__FILE__) . '/include/check.php';

	echo '<script src="source/plugin/chaoji_com/resource/js/My97DatePicker/WdatePicker.js" type="text/javascript"></script><script type="text/javascript" language="javascript" src="source/plugin/chaoji_com/resource/js/jquery.min.js"></script><script src="source/plugin/chaoji_com/resource/js/artDialog/artDialog.source.js?skin=default"></script>
	<script src="source/plugin/chaoji_com/resource/js/artDialog/plugins/iframeTools.js"></script>
<script type="text/javascript">var jq = jQuery.noConflict();var cookiepre=\'' . $_G['config']['cookie']['cookiepre'] . '\';var disallowfloat=false;var clientinfo_status=0;var clientinfo_str=\'\';var PLUGIN_URL = \'' . CJ_PLUGIN_URL . '\'; var FORMHASH = \'' . FORMHASH . '\';</script>
<script type="text/javascript" src="source/plugin/chaoji_com/resource/js/highcharts.js" language="javascript"></script>
<script type="text/javascript" src="source/plugin/chaoji_com/resource/js/tableorder.js" language="javascript"></script>
<script src="source/plugin/chaoji_com/resource/js/template-native.js" type="text/javascript"></script><script src="source/plugin/chaoji_com/resource/js/cj-rivalcompare.js" type="text/javascript"></script>
<link href="source/plugin/chaoji_com/resource/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="source/plugin/chaoji_com/resource/js/script.js"></script>';

$op = isset($_GET['op']) ? trim($_GET['op']) : 'rival-summary';
$op1 = isset($_GET['op1']) ? trim($_GET['op1']) : '';
$formhash = isset($_GET['formhash']) ? trim($_GET['formhash']) : '';

// echo chaojiapp_rival_subnav($op);
chaojiapp_top_menu('rival');
showtableheader();
showtitle(chaojiapp_lang($op));
showtablefooter();

// if(!in_array($op, array('', 'rival-summary', 'rival-pageseo'))){
	
// }

// 默认是今天

$st = isset($_GET['st']) ? addslashes(trim($_GET['st'])) : '';
$et = isset($_GET['et']) ? addslashes(trim($_GET['et'])) : '';
$t = isset($_GET['t']) ? addslashes(trim($_GET['t'])) : '';


if($st == ''){
	$st = dgmdate(TIMESTAMP - 86400 * 6, 'Y-m-d');
}
if($et == ''){
	$et = dgmdate(TIMESTAMP, 'Y-m-d');
}

if(date('H') < CJ_NEW_DAY_BY_HOUR){
	$st = dgmdate(TIMESTAMP - 86400 * 7, 'Y-m-d');
	$et = dgmdate(TIMESTAMP - 86400, 'Y-m-d');
	$maxdate = $et;
}else{
	$maxdate = $et;
}


if($t == ''){
	$t = 3;
}

// 关键词图标
$icon_array = array(
	'百度排名' => 'baidu',
	'谷歌排名' => 'google',
);

// 竞争概况链接数组
$link_array = array(
	'百度收录' => 'http://www.baidu.com/s?wd=site:%s',
	'百度索引量' => 'http://www.baidu.com/s?wd=site:%s',
	'百度反链' => 'http://www.baidu.com/s?wd=domain:%s',
	'百度权重' => 'http://www.chaoji.com/seo/baidusort.aspx?host=%s',
	'首页位置' => 'http://www.baidu.com/s?wd=site:%s',
	'百度快照' => 'http://www.baidu.com/s?wd=%s',
	'谷歌收录' => 'http://www.google.com.hk/search?hl=zh-CN&q=site:%s',
	'谷歌反链' => 'http://www.google.com.hk/search?hl=zh-CN&q=link:%s',
	'谷歌PR' => 'http://pr.chinaz.com/?PRAddress=%s',
	'360收录' => 'http://www.so.com/s?q=site:%s',
	'360反链' => "http://www.so.com/s?q=%%22%s%%22",
	'搜狗收录' => 'http://www.sogou.com/web?query=site:%s',
	'搜狗SR' => 'http://pr.chinaz.com/?PRAddress=%s',
	'Alexa排名' => 'http://alexa.chinaz.com/?domain=%s',
	'综合排名' => 'http://alexa.chinaz.com/?domain=%s',
	'站长工具' => 'http://outlink.chinaz.com/?h=%s',
	'百度词条' => 'http://www.chaoji.com/seo/baidusort.aspx?host=%s',
	'百度流量' => 'http://www.chaoji.com/seo/baidusort.aspx?host=%s'
);
if($op == 'rival-site-list'){

	$result7 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 7, 'strParams' => '', 'accessToken' => getcookie('chaojiapp_access_token')));
	$result71 = $result7['Seo_MonitorWebsiteManagerResult'];
	
	$jsondata = $cjson->decode($result71);
	chaojiapp_api_error($jsondata['code'], 'rival&op=rival-site-list');
	$jsondata = $jsondata['data'];
	// var_dump($jsondata);

	include template('chaoji_com:rival-site-list');
}elseif($op == 'rival-summary'){ // 概况

	$result30 = chaojiapp_soap('Seo_GetCompeteWebsiteAnalysis', array('actionType' => 30, 'beginDate' => date('Y-m-d'), 'endDate'=> date('Y-m-d'),'accessToken' => getcookie('chaojiapp_access_token')));
	$result301 = $result30['Seo_GetCompeteWebsiteAnalysisResult'];
	// exit;
	$jsondata = $cjson->decode($result301);
	chaojiapp_api_error($jsondata['code'], 'rival&op=rival-summary');
	$jsondata = $jsondata['data'];
	if($jsondata['isviewdata']){
		if($jsondata['rivalcount'] == 0){
			include template('chaoji_com:rival-no-site');
		}else{
			include template('chaoji_com:rival-summary');
		}
	}else{
		if($jsondata['typeid'] == '1'){
			if($jsondata['downloadurl'] == ''){
				$textarr = explode(chr(29), $jsondata['showtext']);
			}
		}
		include template('chaoji_com:cannotviewdata');		
	}
}elseif($op == 'rival-pageseo'){ // 页面seo

	$result31 = chaojiapp_soap('Seo_GetCompeteWebsiteAnalysis', array('actionType' => 31, 'beginDate' => date('Y-m-d'), 'endDate'=> date('Y-m-d'),'accessToken' => getcookie('chaojiapp_access_token')));
	$result311 = $result31['Seo_GetCompeteWebsiteAnalysisResult'];
	$jsondata = $cjson->decode($result311);
	chaojiapp_api_error($jsondata['code'], 'rival&op=rival-pageseo');
	$jsondata = $jsondata['data'];
	if($jsondata['isviewdata']){
		if($jsondata['rivalcount'] > 0){
			include template('chaoji_com:rival-pageseo');
		}else{
			include template('chaoji_com:rival-no-site');
		}
	}else{
		if($jsondata['typeid'] == '1'){
			if($jsondata['downloadurl'] == ''){
				$textarr = explode(chr(29), $jsondata['showtext']);
			}
		}
		include template('chaoji_com:cannotviewdata');		
	}
	
}elseif($op == 'rival-snapdate'){ // 快照日期
	if($op1 == 'export'){
		if($formhash == formhash() && isset($_SESSION['tempdata'])){
			chaojiapp_export_rival_snapdate();
		}else{
			cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=rival&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
		}
	}else{
		$result32 = chaojiapp_soap('Seo_GetCompeteWebsiteAnalysis', array('actionType' => 32, 'beginDate' => $st, 'endDate' => $et, 'accessToken' => getcookie('chaojiapp_access_token')));
		$result321 = $result32['Seo_GetCompeteWebsiteAnalysisResult'];
		
		$jsondata = $cjson->decode($result321);
		chaojiapp_api_error($jsondata['code'], 'rival&op=rival-snapdate&st=' . $st . '&et=' . $et);
		$jsondata = $jsondata['data'];
		if($jsondata['isviewdata']){
			if($jsondata['rivalcount'] > 0){
				chaojiapp_search_form();
				$_SESSION['tempdata'] = $jsondata;
				include template('chaoji_com:rival-snapdate');
			}else{
				include template('chaoji_com:rival-no-site');
			}
		}else{
			if($jsondata['typeid'] == '1'){
				if($jsondata['downloadurl'] == ''){
					$textarr = explode(chr(29), $jsondata['showtext']);
				}
			}
			include template('chaoji_com:cannotviewdata');		
		}		
	}
}elseif($op == 'rival-site'){ // 收录
	if($op1 == 'export'){
		if($formhash == formhash() && isset($_SESSION['tempdata'])){
			chaojiapp_export_rival_site();
		}else{
			cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=rival&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
		}
	}else{
		$result33 = chaojiapp_soap('Seo_GetCompeteWebsiteAnalysis', array('actionType' => 33, 'beginDate' => $st, 'endDate' => $et, 'accessToken' => getcookie('chaojiapp_access_token')));
		$result331 = $result33['Seo_GetCompeteWebsiteAnalysisResult'];
		$jsondata = $cjson->decode($result331);
		chaojiapp_api_error($jsondata['code'], 'rival&op=rival-site&st=' . $st . '&et=' . $et);
		$jsondata = $jsondata['data'];
		if($jsondata['isviewdata']){
			if($jsondata['rivalcount'] > 0){
				chaojiapp_search_form();
				$_SESSION['tempdata'] = $jsondata;
				include template('chaoji_com:rival-site');
			}else{
				include template('chaoji_com:rival-no-site');
			}
		}else{
			if($jsondata['typeid'] == '1'){
				if($jsondata['downloadurl'] == ''){
					$textarr = explode(chr(29), $jsondata['showtext']);
				}
			}
			include template('chaoji_com:cannotviewdata');		
		}
	}
}elseif($op == 'rival-link'){ // 反链
	if($op1 == 'export'){
		if($formhash == formhash() && isset($_SESSION['tempdata'])){
			chaojiapp_export_rival_link();
		}else{
			cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=rival&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
		}
	}else{
		
		$result34 = chaojiapp_soap('Seo_GetCompeteWebsiteAnalysis', array('actionType' => 34, 'beginDate' => $st, 'endDate' => $et, 'accessToken' => getcookie('chaojiapp_access_token')));
		
		$result341 = $result34['Seo_GetCompeteWebsiteAnalysisResult'];
		$jsondata = $cjson->decode($result341);
		chaojiapp_api_error($jsondata['code'], 'rival&op=rival-link&st=' . $st . '&et=' . $et);
		$jsondata = $jsondata['data'];
		if($jsondata['isviewdata']){
			if($jsondata['rivalcount'] > 0){
				chaojiapp_search_form();
				$_SESSION['tempdata'] = $jsondata;
				include template('chaoji_com:rival-link');
			}else{
				include template('chaoji_com:rival-no-site');
			}
		}else{
			if($jsondata['typeid'] == '1'){
				if($jsondata['downloadurl'] == ''){
					$textarr = explode(chr(29), $jsondata['showtext']);
				}
			}
			include template('chaoji_com:cannotviewdata');		
		}		
	}
}elseif($op == 'rival-keyword'){ // 关键词
	if($op1 == 'export'){
		if($formhash == formhash() && isset($_SESSION['tempdata'])){
			chaojiapp_export_rival_keyword();
		}else{
			cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=rival&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
		}
	}else{
		// 默认的index
		$default_index = isset($_GET['mt']) ? intval($_GET['mt']) : 0;

		$result35 = chaojiapp_soap('Seo_GetCompeteWebsiteAnalysis', array('actionType' => 35, 'beginDate' => $st, 'endDate' => $et, 'accessToken' => getcookie('chaojiapp_access_token')));
		$result351 = $result35['Seo_GetCompeteWebsiteAnalysisResult'];
		$jsondata = $cjson->decode($result351);
		chaojiapp_api_error($jsondata['code'], 'rival&op=rival-keyword&st=' . $st . '&et=' . $et);
		$jsondata = $jsondata['data'];
		if($jsondata['isviewdata']){
			if($jsondata['rivalcount'] > 0){
				chaojiapp_search_form();
				$array_num = count($jsondata['datatypes']);
				
				$newarr = array();
				for($i = 0; $i < $array_num; $i++){
					
					$newmonitorkeywordlist = array();
					foreach($jsondata['monitorsite']['keywordlist'] as $keyword){
						$newmonitorkeywordlist[] = array(
							'keywordid' => $keyword['keywordid'],
							'keyword' => $keyword['keyword'],
							'datalist' => $keyword['datalist'][$i]
						);
					}
					
					$newrivalsitelist = array();
					foreach($jsondata['rivalsitelist'] as $rivalsite){
						
						$newkeywordlist = array();
						foreach($rivalsite['keywordlist'] as $keyword1){
							$newkeywordlist[] = array(
								'keywordid' => $keyword1['keywordid'],
								'keyword' => $keyword1['keyword'],
								'datalist' => $keyword1['datalist'][$i]
							);
						}
						
						$newrivalsitelist[] = array(
							'websiteid' => $rivalsite['websiteid'],
							'websitename' => $rivalsite['websitename'],
							'host' => $rivalsite['host'],
							'color' => $rivalsite['color'],
							'keywordlist' => $newkeywordlist
						);
					}
					
					$newmonitorsite = array(
						'websiteid' => $jsondata['monitorsite']['websiteid'],
						'websitename' => $jsondata['monitorsite']['websitename'],
						'host' => $jsondata['monitorsite']['host'],
						'color' => $jsondata['monitorsite']['color'],
						'keywordlist' => $newmonitorkeywordlist
					);
					$newarr[] = array(
						'rivalcount' => $jsondata['rivalcount'],
						'currentdate' => $jsondata['currentdate'],
						'createdates' => $jsondata['createdates'],
						'datatypes' => $jsondata['datatypes'],
						'monitorsite' => $newmonitorsite,
						'rivalsitelist' => $newrivalsitelist
					);
				}
				
				$jsondata = $newarr[$default_index];
				
				$_SESSION['tempdata'] = $newarr;
				
				include template('chaoji_com:rival-keyword');
			}else{
				include template('chaoji_com:rival-no-site');
			}
		}else{
			if($jsondata['typeid'] == '1'){
				if($jsondata['downloadurl'] == ''){
					$textarr = explode(chr(29), $jsondata['showtext']);
				}
			}
			include template('chaoji_com:cannotviewdata');		
		}
	}
}elseif($op == 'rival-rank'){ // PR 排名
	if($op1 == 'export'){
		if($formhash == formhash() && isset($_SESSION['tempdata'])){
			chaojiapp_export_rival_rank();
		}else{
			cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=rival&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
		}
	}else{
		$result36 = chaojiapp_soap('Seo_GetCompeteWebsiteAnalysis', array('actionType' => 36, 'beginDate' => $st, 'endDate' => $et, 'accessToken' => getcookie('chaojiapp_access_token')));
		$result361 = $result36['Seo_GetCompeteWebsiteAnalysisResult'];
		$jsondata = $cjson->decode($result361);
		chaojiapp_api_error($jsondata['code'], 'rival&op=rival-rank&st=' . $st . '&et=' . $et);
		$jsondata = $jsondata['data'];
		if($jsondata['isviewdata']){
			if($jsondata['rivalcount'] > 0){
				chaojiapp_search_form();
				$_SESSION['tempdata'] = $jsondata;
				include template('chaoji_com:rival-rank');
			}else{
				include template('chaoji_com:rival-no-site');
			}
		}else{
			if($jsondata['typeid'] == '1'){
				if($jsondata['downloadurl'] == ''){
					$textarr = explode(chr(29), $jsondata['showtext']);
				}
			}
			include template('chaoji_com:cannotviewdata');		
		}
	}
}elseif($op == 'rival-alexa'){
	if($op1 == 'export'){
		if($formhash == formhash() && isset($_SESSION['tempdata'])){
			chaojiapp_export_rival_alexa();
		}else{
			cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=rival&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
		}
	}else{
		$result37 = chaojiapp_soap('Seo_GetCompeteWebsiteAnalysis', array('actionType' => 37, 'beginDate' => $st, 'endDate' => $et, 'accessToken' => getcookie('chaojiapp_access_token')));
		$result371 = $result37['Seo_GetCompeteWebsiteAnalysisResult'];
		$jsondata = $cjson->decode($result371);
		chaojiapp_api_error($jsondata['code'], 'rival&op=rival-alexa&st=' . $st . '&et=' . $et);
		$jsondata = $jsondata['data'];
		if($jsondata['isviewdata']){
			if($jsondata['rivalcount'] > 0){
				chaojiapp_search_form();
				$_SESSION['tempdata'] = $jsondata;
				include template('chaoji_com:rival-alexa');
			}else{
				include template('chaoji_com:rival-no-site');
			}
		}else{
			if($jsondata['typeid'] == '1'){
				if($jsondata['downloadurl'] == ''){
					$textarr = explode(chr(29), $jsondata['showtext']);
				}
			}
			include template('chaoji_com:cannotviewdata');		
		}	
	}
}
?>