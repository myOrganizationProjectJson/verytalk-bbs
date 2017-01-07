<?php
function show_pick_window($title,$html,$args = ''){
	global $_G;
	$charset = GBK ? 'gb2312' : 'UTF-8';
	$big5 = $_G['config']['output']['language'] == 'zh_tw' && $_G['config']['output']['charset'] == 'big5' ? TRUE : FALSE;
	if($big5) $charset = 'big5';
	if(!$args['no_show']) header('Content-Type: text/xml ');
	global $_G;
	ob_clean();
	ob_end_flush();
	$show_footer = $args['f'] && !$args['js_func'] ? false : true;
	$args['js_func'] = $args['js_func'] ? $args['js_func'] : 'hideWindow(\''.$_GET['handlekey'].'\')';
	if(!$args['w']) $args['w'] = 'auto';
	if(!$args['h']) $args['h'] = 'auto';
	$args['y'] = $args['y'] ? 'hidden' : 'scroll';
	if(!$args['no_show']){
		$show_html = '<?xml version="1.0" encoding="'.$charset.'"?>';
		$show_html .= "<root>";
		$show_html .= '<![CDATA[';
	}
	$show_html .= '<h3 class="flb">
	<em>'.$title.'</em>
	<span><a href="javascript:;" onclick="hideWindow(\''.$_GET['handlekey'].'\');" class="flbc" title="'.milu_lang('close').'">'.milu_lang('close').'</a></span>
	</h3>
	<div class="article_detail c" id="return_'.$_GET['handlekey'].'">
	<div class="c bart">
	<div style="width:'.$args['w'].'px; height:'.$args['h'].'px;overflow-y:'.$args['y'].';">'.$html.'</div>
	</div>';
	if($show_footer){
	 	$show_html .=  '<p class="o pns">
		<button type="submit" name="dsf" style="width:50px;  height:25px;" class="pn pnc" onclick="'.$args['js_func'].';"><span>'.milu_lang('ok').'</span></button>
		<button type="reset" style="width:50px; height:25px;" name="dsf" class="pn" onclick="hideWindow(\''.$_GET['handlekey'].'\');"><em>'.milu_lang('cancel').'</em></button>
		</p>';
	}
	$show_html .= "</div>";
	if(!$args['no_show']){
		$show_html .= "]]></root>";
	}
	if($args['no_show'] == 1){
		return $show_html;
	}else{
		echo $show_html;
	}	
	define(FOOTERDISABLED, false);
	exit();
}

function pis_image_ext($ext) {
	$imgext  = array('jpg', 'jpeg', 'gif', 'png', 'bmp');
	return in_array($ext, $imgext) ? 1 : 0;
}

function percent_format($p, $t, $c = 0){
	return sprintf('%.'.$c.'f%%',$p/$t*100);
}

function forum_threadtype_list($flag = 0){
	$query = DB::query("SELECT typeid,name FROM ".DB::table('forum_threadtype')." ORDER BY displayorder");
	$data = array();
	while($type = DB::fetch($query)) {
		$data[$type['typeid']] = $type['name'];
	}
	if($flag == 1) $data[0] = milu_lang('please_select');
	ksort($data);
	return $data;
}

function forum_threadtype_html(){
	$rule_type = trim($_GET['rule_type']);
	$data_id = intval($_GET['rule_id']);
	if($rule_type == 'picker'){
		pload('F:pick');
		$info = get_pick_info($data_id, 'forum_threadtype_id,forum_threadtypes');
	}else if($type == 'system'){
		pload('F:rules');
		$info = get_rules_info($data_id, 'forum_threadtype_id,forum_threadtypes');
	}else{
		pload('F:fastpick');
		$info = fastpick_info($data_id, 'forum_threadtype_id,forum_threadtypes');
	}
	$info['forum_threadtypes'] = dunserialize($info['forum_threadtypes']);
	$info[$_GET['pre'].'_threadtype_id'] = intval($_GET['typeid']);
	return pickOutput::show_threadtypes_html($_GET['pre'], $info);
}

function milu_pick_tpl($args = array()){
	  global $_S;
	  extract((array)$args);
	  sload('C:seoOutput');
	  $head_url = '?'.PLUGIN_GO.$_GET['pmod'].'&myac=';
	  $myac = $_GET['myac'];
	  $tpl = $_GET['tpl'];
	  if(empty($myac)) $myac = $default_ac ? $default_ac : $_GET['pmod'].'_run';
	  if(function_exists($myac)) $info = $myac();
	  $_GET['mytemp'] = $_GET['mytemp'] ? $_GET['mytemp'] : $info['tpl'];
	  $mytemp = $_GET['mytemp'] ? $_GET['mytemp'] : $myac;
	  $tpl = $info['tpl'] ? $info['tpl'] : $tpl;
	  if(!$_GET['inajax']){
		  $_S['set'] = st_get_pluin_set();
		  $submit_pmod = $info['submit_pmod'] ? $info['submit_pmod'] : $_GET['pmod'];
		  $submit_action = $info['submit_action'] ? $info['submit_action'] : $myac;
		  $info['header'] = seoOutput::pick_header_output();
		  if(!$tpl || $tpl!= 'no') include template('milu_seotool:'.$mytemp);
	  }
  }

if(!function_exists('portalcp_get_summary')){
	function portalcp_get_summary($message) {
		$message = preg_replace(array("/\[attach\].*?\[\/attach\]/", "/\&[a-z]+\;/i", "/\<script.*?\<\/script\>/"), '', $message);
		$message = preg_replace("/\[.*?\]/", '', $message);
		require_once libfile('function/home');
		$message = getstr(strip_tags($message), 200);
		return $message;
	}
}

function pload_upload_class(){
	if(file_exists(libfile('class/upload'))){
		require_once libfile('class/upload');
	}else{
		require_once libfile('discuz/upload', 'class');
	}
}

function get_fileext_from_url($url){
	$url_info = parse_url($url);
	$query_url = $url_info['query'] ? $url_info['query'] : $url_info['path'];
	$file_ext = addslashes(strtolower(substr(strrchr($query_url, '.'), 1, 10)));
	return $file_ext;
}

function get_filename_from_url($url){
	$patharr = explode('/', $url);
	return  trim($patharr[count($patharr)-1]);
}

function pserialize($arr){
	if(DISCUZ_VERSION != 'X2') return serialize($arr);
	return serialize(dstripslashes($arr));//X2
}

function create_hash(){
	return md5(time().rand().uniqid());
}

//base64编码，然后转给js
function js_base64_encode($arr){
	foreach((array)$arr as $k => $v){
		if(GBK) $v = piconv($v, 'GB2312', 'UTF-8');
		$re[$k] = base64_encode($v);
	}
	return $re;
}
function clear_html_script($str, $filter_arr){
	if(!$filter_arr) return FALSE;
	global $_G;
	$filter_html = $_G['cache']['evn_milu_pick']['filter_html'];
	$max = count($filter_html);
	foreach((array)$filter_arr as $k => $v){
		if($v < $max ) $new_arr[] =  $filter_html[$v]['search'];
	}
	$rules = implode('|', $new_arr);
	$rules = convertrule($rules);
	$rules = str_replace('\|', '|', $rules);
	$str = preg_replace("/<(\/?(".$rules.").*?)>/si", "", $str);
	if(in_array(12, $filter_arr)){//如果勾选空格过滤
		//去除连续的空格、制表符、换页符等 保留一个空格
		$str = preg_replace('#([\s]{2,})#',' ',$str);
	}
	return $str;
}




function milu_lang($name, $val_arr = array()){
	return lang('plugin/milu_pick', $name, $val_arr);
}

function pset_charset($charset_type = 0){
	global $_G;
	$charset_type = isset($_GET['charset_type']) ? intval($_GET['charset_type']) : $charset_type;
	$charet_type_arr = array(2 => 'GBK', 3 => 'UTF-8', 4 => 'BIG5');
	$charset = $charet_type_arr[$charset_type];
	$_G['cache']['evn_milu_pick']['charset'] = $charset;
	return $charset;
}

function str_iconv($str){
	global $_G;
	$is_big = $_G['cache']['evn_milu_pick']['pick_config']['is_big'];//是否utf-8环境下将繁体转换为简体
	if(!$str) return false;
	$charset = !empty($_G['cache']['evn_milu_pick']['charset']) ? $_G['cache']['evn_milu_pick']['charset'] : strtoupper(get_charset($str));
	$big5 = $_G['config']['output']['language'] == 'zh_tw' && $_G['config']['output']['charset'] == 'big5' ? TRUE : FALSE;
	if(GBK){
		if($charset == 'UTF-8'){
			if($is_big){
				return big5_gbk($str);
			}
			$str = piconv($str, 'UTF-8', 'GBK');
			return $str;
		}else if($charset == 'BIG5'){//繁体
			return big52gb($str);
		}
	}else{
		if($charset != 'UTF-8'){
			if($charset == 'BIG5')  {
				if($_G['config']['output']['language'] != 'zh_tw'){//简体
					$str = big52gb($str);
					return piconv($str, 'GBK', 'UTF-8');
				}
				if($big5) return $str;
			}
			//某些网页这样要比用gb2big5函数转换要好得多，但不知道是不是通用的，待观察
			if($big5) return piconv($str, 'GBK', 'BIG5');
			//if($big5) return gb2big5($str);
			$str = piconv($str, $charset, 'UTF-8');
			
			//火星文的转换 火星文的话，需要先转换成big5，再转换,不然繁体字会乱码
			/*
			$str = piconv($str, 'GBK', 'BIG5');
			$str = piconv($str, 'BIG5', 'UTF-8');
			*/
			
			return $str;		
		}else{
			if($big5){
				$str = piconv($str, 'UTF-8', 'GBK');
				$str = gb2big5($str);
			}
		}
	}
	return $str;	
}


//utf-8环境下 繁体装成简体 只用于gbk程序
function big5_gbk($str){
	global $_G;
	$is_big = $_G['cache']['evn_milu_pick']['pick_config']['is_big'];//是否utf-8环境下将繁体转换为简体
	if(!$is_big) return $str;
	$str = piconv($str, 'UTF-8', 'BIG5');
	$str = big52gb($str);
	return $str;
}


function piconv($str, $in, $out){
	global $_G;
	$is_win = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? TRUE : FALSE;
	if($is_win || $_G['cache']['evn_milu_pick']['pick_config']['is_big']) return diconv($str, $in, $out);
	if(function_exists('mb_convert_encoding')) {
		$str = $in == 'UTF-8' ? str_replace("\xC2\xA0", ' ', $str) : $str;
		$str = mb_convert_encoding($str, $out, $in); 
	}else{	
		$str = diconv($str, $in, $out);
	}
	return $str;
}


//http://www.shipingjie.net/world/index.html这个地址不准确
function get_charset($web_str){
	preg_match("/<meta[^>]+charset=\"?'?([^'\"\>]+)\"?[^>]+\>/is", $web_str, $arr);
	//if($arr[1]) return $arr[1];
	$arr[1] = strtoupper($arr[1]);
	if($arr[1] == 'GBK' || $arr[1] == 'BIG5') return $arr[1];
	$charset = is_utf8($web_str) ? 'UTF-8' : 'GB2312'; 
	if($arr[1] && $arr[1] == $charset) return $arr[1];
	return $charset;
}

function get_base_url($message){
	preg_match("/<base[^>]+href=\"?'?([^'\"\>]+)\"?[^>]+\>/is", $message, $arr);
	if($arr[1]) return $arr[1];
}

function is_utf8($string) { 
	if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$string) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$string) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$string) == true) { 
		return true; 
	}else{ 
		return false; 
	} 
}

