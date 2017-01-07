<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
function article_list($arr){
	global $_G;
	include_once libfile('function/portalcp');
	if(!$arr) return;
	
	$status = $arr['status'] ? $arr['status'] : intval($_GET['status']);
	$pid = $arr['pid'] ? $arr['pid'] : intval($_GET['pid']);
	$perpage = $arr['perpage'] ? $arr['perpage'] : 45;
	if($pid){
		if($status == 4){
			$json_sql = "Inner Join ".DB::table('strayer_timing')." AS t ON a.aid = t.data_id Inner Join ".DB::table('strayer_picker')." AS p ON t.pid = p.pid WHERE t.pid='$pid' ";
			$p_field = ',t.*,p.name';
		}else{
			$json_sql = "Inner Join ".DB::table('strayer_picker')." AS p ON p.pid = a.pid WHERE a.pid='$pid' ";
			$p_field = ',p.pid,p.pick_cid,p.name';
		}
	}else{
		$json_sql = '';
		$s_sql .= ' WHERE  1=1 ';
	}
	
	if($status == 0) {
		$s_sql .= 'AND a.status < 3';
	}else if($status == 1){
		$s_sql .= 'AND a.status < 2';
	}else if($status !=4){
		$s_sql .= " AND a.status=".$status;
	}
	
	if($arr['s']){
		$s_sql .= " AND a.title like '%".$arr['s']."%' ";
	}
	$arr['orderby'] = ($arr['orderby'] != 'default' && $arr['orderby']) ? $arr['orderby'] : 'aid';
	$order_sql = ' ORDER BY a.'.$arr['orderby'].' '.$arr['ordersc'];
	
	$page = $_GET['page'] ? intval($_GET['page']) : 1;
	
	$start = ($page-1)*$perpage;
	$perpages = array($perpage => ' selected');
	$mpurl = $arr['mpurl'] ? $arr['mpurl'].'&pid='.$pid : '?'.PICK_GO.'picker_manage&myaction=article_manage&pid='.$pid.'&status='.$status;
	$mpurl .= '&p='.$_GET['p'].'&perpage='.$perpage;
	$count = pick_article_count($pid,$arr['status'], $arr);
	if($count) {
		if($status == 5){//定时回复
			$like_sql = $arr['s'] ? "AND c.content like '%".$arr['s']."%'" : '';
			$query = DB::query("SELECT t.*,c.* FROM ".DB::table('strayer_timing')." as t Inner Join ".DB::table('strayer_article_content')." AS c ON c.cid = t.data_id WHERE  t.content_type='2' AND t.pid='$pid' $like_sql ORDER BY t.public_dateline ASC LIMIT $start,$perpage ");
			while(($v = DB::fetch($query))) {
				$public_info = dunserialize($v['public_info']);
				$v['author'] = $public_info['author'];
				$v['authorid'] = $public_info['authorid'];
				$v['full_title'] = $public_info['title'];
				$v['title'] = cutstr(trim($public_info['title']), 35);
				$v['short_content'] = cutstr(_striptext(trim($v['content'])), 35);
				$v['short_content'] = $v['short_content'] ? $v['short_content'] : $v['content'];
				$v['content'] = dhtmlspecialchars($v['content']);
				if($arr['s']){
					$v['short_content'] = str_replace($arr['s'], '<span style="color:red">'.$arr['s'].'</span>',$v['short_content']);
				}
				$v['public_dateline'] = $v['public_dateline'] ? dgmdate($v['public_dateline']) : '';
				$data['rs'][] = $v;
			}
		}else{
			$query = DB::query("SELECT a.*".$p_field." FROM ".DB::table('strayer_article_title')." AS a ".$json_sql.$s_sql.$order_sql." LIMIT $start,$perpage ");	
			while(($v = DB::fetch($query))) {
				$v['full_title'] = $v['title'];
				$v['title'] = cutstr(trim($v['title']), 50);
				if($v['pic'] > 0){
					$v['title'] = $v['title'].'&nbsp;<img src="static/image/filetype/image_s.gif" alt="attach_img" title="'.milu_lang('img_article').'" align="absmiddle">';
				}
				if($arr['s']){
					$v['title'] = str_replace($arr['s'], '<span style="color:red">'.$arr['s'].'</span>',$v['title']);
				}
				$v['dateline'] = dgmdate($v['dateline']);
				$v['attach_filesize_count'] = sizecount($v['attach_filesize_count']);
				$v['last_modify'] = $v['last_modify'] ? dgmdate($v['last_modify']) : milu_lang('no_modify');
				$v['public_time'] = $v['public_time'] ? dgmdate($v['public_time']) : milu_lang('no_public');
				$v['public_dateline'] = $v['public_dateline'] ? dgmdate($v['public_dateline']) : '';
				if(!$v['name']){
					$pick_info = article_get_picker_info($v['pid']);
					$v['name'] = $pick_info['name'];
				}
				
				$data['rs'][] = $v;
			}
		}
	}
	$data['multipage'] = multi($count, $perpage, $page, $mpurl);	
	return $data;
}


