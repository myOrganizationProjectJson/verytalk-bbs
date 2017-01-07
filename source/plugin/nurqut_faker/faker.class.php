<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_nurqut_faker{
	function plugin_nurqut_faker() {
		global $_G;

		$set = $_G['cache']['plugin']['nurqut_faker'];
		foreach ( $set as $key => $value ) {
			$this->$key = $value;
		}
		$this->p_run_area = unserialize($this->p_run_area);
		$this->p_run_member = unserialize($this->p_run_member);
		$this->fake = $this->p_run_fake; /* 造假基数：默认不增加，1表示+1或+1% */
		$this->type = $this->p_run_mode; /* 造假类型：1：百分比增加，2：加法增加 */

		$this->basescript = $_G['basescript'];

		$this->run_offset = $this->basescript == 'forum' && $_G['inajax'] == '0' && !empty($this->p_run_area) && in_array($_G['groupid'], $this->p_run_member);

		if ( $this->run_offset ){
			global $todayposts,$postdata,$posts,$totalmembers, $onlinenum,$membercount,$invisiblecount,$guestcount,$onlineinfo, $forumlist, $sublist;
			loadcache("plugin_nurqut_faker_cache");
			$plugin_nurqut_faker_cache = array();
		}
		if ( $this->run_offset && in_array('1', $this->p_run_area) ) {
			/* 帖子总数 */$posts = $this->fake($posts,1);

			/* 各版块今日帖数 */
			$fakertodayposts = 0;
			$fakertodayfids = 0;
			$count = 0;
			if( empty($sublist) ){
				$fakerforumlist = $forumlist;
			}else{
				$fakerforumlist = $sublist;
			}
			$counts = count($fakerforumlist);
			foreach ($fakerforumlist as $key => $value) {
				$extraplus = 0;
				if( $value['threads'] > 0 ) {
					if( $value['todayposts'] > 0 ) {
						$extraplus = 1;
					}elseif( $this->p_run_mode > 10 ) {
						if($this->p_run_mode == 11) $extraplus = mt_rand(0, (int)($this->p_run_fake/10));
						if($this->p_run_mode == 12) $extraplus = mt_rand(0, $this->p_run_fake);
					}
					$count++;
					$fidoldtodayposts = $value['todayposts'];
					$fidtodayposts = $this->fake( $fidoldtodayposts,1 ) + $extraplus;
					if( empty($sublist) ){
						$forumlist[$key]['todayposts'] = $fidtodayposts;
					}else{
						if( $counts > 1 && $count > 1 ) $fidtodayposts = $fidtodayposts - $extraplus;
						$sublist[$key]['todayposts'] = $fidtodayposts;
					}
					$plugin_nurqut_faker_cache['fid_'.$value['fid']] = $fidtodayposts;
					$fidnewtodayposts = $fidtodayposts - $fidoldtodayposts;
					$fakertodayposts += $fidnewtodayposts;
				}
				$fakertodayfids++;
			}

			/* 今日帖数 */$todayposts += $fakertodayposts;
						  if( $this->p_run_mode > 10 ) if($_G['cache']['plugin_nurqut_faker_cache']['todayposts'] < $todayposts ) $plugin_nurqut_faker_cache['todayposts'] = $todayposts;
						  else $todayposts = $_G['cache']['plugin_nurqut_faker_cache']['todayposts'];
			/* 昨日帖数 */$postdata['0'] = $this->fake( $postdata['0'],1 );
						  if( $this->type == 2 || $this->type == 12 ) $postdata['0'] += (int)(8*$fakertodayfids * $this->fake/10);

			/* cache */
			if( !empty($plugin_nurqut_faker_cache) ){
				$plugin_nurqut_faker_cache['dateline'] = time();
				$_G['cache']['plugin_nurqut_faker_cache'] = array_merge($_G['cache']['plugin_nurqut_faker_cache'],$plugin_nurqut_faker_cache);
				savecache('plugin_nurqut_faker_cache', $_G['cache']['plugin_nurqut_faker_cache']);
			}

			/* fid */
			$this->fid = 'fid_'.$_G['fid'];
			$thisfidtodayposts = $_G['cache']['plugin_nurqut_faker_cache'][$this->fid];
			if( !empty($thisfidtodayposts) ) $_G['forum']['todayposts'] = $thisfidtodayposts;
			else $_G['forum']['todayposts'] = $this->fake( $_G['forum']['todayposts'],1 );
		}

		/* tid views */
		if(  $this->run_offset && in_array('2', $this->p_run_area) ){
			foreach ($_G['forum_threadlist'] as $key => $value) {
				$_G['forum_threadlist'][$key]['views'] = $this->fake( $value['views'],1 );
			}
		}
		if(  $this->run_offset && in_array('3', $this->p_run_area) ){
			/* 会员总数 */$totalmembers = $totalmembers;

			/* 在线隐身 */$oldinvisiblecount = $invisiblecount;$invisiblecount = $this->fake( $invisiblecount,1 );
			/* 在线会员 */$membercount = $membercount + $oldinvisiblecount;
			/* 在线游客 */$guestcount = $this->fake( $guestcount,1 );
			/* 在线总数 */$onlinenum = $membercount + $invisiblecount + $guestcount;
			/* 纪录在线 */$onlineinfo['0'] = $this->fake( $onlineinfo['0'],1 );
			/* 纪录时间 */if( $onlinenum > $onlineinfo['0'] ) $onlineinfo['1'] = date("Y-m-d",time());
		}
	}

	function global_header(){
		return '';
	}
	function fake( $int, $fake = 1, $type = 1 ){
		if( $fake == 1 ) $fake = $this->fake;
		if( $type == 1 ) $type = $this->type;

		if( $type == 1 || $type == 11 ) $return = $int + (int)($int*($fake/100));
		else $return = $int + $fake;

		return $return;
	}
}
class plugin_nurqut_faker_forum extends plugin_nurqut_faker{
	function viewthread_title_extra(){
		global $_G;
		/* tid views */
		if(  $this->run_offset && in_array('2', $this->p_run_area) ){
			$_G['forum_thread']['views'] = $this->fake( $_G['forum_thread']['views'],1 );
		}

		$return = '';
		$tid = intval($_GET['tid']);
		$rand = mt_rand(1,50);
		if( $this->p_addviews_fake_offset && $rand < 21 ){
			$views = $rand;
			$halt = DB::query("UPDATE ".DB::table('forum_threadaddviews')." set `addviews` = `addviews`+$views WHERE `tid` = '$tid'");
			if($halt) $return = 'f+'.$views;
		}
		return $return;
	}
}
?>