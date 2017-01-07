<?php
	if (!defined('IN_DISCUZ')) {
		exit('Access Denied');
	}

	global $_G;
	$setting = $_G['cache']['plugin']['strong_g_mobile'];
	$homeset = $setting['homeset'];
	$homeid = $setting['homeid'];
	$homeurl = 'forum.php?mod=forumdisplay&fid='.$homeid;
	$dh_bg_color = $setting['dh_bg_color'];
	$cbl_bg_l = $setting['cbl_bg_l'];	
	$cbl_bg_r = $setting['cbl_bg_r'];	
	
	$titledescribe = $setting['titledescribe'];
	$titlepic = $setting['titlepic'];
	$titledescribepic = $setting['titledescribepic'];
	$titledescribelen = $setting['titledescribelen'];
	$piclist = $setting['pic'];
	$truepic = $setting['truepic'];
	$leftmenu = $setting['leftmenu'];
	$bottomnav = $setting['bottomnav'];
	loadcache('plugin');


		function isfidtd($fid,$titledescribepic,$titledescribe){
			$titledescribepic = explode (',',$titledescribepic);
			$titledescribe = explode (',',$titledescribe);
			$returntrue = in_array($fid,$titledescribepic)? 1 : 0;
				if ($returntrue){return $returntrue; break; }
			$returntrue = in_array($fid,$titledescribe)? 1 : 0;
				if ($returntrue){return $returntrue; break; }
				return 0;
	}

		function isfidtp($fid,$titledescribepic,$titlepic){
			$titledescribepic = explode (',',$titledescribepic);
			$titlepic = explode (',',$titlepic);
			$returntrue = in_array($fid,$titledescribepic)? 1 : 0;
				if ($returntrue){return $returntrue; break; }
			$returntrue = in_array($fid,$titlepic)? 1 : 0;
				if ($returntrue){return $returntrue; break; }
				return 0;
	}

		function ispic($fid,$piclist){
			$piclist = explode (',',$piclist);
			$returntrue = in_array($fid,$piclist)? 1 : 0;
				if ($returntrue){return $returntrue; break; }

				return 0;
	}



	function setthreadpic($tid){
		foreach (DB::fetch_all('SELECT tableid FROM '.DB::table('forum_attachment').' WHERE tid = '.$tid) as $setthread){
			foreach (DB::fetch_all('SELECT * FROM '.DB::table('forum_attachment_'.$setthread['tableid'].'').' WHERE tid = '.$tid . '  LIMIT  0 , 3 ') as $setthreadpic){
					$setthreadpicarray[$setthreadpic['aid']] = 'data/attachment/forum/'.$setthreadpic['attachment'];
		}
	}
	return $setthreadpicarray;
	}


	function tagsreplace($tid,$titledescribelen){

		foreach ($postmessage= DB::fetch_all('SELECT message FROM '.DB::table('forum_post').' WHERE tid = ' . $tid . ' and first = 1 ') as $value){

			$postmessagearray =$value['message'];
		}
		$tagsreplace[0] = $postmessagearray;
		$tagsreplace = preg_replace('/\[\/?.{0,12}\=?(\w*\-?\/*\.?\,?\s?)*\]/','',$tagsreplace);
		$tagsreplace = preg_replace('/\[\/?.{0,12}\=?\W\]/','',$tagsreplace);
		
		$tagsreplace = implode('',$tagsreplace);

		return cutstr(strip_tags($tagsreplace),$titledescribelen);
	}


?>
