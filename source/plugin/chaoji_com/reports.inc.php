<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
session_start();
require_once(dirname(__FILE__) . '/include/function.php');
require_once dirname(__FILE__) . '/include/check.php';

	echo '<script src="source/plugin/chaoji_com/resource/js/My97DatePicker/WdatePicker.js" type="text/javascript"></script><script type="text/javascript" language="javascript" src="source/plugin/chaoji_com/resource/js/jquery.min.js"></script>
<script type="text/javascript">var jq = jQuery.noConflict();var cookiepre=\'' . $_G['config']['cookie']['cookiepre'] . '\';var disallowfloat=false;var clientinfo_status=0;var clientinfo_str=\'\';var PLUGIN_URL = \'' . CJ_PLUGIN_URL . '\'; var FORMHASH = \'' . FORMHASH . '\';</script><script src="source/plugin/chaoji_com/resource/js/artDialog/artDialog.js?skin=default"></script>
	<script src="source/plugin/chaoji_com/resource/js/artDialog/plugins/iframeTools.js"></script>
<script type="text/javascript" src="source/plugin/chaoji_com/resource/js/highcharts.js" language="javascript"></script>
<script type="text/javascript" src="source/plugin/chaoji_com/resource/js/tableorder.js" language="javascript"></script>
<script src="source/plugin/chaoji_com/resource/js/template-native.js" type="text/javascript"></script><script src="source/plugin/chaoji_com/resource/js/cj-rivalcompare.js" type="text/javascript"></script>
<link href="source/plugin/chaoji_com/resource/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="source/plugin/chaoji_com/resource/js/script.js"></script>';

$op = isset($_GET['op']) ? trim($_GET['op']) : 'monitor-todayreport';
$op1 = isset($_GET['op1']) ? trim($_GET['op1']) : '';
$formhash = isset($_GET['formhash']) ? trim($_GET['formhash']) : '';

// echo chaojiapp_report_subnav($op);
chaojiapp_top_menu('reports');
showtableheader();
showtitle(chaojiapp_lang($op));
showtablefooter();

