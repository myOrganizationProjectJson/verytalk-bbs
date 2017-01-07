<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
require_once(dirname(__FILE__) . '/include/function.php');

$op = isset($_GET['op']) ? trim($_GET['op']) : '';

if($op == 'reopen'){
	require_once(dirname(__FILE__) . '/include/check.php');
	if(!submitcheck('confirmreopen')){	
		ob_end_clean();
		$IMGDIR = $_G['style']['imgdir'];
		$STYLEID = $_G['setting']['styleid'];
		$VERHASH = $_G['style']['verhash'];
		$frame = getgpc('frame') != 'no' ? 1 : 0;
		$charset = CHARSET;
		$basescript = ADMINSCRIPT;
		include template('chaoji_com:openseodatamonitor');		
		define(FOOTERDISABLED, false);
		exit;
	}else{
		$result15 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 15, 'strParams' => '', 'accessToken' => getcookie('chaojiapp_access_token')));
		$result151 = $result15['Seo_MonitorWebsiteManagerResult'];
		$jsondata = $cjson->decode($result151);
		chaojiapp_api_error($jsondata['code'], 'setting&op=reopen');
		ob_end_clean();
		if($jsondata['code'] == '0'){
			// 成功
			artdialog_jump(chaojiapp_lang('no_data7'), chaojiapp_code($jsondata['data']), CJ_PLUGIN_URL . 'pmod=overview');
		}else{
			// 失败
			artdialog_jump(chaojiapp_lang('no_data7'), chaojiapp_code($jsondata['msg']), CJ_PLUGIN_URL . 'pmod=overview');
		}
		define(FOOTERDISABLED, false);
		exit;
	}
}elseif($op == 'feedback'){
	if(!submitcheck('submitfeed')){
		$pageurl = isset($_GET['pageurl']) ? $_GET['pageurl'] : 'Discuz Plugin';
		ob_end_clean();
		$IMGDIR = $_G['style']['imgdir'];
		$STYLEID = $_G['setting']['styleid'];
		$VERHASH = $_G['style']['verhash'];
		$frame = getgpc('frame') != 'no' ? 1 : 0;
		$charset = CHARSET;
		$basescript = ADMINSCRIPT;
		include template('chaoji_com:dialog-feedback');		
		define(FOOTERDISABLED, false);
		exit;
	}else{
		ob_end_clean();
		$title = isset($_GET['title']) ? $_GET['title'] : '';
		$content = isset($_GET['content']) ? $_GET['content'] : '';
		$pageurl = isset($_GET['pageurl']) ? $_GET['pageurl'] : '';
		
		if($title == ''){
			$errmsg = chaojiapp_lang('feedbacktitlenotempty');
			$IMGDIR = $_G['style']['imgdir'];
			$STYLEID = $_G['setting']['styleid'];
			$VERHASH = $_G['style']['verhash'];
			$frame = getgpc('frame') != 'no' ? 1 : 0;
			$charset = CHARSET;
			$basescript = ADMINSCRIPT;
			include template('chaoji_com:dialog-feedback');
			define(FOOTERDISABLED, false);
			exit();			
		}
		if($content == ''){
			$errmsg = chaojiapp_lang('feedbackcontentnotempty');
			$IMGDIR = $_G['style']['imgdir'];
			$STYLEID = $_G['setting']['styleid'];
			$VERHASH = $_G['style']['verhash'];
			$frame = getgpc('frame') != 'no' ? 1 : 0;
			$charset = CHARSET;
			$basescript = ADMINSCRIPT;
			include template('chaoji_com:dialog-feedback');
			define(FOOTERDISABLED, false);
			exit();			
		}	

		if(dstrlen($content) < 20){
			$errmsg = chaojiapp_lang('feedbackcontenttoolittle');
			$content = $content;
			$IMGDIR = $_G['style']['imgdir'];
			$STYLEID = $_G['setting']['styleid'];
			$VERHASH = $_G['style']['verhash'];
			$frame = getgpc('frame') != 'no' ? 1 : 0;
			$charset = CHARSET;
			$basescript = ADMINSCRIPT;
			include template('chaoji_com:dialog-feedback');
			define(FOOTERDISABLED, false);
			exit();				
		}
		
		$result = chaojiapp_soap('App_SendFeedback', array('accessToken' => getcookie('chaojiapp_access_token'), 'title' => chaojiapp_xls_charset($title), 'content' => chaojiapp_xls_charset($content), 'pageurl' => chaojiapp_xls_charset($pageurl)));
		$result1 = $result['App_SendFeedbackResult'];
		$jsondata = $cjson->decode($result1);
		if($jsondata['code'] == '0'){
			artdialog_jump(chaojiapp_lang('feedback'), chaojiapp_lang('feedback-success'), '');
		}else{
			artdialog_jump(chaojiapp_lang('feedback'), chaojiapp_code($jsondata['msg']), '');
		}
	}	
}elseif($op == 'detail'){
	ob_end_clean();
	$IMGDIR = $_G['style']['imgdir'];
	$STYLEID = $_G['setting']['styleid'];
	$VERHASH = $_G['style']['verhash'];
	$frame = getgpc('frame') != 'no' ? 1 : 0;
	$charset = CHARSET;
	$basescript = ADMINSCRIPT;	
	$result16 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 16, 'strParams' => '', 'accessToken' => getcookie('chaojiapp_access_token')));
	$result161 = $result16['Seo_MonitorWebsiteManagerResult'];
	$jsondata = $cjson->decode($result161);
	chaojiapp_api_error($jsondata['code'], 'setting&op=detail');
	$jsondata = $jsondata['data'];

	include template('chaoji_com:dialog-customdetails');
	define(FOOTERDISABLED, false);
	exit;
}elseif($op == 'upgrade'){
	require_once(dirname(__FILE__) . '/include/check.php');
	if(!submitcheck('confirmupgrade')){
		session_start();
		// 保存到session中，如果是最高版本了直接读取session
		if(isset($_SESSION['result31'])){
			$result31 = $_SESSION['result31'];
			$jsondata = $cjson->decode($result31);
		}else{
			$result3 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 3, 'strParams' => '', 'accessToken' => getcookie('chaojiapp_access_token')));
			$result31 = $result3['Seo_MonitorWebsiteManagerResult'];
			if(strpos($result31, 'ismaxtypeid') !== FALSE){
				$_SESSION['result31'] = $result31;
			}
			$jsondata = $cjson->decode($result31);
		}
		chaojiapp_api_error($jsondata['code'], 'setting&op=upgrade');
		$jsondata = $jsondata['data'];
		
		ob_end_clean();
		if($jsondata['ismaxtypeid']){
			// 判断是否是最高版本,弹出提示
			$ismaxtypeid = '1';
		}else{
			$ismaxtypeid = '0';
		}
		// chaojiapp_api_error($jsondata['code'], 'setting&op=upgrade');
		// var_dump($result31);
		// exit;
		// exit;
		//chaojiapp_log(getcookie('chaojiapp_access_token'));
		if($jsondata['monitorwebsite']['customserviceinfo']){
			$customserviceinfo = $jsondata['monitorwebsite']['customserviceinfo'];
		}else{
			$customserviceinfo = 'false';
		}
		
		$IMGDIR = $_G['style']['imgdir'];
		$STYLEID = $_G['setting']['styleid'];
		$VERHASH = $_G['style']['verhash'];
		$frame = getgpc('frame') != 'no' ? 1 : 0;
		$charset = CHARSET;
		$basescript = ADMINSCRIPT;
		include template('chaoji_com:dialog-upgrade');
		define(FOOTERDISABLED, false);
		exit;
	}else{
		ob_end_clean();
		$leftmoney = isset($_GET['leftmoney']) ? $_GET['leftmoney'] : 0;
		$realpaymoney = isset($_GET['realpaymoney']) ? $_GET['realpaymoney'] : 0;
		if($leftmoney >= $realpaymoney){		
			// 抵用券套餐类型,抵用券服务周期,抵用券ID,抵用券周期累心
			$vouchers = isset($_GET['vouchers']) ? $_GET['vouchers'] : '';
			// 升级套餐类型
			$packagetype = isset($_GET['packagetype']) ? $_GET['packagetype'] : 0;
			// 升级时长
			$open_month = isset($_GET['open_month']) ? $_GET['open_month'] : 0;
			// 是否自动续费
			$automaticrenewal = isset($_GET['automaticrenewal']) ? $_GET['automaticrenewal'] : 0;
			// 是否使用红包抵用
			$useredpacket = isset($_GET['useredpacket']) ? $_GET['useredpacket'] : 0;
			
			$strParams = urlencode(chaojiapp_xls_charset($vouchers . chr(29) . $packagetype . chr(29) . $open_month . chr(29) . $automaticrenewal . chr(29) . $useredpacket));
			
		
			$result4 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 4, 'strParams' => $strParams, 'accessToken' => getcookie('chaojiapp_access_token')));
			$result41 = $result4['Seo_MonitorWebsiteManagerResult'];
			$jsondata = $cjson->decode($result41);
			chaojiapp_api_error($jsondata['code'], 'setting&op=upgrade');
			if($jsondata['code'] > 0){
				$hasexception = true;
				$msg = chaojiapp_code($jsondata['data']['msg']);
				include template('chaoji_com:dialog-confirm');
			}else{
				$hasexception = false;
				$msg = sprintf(chaojiapp_lang('upgrade-success-confirm'), chaojiapp_code($jsondata['data']['websitename']), $jsondata['data']['host']);
				include template('chaoji_com:dialog-confirm');
			}
		}else{
			$IMGDIR = $_G['style']['imgdir'];
			$STYLEID = $_G['setting']['styleid'];
			$VERHASH = $_G['style']['verhash'];
			$frame = getgpc('frame') != 'no' ? 1 : 0;
			$charset = CHARSET;
			$basescript = ADMINSCRIPT;
			include template('chaoji_com:dialog-balanceless');			
		}		
		define(FOOTERDISABLED, false);
		exit;
	}
}elseif($op == 'renewal'){
	require_once(dirname(__FILE__) . '/include/check.php');
	if(!submitcheck('confirmrenewal')){
		$result1 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 1, 'strParams' => '', 'accessToken' => getcookie('chaojiapp_access_token')));
		$result11 = $result1['Seo_MonitorWebsiteManagerResult'];
		
		$jsondata = $cjson->decode($result11);
		chaojiapp_api_error($jsondata['code'], 'setting&op=renewal');
		$jsondata = $jsondata['data'];
		ob_end_clean();
		$IMGDIR = $_G['style']['imgdir'];
		$STYLEID = $_G['setting']['styleid'];
		$VERHASH = $_G['style']['verhash'];
		$frame = getgpc('frame') != 'no' ? 1 : 0;
		$charset = CHARSET;
		$basescript = ADMINSCRIPT;
		include template('chaoji_com:dialog-renewal');
		define(FOOTERDISABLED, false);
		exit;
	}else{
		ob_end_clean();
		$leftmoney = isset($_GET['leftmoney']) ? $_GET['leftmoney'] : 0;
		$realpaymoney = isset($_GET['realpaymoney']) ? $_GET['realpaymoney'] : 0;
		if($leftmoney >= $realpaymoney){
			
			// 抵用券套餐类型,抵用券服务周期,抵用券ID,抵用券周期累心
			$vouchers = isset($_GET['vouchers']) ? $_GET['vouchers'] : ''; 
			//续费时长
			$open_month = isset($_GET['open_month']) ? $_GET['open_month'] : 1;
			//是否自动续费
			$automaticrenewal = isset($_GET['automaticrenewal']) ? $_GET['automaticrenewal'] : 0;
			//是否使用红包抵用
			$useredpacket = isset($_GET['useredpacket']) ? $_GET['useredpacket'] : 0;
			
			$strParams = urlencode(chaojiapp_xls_charset($vouchers . chr(29) . $open_month . chr(29) . $automaticrenewal . chr(29) . $useredpacket));
			
		
			$result2 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 2, 'strParams' => $strParams, 'accessToken' => getcookie('chaojiapp_access_token')));
			$result21 = $result2['Seo_MonitorWebsiteManagerResult'];
			$jsondata = $cjson->decode($result21);
			chaojiapp_api_error($jsondata['code'], 'setting&op=upgrade');
			// var_dump($result21, $strParams);
			if($jsondata['code'] > 0){
				$hasexception = true;
				$msg = chaojiapp_code($jsondata['data']['msg']);
				include template('chaoji_com:dialog-confirm');
			}else{
				$hasexception = false;
				$msg = sprintf(chaojiapp_lang('renewal-success-confirm'), chaojiapp_code($jsondata['data']['websitename']), $jsondata['data']['host'], $jsondata['data']['expireddate']);
				include template('chaoji_com:dialog-confirm');
			}
		}else{
			$IMGDIR = $_G['style']['imgdir'];
			$STYLEID = $_G['setting']['styleid'];
			$VERHASH = $_G['style']['verhash'];
			$frame = getgpc('frame') != 'no' ? 1 : 0;
			$charset = CHARSET;
			$basescript = ADMINSCRIPT;
			include template('chaoji_com:dialog-balanceless');
		}
		define(FOOTERDISABLED, false);
		exit;		
	}
}elseif($op == 'add-rival-site'){
	require_once dirname(__FILE__) . '/include/check.php';
	if(!submitcheck('saverivalsite')){	
		$errmsg = '';
		ob_end_clean();
		$IMGDIR = $_G['style']['imgdir'];
		$STYLEID = $_G['setting']['styleid'];
		$VERHASH = $_G['style']['verhash'];
		$frame = getgpc('frame') != 'no' ? 1 : 0;
		$charset = CHARSET;
		$basescript = ADMINSCRIPT;
		include template('chaoji_com:dialog-add-rival');
		define(FOOTERDISABLED, false);
		exit();		
	}else{
		$websitename = isset($_GET['websitename']) ? trim($_GET['websitename']) : '';
		$host = isset($_GET['host']) ? trim($_GET['host']) : '';
		$datacolor = isset($_GET['datacolor']) ? trim($_GET['datacolor']) : '';
		
		ob_end_clean();
		
		if($websitename == ''){
			$errmsg = chaojiapp_lang('websitenamenotempty');
			$IMGDIR = $_G['style']['imgdir'];
			$STYLEID = $_G['setting']['styleid'];
			$VERHASH = $_G['style']['verhash'];
			$frame = getgpc('frame') != 'no' ? 1 : 0;
			$charset = CHARSET;
			$basescript = ADMINSCRIPT;
			include template('chaoji_com:dialog-add-rival');
			define(FOOTERDISABLED, false);
			exit();			
		}elseif($host == '' || preg_match('/^[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/', $host) === FALSE){
			$errmsg = chaojiapp_lang('rivalhostnotempty');
			$IMGDIR = $_G['style']['imgdir'];
			$STYLEID = $_G['setting']['styleid'];
			$VERHASH = $_G['style']['verhash'];
			$frame = getgpc('frame') != 'no' ? 1 : 0;
			$charset = CHARSET;
			$basescript = ADMINSCRIPT;
			include template('chaoji_com:dialog-add-rival');
			define(FOOTERDISABLED, false);
			exit();			
		}else{
		
			$result8 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 8, 'strParams' => urlencode(chaojiapp_xls_charset($websitename . chr(29) . $host . chr(29) . $datacolor)), 'accessToken' => getcookie('chaojiapp_access_token')));
			$result81 = $result8['Seo_MonitorWebsiteManagerResult'];
			
			$jsondata = $cjson->decode($result81);
			chaojiapp_api_error($jsondata['code'], 'setting&op=add-rival-site');
			if($jsondata['code'] == '0'){
				// 成功
				artdialog_jump(chaojiapp_lang('add-rival-site'), chaojiapp_lang('add-rival-site-success'), CJ_PLUGIN_URL . 'pmod=rival&op=rival-site-list');
			}else{
				// 失败
				$errmsg = chaojiapp_code($jsondata['msg']);
				$IMGDIR = $_G['style']['imgdir'];
				$STYLEID = $_G['setting']['styleid'];
				$VERHASH = $_G['style']['verhash'];
				$frame = getgpc('frame') != 'no' ? 1 : 0;
				$charset = CHARSET;
				$basescript = ADMINSCRIPT;
				include template('chaoji_com:dialog-add-rival');
				define(FOOTERDISABLED, false);
				exit();
			}
		}
		define(FOOTERDISABLED, false);
		exit();	
	}
}elseif($op == 'edit-rival-site'){
	require_once dirname(__FILE__) . '/include/check.php';
	if(!submitcheck('saverivalsite')){
		ob_end_clean();
		$IMGDIR = $_G['style']['imgdir'];
		$STYLEID = $_G['setting']['styleid'];
		$VERHASH = $_G['style']['verhash'];
		$frame = getgpc('frame') != 'no' ? 1 : 0;
		$charset = CHARSET;
		$basescript = ADMINSCRIPT;
		
		$rivalid = $_GET['rivalid'];
		$websitename = $_GET['rivalname'];
		$host = $_GET['rivalhost'];
		$datacolor = $_GET['datacolor'];
		
		include template('chaoji_com:dialog-edit-rival');
		define(FOOTERDISABLED, false);
		exit();		
	}else{
		$rivalid = $_GET['rivalid'];
		$websitename = isset($_GET['websitename']) ? trim($_GET['websitename']) : '';
		$host = $_GET['rivalhost'];
		$datacolor = isset($_GET['datacolor']) ? trim($_GET['datacolor']) : '';
		
		ob_end_clean();
		if($websitename == ''){
			$datacolor = str_replace('#', '', $datacolor);
			$errmsg = chaojiapp_lang('websitenamenotempty');
			$IMGDIR = $_G['style']['imgdir'];
			$STYLEID = $_G['setting']['styleid'];
			$VERHASH = $_G['style']['verhash'];
			$frame = getgpc('frame') != 'no' ? 1 : 0;
			$charset = CHARSET;
			$basescript = ADMINSCRIPT;
			include template('chaoji_com:dialog-edit-rival');
			define(FOOTERDISABLED, false);
			exit();			
					
		}else{
			$result9 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 9, 'strParams' => urlencode(chaojiapp_xls_charset($rivalid . chr(29) . $websitename . chr(29) . $datacolor)), 'accessToken' => getcookie('chaojiapp_access_token')));
			$result91 = $result9['Seo_MonitorWebsiteManagerResult'];
			$jsondata = $cjson->decode($result91);

			chaojiapp_api_error($jsondata['code'], 'setting&op=edit-rival-site&rivalid=' . $rivalid);
			if($jsondata['code'] == '0'){
				// 成功
				artdialog_jump(chaojiapp_lang('edit-rival-site'), chaojiapp_lang('edit-rival-site-success'), CJ_PLUGIN_URL . 'pmod=rival&op=rival-site-list');
			}else{
				// 失败
				$errmsg = chaojiapp_code($jsondata['msg']);
				$IMGDIR = $_G['style']['imgdir'];
				$STYLEID = $_G['setting']['styleid'];
				$VERHASH = $_G['style']['verhash'];
				$frame = getgpc('frame') != 'no' ? 1 : 0;
				$charset = CHARSET;
				$basescript = ADMINSCRIPT;
				include template('chaoji_com:dialog-edit-rival');
				define(FOOTERDISABLED, false);
				exit();					
			}
			define(FOOTERDISABLED, false);
			exit();	
		}
	}
}elseif($op == 'delete-rival-site'){
	if(!submitcheck('todelete')){
		$rivalid = $_GET['rivalid'];
		// $errmsg = chaojiapp_code($jsondata['msg']);
		$IMGDIR = $_G['style']['imgdir'];
		$STYLEID = $_G['setting']['styleid'];
		$VERHASH = $_G['style']['verhash'];
		$frame = getgpc('frame') != 'no' ? 1 : 0;
		$charset = CHARSET;
		$basescript = ADMINSCRIPT;
		include template('chaoji_com:dialog-confirm-delete-rival');
		define(FOOTERDISABLED, false);
		exit();		
	}else{
		$rivalid = intval($_GET['rivalid']);
		$result10 = chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 10, 'accessToken' => getcookie('chaojiapp_access_token'), 'strParams' => $rivalid));
		
		$result101 = $result10['Seo_MonitorWebsiteManagerResult'];
		$jsondata = $cjson->decode($result101);
		chaojiapp_api_error($jsondata['code'], 'setting&op=delete-rival-site&rivalid=' . $rivalid);
		ob_end_clean();
		$IMGDIR = $_G['style']['imgdir'];
		$STYLEID = $_G['setting']['styleid'];
		$VERHASH = $_G['style']['verhash'];
		$frame = getgpc('frame') != 'no' ? 1 : 0;
		$charset = CHARSET;
		$basescript = ADMINSCRIPT;		
		
		if($jsondata['code'] > 0){
			$hasexception = true;
			$msg = chaojiapp_code($jsondata['msg']);
			include template('chaoji_com:dialog-confirm');
		}else{
			// artdialog_jump(chaojiapp_lang('delete-rival-site'), chaojiapp_lang('delete-rival-site-success'), CJ_PLUGIN_URL . 'pmod=rival&op=rival-site-list');
			$hasexception = false;
			$msg = chaojiapp_lang('delete-rival-site-success');
			include template('chaoji_com:dialog-confirm');
		}
		define(FOOTERDISABLED, false);
		exit();			
	}
}elseif($op == 'edit_keywords'){
	require_once dirname(__FILE__) . '/include/check.php';
	if(!submitcheck('save')){
		$datatype = $_GET['datatype'];
		$returnmod = $_GET['returnmod'];
		// 表示获取关键词列表信息
		$result5=chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 5, 'strParams' => '', 'accessToken' => getcookie('chaojiapp_access_token')));
		$result51 = $result5['Seo_MonitorWebsiteManagerResult'];
		$jsondata = $cjson->decode($result51);
		chaojiapp_api_error($jsondata['code'], 'setting&op=edit_keywords');
		$jsondata = $jsondata['data'];
		$websitename = iconv('utf-8', CHARSET, $jsondata['websitename']);
		$host = $jsondata['host'];
		$keywords = iconv('utf-8', CHARSET, $jsondata['keywords']);
		$userwebsiteid = getcookie('chaojiapp_websiteid');
		ob_end_clean();
		$IMGDIR = $_G['style']['imgdir'];
		$STYLEID = $_G['setting']['styleid'];
		$VERHASH = $_G['style']['verhash'];
		$frame = getgpc('frame') != 'no' ? 1 : 0;
		$charset = CHARSET;
		$basescript = ADMINSCRIPT;
		include template('chaoji_com:dialog-edit-keywords');
		define(FOOTERDISABLED, false);
		exit();
	}else{
		$datatype = $_GET['datatype'];
		$keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
		$websitename = $_GET['websitename'];
		$websiteHost = $_GET['websiteHost'];
		$host = $websiteHost;
		if($datatype == 'add' && $keywords == ''){
			$jsondata['keywordcount'] = 20;
			$errmsg = chaojiapp_lang('keywordnotempty');
			ob_end_clean();
			$IMGDIR = $_G['style']['imgdir'];
			$STYLEID = $_G['setting']['styleid'];
			$VERHASH = $_G['style']['verhash'];
			$frame = getgpc('frame') != 'no' ? 1 : 0;
			$charset = CHARSET;
			$basescript = ADMINSCRIPT;
			include template('chaoji_com:dialog-edit-keywords');
			define(FOOTERDISABLED, false);
			exit();			
		}else{

			$result6=chaojiapp_soap('Seo_MonitorWebsiteManager', array('actionType' => 6, 'strParams' => $keywords, 'accessToken' => getcookie('chaojiapp_access_token')));
			$result61 = $result6['Seo_MonitorWebsiteManagerResult'];
			$jsondata = $cjson->decode($result61);
			chaojiapp_api_error($jsondata['code'], 'setting&op=edit_keywords');
			ob_end_clean();
			if($jsondata['code'] > 0){
				if($datatype == 'add'){
					artdialog_jump(chaojiapp_lang('add_keywords'), $jsondata['msg'], CJ_PLUGIN_URL . 'pmod=overview');
				}else{
					if($keywords == ''){
						artdialog_jump(chaojiapp_lang('delete_keywords'), $jsondata['msg'], CJ_PLUGIN_URL . 'pmod=overview');
					}else{
						artdialog_jump(chaojiapp_lang('edit_keywords'), $jsondata['msg'], CJ_PLUGIN_URL . 'pmod=overview');
					}
				}		
			}else{
				if($datatype == 'add'){
					artdialog_jump(chaojiapp_lang('add_keywords'), chaojiapp_lang('add_keywords_success'), CJ_PLUGIN_URL . 'pmod=overview');
				}else{
					if($keywords == ''){
						artdialog_jump(chaojiapp_lang('delete_keywords'), chaojiapp_lang('delete_keywords_success'), CJ_PLUGIN_URL . 'pmod=overview');
					}else{
						artdialog_jump(chaojiapp_lang('edit_keywords'), chaojiapp_lang('edit_keywords_success'), CJ_PLUGIN_URL . 'pmod=overview');
					}
				}
			}
			define(FOOTERDISABLED, false);
			exit();
		}
	}
}elseif($op == 'data'){
	include_once(dirname(__FILE__) . '/include/check.php');
	require dirname(__FILE__) . '/data.php';
	
}else{
	echo '<script src="source/plugin/chaoji_com/resource/js/My97DatePicker/WdatePicker.js" type="text/javascript"></script><script type="text/javascript" language="javascript" src="source/plugin/chaoji_com/resource/js/jquery.min.js"></script>
<script type="text/javascript">var jq = jQuery.noConflict();var cookiepre=\'' . $_G['config']['cookie']['cookiepre'] . '\';var disallowfloat=false;var clientinfo_status=0;var clientinfo_str=\'\';var PLUGIN_URL = \'' . CJ_PLUGIN_URL . '\'; var FORMHASH = \'' . FORMHASH . '\';</script>
<script type="text/javascript" src="source/plugin/chaoji_com/resource/js/highcharts.js" language="javascript"></script>
<script type="text/javascript" src="source/plugin/chaoji_com/resource/js/tableorder.js" language="javascript"></script>
<script src="source/plugin/chaoji_com/resource/js/template-native.js" type="text/javascript"></script><script src="source/plugin/chaoji_com/resource/js/cj-rivalcompare.js" type="text/javascript"></script>
<link href="source/plugin/chaoji_com/resource/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="source/plugin/chaoji_com/resource/js/script.js"></script><style type="text/css">.tb2 td{border-top:1px dotted #DEEFFB;}</style>';
	chaojiapp_top_menu('setting');
	if(!submitcheck('formsubmit')) {
	
		$errormsg = isset($_GET['errormsg']) ? $_GET['errormsg'] : '';
	
		$settinginfo = chaojiapp_setting_info();
		if(count($settinginfo)>0){
			$appid = $settinginfo['appid'];
			$appsecret = $settinginfo['appsecret'];
			$id = $settinginfo['id'];
		}else{
			$appid = '';
			$appsecret = '';
			$id = '';
		}
		showformheader(CJ_FORM_URL . 'pmod=setting&id=' . $id, 'formsubmit');
		showtableheader();
		
		
		
		showtitle(chaojiapp_lang('setting_tips'));
		
		showtablerow('', ' colspan="5"', chaojiapp_lang('setting_content'));
		showtablerow('', ' colspan="5"', chaojiapp_lang('setting_content1'));	
		
		showtitle(chaojiapp_lang('setting1'));	
		showsetting(chaojiapp_lang('appid'), 'appid', $appid, 'text', '', 0, '', '', '', true);
		showsetting(chaojiapp_lang('appsecret'), 'appsecret', $appsecret, 'text', '', 0, '', '', '', true);
		
		if($errormsg){
			showsubmit('formsubmit', 'submit', '', '<span style="color:red;">' . $errormsg . '</span>');
		}else{
			showsubmit('formsubmit');
		}
		showtablefooter();
		showformfooter();
	}else{
		$appid = trim($_GET['appid']);
		$appsecret = trim($_GET['appsecret']);
		$id = intval($_GET['id']);
		if(!$appid){
			cpmsg(chaojiapp_lang('appid') . chaojiapp_lang('empty'), '', 'error');
		}
		if(!$appsecret){
			cpmsg(chaojiapp_lang('appsecret') . chaojiapp_lang('empty'), '', 'error');
		}	
		
		// 验证appid appsecret的正确性
		$obj1 = chaojiapp_get_access_token($appid, $appsecret);
		if($obj1->code == '0'){
			
			// 
			$platform_name = 'Discuz';
			$platform_language = 'PHP';
			$platform_version = DISCUZ_VERSION;
			$platform_plugin_version = CJ_PLUGIN_VERSION;
			$platformParams = urlencode(chaojiapp_xls_charset($platform_name . chr(29) . $platform_language . chr(29) . $platform_version . chr(29) . $platform_plugin_version));
			$result = chaojiapp_soap('App_UpdatePlatformInfo', array('accessToken' => $obj1->data->accesstoken, 'platformInfos' => $platformParams));
			
			
			$data = array(
				'appid' => $appid,
				'appsecret' => $appsecret,
			);
			if(!$id){
				DB::insert('chaojiapp_setting', $data);
			}else{
				DB::update('chaojiapp_setting', $data, DB::field('id', $id));
			}
			dsetcookie('chaojiapp_access_token', '', -31536000);
			dsetcookie('chaojiapp_access_token1', '', -31536000);
			session_start();
			unset($_SESSION['tempdata']);
			unset($_SESSION['result31']);
			cpmsg(chaojiapp_lang('edit_setting_success'), CJ_PLUGIN_URL . "pmod=overview", 'succeed');	
		}else {
			header('Location:' . CJ_PLUGIN_URL . 'pmod=setting&errormsg=' . chaojiapp_code($obj1->msg));
		}
	}

}			
?>