//由此函数调ajax函数
function ajax_func(){
	global $_G;
	pset_charset();
	$ajax_func = $_GET['af'];
	$allow_func_arr = array('member_public_info', 'fast_pick', 'fastpick', 'picker_list_select', 'skydrive_output', 'skydrive_test', 'rules_get_test', 'system_get_link_test', 'pick_match_rules', 'forum_threadtype_html', 'get_user_info_test', 'url_page_range_test', 'system_get_link_test', 'url_page_range_test', 'get_rss_url', 'getthreadtypes', 'pick_check_uid_exists', 'get_person_blog_class', 'attach_download_url_test', 'system_get_link_test', 'login_test', 'get_member_info', 'share_picker_data', 'download_picker_data', 'get_other_test', 'rules_get_threadtypes', 'tips_no', 'fastpick_evo_test', 'cron_today_clear', 'pick_log_list', 'del_pick_one_log', 'show_pick_class', 'share_fast_pick_data', 'share_system_rules_data', 'download_fast_pick_data', 'download_system_rules_data', 'clear_data_run', 'ajax_downremoteimg', 'import_threadtype_data', 'member_import_online');
	if(strexists($ajax_func, ':')){
		$temp_arr = explode(':', $ajax_func);
		$file_name = $temp_arr[0];
		$ajax_func = $temp_arr[1];
		pload('F:'.$file_name);
		if(!in_array($ajax_func, $allow_func_arr)) exit('Access Denied:0032');
		if(!function_exists($ajax_func)){
			pload('C:'.$file_name);
			if(!function_exists($ajax_func)){
				exit(milu_lang('no_found_ajaxfunc'));
			}
		}
	}
	$inajax = $_GET['inajax'];
	$xml = empty($_GET['xml']) ? 0 : $_GET['xml'];
	if(!in_array($ajax_func, $allow_func_arr)) exit('Access Denied:0032');
	if(!function_exists($ajax_func)) exit(milu_lang('no_found_ajaxfunc'));
	$output = $ajax_func();
	ob_clean();
	ob_end_flush();
	if($xml == 1) include template('common/header_ajax');
	echo $output;
	if($xml == 1) include template('common/footer_ajax');
	define(FOOTERDISABLED, false);
	exit();
}

//获取插件的全局设置
function get_pick_set(){
	global $_G;
	if($_G['cache']['plugin']['milu_pick']) return $_G['cache']['plugin']['milu_pick'];
	loadcache('plugin');
	return $_G['cache']['plugin']['milu_pick'];
}

function _striptext($document) {
	if (!$document) return $document;
	$search = array("'<script[^>]*?>.*?</script>'si",	// strip out javascript
					"'<style[^>]*?>.*?</style>'si",		//去掉css
					"'<!--.*?-->'si",		//去掉注释
					"'<[\/\!]*?[^<>]*?>'si",			// strip out html tags
					"'([\r\n])[\s]+'",					// strip out white space
					"'&(quot|#34|#034|#x22);'i",		// replace html entities
					"'&(amp|#38|#038|#x26);'i",			// added hexadecimal values
					"'&(lt|#60|#060|#x3c);'i",
					"'&(nbsp|#160|#xa0);'i",
					"'&(gt|#62|#062|#x3e);'i",
					"'&(iexcl|#161);'i",
					"'&(cent|#162);'i",
					"'&(pound|#163);'i",
					"'&(copy|#169);'i",
					"'&(reg|#174);'i",
					"'&(deg|#176);'i",
					"'&(#39|#039|#x27);'",
					"'&(euro|#8364);'i",				// europe
					"'&a(uml|UML);'",					// german
					"'&o(uml|UML);'",
					"'&u(uml|UML);'",
					"'&A(uml|UML);'",
					"'&O(uml|UML);'",
					"'&U(uml|UML);'",
					//"' '",//空格
					"'&szlig;'i",
					);
	$replace = array(	"",
						"",
						"",
						"",
						"\\1",
						"\"",
						"&",
						"<",
						">",
						" ",
						chr(161),
						chr(162),
						chr(163),
						chr(169),
						chr(174),
						chr(176),
						chr(39),
						chr(128),
						"?",
						"?",
						"?",
						"?",
						"?",
						"?",
						//"",
						"?",
					);
				
	$text = preg_replace($search,$replace,$document);
							
	return strip_tags($text);
}


function d_s($name = 'default') { 
	global $ss_timing_start_times; 
	$ss_timing_start_times[$name] = explode(' ', microtime());
} 

function d_e($show=1,$name = 'default') { 
	global $ss_timing_stop_times; 
	$ss_timing_stop_times[$name] = explode(' ', microtime()); 
	if($show == 1){
		echo '<p>'.ss_timing_current($name).'</p>';
	}else{
		return ss_timing_current($name);
	}
} 

function ss_timing_current ($name = 'default') { 
	global $ss_timing_start_times, $ss_timing_stop_times; 
	if (!isset($ss_timing_start_times[$name])) {
	   return 0; 
	} 
	if (!isset($ss_timing_stop_times[$name])) { 
	   $stop_time = explode(' ', microtime()); 
	} else { 
	   $stop_time = $ss_timing_stop_times[$name]; 
	} 
	$current = $stop_time[1] - $ss_timing_start_times[$name][1]; 
	$current += $stop_time[0] - $ss_timing_start_times[$name][0]; 
	return $current; 
}


//同义词替换
function get_replace_words(){
	$words = array();
	$data_file = PICK_DIR.'/data/word.dat';
	$handle = fopen($data_file, "r");
	$data = fread($handle, filesize($data_file));
	$data = $old_data = trim($data);
	if(GBK) $data = str_iconv($data);
	$word_arr = explode(WRAP, $data);
	if(!$word_arr){
		$word_arr = explode(WRAP, $old_data);
	}
	if(!$word_arr) return;
	$format_str = milu_lang('format_str');
	$format_arr = explode('@', $format_str);
	$ext_str = '→';
	foreach((array)$format_arr as $k => $v){
		$v_arr = explode('|', $v);
		if($v_arr[0] == '&rarr;') $ext_str =  $v_arr[1];
	}
	foreach((array)$word_arr as $k=>$v){
		if(!$k) continue;
		$str_arr = explode($ext_str, $v);//关键词分割符
		if(empty($str_arr[0])) continue;
		$words += array("$str_arr[0]" => "$str_arr[1]");
	}
	return $words;
}

//msg是数组
function show_pick_info($msg, $type = '', $args = array()){
	$id = $args['now'] ? $args['now'] : '-'.time().rand(1,9999);
	$show_msg = '';
	if($args['start'] == 1 || $type == 'url' || $type == 'left' || $type == 'show_err'){
		$show_msg = '<div class="run_li_box"><ul class="tipsblock">';
	}
	$li_str = $args['li_no_end'] == 1 ? '' : '</li>';
	$no_border = ($args['no_border']== 1 || $type == 'show_err') ? 'style=" border:0"' : '';
	$now_str = $args['now'] > 0 ? $args['now'].($args['sec_now'] ? '-'.$args['sec_now'] : '').'.' : '';
	$show_loading = $no_loading_str = $indent = '';
	if($type == 'left' || $type == 'url'){
		//$indent = $args['sec_now'] ? '&nbsp;&nbsp;&nbsp;&nbsp;' : '';
		$show_loading = '<span class ="show_loading">'.milu_lang('loading').'</span>';
	}
	
	if($type == 'err'){
		$show_msg .= '<span class ="f_r p_e">'.$msg.'</span></li>'.$no_loading_str;
	}else if($type == 'show_err'){
		$show_msg .= '<img  style="margin:5px;float:left;" src="'.PICK_URL.'static/image/s4.gif" /><li  '.$no_border.'>'.$msg.'</li>';
	}else if($type == 'url'){
		$show_status_info = $msg[0].' '.cutstr(trim($msg[1]), 60);
		$show_msg .= '<li id="show_'.$id.'" '.$no_border.'><span class="f_l">'.$indent.$now_str.$msg[0].'</span>  <span class="lin"><a href="'.$msg[1].'" target="_blank">'.cutstr(trim($msg[1]), 65).'</a>'.$msg[2].'</span>'.$li_str;
	}else if($type == 'left'){
		$show_status_info = $msg[0].' '.$msg[1];
		$show_msg .= '<li id="show_'.$id.'" '.$no_border.'><span class="f_l">'.$indent.$now_str.$msg[0].'</span>  <span class="lin">'.$msg[1].'</span>';
	}else if($type == 'success'){
		$h_class = $msg ? '' : 'clear_height'; 
		$show_msg = '<span class="f_r p_r '.$h_class.'">'.$msg.'</span></li>'.$no_loading_str;
	}else if($type =='no'){
		$show_msg .= $msg;
	}else if($type == 'finsh'){
		$show_msg .= '<div class="showmess">'.$msg.'</div><script>p_finsh();</script>'.$no_loading_str;
	}else if($type == 'exit' || !$type){
		$sty = $msg ? 'style=" border:0"' : 'style="height:5px;line-height:5px; border:0"';
		$class = $type == 'exit' ? ' class="e_p_e" ' : '';
		$show_msg .= '<div class="run_li_box"><ul class="tipsblock"><li '.$class.$sty.'>'.$msg.'</li></ul></div>';
		$show_status_info = $msg;
	}
	if($args['end'] == 1  || $type == 'err' || $type == 'success' || $type == 'show_err'){
		$show_msg .= '</ul></div>';
	}
	if($args['pro']){
		$show_msg .= '<script>SetProgress("'.$args['pro'].'","'.$args['wait_time'].'", "'.$args['wait_count'].'", "'.$args['memory'].'");</script>';
	}
	if($args['is_log'] == 1) {
		pload('F:pick');
		pick_log($show_msg, $args);
	}	
	if($args['is_cron'] == 1) return;
	//print str_repeat(" ", 4096);
	$show_status_info = strip_tags($show_status_info, '&nbsp;');
	$show_status_info = cutstr(trim($show_status_info), 85);
	$args['show_id'] = ($type== 'left' || $type == 'url') ?  '' : $args['show_id'];
	$script =  "s('".$id."','".$show_status_info."');".$args['show_js'];
	echo $show_msg."<script>$script</script>".$show_loading;
	ob_flush();
	flush();
}


function ischinese($s){  
	$allen = preg_match("/^[^\x80-\xff]+$/", $s);   //判断是否是英文  
	$allcn = preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$s);  //判断是否是中文  
	if($allen){    
		return 'allen';    
	}else{    
		if($allcn){    
			return 'allcn';    
		}else{    
			return 'encn';    
		}    
	}                    
}   



function unhtmlentities ($string) {
	$string = str_replace('&nbsp;', ' ', $string);
	// Get HTML entities table
	$trans_tbl = get_html_translation_table (HTML_ENTITIES, ENT_QUOTES);
	// Flip keys<==>values
	$trans_tbl = array_flip ($trans_tbl);
	// Add support for &apos; entity (missing in HTML_ENTITIES)
	$trans_tbl += array('&apos;' => "'");
	// Replace entities by values
	return strtr ($string, $trans_tbl);
}



function get_avg($arr){
	if(!$arr) return ;
	sort($arr);
	$count = count($arr);
	if($count > 6) unset($arr[0],$arr[1],$arr[$count-2],$arr[$count-1]);	
	$total = array_sum($arr);
	return $total/$count;
}

function get_repeat_arr($array){
	$existNumArray = array_count_values ($array);
	$answerArray = array();
	if($existNumArray){
		foreach($existNumArray as $k=>$v){
			if($v>1){
				$answerArray[$k] = array_keys($array,$k);
			}
		}
	}
	return $answerArray;
}

function get_element_arr($data, $name_arr){
	foreach($name_arr as $k => $v){
		preg_match_all("#<\s*".$v."[^>]*>(.*?)<\s*\/\s*".$v."\s*>#is", $data, $arr[$k]);
	}
	return $arr;
}

function chineseCount($str){
	$count = preg_match_all("/[\xB0-\xF7][\xA1-\xFE]/",$str,$ff);
	return $count;
}



function array_resolve($arr,$i=0){
	if(!is_array($arr)) return false;
	foreach($arr as $k => $v){
		$b = array_resolve($v, $i);
		if(is_array($v) && $v){
			$a = is_array($a) ? array_merge($a, $b) : $b;
			$i += count($a);  
		}else{
			$a[$i] = $v;
			$i++;
		}
	}
	$re = is_array($a) ? array_unique($a) : $a;
	return $re;
}



