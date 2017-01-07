<?php 
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

include_once dirname(__FILE__) . '/json.class.php';
require_once dirname(__FILE__) . '/nusoap/nusoap.php';
$cjson = new CJSON();

define('CJ_PLUGIN_VERSION', '1.0.3');
// 每天的开始小时
define('CJ_NEW_DAY_BY_HOUR', 9);
// 竞争网站默认个数
define('CJ_RIVAL_SITE_NUM', 3);

define('CJ_PLUGIN_URL', $_G['siteurl'] . ADMINSCRIPT . '?action=plugins&operation=config&do=' . $pluginid . '&identifier=chaoji_com&');
define('CJ_FORM_URL', 'plugins&operation=config&do=' . $pluginid . '&identifier=chaoji_com&');

// 数据接口地址
define('CJ_API_URL', 'http://api.chaoji.com/App.asmx');

define('CJ_OVERVIEW_SITEINFO_URL', '');
define('CJ_OVERVIEW_24HOURS_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=data1&callback=?');
define('CJ_OVERVIEW_SERVER_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=data2&callback=?');
define('CJ_OVERVIEW_KEYWORD_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=data3&callback=?');
define('CJ_OVERVIEW_24HOURSKEYWORD_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=data4&callback=?');
define('CJ_MONITOR_TODAY_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=today&callback=?');
define('CJ_MONITOR_SITE_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=site&callback=?');
define('CJ_MONITOR_SITE_DATE_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=baidupages&callback=?');
define('CJ_MONITOR_LINK_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=link&callback=?');
define('CJ_MONITOR_RANK_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=rank&callback=?');
define('CJ_MONITOR_KEYWORD_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=keyword&callback=?');
define('CJ_MONITOR_KEYWORD_TREND_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=trend&callback=?');
define('CJ_MONITOR_ALEXADATA_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=alexadata&callback=?');
define('CJ_MONITOR_SNAPDATE_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=snapdate&callback=?');
define('CJ_MONITOR_IPCHANGE_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=ipchange&callback=?');
define('CJ_RIVAL_PAGESEO_URL', CJ_PLUGIN_URL . 'pmod=setting&op=data&act=pageseo');

// 搜索查询html
function chaojiapp_search_form(){
	global $st, $et, $t, $op, $jsondata;


$searchform = '
	
	<a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(dgmdate(TIMESTAMP - 86400 * 6, 'Y-m-d')) . '&et=' . urlencode(dgmdate(TIMESTAMP, 'Y-m-d')) . '&t=3" ' . ($t=='3' ? 'class="cur_date"' : '') . '>' . chaojiapp_lang('last7days') . '</a> | 
	
	<a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(dgmdate(TIMESTAMP - 86400 * 29, 'Y-m-d')) . '&et=' . urlencode(dgmdate(TIMESTAMP, 'Y-m-d')) . '&t=4" ' . ($t=='4' ? 'class="cur_date"' : '') . '>' . chaojiapp_lang('last30days') . '</a> |
	
	<a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(date('Y-m-01')) . '&et=' . urlencode(date('Y-m-' . cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')) . '')) . '&t=5" ' . ($t=='5' ? 'class="cur_date"' : '') . '>' . chaojiapp_lang('this_month') . '</a> ';
	
	switch($t){
		case '3':
			$searchform .= '<input type="button" value="' . chaojiapp_lang('7daysbefore') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) - 86400 * 7, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) - 86400 * 7, 'Y-m-d')) . '&t=' . $t . '\';" /> 
	
	<input type="button" value="' . chaojiapp_lang('7daysafter') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) + 86400 * 7, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) + 86400 * 7, 'Y-m-d')) . '&t=' . $t . '\';" ' . (((dmktime($st) + 86400 *7) >= dmktime(date('Y-m-d'))) ? 'disabled="disabled"' : '') . ' /> ';
			break;
		case '4':
			// 前30天
			
			$searchform .= '<input type="button" value="' . chaojiapp_lang('30daysbefore') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) - 86400 * 30, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) - 86400 * 30, 'Y-m-d')) . '&t=' . $t . '\';" /> 
	
	<input type="button" value="' . chaojiapp_lang('30daysafter') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) + 86400 * 30, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) + 86400 * 30, 'Y-m-d')) . '&t=' . $t . '\';" ' . (((dmktime($st) + 86400 *30) >= dmktime(date('Y-m-d'))) ? 'disabled="disabled"' : '') . ' /> ';
			break;
		case '5':
			// 上一个月
			$thismonth = date('m', dmktime($st));
			$thisyear = date('Y', dmktime($st));
			if($thismonth == 1) {
				$lastmonth = 12;
				$lastyear = $thisyear - 1;
			} else {
				$lastmonth = $thismonth - 1;
				$lastyear = $thisyear;
			}
			if($thismonth == 12){
				$nextmonth = 1;
				$nextyear = $thisyear + 1;
			}else{
				$nextmonth = $thismonth + 1;
				$nextyear = $thisyear;
			}
			$last_month_m = $lastmonth;
			$last_month_y = $lastyear;
			$next_month_m = $nextmonth;
			$next_month_y = $nextyear;
			
			$searchform .= '<input type="button" value="' . chaojiapp_lang('last_month') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(date($last_month_y . '-' . $last_month_m . '-01')) . '&et=' . urlencode(date($last_month_y . '-' . $last_month_m . '-' . cal_days_in_month(CAL_GREGORIAN, $last_month_m, $last_month_y) . '')) . '&t=' . $t . '\';" /> 
	
	<input type="button" value="' . chaojiapp_lang('next_month') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(date($next_month_y . '-' . $next_month_m . '-01')) . '&et=' . urlencode(date($next_month_y . '-' . $next_month_m . '-' . cal_days_in_month(CAL_GREGORIAN, $next_month_m, $next_month_y) . '')) . '&t=' . $t . '\';" ' . (($next_month_m > date('m')) ? 'disabled="disabled"' : '') . ' /> ';
			break;
		case '6':
			break;
		case '1':
		case '2':
		default:
			$searchform .= '<input type="button" value="' . chaojiapp_lang('one_day') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) - 86400 * 1, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) - 86400 * 1, 'Y-m-d')) . '&t=' . $t . '\';" /> 
	
	<input type="button" value="' . chaojiapp_lang('next_day') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) + 86400 * 1, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) + 86400 * 1, 'Y-m-d')) . '&t=' . $t . '\';" /> ';
			break;
	}
	
	
	
	$searchform .= '<input type="text" class="txt" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\',minDate:\'2009-11-23\',maxDate:\'#F{$dp.$D(\\\'et\\\')}\'});" name="st" id="st" value="' . $st . '" title="' . chaojiapp_lang('st_title') . '" readonly="readonly" />' . chaojiapp_lang('date_to') . ' <input type="text" class="txt" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\',minDate:\'#F{$dp.$D(\\\'st\\\')}\',maxDate:\'' . $maxdate . '\'});" name="et" id="et" value="' . $et . '" title="' . chaojiapp_lang('et_title') . '" readonly="readonly" />';
	showformheader(CJ_FORM_URL . 'pmod=rival&op=' . $op, 'formsubmit');
	showtableheader();
	chaojiapp_showsubmit('formsubmit', chaojiapp_lang('search'), $searchform, ($jsondata['typeinfo']['isexport'] ? '<div style="position:absolute;top:0px;right:0px;" id="exportlink"><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=' . $op . '&op1=export&formhash=' . FORMHASH . '"><img src="source/plugin/chaoji_com/resource/images/export.png" style="vertical-align: top;" /> ' . chaojiapp_lang('export_data') . '</a></div>' : ''));
	showtablefooter();
	showformfooter();	
}