function article_get_picker_info($pid, $field = 'name'){
	if(!$pid) return array();
	return DB::fetch_first("SELECT $field FROM ".DB::table('strayer_picker')." WHERE pid='$pid'");
}

function pick_article_count($pid = 0,$status=0, $args = array()){
	if($pid){
		if($status == 4){
			$json_sql = "Inner Join ".DB::table('strayer_timing')." AS t ON a.aid = t.data_id WHERE t.pid='$pid' ";
			$p_field = ',t.*';
		}else if($status == 5){//定时回复
			$like_sql = $args['s'] ? "AND c.content like '%".$args['s']."%'" : '';
			return  DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_timing')." as t Inner Join ".DB::table('strayer_article_content')." AS c ON c.cid = t.data_id WHERE  t.content_type='2' AND t.pid='$pid' $like_sql"), 0);
		}else{
			$json_sql = "Inner Join ".DB::table('strayer_picker')." AS p ON p.pid = a.pid WHERE a.pid='$pid'";
			$p_field = ',p.pid,p.pick_cid,p.name';
		}
	}else{
		$json_sql = '';
		$sql .= ' WHERE  1=1 ';
	}
	if($status == 0) {
		$sql .= 'AND a.status < 10';
	}else if($status == 1){
		$sql .= 'AND a.status < 2';
	}else if($status != 4){
		$sql .= " AND a.status=".$status;
	}
	if($args['s']){
		$sql .= " AND a.title like '%".$args['s']."%' ";
	}
	return DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_article_title')." AS a $json_sql ".$sql), 0);
}

function get_timing_count($pid = 0){
	if($pid) $where = " WHERE pid='$pid'";
	return DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_timing').$where), 0);
}

function get_timing_data($args = array()){
	global $_G;
	$pid = $args['pid'] ? $args['pid']: $_GET['pid']; 
	$count = get_timing_count($pid);
	if($count) {
		
	}
}

function pick_article_info($aid){
	$arr = DB::fetch_first("SELECT * FROM ".DB::table('strayer_article_title')." WHERE aid='$aid'");
	if(!$arr) return FALSE;
	if($arr['aid']){
		//读取内容
		$content_arr = article_content_data($aid);
		//读取分类信息
		if($arr['sortid']) $sort_data = article_sort_data($aid);
	}
	$key_arr = array_keys($content_arr);
	$key = array_shift($key_arr);
	$content_info = $content_arr[$key];
	unset($content_arr[$key]);
	$arr['cid'] = $content_info['cid'];
	$arr['content'] = $content_info['content'];
	$arr['content_arr'] = $content_arr;
	$arr['sort_arr'] = $sort_data;
	return $arr;
}