function data_go($url){
	echo "<script>location.href='admin.php?".PICK_GO.$url."';</script>";
}

function create_id(){
	return TIMESTAMP.rand(1,1000);
}



function format_wrap($str, $exp_type = PHP_EOL){
	if(!$str) return false;
	$arr = explode($exp_type, trim($str));
	$arr = array_map('trim',$arr);
	$arr = array_filter($arr);
	return $arr;
}

function format_url($url, $flag = 1){
	$url = trim(str_iconv($url));
	$url = stripslashes($url);
	$url = stripslashes($url);
	if(!$url || ($flag == 1 && $url == 'undefined')) return false;
	$url = str_replace(array('JK123LL', 'JK123GG', 'CYH123', 'K_L123', 'K_R123', 'XH123', 'DY123', 'XG123', 'BFH123'), array('<', '>', '"','(', ')', '*','\'', '\\', '%'), $url);
	//if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
		$url = str_replace("\r\n", "\r\m", $url);	
		$url = str_replace("\n", "\r\n", $url);
		$url = str_replace("\r\m", "\r\n", $url);
	//}	
	return $url;
}


/*
* $args['is_fiter'] 是否过滤 1代表过滤
* $args['replace_rules']替换规则 
* $args['filter_data'] 过滤规则 
* $args['show_type'] 类型 
* $args['result_data'] 要处理的的东西
* $args['test'] 1代表测试 2 代表正式的生产环境
*/
function filter_article($args = array()){
	extract($args);
	if($is_fiter == 2) return $result_data;
	if(!$test) $test = 1;
	//$result_data = dstripslashes($result_data);
	if($replace_rules) {
		if($show_type == 'reply'){
			if(is_array($result_data)){
				foreach($result_data as $k => $v){
					$result_data[$k] = replace_something($v, $replace_rules, $test);
				}
			}
		}else{
			$result_data = replace_something($result_data, $replace_rules, $test);//替换
		}	
	}
	if($filter_data) {
		if(is_array($filter_data)){
			foreach($filter_data as $k => $v){
				if(!$v[1]){
					$v[0] = $v['type'];
					$v[1] = $v['rules'];
				}
				$v[1] = rpc_str($v[1]);
				if(!$v[0]) continue;
				if($v[0] == 1){//dom
					if($show_type == 'reply'){
						if(is_array($result_data)){
							foreach($result_data as $k2 => $v2){
								if($v[1]) $result_data[$k2] = dom_filter_something($v2, $v[1], $test);
								
							}
						}
					}else{
						if($v[1]) $result_data = dom_filter_something($result_data, $v[1], $test);
					}
				}else{//字符串
					if($show_type == 'reply'){
						if(is_array($result_data)){
							foreach($result_data as $k2 => $v2){
								if($v[1])$result_data[$k2] = str_filter_something($v2, $v[1], '', $test);
							}
						}
					}else{
						if($v[1]) $result_data = str_filter_something($result_data, $v[1], '', $test);
					}
				}
			}
			
		}
	}
	//格式化
	if($filter_html){
		if(is_array($result_data)){
			foreach($result_data as $k2 => $v2){
				$result_data[$k2] = clear_html_script($v2, $filter_html);
			}
		}else{
			
			$result_data = clear_html_script($result_data, $filter_html);
		}
	}
	return $result_data;
}

function trip_runma($html){
	return $html;
    //return preg_replace('@<font class="jammer">.*?</font>|<span style="display:none">.*?</span>@', '', $html);
}

//过滤
function filter_something($str,$find,$type = false){
	if(!is_array($find)){
		$find_arr = format_wrap(trim($find));
	}else{
		$find_arr = $find;
	}
	if(!$find_arr) return $type;
	$filterwords = implode("|",$find_arr);
	$filterwords = str_replace('(*)', '*', $filterwords);
	$filterwords = convertrule($filterwords);
	$filterwords = str_replace('\|', '|', $filterwords);
	if(preg_match("/(".$filterwords.")/i",$str,$match) == 1){
   		return false;
	}
	return true;
}


//某组东西替换某个东西
function replace_something($str, $replace_str, $test = 0, $limit = -1){
	if(!$str || !$replace_str) return $str;
	if(!is_array($replace_str)){
		$replace_arr = format_wrap(trim($replace_str));
	}else{
		$replace_arr = $replace_str;
	}
	
	$replace_arr = replace_str_args($replace_arr, $str);
	if($replace_arr){
		foreach($replace_arr as $k => $v){
			$rules_arr = explode('@@', trim($v));
			$rules_arr[0] = str_replace(array('(*)', '*'),array('[list]', 'CT_TR'), $rules_arr[0]);
			$rules_arr[0] = convertrule($rules_arr[0]);
			$rules_arr[0] = str_replace(array("'", "\"", '\[list\]'),array("\'", "\\\"", '\s*(.+?)\s*'), $rules_arr[0]);
			$rules_arr[1] = str_replace("'","\'", $rules_arr[1]);
			$search_arr[$k] = "'".$rules_arr[0]."'si";
			if($test != 1){
				$replace_arr[$k] =  $rules_arr[1];
			}else{
				preg_match_all("/$rules_arr[0]/is", $str, $arr);
				if(is_array($arr[0])){
					foreach($arr[0] as $k1 => $v1){
						if($v1){
							$test_search_arr[$k1] = $v1;
							if(!$rules_arr[1]){
								$str = str_replace($v1, '<del>'.$v1.'</del>', $str);
							}else{
								$str = str_replace($v1, '<ins>'.$rules_arr[1].'</ins>', $str);
							}
							
						}
					}
				}
			}
		}			
	}
	$str = str_replace(array( '*'),array('CT_TR'), $str);
	if($test != 1) $str = preg_replace($search_arr, $replace_arr, $str, $limit);
	$str = str_replace(array( 'CT_TR'),array('*'), $str);
	return $str;
}

function replace_str_args($replace_rules_arr, $str){
	if(!$replace_rules_arr) return $str;
	$search_arr = $rarr = array();
	foreach($replace_rules_arr as $k => $v){
		$rules_arr = explode('@@', trim($v));
		if(!strexists($rules_arr[0], '{') || !strexists($rules_arr[0], '}') || !strexists($rules_arr[1], '{') || !strexists($rules_arr[1], '{')) continue;
		preg_match_all('/\{(.*?)\}/is', $rules_arr[0], $v0_arr, PREG_SET_ORDER);
		preg_match_all('/\{(.*?)\}/is', $rules_arr[1], $v1_arr, PREG_SET_ORDER);
		foreach($v0_arr as $k0 => $v0){
			$search_arr[] = $v0[0];
		}
		$rule = $rules_arr[0];
		
		$rule = str_replace($search_arr, '(.*?)', $rule);
		$rule = preg_quote($rule, "/");
		$rule = str_replace('\(\.\*\?\)', '(.*?)', $rule);
		$rule = str_replace('\(\*\)', '\s*.+?\s*', $rule);
		preg_match_all("/$rule/is", $str, $rarr, PREG_SET_ORDER);
		if($rarr[0] && !$rarr[1]){//{1}放在末尾匹配不出的情况
			$rule = str_replace('(.*?)', '(.*)', $rule);
			preg_match_all("/$rule/is", $str, $rarr, PREG_SET_ORDER);
		}
		array_shift($rarr[0]);
		$rules_arr[0] = str_replace($search_arr, $rarr[0], $rules_arr[0]);
		$rules_arr[1] = str_replace($search_arr, $rarr[0], $rules_arr[1]);
		$replace_rules_arr[$k] = implode('@@', $rules_arr);
	}
	return $replace_rules_arr;
}


function get_keyword($keyword = ''){
	$url = 'http://www.baidu.com/s?wd='.$keyword;
	$html = file_get_html($url);
	if(!$html) return false;
	foreach($html->find('div[id=rs] th a') as $v) {
		$arr[] = str_iconv($v->innertext);
	}
	return $arr;
}

/**
 * 解析内容
 */
function pregmessage($message, $rule, $getstr, $limit=1, $get_type = 'in') {
	if(!$message) return array();
	$message = pick_warp_format($message);	
	$rule = convertrule($rule);		//转义正则表达式特殊字符串
	$result = array();
	$rule = str_replace('\['.$getstr.'\]', '\s*(.+?)\s*', $rule);	//解析为正则表达式
	if($limit == 1) {
		$result = array();
		preg_match("/$rule/is", $message, $rarr);
		if(!empty($rarr[1])) {
			$result[] = $get_type == 'in' ? $rarr[1] : $rarr[0];
		}
	} else {
		preg_match_all("/$rule/is", $message, $rarr, PREG_SET_ORDER);
		if(!empty($rarr[0])) {
			$key  = $get_type == 'in' ? 1 : 0; 
			foreach($rarr as $k => $v){
				$result[] = $v[$key];
			}
		}
	}
	return $result;
}

/**
 * 正则规则
 */
function getregularstring($rule, $getstr) {
	$rule = convertrule($rule);		//转义正则表达式特殊字符串
	$rule = str_replace('\['.$getstr.'\]', '\s*(.+?)\s*', $rule);	//解析为正则表达式
	return $rule;
}
/**
 * 转义正则表达式字符串
 */
function convertrule($rule) {
	$rule = dstripslashes($rule);
	$rule = preg_quote($rule, "/");		//转义正则表达式
	$rule = str_replace('\*', '.*?', $rule);
	$rule = str_replace("\(.*?\)", '(.*?)', $rule);
	
	//$rule = str_replace('\|', '|', $rule);
	
	return $rule;
}


//将数组中相同的值去掉,同时将后面的键名也忽略掉
function sarray_unique($array) {
	$newarray = array();
	if(!empty($array) && is_array($array)) {
		$array = array_unique($array);
		foreach ($array as $value) {
			$newarray[] = $value;
		}
	}
	return $newarray;
}


