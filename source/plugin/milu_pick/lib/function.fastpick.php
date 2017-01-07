<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//��������
function fastpick_info($id='', $field = '*', $table = 'fastpick'){
	global $_G;
	$id = $id ? $id : $_GET['id'];
	$id = intval($id);
	return DB::fetch_first("SELECT $field FROM ".DB::table('strayer_'.$table)." WHERE id='$id'");
}

function fastpick_evo_test(){
	pload('F:spider');
	$id = intval($_GET['id']);
	$rules_info = fastpick_info($id, '*', 'evo');
	$content = get_contents($rules_info['detail_ID_test']);
	$re = evo_rules_get_article($content, $rules_info);
	show_pick_window($re['title'], $re['content'], array('w' => 650,'h' => '460','f' => 1));
}


//�г�һ���ɼ�����
function fastpick_manage(){
	global $head_url,$header_config;
	$page = $_GET['page'] ? intval($_GET['page']) : 1;
	$perpage = 25;
	$start = ($page-1)*$perpage;
	$mpurl .= '&perpage='.$perpage;
	$perpages = array($perpage => ' selected');
	$mpurl = '?'.PICK_GO.'fast_pick&myac=fastpick_manage';
	$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_fastpick')), 0);
	if($count) {
		$query = DB::query("SELECT * FROM ".DB::table('strayer_fastpick')." ORDER BY id DESC LIMIT $start,$perpage ");	
		while(($v = DB::fetch($query))) {
			$v['rules_name'] = dhtmlspecialchars($v['rules_name']);
			$v['rule_desc'] = cutstr(trim($v['rule_desc']), 245);
			$info['rs'][] = $v;
		}
	}
	$info['multipage'] = multi($count, $perpage, $page, $mpurl);
	$info['count'] = $count;
	$info['is_lan'] = check_env(2, 0) ? 'no' : 'yes';
	$info['header'] = pick_header_output($header_config, $head_url);
	return $info;
}

//�༭����
function fastpick_edit(){
	global $head_url,$header_config;
	$id = intval($_GET['id']);
	$copy = $_GET['copy'];
	
	if(!submitcheck('addsubmit')) {
		num_limit('strayer_fastpick', 30, 'f_num_limit');
		$trun_info = get_trun_data();
		$info = $trun_info ? $trun_info : fastpick_info($id);
		$info['theme_url_test'] = $info['theme_url_test'] ? $info['theme_url_test'] : $info['detail_ID_test'];
		$info['title_filter_rules'] = dunserialize($info['title_filter_rules']);
		$info['content_filter_rules'] = dunserialize($info['content_filter_rules']);
		$info['content_filter_html'] = dunserialize($info['content_filter_html']);
		$info['forum_threadtypes'] = dunserialize($info['forum_threadtypes']);
		$info = dhtmlspecialchars($info);
		$info['header'] = pick_header_output($header_config, $head_url);
		$info['id'] = $id;
		$info['copy'] = $_GET['pick_copy'];
		return $info;
	}else{
		$setarr = $_POST['set'];
		$setarr = pstripslashes($setarr);
		$setarr['detail_ID'] = trim($setarr['detail_ID']);
		$setarr['forum_threadtypes'] = pserialize($_POST['forum_threadtypes']);
		$setarr['title_filter_rules'] = pserialize($_POST['title_filter_rules']);
		$setarr['content_filter_rules'] = pserialize($_POST['content_filter_rules']);
		$setarr['content_filter_html'] = pserialize($_POST['content_filter_html']);
		
		if(empty($setarr['rules_name'])) cpmsg_error(milu_lang('rules_no_empty'));
		$setarr = paddslashes($setarr);
		if($id && !$copy){
			$msg = milu_lang('modify');
			DB::update('strayer_fastpick', $setarr, array('id' => $id));
		}else{
			$setarr['rules_hash'] = create_hash();
		
			$id = DB::insert('strayer_fastpick', $setarr, TRUE);
			$msg =  milu_lang('add');
		}
		$url = PICK_GO.'fast_pick&myac=fastpick_edit&id='.$id;
		if(!$id) cpmsg_error($msg.milu_lang('fail'));
		del_search_index(1);
		cpmsg(milu_lang('rules_notice', array('msg' => $msg)), $url, 'succeed');
	}
}


