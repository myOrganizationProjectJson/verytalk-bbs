<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
function create_lang_file(){
	global $_G;
	$file_name = DISCUZ_ROOT.'/data/plugindata/milu_pick.lang.php';
	$xml_ext = 'discuz_plugin_milu_pick_SC_GBK.xml';
	$descdir = DISCUZ_ROOT.'source/plugin/milu_pick/';
	$xml_file = $descdir.'/'.$xml_ext;
	require_once libfile('class/xml');
	$data = file_get_contents($xml_file);
	$data_arr = xml2array($data);
	$lang_arr = $data_arr['Data']['language'];
	$str = "<?php \n";
	foreach($lang_arr as $k => $v){
		$str .= "\$".$k."['milu_pick'] = array(\n";
		foreach($v as $k2 => $v2){
			$v2 = str_replace("'", "[", $v2);
			$str .= "	'$k2' => '$v2',\n";
		}
		$str .= ");\n";
	}
	$str .= "?> \n";
	file_put_contents($file_name, $str);
}

function get_pick_info($pid='', $field = '*'){
	global $_G;
	$pid = $pid ? $pid : $_GET['pid'];
	$pid = intval($pid);
	$data = DB::fetch_first("SELECT $field FROM ".DB::table('strayer_picker')." WHERE pid='$pid'");
	return $data;
}



function rules_get_threadtypes(){
	pload('F:spider');
	$url = format_url($_GET['url']);
	$login_cookie = format_cookie($_GET['login_cookie']);
	$content = get_contents($url, array('cookie' => $login_cookie));
	$get_type = intval($_GET['get_type']);
	$get_rules = format_url($_GET['get_rules']);
	$output = '';
	if($get_type == 1){
		$html = get_htmldom_obj($content);
		$output = dom_get_str($html, $get_rules);
	}else{
		$output = str_get_str($content, $get_rules, 'data');
	}
	if(!$output) $output = milu_lang('no_get_data').milu_lang('body');
	show_pick_window(milu_lang('get_content_test'), $output, array('w' => 540,'h' => '430','f' => 1));
}

function pick_category_list($select = FALSE){
	global $_G;
	$query = DB::query("SELECT * FROM ".DB::table('strayer_category')." ORDER BY displayorder DESC,cid DESC");
	while($rs = DB::fetch($query)) {
		if($select == TRUE){
			$arr[$rs['cid']] = $rs['name'];
		}else{
			$arr[] = $rs;
		}
	}
	if(!$arr){//如果没有分类
		$setarr = array(
			'displayorder' => 0,
			'name' => milu_lang('default_class'),
		);
		$insert_id = DB::insert('strayer_category', $setarr, TRUE);
		DB::query('UPDATE '.DB::table('strayer_picker')." SET pick_cid='$insert_id'");
	}
	return $arr;
}

function move_picker($pid, $to_cid){
	if(empty($pid) || empty($to_cid)) return;
	return DB::query('UPDATE '.DB::table('strayer_picker')." SET pick_cid='$to_cid' WHERE pid='$pid'");
}

//在线采集器
function pick_online(){
	global $_G,$head_url,$header_config;
	$info = get_share_serach('pick');
	$info['header'] = pick_header_output($header_config, $head_url);
	return $info;
}

function share_picker_data(){
	global $_G;
	require_once libfile('function/misc');
	$client_info = get_client_info();
	if(!$client_info) return milu_lang('share_no_allow');
	$pid = intval($_GET['pid']);
	if(!$pid) exit('error');
	$picker_data = get_pick_info($pid);
	$picker_data['forum_threadtypes'] = get_therad_sort_data($picker_data['forum_threadtypes'], $picker_data['forum_threadtype_id']);//加入分类信息
	if(!$picker_data['picker_hash']){
		$setarr['pick_hash'] = $picker_data['picker_hash'] = create_hash();
		DB::update('strayer_picker', $picker_data, array('pid' => $picker_data['pid']));
	}
	
	$picker_data['picker_desc'] = format_url($_GET['picker_desc']);
	$picker_data['name'] = format_url($_GET['pick_name']);
	if(!$picker_data) exit('error');
	if($picker_data['rules_hash']){
		pload('F:rules');
		$data['rules'] = get_rules_info($picker_data['rules_hash']);
		$data['rules']['domain'] = $domain;
	}
	$data['pick'] = $picker_data;
	$rpcClient = rpcClient();
	unset($picker_data['pid'], $data['rules']['login_cookie'], $data['pick']['login_cookie']);
	$re = $rpcClient->upload_data('pick', $data, $client_info);

	if(is_object($re) || $data->Number == 0){
		if($re->Message) return  milu_lang('phprpc_error', array('msg' => $re->Message));
		$re = (array)$re;
	}
	$re = is_array($re) ? $re[0] : $re;
	if($re < 0){
		return $re;
	}else{
		return 'ok';
	}
}


//下载采集数据
function download_picker_data(){
	$pid  = intval($_GET['pid']);
	$cid = intval($_GET['cid']);
	$rpcClient = rpcClient();
	$client_info = get_client_info();
	$re = $rpcClient->download_data('pick', $pid, $client_info);
	if(is_object($re) || $re->Number == 0){
		if($re->Message) return  milu_lang('phprpc_error', array('msg' => $re->Message));
		$re = (array)$re;
	}
	$re = serialize_iconv($re);
	return pick_import_data($re, $cid);
}


//导入采集器
function pick_import(){
	global $_G,$head_url,$header_config;
	if(!submitcheck('submit')) {
		num_limit('strayer_picker', 35, 'p_num_limit');
		$info['header'] = pick_header_output($header_config, $head_url);
		return $info;
	}else{
		$rules_code = $_GET['rules_code'];
		$pick_cid = $_GET['pick_cid'];
		if($rules_code){
			$data = $rules_code;
		}else{
			$file_name =  str_iconv($_FILES['rules_file']['tmp_name']);
			$fp = fopen($file_name, 'r');
			$data = fread($fp,$_FILES['rules_file']['size']);
		}
		
		$arr = pimportfile($data);
		if($arr['rules_hash']) cpmsg_error(milu_lang('import_error', array('url' => PICK_GO)));
		if(!$arr['pick']['pid'] ) cpmsg_error(milu_lang('rules_error_data', array('url' => PICK_GO)));
		if(!array_key_exists('is_setting_article_page', $arr['pick'])){
			$arr['pick']['is_setting_article_page'] = !empty($arr['pick']['content_page_rules']) ? 1 : 2;//是否设置分页
		}
		pick_import_data($arr, $pick_cid);
		cpmsg(milu_lang('import_finsh'), PICK_GO."picker_manage", 'succeed');
	}
}

function pick_import_data($arr, $pick_cid){
	global $_G;
	$arr['pick'] = get_table_field_name('strayer_picker', $arr['pick']);
	$del_data = array('pid','public_type','public_class', 'run_times', 'lastrun', 'nextrun'); //要去掉的字段名称
	foreach($del_data as $v){
		unset($arr['pick'][$v]);
	}
	$arr['pick'] = $arr['pick'];
	$arr['pick']['pick_cid'] = $pick_cid;
	$arr['pick']['displayorder'] = 0;//设为0，导入之后就排在最上面了 
	$arr['pick']['dateline'] = $_G['timestamp'];
	$arr['pick']['is_pick_download_on'] = empty($arr['pick']['is_pick_download_on']) ? 1 : $arr['pick']['is_pick_download_on'];
	$arr['pick']['is_auto_public'] = 0;//自动发布设为否
	$arr['pick']['article_num'] = $arr['pick']['visit_url_num'] = $arr['pick']['article_import_num'] = 0;
	if(empty($arr['pick']['is_setting_article_page'])){//分页
		$arr['pick']['is_setting_article_page'] = !empty($arr['pick']['content_page_rules']) ? 1 : 2;
	} 
	if(empty($arr['pick']['is_get_reply'])){//回复
		$arr['pick']['is_get_reply'] = !empty($arr['pick']['reply_rules']) || $arr['pick']['reply_is_extend'] == 1 ? 1 : 2;
	} 
	$check_picker = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_picker')." WHERE picker_hash='".$arr['pick']['picker_hash']."'"), 0);
	if($check_picker){//不管是否存在，都添加，唯一不同的是，如果存在的话，picker_hash要重新生成
		$arr['pick']['picker_hash'] = create_hash();
	}
	$insert_id = DB::insert('strayer_picker', paddslashes($arr['pick']), TRUE);//导入采集器
	if($arr['rules']){
		$rules_arr = $arr['rules'];	
		$check = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_rules')." WHERE rules_hash='$rules_arr[rules_hash]'"), 0);
		if(!array_key_exists('is_setting_article_page', $rules_arr)){
			$rules_arr['is_setting_article_page'] = !empty($rules_arr['content_page_rules']) ? 1 : 2;//是否设置分页
		}
		
		if(empty($rules_arr['is_setting_article_page'])){//分页
			$rules_arr['is_setting_article_page'] = !empty($rules_arr['content_page_rules']) ? 1 : 2;
		} 
		if(empty($arr['is_get_reply'])){//回复
			$rules_arr['is_get_reply'] = !empty($rules_arr['reply_rules']) || $rules_arr['reply_is_extend'] == 1 ? 1 : 2;
		} 
		
		
		$rules_arr = get_table_field_name('strayer_rules', $rules_arr);
		unset($rules_arr['rid']);//去掉主键
		$rules_arr = paddslashes($rules_arr);
		if($check){//如果存在这个
			$rules_hash = $rules_arr['rules_hash'];
			unset($rules_arr['rules_hash']);
			DB::update('strayer_rules', $rules_arr, array('rules_hash' => $rules_hash));
		}else{
			DB::insert('strayer_rules', $rules_arr, TRUE);
		}
	}
	return $insert_id;
}