function article_content_data($aid){
	$query =  DB::query("SELECT * FROM ".DB::table('strayer_article_content')." WHERE aid='".$aid."' ORDER BY pageorder ASC");
	$data_arr = array();
	while(($v = DB::fetch($query))) {
		$data_arr[$v['cid']] = $v;	
	}
	return $data_arr;
}

function article_sort_data($aid){
	$query =  DB::query("SELECT * FROM ".DB::table('strayer_typeoptionvar')." WHERE aid='".$aid."'");
	$data_arr = array();
	while(($v = DB::fetch($query))) {
		$data_arr[$v['optionid']] = $v['value'];	
	}
	return $data_arr;
}




function get_person_blog_class($uid = '',$now_id = ''){
	global $_G;
	include_once libfile('function/spacecp');
	$uid = $uid ? $uid : intval($_GET['uid']);
	$classarr = $uid?getclassarr($uid):getclassarr($_G['uid']);
	$output = '<select name="classid" id="classid" onchange="addSort(this)" ><option value="0">------</option>';
	foreach((array)$classarr as $key => $value){
		if ($value['classid'] == $now_id) {
			$output .= '<option value="'.$value[classid].'" selected>'.$value[classname].'</option>';
		}else{
			$output .= '<option value="'.$value[classid].'">'.$value[classname].'</option>';
		}
	}	
	$output .= '<option value="addoption" style="color:red;">+'.milu_lang('add_class').'</option>';
	$output .= '</select>';
	return $output;
}

function pick_check_uid_exists($uid = ''){
	global $_G;
	$uid = $uid ? $uid : intval($_GET['uid']);
	if(!$uid) return 'no';
	$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('common_member')." WHERE uid = '$uid' "), 0);
	if($count == 0){
		return 'no';
	}else{
		return $count;
	}
}



//文章批量删除
function article_batch_del($pid){
	article_attach_delete_by_pid($pid);
	$query = DB::query("SELECT aid,status FROM ".DB::table('strayer_article_title')."  WHERE pid ='$pid' ");
	$article_num = $article_import_num = 0;
	while(($v = DB::fetch($query))) {
		$article_num++;//要更改的文章数
		if($v['status'] == 2){
			$article_import_num++;
		}
		article_delete($v['aid'], $pid, 2);
	}
	picker_data_count_check(array($pid));
}



//删除多篇文章(同一个pid)
function article_delete($aid_arr, $pid, $is_del_attach = 1){
	if(!$aid_arr) return ;
	if(!is_array($aid_arr)) $aid_arr = array($aid_arr);
	if($is_del_attach == 1) article_attach_delete_by_aid($aid_arr, $pid);
	DB::query('DELETE FROM '.DB::table('strayer_article_title')." WHERE $sql aid IN (".dimplode($aid_arr).")");
	DB::query('DELETE FROM '.DB::table('strayer_article_content')." WHERE aid IN (".dimplode($aid_arr).")");
	if($is_del_attach == 1) picker_data_count_check(array($pid));
	
}

//删除文章附件 通过pid
function article_attach_delete_by_pid($pid){
	DB::query('DELETE FROM '.DB::table('strayer_attach')." WHERE pid='$pid'");
	pload('C:cache');
	IO::rm(PICK_ATTACH_PATH.'/'.$pid);
}

//删除文章附件，通过aid
function article_attach_delete_by_aid($aid_arr, $pid){
	if(!$aid_arr) return ;
	if(!is_array($aid_arr)) $aid_arr = array($aid_arr);
	DB::query('DELETE FROM '.DB::table('strayer_attach')." WHERE aid IN (".dimplode($aid_arr).")");
	pload('C:cache');
	foreach($aid_arr as $k => $aid){
		IO::rm(PICK_ATTACH_PATH.'/'.$pid.'/'.$aid);
	}
}