function str_format_time($timestamp = '', $format_str = ''){  
	if(!$timestamp) return FALSE;
	$timestamp = trim($timestamp);//去掉首尾空格
	$str = $timestamp;
	$lang = lang('core', 'date');
	$dateline = 0;
	$str = str_replace('&nbsp;', '', $str);
	if($str == $lang['now']){//刚刚
		$dateline = TIMESTAMP;
	}else if(strexists($str, $lang['sec'].$lang['before'])){//x秒前
		$value = str_replace($lang['sec'].$lang['before'], '', $str);
		$value = intval($value);
		$dateline = TIMESTAMP - $value;
	}else if(strexists($str, $lang['min'].$lang['before'])){//x分钟前
		$value = str_replace($lang['min'].$lang['before'], '', $str);
		$value = intval($value);
		//$dateline = TIMESTAMP - rand($value*60, ($value+1)*60);
		$dateline = TIMESTAMP - $value*60;
	}else if($str == $lang['half'].$lang['hour'].$lang['before']){//半小时前
		//$dateline = TIMESTAMP - rand(1800, 3600);
		$dateline = TIMESTAMP - 30*60;
	}else if(strexists($str, $lang['hour'].$lang['before'])){//x小时前
		$value = str_replace($lang['hour'].$lang['before'], '', $str);
		$value = intval($value);
		//$dateline = TIMESTAMP - rand($value*3600, 3600*($value+1));
		$dateline = TIMESTAMP - $value*3600;
	}else if(strexists($str, $lang['yday'])){//昨天&nbsp;01:33
		$value = str_replace($lang['yday'], '', $str);
		$format_str = date('Y-m-d').' '.$value;
		$dateline = strtotime($format_str) - 3600*24;
	}else if(strexists($str, $lang['byday'])){//前天&nbsp;01:33
		$value = str_replace($lang['byday'], '', $str);
		$format_str = date('Y-m-d').' '.$value;
		$dateline = strtotime($format_str) - 3600*24*2;
	}else if(strexists($str, $lang['day'].$lang['before'])){//x天前
		$value = str_replace($lang['day'].$lang['before'], '', $str);
		$value = intval($value);
		//$dateline = TIMESTAMP - rand(3600*24*($value-1), 3600*24*$value);
		$dateline = TIMESTAMP - 3600*24*$value;
	}
	if($dateline > 0) return $dateline;
	
	if($format_str){
		$format_str = strtolower($format_str);
		$date_get_arr = array('y', 'm', 'd', 'h', 'i', 's');
		preg_match_all("/\d+/is", $timestamp, $rarr);
		$value_arr = $rarr[0];
		$key_arr = array();
		foreach($date_get_arr as $k => $v){
			$pos = strrpos($format_str, $v);
			if($pos !== FALSE) $key_arr[$pos] = $v; 
		}
		ksort ($key_arr);
		$temp_arr = array('y' => 'year', 'm' => 'month', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'seconds');
		$i = 0;
		foreach($key_arr as $k => $v){
			$name = $temp_arr[$v];
			$$name = $value_arr[$i];
			$i++;
		}
	}else{
		$old_length = strlen($timestamp);
		$timestamp = str_replace(milu_lang('day').' ', milu_lang('day'), $timestamp);
		$timestamp = str_replace(array(milu_lang('year'), milu_lang('month'), milu_lang('day'), milu_lang('hour'), milu_lang('minute'), milu_lang('sec'), '/'), array('-', '-', ' ', ':', ':', ':', '-'), $timestamp);
		$new_length = strlen($timestamp);
		if($old_length == strlen($timestamp) && !strexists($timestamp, '-')){//类似20131121
			$timestamp = intval($timestamp);
			$year = substr($timestamp,  0, 4);
			$month = substr($timestamp,  4, 2);
			$day = substr($timestamp,  6, 2);
		}else{
			list($date, $time) = explode(" ", $timestamp);
			list($year, $month, $day) = explode("-", $date);
			list($hour, $minute, $seconds ) = explode(":", $time);
		}
	}
	$year = $year ? $year : date('Y');
	$month = $month ? $month : date('m');
	$day = $day ? $day : date('d');
	$hour = $hour ? $hour : 0;
	$minute = $minute ? $minute : 0;
	$seconds = $seconds ? $seconds : 0;
	$timestamp = mktime($hour, $minute, $seconds, $month, $day, $year);
	return $timestamp;
}

function _expandlinks($links,$URI)
{
	$links = trim($links);
	$URI = trim($URI);
	$links = html_entity_decode($links);
	$links =  str_replace("\/", "/", $links);
	preg_match("/^[^\?]+/",$URI,$match);
	$url_parse_arr = parse_url($URI);
	$check = strpos($links, "?");
	if($check == 0 && $check !== FALSE){
		return $url_parse_arr["scheme"]."://".$url_parse_arr["host"].'/'.$url_parse_arr['path'].$links;
	}
	$check = strpos($links, "../");
	if($check == 0 && $check !== FALSE){//相对路径
		$check_arr = explode('/', $url_parse_arr['path']);
		if(trim(end($check_arr))) {//最后一个字符是/的时候
			$path = dirname($url_parse_arr['path']);
		}else{
			$path = $url_parse_arr['path'];
		}
		$path_arr = explode('/', $path);
		array_shift($path_arr);
		$path_arr = array_filter($path_arr);
		$i = 0;
		while ( substr ( $links, 0, 3 ) == "../" ) {  
			$links = substr ( $links, strlen ( $links ) - (strlen ( $links ) - 3), strlen ( $links ) - 3 );
			$i++;
		} 
		$temp_arr = array_slice($path_arr, 0, count($path_arr) - $i);
		return $url_parse_arr["scheme"]."://".$url_parse_arr["host"].'/'.($temp_arr ? implode('/',$temp_arr).'/' : '').$links;
	}
	$match = preg_replace("|/[^\/\.]+\.[^\/\.]+$|","",$match[0]);
	$match = preg_replace("|/$|","",$match);
	$match_part = parse_url($match);
	//纠正类似这样，有多个点的会出错 http://www.56php.com/54.345.html
	if($match_part['path'] && strexists($match_part['path'], '.htm') || strexists($match_part['path'], '.html')){
		$exp_info = explode('/', $match_part['path']);
		$last = end($exp_info);
		$match = str_replace('/'.$last, '', $match);
	}
	$port = $match_part["port"]  ?  ':'.$match_part["port"] : '';
	$match_root = $match_part["scheme"]."://".$match_part["host"].$port;
	$links = str_replace('https://', 'http://ASDFAFDSAFASDFSDA', $links);
	$search = array( 	"|^http://".preg_quote($match_root)."|i",
						"|^(\/)|i",
						"|^(?!http://)(?!mailto:)|i",
						"|/\./|",
						"|/[^\/]+/\.\./|"
					);
					
	$replace = array(	"",
						$match_root."/",
						$match."/",
						"/",
						"/"
					);
	$expandedLinks = preg_replace($search,$replace,$links);
	$expandedLinks = str_replace('http://ASDFAFDSAFASDFSDA', 'https://', $expandedLinks);
	return $expandedLinks;
}



function filter_url_callback($url){
	global $_G;
	$evo_rules = $_G['cache']['evn_milu_pick']['evo_rules'];
	$no_url_arr = $evo_rules['no_url'];
	foreach($no_url_arr as $k => $v){//比正则快十倍以上
		if(strexists($url, $v)) return FALSE;
	}
	return $url;
}


//格式化url
function convert_url($url){
	if(!$url) return;
	$url =  str_replace('&amp;', '&', dhtmlspecialchars(trim($url)));
	$url = html_entity_decode($url);
	return $url;
}


//转换不同编码的序列化数组
function serialize_iconv($thevalue){
	global $_G;
	if(!is_array($thevalue)) return $thevalue;
	foreach((array)$thevalue as $k => $v){//防止编码不同造成的错误
		$v_s = dunserialize($v);
		if($v_s == $v || $v_s == FALSE){//不是序列化
			if(is_array($v)){//如果是数组
				$thevalue[$k] = serialize_iconv($v);
			}else{
				$thevalue[$k] = $_G['config']['output']['language'] == 'zh_tw' && $_G['config']['output']['charset'] == 'big5' ? gb2big5($v) : str_iconv($v);
			}
		}else{
			$v = dunserialize($v);
			$v = serialize_iconv($v);
			$thevalue[$k] = serialize($v);
 		}
	}
	return $thevalue;
}


function rpc_str($str, $flag = 1){
	if($flag && $str == 'undefined') return '';
	$str = str_iconv(trim(format_url(urldecode(urldecode($str)))));
	return $str;
}


function format_cookie($str){
	if($str == 'undefined' || $str == 'false') return '';
	return  str_iconv(trim(format_url($str)));
}




function convert_url_range($args){
	extract($args);
	if(!$url) return;
	if(!strexists($url, '(*)')) return array($url);
	$range_arr = range($start, $end, $step);
	$count = count($range_arr);
	$max_len = strlen($range_arr[$count - 1]);
	foreach($range_arr as $k => $v){
		$v = $auto ? str_pad($v, $max_len, "0", STR_PAD_LEFT) : $v;
		$arr[] = str_replace('(*)', $v, $url); 
	}
	return $arr;
}


/*数组组合合并*/
/*
功能描述如下：
$arr[0] = array('a','h','k');
$arr[1] = array('b' ,'c');
$arr[2] = array('d', 'e' ,'f');
$arr[3] = array('m', 'g' ,'p', 'u'); 
将arr里面的二维数组，每个数组各取一个元素出来进行组合 返回所有组合的数组
*/
function my_array_merge($arr){
	$info = get_array_info($arr);
	for($i = 1; $i < $info['change_arr'][0] + 1; $i++){
		$new_arr[$i] = get_array_value($i, 0, '', $info);
	}
	return $new_arr ? array_unique($new_arr) : $new_arr;
}
function get_array_info($arr){
	$count = 1;
	foreach($arr as $k => $v){
		if(!is_array($v)) $v = array($v);
		$c = count($v);
		$count *= $c ;
		$count_arr[$k] = $c;
	}
	foreach($arr as $k => $v){
		$team_arr = array_slice($count_arr, $k);
		$change_arr[$k] =  array_product($team_arr);    
	}
	$info['count_arr'] = $count_arr;
	$info['change_arr'] = $change_arr;
	$info['arr'] = $arr;
	return $info;
}

function get_array_value($i,$k = 0,$re = '',$info){
	extract($info);
	$last_key = count($change_arr) - 1;
	if($k == count($change_arr)) return $re;
	$v = $change_arr[$k+1];
	$last_c = count($arr[$last_key]);
	$v = $v ? $v : 0;
	$c = $count_arr[$k];
	if($k == $last_key){//最底层的
		if($i > $last_c){
			$j = $i % $last_c - 1;
		}else{
			$j = $i - 1;
		}
		if( ($i % $last_c) == 0) $j = $last_c - 1;
	}else{
		$m = ceil($i / $v);
		if( ($m > $v && $m != $c) || ($k != 0 && $m != $c )){
			if($i > $v){

				if($m % $c == 0){
					$j = $c - $m%$c - 1;
				}else{
					$j = abs($m % $c - 1);
				}
			}else{
				$j = 0;
			}
		}else{
			$j = $m - 1;
			
		}
	}

	$re .= $arr[$k][$j];
	return get_array_value($i, $k + 1, $re, $info);
}
/*结束*/

function clear_ad_html($document) {
	if (!$document) return $document;
	$search = array(
					"'<script[^>]*?>.*?</script>'si",		//去掉js
					"'<style[^>]*?>.*?</style>'si",		//去掉css
					"'<iframe[^>]*?>.*?</iframe>'si",		//去掉框架
					"'<!--.*?-->'si",		//去掉注释
					"/(onclick|onMouseUp|onMouseDown|onDblClick|onMouseOver|onMouseOut|onmouseenter|onload)=('|\")?(.*)\\2/isU",		//去掉各种事件，有些时候去掉style导致图片无法居中
					//"/(onclick|style|onMouseUp|onMouseDown|onDblClick|onMouseOver|onMouseOut|onmouseenter|onload)=('|\")?(.*)\\2/isU",		//去掉各种事件

					);
	//这里不能在过滤之前去掉style等标签，因为过滤要依靠这些标签，去掉的话，有些过滤功能会失效				
	$replace = array(	"",
						"",
						"",
						"",
						"",
					);
				
	$text = preg_replace($search,$replace,$document);
	return $text;
}


function load_cache($key,$clearStaticKey = FALSE){
	require_once(PICK_DIR.'/lib/cache.class.php');
	$cache = new serialize_cache();
	return $cache->get($key,$clearStaticKey);
}

function cache_data($key,$value,$ttl = 3600){
	if($ttl < 0 || $ttl == 0) return FALSE;
	require_once(PICK_DIR.'/lib/cache.class.php');
	$cache = new serialize_cache();
	$value = is_array($value) ? $value : rawurlencode($value);
	$cache->set($key,$value,$ttl);
}
function cache_del($key){
	require_once(PICK_DIR.'/lib/cache.class.php');
	$cache = new serialize_cache();
	$cache->delete($key);
	
}


//新增，用数据库保存缓存
function pload_cache($key){
	global $_G;
	$key = md5($key);//名字不能超过32字符，干脆用md5
	$cache_data = DB::fetch_first("SELECT * FROM ".DB::table('common_syscache')." WHERE cname='$key'");
	$data_info = dunserialize($cache_data['data']);
	if(!$data_info['exp_dateline'] || $data_info['exp_dateline'] < TIMESTAMP) {
		pcache_del($key);
		return FALSE;
	}
	$result_data = dunserialize(base64_decode($data_info['data']));
	if($data_info['data'] && !$result_data) return rawurldecode($data_info['data']);//不是数组
	return $result_data;
}