function show_pick_format($info){	
	$info['rules_var'] = dunserialize($info['rules_var']);
	$info['many_page_list'] = dunserialize($info['many_page_list']);
	$info['title_filter_rules'] = dunserialize($info['title_filter_rules']);
	$info['content_filter_rules'] = dunserialize($info['content_filter_rules']);
	$info['reply_filter_rules'] = dunserialize($info['reply_filter_rules']);
	$info['content_filter_html'] = dunserialize($info['content_filter_html']);
	$info['reply_filter_html'] = dunserialize($info['reply_filter_html']);
	$info['public_class'] = dunserialize($info['public_class']);
	$info['public_uid_group'] = dunserialize($info['public_uid_group']);
	$info['reply_uid_group'] = dunserialize($info['reply_uid_group']);
	$info['forum_threadtypes'] = dunserialize($info['forum_threadtypes']);
	if($info['forum_threadtype_id']){
		$info['forum_threadtype_id'] = get_local_sortid($info['forum_threadtypes'], $info['forum_threadtype_id']);
	}else{
		$info['forum_threadtypes'] = '';
	}
	if(!$info['jump_num'])  $info['jump_num'] = 45;
	if(!$info['time_out']) $info['time_out'] = 5;
	$info = dhtmlspecialchars($info);
	if($info['forum_threadtype_id'] == 0 && $info['forum_threadtypes']) {
		$info['threadtype_select_notice'] = milu_lang('not_same_sort_threadtype_select_notice', array('title' => $info['forum_threadtypes']['threadsort']['name'], 'data_id' => $info['pid'], 'type' => 'picker'));
	}
	$time_pre = '1234321';//这是代表 - 符号
	
	if(strexists($info['public_start_time'], $time_pre)){//含有负号
		$info['public_start_time'] = str_replace($time_pre, '-', $info['public_start_time']);
	}else{
		if($info['public_start_time'] > TIMESTAMP - 20*365*24*3600){
			$info['public_start_time'] = $info['public_start_time'] ? dgmdate($info['public_start_time'], 'Y-m-d H:i') : '';
		}
	}
	if($info['public_end_time'] > TIMESTAMP - 20*365*24*3600){//如果时间不比二十年前的时间戳小，就是具体的时间，反之是小时
		$info['public_end_time'] = $info['public_end_time'] ? dgmdate($info['public_end_time'], 'Y-m-d H:i') : '';
	}
	$info = array_filter($info);

	return $info;
}


//删除采集器
function del_picker($pid){
	if(!$pid) return;
	DB::query('DELETE FROM '.DB::table('strayer_url')." WHERE pid= '$pid'");
	pload('F:article');
	article_batch_del($pid);
	del_pick_log($pid);
	DB::query('DELETE FROM '.DB::table('strayer_picker')." WHERE pid= '$pid'");
}

//某个分类下面的采集器
function category_picker($cid = 0, $get_field = '*'){
	$where = $cid > 0 ? " WHERE pick_cid='$cid'" : '';
	$query = DB::query("SELECT $get_field FROM ".DB::table('strayer_picker').$where);
	while($rs = DB::fetch($query)) {
		$data[] = $rs;
	}
	return $data;
}

//更新运行次数
function update_times($pid){
	if(!$pid) return;
	$pid = intval($pid);
	DB::query('UPDATE  '.DB::table('strayer_picker')." SET run_times=run_times+1 WHERE pid= '$pid'");
}



function create_file($filename, $value){
	
	$filename = $filename.".txt";
	$encoded_filename = urlencode($filename);
	$encoded_filename = str_replace("+", "%20", $encoded_filename);
	
	header('Content-Type: application/octet-stream');
	$ua = $_SERVER["HTTP_USER_AGENT"];
	if (preg_match("/MSIE/", $ua)) {
		header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
	} else if (preg_match("/Firefox/", $ua)) {
		header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
	} else {
		header('Content-Disposition: attachment; filename="' . $filename . '"');
	}
	print $value;
}




function system_get_link_test(){
	global $_G;
	pset_charset();
	pload('F:spider');
	$type = format_url($_REQUEST['type']);
	$is_filter = intval($_REQUEST['is_filter']);
	$is_page_fiter = intval($_REQUEST['is_page_fiter']);
	$url = format_url($_REQUEST['c']);
	$rule = trim(str_iconv(format_url($_REQUEST['b'])));//记得转换中文
	$page_url_replace = format_url($_REQUEST['page_url_replace']);
	$page_url_no_other = format_url($_REQUEST['page_url_no_other']);
	$page_url_contain = format_url($_REQUEST['page_url_contain']);
	$page_url_no_contain = format_url($_REQUEST['page_url_no_contain']);
	$login_cookie = format_cookie($_GET['login_cookie']);
	$dom_type = 0;
	$link_arr = array();
	if($_GET['dom_type'] == 'content_page') {//文章分页
		$dom_type = 1;
	}
	$content = get_contents($url, array('cookie' => $login_cookie));
	if($type == 1){//dom
		if($content != -1){
			$link_arr = dom_page_link($content, array('page_link_rules' => $rule, 'url' => $url), $dom_type);
		}
	}else if($type == 2){//字符
		if($content != -1){
			$link_arr = string_page_link($content, $rule, $url);
		}
	}
	if($dom_type == 1){//文章分页
		if($type == 3){//表达式
			$exp_data_arr = exp_get_pagelink($rule, $url);
			$max = 4;
			for($i = $exp_data_arr['start'];$i < $max;$i++){
				$link_arr[] = str_replace('[page]', $i, $exp_data_arr['url']);
			}
			$link_arr[($max-1)] = 0;
			$link_arr['x'] = str_replace('[page]', 'x', $exp_data_arr['url']);
		}
	}else{
		if($type == 3){//智能
			$link_arr = evo_get_pagelink($content, $url);
		}else if($type == 4){//json
			$link_arr = json_get_pagelink($content, $rule, $url);
		}
	}
	$args = array();
	if(($is_filter == 1 || $is_page_fiter == 1) && $link_arr){
		$args = array(
			'page_url_replace' => $page_url_replace,
			'page_url_no_other' => $page_url_no_other,
			'page_url_contain' => $page_url_contain,
			'page_url_no_contain' => $page_url_no_contain,
			'page_url_no_other' => $page_url_no_other,
		);
	}
	if($content == -1 ) {
		$link_html = milu_lang('unable_pick');
	}else if($content == -2){
		$link_html = milu_lang('get_time_out');
	}else{
		$link_html = pickOutput::windos_show_link($link_arr, '', array(), $args);
	}
	if(intval($_GET['rand'])){
		$rand_arr = array_filter($link_arr);
		$key = rand(0, count($rand_arr) - 1);
		$key = array_rand($rand_arr, 1);
		return $rand_arr[$key] ;
	}
	show_pick_window(milu_lang('get_link', array('count' => count($link_arr))), $link_html, array('w' => 620,'h' => '400','f' => 1));
}