//删除定时发布
function article_timing_delete($id_arr, $pid = ''){
	if(!is_array($id_arr)) $id_arr = array($id_arr);
	if(count($id_arr) == 0 && $pid){
		DB::query('DELETE FROM '.DB::table('strayer_timing')." WHERE pid='$pid'");
		DB::update('strayer_article_title', array('status' => 1), array('pid' => $pid, 'status' => 4));
	}else{
		if($pid) $sql = " pid='$pid' AND ";
		$query = DB::query("SELECT data_id as aid FROM ".DB::table('strayer_timing')." WHERE $sql id IN (".dimplode($id_arr).")");
		$aid_arr = array();
		while($rs = DB::fetch($query)) {
			$aid_arr[] = $rs['aid'];
		}
		//去掉文章下面的回复
		$query = DB::query("SELECT cid  FROM ".DB::table('strayer_article_content')." WHERE aid IN (".dimplode($aid_arr).")");
		$cid_arr = array();
		while($rs = DB::fetch($query)) {
			$cid_arr[] = $rs['cid'];
		}
		if($cid_arr) DB::query('DELETE FROM '.DB::table('strayer_timing')." WHERE $sql content_type=2 AND data_id IN (".dimplode($cid_arr).")");
		DB::query('DELETE FROM '.DB::table('strayer_timing')." WHERE $sql id IN (".dimplode($id_arr).")");
		DB::query("UPDATE ".DB::table('strayer_article_title')." SET status='1' WHERE  $sql aid IN (".dimplode($aid_arr).") ");
	}
}

function article_trash($aid_arr,$status=3){
	if(!$aid_arr) return ;
	foreach($aid_arr as $aid){
		DB::update('strayer_article_title', array('status' => $status), array('aid' => $aid));
	}
}


function pick_article_import($args = array()){
	pload('C:article');
	$article_obj = new article($args);
	$article_obj->run_start();
}

function get_user_info($uid=0){
	global $_G;
	if($uid == 0) $uid =  $_G['uid'];
	return DB::fetch_first("SELECT * FROM ".DB::table('common_member')." WHERE uid='$uid'");
}




function article_timing_add($args){
	extract($args);
	if(!$data_id || !$content_type || !$public_type) return;
	$check = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_timing')." WHERE data_id='$data_id' AND content_type='$content_type' AND public_type='$public_type'"), 0);
	if($check){
		DB::update('strayer_timing', paddslashes($args), array('data_id' => $data_id));
	}else{
		return DB::insert('strayer_timing', paddslashes($args), TRUE);
	}
}



function create_public_time($arr = array(),$num = 1, $is_reply = 0){
	global $_G;
	$p_arr = $arr['p_arr'];
	$time_type = $p_arr['public_time_type'];
	if($is_reply != 1) {
		if(!$time_type || $time_type == 1){//发布时的时间
			$re_arr[] = $_G['timestamp'];
		}else if($time_type == 2){// 采集到的时间
			$re_arr[] = $arr['article_dateline'] ? $arr['article_dateline'] : $_G['timestamp']; 			
		}else if($time_type == 3){//随机时间段
			$time_pre = '1234321';//这是代表 - 符号
			$p_arr['public_start_time'] = strexists($p_arr['public_start_time'], $time_pre) ? $_G['timestamp'] - 3600 * str_replace($time_pre, '', $p_arr['public_start_time']) : ( $p_arr['public_start_time'] > (TIMESTAMP - 20*365*24*3600) ? $p_arr['public_start_time'] : $_G['timestamp'] + $p_arr['public_start_time'] * 3600 );
			$p_arr['public_end_time'] = $p_arr['public_end_time'] > (TIMESTAMP - 20*365*24*3600) ? $p_arr['public_end_time'] : $_G['timestamp'] + $p_arr['public_end_time'] * 3600;
			
			$re_arr[] = rand($p_arr['public_start_time'], $p_arr['public_end_time']);
		}
	}else{
		$reply_time_arr = explode(',', $p_arr['reply_dateline']);
		if(count($reply_time_arr) == 1) {
			$reply_time_arr[1] = $reply_time_arr[0] * 3600; 
			$reply_time_arr[0] = 30*60; 
		}else{	
			$reply_time_arr[0] = $reply_time_arr[0] ? $reply_time_arr[0] * 3600 : 30*60;
			$reply_time_arr[1] = $reply_time_arr[1] ? $reply_time_arr[1] * 3600 : 3600*2;
		}
		for($i = 0;$i < $num;$i++){
			$re_arr[$i] = $arr['public_time'] + rand($reply_time_arr[0], $reply_time_arr[1]);
		}
	}
	sort($re_arr);
	return $re_arr;
}




