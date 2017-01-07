<?php
if(!defined('IN_DISCUZ') ) {
	exit('Access Denied');
}
require_once(DISCUZ_ROOT.'source/plugin/milu_pick/config.inc.php');
pload('F:copyright');
$ac = $_GET['ac'];
if(!empty($ac) && function_exists($ac)) {
	if(!in_array($ac, array('bak_data_clear', 'do_data_bak', 'data_restore', 'bak_data_clear', 'pick_check', 'pick_download', 'pick_install', 'ajax_func')) || $_G['adminid'] < 1 ) exit('Access Denied:0032');
	$info = $ac();
	return;
}
if($_GET['pmod'] == 'pick_info'){
	$user_arr = get_user_level();
	$user_databak_status_arr = get_data_bak_status(3600*24*30);
	$evo_check_msg = evo_check();
	$evo_config_arr = evo_server_config();
	$pick_count_msg = pick_count();
}
function clear_data_run(){
	$type = $_GET['type'];
	pload('C:cache');
	if($type == 'search_index'){
		DB::query('DELETE FROM '.DB::table('strayer_searchindex'));
	}else if($type == 'log'){
		IO::rm(PICK_DIR.'/data/log');
	}else if($type == 'cache'){
		IO::rm(PICK_CACHE);
	}
	return 'ok';
}

function pick_count(){
	clear_pick_cache();//缓存定期清理
	clear_search_index();//清除索引
	clear_log();//清除日志
	pload('C:cache');
	$arr['search_index']['name'] = milu_lang('rules_search_index');
	$arr['search_index']['msg'] = milu_lang('search_index_notice').clear_data_btn_output('search_index');
	$arr['search_index']['show'] =  '<span style=" width:120px; float:left">'.milu_lang('search_index_c').'<hr>';
	$type_arr = array('1' => milu_lang('fast_pick_rules'), '2' => milu_lang('dxc_system_rules'), '3' => milu_lang('fastpick_evo'));
	$type_arr2 = array('3' => milu_lang('server_'), '4' => milu_lang('local_'));
	foreach($type_arr as $k => $v){
		foreach($type_arr2 as $k2 => $v2){
			$type = $k.$k2;
			$show_name = '<span style=" width:120px; float:left">'.$type_arr[$k].$type_arr2[$k2].'</span>';
			$search_index_count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_searchindex')." WHERE type='$type'"), 0);
			$arr['search_index']['show'] .= $show_name.' '.$search_index_count.'<br />';
		}
	}
	$log_info = IO::info(PICK_DIR.'/data/log');
	$arr['log']['name'] = milu_lang('log_size');
	$arr['log']['msg'] = milu_lang('auto_pick_notice').clear_data_btn_output('log');
	$arr['log']['show'] = sizecount($log_info['size']);
	$cache_info = IO::info(PICK_CACHE, 1, 1);
	$arr['cache']['name'] = milu_lang('cache_file_size');
	$pick_set = get_pick_set();
	$max_cache_size = !empty($pick_set['max_cache_size']) ? $pick_set['max_cache_size'] : 40;
	$arr['cache']['show'] = milu_lang('cache_size_value', array('s' => sizecount($cache_info['size']), 'p' => $max_cache_size));
	$arr['cache']['msg'] = milu_lang('cache_notice').clear_data_btn_output('cache');
	//如果直接结算文件夹的大小，计算太长时间了，考虑要不要去掉这个功能
	$attach_info = IO::info(PICK_ATTACH_PATH, 1, 1);
	$arr['attach']['name'] = milu_lang('attach_file_size');
	$arr['attach']['show'] = sizecount(DB::result(DB::query("SELECT SUM(filesize) as sum FROM ".DB::table('strayer_attach').""), 0));
	$arr['attach']['msg'] = milu_lang('attach_notice');
	
	return $arr;
		
}


function clear_data_btn_output($type){
	return '<a  style="margin-left:20px;" onclick="clear_data_run(\''.$type.'\');" href="javascript:void(0)">'.milu_lang('fast_clear').'</a>';
}


//数据备份


