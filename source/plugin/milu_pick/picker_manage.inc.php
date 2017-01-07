<?php
if(!defined('IN_DISCUZ') ) {
	exit('Access Denied');
}
require_once(DISCUZ_ROOT.'source/plugin/milu_pick/config.inc.php');
pload('F:pick,F:copyright,C:pickOutput,F:article');
if($_G['adminid'] < 1 && !in_array($_GET['myac'], array('api_datazip_download', 'api_curl_zipfile_get', 'api_attach_file_check', 'api_curl_attachfile_get', 'api_datazip_download', 'data_trans_connect', 'api_data_gzip_count', 'api_data_gzip'))) exit('Access Denied:0032');
$header_config = array('pick_list', 'picker_set', 'pick_import', 'pick_data_trans', 'pick_cron_list', 'pick_online');
if($_G['cache']['evn_milu_pick']['pick_config']['open_fanyi_module'] == 1) $header_config = array('pick_list', 'picker_set', 'pick_import', 'pick_data_trans', 'pick_cron_list', 'tran_set', 'pick_online');;
if(!VIP) $header_config = array('pick_list', 'picker_set', 'pick_import', 'pick_online');
$head_url = '?'.PICK_GO.'picker_manage&myac=';
$myaction = $_GET['myaction'];
$myac = $_GET['myac'] ? $_GET['myac'] : $_GET['myfunc'];
$submit = $_GET['submit'];
$pid =  intval($_GET['pid']);
$aid = intval($_GET['aid']);
$optype = $_GET['optype'];
if($myac && $myac != 'pick_list'){
	$tpl = $_GET['tpl'];
	if(!in_array($myac, array('pick_import', 'article_manage', 'tran_set', 'article_batch', 'run_article_repick', 'picker_set', 'pick_data_trans', 'create_trans_key' , 'do_pick_data_trans', 'pick_article_edit', 'article_public_start', 'ajax_func', 'show_article_detail', 'article_repick_one', 'article_delete', 'pick_online', 'import_article', 'pick_cron_list', 'rpcServer', 'api_datazip_download', 'api_curl_zipfile_get', 'api_attach_file_check', 'api_curl_attachfile_get', 'api_datazip_download', 'data_trans_connect', 'api_data_gzip_count', 'api_data_gzip'))) exit('Access Denied:0032');
	if(function_exists($myac)) $info = $myac();
	$mytemp = $_REQUEST['mytemp'] ? $_REQUEST['mytemp'] : ($info['tpl'] ? $info['tpl'] : $myac);
	$submit_pmod = $_GET['pmod'];
	$submit_action = $_GET['myac'];
	if(!$tpl && $tpl!= 'no') include template('milu_pick:'.$mytemp);
	exit();
}

switch($myaction){
	case '':
		$cat_arr = pick_category_list();
		$cat_count = count($cat_arr);
		$picker_count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_picker')), 0);
		$class_show = (($picker_count > 35 && $cat_count > 2) || ($cat_count > 6 && $picker_count > 25)) ? 'hide_all();' : 'show_all();';
		$info['header'] = pick_header_output($header_config, $head_url);
		$info['is_lan'] = check_env(2, 0) ? 'no' : 'yes';
		if($_GET['submit']){
			$pid_arr = $_GET['pid'];
			$pick_op = $_GET['pick_op'];
			$move_cid = $_GET['move_cid'];
			if($pick_op == 'del' || $pick_op == 'move'){
				foreach((array)$pid_arr as $k => $pid){
					if($pick_op == 'del'){
						del_picker($pid);
					}else if($pick_op == 'move'){
						move_picker($pid, $move_cid);
					}
				}
			
			}else{
				$cate_setarr = $_GET['cate_setarr'];
				$newname_arr = $_GET['newname'][0];
				$neworder_arr = $_GET['neworder'][0];
				$pick_setarr = $_GET['pick_setarr'];
				foreach((array)$pick_setarr as $k => $v){
					DB::update('strayer_picker', array('displayorder' => $v['displayorder']), array('pid' => $k));
				}
				foreach((array)$cate_setarr as $k => $v){
					DB::update('strayer_category', $v, array('cid' => $k));
				}
				foreach((array)$neworder_arr as $k => $v){
					DB::insert('strayer_category', array('name' => $newname_arr[$k], 'displayorder' => $v), TRUE);
				}
			}
			cpmsg(milu_lang('op_success'), PICK_GO."picker_manage", 'succeed');
		}
		$info['tips'] = version_check();
		
		$config = pick_common_get();
		$now_list = dunserialize($config['cron_now_info']);
		$today_cron_list = dunserialize($config['cron_today_info']);
		$today_cron_list = $today_cron_list[date('ymd', $_G['timestamp'])];
		$info['now_list_count'] = $info['today_cron_list_count'] = 0;
		foreach((array)$now_list as $k => $v){
			if(!is_array($v)) continue;
			$info['now_list_count'] += count($v);
		}
		$info['today_cron_list_count'] = count($today_cron_list);
		
			
		include template('milu_pick:picker_list');
	break;
	
	case 'picker_data_list':
	$rs_c['cid'] = intval($_GET['cid']);
	$data = array();
	$query = DB::query("SELECT * FROM ".DB::table('strayer_picker')." WHERE pick_cid='$rs_c[cid]' ORDER BY displayorder DESC,pid DESC");
	$pick_set = pick_common_get();//插件设置
	while($rs = DB::fetch($query)) {
		$rs['article_count'] = $rs['article_num'];
		$rs['url_count'] = $rs['visit_url_num'];
		$rs['no_import_count'] =  max(0,$rs['article_num'] - $rs['article_import_num']);
		if(VIP){
			$rs['is_cron_show'] = $rs['is_auto_pick'] == 1 ? milu_lang('_usered') : '';
			$rs['pick_lastrun_show'] = $rs['pick_lastrun'] ? dgmdate($rs['pick_lastrun']) : '';
			$rs['pick_nextrun_show'] = $rs['pick_nextrun'] ? dgmdate($rs['pick_nextrun']): '';
			if($pick_set['is_cron'] !=1 ){
				$rs['pick_lastrun_show'] = '';
				$rs['pick_nextrun_show'] = '';
				$rs['pick_wait_run'] = milu_lang('no_open_cron_pick');
			}
			
			//定时采集
			$rs['is_timing_show'] = $rs['is_auto_timing'] == 1 ? milu_lang('_usered') : '';
			$rs['timing_lastrun_show'] = $rs['timing_lastrun'] ? dgmdate($rs['timing_lastrun']) : '';
			$rs['timing_nextrun_show'] = $rs['timing_nextrun'] ? dgmdate($rs['timing_nextrun']): '';
			$rs['wait_run'] = milu_lang('wait_run');
			if($pick_set['is_timing'] !=1 ){
				$rs['timing_lastrun_show'] = '';
				$rs['timing_nextrun_show'] = '';
 				$rs['timing_wait_run'] = milu_lang('no_open_cron_public');
			}
		}
		$rs['old_name'] = dhtmlspecialchars($rs['name']);
		$rs['name'] = cutstr($rs['old_name'], 60);
		$data[$rs_c['cid']][] = $rs;
	}
	include template('milu_pick:picker_data_list');
	break;
	
	case 'edit_pick':
		include_once libfile('function/portalcp');
		require_once libfile('function/forumlist');
		pload('F:rules');
		$info = get_pick_info();
		$step = intval($_GET['step']);
		if(!$step) $step = 1;
		$info = show_pick_format($info);
		if(!$info['manyou_max_level']) $info['manyou_max_level'] = 2;
		$show_rules = show_rules(1,1);
		$forumselect = '<select name="forums" onchange="getthreadtypes(this.value, 0)">'.forumselect(FALSE, 0, $info['public_class'][0], TRUE).'</select>';
		$threadtypes = getthreadtypes(array('typeid' => $info['public_class'][1], 'fid' => $info['public_class'][0]) );
		$portalselect = category_showselect('portal', 'portal', false, $info['public_class'][0]);
		$blogselect = category_showselect('blog', 'blog', TRUE, $info['public_class'][0]);
		$show_bottom_js = pickOutput::bottom_js_output($info);
		include template('milu_pick:picker_edit');
	break;
	case 'pick_del_category'://删除采集器分类
		if(!submitcheck('deletesubmit')) {
			$cid = intval($_GET['cid']);
		}else{
			$cid = intval($_GET['cid']);
			$to_cid = intval($_GET['to_cid']);
			$category_op = $_GET['category_op'];
			$picker_list = category_picker($cid, 'pid');
			if($category_op == 'move'){
				if($cid == $to_cid) cpmsg_error(milu_lang('picker_del_firm'));
				if($picker_list) {
					foreach($picker_list as $v){
						$pid_arr[] = $v['pid'];
					}
					DB::query('UPDATE '.DB::table('strayer_picker')." SET pick_cid='$to_cid' WHERE pid IN (".dimplode($pid_arr).")");
				}
			}else if($category_op == 'delete'){
				if($picker_list) {
					foreach($picker_list as $v){
						del_picker($v['pid']);
					}
				}
			}
			DB::query('DELETE FROM '.DB::table('strayer_category')." WHERE cid= '$cid'");
			cpmsg(milu_lang('op_success'), PICK_GO."picker_manage", 'succeed');
		}
		$info['header'] = pick_header_output($header_config, $head_url);
		include template('milu_pick:picker_category_del');
	break;
	case 'pick_stop':
	$pid = intval($pid);
	
	$url = PICK_GO.'picker_manage';
	cpmsg(milu_lang('op_success'), $url, 'succeed');
	break;
	case 'get_article':
		$info = get_pick_info();
		$pid = $info['pid'];
		$cache_name = 'pick'.$pid.'_0';
		if($_GET['clear'] || intval($_GET['submit']) == 2) cache_del($cache_name);
		$info['save_data'] =  load_cache($cache_name);
		$info['no_check_url'] = intval($_GET['no_check_url']);
		$info['header'] = pick_header_output($header_config, $head_url);
		include template('milu_pick:get_article');
	break;

	
	case 'pick_empty':	
		if($pid && $submit){
			cache_del('pick'.$pid.'_0');
			article_batch_del($pid);
			DB::query('DELETE FROM '.DB::table('strayer_url')." WHERE pid='$pid'");
			$setarr = array('run_times' => 0, 'pick_lastrun' => 0, 'pick_nextrun' => 0, 'timing_lastrun' => 0, 'timing_nextrun' => 0, 'article_num' => 0, 'article_import_num' => 0, 'visit_url_num' => 0);
			DB::update('strayer_picker', $setarr, array('pid' => $pid));
			del_pick_log($pid);
			cpmsg(milu_lang('empty_finsh'), PICK_GO."picker_manage", 'succeed');
		}else{
			cpmsg(milu_lang('empty_pick_confirm'), PICK_GO.'picker_manage&myaction=pick_empty&pid='.$pid.'&submit=1', 'form');
		}	
	break;
	
	case 'pick_del':
		if($pid && $submit){
			del_picker($pid);
			cpmsg(milu_lang('del_finsh'), PICK_GO."picker_manage", 'succeed');
		}else{
			cpmsg(milu_lang('pick_del_confirm'), PICK_GO.'picker_manage&myaction=pick_del&pid='.$pid.'&submit=1', 'form');
		}	
	break;
	
	case 'export':
		$info['pick'] = get_pick_info();
		$info['pick']['forum_threadtypes'] = get_therad_sort_data($info['pick']['forum_threadtypes'], $info['pick']['forum_threadtype_id']);//加入分类信息
		if($info['pick']['forum_threadtypes'] && is_array($info['pick']['forum_threadtypes'])) $info['pick']['forum_threadtypes'] = serialize($info['pick']['forum_threadtypes']);
		if($info['pick']['rules_hash']){
			pload('F:rules');
			$info['rules'] = get_rules_info($info['pick']['rules_hash']);
		}
		$is_hava = $info['rules'] ? milu_lang('hava_system_rules') : milu_lang('no_hava_system_rules');
		$args = array(
			'type' => milu_lang('dxc_rules'),
			'author' => $_G['setting']['bbname'],
			'rules_name' => $info['pick']['name'],
			'rule_desc' => $is_hava,
		);
		$info['version'] = PICK_VERSION;
		exportfile($info, $info['pick']['name'], $args);
	break;
	case 'article_delete':	
		$aid = intval($_GET['aid']);
		$pid = intval($_GET['pid']);
		$url_str = '&pid='.$pid.'&status='.$_GET['status'].'&p='.$_GET['p'].'&s='.$_GET['s'].'&orderby='.$_GET['orderby'].'&ordersc='.$_GET['ordersc'].'&perpage='.$perpage.'&page='.$page;
		if($aid && $submit){
			article_delete(array($aid), $pid);
			cpmsg(milu_lang('del_finsh'), PICK_GO."picker_manage&myac=article_manage".$url_str, 'succeed');
		}else{
			cpmsg(milu_lang('del_confirm'), PICK_GO.'picker_manage&myaction=article_delete&aid='.$aid.'&submit=1'.$url_str, 'form');
		}	
	break;
}