// cxpform语言包
function chaojiapp_lang($key){
	return lang('plugin/chaoji_com', $key);
}

// 得到配置
function chaojiapp_setting_info(){
	$row = DB::fetch_first('select id, appid, appsecret from ' . DB::table('chaojiapp_setting') . ' where id=1');
	
	return $row;
}

// 
function chaojiapp_showsubmit($name = '', $value = 'submit', $before = '', $after = '', $floatright = '', $entersubmit = true) {
	global $_G;
	if(!empty($_G['showsetting_multi'])) {
		return;
	}
	$str = '<tr>';
	$str .= $name && in_array($before, array('del', 'select_all', 'td')) ? '<td class="td25">'.($before != 'td' ? '<input type="checkbox" name="chkall" id="chkall'.($chkkallid = random(4)).'" class="checkbox" onclick="checkAll(\'prefix\', this.form, \'delete\')" /><label for="chkall'.$chkkallid.'">'.cplang($before) : '').'</label></td>' : '';
	$str .= '<td colspan="15" style="border-top:0px;">';
	$str .= $floatright ? '<div class="cuspages right">'.$floatright.'</div>' : '';
	$str .= '<div class="fixsel">';
	$str .= $before && !in_array($before, array('del', 'select_all', 'td')) ? $before.' &nbsp;' : '';
	$str .= $name ? '<input type="submit" class="btn" id="submit_'.$name.'" name="'.$name.'" title="'.($entersubmit ? cplang('submit_tips') : '').'" value="'.cplang($value).'" />' : '';
	$after = $after == 'more_options' ? '<input class="checkbox" type="checkbox" value="1" onclick="$(\'advanceoption\').style.display = $(\'advanceoption\').style.display == \'none\' ? \'\' : \'none\'; this.value = this.value == 1 ? 0 : 1; this.checked = this.value == 1 ? false : true" id="btn_more" /><label for="btn_more">'.cplang('more_options').'</label>' : $after;
	$str = $after ? $str.(($before && $before != 'del') || $name ? ' &nbsp;' : '').$after : $str;
	$str .= '</div></td>';
	$str .= '</tr>';
	echo $str.($name && $entersubmit ? '<script type="text/JavaScript">_attachEvent(document.documentElement, \'keydown\', function (e) { entersubmit(e, \''.$name.'\'); });</script>' : '');
}

// 特殊处理数据
function chaojiapp_spec_data($val){
	if($val == '-1' || $val == '-2' || $val == null){
		return '--';
	}else{
		return chaojiapp_code($val);
	}
}

// 计算字数

/**
 * UTF-8编码情况下 *
 * 计算字符串的长度 *
 * @param   string      $str        字符串
 *
 * @return  array
 */
function chaojiapp_strLength($str, $charset)
{
    $ccLen=0; 
	$ascLen=strlen($str); 
	$ind=0; 
	$hasCC=ereg("[xA1-xFE]",$str); #判断是否有汉字 
	$hasAsc=ereg("[x01-xA0]",$str); #判断是否有ASCII字符 
	if($hasCC && !$hasAsc) #只有汉字的情况 
	return strlen($str)/2; 
	if(!$hasCC && $hasAsc) #只有Ascii字符的情况 
	return strlen($str); 
	for($ind=0;$ind<$ascLen;$ind++) 
	{ 
		if(ord(substr($str,$ind,1))>0xa0) 
		{ 
			$ccLen++; 
			$ind++; 
		} 
		else 
		{ 
			$ccLen++; 
		} 
	} 
	return $ccLen;
}



// 把日期格式化颜色
function chaojiapp_formatdate($date){
	$w = dgmdate(dmktime($date), 'w');
	if($w == '0' || $w == '6'){
		return '<span class="red">' . $date . '</span>';
	}
	return $date;
}

// 导出时的周六周日输出
function chaojiapp_exportdate($date){
	$w = dgmdate(dmktime($date), 'w');
	if($w == '0'){
		return $date . chaojiapp_xls_charset(chaojiapp_lang('sunday'));
	}else if($w == '6'){
		return $date . chaojiapp_xls_charset(chaojiapp_lang('saturday'));
	}else{
		return $date;
	}
}

// 把数字转化成万单位
function chaojiapp_formatnumber($num){
	if($num == '-1' || $num == '-2' || $num == null){
		return '--';
	}
	if($num < 10000){
		return $num;
	}else{
		$str1 = intval($num / 10000) . chaojiapp_lang('w');
		$str2 = $num % 10000;
		$str =  $str1 . ($str2 == 0 ? '' : $str2);
		return $str;
	}
}