function do_data_bak(){
	$step = intval($_GET['step']);
	if($step == 1){
		$count = intval($_GET['count']);
		$i = intval($_GET['i']);
		$bat_num = intval($_GET['bat_num']);
		$key_data = pick_get_site_key();
		$cache_data = load_cache('data_bak_cache');
		$rules_code_arr = array_slice($cache_data, $i, $bat_num);
		$clear = $i>0 ? 0 : 1;
		if(!$rules_code_arr){
			cache_del('data_bak_cache');
			get_data_bak_status(-1);
			cpmsg(milu_lang('data_bak_success'), PICK_GO."pick_info", 'succeed');
		}
		$rpcClient = rpcClient();
		$re = $rpcClient->user_data_bak($key_data, $rules_code_arr, $clear);
		if(is_object($re) && $re->Number != 0){
			cache_del('data_bak_cache');
			if($re->Message) cpmsg_error(milu_lang('phprpc_error', array('msg' => $re->Message)));
		}
		if($re < 0){
			if($re == -1) cpmsg_error(milu_lang('key_check_err1'));//密钥验证失败
			if($re == -11) cpmsg_error(milu_lang('no_vip_error'));
			cpmsg_error(milu_lang('phprpc_error', array('msg' => 'error:'.$re)));
		}
		$i += $bat_num; 
		if($i > ($count - 1)) $i = $count;
		$percent = $i/$count;
		$percent = sprintf("%01.0f", $percent*100).'%';
		cpmsg(milu_lang('data_uploading', array('percent' => $percent)), PICK_GO.'pick_info&ac=do_data_bak&count='.$count.'&step=1&i='.$i.'&bat_num='.$bat_num, 'loading', '', false);
	}else{//查询数据，并缓存
		$data_arr = array();
		for($i =1;$i<5;$i++){
			$data_arr = array_merge($data_arr, get_rules_code($i));
		}
		$count = count($data_arr);
		if($count == 0){
			cpmsg_error(milu_lang('data_bak_empty'));
		}
		$bat_num = 5;//每批上传个数
		cache_data('data_bak_cache', $data_arr);
		cpmsg(milu_lang('pre_data_upload'), PICK_GO.'pick_info&ac=do_data_bak&count='.$count.'&step=1&bat_num='.$bat_num, 'loading', '', false);
	}
}


function data_restore(){
	global $user_databak_status_arr;
	$firm = intval($_GET['firm']);
	if(!$firm) cpmsg(milu_lang('data_restore_firm'), PICK_GO.'pick_info&ac=data_restore&firm=1', 'form');
	$i = intval($_GET['i']);
	$bat_num = 5;
	$cache_time = $i == 0 ? -1 : 3600*24*30;
	$user_databak_status_arr = get_data_bak_status($cache_time);
	$count = $user_databak_status_arr['category_count'] + $user_databak_status_arr['picker_count'] + $user_databak_status_arr['fast_count'] + $user_databak_status_arr['system_count'];
	if($i > ($count - 1)) cpmsg(milu_lang('data_restore_success'), PICK_GO."pick_info", 'succeed');
	$key_data = pick_get_site_key();
	$cache_data = load_cache('data_bak_cache');
	$rpcClient = rpcClient();
	$re = $rpcClient->user_data_restore($key_data, $i, $bat_num);
	if(is_object($re) && $re->Number != 0){
		if($re->Message) cpmsg_error(milu_lang('phprpc_error', array('msg' => $re->Message)));
	}

	if(!is_array($re) && $re < 0){
		if($re == -1) cpmsg_error(milu_lang('key_check_err1'));//密钥验证失败
		if($re == -3) cpmsg_error(milu_lang('data_restore_empty'));//没有数据可以恢复
		cpmsg_error(milu_lang('phprpc_error', array('msg' => 'error:'.$re)));
	}
	//导入规则
	foreach($re as $k => $v){
		$rules_info = unserialize(base64_decode($v['rules_code']));
		if($v['type'] == 1){//采集器配置
			unset($rules_info['pid']);
			import_bak_rules('picker_hash', 'strayer_picker', $rules_info);
		}else if($v['type'] == 2){//单帖采集
			unset($rules_info['rid']);
			import_bak_rules('rules_hash', 'strayer_fastpick', $rules_info);
			
		}else if($v['type'] == 3){//规则模板
			unset($rules_info['rid']);
			import_bak_rules('rules_hash', 'strayer_rules', $rules_info);
		}else if($v['type'] == 4){//采集器分类
			$check = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_category')." WHERE cid='".$rules_info['cid']."'"), 0);
			if(!$check){
				$a = DB::insert('strayer_category', $rules_info, TRUE);
			}

		}
	}
	
	$i += $bat_num;
	if($i > ($count - 1)) $i = $count;
	$percent = $i/$count;
	$percent = sprintf("%01.0f", $percent*100).'%';
	cpmsg(milu_lang('_data_downloading', array('percent' => $percent)), PICK_GO.'pick_info&ac=data_restore&firm=1&i='.$i, 'loading', '', false);
	
}


