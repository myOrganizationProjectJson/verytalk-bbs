<?php
if(!defined('IN_DISCUZ') ) {
	exit('Access Denied');
}
require_once(DISCUZ_ROOT.'source/plugin/milu_pick/config.inc.php');
pload('F:pick,F:copyright,C:pickOutput');
global $_G;
if($_G['adminid'] < 1 ) exit('Access Denied:0032');
if($_GET['pid']){
	$info = get_pick_info();
	$info = show_pick_format($info);
}else{
	num_limit('strayer_picker', 35, 'p_num_limit');
}
if($_GET['turn_type']){
	$info = get_trun_data();
	$info['rules_type'] = 2;
	$info['theme_url_test'] = $info['theme_url_test'] ? $info['theme_url_test'] : $info['detail_ID_test'];
}
$step = $_GET['step'];
if(!$step) $step = 1;
$info['time_out'] = $pick_config['time_out'];
if(!$info['reply_max_num']) $info['reply_max_num'] = '5,35';
include_once libfile('function/portalcp');
require_once libfile('function/forumlist');
$threadtypes = getthreadtypes(array('typeid' => $info['public_class'][1], 'fid' => $info['public_class'][0]) );
$forumselect = '<select id="forums" name="forums" onchange="getthreadtypes(this.value, 0)">'.forumselect(FALSE, 0, $info['public_class'][0], TRUE).'</select>';
$portalselect = category_showselect('portal', 'portal', FALSE, $info['public_class'][0]);
$blogselect = category_showselect('blog', 'blog', TRUE, $info['public_class'][0]);
$show_bottom_js = pickOutput::bottom_js_output($info);
$info['pick_cid'] = $info['pick_cid'] ? $info['pick_cid'] : intval($_GET['pick_cid']);
$save_to_pick = intval($_GET['save_to_pick']);
if(submitcheck('editsubmit')){
	$setarr = $_POST['set'];
	$setarr = pstripslashes($setarr);
	$setarr['rules_var'] = pserialize($_POST['rules_var']);
	//分类信息
	$_POST['forum_threadtypes'] = get_therad_sort_data($_POST['forum_threadtypes'], $setarr['forum_threadtype_id']);
	$setarr['forum_threadtypes'] = pserialize($_POST['forum_threadtypes']);
	$setarr['content_filter_html'] = pserialize($_POST['content_filter_html']);
	$setarr['reply_filter_html'] = pserialize($_POST['reply_filter_html']);
	$setarr['many_page_list'] = pserialize($_POST['many_page_list']);
	$setarr['title_filter_rules'] = pserialize($_POST['title_filter_rules']);
	$setarr['content_filter_rules'] = pserialize($_POST['content_filter_rules']);
	$setarr['reply_filter_rules'] = pserialize($_POST['reply_filter_rules']);
	$strtotime_public_start_time = strtotime($setarr['public_start_time']);
	$time_pre = '1234321';//这是代表 - 符号
	$setarr['public_start_time'] = intval($setarr['public_start_time']);
	if($setarr['public_start_time'] < 0){
		$setarr['public_start_time'] = $time_pre.abs($setarr['public_start_time']);
	}else{
		$setarr['public_start_time'] = !$strtotime_public_start_time && $setarr['public_start_time'] ? $setarr['public_start_time'] : $strtotime_public_start_time;
	}
	$strtotime_public_end_time = strtotime($setarr['public_end_time']);
	$setarr['public_end_time'] = !$strtotime_public_end_time && $setarr['public_end_time'] ? $setarr['public_end_time'] : $strtotime_public_end_time;
	$setarr['public_uid_group'] = pserialize($setarr['public_uid_group']);
	$setarr['reply_uid_group'] = pserialize($setarr['reply_uid_group']);
	if($setarr['public_type'] == 1){
		$setarr['public_class'][0] = intval($_GET['portal']);
	}else if($setarr['public_type'] == 2){
		$setarr['public_class'][0] = intval($_GET['forums']);
		$setarr['public_class'][1] = intval($_GET['threadtypeid']);
	}else if($setarr['public_type'] == 3){
		$setarr['public_class'][0] = intval($_GET['blog']);
	}
	
	if(VIP){
		//计划任务
		pload('C:pick_cron');
		$pick_cron = new pick_cron();
		$setarr = array_merge($setarr, (array)$pick_cron->get_cron_value($_POST, 'pick'));
		//定时发布
		$setarr = array_merge($setarr, (array)$pick_cron->get_cron_value($_POST, 'timing'));
	}
	
	$setarr['public_class'] = pserialize($setarr['public_class']);
	if(empty($setarr['name'])) cpmsg_error(milu_lang('pick_name_no_empty'));
	if($_GET['pid'] && $_GET['add'] != 'copy'){
		$pid = $_GET['pid'] ;
		if(empty($setarr['rules_hash'])) $setarr['rules_hash'] = '';
		if(empty($setarr['page_url_auto'])) $setarr['page_url_auto'] = 0;
		$msg = milu_lang('modify');
		if(empty($setarr['reply_is_extend'])) $setarr['reply_is_extend'] = 0;
		if(empty($setarr['is_use_thread_setting'])) $setarr['is_use_thread_setting'] = 0;
		$setarr = paddslashes($setarr);
		$data_info = get_pick_info();
		if($data_info['pick_cron_loop_type'] != $setarr['pick_cron_loop_type'] || $data_info['pick_cron_loop_daytime'] != $setarr['pick_cron_loop_daytime'] || $data_info['timing_cron_loop_type'] != $setarr['timing_cron_loop_type'] || $data_info['timing_cron_loop_daytime'] != $setarr['timing_cron_loop_daytime']){//计划任务修改时，把下次执行时间清空
			save_syscache('milu_cron_info', '');
			$setarr['pick_lastrun'] = $setarr['pick_nextrun'] = 0;
			$setarr['timing_lastrun'] = $setarr['timing_nextrun'] = 0;
			cache_del('pick'.$pid.'_1');
			
			//计划任务，下次执行时间
			if(VIP){
				list($day, $hour, $minute) = explode('-', $setarr['pick_cron_loop_daytime']);
				$setarr['pick_nextrun'] = pick_cron::get_next_time($setarr['pick_cron_loop_type'], $day, $hour, $minute);
				list($day, $hour, $minute) = explode('-', $setarr['timing_cron_loop_daytime']);
				$setarr['timing_nextrun'] = pick_cron::get_next_time($setarr['timing_cron_loop_type'], $day, $hour, $minute);
			}
				
		}
		
		DB::update('strayer_picker', $setarr, array('pid' => $pid));
		$url = PICK_GO.'picker_manage';
	}else{
		$msg = milu_lang('add');
		$setarr = paddslashes($setarr);
		$setarr['picker_hash'] = create_hash();
		$pid = DB::insert('strayer_picker', $setarr, TRUE);
	}
	$url = $save_to_pick != 1 ? PICK_GO.'picker_manage&myaction=edit_pick&pid='.$pid.'&step='.$_GET['step'] :  PICK_GO.'picker_manage&myaction=get_article&pid='.$pid;
	if(!$pid) cpmsg_error($msg.milu_lang('fail'));
	if($save_to_pick == 1) data_go($url);
	cpmsg(milu_lang('pick_op_finsh', array('msg' => $msg)), $url, 'succeed');
}
if(!$info['jump_num'])  $info['jump_num'] = $pick_config['pick_num'];
include template('milu_pick:picker_edit');
?>