function article_manage(){
	global $head_url,$header_config;
	$data = article_get_args();
	$info = $data['info'];
	$args = $data['args'];
	$data = get_pick_info();
	$info['public_class'] = unserialize($data['public_class']);
	$info['status'] = $args['status'] ? $args['status'] : intval($_GET['status']);
	$info['pid'] = $_GET['pid'] ? intval($_GET['pid']) : $args['pid'];
	
	if(!VIP) unset($info['status_arr'][4], $info['status_arr'][5]);
	
	foreach($info['status_arr'] as $k => $v){
		$info['a_c'][$k] = pick_article_count($info['pid'], $k);
	}
	$info['oparea'] = $_GET['oparea'];
	$info['optype'] = $_GET['optype'];
	$args['pid'] = $info['pid'];
	
	$article_data = article_list($args);
	$info['pick'] =  $data = get_pick_info();
	if($info['optype'] == 'move_portal'){
		$info['public_class'][0] = $_GET['portal'];
	}else if($info['optype'] == 'move_forums'){
		$info['public_class'][0] = $_GET['forums'];
		$info['public_class'][1] = $_GET['threadtypeid'];
	}else if($info['optype'] == 'move_blog'){
		$info['public_class'][0] = $_GET['blog'];
	}
	
	if($_GET['time_public'] == 1) $info['pick']['public_start_time'] = $info['pick']['public_end_time'] = '';
	$info['p'] = $_GET['p'];//判断是不是从采集器列表进来
	$info['pick']['public_start_time'] = $_GET['public_start_time'] ? $_GET['public_start_time'] : $info['pick']['public_start_time'] ;
	$info['pick']['public_end_time'] = $_GET['public_end_time'] ? $_GET['public_end_time'] : $info['pick']['public_end_time'];
	
	$info['pick']['public_sort'] = $info['pick']['public_sort'] ? $info['pick']['public_sort'] : $_GET['public_sort'];

	$info['pick']['public_start_time'] = dgmdate($info['pick']['public_start_time']);
	$info['pick']['public_end_time'] = dgmdate($info['pick']['public_end_time']);
	$info['pick_select'] = pickOutput::pick_search_select('set[pid]', intval($info['pid']));
	$info['article_move_pick_select'] = pickOutput::pick_search_select('move_pid', intval($_GET['move_pid']), $_GET['pid']);
	$info['rs'] = $article_data['rs'];
	$info['multipage'] = $article_data['multipage'];
	$info['count'] = $article_data['count'];
	if(!$info['p'])$info['header'] = pick_header_output($header_config, $head_url);
	$info['threadtypes'] = getthreadtypes(array('typeid' => $info['public_class'][1], 'fid' => $info['public_class'][0]) );
	$info['forumselect'] = '<select id="forums" name="forums" onchange="getthreadtypes(this.value, 0)">'.forumselect(FALSE, 0, $info['public_class'][0], TRUE).'</select>';
	$info['forumselect_public'] = '<select id="public_forums" name="public_forums" >'.forumselect(FALSE, 0, $info['public_class'][0], TRUE).'</select>';
	$info['portalselect'] = category_showselect('portal', 'portal', FALSE, $info['public_class'][0]);
	$info['blogselect'] = category_showselect('blog', 'blog', TRUE, $info['public_class'][0]);
	$info['public_portalselect'] = category_showselect('portal', 'public_portal', FALSE, $info['public_class'][0]);
	$info['public_blogselect'] = category_showselect('blog', 'public_blog', TRUE, $info['public_class'][0]);
	$url_args = '';
	unset($args['mpurl']);
	foreach((array)$args as $k => $v){
		if($k == 'perpage' || $k == 'pid') continue;
		$url_args .= '&'.$k.'='.$v;
	}
	$info['p_arr'] = $data;
	$info['url_args'] = urlencode($url_args);
	return $info;
}


