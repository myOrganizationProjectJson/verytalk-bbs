<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
define('DEBUG_MODE', 0);
error_reporting(0);
if(!defined('DISCUZ_VERSION')) require_once(DISCUZ_ROOT.'/source/discuz_version.php');
class plugin_milu_pick {
	var $output;
	var $script;
	var $fast_pick_open;//单帖采集是否开启
	var $cron_open;//计划任务是否开启
	var $vir_data_open;//虚拟数据是否开启
	var $set;
	var $milu_set;
	var $pick_set;

	function global_header() {
		global $_G;
		loadcache('milu_pick_setting');
		$set = $_G['cache']['milu_pick_setting'];
		if(!strexists(strtolower($_G['setting']['plugins']['version']['milu_pick']), 'vip')) return;
		if( ( $set['is_cron'] != 1 && $set['is_timing'] != 1 )) return;
		
		if(intval($_GET['cron_run_flag']) > 0) {
			$this->_run_cron(TRUE);//通过图片触发
			exit();
		}
		
		loadcache('milu_cron_info');
		$cron_info = $_G['cache']['milu_cron_info'];
		$cron_info = unserialize($cron_info);
		if(intval($cron_info['auto_pick']) <= TIMESTAMP || intval($cron_info['auto_timing']) <= TIMESTAMP || intval($cron_info['timing_article']) <= TIMESTAMP){//可以执行
			if(checkrobot()){//如果是爬虫触发，直接执行
				$this->_run_cron(TRUE);
			}else{//如果是人触发，图片方式触发
				return '<img width="0" height="0" style="display:none" src="'.$_G['siteurl'].'forum.php?cron_run_flag=1&v='.time().'">';
			}
		}
		
	}
	
	
	function _run_cron($cron_run_flag = 0){
		global $_G;
		$cron_run_flag = $cron_run_flag ? $cron_run_flag : intval($_GET['cron_run_flag']);
		if(!$cron_run_flag) return;
		require_once(DISCUZ_ROOT.'source/plugin/milu_pick/config.inc.php');
		require_once(DISCUZ_ROOT.'source/plugin/milu_pick/lib/vip/pick_cron.class.php');
		loadcache('milu_pick_setting');
		$set = $_G['cache']['milu_pick_setting'];
		loadcache('milu_cron_info');
		$cron_info = $_G['cache']['milu_cron_info'];
		$cron_info = unserialize($cron_info);
		if($set['is_cron'] == 1) {
			if(intval($cron_info['auto_pick']) <= TIMESTAMP) pick_cron::runCron('auto_pick');
		}
		if($set['is_timing'] == 1) {
			if(intval($cron_info['auto_timing']) <= TIMESTAMP) pick_cron::runCron('auto_timing');
			if(intval($cron_info['timing_public']) <= TIMESTAMP) pick_cron::run_timing_public();//全局定时发布
		}	
	}
	
	function _show_output($type = 'bbs') {
		if($_GET['inajax']) return;
		$type = $type == 'bbs' ? $type : 'portal';
		if($this->milu_set['fp_open_mod'][$type] != 1) return;
		if($this->fast_pick_open != 1) return;
		require_once(DISCUZ_ROOT.'source/plugin/milu_pick/config.inc.php');
		$script = "<script charset=\"".CHARSET."\" type=\"text/javascript\">var PICK_URL = SITEURL+'admin.php?".PICK_GO."';var fast_type = '".$type."';</script>";
		if($type == 'portal'){
			$script .= '<script language="javascript" charset="gbk" type="text/javascript" src="static/image/editor/editor_base.js"></script>';
		}
		$script .= '<script language="javascript" charset="gbk" type="text/javascript" src="'.PICK_URL.'static/fast_pick.js?v='.PICK_VERSION.'"></script>';
		$script .= "<script charset=\"".CHARSET."\" type=\"text/javascript\">downloadimg_change_icon('".$type."');</script>";
		$output = '<div class="pbt cl">
			<div class="z"><span><input type="text" onblur="pickFocus()" name="article_url" id="article_url"  autocomplete="off" class="px" value="'.lang('plugin/milu_pick', 'input_url').'" onfocus="pickFocus()"  style="width: 32em" tabindex="1"></span>
			<button type="button" id="fast_pick_get" class="pn" style=" margin-bottom:3px;" value="true" onclick="fast_pick()"><em>'.lang('plugin/milu_pick', 'get_data').'</em></button><span id="pick_loading" style="margin:0 10px;width:300px; float:right;height:20px;line-height:20px;"></span></div></div>';
		$this->output = $output;
		$this->script = $script;
	}
	