function pcache_data($key , $value, $ttl = 3600){
	$key = md5($key);
	if($ttl < 0 || $ttl == 0) return FALSE;
	$value = is_array($value) ? serialize($value) : rawurlencode($value);
	$config = pick_common_get();
	$cache_value = array('exp_dateline' => TIMESTAMP + $ttl, 'data' => base64_encode($value));
	$cache_value = (serialize($cache_value));//不加base64_encode有些杂七杂八的东西存进去就没法正常取出来了
	save_syscache($key, $cache_value);
}
function pcache_del($key){
	$key = md5($key);
	DB::query('DELETE FROM '.DB::table('common_syscache')." WHERE cname='".daddslashes($key)."'");
}




function gb2big5($Text){   
   $fp = fopen(PICK_DIR."/data/gb-big5.table", "r");   
   $max = strlen($Text)-1;   
	for($i=0; $i<$max; $i++){ 
		$h = ord($Text[$i]); 
		if($h >= 160){   
			$l=ord($Text[$i+1]);   
		if($h == 161 && $l==64){   
			$gb = " ";  
		}else{   
			fseek($fp, ($h-160)*510+($l-1)*2);   
			$gb = fread($fp,2);   
		}   
		$Text[$i] = $gb[0];   
		$Text[$i+1] = $gb[1]; $i++;   
		}   
	}  
	fclose($fp);   
	return $Text;  
} 


function big52gb($Text){
	$fp = fopen(PICK_DIR."/data/big5-gb.table", "r"); 
    $max = strlen($Text)-1;
    for($i=0;$i<$max;$i++){
	   $h = ord($Text[$i]);
	   if($h>=160){
			$l = ord($Text[$i+1]);
			if($h == 161 && $l==64){
				$gb = " ";
			}else{
				fseek($fp, ($h-160)*510+($l-1)*2);
				$gb = fread($fp,2);
			}
			$Text[$i] = $gb[0];
			$Text[$i+1] = $gb[1];
			$i++;
		}
   }
   fclose($fp);
   return $Text;
 }



function get_data_range($str, $start = 0, $num = 1){
	$str = trim($str);
	if(!$str && $num == 1 ) return 0;
	if(!$str && $num > 1 ) return array();
	if(strexists($str, ',')){
		$str_arr = format_wrap($str, ',');
		$str_arr = array_filter($str_arr, 'intval') ;
		$str_arr[0] = intval($str_arr[0]);
		$str_arr[1] = intval($str_arr[1]);
		if($start < 0){
			for($i = 1; $i < $num + 1; $i++){
				$re_arr[] = rand($str_arr[0], $str_arr[1]); 
			}
			return $num == 1 ? $re_arr[0] : $re_arr;
		}else{
			$start += $str_arr[0]; 
			$end = $start + $num - 1;
			$end = ($end > $str_arr[1]) ? $str_arr[1] : $end;
			$re_arr['list'] = $start > $str_arr[1] ? array() : range($start, $end);
			$re_arr['num'] = $num;
			$re_arr['all_num'] = ($str_arr[1] - $str_arr[0]) + 1;
		}
	}else if(strexists($str, '|')){
		$arr = format_wrap($str, '|');
		$end = $start + $num;
		if($start < 0){
			return array_rand($arr, $num);
		}else{
			$re_arr['list'] = array_slice($arr, $start, $end);
			$re_arr['num'] = $re_arr['all_num'] = count($arr);
		}
	}else{
		$re_arr['list'] = array($str);
		if($start < 0) return $str;
		$re_arr['num'] = $re_arr['all_num'] = 1;
	}
	return $re_arr;
}

function pick_common_set($config_arr){
	if(DISCUZ_VERSION != 'X2') $config_arr = daddslashes($config_arr);
	foreach((array)$config_arr as $k => $v){
		$v = is_array($v) ? pserialize($v) : $v;
		DB::query("REPLACE INTO ".DB::table('strayer_setting')." (`skey`, `svalue`) VALUES ('$k', '".$v."')");
	}
	pick_common_get(1);
}

function pick_common_get($n = 0, $key = ''){
	global $_G;
	$setting = array();
	loadcache('milu_pick_setting');
	$where = $key ? " WHERE skey='$key'" : '';
	if(!($setting = $_G['cache']['milu_pick_setting']) || $n != 0){
		$query = DB::query("SELECT * FROM ".DB::table('strayer_setting')." $where");
		while($row = DB::fetch($query)) {
			$setting[$row['skey']] = $row['svalue'];
		}
		save_syscache('milu_pick_setting', $setting);
	}
	$setting = pstripslashes($setting);
	$setting = $key ? $setting[$key] : $setting;
	return $setting;
}


function strcut($allStr,$star,$end){ 
	eregi("".$star."(.*)".$end."", $allStr, $head);
	$head[0] = str_replace("".$star."","",$head[0]); 
	$head[0] = str_replace("".$end."","",$head[0]);
	return trim($head[0]);
}

function get_memory(){
	if(!function_exists('memory_get_usage')) return FALSE;
	return sizecount(memory_get_usage());
}

function php_set($key){
	if(function_exists('ini_get')) return ini_get($key);
	if(function_exists('get_cfg_var')) get_cfg_var($key);
	return FALSE;
}

function pstripslashes($data){
	if(DISCUZ_VERSION != 'X2') return $data;
	return dstripslashes($data);
}

function paddslashes($data){
	if(DISCUZ_VERSION != 'X2') return $data;
	return daddslashes($data);
}

function diff_time($diff_time, $show = 0){
	$diff_time = intval($diff_time);
	if($diff_time == 0) return;
	$d_str = 24 * 60 * 60;
	$h_str = 60 * 60;
	$m_str = 60;
	$s_str = 1;
	$arr['d'] = floor($diff_time / $d_str); 
	$arr['h'] = floor($diff_time  % $d_str / $h_str); 
	$arr['m'] = floor($diff_time % $d_str % $h_str/$m_str);
	$arr['s'] = floor($diff_time % $d_str % $h_str%$m_str/$s_str);
	if($show ==0) return $arr;
	$re_str = $arr['d'] ? $arr['d'].milu_lang('day') : '';
	$re_str .= $arr['h'] ? $arr['h'].milu_lang('hour') : '';
	$re_str .= $arr['m'] ? $arr['m'].milu_lang('minute') : '';
	$re_str .= $arr['s'] ? $arr['s'].milu_lang('sec') : '';
	return $re_str;
}

function pload($name){
	$arr = explode(',', $name);
	$temp_arr = array();
	$pick_dir = DISCUZ_ROOT.'source/plugin/milu_pick';
	foreach($arr as $k => $v){
		$temp_arr = explode(':', $v);
		$type = strtolower($temp_arr[0]);
		$name = $temp_arr[1];
		$vip_dir = in_array($name, array('vip', 'pick_cron', 'data_trans', 'attach', 'fanyi')) ? 'vip/' : '';
		$func_file = $pick_dir.'/lib/'.$vip_dir.'function.'.$name.'.php';
		$class_file = $pick_dir.'/lib/'.$vip_dir.''.$name.'.class.php';
		if( (!$type || $type == 'f')){//函数库
			require_once($func_file);
		}else if($type == 'c'){//类库
			require_once($class_file);
		}
	}
}


function get_rand_time($s, $e, $n = 1){
	global $_G;
	if($e < $s) return FALSE;
	$s = $s ? $s : $_G['timestamp'];
	$e = $e ? $e : $_G['timestamp'];
	if($n == 1){
		if($e == $s) {
			return $_G['timestamp'];
		}else{
			return rand($s, $e);
		}	
	}
}


function parray_rand($arr, $num = 1){
	$key = array_rand($arr, $num);
	$data_arr = array();
	foreach($key as $k => $v){
		$data_arr[] = $arr[$v];
	}
	if($num > 1) return $data_arr;
	return $arr[$key];
}

function pget_table_count($table_name, $sql = ''){
	return DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_'.$table_name)." $sql"), 0);
}

function rpcServer(){
	global $_config,$_G;
	include_once('phprpc/phprpc_server.php');
	$server = new PHPRPC_Server();
	$server->add(array('test_window','load_keyword', 'show_rules_set', 'login_test', 'tran_api_test'));
	$server->setCharset(CHARSET);
	$server->setEnableGZIP(FALSE);
	$server->start();
	define(FOOTERDISABLED, false);
	exit();

}


function rpcClient($rpc_url = ''){
	include_once ("phprpc/phprpc_client.php");  
	$client = new PHPRPC_Client();  
	$client->setProxy(NULL);
	$client->useService($rpc_url ? $rpc_url : GET_URL.'plugin.php?id=pick_user:share_rules&tpl=no&myac=rpcServer&inajax=1');   
	//$client->setKeyLength(10);  
	//$client->setEncryptMode(3);  
	$client->setCharset('GBK');  
	$client->setTimeout(10);  
	return $client;
}

//导出文件 //改装自SupeSite
function exportfile($array, $filename, $args = array()) {
	include_once libfile('function/home');
	global $_G;
	unset($array['run_times']);
	$array['version'] = strip_tags(PICK_VERSION);
	if(!$args){
		$args = array(
			'type' => milu_lang('dxc_system_rules'),
			'author' => $array['rule_author'],
			'rules_name' => $array['rules_name'],
			'rule_desc' => $array['rule_desc'],
		);
	}
	$args['type'] = str_iconv($args['type']);
	$args['author'] =  str_iconv($args['author']);
	$args['rules_name'] = str_iconv($array['rules_name']);
	$args['rule_desc'] = str_iconv($array['rule_desc']);
	$exporttext = "# DXC Dump\r\n".
	"# Version: DXC ".PICK_VERSION."\r\n".
	"# Type: ".$args['type']."\r\n".
	"# Time: ".dgmdate($_G[timestamp])."\r\n".
	"# From: ".$args['author']." (".$_G['siteurl'].")\r\n".
	"# Name: ".$args['rules_name']."\r\n".
	"# Description: ".$args['rule_desc']."\r\n".
	"# This file was BASE64 encoded\r\n".
	"#\r\n".
	"# DXC: http://www.56php.com/forum-79-1.html\r\n".
	"# Please visit our website for latest news about DXC\r\n".
	"# --------------------------------------------------------\r\n\r\n\r\n".
	
	$text = wordwrap(base64_encode(serialize($array)), 50, "\r\n", 1);
	$file_name = $filename.'['.$args['type'].'].txt"';  
	export_file($exporttext, $file_name);
	
}

function export_file($exporttext, $filename){
	include_once libfile('function/home');
	ob_end_clean(); 
	header('Content-Encoding: none');
	@header("Content-type: text/xml; charset=UTF-8");
	header('Content-Type: '.(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'application/octetstream' : 'application/octet-stream'));
	header('Content-Disposition: attachment; filename="'.$filename);
	header('Content-Length: '.strlen($exporttext));
	header('Pragma: no-cache');
	header('Expires: 0');
	echo $exporttext;
	define(FOOTERDISABLED, false);
	exit;
}


//导入文件 改装自SupeSite
function pimportfile($importdata){
	global $_G;
	$importdata = preg_replace("/(#.*\s+)*/", '', $importdata);	//替换采集器中的注释
	@$thevalue = base64_decode($importdata);	//对采集器编码时行base64解码处理并进行反序列化操作转为可用的数组变量
	$_G['cache']['evn_milu_pick']['charset'] = strtoupper(get_charset($thevalue));//如果样本太短的话，有可能编码识别错误，所以在这里对整体进行编码识别
	$thevalue = unserialize($thevalue);
	$thevalue = serialize_iconv($thevalue);

	//反序列化后，如果结果不是数组，或版本号为空，则提示
	if(!is_array($thevalue) || empty($thevalue['version'])) {
		cpmsg_error(milu_lang('rules_error_data'));
	}

	unset($thevalue['version']);//销毁版本号
	return $thevalue;
}

//把不属于某个表的字段干掉
function get_table_field_name($table, $data_arr = array()){
	global $_G;
	static $db;
	if(empty($db)) $db = & DB::object();
	$fields = mysql_list_fields($db->config[1]['dbname'], DB::table($table), $db->curlink); 
	$columns = mysql_num_fields($fields); 
	for ($i = 0; $i < $columns; $i++) { 
		$field_arr[] = mysql_field_name($fields, $i);
	}
	foreach($data_arr as $k =>$v){
		if($v && is_array($v)) $data_arr[$k] = $v = serialize($v);
		if (!in_array ($k, $field_arr)) unset($data_arr[$k]);
	}
	return $data_arr;
}