function filter_page_link($now_url, $args){
	extract($args);
	if($page_url_no_other){//要过滤的网址
		$user_no_arr = format_wrap(trim($page_url_no_other));
		foreach($user_no_arr as $k => $v){
			$user_no_arr[$k] = str_replace('&amp;', '&', dhtmlspecialchars(trim($v)));
		}
		if(in_array($now_url, $user_no_arr)) return FALSE;
	}
	if(filter_something($now_url, $page_url_contain)) return FALSE;//必须包含
	if(!filter_something($now_url, $page_url_no_contain, TRUE)) return FALSE;//不包含
	return TRUE;
}

function rules_get_test(){
	$func = format_url($_GET['func']);
	$get_type = intval($_GET['get_type']);
	$get_rules = format_url($_GET['get_rules']);
	$login_cookie = format_cookie($_GET['login_cookie']);
	$url_test = format_url($_GET['url']);
	$data_arr = array();
	$output = '';
	$contents = get_contents($url_test, array('cookie' => $login_cookie));
	if($func == 'pick_cover_rules'){
		pload('F:spider');
		$data_arr = rules_get_cover($url_test, $contents, $get_type, $get_rules);
		$output = pickOutput::img_list_output($data_arr);
	}
	show_pick_window(milu_lang('get_cover_count', array('c' => count($data_arr))), $output, array('w' => 650,'h' => '520','f' => 1));
}

function attach_download_url_test(){
	pload('F:spider');
	$attach_redirect_url_get_type = intval($_GET['attach_redirect_url_get_type']);
	$attach_redirect_url_get_rules = format_url($_GET['attach_redirect_url_get_rules']);
	
	$attach_download_url_get_type = intval($_GET['attach_download_url_get_type']);
	$attach_download_url_get_rules = format_url($_GET['attach_download_url_get_rules']);
	$url = format_url($_GET['url']);
	
	$login_cookie = format_cookie($_GET['login_cookie']);
	$content = get_contents($url, array('cookie' => $login_cookie));
	

	$arr = get_redirect_attach_url(array('attach_redirect_url_get_type' => $attach_redirect_url_get_type, 'attach_redirect_url_get_rules' => $attach_redirect_url_get_rules, 'attach_download_url_get_type' => $attach_download_url_get_type, 'attach_download_url_get_rules' => $attach_download_url_get_rules), $url, $content, $login_cookie);
	$output = '<ul class="show_reply"><li>'.milu_lang('_attach_redirect_url', array('url' => $arr['attach_redirect_url'])).'</li><li>'.milu_lang('_attach_download_url', array('url' => $arr['attach_download_url'])).'</li></ul>';
	
	show_pick_window(milu_lang('test_window_title'), $output, array('w' => 650,'h' => '520','f' => 1));
}


function test_window($get_type, $url_test, $is_fiter, $rules, $replace_rules, $filter_data,$show_type, $login_cookie, $filter_html_arr, $reply_is_extend, $best_answer_get_type = 1, $best_answer_get_rules = '', $best_answer_flag = '', $ask_reward_price_get_type= '', $ask_reward_price_get_rules = '', $charset_type){
	pset_charset($charset_type);
	$url_test = rpc_str($url_test);
	$rules = rpc_str($rules);
	$replace_rules = rpc_str($replace_rules);
	$login_cookie = rpc_str(urlencode($login_cookie));
	$best_answer_get_rules = rpc_str($best_answer_get_rules);
	foreach($filter_data as $k => $v){
		if($v) $filter_data[$k][1] = rpc_str($v[1]);
	}
	$filter_html_arr = sarray_unique($filter_html_arr);//去重
	if($show_type == 'title'){
		$show_name = milu_lang('title');
	}else if($show_type == 'body'){
		$show_name = milu_lang('body');
	}else{
		$show_name = milu_lang('reply');
	}

	$contents = get_contents($url_test, array('cookie' => $login_cookie));
	if($get_type == 1){//dom
		if($show_type == 'reply'){
			$result_data = dom_get_manytext($contents, $rules, 0);
			if($reply_is_extend) unset($result_data[0]);
		}else{
			if($show_type == 'title'){
				$dom_rules['title'] = $rules;
			}else{
				$dom_rules['content'] = $rules;
			}
			$re = dom_single_article($contents, $dom_rules);
			$result_data = $show_type == 'title' ? $re['title'] : $re['content'];
			
			
		}
	}else if($get_type == 2){//字符串
		if($contents != -1){
			if($show_type == 'reply'){
				$rules =  str_replace('[body]', '[reply]', $rules);
				$result_data = str_get_str($contents, $rules, $show_type, -1);
				if($reply_is_extend) unset($result_data[0]);
			}else{
				$result_data = str_get_str($contents, $rules, $show_type, 1);
			}
			
		}
	}else{//智能获取
		if($contents != -1 && $show_type != 'reply'){
			$re = get_single_article($contents, $url_test);
			if($show_type == 'title'){
				$result_data = $re['title'];
			}else{
				$result_data = $re['content'];
			}
		}
	}
	if(is_array($result_data)){
		foreach($result_data as $k => $v){
			$v = attach_format($url_test, $v);
			$c_arr = format_article_imgurl($url_test, $v);
			$result_data[$k] = $c_arr['message'];
		}
	}else{
		$result_data = attach_format($url_test, $result_data);
		$c_arr = format_article_imgurl($url_test, $result_data);
		$result_data = $c_arr['message'];
	}
	
	$best_answer_output = '';
	$best_answer_key = -1;
	$best_answer_notice = '';
	if($show_type == 'reply' && $best_answer_get_rules){//设置问答采集
		if($best_answer_get_type != 3){
			$best_answer = get_best_answer($best_answer_get_type, $best_answer_get_rules, $contents);
			if($best_answer){
				array_unshift($result_data, $best_answer);
				$best_answer_key = 0;
			}else{//获取不到
				$best_answer_notice = '<h1 class="red">'.milu_lang('best_answer_no_get_notice').'</h1>';
			}
		}else{
			if(strexists($contents, $best_answer_flag)){
				$best_answer_key = intval(trim($best_answer_get_rules)) - 1;//因为是从1算起的
			}else{
				$best_answer_notice = '<h1 class="red">'.milu_lang('quetion_no_resovle').'</h1>';
			}
		}
		$answer_reward_price = get_reward_price($ask_reward_price_get_type, $ask_reward_price_get_rules, $contents);
		$reward_price_output = '<span style="font-weight:normal;margin-left:15px;">'.milu_lang('_reward_price', array('p' => $answer_reward_price)).'</span>';
		
	}

	if(!$best_answer){
		if($result_data == -1) {
			echo milu_lang('unable_pick');
			return;
		}else if($result_data == -2){
			echo milu_lang('get_time_out');
			return;
		}
		if(!$result_data) {
			echo(milu_lang('no_get_data').$show_name); 
			return;
		}
	}
		
	$format_args = array(
		'is_fiter' => $is_fiter,
		'show_type' => $show_type,
		'result_data' => $result_data,
		'replace_rules' => $replace_rules,
		'filter_data' => $filter_data,
		'test' => 1,
		'filter_html' => $filter_html_arr,
	);
	
	$result_data = filter_article($format_args);
	if($show_type == 'reply'){
		$body = pickOutput::show_reply_output($result_data, array('best_answer_key' => $best_answer_key, 'reward_price_output' => $reward_price_output));
	}else{
		$body .= $result_data;
	}
	
	echo $best_answer_notice.$body;
}



function show_rules_select($args){
	global $_G;
	$system_rules = $_G['cache']['evn_milu_pick']['system_rules'];
	$no_ajax = $args['no_ajax'];
	if($no_ajax != 1){
		ob_clean();
		ob_end_flush();
	}
	$type = $_GET['type'] ? $_GET['type'] : $args['type'];
	$select_id =  $_GET['select_id'] ? $_GET['select_id'] : $args['select_id'];
	if($select_id){
		$rules_info = DB::fetch_first("SELECT rules_type,rules_hash FROM ".DB::table('strayer_rules')." WHERE rules_hash='$select_id'");
		$type  = $rules_info['rules_type'];
	}
	$html = '<select id="select_rules_type" onchange="rules_type_select(this.value,0)" name="system_rules_type">';
	$html .= '<option value="0">'.milu_lang('select_rules').'</option>';
	foreach($system_rules as $k => $v){
		  $selected = $type == $k ? 'selected="selected"' : '';
          $html .= '<option '.$selected.' value="'.$k.'">'.$v.'</option>';   
	}
	$html .= '</select>';
	echo $html;
	if(!$type) return;
	$query = DB::query("SELECT rules_name,rules_hash FROM ".DB::table('strayer_rules')." WHERE rules_type='$type' ORDER BY rid DESC");
	$i = 0;
	$html = '<select name="set[rules_hash]" id="show_rules_set" onchange="my_show_rules_set(this.value)">';
	while($rs = DB::fetch($query)) {
		$i++;
		$selected = $select_id == $rs['rules_hash'] ? 'selected="selected"' : '';
        $html .= '<option '.$selected.' value="'.$rs['rules_hash'].'">'.$rs['rules_name'].'</option>';
	}
	$html .= '</select>';
	if($i == 0) $html = milu_lang('class_no_rules');
	echo $html;
	if($no_ajax != 1){
		define(FOOTERDISABLED, false);
		exit();
	}
}