// 趋势
function chaojiapp_trend($type, $val){
	if($val == '--' || $val == '0'){
		if($type == 'alexa'){
			return '--';
		}else{
			return '';
		}
	}else{
		$val = intval($val);
		if($type == 'alexa1'){
			if($val > 0){
				return '<img src="source/plugin/chaoji_com/resource/images/arrow-down.png" title="' . chaojiapp_lang('trend_down') . $val . '">';
			}else{
				$val = abs($val);
				return '<img src="source/plugin/chaoji_com/resource/images/arrow-up.png" title="' . chaojiapp_lang('trend_up') . $val . '">';
			}		
		}elseif($type == 'alexa'){
			if($val > 0){
				return '<font class="red">' . $val . '</font> <img src="source/plugin/chaoji_com/resource/images/arrow-down.png" title="' . chaojiapp_lang('trend_down') . $val . '">';
			}else{
				$val = abs($val);
				return '<font class="green">' . $val . '</font> <img src="source/plugin/chaoji_com/resource/images/arrow-up.png" title="' . chaojiapp_lang('trend_up') . $val . '">';
			}
		}else{
			if($val > 0){
				return '<img src="source/plugin/chaoji_com/resource/images/arrow-up.png" title="' . chaojiapp_lang('trend_up') . $val . '">';
			}else{
				$val = abs($val);
				return '<img src="source/plugin/chaoji_com/resource/images/arrow-down.png" title="' . chaojiapp_lang('trend_down') . $val . '">';
			}		
		}
	}
}

// soap请求
function chaojiapp_soap($method, $params = array()){
	$client = new nusoap_client(CJ_API_URL . '?wsdl', 'wsdl');
	$client->soap_defencoding = CHARSET;
	// $client->decode_utf8 = false;  
	$client->xml_encoding = CHARSET;
	// $client->http_encoding = true;
	$client->use_curl = TRUE;
	if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
		$client->setCurlOption(CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	}	
	$result=$client->call($method, $params);
	$err = $client->getError();
	if ($err) {
		echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
		exit;
	}	
	if ($client->fault) {
		echo '<h2>Fault</h2><pre>';
		print_r($result);
		echo '</pre>';
		exit;
	} else {
		$err = $client->getError();
		if ($err) {
			// Display the error
			echo '<h2>Error</h2><pre>' . $err . '</pre>';
			exit;
		} else {
			// Display the result
			//echo '<h2>Result</h2><pre>';
			return $result;
			//echo '</pre>';
		}		
	}	
}

// 得到access_token
function chaojiapp_get_access_token($appid, $appsecret){
	$cjson = new CJSON();
	// $client = new SoapClient(CJ_API_URL . '?wsdl');	
	$result = chaojiapp_soap('App_GetCredential', array('appID' => $appid, 'appSecret' => $appsecret));
	$result = $result['App_GetCredentialResult'];
	$access_token = $cjson->decode($result, FALSE);
	return $access_token;
}

// 处理数据请求的错误
function chaojiapp_api_error($errcode, $mod = 'overview'){
	switch($errcode){
		// case '20003':
		case '20001':
			// 不合法的AccessToken
			// 删除cookie
			dsetcookie('chaojiapp_access_token', '', -31536000);
			// 重新获取页面
			header('Location:' . CJ_PLUGIN_URL . 'pmod=' . $mod);
			exit;
			break;
		case '30002':		//编辑关键词返回的错误信息
		case '30003':		//请求参数错误
		case '30004':		//添加竞争网站返回的错误信息
		case '30005':		//修改竞争网站返回的错误信息
		case '30006':	//删除竞争网站返回的错误信息
		case '30007':		//续费监控网站返回的错误信息
		case '30008':		//升级监控网站返回的错误信息
		case '30009':		//开通SEO数据监控返回的错误信息			
		case '0':
			// 什么也不做
			break;
		default:
			cpmsg(chaojiapp_lang('invalid access') . $errcode, CJ_PLUGIN_URL . 'pmod=setting', 'error');
			exit;
			break;
			
	}
}

function chaojiapp_log($content){
	$h = fopen('testlog.txt', 'a+');
	fwrite($h, $content . date('Y-m-d H:i:s') . "\n");
	fclose($h);
}


//导成csv编码
function chaojiapp_xls_charset($content){

	$content = chaojiapp_formatnumber($content);

	return iconv(CHARSET, 'utf-8', $content);
	// if(strtoupper(CHARSET) === 'GBK'){
		// return iconv('', 'utf-8', $content);
	// }elseif(strtolower(CHARSET) === 'big5'){	
		// return iconv('big5', 'gbk', $content);
	// }else{
		// return iconv('utf-8', 'gbk', $content);
	// }
}

// 
function chaojiapp_array_to_json_string($arraydata) {
	$output = "";
	$output .= "{";
	foreach($arraydata as $key=>$val){
		if (is_array($val)) {
			$output .= "\"".$key."\" : [{";
				foreach($val as $subkey=>$subval){
					$output .= "\"".$subkey."\" : \"".$subval."\",";
				}
			$output .= "}],";
		} else {
			$output .= "\"".$key."\" : \"".$val."\",";
		}
	}
	$output .= "}";
	return $output;
}

function chaojiapp_code($str){
	// return preg_replace("#\\\u([0-9a-f]{4}+)#ie", "iconv('UCS-2', " . CHARSET . ", pack('H4', '\\1'))", $str);
	return iconv('utf-8', CHARSET, $str);
}

/**
 * $str 原始中文字符串
 * $encoding 原始字符串的编码，默认GBK
 * $prefix 编码后的前缀，默认"&#"
 * $postfix 编码后的后缀，默认";"
 */
function chaojiapp_unicode_encode($str, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
    $str = iconv($encoding, 'UCS-2', $str);
    $arrstr = str_split($str, 2);
    $unistr = '';
    for($i = 0, $len = count($arrstr); $i < $len; $i++) {
        $dec = hexdec(bin2hex($arrstr[$i]));
        $unistr .= $prefix . $dec . $postfix;
    } 
    return $unistr;
} 
 
/**
 * $str Unicode编码后的字符串
 * $decoding 原始字符串的编码，默认GBK
 * $prefix 编码字符串的前缀，默认"&#"
 * $postfix 编码字符串的后缀，默认";"
 */
function chaojiapp_unicode_decode($unistr, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
    $arruni = explode($prefix, $unistr);
    $unistr = '';
    for($i = 1, $len = count($arruni); $i < $len; $i++) {
        if (strlen($postfix) > 0) {
            $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
        } 
        $temp = intval($arruni[$i]);
        $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
    } 
    return iconv('UCS-2', $encoding, $unistr);
}