if(!in_array($op, array('', 'monitor-ipchange', 'monitor-todayreport'))){
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

$searchform = '
	
	<a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(dgmdate(TIMESTAMP - 86400 * 6, 'Y-m-d')) . '&et=' . urlencode(dgmdate(TIMESTAMP, 'Y-m-d')) . '&t=3" ' . ($t=='3' ? 'class="cur_date"' : '') . '>' . chaojiapp_lang('last7days') . '</a> | 
	
	<a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(dgmdate(TIMESTAMP - 86400 * 29, 'Y-m-d')) . '&et=' . urlencode(dgmdate(TIMESTAMP, 'Y-m-d')) . '&t=4" ' . ($t=='4' ? 'class="cur_date"' : '') . '>' . chaojiapp_lang('last30days') . '</a> |
	
	<a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(date('Y-m-01')) . '&et=' . urlencode(date('Y-m-' . cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')) . '')) . '&t=5" ' . ($t=='5' ? 'class="cur_date"' : '') . '>' . chaojiapp_lang('this_month') . '</a> ';
	
	switch($t){
		case '3':
			$searchform .= '<input type="button" value="' . chaojiapp_lang('7daysbefore') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) - 86400 * 7, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) - 86400 * 7, 'Y-m-d')) . '&t=' . $t . '\';" /> 
	
	<input type="button" value="' . chaojiapp_lang('7daysafter') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) + 86400 * 7, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) + 86400 * 7, 'Y-m-d')) . '&t=' . $t . '\';" ' . (((dmktime($st) + 86400 *7) >= dmktime(date('Y-m-d'))) ? 'disabled="disabled"' : '') . ' /> ';
			break;
		case '4':
			// 前30天
			
			$searchform .= '<input type="button" value="' . chaojiapp_lang('30daysbefore') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) - 86400 * 30, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) - 86400 * 30, 'Y-m-d')) . '&t=' . $t . '\';" /> 
	
	<input type="button" value="' . chaojiapp_lang('30daysafter') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) + 86400 * 30, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) + 86400 * 30, 'Y-m-d')) . '&t=' . $t . '\';" ' . (((dmktime($st) + 86400 *30) >= dmktime(date('Y-m-d'))) ? 'disabled="disabled"' : '') . ' /> ';
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
			
			$searchform .= '<input type="button" value="' . chaojiapp_lang('last_month') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(date($last_month_y . '-' . $last_month_m . '-01')) . '&et=' . urlencode(date($last_month_y . '-' . $last_month_m . '-' . cal_days_in_month(CAL_GREGORIAN, $last_month_m, $last_month_y) . '')) . '&t=' . $t . '\';" /> 
	
	<input type="button" value="' . chaojiapp_lang('next_month') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(date($next_month_y . '-' . $next_month_m . '-01')) . '&et=' . urlencode(date($next_month_y . '-' . $next_month_m . '-' . cal_days_in_month(CAL_GREGORIAN, $next_month_m, $next_month_y) . '')) . '&t=' . $t . '\';" ' . (($next_month_m > date('m')) ? 'disabled="disabled"' : '') . ' /> ';
			break;
		case '6':
			break;
		case '1':
		case '2':
		default:
			$searchform .= '<input type="button" value="' . chaojiapp_lang('one_day') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) - 86400 * 1, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) - 86400 * 1, 'Y-m-d')) . '&t=' . $t . '\';" /> 
	
	<input type="button" value="' . chaojiapp_lang('next_day') . '" class="btn" onclick="location.href=\'' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&st=' . urlencode(dgmdate(dmktime($st) + 86400 * 1, 'Y-m-d')) . '&et=' . urlencode(dgmdate(dmktime($et) + 86400 * 1, 'Y-m-d')) . '&t=' . $t . '\';" /> ';
			break;
	}
	
	if(isset($_SESSION['isviewdata']) && $_SESSION['isviewdata']){
	
		$searchform .= '<input type="text" class="txt" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\',minDate:\'2009-11-23\',maxDate:\'#F{$dp.$D(\\\'et\\\')}\'});" name="st" id="st" value="' . $st . '" title="' . chaojiapp_lang('st_title') . '" readonly="readonly" />' . chaojiapp_lang('date_to') . ' <input type="text" class="txt" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\',minDate:\'#F{$dp.$D(\\\'st\\\')}\',maxDate:\'' . $maxdate . '\'});" name="et" id="et" value="' . $et . '" title="' . chaojiapp_lang('et_title') . '" readonly="readonly" />';
		showformheader(CJ_FORM_URL . 'pmod=reports&op=' . $op, 'formsubmit');
		showtableheader();
		chaojiapp_showsubmit('formsubmit', chaojiapp_lang('search'), $searchform, '<div style="position:absolute;top:0px;right:0px;" id="exportlink"><a href="' . CJ_PLUGIN_URL . 'pmod=reports&op=' . $op . '&op1=export&formhash=' . FORMHASH . '"><img src="source/plugin/chaoji_com/resource/images/export.png" style="vertical-align: top;" /> ' . chaojiapp_lang('export_data') . '</a></div>');
		showtablefooter();
		showformfooter();
	}
}
$cjson = new CJSON();
if($op == 'monitor-todayreport'){
	$st = date('Y-m-d');
	$et = date('Y-m-d');
	$result20 = chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => '20', 'beginDate' => $st, 'endDate' => $et, 'accessToken' => getcookie('chaojiapp_access_token')));
	$result201 = $result20['Seo_GetWebsiteDataReportResult'];

	$jsondata = $cjson->decode($result201);
	chaojiapp_api_error($jsondata['code'], 'rival&op=monitor-todayreport&st=' . $st . '&et=' . $et);
	$jsondata = $jsondata['data'];
	chaojiapp_clientinfo($jsondata);
	if($jsondata['isviewdata']){
		$date1 = '<input type="text" class="txt" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\',minDate:\'2009-11-23\',maxDate:\'#F{$dp.$D(\\\'et\\\')}\'});" name="st" id="st" value="' . $st . '" title="' . chaojiapp_lang('st_title') . '" readonly="readonly" />' . chaojiapp_lang('date_to') . ' <input type="text" class="txt" onclick="WdatePicker({dateFmt:\'yyyy-MM-dd\',minDate:\'#F{$dp.$D(\\\'st\\\')}\',maxDate:\'' . $maxdate . '\'});" name="et" id="et" value="' . $et . '" title="' . chaojiapp_lang('et_title') . '" readonly="readonly" />';
		
		include template('chaoji_com:monitor-todayreport');
	}else{
		if($jsondata['typeid'] == '1'){
			if($jsondata['downloadurl'] == ''){
				$textarr = explode(chr(29), $jsondata['showtext']);
			}
		}
		include template('chaoji_com:cannotviewdata');		
	}
}elseif($op == 'monitor-site'){
	if(isset($_SESSION['isviewdata']) && $_SESSION['isviewdata']){
		if($op1 == 'export'){
			if($formhash == formhash() && isset($_SESSION['tempdata'])){
				chaojiapp_export_site();			
			}else{
				cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=reports&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');	
			}
		}else{
			include template('chaoji_com:monitor-site');
		}	
	}else{
		$jsondata = $_SESSION['isviewdata_data'];
		if($jsondata['typeid'] == '1'){
			if($jsondata['downloadurl'] == ''){
				$textarr = explode(chr(29), $jsondata['showtext']);
			}
		}
		include template('chaoji_com:cannotviewdata');			
	}
}elseif($op == 'monitor-link'){
	if(isset($_SESSION['isviewdata']) && $_SESSION['isviewdata']){
		if($op1 == 'export'){
			if($formhash == formhash() && isset($_SESSION['tempdata'])){
				chaojiapp_export_link();			
			}else{
				cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=reports&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');	
			}
		}else{
			include template('chaoji_com:monitor-link');
		}
	}else{
		$jsondata = $_SESSION['isviewdata_data'];
		if($jsondata['typeid'] == '1'){
			if($jsondata['downloadurl'] == ''){
				$textarr = explode(chr(29), $jsondata['showtext']);
			}
		}
		include template('chaoji_com:cannotviewdata');			
	}	
}elseif($op == 'monitor-rank'){
	if(isset($_SESSION['isviewdata']) && $_SESSION['isviewdata']){
		if($op1 == 'export'){
			if($formhash == formhash() && isset($_SESSION['tempdata'])){
				chaojiapp_export_rank();
			}else{
				cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=reports&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
			}
		}else{
			include template('chaoji_com:monitor-rank');
		}
	}else{
		$jsondata = $_SESSION['isviewdata_data'];
		if($jsondata['typeid'] == '1'){
			if($jsondata['downloadurl'] == ''){
				$textarr = explode(chr(29), $jsondata['showtext']);
			}
		}
		include template('chaoji_com:cannotviewdata');			
	}
}elseif($op == 'monitor-keyword'){
	if(isset($_SESSION['isviewdata']) && $_SESSION['isviewdata']){
		if($op1 == 'export'){
			if($formhash == formhash() && isset($_SESSION['tempdata'])){
				chaojiapp_export_keyword();
			}else{
				cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=reports&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
			}
		}else{
			include template('chaoji_com:monitor-keyword');
		}
	}else{
		$jsondata = $_SESSION['isviewdata_data'];
		if($jsondata['typeid'] == '1'){
			if($jsondata['downloadurl'] == ''){
				$textarr = explode(chr(29), $jsondata['showtext']);
			}
		}
		include template('chaoji_com:cannotviewdata');		
	}
}elseif($op == 'monitor-alexadata'){
	if(isset($_SESSION['isviewdata']) && $_SESSION['isviewdata']){
		if($op1 == 'export'){
			if($formhash == formhash() && isset($_SESSION['tempdata'])){
				chaojiapp_export_alexadata();
			}else{
				cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=reports&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
			}
		}else{
			include template('chaoji_com:monitor-alexadata');
		}
	}else{
		$jsondata = $_SESSION['isviewdata_data'];
		if($jsondata['typeid'] == '1'){
			if($jsondata['downloadurl'] == ''){
				$textarr = explode(chr(29), $jsondata['showtext']);
			}
		}
		include template('chaoji_com:cannotviewdata');		
	}
}elseif($op == 'monitor-snapdate'){
	if(isset($_SESSION['isviewdata']) && $_SESSION['isviewdata']){
		if($op1 == 'export'){
			if($formhash == formhash() && isset($_SESSION['tempdata'])){
				chaojiapp_export_snapdate();
			}else{
				cpmsg(chaojiapp_lang('export_error'), CJ_PLUGIN_URL . "pmod=reports&op=" . $op . "&st=" . $st . '&et=' . $et, 'error');
			}
		}else{
			include template('chaoji_com:monitor-snapdate');
		}
	}else{
		$jsondata = $_SESSION['isviewdata_data'];
		if($jsondata['typeid'] == '1'){
			if($jsondata['downloadurl'] == ''){
				$textarr = explode(chr(29), $jsondata['showtext']);
			}
		}
		include template('chaoji_com:cannotviewdata');		
	}
}elseif($op == 'monitor-ipchange'){
	if($op1 == 'export'){
		
	}else{
		include template('chaoji_com:monitor-ipchange');
	}
}else{
	include template('chaoji_com:monitor-site');
}
// echo $op;
?>