function article_get_args(){
	global $head_url,$header_config,$_G;
	include_once libfile('function/portalcp');
	require_once libfile('function/forumlist');

	$article_status = $_G['cache']['evn_milu_pick']['article_status'];
	$status = $_GET['status'] ? $_GET['status'] : 0;
	$info['orderby_arr'] = array(
		'default' => milu_lang('default_sort'),
		'dateline' => milu_lang('add_dateline'),
		'pic' => milu_lang('pic_num'),
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
	$info['status_arr'] = $article_status;
	
	$args = $_GET['set'];
	$args['s'] = $args['s'] ? $args['s'] : $_GET['s'];
	$args['status'] = $args['status'] ? $args['status'] : $_GET['status'];
	$args['orderby'] = $args['orderby'] ? $args['orderby'] : $_GET['orderby'];
	$args['orderby'] = $args['orderby'] ? $args['orderby'] : 'default';
	$args['ordersc'] = $args['ordersc'] ? $args['ordersc'] : $_GET['ordersc'];
	$args['ordersc'] = $args['ordersc'] ? $args['ordersc'] : 'desc';
	$args['perpage'] = $args['perpage'] ? $args['perpage'] : $_GET['perpage'];
	$args['perpage'] = $args['perpage'] ? $args['perpage'] : '25';
	$args['pid'] = $_GET['set']['pid'] ? intval($_GET['set']['pid']) : 0;
	$info = array_merge($args, $info);
	$url_args = '';
	foreach((array)$args as $k => $v){
		if($k == 'perpage' || $k == 'pid') continue;
		$url_args .= '&'.$k.'='.$v;
	}
	$config = pick_common_get();
	$info['article_batch_num'] = $config['article_batch_num'] ? $config['article_batch_num'] : 15;
	$args['page'] = $_GET['page'] ? intval($_GET['page']) : 1;
	$args['mpurl'] = $head_url.$_GET['myac'].$url_args;
	$data['args'] = $args;
	$data['info'] = $info;
	
	return $data;
}

//翻译设置
function tran_set(){
	global $head_url,$header_config;
	if(!submitcheck('submit')) {
		$info = pick_common_get();
		$info['tran_api_key'] = dunserialize($info['tran_api_key']);
		$info['tran_aplay_picker'] = dunserialize($info['tran_aplay_picker']);
		$info['header'] = pick_header_output($header_config, $head_url);
		return $info;
	}else{
		$set = $_POST['set'];
		$set['tran_api_key'] = pserialize($_POST['tran_api_key']);
		$set['tran_aplay_picker'] = pserialize($_POST['tran_aplay_picker']);
		$set['tran_user_words'] = trim($set['tran_user_words']);
		if($set['tran_user_words']){
			$word_arr = explode("\r\n", $set['tran_user_words']);
			foreach((array)$word_arr as $k => $v){
				$row_arr = explode('=', $v);
				if(!trim($row_arr[0]) || !trim($row_arr[1])) unset($word_arr[$k]);
			}
			$set['tran_user_words'] = implode("\r\n", $word_arr);
		}
		pick_common_set($set);
		cpmsg(milu_lang('op_success'), PICK_GO."picker_manage&myac=tran_set", 'succeed');	
	}
}

//翻译测试
function tran_api_test($api_type = '', $api_key= '', $tran_open_par = '', $tran_user_words = '', $tran_words = ''){
	pload('F:fanyi');
	$api_type = intval($api_type);
	$tran_open_par = intval($tran_open_par);
	$api_key = rpc_str($api_key);
	$tran_words = rpc_str($tran_words);
	$tran_user_words = rpc_str($tran_user_words);
	$user_words_arr = array();
	if($tran_user_words){//用户自定义翻译
		$user_words_arr = get_ptran_user_words($tran_user_words);
		$tran_words = strtr($tran_words, $user_words_arr[0]);
	}
	if(empty($api_key)) {
		echo milu_lang('apikey_empty');
		return;
	}
	$re = (array)pfanyi_baidu_api($tran_words, array('api_key' => $api_key, 'api_type' => $api_type, 'user_words_arr' => $user_words_arr));
	if($re['error_code']) {
		echo $re['error_msg'];
	}else{
		
		if($tran_open_par == 1) {
			echo str_replace("\r\n", '<br />', $re[0]);
		}else{
			echo str_replace("\r\n", '<br />', $re[1]);
		}
		
	}
	return;
}



function article_repick_one(){
	global $header_config,$head_url;
	$aid = intval($_GET['aid']);
	$pid = intval($_GET['pid']);
	$from_url = $_SERVER['HTTP_REFERER'];
	$article_url = urldecode($_GET['article_url']);
	$info = get_pick_info();
	cache_del('pick'.$pid);
	$info['header'] = pick_header_output($header_config, $head_url);
	$info['pick_submit'] = 1; 
	$info['tpl'] = 'get_article';
	$info['pick_args'] = array('return_url' => $from_url, 'url_arr' => array($aid => $article_url));
	$info['header'] = pick_header_output($header_config, $head_url);
	return $info;
}





function article_bat_list($args, $currow, $pp){
	$limit = '';
	$where = '';
	if($args['oparea'] == 'selected'){
		$aid_arr = $args['aid_arr'];
		$where = " AND aid IN (".dimplode($aid_arr).") ";
	}else if($args['oparea'] == 'all'){
		$where .= !empty($args['s']) ? " AND title like '%".$args['s']."%' " : '';
		$status = $args['status'];
		if($status == 0) {
			$where .= 'AND status<3';
		}else if($status == 1){
			$where .= 'AND status < 2';
		}else if($status !=4){
			$where .= " AND status=".$status;
		}
		$limit = "LIMIT $currow,$pp";
	}
	$query = DB::query("SELECT aid,title FROM ".DB::table('strayer_article_title')." WHERE pid='$args[pid]' $where ORDER by dateline ".$public_sort." $limit");
	$article_arr = array();
	while($rs = DB::fetch($query)) {
		$article_arr[] = $rs;
	}
	return $article_arr;
}

function article_batch(){
	global $_G;
	extract($_GET);
	extract((array)$set);
	$step = intval($step);
	$pp = 20;
	if(!$_GET['step']){
		$p_arr = get_pick_info($pid);
		$article_public_sort = $p_arr['article_public_sort'];
		$args_arr =  array('optype', 'pid', 'is_public_del', 'article_public_sort', 'is_public_del', 'public_start_time', 'public_end_time','status', 's', 'ordersc', 'orderby', 'time_public', 'portal', 'forums', 'threadtypeid', 'blog', 'perpage', 'oparea', 'article_batch_num', 'move_pid','p');
		$args_url = '';
		$cache_data = array();
		foreach($args_arr as $k => $v){
			$args_url .= '&'.$v.'='.$$v;
			$cache_data['args'][$v] = $$v;
		}
		$aid_arr = $aid;
		$cache_data['args']['aid_arr'] = $aid_arr;
		$from_url = PICK_GO."picker_manage&myac=article_manage&finished=1".$args_url;
		$cache_data['from_url'] = $from_url;
		if(!$optype)  cpmsg_error(milu_lang('must_select_optype'));
		if($oparea == 'selected'){
			if(!$aid_arr)  cpmsg_error(milu_lang('must_select_data'));
			$total = count($aid_arr);	
		}else if($oparea == 'all'){
			$total = pick_article_count($cache_data['args']['pid'], $cache_data['args']['status'], array('s' => $cache_data['args']['s']));
		}
		$cache_data['total'] = $total;
		$cache_data['current']['currow'] = 0;
		pcache_data('article_bat_run_normal', $cache_data);
		$step = 1;
		$temp_optype_arr = array('delete', 'move_picker', 'timing_delete', 'repick');
		if(in_array($cache_data['args']['optype'], $temp_optype_arr)) $step = 3;//删除,移动,批量取消定时发布
		
		cpmsg(milu_lang('pre_public_article'), PICK_GO.'picker_manage&myac=article_batch&tpl=no&step='.$step, 'loading', '', false);
	}else if($step == 1){//查询数据(文章发布)
		if(!VIP){
			$today_arr = dunserialize(pick_common_get('', 'pick_today'));
			if($today_arr['day'] != date('md', $_G['timestamp'])){
				$c_set['pick_today'] = array();
				pick_common_set($c_set);
			}else{
				$article_public_num = $today_arr['article_public_num'];
				if($article_public_num > 120) cpmsg_error(milu_lang('article_public_limit', array('n' => 120)));
			}
		}
	
		//usleep(400000);
		$cache_data = (array)pload_cache('article_bat_run_normal');
		$args = $cache_data['args'];
		$currow = intval($cache_data['current']['currow']);
		
		if(!$cache_data || $currow == $cache_data['total'] || $currow > $cache_data['total']) {
			cpmsg(milu_lang('run_finsh'), $cache_data['from_url'], 'succeed');
		}
		$public_sort = $args['article_public_sort'] == 1 ? 'asc' : 'desc';
		if(count($cache_data['article_arr']) == 0){
			$article_arr = article_bat_list($args, $currow, $pp);
		}else{
			$article_arr = $cache_data['article_arr'];
		}
		$info = array_shift($article_arr);
		$aid = $info['aid'];
		$title = $info['title'];
		$cache_data['article_arr'] = $article_arr;
		$title = str_replace(':', '：', $title);
		$cache_data['current'] = array('aid' => $aid, 'title' => $title, 'currow' => $currow);
		pcache_data('article_bat_run_normal', $cache_data);
		cpmsg(milu_lang('bat_i_p_a', array('t' => $title)).milu_lang('bat_import_article', array('t' => $cache_data['total'], 'p' => percent_format($currow, $cache_data['total']))), PICK_GO.'picker_manage&myac=article_batch&tpl=no&step=2', 'loading', '', false);
	}else if($step == 2){//执行操作(文章发布)
		$cache_data = pload_cache('article_bat_run_normal');
		$args = $cache_data['args'];
		$current = $cache_data['current'];
		$aid = intval($current['aid']);
		pick_article_import(array('optype' => $args['optype'], 'aid' => $aid, 'forums' => $args['forums'], 'portal' => $args['portal'], 'blog' => $args['blog'], 'threadtypeid' => $args['threadtypeid'], 'run_type' => 'normal',  'pid' => $args['pid'], 'check_title' => 0));
		$cache_data['current']['currow'] += 1;
		pcache_data('article_bat_run_normal', $cache_data);
		cpmsg(milu_lang('bat_i_p_a_f', array('t' => $current['title'])).milu_lang('bat_import_article', array('t' => $cache_data['total'], 'p' => percent_format($cache_data['current']['currow'], $cache_data['total']))), PICK_GO.'picker_manage&myac=article_batch&tpl=no&step=1', 'loading', '', false);
		
	}else if($step == 3){//删除、移动文章
		$cache_data = pload_cache('article_bat_run_normal');
		$args = $cache_data['args'];
		$limit = '';
		if($args['oparea'] == 'selected'){
			$aid_arr = array_slice($args['aid_arr'], $cache_data['current']['currow'], $pp);
			$cache_data['current']['currow'] += $pp > $cache_data['total'] ? $cache_data['total'] : $pp;
		}else if($args['oparea'] == 'all'){ 
			if($args['s']){
				$limit = "LIMIT 0,$pp";
				$aid_arr = array();
				$name = 'aid';
				if($args['optype'] == 'timing_delete'){
					$where = " AND  a.title like '%".$args['s']."%' ";
					$name = 'id';
					$query = DB::query("SELECT a.aid,t.id, t.data_id FROM ".DB::table('strayer_timing')." t Inner Join ".DB::table('strayer_article_title')." a ON a.aid = t.data_id WHERE t.pid='$args[pid]' $where $limit");
				}else{
					$where = " AND title like '%".$args['s']."%' ";
					$query = DB::query("SELECT aid FROM ".DB::table('strayer_article_title')." WHERE pid='$args[pid]' $where $limit");
				}
				while($rs = DB::fetch($query)) {
					$cache_data['current']['currow'] += 1;
					$aid_arr[] = $rs[$name];
				}
			}else{//如果是全部数据，可以直接通过pid进行查询
				if($args['optype'] == 'delete'){
					article_batch_del($args['pid']);//直接通过pid删除
				}else if($args['optype'] == 'move_picker'){//移动
					article_move_picker($args['pid'], $args['move_pid']);
				}else if($args['optype'] == 'timing_delete'){
					article_timing_delete(array(), $args['pid']);
				}else if($args['optype'] == 'repick'){
					article_repick(array(), $args['pid']);
					return;
				}
				cpmsg(milu_lang('run_finsh'), $cache_data['from_url'], 'succeed');
			}
		}
		if(count($aid_arr) == 0 || $cache_data['current']['currow'] > $cache_data['total']) {
			cache_del('attach');
			cpmsg(milu_lang('run_finsh'), $cache_data['from_url'], 'succeed');
		}
		if($args['optype'] == 'delete'){
			article_delete($aid_arr, $args['pid']);
			article_attach_delete_by_aid($aid_arr);
		}else if($args['optype'] == 'move_picker'){//移动
			article_move_picker($args['pid'], $args['move_pid'], $aid_arr);
		}else if($args['optype'] == 'timing_delete'){
			article_timing_delete($aid_arr, $args['pid']);
		}else if($args['optype'] == 'repick'){
			article_repick($aid_arr, $args['pid']);
		}
		
		pcache_data('article_bat_run_normal', $cache_data);
		cpmsg(milu_lang('bat_import_article', array('t' => $cache_data['total'], 'p' => percent_format($cache_data['current']['currow'], $cache_data['total']))), PICK_GO.'picker_manage&myac=article_batch&tpl=no&step=3', 'loading', '', false);
		
	}
	
	
	
}


function article_repick($aid_arr, $pid){
	$cache_data = pload_cache('article_bat_run_normal');
	$args = $cache_data['args'];
	$aid_args_arr = array();
	$where_sql = $args['oparea'] == 'selected' ? "aid IN (".dimplode($aid_arr).")" : ($args['s'] ? " title like '%".$args['s']."%' " : '1=1');
	$query = DB::query("SELECT aid,url FROM ".DB::table('strayer_article_title')." WHERE pid='$pid' AND $where_sql");
	while($rs = DB::fetch($query)) {
		$aid_args_arr[$rs['aid']] = $rs['url'];
	}
	$pick_args = array('return_url' => 'admin.php?'.$cache_data['from_url'], 'url_arr' => $aid_args_arr);
	cache_data('article_repick', $pick_args);
	data_go('picker_manage&myac=run_article_repick&pid='.$pid);
}

function run_article_repick(){
	global $header_config, $head_url;
	$cache_data_repick = load_cache('article_repick');
	$info = get_pick_info();
	cache_del('pick'.$pid);
	$info['header'] = pick_header_output($header_config, $head_url);
	$info['pick_submit'] = 1; 
	$info['tpl'] = 'get_article';
	$info['pick_args'] = $cache_data_repick;
	return $info;
}



function article_public_start(){
	pick_article_import();
}


function picker_set(){
	global $header_config, $head_url;
	$info = pick_common_get();
	$info['tpl'] = 'common_set';
	if($_POST['editsubmit']){
		$set = $_POST['set'];
		if(!VIP) $set['skydrive_type'] = 0;
		pick_common_set($set);
		save_syscache('milu_cron_info', '');
		cpmsg(milu_lang('op_success'), PICK_GO."picker_manage&myac=picker_set", 'succeed');
	}else{
		if(VIP){
			$show .=  pickOutput::show_tr(
						array(
							'name' => milu_lang('is_cron'),
							'desc' => milu_lang('is_cron_notice'),
							'arr' => array(
								'name' => 'is_cron',
								'info' => $info,
								'int_val' => 2,
								'lang_type' => 2,
							),
						)
						,'radio');
		  $show .=  pickOutput::show_tr(
			  array(
				  'name' => milu_lang('is_timing'),
				  'desc' => milu_lang('is_timing_notice'),
				  'arr' => array(
					  'name' => 'is_timing',
					  'info' => $info,
					  'int_val' => 2,
					  'lang_type' => 2,
				  ),
			  )
			  ,'radio');					
			$show .= pickOutput::show_tr(
						array(
							'name' => milu_lang('is_cron_run_sametime_open'),
							'desc' => milu_lang('is_cron_run_sametime_open_notice'),
							'arr' => array(
								'name' => 'is_cron_run_sametime_open',
								'int_val' => 2,
								'info' => $info,
								'lang_type' => 2,
							),
						)
						,'radio');	
						
			$show .= pickOutput::show_tr(
						array(
							'name' => milu_lang('cron_check_time'),
							'desc' => milu_lang('cron_check_time_notice'),
							'arr' => array(
								'name' => 'cron_check_time',
								'info' => $info,
							),
						)
						,'input');					
										
			$show .=  pickOutput::show_tr(
						array(
							'name' => milu_lang('is_log_cron'),
							'desc' => milu_lang('is_log_cron_notice'),
							'arr' => array(
								'name' => 'is_log_cron',
								'info' => $info,
								'int_val' => 2,
								'lang_type' => 2,
							),
						)
						,'radio');
						
	
			$show .= pickOutput::show_tr(
						array(
							'name' => milu_lang('is_reply_hide_on'),
							'desc' => milu_lang('is_reply_hide_on_notice'),
							'arr' => array(
								'name' => 'is_reply_hide_on',
								'int_val' => 2,
								'info' => $info,
								'lang_type' => 2,
							),
						)
						,'radio');
		}
		$show .= pickOutput::show_tr(
					array(
						'name' => milu_lang('open_tag'),
						'desc' => milu_lang('open_tag_notice'),
						'arr' => array(
							'name' => 'open_tag',
							'int_val' => 2,
							'info' => $info,
							'lang_type' => 2,
						),
					)
					,'radio');			
					
							
		
		if(VIP){
			$show .= pickOutput::show_tr(
						array(
							'name' => milu_lang('skydrive_type'),
							'desc' => milu_lang('skydrive_type_notice'),
							'arr' => array(
								'name' => 'skydrive_type',
								'set_name' => 1,
								'int_val' => 0,
								'flag' => 2, 
								'info' => $info,
								'lang_type' => 1,
								'option_arr' => array(0 => milu_lang('no_use_skydrive'), 1 => milu_lang('baidu_skydrive'), 2 => milu_lang('qiniu_skydrive')),
								 'js' => 'onchange="skydrive_select(this.value)"',
							),
						)
						,'select');
		}
		$show .= '<tbody id="skydrive_show">'.skydrive_output($info['skydrive_type']).'</tbody>';			
	
	}
														
	$info['show'] = $show;			
	$info['header'] = pick_header_output($header_config, $head_url);
	return $info;
}

function skydrive_output($type = 0){
	$info = pick_common_get();
	$type = $_GET['type'] ? intval($_GET['type']) : $type;
	if($type == 0) return;
	if($type == 1){//百度网盘
		
		$show .= pickOutput::add_tr(
					array(
						'name' => milu_lang('about_help'),
						'td' => 2,
					)
					, milu_lang('baidu_skydrive_notice'));	
	
		$show .= pickOutput::show_tr(
					array(
						'name' => 'Bucket',
						'desc' => '',
						'arr' => array(
							'name' => 'baidu_bucket',
							'info' => $info,
						),
					)
					,'input');	
		$show .= pickOutput::show_tr(
					array(
						'name' => 'AK(Access Key)',
						'desc' => '',
						'arr' => array(
							'name' => 'baidu_ak',
							'info' => $info,
						),
					)
					,'input');	
		$show .= pickOutput::show_tr(
					array(
						'name' => 'SK(Secure Key)',
						'desc' => '',
						'arr' => array(
							'name' => 'baidu_sk',
							'info' => $info,
						),
					)
					,'input');	
											
	}else if($type == 2){//七牛云存储
		$show .= pickOutput::add_tr(
					array(
						'name' => milu_lang('about_help'),
						'td' => 2,
					)
					, milu_lang('qiniu_skydrive_notice'));	
	
		$show .= pickOutput::show_tr(
					array(
						'name' => milu_lang('qiniu_bucket'),
						'desc' => '',
						'arr' => array(
							'name' => 'qiniu_bucket',
							'info' => $info,
						),
					)
					,'input');	
		$show .= pickOutput::show_tr(
					array(
						'name' => 'AK(Access Key)',
						'desc' => '',
						'arr' => array(
							'name' => 'qiniu_ak',
							'info' => $info,
						),
					)
					,'input');	
		$show .= pickOutput::show_tr(
					array(
						'name' => 'SK(Secure Key)',
						'desc' => '',
						'arr' => array(
							'name' => 'qiniu_sk',
							'info' => $info,
						),
					)
					,'input');
		$show .= pickOutput::show_tr(
					array(
						'name' => milu_lang('qiniu_skydrive_domain'),
						'desc' => milu_lang('qiniu_skydrive_domain_notice'),
						'arr' => array(
							'name' => 'qiniu_domain',
							'info' => $info,
						),
					)
					,'input');					
	}
	$show .=  pickOutput::add_tr(array(), '<a onclick="skydrive_test('.$type.');" href="javascript:void(0);">'.milu_lang('hit_view_result').'</a>');
	return $show;
}


function skydrive_test(){
	$type = intval($_GET['type']);
	$a = format_url($_GET['a']);
	$b = format_url($_GET['b']);
	$c = format_url($_GET['c']);
	$d = format_url($_GET['d']);
	$file_name = 'sky_test.txt';
	$temp_file = PICK_CACHE.'/'.$file_name;
	file_put_contents($temp_file, 'ok');
	pload('F:attach');
	$object = '/test/'.$file_name;
	$check = FALSE;
	if($type == 1){
		
		$config = array('baidu_bucket' => $a, 'baidu_ak' => $b, 'baidu_sk' => $c);
		$url = baidu_attach_upload($temp_file, $object, $config);
		$result = dfsockopen($url);
		if($result == 'ok') $check = TRUE;
		delete_object($object, $config);
	}else if($type == 2){//七牛云
		$config = array('qiniu_bucket' => $a, 'qiniu_ak' => $b, 'qiniu_sk' => $c, 'qiniu_domain' => $d);
		$url = qiniu_attach_upload($temp_file, $object, $config);
		$check = dfsockopen($url);
		if($result == 'ok') $check = TRUE;
		delete_qiniu_object($object, $config);
	}
	@unlink($temp_file);
	if($check){
		return 1;
	}
	return 0;
}

function pick_data_trans(){
	global $header_config, $head_url,$_G;
	pick_free_access_denied();
	$info = $config = pick_common_get();
	$info['tpl'] = 'common_set';
	if($_POST['editsubmit']){
		$set = $_POST['set'];
		$set['tran_picker_cid'] = intval($_GET['tran_picker_cid']);
		pick_common_set($set);
		cpmsg(milu_lang('connectting_target_server'), PICK_GO.'picker_manage&myac=do_pick_data_trans&step=1', 'loading', '', false);
	}else{ 
		if(!$info['local_key_code']) {
			$info['local_key_code'] = $config['local_key_code'] = random(15);
			pick_common_set($config);
		}
		$show .=  pickOutput::add_tr(
					array(
						'name' => milu_lang('local_key_code'),
						'desc' => milu_lang('local_key_code_notice'),
					)
					, $info['local_key_code'].'<input id="local_key_code" type="hidden" class="txt length_6" name="set[local_key_code]" value="'.$info['local_key_code'].'"> <a  style=" margin-left:15px;" href="?'.PICK_GO.'picker_manage&myac=create_trans_key">'.milu_lang('rebuild').'</a><a onclick="setCopy(\''.$info['local_key_code'].'\', \''.milu_lang('copy_success').'\');return false;" style=" margin-left:15px;" href="javascript:void(0);">'.milu_lang('copy').'</a>');
					
		$show .= pickOutput::show_tr(
					array(
						'name' => milu_lang('target_url'),
						'desc' => milu_lang('target_url_notice'),
						'arr' => array(
							'name' => 'target_url',
							'info' => $info,
						),
					)
					,'input');
					
		$show .= pickOutput::show_tr(
					array(
						'name' => milu_lang('target_key_code'),
						'desc' => milu_lang('target_key_notice'),
						'arr' => array(
							'name' => 'target_key_code',
							'info' => $info,
						),
					)
					,'input');	
					
		$cat_arr = pick_category_list();
		$cat_arr_data_arr = array();	
		foreach($cat_arr as $k => $v){
			$cat_arr_data_arr[$v['cid']] = $v['name'];
		}
		$cat_arr_data_arr[0] = milu_lang('all_data_');
		if($_GET['pid']){
			$info['tran_picker_pid'] = $_GET['pid'] ? intval($_GET['pid']) : $info['tran_picker_pid'];
			$picker_info = get_pick_info($info['tran_picker_pid']);
			$cid = $picker_info['pick_cid'];
			$info['tran_picker_cid'] = $cid;
		}
		ksort($cat_arr_data_arr);
		
		$cat_select_show = pickOutput::select(array('name' => 'tran_picker_cid', 'js' => 'onchange="cat_select_picker(this.value);"', 'flag' =>1, 'int_val' => 0, 'option_arr' => $cat_arr_data_arr,), $info);
		$picker_show = '<span id="picker_select_show">'.picker_list_select($cid, $info['tran_picker_pid']).'</span>';
		
		$show .= pickOutput::show_tr(
					array(
						'name' => milu_lang('tran_picker_select'),
						'td' => '2',
						'html' => $cat_select_show.$picker_show.milu_lang('tran_picker_select_notice'),
					));					
								
		$show .= pickOutput::show_tr(
					array(
						'name' => milu_lang('trans_type'),
						'desc' => milu_lang('trans_type_notice'),
						'arr' => array(
							'name' => 'trans_type',
							'int_val' => 1,
							'info' => $info,
							'lang_arr' => array(milu_lang('upload'), milu_lang('download')),
						),
					)
					,'radio');		

	}
	$info['submit_name'] = milu_lang('pick_data_trans');													
	$info['show'] = $show;			
	$info['header'] = pick_header_output($header_config, $head_url);
	return $info;
}

function picker_list_select($cid, $selected_pid = 0){
	$cid = $_GET['cid'] ? intval($_GET['cid']) : $cid;
	$picker_list = category_picker($cid, 'name,pid');
	$picker_data_arr = array();
	foreach((array)$picker_list as $k => $v){
		$picker_data_arr[$v['pid']] = $v['name'];
	}
	$picker_data_arr[0] = milu_lang('all_data_');
	ksort($picker_data_arr);
	$picker_show = pickOutput::select(array('name' => 'set[tran_picker_pid]', 'flag' =>1, 'int_val' => 0, 'option_arr' => $picker_data_arr,), array('set[tran_picker_pid]' => $selected_pid));
	if($show == 0) return $picker_show;
}




function do_pick_data_trans(){
	global $_G;
	$step = intval($_GET['step']);
	$config = pick_common_get();
	
	$base_url = $config['target_url'].'/plugin.php?id=milu_pick:picker_manage&tpl=no&inajax=1&myac=';
	$i = intval($_GET['i']);
	$count = intval($_GET['count']);
	if($config['trans_type'] == 1 && !function_exists('curl_init')) cpmsg_error(milu_lang('no_open_curl_error'));
	if($step == 1){//连接目标站点
		cache_del('data_tran');
		$content = get_contents($base_url.'data_trans_connect&key='.$config['target_key_code'].'&charset='.urlencode($_G['config']['output']['charset']), array('cache' => -1));
		if(!$content || $content == -1) cpmsg_error(milu_lang('trans_connect_fail'));
		if($content == -2) cpmsg_error(milu_lang('trans_connect_fail2'));
		if($content == 1) {
			cpmsg(milu_lang('pre_trans_data'), PICK_GO.'picker_manage&myac=do_pick_data_trans&step=2', 'loading', '', false);
		}else{
			cpmsg_error(milu_lang('unknow_error'));
		}
	}else if($step > 1 && $step < 6){//传输采集器配置数据
		pload('C:data_trans');
		$data_trans = new data_trans();
		$data_trans->run_start();
	}else if($step == 6){
		cpmsg(milu_lang('trans_data_success'), PICK_GO."picker_manage&myac=pick_data_trans", 'succeed');
	}
}




function data_trans_connect(){
	$key = $_GET['key'];
	$info = pick_common_get();
	if(!$key || $info['local_key_code'] != $key) exit('-2');
	$info['trans_data_charset'] = urldecode($_GET['charset']);//编码
	pick_common_set($info);
	exit('1');
		
}


//接收curl上传的文件，导入里面的数据
function api_curl_zipfile_get(){
	$config = pick_common_get();
	$key = $_POST['key'];
	if(empty($key) || $key != $config['local_key_code']) return -1;
	pload('C:data_trans');
	$data_trans = new data_trans();
	$data_trans->import_data_from_zip($_FILES['curl_file']['tmp_name']);
}



//建立分类表和采集器表的映射关系
function import_data_mapping($temp_data, $key_name, $table_name, $where_name, $cid_arr = array(), $sort_data_arr = array()){
	$data_id_arr = array();
	$data = $key_name == 'cid' ? $temp_data['category'] : $temp_data['picker'];
	foreach((array)$data as $k => $v){
		$key_value = $v[$key_name];
		$where_name_value = paddslashes($v[$where_name]);
		$info = DB::fetch_first("SELECT $key_name FROM ".DB::table($table_name)." WHERE $where_name='".$where_name_value."'");
		if($info[$key_name]){
			$id = $info[$key_name];
		}else{
			if($key_name == 'pid'){//采集器
				$v['pick_cid'] = $cid_arr[$v['pick_cid']];
				$v['forum_threadtype_id'] = $sort_data_arr['sortid'][$v['forum_threadtype_id']];
				$v['forum_threadtypes'] = get_therad_sort_data($v['forum_threadtypes'], $v['forum_threadtype_id']);
				$v['forum_threadtypes'] = dunserialize($v['forum_threadtypes']);
				foreach((array)$v['forum_threadtypes']['get_type'] as $k1 => $v1){
					$get_rules = $v['forum_threadtypes']['get_rules'][$k1];
					unset($v['forum_threadtypes']['get_type'][$k1], $v['forum_threadtypes']['get_rules'][$k1]);
					$optionid = $sort_data_arr['optionid'][$k1];
					$v['forum_threadtypes']['get_type'][$optionid] = $v1;
					$v['forum_threadtypes']['get_rules'][$optionid] = $get_rules;
				}
				$v['forum_threadtypes'] = serialize($v['forum_threadtypes']);
			}
			unset($v[$key_name]);//去掉主键
			$id = DB::insert($table_name, paddslashes($v), TRUE);
		}
		$data_id_arr[$key_value] = $id;
	}
	return $data_id_arr;
}

//分类信息映射关系
function import_threadsort_data_mapping($picker_data){
	$data_id_arr = array();
	foreach($picker_data as $k => $v){
		if(!$v['forum_threadtype_id'] || $v['is_get_threadtypes'] == 2) continue;
		$v['forum_threadtypes'] = dunserialize($v['forum_threadtypes']);
		$check_forum_threadtype_id = get_local_sortid($v['forum_threadtypes'], $v['forum_threadtype_id']);
		if($check_forum_threadtype_id == 0 && $v['forum_threadtypes']){//没有
			$sortid = import_thread_sort($v['forum_threadtypes']['threadsort']['data']);//导入信息分类
			if($sortid > 0){//导入成功
				$data_id_arr['sortid'][$v['forum_threadtype_id']] = $sortid;
			}
		}else{//如果有
			$data_id_arr['sortid'][$v['forum_threadtype_id']] = $check_forum_threadtype_id;
		}
		
		//字段映射
		$identifier_arr = array();
		foreach((array)$v['forum_threadtypes']['threadsort']['data'] as $k1 => $v1){
			$identifier_arr[$v1['identifier']] = $v1['optionid'];//规则里面的
		}
		$query = DB::query("SELECT identifier,optionid FROM ".DB::table('forum_typeoption')." WHERE identifier IN (".dimplode(array_keys($identifier_arr)).")");
		while($rs = DB::fetch($query)) {
			$data_id_arr['optionid'][$identifier_arr[$rs['identifier']]] = $rs['optionid'];
		}

	}
	return $data_id_arr;
}



//接收curl上传的文件，并拷贝到合适的目录
function api_curl_attachfile_get(){
	api_check_pick_key();
	$real_path = base64_decode($_POST['real_path']);
	$name = base64_decode($_POST['name']);
	$dir = str_replace($name, '', $real_path);
	dmkdir($dir);
	$re = @copy($_FILES['curl_file']['tmp_name'], $real_path);
}

function api_check_pick_key(){
	$config = pick_common_get();
	$key = $_POST['key'] ? $_POST['key'] : $_GET['key'];
	if(empty($key) || $key != $config['local_key_code']) exit('-1');
}

function api_attach_file_check(){
	api_check_pick_key();
	$file_list = unserialize(base64_decode($_POST['file_list']));
	$root_dir = base64_decode($_POST['root_dir']);
	pload('C:data_trans');
	$data_trans = new data_trans($args);
	$file_list = $data_trans->check_tran_filelist($file_list, $root_dir);
	$data_trans->picker_data_count_check();//数据校验
	header('file_list:'.base64_encode(serialize($file_list)));
	return;
}


function api_data_gzip_count(){
	api_check_pick_key();
	$args['cid_name'] = base64_decode($_POST['cid_name']);
	$args['picker_hash'] = base64_decode($_POST['picker_hash']);
	pload('C:data_trans');
	$data_trans = new data_trans($args);
	$data_trans->cache = array();
	$data_trans->trans_type = 3;
	$data_trans->get_gzip_file_count();
	header('count:'.$data_trans->count);
	return;
}


//压缩文章数据接口
function api_data_gzip(){
	api_check_pick_key();
	$args['i'] = intval($_POST['i']);
	$args['step'] = intval($_POST['step']);
	$args['cid'] = intval($_POST['cid']);
	$args['pid'] = intval($_POST['pid']);
	pload('C:data_trans');
	$data_trans = new data_trans($args);
	$data_trans->trans_type = 3;
	$article_data = $data_trans->get_article_data();
	$data_trans->array_zip($article_data);
	$data_trans->cache['data_gzip']['zip_arr'] = sarray_unique($data_trans->cache['data_gzip']['zip_arr']);
	$data_gzip_arr = $data_trans->cache['data_gzip'];
	header('data_gzip_arr:'.base64_encode(serialize($data_gzip_arr)));
}

//传输压缩文件接口
function api_datazip_download(){
	$i = intval($_GET['i']);
	api_check_pick_key();
	pload('C:data_trans');
	$data_trans = new data_trans();
	$file_name = $data_trans->cache['data_gzip']['zip_arr'][$i];
	$zip_file = PICK_CACHE.'/data_gzip/'.$file_name;
	$content = @file_get_contents($zip_file);
	@unlink($zip_file);
	exit($content);
}

//获取文件列表
function api_datazip_file_check(){
	api_check_pick_key();
	pload('C:data_trans');
	$args['cid_name'] = base64_decode($_GET['cid_name']);
	$args['picker_hash'] = base64_decode($_GET['picker_hash']);
	$data_trans = new data_trans($args);
	$data_trans->trans_type = 3;
	$file_data = $data_trans->get_filelist_data();
	$root_dir = str_replace('/', '\\', PICK_PATH);
	exit(base64_encode(serialize(array('root_dir' => $root_dir, 'file_list' => $file_data))));
}

//下载附件文件

function api_attach_file_download(){
	api_check_pick_key();
	$real_path = base64_decode($_GET['file_path']);
	$name = base64_decode($_GET['name']);
	$dir = str_replace($name, '', $real_path);
	dmkdir($dir);
	$local_root_dir = str_replace('/', '\\', PICK_PATH);
	$file_url = str_replace($local_root_dir, PICK_URL, $real_path);
	header("HTTP/1.1 303 See Other"); 
    header("Location: $file_url"); 
}


function import_typeoptionvar_data($data_list){
	foreach((array)$data_list as $k => $v){
		$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_typeoptionvar')." WHERE aid='$v[aid]' AND optionid='$v[optionid]'"), 0);
		$v = paddslashes($v);
		if($count > 0){
			$a = DB::update('strayer_typeoptionvar', $v, array('aid' => $v['aid'], 'optionid' => $v['optionid']));
		}else{
			DB::insert('strayer_typeoptionvar', $v, TRUE);
		}
	}
}




function import_table_data($pk_key_name, $table_name, $data_arr){
	$pk_value = $data_arr[$pk_key_name];//主键
	$table_name = 'strayer_'.$table_name;
	$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table($table_name)." WHERE $pk_key_name='$pk_value'"), 0);
	$data_arr = paddslashes($data_arr);
	if($count > 0){
		$a = DB::update($table_name, $data_arr, array($pk_key_name => $pk_value));
	}else{
		$a = DB::insert($table_name, $data_arr, TRUE);
	}
	return $a;
}