function get_user_info_test(){
	pload('F:spider');
	$user_get_type = intval($_GET['user_get_type']);
	$dateline_get_type = intval($_GET['dateline_get_type']);
	$is_get_user_other = intval($_GET['is_get_user_other']);
	$is_use_thread_setting = intval($_GET['is_use_thread_setting']);
	$type = format_url($_GET['type']);
	$user_get_rules = format_url($_GET['user_get_rules']);
	$dateline_get_rules = format_url($_GET['dateline_get_rules']);
	$user_other_rules = format_url($_GET['user_other_rules']);
	$login_cookie = format_cookie($_GET['login_cookie']);
	$theme_url_test = format_cookie($_GET['theme_url_test']);
	$content = get_contents($theme_url_test, array('cookie' => $login_cookie));
	if($user_get_type == 1 || $dateline_get_type == 1){
		$html = get_htmldom_obj($content);
	}

	$is_reply_user = $type == 'thread' ? 0 : 1;
	$post_user_data = get_public_user_data(array('user_other_rules' => $user_other_rules,'dateline_get_type' => $dateline_get_type, 'dateline_get_rules' => $dateline_get_rules, 'user_get_type' => $user_get_type, 'user_get_rules' => $user_get_rules, 'is_use_thread_setting' => $is_use_thread_setting, 'is_reply_user' => $is_reply_user, 'content' => $content), ($is_use_thread_setting ? 1 : 0));

	$other_html = $user_other_rules ? '<th width="55">'.milu_lang('avatar').'</th><th width="15">'.milu_lang('bbs_sign').'</th>' : '';
	$output = '<table class="tb tb2 "><tbody><tr class="header"><th width="15">'.milu_lang('order_num').'</th>'.$other_html.'<th width="80">'.milu_lang('user_name').'</th><th width="120">'.milu_lang('public_time').'('.milu_lang('before_conv').')</th><th width="120">'.milu_lang('public_time').'('.milu_lang('after_conv').')</th></tr>';
	$i = 0;
	$post_data = count($post_user_data['username']) > count($post_user_data['dateline']) ? $post_user_data['username'] : $post_user_data['dateline'];
	foreach($post_data as $k => $v){
		$i++;
		$avatar = $post_user_data['avatar_url'][$k] ? '<img width="48" height="48" src="'.$post_user_data['avatar_url'][$k].'">' : '';
		$sgin  = $post_user_data['sign'][$k];
		$sgin = $sgin ? '<textarea disabled="disabled" rows="3"  cols="20" >'.$sgin.'</textarea>' : '';
		$other_html = $user_other_rules ? '<td class="td25">'.$avatar.'</td><td class="td25">'.$sgin.'</td>' : '';
		$output .= '<tr class="td24"><td class="td25">'.$i.'</td>'.$other_html.'<td class="td23">'.$post_user_data['username'][$k].'</td><td>'.$post_user_data['org_dateline'][$k].'</td><td>'.($post_user_data['dateline'][$k] ? dgmdate($post_user_data['dateline'][$k], 'Y-m-d H:i:s') : '').'</td></tr>';
	}
	$output .= '</tr></tbody></table>';
	if($is_reply_user) $output .= '<p>'.milu_lang('post_user_get_notice').'</p>';
	if(count($post_data) == 0) $output = milu_lang('rules_no_get_data');
	show_pick_window(milu_lang('test_window_title'), $output, array('w' => 645,'h' => '460','f' => 1));	
}



function rules_list_simple($field = '*'){
	$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_rules')), 0);
	if(!$count) return;
	$query = DB::query("SELECT ".$field." FROM ".DB::table('strayer_rules')." ORDER BY rid DESC");	
	while(($v = DB::fetch($query))) {
		$list[] = $v;
	}
	return $list;
}



function show_rules_set($show = 1, $args = array()){
	global $_G;
	pload('F:rules');
	$rules_hash = $_GET['rules_hash'] ? $_GET['rules_hash'] : $args['rules_hash'];
	$info = get_rules_info($rules_hash);
	$url_var = unserialize($info['url_var']);
	if(!$url_var) return;
	foreach($url_var as $k => $v){
		$html .= pickOutput::show_var_html($k, $v, $args['rules_set'][$k]);
	}
	if(!$show) return $html;
	$arr['page_get_type'] = $info['page_get_type'];
	$arr['page_link_rules'] = $info['page_link_rules'];
	$arr['page_url_test'] = $info['page_url_test'];
	$arr['charset_type'] = $info['charset_type'];
	$arr['html'] = $html;
	$arr = js_base64_encode($arr);
	echo json_encode($arr);
	//exit();
}



function load_keyword($keyword = ''){
	global $_G;
	$keyrowd = rpc_str($keyword);
	$keyword_arr = get_keyword($keyrowd);
	$li_html = '';
	if(!$keyword_arr) {
		$li_html = milu_lang('no_keyword');
	}else{	
		foreach($keyword_arr as $k => $v){
			$li_html .= '<li class="a"><label><input onclick="select_keyword();" type="checkbox" class="pc" checked="checked" value="'.$v.'" ><span class="xi2">'.$v.'</span></label></li>';
		}
	}
	echo $li_html;
	return $li_html;
}


function url_page_range_test(){
	global $_G;
	$url = format_url($_GET['url']);
	$url = cnurl($url);
	$rand_flag = intval($_GET['rand']);
	if(!strexists($url, '(*)')) {
		$new_arr = array($url);
		$count = 1;
	}else{
		$auto = $_GET['auto'];
		$start = $_GET['start'];
		$end = $_GET['end'];
		$step = $_GET['step'];
		if($auto == 'undefined') $auto = 0;
		$range_arr = range($start, $end, $step);
		$count = count($range_arr);
		$start = intval($start);
		$end = intval($end);
		$step = intval($step);
		$max_len = strlen($range_arr[$count - 1]);
		if($start == $end) {
			show_pick_window(milu_lang('get_link_list_test'), milu_lang('start_no_less_end'), array('w' => 620,'h' => '400','f' => 1));
			exit();
		}
		if($step == 0) {
			show_pick_window(milu_lang('get_link_list_test'), milu_lang('step_no_data'), array('w' => 620,'h' => '400','f' => 1));
			exit();
		}
		if($start > 1677215 || $end > 1677215) {
			show_pick_window(milu_lang('get_link_list_test'), milu_lang('long_data'), array('w' => 620,'h' => '400','f' => 1));
			exit();
		}
		if($count < 9){
			$new_arr = convert_url_range(array('url' => $url, 'auto' => $auto, 'start' => $start, 'end' => $end, 'step' => $step));
		}else{	
			$arr1 = array_slice($range_arr, 0, 4);
			array_push ($arr1, 0);
			$arr2 = array_slice($range_arr, $count-4, $count-1);
			$arr = array_merge($arr1, $arr2);
			foreach($arr as $k => $v){
				if($v == 0){
					$new_arr[$k] = 0;
				}else{
					$v = $auto ? str_pad($v, $max_len, "0", STR_PAD_LEFT) : $v;
					$key = array_search($v, $range_arr); 
					$new_arr[$key] = str_replace('(*)', $v, $url); 
				}
			}
		}
	}
	
	if($rand_flag == 1) {
		$rand_arr = array_filter($new_arr);
		$key = rand(0, count($rand_arr) - 1);
		$key = array_rand($rand_arr, 1);
		return $rand_arr[$key] ;
	}
	$link_html = pickOutput::windos_show_link($new_arr,'',array('count' => $count), url_filter_args_data());
	show_pick_window(milu_lang('get_link', array('count' => $count)), $link_html, array('w' => 620,'h' => '400','f' => 1));
}