// 后台数据报表的二级菜单
if(!function_exists('chaojiapp_report_subnav')){
	function chaojiapp_report_subnav($cur_op = ''){
		global $_G;
		echo '<div><div class="itemtitle"><h3>' . chaojiapp_lang('report') . '</h3><ul class="tab1">
				
				<li ' . ($cur_op == 'monitor-todayreport' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-todayreport"><span>' . chaojiapp_lang('monitor-todayreport') . '</span></a></li>
				<li ' . ($cur_op == 'monitor-site' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-site"><span>' . chaojiapp_lang('monitor-site') . '</span></a></li>
				<li ' . ($cur_op == 'monitor-link' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-link"><span>' . chaojiapp_lang('monitor-link') . '</span></a></li>
				<li ' . ($cur_op == 'monitor-rank' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-rank"><span>' . chaojiapp_lang('monitor-rank') . '</span></a></li>
				<li ' . ($cur_op == 'monitor-keyword' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-keyword"><span>' . chaojiapp_lang('monitor-keyword') . '</span></a></li>
				<li ' . ($cur_op == 'monitor-alexadata' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-alexadata"><span>' . chaojiapp_lang('monitor-alexadata') . '</span></a></li>
				<li ' . ($cur_op == 'monitor-snapdate' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-snapdate"><span>' . chaojiapp_lang('monitor-snapdate') . '</span></a></li>
				<!--<li ' . ($cur_op == 'monitor-ipchange' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-ipchange"><span>' . chaojiapp_lang('monitor-ipchange') . '</span></a></li>-->
				</ul></div></div>';
	}
}

// 竞争网站分析的二级菜单
if(!function_exists('chaojiapp_rival_subnav')){
	function chaojiapp_rival_subnav($cur_op = ''){
		global $_G;
		echo '<div><div class="itemtitle"><h3>' . chaojiapp_lang('rival') . '</h3><ul class="tab1">
			<li ' . ($cur_op == 'rival-summary' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-summary"><span>' . chaojiapp_lang('rival-summary') . '</span></a></li>
			<li ' . ($cur_op == 'rival-pageseo' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-pageseo"><span>' . chaojiapp_lang('rival-pageseo') . '</span></a></li>
			<li ' . ($cur_op == 'rival-snapdate' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-snapdate"><span>' . chaojiapp_lang('rival-snapdate') . '</span></a></li>
			<li ' . ($cur_op == 'rival-site' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-site"><span>' . chaojiapp_lang('rival-site') . '</span></a></li>
			<li ' . ($cur_op == 'rival-link' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-link"><span>' . chaojiapp_lang('rival-link') . '</span></a></li>
			<li ' . ($cur_op == 'rival-keyword' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-keyword"><span>' . chaojiapp_lang('rival-keyword') . '</span></a></li>
			<li ' . ($cur_op == 'rival-rank' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-rank"><span>' . chaojiapp_lang('rival-rank') . '</span></a></li>
			<li ' . ($cur_op == 'rival-alexa' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-alexa"><span>' . chaojiapp_lang('rival-alexa') . '</span></a></li>
		</ul></div></div>';
	}
}

// 特别的顶部
function chaojiapp_top_menu($cur_op = ''){
	global $_G, $plugin;
	echo '<div class="floattop" id="floattop1"><div class="itemtitle"><h3>' . $plugin['name'] . '</h3><ul class="tab1" id="submenu">
		<li ' . ($cur_op == '' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=overview"><span>' . chaojiapp_lang('overview') . '</span></a></li>
		
		<li id="nav_m2" ' . ($cur_op == 'reports' ? 'class="current"' : 'class="hasdropmenu"') . ' onmouseover="chaojiapp_dropmenu(this);"><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-todayreport"><span>' . chaojiapp_lang('report') . '<em>&nbsp;&nbsp;</em></span></a>
			<div id="nav_m2child" class="dropmenu" style="display:none;">
				<ul>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-todayreport">' . chaojiapp_lang('monitor-todayreport') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-site">' . chaojiapp_lang('monitor-site') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-link">' . chaojiapp_lang('monitor-link') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-rank">' . chaojiapp_lang('monitor-rank') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-keyword">' . chaojiapp_lang('monitor-keyword') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-alexadata">' . chaojiapp_lang('monitor-alexadata') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=monitor-snapdate">' . chaojiapp_lang('monitor-snapdate') . '</a></li>
				</ul>
			</div>
		</li>
		
		<li id="nav_group" ' . ($cur_op == 'rival' ? 'class="current"' : 'class="hasdropmenu"') . ' onmouseover="chaojiapp_dropmenu(this);"><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-summary"><span>' . chaojiapp_lang('rival') . '<em>&nbsp;&nbsp;</em></span></a>
			<div id="nav_groupchild" class="dropmenu" style="display:none;">

				<ul>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-summary">' . chaojiapp_lang('rival-summary') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-pageseo">' . chaojiapp_lang('rival-pageseo') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-snapdate">' . chaojiapp_lang('rival-snapdate') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-site">' . chaojiapp_lang('rival-site') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-link">' . chaojiapp_lang('rival-link') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-keyword">' . chaojiapp_lang('rival-keyword') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-rank">' . chaojiapp_lang('rival-rank') . '</a></li>
					<li><a href="' . CJ_PLUGIN_URL . 'pmod=rival&op=rival-alexa">' . chaojiapp_lang('rival-alexa') . '</a></li>
				</ul>				
			</div>
		</li>
		
		<li ' . ($cur_op == 'optimizelog' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=optimizelog"><span>' . chaojiapp_lang('optimizelog') . '</span></a></li>
		
		<li ' . ($cur_op == 'baidusort' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=baidusort"><span>' . chaojiapp_lang('baidusort') . '</span></a></li>
		
		<li ' . ($cur_op == 'spiderdata' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=spiderdata"><span>' . chaojiapp_lang('spiderdata') . '</span></a></li>
		
		<li ' . ($cur_op == 'changweici' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=changweici"><span>' . chaojiapp_lang('changweici') . '</span></a></li>
		<li ' . ($cur_op == 'setting' ? 'class="current"' : '') . '><a href="' . CJ_PLUGIN_URL . 'pmod=setting"><span>' . chaojiapp_lang('setting') . '</span></a></li>
		</ul></div></div>';
}