function create_trans_key(){
	$set['local_key_code'] = random(15);
	pick_common_set($set);
	cpmsg(milu_lang('op_success'), PICK_GO."picker_manage&myac=pick_data_trans", 'succeed');
}

function article_move_trash($aid_arr, $pid){
	$sql = $pid ? " pid='$pid' AND " : '';
	DB::query("UPDATE ".DB::table('strayer_article_title')." SET status='3' WHERE  $sql aid IN (".dimplode($aid_arr).") ");
}

function article_recover($aid_arr, $pid){
	$sql = $pid ? " pid='$pid' AND " : '';
	$query = DB::query("SELECT aid,forum_id,blog_id,portal_id FROM ".DB::table('strayer_article_title')." WHERE $sql aid IN (".dimplode($aid_arr).") ");
	while($rs = DB::fetch($query)) {
		if($rs['forum_id'] || $rs['portal_id'] || $rs['blog_id']){
			$imported_arr[] = $rs['aid'];
		}else{
			$no_import_arr[] = $rs['aid'];
		}
	}
	if($imported_arr) DB::query("UPDATE ".DB::table('strayer_article_title')." SET status='2' WHERE $sql aid IN (".dimplode($imported_arr).") ");
	if($no_import_arr) DB::query("UPDATE ".DB::table('strayer_article_title')." SET status='1' WHERE $sql aid IN (".dimplode($no_import_arr).") ");
	
}