function getthreadtypes($args = array() ){
	global $_G;
	if(empty($_GET['selectname'])) $_GET['selectname'] = 'threadtypeid';
	$now_id = $args['typeid'] ? $args['typeid'] : intval($_GET['typeid']);
	$fid = $args['fid'] ? $args['fid'] : intval($_GET['fid']);
	$output = '<select name="'.$_GET['selectname'].'">';
	$query = DB::query("SELECT typeid,name,displayorder FROM ".DB::table('forum_threadclass')." WHERE  fid='$fid' ORDER BY displayorder");
	$output .= '<option value="0" >'.milu_lang('select_class').'</option>';
	while($rs = DB::fetch($query)) {
		$selected = ($rs['typeid'] == $now_id) ? 'selected="selected"' : ''; 
		$output .= '<option '.$selected.' value="'.$rs['typeid'].'">'.$rs['name'].'</option>';
	}
	$output .= '</select>';
	return $output;
}

//检测运行环境
function check_env($type = 1, $get_msg = 1){
	global $_G;
	$msg_s = '<div class="showmess">';
	$check = TRUE;
	$notice = '';
	switch($type){
		case 1://检查是否开启函数
			if(!function_exists('fsockopen') && !function_exists('pfsockopen')){
				$check = FALSE;
				$notice = '<p>'.milu_lang('no_pick_func').'</p>';
			}
		break;
		case 2://检查是否在内网环境
			require_once libfile('function/misc');
			pload('F:copyright');
			$client_info = get_client_info();
			if(!$client_info) {
				$check = FALSE;
				$notice = '<p>'.milu_lang('lan_no_use').'</p>';
			}
		break;
	}
	
	$msg_e = '</div>';
	if($get_msg != 1) return $check;
	if($notice) return $msg_s.$notice.$msg_e;
}

//cookie登录测试
function login_test(){
	pload('F:spider');
	d_s();
	$is_login = intval($_GET['is_login']);
	$login_cookie = rpc_str($_GET['login_cookie']);
	$login_test_url = rpc_str($_GET['login_test_url']);
	$must_have = rpc_str($_GET['must_have']);
	$no_have = rpc_str($_GET['no_hava']);
	$cache_key = 'login_test';
	$pick_config = get_pick_set();
	$cache_time = $pick_config['cache_time'];
	$data_arr = load_cache($cache_key);
	if($cache_time > 0 && $data_arr['content'] && $data_arr['cookie'] == $login_cookie){
		$content = $data_arr['content'];
	}else{
		$args = array(
			'referer' => $login_test_url,
			'cookie' => $login_cookie,
		);
		$snoopy_obj->agent = $_SERVER[HTTP_USER_AGENT];
		$snoopy_obj = get_snoopy_obj($args);
		$snoopy_obj->fetch($login_test_url);
		
		$content = $snoopy_obj->results;
		$header = (array)$snoopy_obj->headers;
		$header = array_map('trim', $header);
		$key = array_search("Content-Encoding: gzip", $header);
		if($key) $content = gzdecode($content);//gzip
		
		if($content) cache_data($cache_key, array('content' => $content, 'cookie' => $login_cookie), $cache_time);
	}
	$old_content = str_iconv($content);

	$must_have_msg = $no_have_msg = '';
	$no_pass_flag1 = $no_pass_flag2 = FALSE;
	if($must_have) {
		$no_pass_flag1 = !strexists($old_content, $must_have);
		$result = $no_pass_flag1 ? milu_lang('test_no_pass') : milu_lang('test_pass');
		$must_have_msg = '<p align="left">'.milu_lang('cookie_test_hava').milu_lang('_maohao').$result.'</p>';
	}	
	if($no_have) {
		$no_pass_flag2 = strexists($old_content, $no_have);
		$result = $no_pass_flag2 ? milu_lang('test_no_pass') : milu_lang('test_pass');
		$no_have_msg = '<p align="left">'.milu_lang('cookie_test_no_hava').milu_lang('_maohao').$result.'</p>';
	}
	if($must_have || $no_have){
		$test_result = '<p align="left">'.milu_lang('cookie_can_use_result').$result.'</p>';
		$result  =  ($no_pass_flag1 || $no_pass_flag2) ? milu_lang('test_no_pass') : milu_lang('test_pass');
		$test_result = '<p align="left">'.milu_lang('cookie_can_use_result').$result.'</p>';
	}
	$content2 = dhtmlspecialchars($old_content);

	$file = md5($login_test_url).'.htm';
	$file2 = md5($login_test_url).'_source.htm';
	file_put_contents(PICK_CACHE.'/'.$file, strexists(strtoupper($content), 'CHARSET') ? $content : $content2);
	file_put_contents(PICK_CACHE.'/'.$file2, '<textarea name="" cols="" rows="" style="width:100%; height:100%" >'.$content2.'</textarea>');
	$get_time = d_e(0);
	$output = '<div style="margin:40px 0 0 140px; ">'.$must_have_msg.$no_have_msg.$test_result.'<p align="left">'.milu_lang('view_login_page_length', array('l' => strlen($content), 't' => $get_time)).'</p><br /><p align="left"><a target="_blank" href="'.PICK_URL.'data/cache/'.$file.'" >'.milu_lang('view_login_page_by_html').'</a></p><br /><p align="left"><a target="_blank" href="'.PICK_URL.'data/cache/'.$file2.'" >'.milu_lang('view_login_page_by_source').'</a></p></div>';
	return $output;

}

//处理中文域名
function cnurl($url){
	global $_G;
	if(ischinese($url) != 'encn') return $url;
	$_G['cn_charset'] = $_G['cache']['evn_milu_pick']['charset'];
	if(!$_G['cn_charset']){
		$content = get_contents($url);
		$_G['cn_charset'] = strtoupper(get_charset($content));
	}
	$url = url_unescape($url);
	$url_info = parse_url($url);
	$url_query = $url_info['query'];
	parse_str($url_query, $url_arr);
	$args_arr = array();
	if($url_arr){
		foreach((array)$url_arr as $k => $v){
			$v = cnurl_format($v);
			$args_arr[] = $k.'='.$v; 
		}
		$args_str = implode('&', $args_arr);
		$url = str_replace($url_query, $args_str, $url);
	}else{
		return cnurl_format($url);
	}
	return $url;
}

function cnurl_format($str){
	global $_G;
	$str = trim($str);
	if(!$str) return;
	$str = url_unescape($str);
	if(ischinese($str) == 'allen') return $str;
	$str = piconv($str, CHARSET, $_G['cn_charset']);
	return preg_replace(array('/\%3A/i', '/\%2F/i' , '/\%3F/i', '/\%3D/i', '/\%26/i'), array(':', '/', '?', '=', '&'), rawurlencode($str) );
}

function url_unescape($str) {
	$str = rawurldecode($str);
  	preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U",$str,$r);
  	$ar = $r[0];
  	foreach($ar as $k=>$v) {
		if(substr($v,0,2) == "%u"){
	  		$ar[$k] = iconv("UCS-2","GB2312",pack("H4",substr($v,-4)));
		}elseif(substr($v,0,3) == "&#x"){
	  		$ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,3,-1)));
		}elseif(substr($v,0,2) == "&#") {
	  		$ar[$k] = iconv("UCS-2","UTF-8",pack("n",substr($v,2,-1)));
		}
  	}
  return join("",$ar);
}