function chaojiapp_get_config($file, $ini, $type="string"){
	if(!file_exists($file)) return false;
	
	$str = file_get_contents($file);
	if ($type=="int"){
		$config = preg_match("/".preg_quote($ini)."=(.*);/", $str, $res);
		return $res[1];
	}else{
		$config = preg_match("/".preg_quote($ini)."=\"(.*)\";/", $str, $res);
		if($res[1]==null){
			$config = preg_match("/".preg_quote($ini)."='(.*)';/", $str, $res);
		}
		return $res[1];
	}
}
function chaojiapp_update_config($file, $ini, $value,$type="string"){
	if(!file_exists($file)) return false;
	$str = file_get_contents($file);
	$str2="";
	if($type=="int"){
		$str2 = preg_replace("/".preg_quote($ini)."=(.*);/", $ini."=".$value.";",$str);
	}else{
		$str2 = preg_replace("/".preg_quote($ini)."=(.*);/",$ini."=\"".$value."\";",$str);
	}
	file_put_contents($file, $str2);
}

// 输出artdialog的弹窗跳转
function artdialog_jump($title, $content, $url){
	global $_G;
	$IMGDIR = $_G['style']['imgdir'];
	$STYLEID = $_G['setting']['styleid'];
	$VERHASH = $_G['style']['verhash'];
	$frame = getgpc('frame') != 'no' ? 1 : 0;
	$charset = CHARSET;
	$basescript = ADMINSCRIPT;
	$hasexception = false;
	$msg = $content;
	include template('chaoji_com:dialog-confirm');
}

//==================================================================================

// 导出收录数据
function chaojiapp_export_site(){
	global $st,$et,$cjson;
	ob_end_clean();
	$jsondata = $cjson->decode($_SESSION['tempdata']);
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . "_" . chaojiapp_lang('monitor-site') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();

	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('monitor-site')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('site')) . " " . getcookie('chaojiapp_domain') . " " . chaojiapp_xls_charset(chaojiapp_lang('monitor-site')), 'StyleHeader');
	$sheet->cellMerge(1, 1, 5);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->writeString(2,2,chaojiapp_xls_charset(chaojiapp_lang('baidu')));
	$sheet->writeString(2,3,chaojiapp_xls_charset(chaojiapp_lang('baiduindexnum')));
	$sheet->writeString(2,4,chaojiapp_xls_charset(chaojiapp_lang('google')));
	$sheet->writeString(2,5,chaojiapp_xls_charset(chaojiapp_lang('qihu')));
	$sheet->writeString(2,6,chaojiapp_xls_charset(chaojiapp_lang('sogou')));
	
	$i = 3;
	foreach($jsondata['data']['createdates'] as $k=>$a){
		$sheet->writeString($i, 1, chaojiapp_exportdate($jsondata['data']['createdates'][$k]));
		$sheet->writeString($i, 2, chaojiapp_spec_data($jsondata['data']['datalist'][0]['datas'][$k]));
		$sheet->writeString($i, 3, chaojiapp_spec_data($jsondata['data']['datalist'][1]['datas'][$k]));
		$sheet->writeString($i, 4, chaojiapp_spec_data($jsondata['data']['datalist'][2]['datas'][$k]));
		$sheet->writeString($i, 5, chaojiapp_spec_data($jsondata['data']['datalist'][3]['datas'][$k]));
		$sheet->writeString($i, 6, chaojiapp_spec_data($jsondata['data']['datalist'][4]['datas'][$k]));
		$i++;
	}
	

	$xml->sendHeaders();
	$xml->writeData();	
	define(FOOTERDISABLED, false);
	exit;	
}

// 导出反链数据
function chaojiapp_export_link(){
	global $st,$et,$cjson;
	ob_end_clean();
	$jsondata = $cjson->decode($_SESSION['tempdata']);
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . "_" . chaojiapp_lang('monitor-link') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();

	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('monitor-link')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('site')) . " " . getcookie('chaojiapp_domain') . " " . chaojiapp_xls_charset(chaojiapp_lang('monitor-link')), 'StyleHeader');
	$sheet->cellMerge(1, 1, 4);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->writeString(2,2,chaojiapp_xls_charset(chaojiapp_lang('baidu')));
	$sheet->writeString(2,3,chaojiapp_xls_charset(chaojiapp_lang('google')));
	$sheet->writeString(2,4,chaojiapp_xls_charset(chaojiapp_lang('qihu')));
	$sheet->writeString(2,5,chaojiapp_xls_charset(chaojiapp_lang('chinaztool')));
	
	$i = 3;
	foreach($jsondata['data']['createdates'] as $k=>$a){
		$sheet->writeString($i, 1, chaojiapp_exportdate($jsondata['data']['createdates'][$k]));
		$sheet->writeString($i, 2, chaojiapp_spec_data($jsondata['data']['datalist'][0]['datas'][$k]));
		$sheet->writeString($i, 3, chaojiapp_spec_data($jsondata['data']['datalist'][1]['datas'][$k]));
		$sheet->writeString($i, 4, chaojiapp_spec_data($jsondata['data']['datalist'][2]['datas'][$k]));
		$sheet->writeString($i, 5, chaojiapp_spec_data($jsondata['data']['datalist'][3]['datas'][$k]));
		$i++;
	}
	

	$xml->sendHeaders();
	$xml->writeData();	
	define(FOOTERDISABLED, false);
	exit;	
}

// 导出PR及权重
function chaojiapp_export_rank(){
	global $st,$et,$cjson;
	ob_end_clean();
	$jsondata = $cjson->decode($_SESSION['tempdata']);
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . "_" . chaojiapp_lang('monitor-rank') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();

	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('monitor-rank')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('site')) . " " . getcookie('chaojiapp_domain') . " " . chaojiapp_xls_charset(chaojiapp_lang('monitor-rank')), 'StyleHeader');
	$sheet->cellMerge(1, 1, 5);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->writeString(2,2,chaojiapp_xls_charset(chaojiapp_lang('baidurank1')));
	$sheet->writeString(2,3,chaojiapp_xls_charset(chaojiapp_lang('baidu_citiao')));
	$sheet->writeString(2,4,chaojiapp_xls_charset(chaojiapp_lang('baidu_liuliang')));
	$sheet->writeString(2,5,chaojiapp_xls_charset(chaojiapp_lang('googlepr')));
	$sheet->writeString(2,6,chaojiapp_xls_charset(chaojiapp_lang('sogourank')));
	
	$i = 3;
	foreach($jsondata['data']['createdates'] as $k=>$a){
		$sheet->writeString($i, 1, chaojiapp_exportdate($jsondata['data']['createdates'][$k]));
		$sheet->writeString($i, 2, chaojiapp_spec_data($jsondata['data']['datalist'][0]['datas'][$k]));
		$sheet->writeString($i, 3, chaojiapp_spec_data($jsondata['data']['datalist'][1]['datas'][$k]));
		$sheet->writeString($i, 4, chaojiapp_spec_data($jsondata['data']['datalist'][2]['datas'][$k]));
		$sheet->writeString($i, 5, chaojiapp_spec_data($jsondata['data']['datalist'][3]['datas'][$k]));
		$sheet->writeString($i, 6, chaojiapp_spec_data($jsondata['data']['datalist'][4]['datas'][$k]));
		$i++;
	}
	

	$xml->sendHeaders();
	$xml->writeData();	
	define(FOOTERDISABLED, false);
	exit;	
}