	//初始化参数
	function _ini(){
		global $_G;
		require_once(DISCUZ_ROOT.'source/plugin/milu_pick/lib/function.global.php');
		$set = $_G['cache']['plugin']['milu_pick'];
		loadcache('milu_pick_setting');
		$milu_set = $_G['cache']['milu_pick_setting'];
		$pick_set = $_G['cache']['milu_pick_setting'];
		$this->vir_data_open = $milu_set['vir_open'] == 1 ? 1 : 0;
		$this->cron_open = $set['cron_open'];
		$this->set = $set;
		$this->milu_set = $milu_set;
		$this->pick_set = $pick_set;
		$this->vip = strexists(strtolower($_G['setting']['plugins']['version']['milu_pick']), 'vip') ? TRUE : FALSE;
		$this->pick_set['open_seo'] = 0;//强行关掉
		if($this->milu_set['fp_open'] == 2 && $this->milu_set['fp_open'] == 2) return;
		
	}
	
	function _check_open(){
		global $_G;
		if($_GET['inajax']) return;
		$this->_ini();
		$this->fast_pick_open = 0;
		$this->milu_set['fp_forum'] = unserialize($this->milu_set['fp_forum']);
		
		$this->milu_set['fp_usergroup'] = unserialize($this->milu_set['fp_usergroup']);
		if($this->milu_set['fp_open'] == 1){//开启
			$this->milu_set['fp_open_mod'] = unserialize($this->milu_set['fp_open_mod']);
			$this->milu_set['fp_open_mod']['portal'] = in_array(1, $this->milu_set['fp_open_mod']) && $this->vip ? 1 : 0;
			$this->milu_set['fp_open_mod']['bbs'] = in_array(2, $this->milu_set['fp_open_mod']) ? 1 : 0;
			if($this->milu_set['fp_usergroup'] && !in_array($_G['groupid'], $this->milu_set['fp_usergroup'])) {
				$this->fast_pick_open = 0;
				return;
			}	
			if($this->milu_set['fp_forum'] && !in_array($_G['fid'], $this->milu_set['fp_forum']) ) {
				$this->fast_pick_open = 0;
				return;
			}	
			$this->fast_pick_open = 1;
		}
	}
	
	function _article_info($aid = ''){
		$aid = $aid ? $aid : $_GET['pick_aid'];
		$aid = intval($aid);
		if($aid == 0) return;
		require_once(DISCUZ_ROOT.'source/plugin/milu_pick/lib/function.article.php');	
		return pick_article_info($aid);
	}
	