function article_move_picker($pid, $to_pid, $aid_arr = array()){
	$to_pid = intval($to_pid);
	$sql = $pid ? " pid='$pid' " : '';
	if($aid_arr){
		$sql .= " AND aid IN (".dimplode($aid_arr).")";
	}
	DB::query("UPDATE ".DB::table('strayer_article_title')." SET pid='$to_pid' WHERE $sql ");
	$sql = str_replace('aid', 'tid', $sql);
	//移动附件
	DB::query("UPDATE ".DB::table('strayer_attach')." SET pid='$to_pid' WHERE $sql ");
	pload('C:cache');
	if(!$aid_arr){
		$dir_list = IO::ls(PICK_ATTACH_PATH.'/'.$pid);
		foreach((array)$dir_list as $k => $v){
			$src = $v[1];
			$str = str_replace('/', '\\', $src);
			$str_arr = explode('\\', $str);
			$aid = intval(end($str_arr));
			if($aid == 0) break;
			$dst = PICK_ATTACH_PATH.'/'.$to_pid.'/'.$aid;
			IO::copy($src, $dst);
		}
	}else{
		foreach($aid_arr as $k => $aid){
			$src = PICK_ATTACH_PATH.'/'.$pid.'/'.$aid; 
			$dst = PICK_ATTACH_PATH.'/'.$to_pid.'/'.$aid;
			IO::copy($src, $dst);
		}
	}
	//移动完校验数据
	picker_data_count_check(array($pid, $to_pid));
}