function many_list_get_page($rules_arr,$start_url = ''){
	extract($rules_arr);
	$url = $start_url ? $start_url : $test;
	$rules = stripslashes($rules);
	$content = get_contents($url, array('login_cookie' => $login_cookie, 'cache' => -1));
	if($type == 1){//dom
		$link_arr = dom_page_link($content, array('page_link_rules' => $rules, 'url_page_range'=>$url));
	}else{
		$link_arr = string_page_link($content, $rules, $url);
	}
	return $link_arr;
}

function create_rules_html(){
	return pickOutput::create_rules_html();
}


function url_filter_args_data(){
	$is_filter = intval($_GET['is_filter']);
	$is_page_fiter = intval($_GET['is_page_fiter']);
	
	
	$page_url_no_other = format_url($_GET['page_url_no_other']);
	
	$page_url_replace = format_url($_GET['page_url_replace']);
	$page_url_contain = format_url($_GET['page_url_contain']);
	$page_url_no_contain = format_url($_GET['page_url_no_contain']);
	if($is_filter != 1){
		$page_url_replace = $page_url_contain = $page_url_no_contain = '';
	}
	
	$args = array();
	if(($is_filter == 1 || $is_page_fiter == 1)){
		$args = array(
			'page_url_replace' => $page_url_replace,
			'page_url_no_other' => $page_url_no_other,
			'page_url_contain' => $page_url_contain,
			'page_url_no_contain' => $page_url_no_contain,
			'page_url_no_other' => $page_url_no_other,
		);
	}
	return $args;
}

function many_list_test(){
	global $_G;
	$type = $_GET['type'];
	$rules = rpc_str($_GET['rules']);//记得转换中文
	$url = rpc_str($_GET['test']);
	$login_cookie = format_cookie($_GET['login_cookie']);
	$link_arr = many_list_get_page(array('type'=>$type, 'rules' => $rules, 'test' => $url, 'login_cookie' => $login_cookie));
	$args = url_filter_args_data();
	$link_html = pickOutput::windos_show_link($link_arr, '', array(), $args);
	show_pick_window(milu_lang('get_link_list_test'), $link_html, array('w' => 620,'h' => '400','f' => 1));
}

function get_rss_url($show = 1, $rss_url = ''){
	pload('F:spider');
	$rss_url = $rss_url ? $rss_url : rpc_str($_GET['rss_url']);
	$url_arr = format_wrap($rss_url);
	$rss = get_rss_obj();
	$arr = $arr_new = array();
	foreach((array)$url_arr as $k => $v){
		$rs = $rss->Get(trim($v)); //不去掉空格好像不行
		$items = $rs['items'];
		foreach((array)$items as $k1 => $v1){
			$arr[] = $v1['link'];
		}
		$arr_new = array_merge($arr_new, $arr);
		unset($arr); 
	}
	if(intval($_GET['rand']) == 1) {
		$rand_arr = array_filter($arr_new);
		$key = rand(0, count($rand_arr) - 1);
		$key = array_rand($rand_arr, 1);
		return $rand_arr[$key] ;
	}
	
	if($show != 1) return $arr_new;
	$args = url_filter_args_data();
	$link_html = pickOutput::windos_show_link($arr_new, '', array(), $args);
	show_pick_window(milu_lang('get_link_list_test'), $link_html, array('w' => 620,'h' => '400','f' => 1));
}



//执行采集
function start_pick($args = array()){
	require_once(PICK_DIR.'/lib/pick.class.php');
	$pick = new pick();
	if($args['url_arr']){//重新采集
		$pick->p_arr['rules_type'] = 2;
		$pick->p_arr['url_range_type'] = 2;
		$pick->p_arr['page_fiter'] = 1;
		$pick->pick_cache_data['no_check_url'] = 1;
		$pick->pick_cache_data['repick']['return_url'] = $args['return_url'];
		$n = 0;
		$pick->p_arr['url_page_range'] = '';
		$pick->p_arr['page_url_other'] = $pick->p_arr['page_url_no_other'] = '';
		$pick->p_arr['is_check_title'] = 0;//不检测标题
		
		foreach($args['url_arr'] as $aid => $url){
			$url_hash = md5($url);
			$pick->pick_cache_data['repick'][$url_hash] = $aid;
			if($n == 0){
				$pick->p_arr['url_page_range'] .= $url;
			}else{
				$pick->p_arr['page_url_other'] .= $url."\r\n";
			}
			$n++;
		}
		
	}
	$pick->run_start();
}

//同步
function rules_update($rules_hash = ''){
	$rules_hash = $rules_hash ? $rules_hash : $_GET['rules_hash'];
	$v_info = get_rules_info($rules_hash);
	$field_arr = array('page_get_type', 'page_link_rules', 'page_url_test', 'theme_url_test', 'theme_get_type', 'theme_rules', 'is_fiter_title', 'title_replace_rules', 'title_filter_rules', 'content_get_type', 'is_fiter_content', 'content_filter_rules', 'is_fiter_reply', 'reply_is_extend', 'reply_get_type', 'reply_rules', 'reply_fiter_replace', 'reply_filter_rules', 'content_page_get_type', 'content_page_rules', 'content_page_get_mode', 'is_get_other', 'from_get_type', 'author_get_type', 'from_get_rules', 'author_get_rules', 'dateline_get_type', 'dateline_get_rules','reply_replace_rules', 'content_rules', 'content_replace_rules', 'reply_filter_html', 'charset_type', 'is_fiter_page_link', 'page_link_replace_rules', 'page_url_contain', 'page_url_no_contain', 'content_no_contain', 'is_pick_cover_from_listpage', 'pick_cover_rules_get_type', 'pick_cover_rules_get_rules', 'is_get_thread_user', 'thread_user_get_type', 'thread_user_get_rules', 'thread_dateline_get_type', 'thread_dateline_get_rules', 'is_get_user_other', 'user_other_rules', 'is_get_threadtypes', 'forum_threadtype_id', 'is_get_reply', 'is_setting_best_answer', 'best_answer_get_type', 'best_answer_get_rules', 'best_answer_flag', 'ask_reward_price_get_type', 'ask_reward_price_get_rules', 'is_get_post_user', 'post_user_get_type', 'post_user_get_rules', 'post_dateline_get_type', 'post_dateline_get_rules', 'is_setting_article_page', 'is_fiter_content_page_link', 'content_page_link_replace_rules', 'content_page_url_contain', 'content_page_url_no_contain', 'is_attach_setting', 'attach_redirect_url_get_type', 'attach_redirect_url_get_rules', 'attach_download_url_get_type', 'attach_download_url_get_rules', 'forum_threadtypes');
	foreach($field_arr as $k => $v){
		$setarr[$v] = $v_info[$v];
	}
	$query = DB::query("SELECT pid,rules_hash,rules_type FROM ".DB::table('strayer_picker')."  WHERE rules_hash ='$rules_hash' AND rules_type='1'");
	while(($rs = DB::fetch($query))) {
		DB::update('strayer_picker', $setarr, array('pid' => $rs['pid']));
	}
}


function pick_log_list($pid = 0){
	$pid = $pid ? $pid : $_REQUEST['pid'];
	$pid = intval($pid);
	$log_dir = PICK_PATH.'/data/log/pick/'.$pid;
	require_once(PICK_DIR.'/lib/cache.class.php');
	$output_html = pick_log_show($pid, 'pick').pick_log_show($pid, 'timing');//exit();
	show_pick_window(milu_lang('log_list'), $output_html, array('w' => 620,'h' => '400','f' => 1));
}