//获取随机用户 需要的参数 public_uid，p_arr, reply_num
function get_rand_uid($args, $type = 'public'){
	global $_G;
	$p_arr = $args['p_arr'];
	$uid_set_rules = $p_arr[$type.'_uid'];
	$uid_set_type = $p_arr[$type.'_uid_type'];
	$public_uid = $args['public_uid'];
	$reply_num = $args['reply_num'];
	$no_uid_sql = $public_uid ? "uid<>'$public_uid' AND " : '';
	if($set_arr['uid']) {
		$sql = 'AND uid != '.$set_arr['uid'];
	}
	if($reply_num > 0 && $type == 'reply' && !$uid_set_rules && $p_arr['is_public_reply'] == 1) {//如果不填写的话，系统自动随机设定
		$max_uid = DB::result(DB::query("SELECT MAX(uid) FROM ".DB::table('common_member')." WHERE groupid!=9 "), 0);
		$uid_set_rules = '1,'.$max_uid;	
		$uid_set_type = 2;
	}
	$num = 1 + $reply_num;
	$limit_str = $num ==1 ? "limit 1" : "limit 0,$num";
	if($uid_set_type == 1){//用户组
		$uid_group = $p_arr[$type.'_uid_group'];
		$uid_group_arr = dunserialize($uid_group);
		$g_sql = '';
		if($uid_group_arr[0]){
			$g_sql = " WHERE $no_uid_sql groupid IN (".dimplode($uid_group_arr).") "	;
		}else{
			$g_sql = " WHERE $no_uid_sql groupid!=9 ";
		}
		$query = DB::query("SELECT uid,username FROM ".DB::table('common_member').$g_sql." ORDER BY rand() $limit_str");
		while(($v = DB::fetch($query))) {
			$arr[] = $v;
		}
	}else{
		if(strexists($uid_set_rules, '|')){
			$uid_arr = explode('|', $uid_set_rules);
			$uid_arr = array_filter($uid_arr);
		
			$query = DB::query("SELECT uid,username FROM ".DB::table('common_member')." WHERE $no_uid_sql uid IN (".dimplode($uid_arr).") ".$sql." AND groupid!=9 ORDER BY rand() $limit_str");
			while(($v = DB::fetch($query))) {
				$arr[] = $v;
			}
		}else if(strexists($uid_set_rules, ',')) {
			$range_arr = format_wrap($uid_set_rules, ',');
			$max = intval($range_arr[1]);
			$min = intval($range_arr[0]);
			if(!$max || !$min || $max < 0 || $min < 0 || (($max - $min) < 0 )) return $now_arr;
			$query = DB::query("SELECT uid,username FROM ".DB::table('common_member')." WHERE $no_uid_sql uid<$max AND uid>$min ".$sql." AND groupid!=9 ORDER BY rand() $limit_str");
			while(($v = DB::fetch($query))) {
				$arr[] = $v;
			}
		}else{//只填一个
			$info = get_user_info($uid_set_rules);
			$now_arr[0]['uid'] = $info['uid'];
			$now_arr[0]['username'] = $info['username'];
			if($num == 1) return $now_arr;
			for($i = 1; $i< $num+1; $i++){
				$arr[] = $now_arr[0];
			}
		}
	}	
	
	if(!$arr[0]['uid']){//如果都获取不到，使用当前登录的uid
		$now_arr[0]['uid'] = $_G['uid'];
		$now_arr[0]['username'] = $_G['username'];
		return $now_arr;
	}
	return $arr;
}