// 导出关键词数据
function chaojiapp_export_keyword(){
	global $st,$et,$cjson;
	ob_end_clean();
	$jsondata = $cjson->decode($_SESSION['tempdata']);
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . "_" . chaojiapp_lang('monitor-keyword') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();
	
	// 循环关键词
	$keywordlist = $jsondata['data']['keywordlist'];
	
	foreach($keywordlist as $keyword){
	
		$sheet = $xml->addSheet($keyword['keyword']);
		$sheet->columnWidth(1, 150);
		$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('site')) . " " . getcookie('chaojiapp_domain') . " " . chaojiapp_xls_charset(chaojiapp_lang('monitor-keyword')), 'StyleHeader');
		$sheet->cellMerge(1, 1, 5);
		$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('keyword') . chaojiapp_lang('maohao')) . $keyword['keyword']);
		$sheet->cellMerge(2, 1, 5);
		$sheet->writeString(3,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
		$sheet->writeString(3,2,chaojiapp_xls_charset(chaojiapp_lang('baidu_index')));
		$sheet->writeString(3,3,chaojiapp_xls_charset(chaojiapp_lang('baidurank')));
		$sheet->writeString(3,4,chaojiapp_xls_charset(chaojiapp_lang('bidding_num')));
		$sheet->writeString(3,5,chaojiapp_xls_charset(chaojiapp_lang('natural_rank')));
		$sheet->writeString(3,6,chaojiapp_xls_charset(chaojiapp_lang('googlerank')));
		
		$i = 4;
		foreach($keyword['createdates'] as $k=>$a){
			$sheet->writeString($i, 1, chaojiapp_exportdate($keyword['createdates'][$k]));
			$sheet->writeString($i, 2, chaojiapp_spec_data($keyword['datalist'][0]['datas'][$k]));
			$sheet->writeString($i, 3, chaojiapp_spec_data($keyword['datalist'][1]['datas'][$k]));
			$sheet->writeString($i, 4, chaojiapp_spec_data($keyword['datalist'][2]['datas'][$k]));
			$sheet->writeString($i, 5, chaojiapp_spec_data($keyword['datalist'][3]['datas'][$k]));
			$sheet->writeString($i, 6, chaojiapp_spec_data($keyword['datalist'][4]['datas'][$k]));
			$i++;
		}
	
	}
	

	$xml->sendHeaders();
	$xml->writeData();	
	define(FOOTERDISABLED, false);
	exit;	
}

// 导出ALEXA
function chaojiapp_export_alexadata(){
	global $st,$et,$cjson;
	ob_end_clean();
	$jsondata = $cjson->decode($_SESSION['tempdata']);
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . "_" . chaojiapp_lang('monitor-alexadata') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();

	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('monitor-alexadata')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('site')) . " " . getcookie('chaojiapp_domain') . " " . chaojiapp_xls_charset(chaojiapp_lang('monitor-alexadata')), 'StyleHeader');
	$sheet->cellMerge(1, 1, 5);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->writeString(2,2,chaojiapp_xls_charset(chaojiapp_lang('alexa_rank')));
	$sheet->writeString(2,3,chaojiapp_xls_charset(chaojiapp_lang('traffic_rank1')));
	$sheet->writeString(2,4,chaojiapp_xls_charset(chaojiapp_lang('visit_rank1')));
	$sheet->writeString(2,5,chaojiapp_xls_charset(chaojiapp_lang('pageviews_rank1')));
	
	$i = 3;
	foreach($jsondata['data']['createdates'] as $k=>$a){
		$sheet->writeString($i, 1, chaojiapp_exportdate($jsondata['data']['createdates'][$k]));
		$sheet->writeString($i, 2, chaojiapp_spec_data($jsondata['data']['datalist'][0]['datas'][$k]));
		$sheet->writeString($i, 3, chaojiapp_spec_data($jsondata['data']['datalist'][1]['datas'][$k]));
		$sheet->writeString($i, 4, chaojiapp_spec_data($jsondata['data']['datalist'][2]['datas'][$k]));
		$sheet->writeString($i, 5, chaojiapp_spec_data($jsondata['data']['datalist'][3]['datas'][$k]));
		$i++;
	}
	

	$xml->sendHeaders();
	$xml->writeData();	
	define(FOOTERDISABLED, false);
	exit;		
}

// 导出百度快照
function chaojiapp_export_snapdate(){
	global $st,$et,$cjson;
	ob_end_clean();
	$jsondata = $cjson->decode($_SESSION['tempdata']);
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . "_" . chaojiapp_lang('monitor-snapdate') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();

	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('monitor-snapdate')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('site')) . " " . getcookie('chaojiapp_domain') . " " . chaojiapp_xls_charset(chaojiapp_lang('monitor-snapdate')), 'StyleHeader');
	$sheet->cellMerge(1, 1, 2);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->writeString(2,2,chaojiapp_xls_charset(chaojiapp_lang('baidu_kuaizhao')));
	$sheet->writeString(2,3,chaojiapp_xls_charset(chaojiapp_lang('indexposition')));
	
	$i = 3;
	foreach($jsondata['data']['createdates'] as $k=>$a){
		$sheet->writeString($i, 1, chaojiapp_exportdate($jsondata['data']['createdates'][$k]));
		$sheet->writeString($i, 2, chaojiapp_spec_data($jsondata['data']['datalist'][0]['datas'][$k]));
		$sheet->writeString($i, 3, chaojiapp_spec_data($jsondata['data']['datalist'][1]['datas'][$k]));
		$i++;
	}
	

	$xml->sendHeaders();
	$xml->writeData();	
	define(FOOTERDISABLED, false);
	exit;		
}