function pick_cron_list(){
	pick_free_access_denied();
	global $head_url,$header_config,$_G;
	loadcache('milu_cron_info', 'true');
	$config = pick_common_get();
	$info['cron_info'] = dunserialize($_G['cache']['milu_cron_info']);
	$info['header'] = pick_header_output($header_config, $head_url);
	//正在执行的任务
	$now_list = dunserialize($config['cron_now_info']);
	$type_lang_arr = array('pick' => 'auto_pick', 'timing' => 'timing', 'update' => '_auto_update');
	$info['now_list'] = array();
	foreach($type_lang_arr as $k0 => $v0){
		foreach((array)$now_list[$k0] as $k => $v){
			$v['type_name'] = milu_lang($type_lang_arr[$k0]);
			$v['pid'] = $k;
			$v['show_start_dateline'] = dgmdate($v['start_dateline'], 'u');
			$info['now_list'][$v['start_dateline']] = $v;
			
		}
	}
	krsort($info['now_list']);
	$info['now_list_count'] = count($info['now_list']);
	
	//今天的执行记录
	$today_cron_list = dunserialize($config['cron_today_info']);
	$today_cron_list = $today_cron_list[date('ymd', $_G['timestamp'])];
	$info['today_cron_list'] = array();
	foreach((array)$today_cron_list as $k => $v){
		if(!$v['type'] || !$v['start_dateline']) continue;
		$v['type_name'] = milu_lang($type_lang_arr[$v['type']]);
		$v['log_url'] = PICK_URL.'data/log/'.$v['type'].'/'.$v['pid'].'/'.date("Y-m-d", time()).'.txt';
		$v['show_start_dateline'] = dgmdate($v['start_dateline'], 'u');
		$v['show_end_dateline'] = dgmdate($v['end_dateline']);
		$v['time'] = diff_time($v['end_dateline'] - $v['start_dateline'], 1);
		$v['time'] = $v['time'] ? $v['time'] : 0;
		$info['today_cron_list'][$v['start_dateline']] = $v;
		
	}
	krsort($info['today_cron_list']);
	$info['today_cron_list_count'] = count($info['today_cron_list']);
	
	return $info;
}