function get_contents($url, $args = array()){
	global $_G;
	if(!$url) return;
	$pick_config = get_pick_set();
	$proxy_config = $pick_config['proxy_config'];
	if($proxy_config){
		list($proxy_host, $proxy_port, $proxy_user, $proxy_pass) = format_wrap($proxy_config); 
	}
	pload('F:spider');
	$args['cookie'] = $args['cookie'] ? $args['cookie'] : format_cookie($_GET['login_cookie']);
	extract($args);
	$time_out = $time_out ? $time_out : ($pick_config['time_out'] ? $pick_config['time_out'] : 15);
	$max_redirs = $max_redirs ? $max_redirs : $pick_config['max_redirs'];
	$max_redirs = $max_redirs ? $max_redirs : 3;
	$cache = !empty($cache) ? $cache : $pick_config['cache_time'] * 60;
	$data_arr = load_cache($url);
	$content = $data_arr['content'];
	if($cache > 0 && $content && $data_arr['cookie'] == $args['cookie']){
		//$content = media_format($content, $url);
		return $content;
	}else{
		$time_out = $time_out ? $time_out : $pick_config['time_out'];
		if(!function_exists('fsockopen') && !function_exists('pfsockopen') && !function_exists('file_get_contents')){
			return FALSE;
		}
		if(!function_exists('fsockopen') && !function_exists('pfsockopen')){
			if(!function_exists('file_get_contents')) return -1;
			$content = file_get_contents($url);
			$content = str_iconv($content);
		}else{
			require_once(PICK_DIR.'/lib/Snoopy.class.php');
			$snoopy = new Snoopy;    
			if(!empty($proxy_host)){
				$snoopy->proxy_host = $proxy_host;
				$snoopy->proxy_port = $proxy_port;
				$snoopy->proxy_user = $proxy_user;
				$snoopy->proxy_pass = $proxy_pass;
			}
			$snoopy->maxredirs = $max_redirs;   
			$snoopy->expandlinks = TRUE;
			$snoopy->offsiteok = TRUE;//是否允许向别的域名重定向
			$snoopy->maxframes = 3;
			$snoopy->agent = !empty($pick_config['user_agent']) ? $pick_config['user_agent'] : $_SERVER['HTTP_USER_AGENT'];//不设置这里，有些网页没法获取
			$snoopy->referer = $url;
			$snoopy->rawheaders["COOKIE"]= $cookie;
			$snoopy->read_timeout = $time_out;
			if(!$snoopy->fetch($url)) return FALSE;
			$header = (array)$snoopy->headers;
			$header = array_map('trim', $header);
			$key = array_search("Content-Encoding: gzip", $header);
			if($header[0] == 'HTTP/1.1 404 Not Found
' || $header[0] == 'HTTP/1.1 500 Internal Server Error') return FALSE;
			if($key) $snoopy->results = gzdecode($snoopy->results);//gzip
			$content = str_iconv($snoopy->results);
		}
		$content = media_format($content, $url);//有些内容没必要这样处理
		if($content) cache_data($url, array('content' => $content, 'cookie' => $args['cookie']), $cache);
		return $content;
	}
}


if (!function_exists('gzdecode')) {      
    function gzdecode ($data) {      
        $flags = ord(substr($data, 3, 1));      
        $headerlen = 10;      
        $extralen = 0;      
        $filenamelen = 0;      
        if ($flags & 4) {      
            $extralen = unpack('v' ,substr($data, 10, 2));      
            $extralen = $extralen[1];      
            $headerlen += 2 + $extralen;      
        }      
        if ($flags & 8) // Filename      
            $headerlen = strpos($data, chr(0), $headerlen) + 1;      
        if ($flags & 16) // Comment      
            $headerlen = strpos($data, chr(0), $headerlen) + 1;      
        if ($flags & 2) // CRC at end of file      
            $headerlen += 2;      
        $unpacked = @gzinflate(substr($data, $headerlen));      
        if ($unpacked === FALSE)      
              $unpacked = $data;      
        return $unpacked;      
     }      
} 

//将内容中的附件格式化

function dz_attach_format($url, $message){
	if(!$message) return;
	$base_url  = get_base_url($message);
	$url = $base_url ? $base_url : $url;
	$attach_arr = array();
	preg_match_all('/<\s*ignore_js_op\s*>(.*?)<\/\s*ignore_js_op\s*>/is', $message, $block_arr, PREG_SET_ORDER);//DZ2.0和DZ2.5的附件
	foreach((array)$block_arr as $k => $v){
		preg_match_all('/<img\s+src="static\/image\/filetype\/(.*?)"[\s\S]*?<a href=[\'"](.*?forum\.php\?.*?)[\'"].*?target="_blank"\>(.*?)<\/a>/is', $v[1], $t_arr, PREG_SET_ORDER);
		if($t_arr){
			$t_arr[0][0] = $v[0];
			$attach_arr[] = $t_arr[0];
		}
	}
	
	foreach((array)$attach_arr as $k => $v){
		$search_arr[] = $v[0];
		$attach_url = _expandlinks($v[2], $url);
		$replace_arr[] = '<a href="'.$attach_url.'" title="'.trim($v[4]).'">'.trim($v[3]).'</a>';
	}
	$message = str_replace($search_arr, $replace_arr, $message);
	return $message;
}

//这个函数在网页有多个链接的时候特别耗时，所以使用要谨慎。
function attach_format($url, $message){
	global $_G;
	if(!$message) return;
	$base_url  = get_base_url($message);
	$url = $base_url ? $base_url : $url;
	$temp = $attach_arr = $attach_arr2 = array();
	
	$message = dz_attach_format($url, $message);
	
	preg_match_all("/\<a.+href=('|\"|)?(.*)(\\1)(.*)?\>(.*)?<\/a>/isU", $message , $attach_arr2, PREG_SET_ORDER);
	
	$no_ext_arr = array('html', 'htm', 'shtml', 'close()', 'print();');
	foreach((array)$attach_arr2 as $k => $v){
		$search_arr[$k] = $v[0];
		$v[2] = $v[2] ? $v[2] : $v[4];
		$v_info = parse_url($v[2]);
		$ext = addslashes(strtolower(substr(strrchr($v_info['path'], '.'), 1, 10)));
		if(in_array($ext,  $no_ext_arr)) {
			$replace_arr[$k] = $v[0];
			continue;
		}
		//磁力链接不补全
		if($_G['cache']['evn_milu_pick']['no_expandlinks_urls'] && !filter_something($v[2], $_G['cache']['evn_milu_pick']['no_expandlinks_urls'], TRUE)){
			$attach_url = $v[2];
		}else{
			$attach_url = _expandlinks($v[2], $url);
		}
		$replace_arr[$k] = str_replace('href='.$v[1].$v[2].$v[1], 'href='.$v[1].$attach_url.$v[1], $v[0]);
	}
	$message = str_replace($search_arr, $replace_arr, $message);
	return $message;
}


function pick_rand_reply_data(){
	$reply_data = $old_data = @file_get_contents(PICK_DIR.'/data/reply.dat');
	if(GBK) $reply_data = str_iconv($reply_data);
	$reply_data_arr = explode(WRAP, $reply_data);
	if(!$reply_data_arr){
		$reply_data_arr = explode(WRAP, $old_data);
	}
	return $reply_data_arr;
}


function check_web_type(){
	global $_G;
	pload('F:spider');
	ob_clean();
	ob_end_flush();
	$url = format_url($_GET['url']);
	$list_ID = trim(format_url($_GET['list_ID']));
	$data = str_iconv(get_contents($url, array('cache' => -1)));//不应该放入cookie才能测试到
	if(!$list_ID) exit();
	if(!filter_something($data, $list_ID) && $data && $url) echo 1;
	define(FOOTERDISABLED, false);
	exit();
}

function get_share_serach($data_type){
	global $_G,$head_url;

	$info['orderby_arr'] = array(
		'default' => milu_lang('default_sort'),
		'dateline' => milu_lang('upload_dateline'),
		'download' => milu_lang('download_count'),
	) ;
	$info['ordersc_arr'] = array(
		'desc' => milu_lang('sort_desc'),
		'asc' => milu_lang('sort_asc'),
	) ;
	$info['perpage_arr'] = array(
		'25' => milu_lang('per_page_show', array('n' => 25)),
		'50' => milu_lang('per_page_show', array('n' => 50)),
		'100' => milu_lang('per_page_show', array('n' => 100)),
	) ;
	$info['evn_msg'] = check_env();
	if(!submitcheck('submit')) {
	}else{
		$info = array_merge($_GET['set'], $info);
	}
	$args = $_GET['set'];
	$args['s'] = $args['s'] ? $args['s'] : $_GET['s'];
	$args['picker_author'] = $args['picker_author'] ? $args['picker_author'] : $_GET['picker_author'];
	$args['orderby'] = $args['orderby'] ? $args['orderby'] : $_GET['orderby'];
	$args['orderby'] = $args['orderby'] ? $args['orderby'] : 'default';
	$args['ordersc'] = $args['ordersc'] ? $args['ordersc'] : $_GET['ordersc'];
	$args['ordersc'] = $args['ordersc'] ? $args['ordersc'] : 'desc';
	$args['perpage'] = $args['perpage'] ? $args['perpage'] : $_GET['perpage'];
	$args['perpage'] = $args['perpage'] ? $args['perpage'] : '25';
	$url_args = '';
	foreach((array)$args as $k => $v){
		if($k == 'perpage') continue;
		$url_args .= '&'.$k.'='.$v;
	}
	$args['page'] = $_GET['page'] ? intval($_GET['page']) : 1;
	$args['mpurl'] = $head_url.$_GET['myac'].$url_args;
	$rpcClient = rpcClient();
	$host_info = get_client_info();
	$args['domain'] = $host_info['domain'];
	$args['dxc_version'] = $host_info['dxc_version'];
	$data = $rpcClient->get_list_data($data_type, $args);
	if($data == 'rpclimit') exit(milu_lang('vip_rpc_limit'));
	if(is_object($data)){
		if($data->Message || $data->Number == 0) {
			$info['evn_msg'] = milu_lang('phprpc_error', array('msg' => $data->Message));
			return $info;
		}	
	}
	$data = serialize_iconv($data);
	$info['rs'] = $data['rs'];
	$info['count'] = $data['count'];
	$info['multipage'] = $data['multipage'];
	if($args['s']){
		$info['show_result'] = milu_lang('search_num', array('n' => $info['count'] ? $info['count'] : 0));
	}
	return $info;
}

function get_path_hash($url){
	$url_temp = preg_replace('/\d+/', '', $url);
	$arr_temp = parse_url($url_temp);
	if(!strexists($arr_temp['path'], '.')){
		$explode_arr = explode('/',$arr_temp['path']);
		if($explode_arr > 1){
			array_pop ($explode_arr);
			$arr_temp['path'] = implode('/',$explode_arr);
		}
	}
	$path_hash = md5($arr_temp['path']);
	return $path_hash;
}

//本地搜索规则 $get_type 1单贴规则 2 内置规则（包括列表和详细页） 4，只搜索列表页  3学习到的规则
function match_rules($url, $content, $get_type, $show = 1){
	global $_G;
	$pick_config = $_G['cache']['evn_milu_pick']['pick_config'];
	$index_localtion_cache_time = $pick_config['index_localtion_cache_time'];
	pload('F:copyright');
	$host_info = GetHostInfo($url);
	$domain = $host_info['host'];
	$domain_hash = md5($domain);
	$pash_hash = get_path_hash($url);
	$content = clear_ad_html($content);
	$content = preg_replace("'([\r\n])[\s]+'", "", $content);//去掉换行速度更快
	$content = daddslashes($content);
	if(!$content) return FALSE;
	$cache_time = $pick_config['index_cache_time'];
	
	if($get_type == 1){//单贴规则
		$locate_sql = "LOCATE(detail_ID,'$content')";
		$table_name = 'strayer_fastpick';
		$id_name = 'id';
	}else if($get_type == 2 || $get_type == '4' || $get_type == '5'){//内置规则 4是单搜索列表页匹配 （后来加一个 5 只搜索详细页）
		if($get_type == 4){
			$locate_sql = " list_ID <>'' AND LOCATE(list_ID,'$content')";
		}else if($get_type == 2){
			$locate_sql = "(( list_ID <>'' AND LOCATE(list_ID,'$content')) OR ( detail_ID <>'' AND LOCATE(detail_ID,'$content') ))";
		}else if($get_type == 5){
			$locate_sql = " detail_ID <>'' AND LOCATE(detail_ID,'$content') ";
		}
		$table_name = 'strayer_rules';
		$id_name = 'rid';
	}else if($get_type == 3){//学习到的规则
		$locate_sql =  "LOCATE(detail_ID,'$content')";
		$table_name = 'strayer_evo';
		$id_name = 'id';
	}
	$over_dateline = $_G['timestamp'] - $index_localtion_cache_time;
	$base_sql = "SELECT COUNT(*) FROM ".DB::table('strayer_searchindex')." WHERE  domain_hash='".$domain_hash."' AND path_hash='".$path_hash."'";
	if($get_type != 3){//内置规则和单贴采集规则需检测，学习规则则不必
		$check = DB::result(DB::query($base_sql." AND type='".$get_type."4' AND dateline > $over_dateline AND rid=0"), 0);
		if($check) return 'no';
	}
	$count = DB::result(DB::query($base_sql." AND type='".$get_type."4' AND rid>0"), 0);
	if($count){
		$query = DB::query("SELECT * FROM ".DB::table('strayer_searchindex')."  WHERE domain_hash='".$domain_hash."' AND path_hash='".$path_hash."' AND rid>0");	
		while(($v = DB::fetch($query))) {
			$index_id_arr[] = $v['rid'];
			$index_list[] = $v;
		}
	}
	if(!$count){//没有索引
		if($get_type != 3){//学习规则库没有索引的情况下，不需要寻找
			$info = DB::fetch_first("SELECT * FROM ".DB::table($table_name)." WHERE $locate_sql ");
			if(!$info) {
				add_search_index($domain_hash, $path_hash, $get_type.'4', 0);
				return 'no';
			}	
			$data_id = $info[$id_name];
			add_search_index($domain_hash, $path_hash, $get_type.'4', $data_id);
		}else{
			return 'no';
		}
		
	}else{//有索引
		$index_id_arr = array_filter($index_id_arr);
		$info = DB::fetch_first("SELECT * FROM ".DB::table($table_name)." WHERE $id_name IN (".dimplode($index_id_arr).") AND  $locate_sql ");
		if(!$info) {
			add_search_index($domain_hash, $path_hash, $get_type.'4', 0);
			return 'no';
		}
	}
	if($show == 1){
		return json_encode($info);
	}else{
		return $info;
	}
	
}


function add_search_index($domain_hash, $path_hash, $get_type, $data_id){
	global $_G;
	if($get_type == 3 && !$data_id) return;//学习规则不需要
	$check = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_searchindex')." WHERE  domain_hash='$domain_hash' AND path_hash='$path_hash' AND type='$get_type' AND rid='$data_id'"), 0);
	if($check) return;

	$setarr = array('domain_hash' => $domain_hash, 'path_hash' => $path_hash, 'type' => $get_type, 'rid' => $data_id, 'dateline' => $_G['timestamp']);//1是单帖 2是内置规则 3学习到的规则
		
	$setarr	= paddslashes($setarr);
	DB::insert('strayer_searchindex', $setarr, TRUE);//添加索引
}

//转换 <img file="http://bbs.942dn.com/static/image/common/back.gif" onload="thumbImg(this)" alt="" />
function img_htmlbbcode($text, $url = ''){
	$pregfind = array(
		'/<img[^>]*file="([^>]+)"[^>]*>/eiU',
		'/<img[^>]*picsrc="([^>]+)"[^>]*>/eiU',
	);
	$pregreplace = array(
		"img_tag('\\1', '".$url."')",
		"img_tag('\\1', '".$url."')",
	);
	return preg_replace($pregfind, $pregreplace, $text);
}



function img_tag($attributes, $page_url) {
	global $_G;
	$evo_img_no = $_G['cache']['evn_milu_pick']['evo_img_no'];
	$attributes = dstripslashes($attributes);
	if(!preg_match("/^http:\/\//i", $file)) {
		$no_remote = 0;
		if(!filter_something($attributes, $evo_img_no)){//存在
			$no_remote = 1;
		}
		$no_remote = $no_remote == 1 && file_exists(DISCUZ_ROOT.'/'.$attributes) ? 1 : 0;//看看本地是否有这个文件
		$file = $no_remote == 1 ? $attributes : _expandlinks($attributes, $page_url);
	}
	return $file ? ($width && $height ? '[img='.$width.','.$height.']'.$file.'[/img]' : '[img]'.$file.'[/img]') : '';
}




//转换某些特殊字符
function format_html($str){
	if(!$str) return;
	$format_str = milu_lang('format_str');
	$format_arr = explode('@', $format_str);
	foreach((array)$format_arr as $k => $v){
		$v_arr = explode('|', $v);
		$strfind[] = $v_arr[0];
		$strreplace[] = $v_arr[1];
	}
	$str = str_replace($strfind, $strreplace, $str);
	$str = str_replace('&nbsp;', ' ', $str);
	if(function_exists('mb_convert_encoding')){
		foreach(get_html_translation_table(HTML_ENTITIES) as $k=>$v) {
			$str = str_replace($v, mb_convert_encoding($v, CHARSET, "HTML-ENTITIES"), $str);
		}
	}
	return $str;
}

function content_html_ubb($content, $page_url, $is_htmlon = 0){
	if(DISCUZ_VERSION == 'X2.5' || DISCUZ_VERSION == 'X2') $is_htmlon = 0;//X3以上版本才有这个功能
	pload('F:article');
	$content = img_htmlbbcode($content, $page_url);
	$content = media_htmlbbcode($content, $page_url, 'bbs', $is_htmlon);
	$content = audio_htmlbbcode($content, $page_url, 'bbs', $is_htmlon);
	if($is_htmlon == 1) return $content;
	$content = pick_html2bbcode($content);
	$content = htmlspecialchars_decode($content, ENT_QUOTES);
	$content = format_html($content);
	$content = unhtmlentities($content);
	$content = pick_parseed2k($content);//对种子地址(ed2k://)的特殊处理
	return $content;
}

function pick_parseed2k($message){
	if(strpos($message, 'ed2k://') === FALSE) return $message;
	preg_match_all('/\[url=ed2k:\/\/(.*?)\](.*?)\[\/url\]/is', $message, $block_arr, PREG_SET_ORDER);//DZ2.0和DZ2.5的附件
	$search_arr = $replace_arr = array();
	foreach((array)$block_arr as $k => $v){
		$search_arr[] = $v[0];
		$replace_arr[] = '[b]ed2k://'.$v[1].'[/b]';
	}
	if($search_arr[0]){
		$message = str_replace($search_arr, $replace_arr, $message);
	}
	return $message;
}




//视频标签的过滤
function media_htmlbbcode($text, $url= '', $type = 'bbs', $pick_htmlon = 0){
	global $_G;
	if(!$text) return;
	$pregfind = array(
		"/<embed([^>]*src[^>]*)>/eiU",
		"/<embed([^>]*src[^>]*)*\"><\/embed>/eiU",
	);
	
	$pregreplace = array(
		"mediatag('\\1', '".$url."', ".$type.")",
		"mediatag('\\1', '".$url."', ".$type.")",
	);
	return preg_replace($pregfind, $pregreplace, $text);
}


function audio_htmlbbcode($text, $url = '', $type = 'bbs', $pick_htmlon = 0){
	global $_G;
	if($pick_htmlon == 1){//开启html
		preg_match_all('/<param\sname="(url|src)"\svalue="(.*?)"/is', stripslashes($text), $matches, PREG_SET_ORDER);
		if(is_array($matches[0])) {
			$audio_url = _expandlinks($matches[0][2], $url);
		}
		$replace_url = str_replace($matches[0][1], $audio_url, $matches[0][0]);	
		$text = str_replace($matches[0][0], $replace_url, $text);
		return $text;
	}
	preg_match_all("/\<object(.*)?>(.*)?<\/object>/i", $text , $attach_arr, PREG_SET_ORDER);
	if(!$attach_arr) return $text;
	$search_arr = $replace_arr = array();
	foreach($attach_arr as $k => $v){
		if(strexists($v[0], '[/flash]')) continue;
		$search_arr[] = $v[0];
		$replace_arr[] = get_audio_param($v[1], $url, $type);
	}
	$text = str_replace($search_arr, $replace_arr, $text);
	return $text;
}

function get_audio_param($attributes, $page_url, $type = 'bbs') {
	global $_G;
	if(!$attributes) return;
	$attributes = dstripslashes($attributes);
	$value = array('width' => '', 'height' => '');
	preg_match_all('/(width|height)=(["\'])?([^\'" ]*)(?(2)\2)/i', dstripslashes($attributes), $matches);
	if(is_array($matches[1])) {
		foreach($matches[1] as $key => $attribute) {
			$value[strtolower($attribute)] = $matches[3][$key];
		}
	}
	$value['width'] = $value['width'] ? $value['width'] : 500;
	$value['height'] = $value['height'] ? $value['height'] : 375;
	preg_match_all('/<param\sname="(url|src)"\svalue="(.*?)"/is', stripslashes($attributes), $matches, PREG_SET_ORDER);
	if(is_array($matches[0])) {
		$audio_url = _expandlinks($matches[0][2], $page_url);
	}

	$ext = strtolower(substr(strrchr($audio_url, '.'), 1, 10));
	$x = in_array($ext, array('wmv', 'avi', 'rmvb', 'mov', 'swf', 'flv')) ? $ext : 'wmv';
	return $type == 'bbs' ? '[media='.$x.','.$value['width'].','.$value['height'].']'.$audio_url.'[/media]' : '[flash]'.$audio_url.'[/flash]';

}


function get_trun_data($turn_type = '', $turn_id = ''){
	$turn_id = $turn_id ? $turn_id : intval($_GET['turn_id']);
	$turn_type = $turn_type ? $turn_type : $_GET['turn_type'];
	if(!$turn_type && !$turn_id) return FALSE;
	if($turn_type == 'evo' || $turn_type == 'fastpick'){
		if(!function_exists('fastpick_info'))  pload('F:fastpick');
		return fastpick_info($turn_id, '*', $turn_type);
	}else if($turn_type == 'system'){
		if(!function_exists('get_rules_info'))  pload('F:rules');
		return get_rules_info($turn_id);
	}else{
		if(!function_exists('get_pick_info'))  pload('F:pick');
		$info = get_pick_info($turn_id);
		if($info['rules_type'] != 1) return $info;
		return get_trun_data('system', $info['rid']);//内置规则
	}
}

	

if(!function_exists('dunserialize')){//这个函数是DZ2.5新加入的
	function dunserialize($data) {
		if(($ret = unserialize($data)) === false) {
			$ret = unserialize(stripslashes($data));
		}
		return $ret;
	}
}

function get_htmldom_obj($str){
	if(!$str) return $str;
	require_once(PICK_DIR.'/lib/simple_html_dom.php');
	$html = str_get_html($str, true, true, DEFAULT_TARGET_CHARSET, FALSE);
	if(!$html) return false;
	return $html;
}

function get_snoopy_obj($args = array()){
	extract((array)$args);
	$pick_config = get_pick_set();
	$proxy_config = $pick_config['proxy_config'];
	if($proxy_config){
		list($proxy_host, $proxy_port, $proxy_user, $proxy_pass) = format_wrap($proxy_config); 
	}
	require_once(PICK_DIR.'/lib/Snoopy.class.php');
	$max_redirs = $max_redirs ? $max_redirs : 3;
	$time_out = $time_out ? $time_out : ($pick_config['time_out'] ? $pick_config['time_out'] : 10);
	$snoopy = new Snoopy;  
	if(!empty($proxy_host)){
		$snoopy->proxy_host = $proxy_host;
		$snoopy->proxy_port = $proxy_port;
		$snoopy->proxy_user = $proxy_user;
		$snoopy->proxy_pass = $proxy_pass;
	}
	$snoopy->maxredirs = $max_redirs;   
	$snoopy->expandlinks = TRUE;
	$snoopy->offsiteok = TRUE;//是否允许向别的域名重定向
	$snoopy->maxframes = 3;
	$snoopy->agent = !empty($pick_config['user_agent']) ? $pick_config['user_agent'] : $_SERVER['HTTP_USER_AGENT'];//不设置这里，有些网页没法获取
	$snoopy->referer = $referer ? $referer : $url;
	$snoopy->rawheaders["COOKIE"]= $cookie;
	$snoopy->read_timeout = $time_out;
	return $snoopy;
}

function content_test_cache($url, $cookie, $cache_key){
	$data_arr = load_cache($cache_key);
	$pick_config = get_pick_set();
	$cache_time = $pick_config['cache_time'];
	if($cache_time > 0 && $data_arr['content'] && $data_arr['cookie'] == $cookie){
		return $data_arr['content'];
	}else{
		$content = get_contents($url, array('cache' => -1, 'cookie' => $cookie));
		if($content) cache_data($cache_key, array('content' => $content, 'cookie' => $cookie), $cache_time);
		return $content;
	}
}


//清除缓存
function clear_pick_cache($del = 0){
	pload('C:cache');
	$pick_clear_cache = pick_common_get('', 'pick_clear_cache');
	if( (TIMESTAMP - $pick_clear_cache) < 3600*24 && $del == 0) return;
	$cache_info = IO::info(PICK_CACHE);
	$pick_set = get_pick_set();
	$max_cache_size = !empty($pick_set['max_cache_size']) ? $pick_set['max_cache_size'] : 40;
	if($cache_info['size'] > $max_cache_size*1024*1024){
		IO::rm(PICK_CACHE);
	}
	pick_common_set(array('pick_clear_cache' => TIMESTAMP));
}	

//定期清除索引
function clear_search_index($del = 0){
	global $_G;
	$clear_search_index = pick_common_get('', 'clear_search_index');
	$time = TIMESTAMP - 3600*24*7;//一周前
	if( (TIMESTAMP - $clear_search_index) < 3600*24*7 && $del == 0) return;
	DB::query('DELETE FROM '.DB::table('strayer_searchindex')." WHERE dateline<'$time' AND rid=0");
	pick_common_set(array('clear_search_index' => TIMESTAMP));
}

// $type  1是单帖 2是内置规则 3学习到的规则
function del_search_index($type = 0, $rid = 0){
	$sql = $type ? " AND (type='$type' OR type='".$type."4') " : '';
	return DB::query('DELETE FROM '.DB::table('strayer_searchindex')." WHERE rid='$rid' $sql ");
}

//定期清除一周之前的日志
function clear_log($del = 0){
	pload('C:cache');
	$clear_log = pick_common_get('', 'clear_log');
	if( (TIMESTAMP - $clear_log) < 3600*24*2 && $del == 0) return;
	$log_info = IO::info(PICK_DIR.'/data/log');
	$file_list = $log_info['ls'];
	if(!$file_list) return;
	foreach($file_list as $k => $v){
		if(TIMESTAMP - $v['change'] > 3600*24*3) @unlink($v['location']);
	}
	pick_common_set(array('clear_log' => TIMESTAMP));
}


function jammer_replace($str, $flag = 0){
	if(!$str ) return $str;
	//return $str;//平常关闭掉。过滤干扰码的时候开启
	return $flag !=1  ? preg_replace(array('/{/s', '/}/s', '/@@/s', '/\"s/s', '/\"\\\"/s'), array('MM56NN','MM78NN', 'FF45DD', '" s', 'MM34NN'), $str) : preg_replace(array('/MM56NN/s', '/MM78NN/s', '/FF45DD/s', '/" s/s', '/MM34NN/s'), array('{','}','@@', '"s', '"\"'), $str);//符号干扰正则
}


function pick_free_access_denied(){
	if(VIP) return;
	cpmsg_error(milu_lang('free_cannot_use'));
}

function get_domain($url){
	if(empty($url)) return;
	$d = RootDomain::instace();
	$d->setUrl($url);
	return $d->getDomain();
}

function pick_ajax_decode($str){
	return json_decode(base64_decode($str));
}

?>