function bak_data_clear(){
	$firm = intval($_GET['firm']);
	if($firm == 0){
		cpmsg(milu_lang('clear_bak_data_firm'), PICK_GO.'pick_info&ac=bak_data_clear&firm=1', 'form');
	}else{
		$key_data = pick_get_site_key();
		$rpcClient = rpcClient();
		$re = $rpcClient->bak_data_clear($key_data);
		if(is_object($re) && $re->Number != 0){
			if($re->Message) cpmsg_error(milu_lang('phprpc_error', array('msg' => $re->Message)));
		}
		
		if(!is_array($re) && $re < 0){
			if($re == -1) cpmsg_error(milu_lang('key_check_err1'));//密钥验证失败
			cpmsg_error(milu_lang('phprpc_error', array('msg' => 'error:'.$re)));
		}
		
		get_data_bak_status(-1);
		cpmsg(milu_lang('clear_finsh'), PICK_GO."pick_info", 'succeed');
	}
}

function import_bak_rules($hash_name, $table_name, $rules_info){
	$rules_hash = $rules_info[$hash_name];
	$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table($table_name)." WHERE $hash_name='$rules_hash'"), 0);
	$rules_info = get_table_field_name($table_name, $rules_info);//避免新版本升级字段不一致可能导致出错
	$rules_info = paddslashes($rules_info);
	if($count > 0){
		$a = DB::update($table_name, $rules_info, array($hash_name => $rules_hash));
	}else{
		if($table_name == 'strayer_picker'){
			$rules_info['article_num'] = $rules_info['visit_url_num'] = $rules_info['article_import_num'] = 0;
		}
		DB::insert($table_name, $rules_info, TRUE);
	}
}

function get_rules_code($table_type){
	$data_array = array();
	$table_type_array = array('1' => 'picker', 2 => 'fastpick', 3 => 'rules', 4 => 'category');
	$query = DB::query("SELECT * FROM ".DB::table('strayer_'.$table_type_array[$table_type]));
	while($rs = DB::fetch($query)) {
		$data_array[] = array('type' => $table_type, 'rules_code' => base64_encode(serialize($rs)));
	}
	return $data_array;
}

function get_data_bak_status($cache){
	$client_info = get_client_info();
	$url = GET_URL.'plugin.php?id=pick_user:api&myac=get_userbak_status&tpl=no&domain='.$client_info['domain'];
	if($cache < 0) {
		cache_del($url);
	}
	$data = get_contents($url, array('cache' => $cache) );
	$data_obj = pick_ajax_decode($data);
	$data_arr = array();
	if($data_obj->update_dateline){
		$data_arr['time'] = dgmdate($data_obj->update_dateline, 'u');
		$data_arr['category_count'] = $data_obj->category_count;
		$data_arr['picker_count'] = $data_obj->picker_count;
		$data_arr['fast_count'] = $data_obj->fast_count;
		$data_arr['system_count'] = $data_obj->system_count;
	}
	return $data_arr;
}