function cron_today_clear(){
	$config = pick_common_get();
	$config['cron_today_info'] = '';
	$config['cron_now_info'] = '';
	save_syscache('milu_pick_setting', $config);
	save_syscache('milu_cron_info', '');//清除
	
}

function article_data_count(){
	global $head_url,$header_config,$_G;
	$info['header'] = pick_header_output($header_config, $head_url);
	return $info;
	
}


function pick_article_edit(){
	global $_G;
	include_once libfile('function/portalcp');
	include_once libfile('function/spacecp');
	include_once libfile('function/home');
	require_once libfile('function/forumlist');
	pload('F:spider,F:article,C:article');
	if(!submitcheck('articlesubmit')) {
		$aid = intval($_GET['aid']);
		$pid = intval($_GET['pid']);
		$data = pick_article_info($aid);
		$p_arr = get_pick_info($pid);
		$p_arr['public_class'] = unserialize($p_arr['public_class']);
		$data['p_arr'] = $p_arr;
		$data['status'] = intval($_GET['status']);
		if(!$data['view_num']){
			$view_arr = format_wrap($p_arr['view_num'], ',');
			if($view_arr) $data['view_num'] = rand($view_arr[0],$view_arr[1]);
		}
		if($data['contents'] > 1){
			array_unshift($data['content_arr'], array('cid' => $data['cid'], 'aid'  => $data['aid'], 'pageorder' => 1, 'content' => $data['content'], 'title' => $data['title']));
			$data['content'] = content_merge($data['content_arr'], 1);
		}
		$timing_info = DB::fetch_first("SELECT * FROM ".DB::table('strayer_timing')." WHERE data_id='$aid'");
		if($timing_info['public_dateline']){
			$data['public_time'] = $timing_info['public_dateline'];
		}
		if(!$data['public_time']){
			$time_arr = create_public_time($data, 1);
			$data['public_time'] = array_pop ($time_arr);
		}
		$data['public_time'] = dgmdate($data['public_time'], 'Y-m-d H:i');
		if(!$data['uid']){
			$rand_arr = get_rand_uid(array('p_arr' => $p_arr));
			$data['uid']  = $rand_arr[0]['uid'] ? $rand_arr[0]['uid'] : $_G['uid'];
			$data['username'] = $rand_arr[0]['username'] ? $rand_arr[0]['username'] : $_G['username'];
		}
		$data['raids'] = unserialize($data['raids']);
		if($data['raids']) {
			$query = DB::query("SELECT title,aid FROM ".DB::table('portal_article_title')." WHERE aid IN (".dimplode($data['raids']).")");
			$list = array();
			while(($value = DB::fetch($query))) {
				$list[$value['aid']] = $value;
				$data['raids_html'] .= '<li id="raid_li_'.$value['aid'].'"><input type="hidden" name="raids[]" value="'.$value['aid'].'" size="5"><a href="portal.php?mod=view&aid='.$value['aid'].'" target="_blank">'.$value['title'].'</a>('.milu_lang('article').' ID: '.$value['aid'].')<a href="javascript:;" onclick="raid_delete('.$value['aid'].');" class="xg1">'.milu_lang('del').'</a></li>';
			}	
		}
		if(!$data['forum_typeid']) $data['forum_typeid'] = $p_arr['public_class'][1];
		$data['threadtypes'] = getthreadtypes(array('typeid' => $p_arr['public_class'][1], 'fid' => $p_arr['public_class'][0]) );
		$data['forumselect'] = '<select id="forums" name="forums" onchange="getthreadtypes(this.value, 0)">'.forumselect(FALSE, 0, $p_arr['public_class'][0], TRUE).'</select>&nbsp;&nbsp;<span id="threadtypes">'.$data['threadtypes'].'</span>';
		$data['portalselect'] = category_showselect('portal', 'portal', '',$p_arr['public_class'][0]);
		$data['blogselect'] = category_showselect('blog', 'blog', '', $p_arr['public_class'][0]);
		$data['article_tags'] = article_parse_tags($data['tag']);
		$data['tag_names'] = article_tagnames();
		$data['show_blog_class'] = get_person_blog_class($data['uid'], $data['blog_small_cid']);
		$data['pid'] = $pid;
		$data['public_type'] = $p_arr['public_type'];
		$data['content'] = dhtmlspecialchars(($data['content']));
		$data['title'] = dhtmlspecialchars($data['title']);
		$data['summary'] = dhtmlspecialchars($data['summary']);
		$data['url_args'] = $_GET['url_args'];
		$data['p_arr']['is_download_img'] = $data['p_arr']['is_download_img'] ? $data['p_arr']['is_download_img'] : $data['is_download_img'];
		$data['p_arr']['is_water_img'] = $data['p_arr']['is_water_img'] ? $data['p_arr']['is_water_img'] : $data['is_water_img'];
		
	}else{//提交
		$_POST = pstripslashes($_POST);
		$_GET = pstripslashes($_GET);
		$setarr = $_POST['set'];
		if(pick_check_uid_exists($setarr['uid']) == 'no') cpmsg_error(milu_lang('user_no_exists'));
		$pick_common_set = get_pick_set();
		$pick_common_set['title_length'] = $pick_common_set['title_length'] ? $pick_common_set['title_length'] : 80;
		$pid = intval($_GET['pid']);
		$p_arr = get_pick_info($pid);
		$setarr['portal_cid'] = $_POST['portal'];
		$setarr['forum_fid'] = $_POST['forums'];
		$setarr['forum_typeid'] = $_POST['threadtypeid'];
		$setarr['blog_big_cid'] = $_POST['blog'];
		$setarr['blog_small_cid'] = $_POST['classid'];
		$setarr['title'] =  getstr(trim($setarr['title']), $pick_common_set['title_length'], 0, 0);
		$setarr['title'] = format_html($setarr['title']);
		if(strlen($setarr['title']) < 1) {
			cpmsg_error(milu_lang('title_no_empty'));
		}
		
		if(empty($setarr['summary'])) $setarr['summary'] = portalcp_get_summary($_POST['message']);
		$setarr['summary'] = addslashes($setarr['summary']);
		$setarr['public_time'] = $setarr['article_dateline'] = strtotime($setarr['public_time']);
		$setarr['from'] = dhtmlspecialchars($setarr['from']);
		$setarr['article_tag'] = dhtmlspecialchars($setarr['article_tag']);
		$setarr['fromurl'] = str_replace('&amp;', '&', dhtmlspecialchars($setarr['fromurl']));
		$aid = intval($_GET['aid']);
		$pid = intval($_GET['pid']);
		$status = intval($_GET['status']);
		$relatedarr = array();
		if($_POST['raids']){
			$relatedarr = array_map('intval', $_POST['raids']);
			$relatedarr = array_unique($relatedarr);
			$relatedarr = array_filter($relatedarr);
			$setarr['raids'] = serialize($relatedarr);
		}
		$setarr['tag'] = article_make_tag($_POST['tag']);
		$setarr['last_modify'] = $_G['timestamp'];
		$user_info = get_user_info($setarr['uid']);
		$setarr['username'] = $user_info['username'];
		
		$article_arr = $setarr;
		DB::update('strayer_article_title', paddslashes($setarr), array('aid' => $aid));
		$article_arr['is_download_img'] = $setarr['is_download_img'];
		$article_arr['is_water_img'] = $setarr['is_water_img'];
		$setarr = array();
		$content = $_POST['message'];
		if(!$_GET['is_bbs']){
			$regexp = '/(###NextPage(\[title=(.*?)\])?###)+/';
			preg_match_all($regexp, $content ,$arr);
			$contents = preg_split($regexp, $content);
			DB::delete('strayer_article_content', "aid='$aid'");
			foreach($contents  as $k => $v){
				$setarr['content'] = trim($v);
				$setarr['pageorder'] = $k+1;
				$setarr['aid'] = $aid;
				$setarr['dateline'] = $_G['timestamp'];
				DB::insert("strayer_article_content", paddslashes($setarr), true);
			}
			DB::update('strayer_article_title', array('contents' => count($contents)), array('aid' => $aid));
		}else{//如果是带回复的
			$setarr['content'] = trim($content);
			DB::update("strayer_article_content", paddslashes($setarr), array('aid' => $aid, 'pageorder' => 1));
		}
		
		if($_POST['public_flag'] != 1){//只是保存文章
			$return_url = '?'.PICK_GO.'picker_manage&myac=article_manage&p=1&pid='.$pid.$_GET['url_args'];
			$return_list_html = '<a href="'.$return_url.'">'.milu_lang('return_list').'</a>';
			$article_view_output = '&nbsp;<span class="pipe">|</span>&nbsp;<a target="_blank" href="'.$article_view_url.'">'.milu_lang('view_article').'</a>';
			cpmsg(milu_lang('save_success').'<br><br><a href="?'.PICK_GO.'picker_manage&myac=pick_article_edit&aid='.$aid.'&pid='.$pid.'">'.milu_lang('continue_edit').'</a>&nbsp;<span class="pipe">|</span>&nbsp;<a href="'.$return_url.'">'.milu_lang('return_list').'</a>', PICK_GO.'picker_manage&myac=pick_article_edit&aid='.$aid.'&pid='.$pid, 'succeed');
			return;
		}
		//
		$run_type = 'article_edit';
		$cache_data['run_type'] = $run_type;
		$cache_data['current'] = array('aid' => $aid, 'title' => $article_arr['title'], 'currow' => 0);
		
		pcache_data('article_bat_run_normal', $cache_data);
		
		$optype_arr = array(1 => 'move_portal', 2 => 'move_forums', 3 => 'move_blog');
		$article_obj = new article(array('optype' => $optype_arr[$_POST['optype']], 'aid' => $aid, 'forums' => $_POST['forums'], 'portal' => $_POST['portal'], 'blog' => $_POST['blog'], 'threadtypeid' => $_POST['threadtypeid'], 'check_title' => 0, 'run_type' => $run_type, 'pid' => $pid));
		$article_obj->cache['p_arr']['is_download_img'] = $article_arr['is_download_img'];
		$article_obj->cache['p_arr']['is_water_img'] = $article_arr['is_water_img'];
		$article_obj->cache['blog_small_cid'] = $article_arr['blog_small_cid'];
		$article_obj->cache['url_args'] = $_POST['url_args'];
		$article_obj->cache['article_info']['public_uid'] = $article_arr['uid'];
		$article_obj->cache['article_info']['public_username'] = $article_arr['username'];
		if($article_arr['public_time']) $article_obj->cache['article_info']['public_time'] = $article_arr['public_time'];
		$article_obj->run_start();
	}
	$data['tpl'] = 'article_edit';
	return $data;
}