function pick_log_show($pid, $type = 'pick'){
	$log_dir = PICK_PATH.'/data/log/'.$type.'/'.$pid;
	require_once(PICK_DIR.'/lib/cache.class.php');
	$arr = IO::ls($log_dir);
	$title = $type == 'pick' ? milu_lang('auto_pick') : milu_lang('timing');
	$output_html = '<ul id="'.$type.'_log_list" class="show_debug" style="width:250px;float:left; margin-left:25px;"><li><h1>'.$title.'</h1></li>';
	foreach((array)$arr as $k => $v){
		if(!$v) continue;
		$file_name = basename($v[1]);
		if($file_name == 'index.html') {
			if(count($arr) == 1) $arr = array();
			continue;
		}
		$i++;
		$path = $v[1];
		$url = PICK_URL.'data/log/'.$type.'/'.$pid.'/'.$file_name;
		$output_html .= '<li id="'.$type.'_log_'.$i.'" style="border-bottom:1px dashed #FFCCFF; line-height:25px; height:25px;">'.$i.'. <a style="color:#333333"  target="_blank"  href="'.$url.'">'.$file_name.'</a><a href="javascript:void(0)" style=" float:right;color:#2366A8" onclick="del_log('.$pid.', \''.$file_name.'\','.$i.', \''.$type.'\');">'.milu_lang('del').'</a></li>';
	}
	if(!$arr) $output_html .= '<li>'.milu_lang('no_log').'</li>';
	$output_html .= '</ul>';
	
	return $output_html;
}


function del_pick_one_log(){
	$pid = intval($_REQUEST['pid']);
	$file_name = rpc_str($_REQUEST['file_name']);
	$type = $_GET['type'];
	$log_file = PICK_PATH.'/data/log/'.$type.'/'.$pid.'/'.$file_name;
	@unlink($log_file);
}

//清空某个采集器的日志
function del_pick_log($pid){
	$log_pick_dir = PICK_PATH.'/data/log/pick/'.$pid;
	$log_timing_dir = PICK_PATH.'/data/log/timing/'.$pid;
	require_once(PICK_DIR.'/lib/cache.class.php');
	IO::rm($log_pick_dir);
	IO::rm($log_timing_dir);
}


function pick_log($msg, $args = array() ){
	extract($args);
	$log_type = $log_type ? $log_type : 'pick';
	$log_dir = PICK_PATH.'/data/log/'.$log_type.'/'.$pid.'/';
	$log_file = ($file_name ? $file_name : $log_dir.date("Y-m-d", time())).'.txt';
	if(!is_dir($log_dir)) dmkdir($log_dir);
	$msg = clear_ad_html($msg);
	$msg = str_replace(milu_lang('loading'), '', $msg);
	if($memory) $m_str = 'memory:'.$memory;
	$log_str .= date("Y-m-d H:i:s").'	'.$m_str.'	'.strip_tags($msg)."\r\n";
	$log_str .= str_repeat('-',100)."\r\n";
	require_once(PICK_DIR.'/lib/cache.class.php');
	IO::write($log_file, $log_str, 1);
}




function get_other_test(){
	global $_G;
	pload('F:spider');
	$url = format_url($_GET['url']);
	$args['from_get_type'] = format_url($_GET['from_get_type']);
	$args['author_get_type'] = format_url($_GET['author_get_type']);
	$args['dateline_get_type'] = format_url($_GET['dateline_get_type']);
	$args['from_get_rules'] = format_url($_GET['from_get_rules']);
	$args['author_get_rules'] = format_url($_GET['author_get_rules']);
	$args['dateline_get_rules'] = format_url($_GET['dateline_get_rules']);
	$login_cookie = format_cookie($_GET['login_cookie']);
	
	$contents = get_contents($url, array('cookie' => $login_cookie));
	$dateline_info = format_wrap($args['dateline_get_rules'], '@@');
	$args['dateline_get_rules'] = $dateline_info[0];
	$data = get_other_info($contents, $args);
	$show_time = str_format_time($data['article_dateline'], $dateline_info[1]);
	$show_time = $show_time ? dgmdate($show_time) : '';
	if(!$data){
		$output = milu_lang('no_get_info');
	}else{
		$out_data_arr = array(
			0 => array('name' => milu_lang('article_from'), 'value' => $data['from'], 'format_value' => milu_lang('no_turn')),
			1 => array('name' => milu_lang('old_author'), 'value' => $data['author'], 'format_value' => milu_lang('no_turn')),
			2 => array('name' => milu_lang('public_time'), 'value' => $data['article_dateline'], 'format_value' => $show_time),
		);
		$output = pickOutput::other_value_output($out_data_arr);
	
		if($args['dateline_get_rules']){
		 	$output .= milu_lang('get_other_notice');
		}
	}
	show_pick_window(milu_lang('get_other_show'), $output, array('w' => 645,'h' => '460','f' => 1));
}



function pick_match_rules(){
	$url = format_url($_GET['url']);d_s();
	$content = get_contents($url);
	$v = match_rules($url, $content, 2, 0);
	if(!$v || !is_array($v)) {
		$v = pick_match_coloud_rules($url);
		if($v['data_type'] == 1) {
			pload('F:rules');
			$v = $v['data'];
			rules_add($v);
			del_search_index(2);
		}	
	}
	if(!$v || !is_array($v)) return 'no';
	
	$re_arr = array($v['rules_type'],$v['rules_hash']);
	return json_encode($re_arr);
}

//搜索服务端规则
function pick_match_coloud_rules($url, $get_type = 2){
	
	$args = array(
		'get_type' => $get_type,
		'url' => $url,
	);
	$rpcClient = rpcClient();
	$client_info = get_client_info();
	$re = $rpcClient->cloud_match_rules($args, $client_info);
	if(is_object($re) || $re->Number == 0){
		if($re->Message) return  milu_lang('phprpc_error', array('msg' => $re->Message));
		$re = (array)$re;
	}
	return $re;
}


function get_therad_sort_data($threadtypes_data, $sortid){
	$is_return_array = 1;
	if(!empty($threadtypes_data) && !is_array($threadtypes_data)) {
		$threadtypes_data = dunserialize($threadtypes_data);
		$is_return_array = 0;
	}
	$threadtypes_data = (array)$threadtypes_data;
	$threadtypes_data['threadsort']['sortid'] = $sortid;
	$threadtypes_data['threadsort']['data'] = export_thread_sort($sortid);
	$threadtypes_data['threadsort']['name'] = $threadtypes_data['threadsort']['data'][0]['name'];
	if($is_return_array != 1) $threadtypes_data = serialize($threadtypes_data);
	return $threadtypes_data;
	
}


//判断采集器里面的信息分类和论坛本地的是不是同一个
function get_local_sortid($threadtypes_data, $sortid){
	global $_G;
	if(!$threadtypes_data || !is_array($threadtypes_data)) return;
	$rules_sort_data = $threadtypes_data['threadsort']['data'];
	
	$name = paddslashes($threadtypes_data['threadsort']['name']);
	$threadtype_info = DB::fetch_first("SELECT typeid FROM ".DB::table('forum_threadtype')." WHERE typeid='$sortid' AND name='".$name."'");//同名 同id
	if($threadtype_info['typeid']) return $threadtype_info['typeid'];
	
	$threadtype_info = DB::fetch_first("SELECT typeid FROM ".DB::table('forum_threadtype')." WHERE name='".$name."'");//同名，不同id
	//查询是不是同一个
	$identifier_arr = array();
	foreach((array)$rules_sort_data as $k => $v){
		$identifier_arr[] = $v['identifier'];//规则里面的
	
	}
	$sortid = $threadtype_info['typeid'];
	$typevar_arr = array();
	$query = DB::query("SELECT optionid FROM ".DB::table('forum_typevar')." WHERE sortid='$sortid'");
	while($rs = DB::fetch($query)) {
		$typevar_arr[] = $rs['optionid'];
	}
	$typeoption_arr = array();
	$query = DB::query("SELECT identifier FROM ".DB::table('forum_typeoption')." WHERE optionid IN (".dimplode($typevar_arr).")");
	while($rs = DB::fetch($query)) {
		$typeoption_arr[] = $rs['identifier'];
	}
	if(count($identifier_arr) > count($typeoption_arr)){
		$a = $identifier_arr;
		$b = $typeoption_arr;
	}else{
		$b = $identifier_arr;
		$a = $typeoption_arr;
	}
	$diff = array_diff($a, $b);
	if(!$diff[0]) return $sortid;//是同一个
	
	//如果含有多个类似名称的。可能有其中一个是
	$typeid_arr = array();
	$query = DB::query("SELECT typeid FROM ".DB::table('forum_threadtype')." WHERE name like '%".$name."%'");
	while($rs = DB::fetch($query)) {
		$typeid_arr[] = $rs['typeid'];
	}
	$optionid_arr = array();
	$query = DB::query("SELECT optionid FROM ".DB::table('forum_typeoption')." WHERE identifier IN (".dimplode($identifier_arr).")");
	while($rs = DB::fetch($query)) {
		$optionid_arr[] = $rs['optionid'];//如果同时满足这些字段。
	}
	
	//进一步验证，如果名称差不多，而且同时满足这些字段,而且含有的字段数目一样
	$sortid_arr = array();
	$var_count = count($identifier_arr);
	foreach($typeid_arr as $k => $v){
		$check_count = DB::result(DB::query("SELECT  COUNT(*) FROM ".DB::table('forum_typevar')." WHERE sortid='$v'"));
		if($check_count != $var_count) continue;
		$check2 = DB::result(DB::query("SELECT  COUNT(*) FROM ".DB::table('forum_typevar')." WHERE sortid='$v' AND optionid IN (".dimplode($optionid_arr).")"));
		if($check2) return $v;
		
	}
	return 0;

}