//�������
function fastpick_import(){
	global $head_url,$header_config;
	if(!submitcheck('submit')) {
		$info['header'] = pick_header_output($header_config, $head_url);
		num_limit('strayer_fastpick', 30, 'f_num_limit');
		return $info;
	}else{
		$rules_code = $_GET['rules_code'];
		$update_flag = intval($_GET['update_flag']);
		if($rules_code){
			$data = $rules_code;
		}else{
			$file_name =  str_iconv($_FILES['rules_file']['tmp_name']);
			$fp = fopen($file_name, 'r');
			$data = fread($fp,$_FILES['rules_file']['size']);
		}
		$arr = pimportfile($data);
		if(empty($arr['rules_name'])) $arr['rules_name'] = $_G['timestamp'];
		unset($arr['id'], $arr['version']);	//���ٲɼ�����¼��ID�� �汾��
		if($arr['pick']['pid']) cpmsg_error(milu_lang('import_error2', array('url' => PICK_GO)));
		if(!$arr['rules_hash']){
			cpmsg_error(milu_lang('rules_error_data'));
		}
		$check = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_fastpick')." WHERE rules_hash='".$arr['rules_hash']."'"), 0);
		if($check && !$update_flag){
			if(!$rules_code) $rules_code = $data;
			cpmsg(milu_lang('cover_notice').'?<input type="hidden" value="'.dstripslashes($rules_code).'" name="rules_code">', PICK_GO.'fast_pick&myac=fastpick_import&pid='.$pid.'&submit=1&update_flag=1', 'form');
		}
		if(!array_key_exists('is_setting_article_page', $arr)){
			$arr['is_setting_article_page'] = !empty($arr['content_page_rules']) ? 1 : 2;//�Ƿ����÷�ҳ
		}
		$arr = get_table_field_name('strayer_fastpick', $arr);
		unset($arr['rid'], $arr['id']);//ȥ������
		$arr = paddslashes($arr);
		if($update_flag){
			$rules_hash = $arr['rules_hash'];
			unset($arr['rules_hash']);
			DB::update('strayer_fastpick', $arr, array('rules_hash' => $rules_hash));
		}else{
			$id = DB::insert('strayer_fastpick', $arr, TRUE);
		}
		del_search_index(1);
		cpmsg(milu_lang('import_finsh'), PICK_GO."fast_pick&myac=fastpick_import", 'succeed');	
	}
}



//��������
function fastpick_export() {
	global $_G;
	$id = $_GET['id'];
	if(!$id) cpmsg_error(milu_pick('select_rules'));
	$rules_info = fastpick_info($id);
	unset($rules_info['id']);
	$args = array(
		'type' => milu_lang('fastpick_rules'),
		'author' => $_G['setting']['bbname'],
		'rules_name' => $rules_info['rules_name'],
		'rule_desc' => $rules_info['rule_desc'],
	);
	$info['version'] = PICK_VERSION;
	exportfile($rules_info,$rules_info['rules_name'], $args);
}

function fastpick_set(){
	global $head_url,$header_config;
	if(!submitcheck('submit')) {
		require_once libfile('function/forumlist');
		$info = pick_common_get();
		$info['fp_open_mod'] = dunserialize($info['fp_open_mod']);
		$info['_fp_open_mod'] = array_map('intval', $info['fp_open_mod']);
		
		$info['fp_open_mod'][0] = in_array(1, $info['_fp_open_mod']) ? 1 : 0;//�Ż�
		$info['fp_open_mod'][1] = in_array(2, $info['_fp_open_mod']) ? 1 : 0;//��̳
		$info['fp_forum'] = dunserialize($info['fp_forum']);
		$info['fp_usergroup'] = dunserialize($info['fp_usergroup']);
		$info['forumselect'] = '<select name="set[fp_forum][]" size="10" multiple="multiple"><option value="">'.cplang('plugins_empty').'</option>'.forumselect(FALSE, 0, $info['fp_forum'], TRUE).'</select>';
		$info['header'] = pick_header_output($header_config, $head_url);
		return $info;
	}else{
		$set = $_POST['set'];
		$set['fp_open_mod'] = pserialize($set['fp_open_mod']);
		if(!$set['fp_forum'][0] && count($set['fp_forum']) == 1) $set['fp_forum'] = '';
		if(!$set['fp_usergroup'][0] && count($set['fp_usergroup']) == 1) $set['fp_usergroup'] = '';
		pick_common_set($set);
		cpmsg(milu_lang('op_success'), PICK_GO."fast_pick&myac=fastpick_set", 'succeed');	
	}
}