function show_article_detail(){
	$aid = intval($_GET['aid']);
	$ar_info = pick_article_info($aid);
	if(!$ar_info['content']) $ar_info['content']= milu_lang('article_content_empty');
	if($ar_info['is_bbs'] == 1){
		$output = $ar_info['content'];
		$p_arr = get_pick_info($ar_info['pid']);
		$member_uid_arr = $member_data_arr =  array();
		$body_user_output = '';
		if($p_arr['is_get_thread_user'] == 1 || $p_arr['is_get_post_user'] == 1){//采集用户信息
			$member_uid_arr[] = $ar_info['uid'];
			foreach($ar_info['content_arr'] as $k => $v){
				$member_uid_arr[] = $v['uid'];
			} 
			
			$member_uid_arr = array_filter($member_uid_arr);
			$member_uid_arr = sarray_unique($member_uid_arr);
			
			if($member_uid_arr){
				$query = DB::query("SELECT get_web_url,uid,get_uid,username FROM ".DB::table('strayer_member')." WHERE uid IN (".dimplode($member_uid_arr).")");
				pload('F:member');
				while(($v = DB::fetch($query))) {
					if(file_exists(PICK_PATH.'/'.get_avatar($v['uid'], 'small'))){
						$v['avatar_url'] = PICK_URL.get_avatar($v['uid'], 'small');
					}
					$member_data_arr[$v['uid']] = $v;
				}
			}
			$user_info = $member_data_arr[$ar_info['uid']];
			$body_user_output = '<div><div class="reply_user"><span style=" margin-left:0;"><a>'.$user_info['username'].'</a></span>发表于<span>'.dgmdate($ar_info['article_dateline']).'</span></div>'.($user_info['avatar_url'] ? '<span style=" float:left; margin-right:10px;"><img src="'.$user_info['avatar_url'].'"></span>' : '').'</div>';
			
		}
		$output .= pickOutput::show_reply_output($ar_info['content_arr'], array('member_data_arr' => $member_data_arr, 'best_answer_key' => $ar_info['best_answer_cid']));
		$output = $body_user_output.$output;
	}else{
		if($ar_info['contents'] == 1){//普通没分页文章
			$output = $ar_info['content'];
		}else{
			array_unshift($ar_info['content_arr'], array('cid' => $ar_info['cid'], 'aid'  => $ar_info['aid'], 'pageorder' => 1, 'content' => $ar_info['content'], 'title' => $ar_info['content_arr']['title']));
			ksort($ar_info['content_arr']);
			$output = pickOutput::show_page_output($ar_info['content_arr']);
		}
	}
	//分类信息
	$sort_output = '';
	if($ar_info['sortid'] && $ar_info['sort_arr']){
		global $_G;
		loadcache(array('threadsort_option_'.$ar_info['sortid']));
		$sortoptionarray = $_G['cache']['threadsort_option_'.$ar_info['sortid']];
		$sort_output = '<div class="typeoption"><table style="width:98%" summary="'.milu_lang('pick_info_class').'" cellpadding="0" cellspacing="0" class="cgtl mbm"><caption>'.milu_lang('pick_info_class').'</caption><tbody>';
		foreach($sortoptionarray as $k => $v){
			$value = $ar_info['sort_arr'][$k];
			if($v['type'] == 'image'){
				$value = '<img src="'.$value.'" />';
			}else if($v['type'] == 'select'){
				
			}else if($v['type'] == 'calendar'){
				
			}
			$sort_output .= '<tr><th>'.$v['title'].':</th><td>'.$value.' </td></tr>';
		}
		$sort_output .= '</tbody></table></div>';

	}
	show_pick_window(dhtmlspecialchars($ar_info['title']), $sort_output.$output, array('w' => 650,'h' => '450','f' => 1));
}


function import_threadtype_data(){
	$type = $_GET['type'];
	$data_id = intval($_GET['data_id']);
	$submit = intval($_GET['submit']);
	if(!$submit) show_pick_window(milu_lang('sure_import_threadtype_data_title'), milu_lang('sure_import_threadtype_data'), array('w' => 250,'h' => 45, 'js_func' => 'import_threadtype_data_ajax(\''.$type.'\', \''.$data_id.'\')'));
	$table_name = $key_name = '';
	if($type == 'picker'){
		pload('F:pick');
		$info = get_pick_info($data_id, 'forum_threadtype_id,forum_threadtypes');
		$table_name = 'picker';
		$key_name = 'pid';
	}else if($type == 'system'){
		pload('F:rules');
		$table_name = 'rules';
		$key_name = 'rid';
		$info = get_rules_info($data_id, 'forum_threadtype_id,forum_threadtypes');
	}else{
		$table_name = 'fastpick';
		$key_name = 'id';
		pload('F:fastpick');
		$info = fastpick_info($data_id, 'forum_threadtype_id,forum_threadtypes');
	}
	$info['forum_threadtypes'] = dunserialize($info['forum_threadtypes']);
	$sortid = import_thread_sort($info['forum_threadtypes']['threadsort']['data']);
	$info['forum_threadtypes'] = forum_thread_data_format($info['forum_threadtypes'], $sortid);
	$info['forum_threadtypes']['threadsort']['sortid'] = $sortid;
	$info['forum_threadtypes'] = serialize($info['forum_threadtypes']);
	if(intval($sortid) > 0){//更新采集器
		DB::update('strayer_'.$table_name, paddslashes(array('forum_threadtype_id' => $sortid, 'forum_threadtypes' => $info['forum_threadtypes'])), array($key_name => $data_id));
	}
	return $sortid;
}


function show_pick_class(){
	return pickOutput::select_output(pick_category_list(TRUE), '', 'move_cid', '', 1);
}
?>