/**
 * ======================================================================================================================================
 * 竞争报表数据导出
 */ 
// 快照日期
function chaojiapp_export_rival_snapdate(){
	global $st, $et, $cjson;
	ob_end_clean();
	// var_dump($_SESSION['tempdata']);
	// $jsondata = $cjson->decode($_SESSION['tempdata']);
	$jsondata = $_SESSION['tempdata'];
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . chaojiapp_lang('rival_export_title') . "_" . chaojiapp_lang('rival-snapdate') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();
	
	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('rival-snapdate')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('rival_export_title') . ' ' . chaojiapp_lang('rival-snapdate')), 'StyleHeader');
	$sheet->cellMerge(1,1,$jsondata['rivalcount'] + 2);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->writeString(2,2,$jsondata['monitorsite']['host']);
	foreach($jsondata['rivalsitelist'] as $k1 => $rivalsite){
		$sheet->writeString(2, 3 + $k1, $rivalsite['host']);
	}
	$i = 3;
	foreach($jsondata['monitorsite']['datalist'] as $k2 => $data){
		$sheet->writeString($i, 1, $data['name'], 'StyleBold');
		$monitordata = $data['datas'];
		$j = $i+1;
		foreach($jsondata['createdates'] as $k3 => $date){
			$sheet->writeString($j, 1, chaojiapp_exportdate($date));
			$sheet->writeString($j, 2, chaojiapp_xls_charset(chaojiapp_formatnumber($monitordata[$k3])));
			$k = 3;
			foreach($jsondata['rivalsitelist'] as $k4 => $rivalsite4){
				$sheet->writeString($j, $k, chaojiapp_xls_charset(chaojiapp_formatnumber($rivalsite4['datalist'][$k2]['datas'][$k3])));
				$k++;
			}
			$j++;
		}
		
		$i = $j;
	}
	
	$xml->sendHeaders();
	$xml->writeData();
	define(FOOTERDISABLED, false);
	exit;
}  
// 反链数据
function chaojiapp_export_rival_site(){
	global $st, $et, $cjson;
	ob_end_clean();
	// var_dump($_SESSION['tempdata']);
	// $jsondata = $cjson->decode($_SESSION['tempdata']);
	$jsondata = $_SESSION['tempdata'];
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . chaojiapp_lang('rival_export_title') . "_" . chaojiapp_lang('rival-site') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();
	
	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('rival-site')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('rival_export_title') . ' ' . chaojiapp_lang('rival-site')), 'StyleHeader');
	$sheet->cellMerge(1,1,$jsondata['rivalcount'] + 2);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->writeString(2,2,$jsondata['monitorsite']['host']);
	foreach($jsondata['rivalsitelist'] as $k1 => $rivalsite){
		$sheet->writeString(2, 3 + $k1, $rivalsite['host']);
	}
	$i = 3;
	foreach($jsondata['monitorsite']['datalist'] as $k2 => $data){
		$sheet->writeString($i, 1, $data['name'], 'StyleBold');
		$monitordata = $data['datas'];
		$j = $i+1;
		foreach($jsondata['createdates'] as $k3 => $date){
			$sheet->writeString($j, 1, chaojiapp_exportdate($date));
			$sheet->writeString($j, 2, chaojiapp_xls_charset(chaojiapp_formatnumber($monitordata[$k3])));
			$k = 3;
			foreach($jsondata['rivalsitelist'] as $k4 => $rivalsite4){
				$sheet->writeString($j, $k, chaojiapp_xls_charset(chaojiapp_formatnumber($rivalsite4['datalist'][$k2]['datas'][$k3])));
				$k++;
			}
			$j++;
		}
		
		$i = $j;
	}
	
	$xml->sendHeaders();
	$xml->writeData();
	define(FOOTERDISABLED, false);
	exit;
} 
 
// 反链数据
function chaojiapp_export_rival_link(){
	global $st, $et, $cjson;
	ob_end_clean();
	// var_dump($_SESSION['tempdata']);
	// $jsondata = $cjson->decode($_SESSION['tempdata']);
	$jsondata = $_SESSION['tempdata'];
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . chaojiapp_lang('rival_export_title') . "_" . chaojiapp_lang('rival-link') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();
	
	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('rival-link')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('rival_export_title') . ' ' . chaojiapp_lang('rival-link')), 'StyleHeader');
	$sheet->cellMerge(1,1,$jsondata['rivalcount'] + 2);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->writeString(2,2,$jsondata['monitorsite']['host']);
	foreach($jsondata['rivalsitelist'] as $k1 => $rivalsite){
		$sheet->writeString(2, 3 + $k1, $rivalsite['host']);
	}
	$i = 3;
	foreach($jsondata['monitorsite']['datalist'] as $k2 => $data){
		$sheet->writeString($i, 1, $data['name'], 'StyleBold');
		$monitordata = $data['datas'];
		$j = $i+1;
		foreach($jsondata['createdates'] as $k3 => $date){
			$sheet->writeString($j, 1, chaojiapp_exportdate($date));
			$sheet->writeString($j, 2, chaojiapp_xls_charset(chaojiapp_formatnumber($monitordata[$k3])));
			$k = 3;
			foreach($jsondata['rivalsitelist'] as $k4 => $rivalsite4){
				$sheet->writeString($j, $k, chaojiapp_xls_charset(chaojiapp_formatnumber($rivalsite4['datalist'][$k2]['datas'][$k3])));
				$k++;
			}
			$j++;
		}
		
		$i = $j;
	}
	
	$xml->sendHeaders();
	$xml->writeData();
	define(FOOTERDISABLED, false);
	exit;
}  