//合并多个内容
//###NextPage###
function content_merge($content_arr, $page_flag = "<br />"){
	$page_flag_str = '';
	$n = 0;
	foreach($content_arr as $k => $v){
		$title = $v['title'] ? '[title='.$v['title'].']' : '';
		if($n > 0) $page_flag_str = $page_flag == 1 ? '###NextPage'.$title.'###' : '';
		$new_arr[$k] = $page_flag_str.$v['content'];
		$n++;
	}
	$push = $page_flag!=1 ? $page_flag : '';
	return implode($push, $new_arr);
}


//重新改写discuz的html2bbcode函数，不然有些标签被他过滤掉了
function pick_html2bbcode($text) {
	//处理pre标签
	require_once libfile('function/editor');
	$pre_arr = $blockcode_arr = array();
	if(strexists($text, '</pre>')){
		preg_match_all("/<pre.*>(.*)?<\/pre>/isU", $text, $pre_arr, PREG_SET_ORDER);
		if($pre_arr){
			$replace_key = 'DXCPICKPRE_';
			$replace_arr = array();
			foreach($pre_arr as $k => $v){
				$replace_arr['_old'][] = $v[0];
				$replace_arr['old'][] = str_replace($v[1], $replace_key.$k, $v[0]);
				$replace_arr['key'][] = $replace_key.$k;
				$v[1] = strip_tags($v[1], '');
				//$v[1] = str_replace('<br/>', "", $v[1]);
				$replace_arr['new'][] = $v[1];
			}
			$text = str_replace($replace_arr['_old'], $replace_arr['old'], $text);
		}
	}
	//处理blockcode标签
	if(strexists($text, '<div class="blockcode">')){
		preg_match_all("/<div class=\"blockcode\">(.*)?<\/em><\/div><br \/>/isU", $text, $blockcode_arr, PREG_SET_ORDER);
		if($blockcode_arr){
			$replace_key = 'DXCPICKCODE_';
			$blockcode_replace_arr = array();
			foreach($blockcode_arr as $k => $v){
				$blockcode_replace_arr['_old'][] = $v[0];
				$blockcode_replace_arr['old'][] = '[code]'.$replace_key.$k.'[/code]';
				$blockcode_replace_arr['key'][] = $replace_key.$k;
				$v[1] = strip_tags($v[1], '<li>');
				$v[1] = str_replace(array('<li>', '复制代码'), array("\r\n", ''), $v[1]);
				$blockcode_replace_arr['new'][] = '[code]'.$v[1].'[/code]';
			}
			$text = str_replace($blockcode_replace_arr['_old'], $blockcode_replace_arr['old'], $text);
		}
	}
	
	$text = strip_tags($text, '<table><tr><td><b><strong><i><em><u><a><div><span><p><strike><blockquote><pre><ol><ul><li><font><img><br><br/><h1><h2><h3><h4><h5><h6><script>');
	if(ismozilla()) {
		$text = preg_replace("/(?<!<br>|<br \/>|\r)(\r\n|\n|\r)/", ' ', $text);
	}
	$pregfind = array(
		"/<script.*>.*<\/script>/siU",
		'/on(mousewheel|mouseover|click|load|onload|submit|focus|blur)="[^"]*"/i',
		"/(\r\n|\n|\r)/",
		"/<table([^>]*(width|background|background-color|bgcolor)[^>]*)>/siUe",
		"/<table.*>/siU",
		"/<tbody.*>/siU",//这里新增
		"/<\/tbody>/i",//这里新增
		"/<blockquote.*>/siU",//这里新增
		"/<\/blockquote>/i",//这里新增
		"/<pre.*>/siU",//这里新增
		"/<\/pre>/i",//这里新增
		"/<tr.*>/siU",
		"/<td>/i",
		"/<td(.+)>/siUe",
		"/<\/td>/i",
		"/<\/tr>/i",
		"/<\/table>/i",
		'/<h([0-9]+)[^>]*>/siUe',
		'/<\/h([0-9]+)>/siU',
		"/<img[^>]+smilieid=\"(\d+)\".*>/esiU",
		"/<img([^>]*src[^>]*)>/eiU",
		"/<a\s+?name=.+?\".\">(.+?)<\/a>/is",
		"/<br.*>/siU",
		"/<span\s+?style=\"float:\s+(left|right);\">(.+?)<\/span>/is",
	);
	$pregreplace = array(
		'',
		'',
		'',
		"tabletag('\\1')",
		'[table]',
		'',//这里新增
		'',//这里新增
		'[quote]',//这里新增
		'[/quote]',//这里新增
		'[code]',//这里新增
		'[/code]',//这里新增
		'[tr]',
		'[td]',
		"tdtag('\\1')",
		'[/td]',
		'[/tr]',
		'[/table]',
		"\"[size=\".(7 - \\1).\"]\"",
		"[/size]\n\n",
		"smileycode('\\1')",
		"pick_imgtag('\\1')",
		'\1',
		"\n",
		"[float=\\1]\\2[/float]",
	);
	$text = preg_replace($pregfind, $pregreplace, $text);
	//处理pre标签
	if($pre_arr) $text = str_replace($replace_arr['key'], $replace_arr['new'], $text);
	
	//处理blockcode标签
	if($blockcode_arr) $text = str_replace($blockcode_replace_arr['old'], $blockcode_replace_arr['new'], $text);
	
	$text = recursion('b', $text, 'simpletag', 'b');
	$text = recursion('strong', $text, 'simpletag', 'b');
	$text = recursion('i', $text, 'simpletag', 'i');
	$text = recursion('em', $text, 'simpletag', 'i');
	$text = recursion('u', $text, 'simpletag', 'u');
	$text = recursion('a', $text, 'atag');
	$text = recursion('font', $text, 'fonttag');
	$text = recursion('blockquote', $text, 'simpletag', 'indent');
	$text = recursion('ol', $text, 'listtag');
	$text = recursion('ul', $text, 'listtag');
	$text = recursion('div', $text, 'divtag');
	$text = recursion('span', $text, 'spantag');
	$text = recursion('p', $text, 'ptag');


	$pregfind = array("/(?<!\r|\n|^)\[(\/list|list|\*)\]/", "/<li>(.*)((?=<li>)|<\/li>)/iU", "/<p><\/p>/i", "/(<a>|<\/a>|<\/li>)/is", "/<\/?(A|LI|FONT|DIV|SPAN)>/siU", "/\[url[^\]]*\]\[\/url\]/i", "/\[url=javascript:[^\]]*\](.+?)\[\/url\]/is");
	$pregreplace = array("\n[\\1]", "\\1\n", '', '', '', '', "\\1");
	$text = preg_replace($pregfind, $pregreplace, $text);

	$strfind = array('&nbsp;', '&lt;', '&gt;', '&amp;');
	$strreplace = array(' ', '<', '>', '&');
	$text = str_replace($strfind, $strreplace, $text);
	return dhtmlspecialchars(trim($text));
}