function export_thread_sort($sortid){
	$sortid = intval($sortid);
	if(empty($sortid)) return array();
	$typevarlist = $typevararr = $typeoptionarr = array();
	$query = DB::query("SELECT * FROM ".DB::table('forum_typevar')." WHERE sortid='$sortid'");
	while($rs = DB::fetch($query)) {
		$typevararr[$rs['optionid']] = $rs;
	}
	
	$query = DB::query("SELECT * FROM ".DB::table('forum_typeoption')." WHERE optionid IN (".dimplode(array_keys($typevararr)).")");
	while($rs = DB::fetch($query)) {
		$typeoptionarr[$rs['optionid']] = $rs;
	}
	
	$threadtypearr = DB::fetch_first("SELECT * FROM ".DB::table('forum_threadtype')." WHERE typeid='$sortid'");
	foreach($typevararr as $typevar) {
		$typeoption = $typeoptionarr[$typevar['optionid']];
		$typevar = array_merge($threadtypearr, $typevar);
		$typevar = array_merge($typeoption, $typevar);
		$typevar['tpdescription'] = $typeoption['description'];
		$typevar['ttdescription'] = $threadtypearr['description'];
		$typevar['tpexpiration'] = $typeoption['expiration'];
		$typevar['ttexpiration'] = $threadtypearr['expiration'];
		unset($typevar['fid']);
		$typevarlist[] = $typevar;
	}
	if(empty($typevarlist)) {
		$threadtype = DB::fetch_first("SELECT * FROM ".DB::table('forum_threadtype')." WHERE typeid='$sortid'");
		$threadtype['ttdescription'] = $threadtype['description'];
		unset($threadtype['fid']);
		$typevarlist[] = $threadtype;
	}
	return $typevarlist;
}

//导入信息分类
function import_thread_sort($newthreadtype){
	if(!$newthreadtype) return 1;
	$idcmp = $searcharr = $replacearr = $indexoption = array();
	$create_tableoption_sql = $separator = '';
	$i = 0;
	$mysql_keywords = array( 'ADD', 'ALL', 'ALTER', 'ANALYZE', 'AND', 'AS', 'ASC', 'ASENSITIVE', 'BEFORE', 'BETWEEN', 'BIGINT', 'BINARY', 'BLOB', 'BOTH', 'BY', 'CALL', 'CASCADE', 'CASE', 'CHANGE', 'CHAR', 'CHARACTER', 'CHECK', 'COLLATE', 'COLUMN', 'CONDITION', 'CONNECTION', 'CONSTRAINT', 'CONTINUE', 'CONVERT', 'CREATE', 'CROSS', 'CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP', 'CURRENT_USER', 'CURSOR', 'DATABASE', 'DATABASES', 'DAY_HOUR', 'DAY_MICROSECOND', 'DAY_MINUTE', 'DAY_SECOND', 'DEC', 'DECIMAL', 'DECLARE', 'DEFAULT', 'DELAYED', 'DELETE', 'DESC', 'DESCRIBE', 'DETERMINISTIC', 'DISTINCT', 'DISTINCTROW', 'DIV', 'DOUBLE', 'DROP', 'DUAL', 'EACH', 'ELSE', 'ELSEIF', 'ENCLOSED', 'ESCAPED', 'EXISTS', 'EXIT', 'EXPLAIN', 'FALSE', 'FETCH', 'FLOAT', 'FLOAT4', 'FLOAT8', 'FOR', 'FORCE', 'FOREIGN', 'FROM', 'FULLTEXT', 'GOTO', 'GRANT', 'GROUP', 'HAVING', 'HIGH_PRIORITY', 'HOUR_MICROSECOND', 'HOUR_MINUTE', 'HOUR_SECOND', 'IF', 'IGNORE', 'IN', 'INDEX', 'INFILE', 'INNER', 'INOUT', 'INSENSITIVE', 'INSERT', 'INT', 'INT1', 'INT2', 'INT3', 'INT4', 'INT8', 'INTEGER', 'INTERVAL', 'INTO', 'IS', 'ITERATE', 'JOIN', 'KEY', 'KEYS', 'KILL', 'LABEL', 'LEADING', 'LEAVE', 'LEFT', 'LIKE', 'LIMIT', 'LINEAR', 'LINES', 'LOAD', 'LOCALTIME', 'LOCALTIMESTAMP', 'LOCK', 'LONG', 'LONGBLOB', 'LONGTEXT', 'LOOP', 'LOW_PRIORITY', 'MATCH', 'MEDIUMBLOB', 'MEDIUMINT', 'MEDIUMTEXT', 'MIDDLEINT', 'MINUTE_MICROSECOND', 'MINUTE_SECOND', 'MOD', 'MODIFIES', 'NATURAL', 'NOT', 'NO_WRITE_TO_BINLOG', 'NULL', 'NUMERIC', 'ON', 'OPTIMIZE', 'OPTION', 'OPTIONALLY', 'OR', 'ORDER', 'OUT', 'OUTER', 'OUTFILE', 'PRECISION', 'PRIMARY', 'PROCEDURE', 'PURGE', 'RAID0', 'RANGE', 'READ', 'READS', 'REAL', 'REFERENCES', 'REGEXP', 'RELEASE', 'RENAME', 'REPEAT', 'REPLACE', 'REQUIRE', 'RESTRICT', 'RETURN', 'REVOKE', 'RIGHT', 'RLIKE', 'SCHEMA', 'SCHEMAS', 'SECOND_MICROSECOND', 'SELECT', 'SENSITIVE', 'SEPARATOR', 'SET', 'SHOW', 'SMALLINT', 'SPATIAL', 'SPECIFIC', 'SQL', 'SQLEXCEPTION', 'SQLSTATE', 'SQLWARNING', 'SQL_BIG_RESULT', 'SQL_CALC_FOUND_ROWS', 'SQL_SMALL_RESULT', 'SSL', 'STARTING', 'STRAIGHT_JOIN', 'TABLE', 'TERMINATED', 'THEN', 'TINYBLOB', 'TINYINT', 'TINYTEXT', 'TO', 'TRAILING', 'TRIGGER', 'TRUE', 'UNDO', 'UNION', 'UNIQUE', 'UNLOCK', 'UNSIGNED', 'UPDATE', 'USAGE', 'USE', 'USING', 'UTC_DATE', 'UTC_TIME', 'UTC_TIMESTAMP', 'VALUES', 'VARBINARY', 'VARCHAR', 'VARCHARACTER', 'VARYING', 'WHEN', 'WHERE', 'WHILE', 'WITH', 'WRITE', 'X509', 'XOR', 'YEAR_MONTH', 'ZEROFILL', 'ACTION', 'BIT', 'DATE', 'ENUM', 'NO', 'TEXT', 'TIME');
	foreach($newthreadtype as $key => $value) {
		if(!$i) {
			if($newname1 = trim(strip_tags($value['name']))) {
				$findname = 0;
				$tmpnewname1 = $newname1;
				$decline = '_';
				while(!$findname) {
					$check = DB::result_first("SELECT typeid FROM ".DB::table('forum_threadtype')." WHERE name='$tmpnewname1'");
					if($check) {
						$tmpnewname1 = $newname1.$decline;
						$decline .= '_';
					} else {
						$findname = 1;
					}
				}
				$newname1 = $tmpnewname1;
				$data = array(
					'name' => $newname1,
					'description' => dhtmlspecialchars(trim($value['ttdescription'])),
					'special' => 1,
				);
				$sortid = DB::insert('forum_threadtype', paddslashes($data), TRUE);
			}
			$i = 1;

			if(empty($value['identifier'])) {
				return $sortid;
			}
		}

		$typeoption = array(
			'classid' => $value['classid'],
			'expiration' => $value['tpexpiration'],
			'protect' => $value['protect'],
			'title' => $value['title'],
			'description' => $value['tpdescription'] ? $value['tpdescription'] : '',
			'type' => $value['type'],
			'unit' => $value['unit'] ? $value['unit'] : '',
			'rules' => $value['rules'],
			'permprompt' => $value['permprompt'],
		);
		if(strlen($value['identifier']) > 34) {
			return -1;
		}

		$findidentifier = 0;
		$tmpidentifier = $value['identifier'];
		$decline = '_';
	
		$typeoption['identifier'] = $tmpidentifier;
		$idcmp[$value['identifier']] = $tmpidentifier;
		if(DISCUZ_VERSION == 'X2') unset($typeoption['permprompt']);//版本兼容
		$check = DB::result_first("SELECT optionid FROM ".DB::table('forum_typeoption')." WHERE identifier='$tmpidentifier'");
		if(!$check) {
			$newoptionid = DB::insert('forum_typeoption', paddslashes($typeoption), TRUE);
		}else{
			$newoptionid = $check;
		}
		$typevar = array(
			'sortid' => $sortid,
			'optionid' => $newoptionid,
			'available' => $value['available'],
			'required' => $value['required'],
			'unchangeable' => $value['unchangeable'],
			'search' => $value['search'],
			'displayorder' => $value['displayorder'],
			'subjectshow' => $value['subjectshow'],
		);
		DB::insert('forum_typevar', $typevar, TRUE);

		if($tmpidentifier) {
			if(in_array($value['type'], array('radio'))) {
				$create_tableoption_sql .= "$separator$tmpidentifier smallint(6) UNSIGNED NOT NULL DEFAULT '0'";
			} elseif(in_array($value['type'], array('number', 'range'))) {
				$create_tableoption_sql .= "$separator$tmpidentifier int(10) UNSIGNED NOT NULL DEFAULT '0'";
			} elseif($value['type'] == 'select') {
				$create_tableoption_sql .= "$separator$tmpidentifier varchar(50) NOT NULL";
			} else {
				$create_tableoption_sql .= "$separator$tmpidentifier mediumtext NOT NULL";
			}
			$separator = ' ,';
			if(in_array($value['type'], array('radio', 'select', 'number'))) {
				$indexoption[] = $tmpidentifier;
			}
		}
	}

	foreach($idcmp as $k => $v) {
		if($k != $v) {
			$searcharr[] = '{'.$k;
			$searcharr[] = '['.$k;
			$replacearr[] = '{'.$v;
			$replacearr[] = '['.$v;
		}
	}

	$threadtype = array(
		'icon' => $value['icon'],
		'special' => $value['special'],
		'modelid' => $value['modelid'],
		'expiration' => $value['ttexpiration'],
		'template' => str_replace($searcharr, $replacearr, $value['template']),
		'stemplate' => str_replace($searcharr, $replacearr, $value['stemplate']),
		'ptemplate' => str_replace($searcharr, $replacearr, $value['ptemplate']),
		'btemplate' => str_replace($searcharr, $replacearr, $value['btemplate']),
	);
	DB::update('forum_threadtype', paddslashes($threadtype), array('typeid' => $sortid));

	$fields = ($create_tableoption_sql ? $create_tableoption_sql.',' : '')."tid mediumint(8) UNSIGNED NOT NULL DEFAULT '0',fid smallint(6) UNSIGNED NOT NULL DEFAULT '0',dateline int(10) UNSIGNED NOT NULL DEFAULT '0',expiration int(10) UNSIGNED NOT NULL DEFAULT '0',";
	$fields .= "KEY (fid), KEY(dateline)";
	if($indexoption) {
		foreach($indexoption as $index) {
			$fields .= "$separator KEY $index ($index)";
			$separator = ' ,';
		}
	}
	$dbcharset = $_G['config']['db'][1]['dbcharset'];
	$dbcharset = empty($dbcharset) ? str_replace('-','',CHARSET) : $dbcharset;
	forum_optionvalue_create($sortid, $fields, $dbcharset);
	require_once libfile('function/cache');
	updatecache('threadsorts');
	return $sortid;
}