// 关键词
function chaojiapp_export_rival_keyword(){
	global $st, $et, $cjson;
	ob_end_clean();
	// var_dump($_SESSION['tempdata']);
	// $jsondata = $cjson->decode($_SESSION['tempdata']);
	$jsondatas = $_SESSION['tempdata'];
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . chaojiapp_lang('rival_export_title') . "_" . chaojiapp_lang('rival-keyword') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();
	
	foreach($jsondatas as $k => $jsondata){
		$sheet = $xml->addSheet($jsondata['datatypes'][$k]);
		$sheet->columnWidth(1, 150);
		$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('rival_export_title') . ' ' . chaojiapp_lang('rival-keyword')), 'StyleHeader');
		$sheet->cellMerge(1,1,$jsondata['rivalcount'] + 2);
		$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
		$sheet->writeString(2,2,$jsondata['monitorsite']['websitename']);
		foreach($jsondata['rivalsitelist'] as $k1 => $rivalsite){
			$sheet->writeString(2, 3 + $k1, $rivalsite['websitename']);
		}
		$i = 3;
		foreach($jsondata['monitorsite']['keywordlist'] as $k2 => $keyword){
			$sheet->writeString($i, 1, chaojiapp_xls_charset(chaojiapp_lang('keyword') . chaojiapp_lang('maohao')) . $keyword['keyword'], 'StyleBold');
			$monitordata = $keyword['datalist']['datas'];
			$j = $i+1;
			foreach($jsondata['createdates'] as $k3 => $date){
				$sheet->writeString($j, 1, chaojiapp_exportdate($date));
				$sheet->writeString($j, 2, chaojiapp_xls_charset(chaojiapp_formatnumber($monitordata[$k3])));
				$k = 3;
				foreach($jsondata['rivalsitelist'] as $k4 => $rivalsite4){
					$sheet->writeString($j, $k, chaojiapp_xls_charset(chaojiapp_formatnumber($rivalsite4['keywordlist'][$k2]['datalist']['datas'][$k3])));
					$k++;
				}
				$j++;
			}
			$i = $j;
		}
	}
	
	$xml->sendHeaders();
	$xml->writeData();
	define(FOOTERDISABLED, false);
	exit;
} 

// PR及排名
function chaojiapp_export_rival_rank(){
	global $st, $et, $cjson;
	ob_end_clean();
	// var_dump($_SESSION['tempdata']);
	// $jsondata = $cjson->decode($_SESSION['tempdata']);
	$jsondata = $_SESSION['tempdata'];
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . chaojiapp_lang('rival_export_title') . "_" . chaojiapp_lang('rival-rank') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();
	
	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('rival-rank')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('rival_export_title') . ' ' . chaojiapp_lang('rival-rank')), 'StyleHeader');
	$sheet->cellMerge(1,1,$jsondata['rivalcount'] + 2);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->writeString(2,2,$jsondata['monitorsite']['host']);
	foreach($jsondata['rivalsitelist'] as $k1 => $rivalsite){
		$sheet->writeString(2, 3 + $k1, $rivalsite['host']);
	}
	$i = 3;
	foreach($jsondata['monitorsite']['datalist'] as $k2 => $data){
		$sheet->writeString($i, 1, $data['name'], 'StyleBold');
		$monitordata = $data['datas'];
		$j = $i+1;
		foreach($jsondata['createdates'] as $k3 => $date){
			$sheet->writeString($j, 1, chaojiapp_exportdate($date));
			$sheet->writeString($j, 2, chaojiapp_xls_charset(chaojiapp_formatnumber($monitordata[$k3])));
			$k = 3;
			foreach($jsondata['rivalsitelist'] as $k4 => $rivalsite4){
				$sheet->writeString($j, $k, chaojiapp_xls_charset(chaojiapp_formatnumber($rivalsite4['datalist'][$k2]['datas'][$k3])));
				$k++;
			}
			$j++;
		}
		
		$i = $j;
	}
	
	$xml->sendHeaders();
	$xml->writeData();
	define(FOOTERDISABLED, false);
	exit;
} 
 
// alexa数据 
function chaojiapp_export_rival_alexa(){
	global $st, $et, $cjson;
	ob_end_clean();
	// var_dump($_SESSION['tempdata']);
	// $jsondata = $cjson->decode($_SESSION['tempdata']);
	$jsondata = $_SESSION['tempdata'];
	include(dirname(__FILE__) . '/ExcelWriterXML/ExcelWriterXML.php');
	$xml = new ExcelWriterXML(getcookie('chaojiapp_sitename') . chaojiapp_lang('rival_export_title') . "_" . chaojiapp_lang('rival-alexa') . "_" . $st . "_" . $et . ".xls");
	$xml->docAuthor('chaoji.com');
	
	$format = $xml->addStyle('StyleHeader');
	$format->alignHorizontal('Center');
	
	$format = $xml->addStyle('StyleBold');
	$format->fontBold();
	
	$sheet = $xml->addSheet(chaojiapp_xls_charset(chaojiapp_lang('rival-alexa')));
	
	$sheet->writeString(1,1,chaojiapp_xls_charset(chaojiapp_lang('rival_export_title') . ' ' . chaojiapp_lang('rival-alexa')), 'StyleHeader');
	$sheet->cellMerge(1,1,$jsondata['rivalcount'] + 2);
	$sheet->writeString(2,1,chaojiapp_xls_charset(chaojiapp_lang('report8_1')));
	$sheet->columnWidth(1, 150);
	$sheet->writeString(2,2,$jsondata['monitorsite']['host']);
	foreach($jsondata['rivalsitelist'] as $k1 => $rivalsite){
		$sheet->writeString(2, 3 + $k1, $rivalsite['host']);
	}
	$i = 3;
	foreach($jsondata['monitorsite']['datalist'] as $k2 => $data){
		$sheet->writeString($i, 1, $data['name'], 'StyleBold');
		$monitordata = $data['datas'];
		$j = $i+1;
		foreach($jsondata['createdates'] as $k3 => $date){
			$sheet->writeString($j, 1, chaojiapp_exportdate($date));
			$sheet->writeString($j, 2, chaojiapp_xls_charset($monitordata[$k3]));
			$k = 3;
			foreach($jsondata['rivalsitelist'] as $k4 => $rivalsite4){
				$sheet->writeString($j, $k, chaojiapp_xls_charset($rivalsite4['datalist'][$k2]['datas'][$k3]));
				$k++;
			}
			$j++;
		}
		
		$i = $j;
	}
	
	$xml->sendHeaders();
	$xml->writeData();
	define(FOOTERDISABLED, false);
	exit;
}

// 客户端信息
function  chaojiapp_clientinfo($jsondata){
	// 客户端信息
	if($jsondata['clientinfo']){
		$clientinfo = $jsondata['clientinfo'];
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
	echo '<script>var clientinfo_status=' . $clientinfo_status . ';var clientinfo_str=\'' . $clientinfo_str . '\';</script>';
} 
?>