//discuz这个函数有个bug，https开头的图片转换有问题，所以得重写
function pick_imgtag($attributes) {
	$value = array('src' => '', 'width' => '', 'height' => '');
	preg_match_all("/(src|width|height)=([\"|\']?)([^\"']+)(\\2)/is", dstripslashes($attributes), $matches);
	if(is_array($matches[1])) {
		foreach($matches[1] as $key => $attribute) {
			$value[strtolower($attribute)] = $matches[3][$key];
		}
	}
	@extract($value);
	if(!preg_match("/^(http:|https:)\/\//i", $src)) {
		$src = absoluteurl($src);
	}
	return $src ? ($width && $height ? '[img='.$width.','.$height.']'.$src.'[/img]' : '[img]'.$src.'[/img]') : '';
}



if(!function_exists('portalcp_article_pre_next')){
	function portalcp_article_pre_next($catid, $aid) {
		$data = array(
			'preaid' => C::t('portal_article_title')->fetch_preaid_by_catid_aid($catid, $aid),
			'nextaid' => C::t('portal_article_title')->fetch_nextaid_by_catid_aid($catid, $aid),
		);
		if($data['preaid']) {
			C::t('portal_article_title')->update($data['preaid'], array(
				'preaid' => C::t('portal_article_title')->fetch_preaid_by_catid_aid($catid, $data['preaid']),
				'nextaid' => C::t('portal_article_title')->fetch_nextaid_by_catid_aid($catid, $data['preaid']),
				)
			);
		}
		return $data;
	}
}