function evo_check(){
	$arr[1]['name'] = milu_lang('open_crul');
	$arr[1]['check'] = 1;
	$arr[1]['msg'] = milu_lang('open_crul_notice');
	if(!function_exists('curl_init')){
		$arr[1]['check'] = 0;
	}
	
	
	$arr[2]['name'] =  milu_lang('open_tow_p');
	$arr[2]['msg'] = milu_lang('no_tow_notice');
	if(function_exists('fsockopen') || function_exists('pfsockopen')){
		$arr[2]['check'] = 1;
	}else{
		$arr[2]['check'] = 0;
		
	}
	$arr[3]['name'] =  'file_get_contents()'.milu_lang('func');
	if(function_exists('file_get_contents')){
		$arr[3]['check'] = 1;
	}else{
		$arr[3]['check'] = 0;
		if ($arr[2]['check'] == 0 && $arr[3]['check'] == 0) $arr[1]['msg'] = '<ul id="tipslis"><li>'.milu_lang('no_use_pick').'</li></ul>';
	}
	
	$arr[4]['name'] =  milu_lang('pick_dir_write');
	$arr[4]['check'] = 1;
	if(!dir_writeable(PICK_PATH.'/data/cache')){
		$arr[4]['check'] = 0;
		$arr[4]['msg'] = '<li>'.milu_lang('dir_no_write', array('dir' => './source/plugin/milu_pick/data/cache')).'</li>';
	}
	if(!dir_writeable(PICK_PATH.'/data/log')){
		$arr[4]['check'] = 0;
		$arr[4]['msg'] .= '<li>'.milu_lang('dir_no_write', array('dir' => './source/plugin/milu_pick/data/log')).'</li>';
	}
	if($arr[4]['msg']) $arr[4]['msg'] = '<ul id="tipslis">'.$arr[4]['msg'].'</ul>'; 
	/*
	$arr[6]['name'] =  '插件文件完整性';
	if($a == $b){
		$arr[6]['check'] = 1;
	}else{
		$arr[6]['check'] = 0;
		$arr[6]['msg'] = '<ul id="tipslis"><li>插件上传过程中，文件丢失，请重新上传文件</li></ul>';
	}
	*/
	
	$arr[7]['name'] =  milu_lang('open_gzinflate');
	$arr[7]['msg'] = milu_lang('no_gzinflate_notice');
	if(function_exists('gzinflate')){
		$arr[7]['check'] = 1;
	}else{
		$arr[7]['check'] = 0;
		
	}
	$arr[8]['name'] =  milu_lang('open_zend');
	if(($zend_re = is_zend()) > 0){
		$arr[8]['check'] = 1;
		$arr[8]['msg'] = milu_lang('zend_notice');
	}else{
		$arr[8]['check'] = 0;
		$arr[8]['msg'] = $zend_re == -1 ? milu_lang('http_visit', array('file' => 'source/plugin/milu_pick/zend/zendcheck.php')) : milu_lang('zend_enable');
	}
	return $arr;
}
//获取服务器参数
function evo_server_config(){
	$get = function_exists('ini_get') ? TRUE : FALSE;
	$memory_str = $get ? ini_get('memory_limit') : '-1';
	if($memory_str >0){
		$m = intval($memory_str);
		$memory_msg = milu_lang('memory_notice');
	}
	$config_arr['php_version'] = array(
		'name' => milu_lang('phpversion'),
		'value' => phpversion(),
		'msg' => '',
		'best_value' => '',
	);
	$config_arr['memory_limit'] = array(
		'name' => milu_lang('php_memory_set'),
		'value' => $memory_str == '-1' ?  milu_lang('un_know') : $memory_str,
		'msg' => $memory_msg,
		'best_value' => '256MB'.milu_lang('set_up'),
 	);
	$dis_fun = $get ? ini_get("disable_functions") : '-1';
	$config_arr['display_function'] = array(
		'name' => milu_lang('no_use_func'),
		'value' => $dis_fun ? ($dis_fun != '-1' ? $dis_fun : milu_lang('un_know')) : milu_lang('no_have'),
	);
	
	$max_time = $get ? ini_get("max_execution_time") : '-1';
	$config_arr['max_execution_time'] = array(
		'name' => milu_lang('time_out_time'),
		'value' => $max_time ? ($max_time != '-1' ? $max_time.milu_lang('sec') : milu_lang('un_know')) : milu_lang('no_limit'),
		'best_value' => milu_lang('no_limit'),
	);
	
	return $config_arr;
}


$_GET['tpl'] = $_GET['tpl'] ? $_GET['tpl'] : 'pick_info';
if($_GET['tpl'] != 'no' && $_GET['tpl']) include template('milu_pick:'.$_GET['tpl']);
?>