function fastpick_del(){
	global $_G;
	$id = $_GET['id'];
	if(!$id) cpmsg_error(milu_lang('select_rules'));
	$confirm = $_GET['confirm'];
	if($confirm || is_array($id)){
		$id_arr = is_array($id) ? $id : array($id);
		foreach($id_arr as $id){
			DB::query('DELETE FROM '.DB::table('strayer_fastpick')." WHERE id= '$id'");
		}
		cpmsg(milu_lang('rules_del_success').'!', PICK_GO.'fast_pick&myac=fastpick_manage', 'succeed');
	}else{
		cpmsg(milu_lang('del_confirm'), PICK_GO.'fast_pick&myac=fastpick_del&id='.$id.'&confirm=1', 'form');
	}
}

function fastpick_share(){
	global $_G,$head_url,$header_config;
	$info = get_share_serach('fastpick');
	$info['header'] = pick_header_output($header_config, $head_url);
	return $info;
}

function share_fast_pick_data(){
	global $_G;
	$id = intval($_GET['id']);
	if(!$id) exit('error');
	$client_info = get_client_info();
	if(!$client_info) return milu_lang('share_no_allow');
	$rules_data = fastpick_info($id);
	if(!$rules_data) exit('error');
	$rpcClient = rpcClient();
	unset($rules_data['id'], $rules_data['login_cookie']);
	$rules_data['rules_name'] = $_GET['rules_name'];
	$rules_data['rule_desc'] = $_GET['rules_desc'];
	$re = $rpcClient->upload_data('fastpick', $rules_data, $client_info);
	if(is_object($re) || $re->Number == 0){
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


function download_fast_pick_data(){
	$id = intval($_GET['id']);
	$rpcClient = rpcClient();
	$client_info = get_client_info();
	$re = $rpcClient->download_data('fastpick', $id, $client_info);
	if(is_object($re) || $re->Number == 0){
		if($re->Message) return  milu_lang('phprpc_error', array('msg' => $re->Message));
		$re = (array)$re;
	}
	$re = serialize_iconv($re);
	import_fastpick_data($re);
	return 'ok';
}


function import_fastpick_data($arr){
	$check = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_fastpick')." WHERE rules_hash='$arr[rules_hash]'"), 0);
	
	if(empty($arr['is_setting_article_page'])){//��ҳ
		$arr['is_setting_article_page'] = !empty($arr['content_page_rules']) ? 1 : 2;
	} 

	$arr = get_table_field_name('strayer_fastpick', $arr);
	unset($arr['id']);//ȥ������
	$arr = paddslashes($arr);
	del_search_index(1);	
	if($check){
		$rules_hash = $arr['rules_hash'];
		unset($arr['rules_hash']);
		 DB::update('strayer_fastpick', $arr, array('rules_hash' => $rules_hash));
	}else{
		return DB::insert('strayer_fastpick', $arr, TRUE);
	}
}

function import_evo_data($arr){
	global $_G;
	$arr = paddslashes($arr);
	$check = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_evo')." WHERE domain_hash='$arr[domain_hash]' AND detail_ID_hash='$arr[detail_ID_hash]' AND status='1'"), 0);
	if($check) return;
	//ɾ��֮ǰ��һЩ��¼
	DB::query('DELETE FROM '.DB::table('strayer_evo')." WHERE domain_hash='$arr[domain_hash]' AND detail_ID_hash='$arr[detail_ID_hash]' AND status='0'");
	$arr['dateline'] = $_G['timestamp'];
	$arr['hit_num'] = 1;
	$arr['status'] = 1;
	$arr = get_table_field_name('strayer_evo', $arr);
	unset($arr['id']);//ȥ������
	del_search_index(3);
	return DB::insert('strayer_evo', $arr, TRUE);
}


function fast_pick(){
	global $_G;
	d_s('f_g');
	d_s('g_t');
	pload('F:spider');
	$url = $_GET['url'];
	$plugin_set = get_pick_set();
	$pick_cache_time = $plugin_set['pick_cache_time'] ? $plugin_set['pick_cache_time']*3600 : -1;
	$content = get_contents($url, array('cache' => $pick_cache_time));
	$get_time = d_e(0, 'g_t');
	$type = $_GET['type'] ? $_GET['type'] : 'bbs';
	$milu_set = pick_common_get();

	$data = (array)get_single_article($content, $url);
	if($milu_set['fp_word_replace_open'] == 1 && VIP){//����ͬ����滻
		$words = get_replace_words();
		if($data['title']) $data['title'] = strtr($data['title'], $words);
		if($data['content']) $data['content'] = strtr($data['content'], $words);
	}

	
	if($milu_set['fp_article_from'] == 1){//������Դ
		$data['fromurl'] = $url;
		if($type == 'bbs' && $data['content']){
			$data['content'] .= "[p=30, 2, left]".milu_lang('article_from').':'.$url."[/p]";
		}
	}
	$data['get_text_time'] = $get_time;
	$data['all_get_time'] = d_e(0, 'f_g');
	$data = $data ? $data : array();
	$data = js_base64_encode($data);
	$re = json_encode($data);
	return $re;
}

//DZ�����Զ��ͼƬ���ػ��޷�����ĳЩ������ͼƬ�����������������
function ajax_downremoteimg(){
	global $_G;
	pload('F:spider');
	
	$_GET['message'] = str_replace(array("\r", "\n"), array($_GET['wysiwyg'] ? '<br />' : '', "\\n"), $_GET['message']);
	preg_match_all("/\[img\]\s*([^\[\<\r\n]+?)\s*\[\/img\]|\[img=\d{1,4}[x|\,]\d{1,4}\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $_GET['message'], $image1, PREG_SET_ORDER);
	preg_match_all("/\<img.+src=('|\"|)?(.*)(\\1)([\s].*)?\>/ismUe", $_GET['message'], $image2, PREG_SET_ORDER);

	$temp = $aids = $existentimg = array();
	if(is_array($image1) && !empty($image1)) {
		foreach($image1 as $value) {
			$temp[] = array(
				'0' => $value[0],
				'1' => trim(!empty($value[1]) ? $value[1] : $value[2])
			);
		}
	}
	if(is_array($image2) && !empty($image2)) {
		foreach($image2 as $value) {
			$temp[] = array(
				'0' => $value[0],
				'1' => trim($value[2])
			);
		}
	}
	require_once libfile('class/image');
	if(is_array($temp) && !empty($temp)) {
		pload_upload_class();
		$upload = new discuz_upload();
		$attachaids = array();

		foreach($temp as $value) {
			$imageurl = $value[1];
			$hash = md5($imageurl);
			if(strlen($imageurl)) {
				$imagereplace['oldimageurl'][] = $value[0];
				if(!isset($existentimg[$hash])) {
					$existentimg[$hash] = $imageurl;
					$attach['ext'] = $upload->fileext($imageurl);
					$content = '';
					if(preg_match('/^(http:\/\/|\.)/i', $imageurl)) {
						$snoopy_args['cookie'] = '';
						$snoop_obj = get_snoopy_obj($snoopy_args);
						$content_re = (array)get_img_content($imageurl, $snoop_obj, array('referer' => $imageurl));
						$content = $content_re['content'];
						$attach['ext'] = $content_re['file_ext'];
						
					} elseif(preg_match('/^('.preg_quote(getglobal('setting/attachurl'), '/').')/i', $imageurl)) {
						$imagereplace['newimageurl'][] = $value[0];
					}
					if(empty($content)) continue;
					$patharr = explode('/', $imageurl);
					$attach['name'] =  trim($patharr[count($patharr)-1]);
					$attach['thumb'] = '';

					$attach['isimage'] = $upload -> is_image_ext($attach['ext']);
					$attach['extension'] = $upload -> get_target_extension($attach['ext']);
					$attach['attachdir'] = $upload -> get_target_dir('forum');
					$attach['attachment'] = $attach['attachdir'] . $upload->get_target_filename('forum').'.'.$attach['extension'];
					$attach['target'] = getglobal('setting/attachdir').'./forum/'.$attach['attachment'];

					if(!@$fp = fopen($attach['target'], 'wb')) {
						continue;
					} else {
						flock($fp, 2);
						fwrite($fp, $content);
						fclose($fp);
					}
					if(!$upload->get_image_info($attach['target'])) {
						@unlink($attach['target']);
						continue;
					}
					$attach['size'] = filesize($attach['target']);
					$upload->attach = $attach;
					$thumb = $width = 0;
					if($upload->attach['isimage']) {
						if($_G['setting']['thumbstatus']) {
							$image = new image();
							$thumb = $image->Thumb($upload->attach['target'], '', $_G['setting']['thumbwidth'], $_G['setting']['thumbheight'], $_G['setting']['thumbstatus'], $_G['setting']['thumbsource']) ? 1 : 0;
							$width = $image->imginfo['width'];
						}
						if($_G['setting']['thumbsource'] || !$_G['setting']['thumbstatus']) {
							list($width) = @getimagesize($upload->attach['target']);
						}
						if($_G['setting']['watermarkstatus'] && empty($_G['forum']['disablewatermark'])) {
							$image = new image();
							$image->Watermark($attach['target'], '', 'forum');
							$upload->attach['size'] = $image->imginfo['size'];
						}
					}
					$aids[] = $aid = getattachnewaid();
					$setarr = array(
						'aid' => $aid,
						'dateline' => $_G['timestamp'],
						'filename' => daddslashes($upload->attach['name']),
						'filesize' => $upload->attach['size'],
						'attachment' => $upload->attach['attachment'],
						'isimage' => $upload->attach['isimage'],
						'uid' => $_G['uid'],
						'thumb' => $thumb,
						'remote' => '0',
						'width' => $width
					);
					DB::insert("forum_attachment_unused", $setarr);
					$attachaids[$hash] = $imagereplace['newimageurl'][] = '[attachimg]'.$aid.'[/attachimg]';

				} else {
					$imagereplace['newimageurl'][] = $attachaids[$hash];
				}
			}
		}
		if(!empty($aids)) {
			require_once libfile('function/post');
		}
		$_GET['message'] = str_replace($imagereplace['oldimageurl'], $imagereplace['newimageurl'], $_GET['message']);
		$_GET['message'] = addcslashes($_GET['message'], '/"\'');

	}
	$output = '<script type="text/javascript">parent.ATTACHORIMAGE = 1;parent.updateDownImageList(\''.$_GET['message'].'\');</script>';
	return $output;

}

// type 1�ǵ��� 2�����ù��� 3ѧϰ���Ĺ���
function write_evo_errlog($data, $url, $rule_info, $type = 0){
	global $_G;
	
	if(!$rule_info || !$url) return;
	if($data['title'] && $data['content']) return;
	if(!$data['title'] && $data['content']) {
		$why = 1;
	}else if(!$data['content'] && $data['title']){
		$why = 2;
	}else{
		$why = 0;
	}

	$p_key = 'id';
	if(!$type){
		if(array_key_exists('url_var', $rule_info)){//���ù���
			$type = 2;
			$p_key = 'rid';
		}else if(array_key_exists('evo_title_info', $rule_info)){//ѧϰ���Ĺ���
			$type = 3;
		}else{
			$type = 1;
		}
	}
	if(!$rule_info[$p_key]) return;
	$set['why'] = $why;
	$set['dateline'] = $_G['timestamp'];
	$set['type'] = $type;
	$set['url'] = $url;
	$set['data_id'] = $rule_info[$p_key];
	$set['rules_name'] = $rule_info['rules_name'];
	$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_evo_log')." WHERE type='$type' AND data_id='$rule_info[$p_key]' AND url='".daddslashes($url)."'"), 0);
	if($count) return;
	$set = paddslashes($set);
	return DB::insert('strayer_evo_log', $set, TRUE);
}

function fastpick_evo(){
	global $head_url,$header_config;
	$page = $_GET['page'] ? intval($_GET['page']) : 1;
	$perpage = 25;
	$start = ($page-1)*$perpage;
	$mpurl .= '&perpage='.$perpage;
	$perpages = array($perpage => ' selected');
	$mpurl = '?'.PICK_GO.'fast_pick&myac=fastpick_evo';
	$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_evo')." WHERE status<>0"), 0);
	if($count) {
		$query = DB::query("SELECT * FROM ".DB::table('strayer_evo')." WHERE status<>0 ORDER BY dateline LIMIT $start,$perpage ");	
		while(($v = DB::fetch($query))) {
			$v['dateline'] = dgmdate($v['dateline']);
			$v['show_detail_ID_test'] = cutstr($v['detail_ID_test'], 20);
			$v['theme_rules'] = dhtmlspecialchars($v['theme_rules']);
			$v['content_rules'] = dhtmlspecialchars($v['content_rules']);
			$v['full_theme_rules'] = $v['theme_rules'];
			$v['full_content_rules'] = $v['content_rules'];
			$v['theme_rules'] = cutstr($v['theme_rules'], 20);
			$v['content_rules'] = cutstr($v['content_rules'], 20);
			$v['detail_ID'] = dhtmlspecialchars($v['detail_ID']);
			$info['rs'][] = $v;
		}
	}
	$info['multipage'] = multi($count, $perpage, $page, $mpurl);
	$info['count'] = $count;
	$info['header'] = pick_header_output($header_config, $head_url);
	return $info;
	
}

function fastpick_evo_del(){
	$id = $_GET['id'];
	if($id){
		$id_arr = is_array($id) ? $id : array($id);
		$id_str = base64_encode(serialize($id_arr));
	}
	$submit = $_GET['submit'];
	if($submit){
		$id_arr = unserialize(base64_decode($_GET['id_str']));
		DB::query("DELETE FROM ".DB::table('strayer_evo')." WHERE id IN (".dimplode($id_arr).")");
		DB::query("DELETE FROM ".DB::table('strayer_searchindex')." WHERE rid IN (".dimplode($id_arr).") AND type='34'");		
		cpmsg(milu_lang('del_finsh'), PICK_GO."fast_pick&myac=fastpick_evo", 'succeed');
	}else{
		cpmsg(milu_lang('del_confirm'), PICK_GO.'fast_pick&myac=fastpick_evo_del&id_str='.$id_str.'&submit=1', 'form');
	}	
}

function fastpick_evo_log(){
	global $head_url,$header_config;
	$page = $_GET['page'] ? intval($_GET['page']) : 1;
	$perpage = 25;
	$start = ($page-1)*$perpage;
	$mpurl .= '&perpage='.$perpage;
	$perpages = array($perpage => ' selected');
	$mpurl = '?'.PICK_GO.'fast_pick&myac=fastpick_evo_log';
	$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_evo_log')), 0);
	$type_arr = array('1' => milu_lang('fast_pick_rules'), '2' => milu_lang('system_rules'), '3' => milu_lang('fastpick_evo'));
	$why_arr =  array('1' => milu_lang('no_get_title'), '2' => milu_lang('no_get_content'), '0' => milu_lang('no_get_all'));
	if($count) {
		$query = DB::query("SELECT * FROM ".DB::table('strayer_evo_log')." ORDER BY dateline DESC LIMIT $start,$perpage ");	
		while(($v = DB::fetch($query))) {
			$v['dateline'] = dgmdate($v['dateline']);
			$v['show_type'] = $type_arr[$v['type']];
			$v['show_why'] = $why_arr[$v['why']];
			$v['show_url'] = cutstr($v['url'], 50);
			if($v['type'] == 1){
				$v['go_url'] = '?'.PICK_GO.'fast_pick&myac=fastpick_edit&id='.$v['data_id'];
			}else if($v['type'] == 2){
				$v['go_url'] = '?'.PICK_GO.'system_rules&myac=rules_edit&rid='.$v['data_id'];
			}else if($v['type'] == 3){
				$v['go_url'] = '';
			}
			$info['rs'][] = $v;
		}
	}
	$info['multipage'] = multi($count, $perpage, $page, $mpurl);
	$info['count'] = $count;
	$info['header'] = pick_header_output($header_config, $head_url);
	return $info;
}

function fastpick_evo_log_del(){
	$id = $_GET['id'];
	if($id){
		$id_arr = is_array($id) ? $id : array($id);
		$id_str = base64_encode(serialize($id_arr));
	}
	$submit = $_GET['submit'];
	if($submit){
		$id_arr = unserialize(base64_decode($_GET['id_str']));
		DB::query("DELETE FROM ".DB::table('strayer_evo_log')." WHERE id IN (".dimplode($id_arr).")");
		cpmsg(milu_lang('del_finsh'), PICK_GO."fast_pick&myac=fastpick_evo_log", 'succeed');
	}else{
		cpmsg(milu_lang('del_confirm'), PICK_GO.'fast_pick&myac=fastpick_evo_log_del&id_str='.$id_str.'&submit=1', 'form');
	}	
}
?>