	function _public_add_info($type = 'bbs'){
		global $_G;
		$info = $this->_article_info();
		$pid = intval($_GET['pid']);
		$p_arr = DB::fetch_first("SELECT is_html_public,is_page_public FROM ".DB::table('strayer_picker')." WHERE pid='$pid'");
		$page_flag = $type == 'portal' && $p_arr['is_page_public'] !=1 ? 1 : '<br />';
		if($info['content_arr'] && $info['is_bbs'] != 1){//合并分页
			array_unshift($info['content_arr'], array('cid' => $info['cid'], 'aid'  => $info['aid'], 'pageorder' => 1, 'content' => $info['content'], 'title' => $info['content_arr']['title']));
			ksort($info['content_arr']);
			$info['content'] = content_merge($info['content_arr'], $page_flag);
		}
		if(!$info) return;
		if($type == 'bbs'){
			require_once libfile('function/editor');
			
			
			//分类信息
			$sort_js = '';
			foreach((array)$_G['forum_optionlist'] as $k => $v){
				$info['sort_arr'][$k] = str_replace("'", '\\\'', $info['sort_arr'][$k]);
				$sort_js .= '$("typeoption_'.$v['identifier'].'").value= \''.$info['sort_arr'][$k].'\';';
			}
			
			$is_htmlon = $p_arr['is_html_public'] == 1 ? 1 : 0;
			$info['content'] = content_html_ubb($info['content'], $info['page_url'], $is_htmlon);

			$script .= '<div id="show_title" style="display:none">'.$info['title'].'</div><div id="show_content" style="display:none">'.$info['content'].'</div><script language="javascript" type="text/javascript" >';
			$script .= '
					var subject = $("show_title").innerHTML;
					var message = $("show_content").innerHTML;
					$("subject").value= subject;
					message = message.replace(/<p>([\s\S]*?)<\/p>/ig, "$1<br />");
					message = message.replace(/<center>([\s\S]*?)<\/center>/ig, "[align=center]$1[/align]");
					$(\'e_textarea\').value = message;
					$("subject").focus();';
			$script .= $sort_js;		
			$script .= '</script>';	
		}else if($type == 'portal'){
			$script .= '<div id="show_title" style="display:none">'.$info['title'].'</div><div id="show_content" style="display:none">'.$info['content'].'</div><script language="javascript" type="text/javascript" >';
			$script .= '
					var subject = $("show_title").innerHTML;
					var message = $("show_content").innerHTML;
					$("title").value= subject;
					$("from").value= \''.$this->_public_data($info['from']).'\';
					document.getElementsByName(\'fromurl\')[0].value = \''.$this->_public_data($info['url']).'\';
					document.getElementsByName(\'author\')[0].value = \''.$this->_public_data($info['author']).'\';
					$(\'uchome-ttHtmlEditor\').value  = message;
					var p = window.frames[\'uchome-ifrHtmlEditor\'];
					var obj = p.window.frames[\'HtmlEditor\'];
					obj.document.body.innerHTML = message;
					edit_save();
					$("title").focus()';
			$script .= '</script>';	
		}else if($type == 'blog'){
			$script .= '<div id="show_title" style="display:none">'.$info['title'].'</div><div id="show_content" style="display:none">'.$info['content'].'</div><script language="javascript" type="text/javascript" >';
			$script .= '
					var subject = $("show_title").innerHTML;
					var message = $("show_content").innerHTML;
					$("subject").value= subject;
					document.getElementsByName(\'tag\')[0].value = \''.$this->_public_data($info['article_tag']).'\';
					$(\'uchome-ttHtmlEditor\').value  = message;
					var p = window.frames[\'uchome-ifrHtmlEditor\'];
					var obj = p.window.frames[\'HtmlEditor\'];
					obj.document.body.innerHTML = message;
					edit_save();
					$("subject").focus()';
			$script .= '</script>';	
		}
		return $script;	
	}
	function _public_data($str){
		if(!$str) return;
		return str_replace("'", "\'", $str);
	}
	
	function get_count_data($data){
		if(!strexists($data, ',')) return $data;
		$temp_arr = format_wrap($data, ',');	
		return rand($temp_arr[0], $temp_arr[1]);	
	}
	function get_count_arr($data){
		if(strexists($data, ',')){
			$temp_arr = format_wrap($data, ',');
			$must_arr = range($temp_arr[0], $temp_arr[1]);
		}else if(strexists($data, '|')){
			$must_arr = format_wrap($data, '|');
		}else{
			$must_arr = array($data);
		}
		return $must_arr;
	}
	function get_member_list($num = 0){
		if($num == 0) return array();
		$get_type = $this->milu_set['online_data_from'];
		$must_num = 0;
		if($this->milu_set['vir_must_online']){
			$must_arr = $this->get_count_arr($this->milu_set['vir_must_online']);
		}
		$sql_base = "SELECT uid,username,groupid FROM ".DB::table('common_member');
		$uid_arr = $must_arr;
		$uid_arr = array_map('intval', $uid_arr);
		$query = DB::query($sql_base." WHERE groupid!=9 AND uid IN (".dimplode($uid_arr).")  ORDER BY groupid");
		while(($v = DB::fetch($query))) {
			$must_member_arr[] = $v;
		}
		$must_num = count($must_member_arr);
		if($must_num > $num || $must_num == $num){
			$must_member_arr = array_slice($must_member_arr, 0, $num);
			return $must_member_arr;//既然必须登录的会员凑够数了，没必要再去取
		}
		
		
		$limit_num = (int)$num - $must_num;//没凑够数当然要再获取了
		$limit_str = 'LIMIT 1,'.$limit_num;
		if($get_type == 1 || !$get_type){//所有会员
			
		}else if($get_type == 2){//从用户组
			$this->milu_set['vir_data_usergroup'] = unserialize($this->milu_set['vir_data_usergroup']);
			if($this->milu_set['vir_data_usergroup']){
				$sql = " AND groupid IN (".dimplode($this->milu_set['vir_data_usergroup']).") ";
			}
		}else if($get_type == 3){//自定义
		
			if($this->milu_set['online_data_user_set']){
				if(strexists($this->milu_set['online_data_user_set'], ',')){
					$temp_arr = format_wrap($this->milu_set['online_data_user_set'], ',');
					$temp_arr = array_map('intval', $temp_arr);
					$sql = " AND (uid<'".intval($temp_arr[1])."' AND uid>'".intval($temp_arr[0])."')";
				}else if(strexists($this->milu_set['online_data_user_set'], '|')){
					$temp_arr = format_wrap($this->milu_set['online_data_user_set'], '|');
					$temp_arr = array_map('intval', $temp_arr);
					$sql = " AND uid IN (".dimplode($temp_arr).") ";
				}else{
					$temp_arr = array(intval($this->milu_set['online_data_user_set']));
					$temp_arr = array_map('intval', $temp_arr);
					$sql = " AND uid IN (".dimplode($temp_arr).") ";
				}
			}
		}
		$query = DB::query($sql_base." WHERE  groupid!=9 $sql ORDER BY rand() $limit_str");
		while(($rs = DB::fetch($query))) {
			$must_member_arr[] = $rs;
		}
		return $must_member_arr;
	} 
	function  _jammer($first = 1) {
	 	$rand_arr = $first == 1 ? $this->pick_set['push_content_body_arr'] : $this->pick_set['push_reply_body_arr'];
		$randomstr = $rand_arr[array_rand($rand_arr)];
		return mt_rand(0, 1) && $bbs==1 ? '<font class="jammer">'.$randomstr.'</font>'."<br />" :
		 "<br />".'<span style="display:none">'.$randomstr.'</span>';
	}
	
	function _milu_portal_content_output(){
		global $_G,$content,$article;
		if(!$this->vip || $this->pick_set['open_seo'] != 1) return;
		$this->pick_set['open_seo_mod'] = unserialize($this->pick_set['open_seo_mod']);
		if(!in_array(1, $this->pick_set['open_seo_mod'])) return;
		$aid = intval($_GET['aid']);
		$mod = $_GET['mod'];
		if(empty($aid) && $mod != 'view') return false;
		$seo_arr = $this->_article_seo_output(array('content' => $content['content'], 'title' => $article['title']));
		$content['content'] = $seo_arr['content'];
		$article['title'] = $seo_arr['title'];
	}
	function _article_seo_output($data){
		pload('F:seo');
		$seo_arr = pick_seo_replace($data, 0, 0);
		$arr['content'] = $seo_arr['content'];
		$arr['title'] = $seo_arr['title'];
		$arr['reply'] = $seo_arr['reply'];
		return $arr;
	}
	
}

class plugin_milu_pick_forum extends plugin_milu_pick {
	function plugin_milu_pick_forum(){
		global $isfirstpost;
		$this->_check_open();
		if($isfirstpost != 1 || $this->milu_set['fp_open_mod']['bbs'] != 1) return;
		$this->_show_output();
	}
	function post_top_output() {
		
		if($this->output) return $this->output;
		
	}
	function post_bottom_output() {
		if($_GET['pick_aid']){
			$show = $this->_public_add_info();
			$this->script .= $show;
		}
		if($this->output || $_GET['pick_aid']) return $this->script;
		
	}
	function _load_cache($name){
		global $_G;
		$cache_data = DB::fetch_first("SELECT * FROM ".DB::table('common_syscache')." WHERE cname='$name'");
		return $_G['cache'][$name] = unserialize($cache_data['data']);
	}
	
	function _load_file_cache($name){
		$cache_dir = DISCUZ_VERSION != 'X2' ? 'sysdata' : 'cache';
		$cachefile = DISCUZ_ROOT.'./data/'.$cache_dir.'/'.$name.'.php';
		if(!file_exists($cachefile)) return FALSE;
		@include $cachefile;
		$data = unserialize($c_data);
		return $data;
	}
	//虚拟数据
	function index_top_output() {
		global $_G,$invisiblecount,$todayposts,$postdata,$posts,$whosonline,$detailstatus,$onlinenum,$membercount,$guestcount,$onlineinfo,$forumlist,$showoldetails;
		if($this->vir_data_open != 1) return;
		$bei = (float)$this->milu_set['vir_data_bei'];
		if($this->milu_set['vir_data_bei'] > 0){//数据加倍
			$_G['cache']['userstats']['totalmembers'] = ceil($_G['cache']['userstats']['totalmembers'] * $bei);
			$onlineinfo[0] = ceil($onlineinfo[0] * $bei);
			if($this->milu_set['vir_data_forum']){
				$setting = $this->_load_cache('milu_pick_vir_data');
				if(!$setting || $setting['old_todayposts'] != $todayposts || !$setting['forumlist']){//
					$setting['old_todayposts'] = $todayposts;
					$todayposts = $posts = 0;
					$this->milu_set['vir_data_forum'] = unserialize($this->milu_set['vir_data_forum']);
					$fid_arr = array_keys($forumlist);
					foreach($fid_arr as $fid){
						if(!in_array($fid, $this->milu_set['vir_data_forum'])) continue;
						$forumlist[$fid]['todayposts'] = $forumlist[$fid]['todayposts'] ? ceil($forumlist[$fid]['todayposts'] * $bei) : ceil($forumlist[$fid]['threads'] / 12);
						$forumlist[$fid]['threads'] = ceil($forumlist[$fid]['threads']) * $bei;//主题数
						$forumlist[$fid]['folder'] = 'class="new"';
						$forumlist[$fid]['lastpost']['dateline'] = dgmdate($_G['timestamp'] - rand(0, 100) * 60, 't');
						$forumlist[$fid]['posts'] = ceil($forumlist[$fid]['posts'] * $bei);//回复数
						$todayposts +=  $forumlist[$fid]['todayposts'];//今日帖子
						$posts += $forumlist[$fid]['posts'];//总帖子
					}
					$setting['forumlist'] = base64_encode(serialize($forumlist));
					$setting['posts'] = $posts;
					$setting['todayposts'] = $todayposts;
					$setting['dateline'] = $_G['timestamp'];
					save_syscache('milu_pick_vir_data', $setting);
				}else{
					$posts = ceil($setting['posts']);
					$todayposts = ceil($setting['todayposts']);
					$forumlist = unserialize(base64_decode($setting['forumlist']));
				}
				unset($setting);
			}else{
				$todayposts = ceil($todayposts * $bei);
			}
			loadcache('milu_pick_vir_postdata');
			$setting = $_G['cache']['milu_pick_vir_postdata'];
			$cache_dateline = $setting['dateline'];
			if(date("d", $cache_dateline) != date("d", $_G['timestamp']) || !$setting){
				$postdata[0] = $postdata[0] < $todayposts ? $todayposts + rand(ceil($todayposts*0.05), ceil($todayposts*0.3)) : $postdata[0];
				$setting['postdata'] = $postdata;
				$setting['dateline'] = $_G['timestamp'];
				save_syscache('milu_pick_vir_postdata', $setting);
			}else{
				$postdata = $setting['postdata'];
			}
			
		}
		
		$member_online_count = $this->get_count_data($this->milu_set['vir_online_member_count']);
		$guest_online_count = $this->get_count_data($this->milu_set['vir_online_guest_count']);
		$add_member_count = $add_guest_count = 0;
		if($member_online_count > $membercount){
			$add_member_count = $member_online_count - $membercount;
		}
		if($guest_online_count > $guestcount){
			$add_guest_count = $guest_online_count - $guestcount;
		}
		$setting = $this->_load_file_cache('milu_pick_vir_online');
		$onlinehold = $_G['setting']['onlinehold'] ;//在线保持时间
		$cache_setting = array();
		if (($_G['timestamp'] - $setting['dateline']) > $onlinehold ) $setting = FALSE;
		$detailstatus = $showoldetails == 'yes' || (((!isset($_G['cookie']['onlineindex']) && !$_G['setting']['whosonline_contract']) || $_G['cookie']['onlineindex']) && $member_online_count < 500 && !$showoldetails);
		if($detailstatus){//显示详细的在线会员
			$maxonlinelist = $_G['setting']['maxonlinelist'];
			$max_member_count =  $member_online_count;
			if($member_online_count > $maxonlinelist && $maxonlinelist){
				$max_member_count = $maxonlinelist;
			}
			if($setting['whosonline'] && count($setting['whosonline']) > ($maxonlinelist + 5)) $setting = FALSE;
			if(!$setting['whosonline']){//缓存起来
				$setting = FALSE;
				foreach((array)$whosonline as $k => $v){
					$old_uid_arr[] = $v['uid']; 
				}
				
				loadcache('milu_pick_vir_no_dateal_data');
				if($_G['cache']['milu_pick_vir_no_dateal_data']){
					$membercount = $_G['cache']['milu_pick_vir_no_dateal_data']['add_member_count'];
				}else{
					$membercount = $member_online_count;
				}
				$member_list = $this->get_member_list($max_member_count);
				$result_count = count($member_list);
				if($result_count < $max_member_count) {
					$membercount = $result_count;
					save_syscache('milu_pick_vir_no_dateal_data', array('add_member_count' => $membercount, 'dateline' => $_G['timestamp']) );
				}
				$invisible = 0;
				foreach($member_list as $k => $v){
					if(in_array($v['uid'], $old_uid_arr)) continue;
					$groupid = $v['groupid'];
					$icon = empty($_G['cache']['onlinelist'][$groupid]) ? $_G['cache']['onlinelist'][0] : $_G['cache']['onlinelist'][$groupid];
					$rand_id = rand(1,100);
					$invisible  = $invisiblecount += $rand_id < 3 ? 1 : 0;
					
					$lastactivity = dgmdate($_G['timestamp'] - rand(1, $onlinehold/60) * 60, 't');
					$memberlist = array('uid' => $v['uid'], 'username' => $v['username'], 'groupid' => $_G['groupid'], 'invisible' => $invisible, 'icon' => $icon, 'action' => 2, 'lastactivity' => $lastactivity);
					$whosonline[] = $memberlist;
				}
				$cache_setting['whosonline'] = $whosonline;
				$cache_setting['membercount'] = $membercount;
				$cache_setting['invisiblecount'] = $invisiblecount;
			}else{
				$whosonline = $setting['whosonline'];
				$membercount = $setting['membercount'];
				if($_G['uid']){
					$have = 0;
					foreach($whosonline as $k => $v){
						if($v['uid'] == $_G['uid']) {
							$have = 1;
							break;
						}

					}
					$now_user = array();
					if($have == 0){
						$now_user = $_G['session'];
						$groupid = $_G['groupid'];
						$now_user['icon'] = empty($_G['cache']['onlinelist'][$groupid]) ? $_G['cache']['onlinelist'][0] : $_G['cache']['onlinelist'][$groupid];
						$whosonline[0] = $now_user;
					}
				}
				$invisiblecount = $setting['invisiblecount'];
			}
			
		}else{
			loadcache('milu_pick_vir_no_dateal_data');
			$no_dateil_cache = $_G['cache']['milu_pick_vir_no_dateal_data'];
			$cache_add_member_count = $no_dateil_cache['add_member_count'];
			if (($_G['timestamp'] - $no_dateil_cache['dateline']) > 15*60 ) $cache_add_member_count = FALSE;
			if(!$cache_add_member_count){
				save_syscache('milu_pick_vir_no_dateal_data', array('add_member_count' => $add_member_count, 'dateline' => $_G['timestamp']) );
			}else{
				$add_member_count = $cache_add_member_count;
			}
			$membercount += $add_member_count;
			
		}
		
		$guestcount += $add_guest_count;
		if(!$setting){
			$cache_setting['guestcount'] = $guestcount;
			$cache_setting['dateline'] = $_G['timestamp'];
			require_once libfile('function/cache');
			writetocache('milu_pick_vir_online', getcachevars(array('c_data' => serialize($cache_setting))),'');
		}else{
			$guestcount = $setting['guestcount'];
		}
		$onlinenum = $guestcount+$membercount;
		$onlineinfo[0] = $onlineinfo[0] < $onlinenum ? $onlinenum + $onlineinfo[0] : $onlineinfo[0]; 
		return;
	}
	
	function viewthread_bottom_output(){
		global $_G,$postlist,$navtitle;
		if($this->pick_set['open_seo'] != 1) return;
		$this->pick_set['open_seo_mod'] = unserialize($this->pick_set['open_seo_mod']);
		if(!in_array(2, $this->pick_set['open_seo_mod'])) return;
		$this->pick_set['push_content_body_arr'] = format_wrap($this->pick_set['push_content_body']);
		$this->pick_set['push_reply_body_arr'] = format_wrap($this->pick_set['push_reply_body']);
		foreach($postlist as $pid => $post) {
			if($post['first'] == 1){
				$seo_arr = $this->_article_seo_output(array('title' => $post['subject'], 'content' => $post['message']));
				$thread_title = cutstr($seo_arr['title'], 80);
				$postlist[$pid]['subject'] = $_G['thread']['subject'] = $thread_title;
				$postlist[$pid]['message'] = $seo_arr['content'];
				$short_title = cutstr($seo_arr['title'], 52);
				$navtitle = str_replace($post['subject'], $seo_arr['title'], $navtitle);
				$_G['forum_thread']['short_subject'] = str_replace($_G['forum_thread']['short_subject'], $short_title, $_G['forum_thread']['short_subject']);
				$postlist[$pid]['subject'] = $seo_arr['title'];
			}else{
				$seo_arr = $this->_article_seo_output(array('reply' => $post['message']));
				$postlist[$pid]['message'] = $seo_arr['reply'];
			}
			if(($post['first'] == 1 && $this->pick_set['push_content_body_arr']) || ($post['first'] != 1 && $this->pick_set['push_reply_body_arr']) ) $postlist[$pid]['message'] = preg_replace("/<br \/>|<br>/e", "\$this->_jammer(\$post['first'])", $postlist[$pid]['message']);
			$postlist[$pid]['message'] = str_replace($_G['siteurl'].'magnet:?', 'magnet:?', $postlist[$pid]['message']);
		}
	}
	
}

class plugin_milu_pick_portal extends plugin_milu_pick {
	
	function plugin_milu_pick_portal(){
		$this->_check_open();
		$this->_show_output('portal');
	}
	function portalcp_top_output() {

		if($this->output) return $this->output;
		
	}
	function view_article_content_output(){
		global $_G,$content,$article,$navtitle;
		if(!$this->vip || $this->pick_set['open_seo'] != 1) return;
		$this->pick_set['open_seo_mod'] = unserialize($this->pick_set['open_seo_mod']);
		if(!in_array(1, $this->pick_set['open_seo_mod'])) return;
		$seo_arr = $this->_article_seo_output(array('content' => $content['content'], 'title' => $article['title']));
		$content['content'] = $seo_arr['content'];
		$navtitle = str_replace($article['title'], $seo_arr['title'], $navtitle);
		$article['title'] = $seo_arr['title'];
	}
	

	function portalcp_bottom_output(){
		if($_GET['pick_aid']){
			$show = $this->_public_add_info('portal');
			$this->script .= $show;
		}
		if($this->script || $_GET['pick_aid']) return $this->script;
	}
}


class plugin_milu_pick_home extends plugin_milu_pick {
	
	function plugin_milu_pick_home(){
		
	}
	
	function space_blog_title_output(){
		global $_G,$blog,$navtitle;
		$this->_ini();
		$old_subject = $blog['subject'];
		if(!$this->vip || $this->pick_set['open_seo'] != 1) return;
		$this->pick_set['open_seo_mod'] = unserialize($this->pick_set['open_seo_mod']);
		if(!in_array(3, $this->pick_set['open_seo_mod'])) return;
		$seo_arr = $this->_article_seo_output(array('title' => $blog['subject'], 'content' => $blog['message']));
		$blog['message'] = $seo_arr['content'];
		$navtitle = str_replace($blog['subject'], $seo_arr['title'], $navtitle);
		$blog['subject'] = $seo_arr['title'];
	}
	function spacecp_blog_bottom_output(){
		if(!$_GET['pick_aid']) return;
		$show = $this->_public_add_info('blog');
		return $show;
	}
}	

?>