function forum_optionvalue_create($sortid, $fields, $dbcharset) {
	if(!$sortid || !$fields || !$dbcharset) {
		return;
	}
	$sortid = intval($sortid);
	$_table = 'forum_optionvalue'.$sortid;
	$query = DB::query("SHOW TABLES LIKE '$_table'");
	if(DB::num_rows($query) != 1) {
		$create_table_sql = "CREATE TABLE ".DB::table($_table)." ($fields) TYPE=MyISAM;";
		$db = DB::object();
		$create_table_sql = forum_optionvalue_syntablestruct($create_table_sql, $db->version() > '4.1', $dbcharset);
		DB::query($create_table_sql);
	}
}

function forum_optionvalue_syntablestruct($sql, $version, $dbcharset) {
	if(strpos(trim(substr($sql, 0, 18)), 'CREATE TABLE') === FALSE) {
		return $sql;
	}

	$sqlversion = strpos($sql, 'ENGINE=') === FALSE ? FALSE : TRUE;

	if($sqlversion === $version) {

		return $sqlversion && $dbcharset ? preg_replace(array('/ character set \w+/i', '/ collate \w+/i', "/DEFAULT CHARSET=\w+/is"), array('', '', "DEFAULT CHARSET=$dbcharset"), $sql) : $sql;
	}

	if($version) {
		return preg_replace(array('/TYPE=HEAP/i', '/TYPE=(\w+)/is'), array("ENGINE=MEMORY DEFAULT CHARSET=$dbcharset", "ENGINE=\\1 DEFAULT CHARSET=$dbcharset"), $sql);

	} else {
		return preg_replace(array('/character set \w+/i', '/collate \w+/i', '/ENGINE=MEMORY/i', '/\s*DEFAULT CHARSET=\w+/is', '/\s*COLLATE=\w+/is', '/ENGINE=(\w+)(.*)/is'), array('', '', 'ENGINE=HEAP', '', '', 'TYPE=\\1\\2'), $sql);
	}
}

//采集器采集数、已导入、未导入数据的校验
function picker_data_count_check($pid_arr){
	foreach((array)$pid_arr as $k => $pid){
		if(!$pid) continue;	
		$url_count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_url')." WHERE pid='".$pid."'"), 0);
		$article_count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_article_title')." WHERE pid ='".$pid."'"), 0);
		$no_import_count =  DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_article_title')." WHERE pid ='".$pid."' AND status=0"), 0);
		$picker_info = DB::fetch_first("SELECT article_num,article_import_num,visit_url_num FROM ".DB::table('strayer_picker')." WHERE pid='".$pid."'");
		$import_count = max($article_count - $no_import_count, 0);
		if($picker_info['article_num'] != $article_count || $picker_info['visit_url_num'] != $url_count || $picker_info['article_import_num'] != $import_count){
			DB::update('strayer_picker', array('article_num' => $article_count, 'visit_url_num' => $url_count, 'article_import_num' => $import_count, ), array('pid' => $pid));
		}
		
	}
}

/*分类信息新旧数据sort_id的转换*/
function forum_thread_data_format($forum_threadtypes_arr, $old_sortid){
	global $_G;
	loadcache(array('threadsort_option_'.$old_sortid));
	$sortoptionarray = $_G['cache']['threadsort_option_'.$old_sortid];
	if($forum_threadtypes_arr['threadsort']['sortid'] == $old_sortid) return $forum_threadtypes_arr;
	$new_var_arr  = $map_arr = array();
	foreach((array)$sortoptionarray as $k => $v){
		$new_var_arr[$v['identifier']] = $k;
	} 
	foreach((array)$forum_threadtypes_arr['threadsort']['data'] as $k => $v){
		$map_arr[$v['optionid']] = $new_var_arr[$v['identifier']];
	} 
	foreach((array)$map_arr as $old_id => $new_id){
		$forum_threadtypes_arr['get_type'][$new_id] = $forum_threadtypes_arr['get_type'][$old_id];
		unset($forum_threadtypes_arr['get_type'][$old_id]);
		$forum_threadtypes_arr['get_rules'][$new_id] = $forum_threadtypes_arr['get_rules'][$old_id];
		unset($forum_threadtypes_arr['get_rules'][$old_id]);
	}
	return $forum_threadtypes_arr;
}
?>