function article_attach_by_aid($aid){
	$query =  DB::query("SELECT * FROM ".DB::table('strayer_attach')." WHERE tid='$aid' ");
	$data_arr = array();
	while(($v = DB::fetch($query))) {
		$data_arr[$v['url_hash']] = $v;
	}
	return $data_arr;
}





function get_attach_data($url, $message){
	if(!$message) return array();
	$url = $base_url ? $base_url : $url;
	$data =  array();
	preg_match_all("/\<a(.*)?>(.*)?<\/a>/isU", $message , $attach_arr, PREG_SET_ORDER);
	if(!$attach_arr) return array();
	foreach($attach_arr as $k => $v){
		$info = get_attach_info($v[1], $url);
		$data[$k][0] = $v[0];
		$data[$k][1] = $info['href'];
		$data[$k][2] = $v[2];
		$data[$k][3] = $info['title'];
		$data[$k][4] = 1;
	}
	return $data;
}


function get_attach_info($attributes, $page_url) {
	global $_G;
	if(!$attributes) return;
	$value = array('title' => '', 'href' => '');
	preg_match_all('/(title|href)=([\"|\'])?(.*?)(?(2)\2|\s)/is', stripslashes($attributes), $matches);
	if(is_array($matches[1])) {
		foreach($matches[1] as $key => $attribute) {
			$value_name = strtolower($attribute);
			$value_value = trim($matches[3][$key]);
			if($value_name == 'href'){
				//磁力链接不补全
				if($_G['cache']['evn_milu_pick']['no_expandlinks_urls'] && !filter_something($value_value, $_G['cache']['evn_milu_pick']['no_expandlinks_urls'], TRUE)){
					
				}else{
					$value_value = _expandlinks($value_value, $page_url);
				}
			}
			$value[$value_name] = $value_value;
		}
	}
	return $value;

}


function dz_get_tag($subject, $message, $return_array = 0){
	if(empty($subject) && empty($message)) return FALSE;
	$subjectenc = rawurlencode(strip_tags($subject));
	$message = clear_ad_html($message);
	$message = strip_tags(preg_replace("/\[.+?\]/U", '', $message));
	$message = cutstr($message, 960, '');
	$messageenc = rawurlencode($message);
	$data = @implode('', file("http://keyword.discuz.com/related_kw.html?ics=".CHARSET."&ocs=".CHARSET."&title=$subjectenc&content=$messageenc"));
	if(!$data) return FALSE;
	
	if(PHP_VERSION > '5' && CHARSET != 'utf-8') {
		require_once libfile('class/chinese');
		$chs = new Chinese('utf-8', CHARSET);
	}

	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $data, $values, $index);
	xml_parser_free($parser);

	$kws = array();

	foreach($values as $valuearray) {
		if($valuearray['tag'] == 'kw' || $valuearray['tag'] == 'ekw') {
			$kws[] = !empty($chs) ? $chs->convert(trim($valuearray['value'])) : trim($valuearray['value']);
		}
	}
	if($return_array) return $kws;
	$return = '';
	if($kws) {
		foreach($kws as $kw) {
			$kw = dhtmlspecialchars($kw);
			$return .= $kw.' ';
		}
		$return = dhtmlspecialchars($return);
	}
	return $return;
}
?>