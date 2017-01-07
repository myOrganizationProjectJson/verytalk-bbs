<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

//网页正文提取
function get_single_article($content, $url, $args = array()){
	global $_G;
	extract($args);
	if(strlen(trim($content)) < 1) return;
	d_s('evo');
	$base_url  = get_base_url($content);
	$get_type = $_GET['get_type'] ? intval($_GET['get_type']) : $get_type;
	$get_type = $get_type ? $get_type : 1;
	$milu_set = pick_common_get();
	$rules_info = match_rules($url, $content, $get_type, 0);
	if(is_array($rules_info) && $rules_info){
		pload('F:fastpick');
		if($rules_info['login_cookie']){
			$content = get_contents($url, array('cookie' => $rules_info['login_cookie']));
		}
		$data = rules_get_article($content, $rules_info);
		write_evo_errlog($data, $url, $rules_info);
	}else{
		$data = (array)cloud_match_rules($get_type, $url, $content); //从云端下载规则 这里应该做点优化，暂时没想到方法.
		if(!$data['content'] && $milu_set['fp_open_auto'] == 1){//开启智能获取
			pload('C:HtmlExtractor');
			pload('F:article');
			$he = new HtmlExtractor($content, $url);
			$data = (array)$he->get_text();
			$data['content'] = dz_attach_format($url, $data['content']);
			$arr = format_article_imgurl($url, $data['content'], $base_url);
			$data['content'] = $arr['message'];
			$del_dom_rules = array('div[id*=share]', 'div[class*=page]');
			foreach($del_dom_rules as $k => $v){
				$data['content'] = dom_filter_something($data['content'], $v, 2);
			}
			unset($data['evo_title_info']);
		}
	}
	$data['title'] = dstripslashes($data['title']);
	if($_GET['type'] == 'bbs') {
		//$data['content'] = content_html_ubb($data['content'], $url);//统一在前台转换了
	}
	$data['evo_time'] = d_e(0, 'evo');
	return $data;
}



//视频标签的过滤
function media_format($text, $url = ''){
	$pregfind = array(
		"/<script type=\"text\/javascript\".+'width',\s+'(\d+)',\s+'height',\s+'(\d+)'.+'src',\s+'([^\']+)'.+<\/script>/eiU",
		'/<script language="JavaScript">player\(\'player_\'\+\d+,\'(.*)\'.+<\/script>/eiU',
	);
	
	$pregreplace = array(
		"mediatag_format('\\3','\\1','\\2', '".$url."')",
		"mediatag_format('\\1', '".$url."')",
	);
	$text = preg_replace($pregfind, $pregreplace, $text);
	//$text = clear_ad_html($text);
	return $text;
}


//根据规则获取封面
function rules_get_cover($url, $content, $get_type, $get_rules, $base_url = ''){
	
	if($get_type == 1){//dom
		$rule_arr = format_wrap($get_rules); 
		$a = $rule_arr[1] ? $rule_arr[1] : 'img';
		$a_info = explode('->', $a);
		$a = $a_info[0];
		$attr = $a_info[1] ? $a_info[1] : 'src';
		$html = get_htmldom_obj($content);
		if(!$html) return;
		$base_url  = $base_url ? $base_url : get_base_url($content);
		$base_url = $base_url ? $base_url : $url;
		foreach($html->find($rule_arr[0]) as $v) {
			$a_url = $v->find($a, 0)->attr[$attr];
			if(!$a_url || $a_url == '#') continue; 
			$item[] = _expandlinks($a_url, $base_url);
		}
		//$item = sarray_unique($item);//去重复
		$html->clear();
		unset($html);
	}else{
		$item = string_page_link($content, $get_rules, $url);
		//字符串
	}
	return $item;
}

function mediatag_format($src, $width = 0, $height = 0, $page_url = '') {
	if(!preg_match("/^http:\/\//i", $src)) {
		$src = _expandlinks($src, $page_url);
	}
	return '[flash]'.$src.'[/flash]';//门户和博客压根就不需要长宽这些，照样能播放视频
	$ext = strtolower(substr(strrchr($src, '.'), 1, 10));
	if($ext == 'swf'){
		return $src ? ($width && $height ? '[flash='.$width.','.$height.']'.$src.'[/flash]' : '[flash]'.$src.'[/flash]') : '';
	}else{
		return $src ? '[media='.$ext.','.$width.','.$height.']'.$src.'[/media]' : '';
	}
}



function mediatag($attributes, $page_url, $type = 'bbs') {
	global $_G;
	$attributes = dstripslashes($attributes);
	$value = array('src' => '', 'width' => '', 'height' => '', 'flashvars' => '');
	preg_match_all('/(src|width|flashvars|height)=(["\'])?([^\'" ]*)(?(2)\2)/i', $attributes, $matches);
	if(is_array($matches[1])) {
		foreach($matches[1] as $key => $attribute) {
			$attribute = strtolower($attribute);
			$value[$attribute] = $matches[3][$key];
		}
	}

	@extract($value);
	if($flashvars)  {
		parse_str($flashvars);
		$flash_src = $file;	
	}	
	if(!preg_match("/^http:\/\//i", $src)) {
		$src = _expandlinks($src, $page_url);
	}
	if(!preg_match("/^http:\/\//i", $flash_src) && $flash_src) {
		$flash_src = _expandlinks($flash_src, $page_url);
	}
	$src_info = parse_url($src);
	$ext = strtolower(substr(strrchr($src_info['path'], '.'), 1, 10));
	if(strexists($ext, '&')){
		$ext = current(explode('&', $ext));
	}
	if(strexists($ext, '?')){
		$ext = current(explode('?', $ext));
	}
	if($ext == 'swf'){
		$src = $flash_src ? $flash_src : $src;
		return $src ? ($width && $height && $type == 'bbs' ? '[flash='.$width.','.$height.']'.$src.'[/flash]' : '[flash]'.$src.'[/flash]') : '';
	}else{
		$file_ext = addslashes(strtolower(substr(strrchr($src, '.'), 1, 10)));
		if($type != 'bbs'){
			//if($file_ext == 'wmv'){
				$media_type = 'media';
			//}
			return $src ? '[flash='.$media_type.']'.$src.'[/flash]' : '';
		}
		if(!$src) return '';
		switch(strtolower($file_ext)) {
			case 'mp3':
			case 'wma':
			case 'ra':
			case 'ram':
			case 'wav':
			case 'mid':
				return '[audio]'.$src.'[/audio]';
			case 'wmv':
			case 'rm':
			case 'rmvb':
			case 'avi':
			case 'asf':
			case 'mpg':
			case 'mpeg':
			case 'mov':
			case 'flv':
			case 'swf':
				return '[media='.$attach['ext'].','.$width.','.$height.']'.$src.'[/media]';
			default:
				return '[media=x,'.$width.','.$height.']'.$src.'[/media]';
		}
	}
}


//获取文章中的附件
function get_article_attach($content, $is_download_file, $page_url){
	global $_G;
	if(!$content) return;
	$evo_img_no = $_G['cache']['evn_milu_pick']['evo_img_no'];
	$content = str_replace(array("src=\r\n\"", "src=\r\"", "src=\n\"", "img\r\n", "img\r", "img\n"), array("src=\"", "src=\"", "src=\"", "img ", "img ", "img "), $content);//有些特殊的
	
	preg_match_all("/\[img\]\s*([^\[\<\r\n]+?)\s*\[\/img\]|\[img=\d{1,4}[x|\,]\d{1,4}\]\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $content, $image1, PREG_SET_ORDER);
	preg_match_all("/\<img.+src=('|\"|)?(.*)(\\1)(.*)?\>/isU", $content, $image2, PREG_SET_ORDER);
	$temp = $aids = $existentimg = $attach_arr = array();
	if(is_array($image1) && !empty($image1)) {
		foreach($image1 as $value) {
			$v = trim(!empty($value[1]) ? $value[1] : $value[2]);
			$no_remote = 0;
			if(!filter_something($v, $evo_img_no)){//存在
				$no_remote = 1;
			}
			if($no_remote == 0){
				$temp[] = array(
					'0' => $value[0],
					'1' => $v,
					'2' => pget_image_title($value[0]),
				);
			}
		}
	}
	if(is_array($image2) && !empty($image2)) {
		foreach($image2 as $v) {
			if($v[0] && !$v[1] && !$v[2] && !$v[3]){//匹配这样的<img src=http://3.pic.58control.cn/p1/big/n_s12172353208093464094.jpg />
				preg_match_all("/src=(.*)(\/>|\s)/isU", $v[0], $image_temp, PREG_SET_ORDER);
				if($image_temp[0][1]) {
					$v[2] = $image_temp[0][1];
				}else{//类似<img src=/pu/2014/8/21/3533_37514/1.gif>
					$v[2] = $v[4];
				}
			}
			$no_remote = 0;
			$v[2] = trim(strip_tags($v[2]));
			$v[2] = _expandlinks($v[2], $page_url);
			if(!filter_something($v[2], $evo_img_no)){//存在
				$no_remote = 1;
			}
			if($no_remote == 0){
				$temp[] = array(
					'0' => $v[0],
					'1' => $v[2],
					'2' => pget_image_title($v[0]),
				);
			}
		}
	}
	if($is_download_file == 1) {
		$attach_arr = get_attach_data($page_url, $content);
	}
	$attach_arr = $attach_arr ? $attach_arr : array();
	$temp = $temp ? $temp : array();
	$temp = array_merge($temp, $attach_arr);
	return $temp;
}

function pget_image_title($attributes){
	$value_arr = array('title' => '', 'alt' => '');
	$value_spit_str = implode('|', array_keys($value_arr));
	preg_match_all('/('.$value_spit_str.')=(["\'])?([^\'" ].*?)(?(2)\2)/i', $attributes, $matches);
	if(is_array($matches[1])) {
		foreach($matches[1] as $key => $attribute) {
			$attribute = strtolower($attribute);
			$value_arr[$attribute] = $matches[3][$key];
		}
	}
	return $value_arr['title'] ? $value_arr['title'] : $value_arr['alt'];
}



//可以下载防盗链图片
//默认只能下载图片，如果想下载zip，args加上no_only_image=1
function get_img_content($img_url, $snoopy_obj = '', $args = array()){
	global $_G;
	$pick_config = get_pick_set();
	$no_allow_ext = $args['no_only_image'] == 1 ? array() : array('htm', 'html', 'shtml');
	if(!function_exists('fsockopen') && !function_exists('pfsockopen') || !$snoopy_obj){
		$content = dfsockopen($img_url);
	}else{
		if($pick_config['is_set_referer'] == 1 || $args['is_set_referer'] == 1) {
			$snoopy_obj->referer = $args['referer'] ? $args['referer'] : $img_url; 
		}
		$snoopy_obj->fetch($img_url);
		$content = $snoopy_obj->results;
		$headers = $snoopy_obj->headers;
		$key = array_search("Content-Encoding: gzip", $headers);
		if($key) $content = gzdecode($content);//gzip
		if($snoopy_obj->status == '403'){
			$snoopy_obj->referer = ''; 
			$snoopy_obj->fetch($img_url);
			$content = $snoopy_obj->results;
			$headers = $snoopy_obj->headers;
		}
		if($snoopy_obj->status == '404' || $snoopy_obj->status == '403' || $snoopy_obj->status == '400'){
			if($args['check'] != 2 && ischinese($img_url) != 'allen'){//地址含有中文
				//检测是否编码不一致
				foreach($headers as $k => $v){
					$v = strtoupper($v);
					if(strexists($v, 'CHARSET=')){
						$temp_arr = explode('CHARSET=', $v);
						$charset = trim($temp_arr[1]);
						if(CHARSET != $charset){//编码不一样
							$_G['cn_charset'] = $charset;
							$img_url = cnurl($img_url);
							$args['check'] = 2;
							return get_img_content($img_url, $snoopy_obj, $args);
						}
						break;
					}else{//如果获取不到编码
						$_G['cn_charset'] = 'utf-8';//有些页面编码是gb2312，但却需要转换成utf-8格式的网址才能下载附件。这种只能靠猜了
						$img_url = cnurl($img_url);
						$args['check'] = 2;
						return get_img_content($img_url, $snoopy_obj, $args);
					}
				}
			}
			return FALSE;
		}
		if($headers[0] == 'HTTP/1.1 400 Bad Request') return FALSE;
		foreach((array)$headers as $v){
			$v_arr = explode(':', $v);
			if($v_arr[1]) $header_arr[strtolower($v_arr[0])] = trim($v_arr[1]);
		}
		pload('F:http');
		$info['file_size'] = $header_arr['content-length'] ? $header_arr['content-length'] : strlen($content);
		$url_info = parse_url($img_url);
		$query_url = $url_info['query'] ? $url_info['query'] : $url_info['path'];
		$info['file_ext'] = addslashes(strtolower(substr(strrchr($query_url, '.'), 1, 10)));
		$info['content'] = $content;
		
		if($header_arr['content-disposition']){
			$c_d = $header_arr['content-disposition'];
			$info_arr = explode(';', $c_d);
			$file_arr = explode('=', $info_arr[1]);
			$arr[2] = preg_replace('(\'|\")', '', $file_arr[1]);//去掉引号
			$file_name = $info['file_name'] = str_iconv(urldecode($arr[2]));
			if(trim($info_arr[0]) == 'attachment' && trim($file_arr[0]) == 'filename'){
				$info['file_ext'] = addslashes(strtolower(substr(strrchr($file_name, '.'), 1, 10)));
			}else{
				$info['file_ext'] = $info['file_ext'] ? $info['file_ext'] : addslashes(strtolower(substr(strrchr($file_name, '.'), 1, 10)));
			}
			if(empty($file_name)){
				$patharr = explode('/', $img_url);
				$info['file_name'] =  trim($patharr[count($patharr)-1]);
			}
			$info['content'] = $content;
			return $info;
		}else{
			if(in_array($info['file_ext'], $no_allow_ext)){
				return FALSE;
			}
			if(!$info['file_ext']){
				$content_type = array_flip(GetContentType());
				$header_arr['content-type'] = strtolower(str_replace(';', '', $header_arr['content-type']));
				$file_content_type = explode(' ', $header_arr['content-type']);
				$info['file_ext'] = $content_type[$file_content_type[0]];
				//基于文件头获取扩展名
				$info['file_ext'] = $info['file_ext'] && $header_arr['content-type'] != 'application/octet-stream'  ? $info['file_ext'] : FileExt::get_fileext($content);
				if(!$info['file_ext']){
					if(strexists($info['content'], 'torrent')){//对于一些torrent类型的附件，又获取不到任何扩展名，只能这样
						$info['file_ext'] = 'torrent';
					}
				} 
				if(in_array($info['file_ext'], $no_allow_ext)){
					return FALSE;
				}
			}
		}
		if($info['file_ext']){
			$ext_info = explode('/', $info['file_ext']);
			$image_ext_arr = array('gif', 'jpg', 'jpeg', 'png');
			if(count($ext_info) > 1){//扩展名是 jpg/0这些
				foreach($ext_info as $v){
					$ext_key = array_search(strtolower($v), $image_ext_arr);
					if($ext_key != FALSE){
						$info['file_ext'] = $image_ext_arr[$ext_key];
					}
				}
			}
			$patharr = explode('/', $img_url);
			$info['file_name'] =  trim($patharr[count($patharr)-1]);
			if(strlen($info['file_name']) > 35 && ischinese($info['file_name']) == 'allen'){//如果文件名太长，而且都是字母，重新命名
				$info['file_name'] = time().'.'.$info['file_ext'];
			}
			if(strexists($info['file_name'], 'forum.php?mod=attachment')) $info['file_name'] = $info['file_ext'] = '';
		}
		$info['content'] = $content;
		if($ext == 'no_get') return $info;
	}
	$info['content'] = $content;
	$info['file_size'] = $info['file_size'] ? $info['file_size'] : strlen($content);
	return $info;
}




function get_rss_obj(){
	require_once(PICK_DIR.'/lib/lastRSS.class.php');
	$set = get_pick_set();
	$cache_time = 0;
	$rss = new lastRSS;
	$rss->cache_dir = PICK_CACHE.'/rss/';//设置缓存目录
	if(!is_dir($rss->cache_dir)) dmkdir($rss->cache_dir);
	$rss->cache_time = $set['cache_time'] * 60;
	$rss->default_cp = 'GB2312';//目标字符编码，默认为UTF-8
	$rss->cp = CHARSET;//自己的编码
	$rss->items_limit = 0;//设置输出数量，默认为10
	$rss->date_format = 'U'; //设置时间格式。默认为字符串；U为时间戳，可以用date设置格式
	$rss->stripHTML = FALSE; //设置过滤html脚本。默认为false，即不过滤<br>
	$rss->CDATA = 'content'; //设置处理CDATA信息。默认为nochange。另有strip和content两个选项
	return $rss; //输出
}



//dom获取多个内容段
function dom_get_manytext($content, $dom_rules, $count = 0){
	if(!$content || !$dom_rules) return;
	$content = jammer_replace($content);
	$html = get_htmldom_obj($content);
	$count = intval($count);
	if(!$html) return false;
	foreach($html->find($dom_rules) as $k => $v) {
		$v->innertext = jammer_replace($v->innertext, 1);
		$text_arr[] = $v->innertext;
		if($count > 0 &&  ($k == $count - 1 )) break;
	}
	$html->clear();
	unset($html);
	return $text_arr;
	
}
//利用dom剔除某个内容
function dom_filter_something($str, $dom_rules, $test_flag = 1){
	if(!$str || !$dom_rules) return $str;
	$is_jammer = strexists($dom_rules, 'jammer') || strexists($dom_rules, 'display:none') ? 1 : 0;
	$str = jammer_replace($str);
	$html = get_htmldom_obj($str);
	if(!$html) return false;
	$get_dom_arr = dom_get_str($html, $dom_rules, array('is_get_all' => 1, 'text_type' => 'outertext', 'is_return_array' => 1));
	foreach($get_dom_arr as $dom_get_str) {
		$dom_get_str = jammer_replace($dom_get_str);
		if($dom_get_str) {
			if($test_flag == 1){
				$str = str_replace($dom_get_str, '<del>'.$dom_get_str.'</del>', $str);
			}else{
				$str = str_replace($dom_get_str, '', $str);
			}
		}
	}
	$str = jammer_replace($str, 1);
	return $str;
	
}

//利用字符剔除某个内容
function str_filter_something($str, $rule, $get_str = '(*)',$test_flag = 1){
	if(!$str || !$rule) return $str;
	$str = pick_warp_format($str);
	$content_arr = pregmessage($str, $rule, 1, -1, 'out');
	if(!$content_arr[0]) $content_arr = pregmessage($str, $rule, 1, 1, 'out');
	if(!$content_arr[0]) return $str;
	foreach($content_arr as $v){
		if($v){
			if($test_flag == 1){
				$str = str_replace($v, '<del>'.$v.'</del>', $str);
			}else{
				$str = str_replace($v, '', $str);
			}
		}
	}
	return $str;
}

//处理字符串中的换行符
function pick_warp_format($str){
	$str = str_replace("\r\n", "\r\m", $str);	
	$str = str_replace("\n", "\r\n", $str);
	$str = str_replace("\r\m", "\r\n", $str);
	return $str;
}


//通过字符串获取列表中文章链接
function string_page_link($content, $rule, $url){
	$rule = trim($rule);
	$rule = str_replace('[link]', '[data]', $rule);
	$link_content = pregmessage($content, $rule, 'data',-1);
	if($link_content[0]){
		$arr = $link_content;
	}else{
		$link_content = pregmessage($content, $rule, 'data');
		$arr = _striplinks($link_content[0]);
		
	}
	$base_url  = get_base_url($content);
	$url = $base_url ? $base_url : $url;
	if(is_array($arr)){
		foreach($arr as $k => $v){
			$re_arr[$k] = _expandlinks($v, $url);
		}
	}
	return sarray_unique($re_arr);	
}



function get_reward_price($get_type, $get_rules, $contents){
	preg_match_all("/\{(.*)?}/isU", $get_rules , $temp_arr, PREG_SET_ORDER);
	$rand_rules = '';
	if(!$temp_arr && $get_rules) {
		$rules = $get_rules;
	}else{
		$rules = $temp_arr[0][1];
		$rand_rules = $temp_arr[1][1];
	}
	$reword_price = 0;
	if($get_type == 1){//DOM
		$html = get_htmldom_obj($contents);
		$reword_price = dom_get_str($html, $rules);
	}else if($get_type == 2){//STR
		$reword_price = str_get_str($contents, $rules, 'data');
	}else{//自定义
		$rand_rules = $get_rules;
	}
	$reword_price = intval($reword_price);
	if(($get_type != 3 && $reword_price == 0 && $rand_rules ) || $get_type == 3){
		$rand_info = explode(',', $rand_rules);
		$reword_price = (count($rand_info) == 1) ? $rand_info[0] : rand($rand_info[0], $rand_info[1]);
	}
	return $reword_price;
}
//format_article_imgurl('format_article_imgurl', '');
//将文章中图片的相对路径转换为绝对路径
function format_article_imgurl($url, $message, $base_url = ''){
	global $_G;
	$evo_img = $_G['cache']['evn_milu_pick']['evo_img'];
	$evo_img_no = $_G['cache']['evn_milu_pick']['evo_img_no'];
	//图片获取
	$base_url  = $base_url ? $base_url : get_base_url($message);
	$url = $base_url ? $base_url : $url;
	$search_arr = array_keys($evo_img);
	preg_match_all("/\<img.+src=('|\"|)?(.*)(\\1)(.*)?\>/isU", $message, $image1, PREG_SET_ORDER);
	preg_match_all("/\<img.+file=('|\"|)?(.*)(\\1)(.*)?\>/isU", $message, $image2, PREG_SET_ORDER);
	preg_match_all("/\<embed.+src=('|\"|)?(.*)(\\1)(.*)?\>/isU", $message, $image3, PREG_SET_ORDER);//视频标签
	$temp =  array();
	if(is_array($image1) && !empty($image1)) {
		foreach($image1 as $value) {
			if(substr_count($value[0], '<img') > 1) continue;
			$temp[] = array(
				'0' => $value[0],
				'1' => trim($value[2])
			);
		}
	}
	if(is_array($image2) && !empty($image2)) {
		foreach($image2 as $v) {
			$temp[] = array(
				'0' => $v[0],
				'1' => trim(strip_tags($v[2]))
			);
			$file_img[] = md5($v[0]);
		}
	}
	$file_count = 0;
	if(is_array($image3) && !empty($image3)) {
		foreach($image3 as $v) {
			$file_count++;
			$temp[] = array(
				'0' => $v[0],
				'1' => trim(strip_tags($v[2]))
			);
		}
	}
	if(is_array($temp) && !empty($temp)) {
		foreach($temp as $key => $value) {
			$value_old[1] = $value[1];
			foreach($search_arr as $v){
				if(!filter_something($value[0], array($v))){
					$get_attr = $evo_img[$v];
					if($get_attr == 'file') $file_img[] = md5($value[0]);
					preg_match_all("/\<img.+".$get_attr."=('|\"|)?(.*)(\\1)(.*)?\>/isU", $value[0], $img_arr, PREG_SET_ORDER);
					$value[1] = $img_arr[0][2];
				}
			}
			$search[$key] = $value[0];
			$no_remote = 0;
			if(!filter_something($value[1], $evo_img_no)){//存在
				$no_remote = 1;
				$match_part = parse_url($value[1]);
				if($match_part["scheme"]){//获取图片的相对路径
					$port = $match_part["port"]  ?  ':'.$match_part["port"] : '';
					$match_root = $match_part["scheme"]."://".$match_part["host"].$port.'/';
					$value[1] = str_replace($match_root, '', $value[1]);
				}
			}

			$no_remote = $no_remote == 1 && file_exists(DISCUZ_ROOT.'/'.$value[1]) ? 1 : 0;//看看本地是否有这个文件
			$replace_url = $no_remote == 1 ? $value[1] : _expandlinks($value[1], ($match_root ? $match_root : $url));
			$replace[$key] = str_replace(array($value_old[1], 'smilieid'), array($replace_url, 'smilie_id'), $value[0]);
			if(in_array(md5($value[0]), (array)$file_img)) {
				$r_str = strexists($replace[$key], 'src=') ? 'pold=' : 'src=';
				$replace[$key] = str_replace('file=', $r_str, $replace[$key]);
			}
		}
	}
	$message = str_replace($search, $replace, $message);
	$arr['message'] = $message;
	$arr['pic'] = count($search);
	if($arr['pic'] < 0)  $arr['pic'] = 0;
	return $arr;
}



//通过dom方式获取列表中的文章链接
function dom_page_link($content, $arr ='', $type = 0){
	$rule_arr = format_wrap($arr['page_link_rules']); 
	$a = $rule_arr[1] ? $rule_arr[1] : 'a';
	$html = get_htmldom_obj($content);
	if(!$html) return FALSE;
	$base_url  = get_base_url($content);
	$url = $base_url ? $base_url : $arr['url'];
	foreach($html->find($rule_arr[0]) as $v) {
		if($a != 'img'){
			$a_url = $type == 0 ? $v->find($a, 0)->attr['href'] : $v->attr['href'];
		}else{
			$a_url = $type == 0 ? $v->find($a, 0)->attr['src'] : $v->attr['src'];
		}
		if(!$a_url || $a_url == '#' || $v->innertext == '上一页') continue; 
		$item[] = _expandlinks($a_url, $url);
	}
	$item = sarray_unique($item);//去重复
	$html->clear();
	unset($html);
	return $item;
}
function get_page_link($url){
	$snoopy = new Snoopy;
	$snoopy->fetchlinks($url) ;
	$all_link = $snoopy->results;
	$re = is_array($all_link) ? array_unique($all_link) : $all_link;
	return $re;
}

//通过dom方式获取一篇文章的信息
function dom_single_article($content = '',$dom_arr = ''){
	$content = jammer_replace($content);
	if(!$content) return ;
	$html = get_htmldom_obj($content);
	if(!$html) return false;
	if($dom_arr['title']){
		$div2 = $html->find('title');
		$re['other']['old_title'] = str_iconv($div2[0]->innertext);
		$re['title'] = dom_get_str($html, $dom_arr['title']);
		$re['other']['old_title'] = jammer_replace($re['other']['old_title'], 1);
		$re['title'] = jammer_replace($re['title'], 1);
		unset($div2);
	}
	$re['content'] = dom_get_str($html, $dom_arr['content']);
	$re['content'] = jammer_replace($re['content'], 1);
	$html->clear();
	unset($html);
	return $re;
}

//array('dateline_get_type' => $dateline_get_type, 'dateline_get_rules' => $dateline_get_rules, 'user_get_type' => $user_get_type, 'user_get_rules' => $user_get_rules, 'is_use_thread_setting' => $is_use_thread_setting, 'is_reply_user' => 1, 'content' => $content)

//支持这样的dom规则div.text span a.ask-author||span.ask-author中间的||号表示第一个规则获取不到的情况下，使用第二个备用规则
function dom_or_get($html, $rules, $args = array()){
	$rules_arr = explode('||', $rules);	
	foreach($rules_arr as $k => $v){
		$result = dom_get_str($html, $v, $args);
		if($result) return $result;
	}
}



//根据头像地址分析出头像主url和uid
function get_info_by_avatar($avatar_url){
	if(!$avatar_url) return array();
	$exp_arr = explode('/data/avatar/', $avatar_url);
	$avatar_host = $exp_arr[0].'/';
	preg_match_all('/\/data\/avatar\/(.*?)_avatar/i',$avatar_url, $matchs);
	$uid_str = str_replace('/', '', $matchs[1][0]);
	$uid = intval($uid_str);
	return array('host' => $avatar_host, 'uid' => $uid);
}

//dom取某个区域 $is_get_all 0默认取第一个 1 表示取所有 $text_type = 'innertext' is_return_array = 0是否以数组形式返回
function dom_get_str($html, $rules, $args = array()){
	extract($args);
	if(!$html) return false;
	$text_type = $text_type ? $text_type : 'innertext';
	$is_get_all = isset($is_get_all) ? $is_get_all : 0;
	$is_return_array = isset($is_return_array) ? $is_return_array : 0;
	$rules = str_replace(array('\{', '\}'), array('{UUU', 'UUU}'), $rules);
	$c_arr = $r = $text_arr = array();
	if(strexists($rules, '{') && strexists($rules, '}')){
		preg_match_all("/\{(.*)?}/isU", $rules , $c_arr, PREG_SET_ORDER);
		foreach($c_arr as $k => $v){
			$r['search'][] = $v[0];
			$c_dom_arr[] = $v[1];
		}
	}else{
		$c_dom_arr = format_wrap($rules);
	}
	$index_arr = array();
	if($c_dom_arr){
		foreach($c_dom_arr as $k => $v){
			preg_match_all('/\w+\[[^]]+]|[^\s]+/', $v, $v_s_arr);
			$v_arr = $v_s_arr[0];
			if(strexists($v, '->')){//将->out这样的并进去
				foreach($v_arr as $k1 => $v1){
					$exp_arr = explode('->', $v1);
					$exp_arr = array_filter($exp_arr);
					if(strexists($v1, '->') && count($exp_arr) == 1){
						$v_arr[($k1-1)] .= $v1;
						unset($v_arr[$k1]);
					}
				}
			}
			
			$text_data_arr =  dom_find_value($v_arr, $html, $text_type, array(), $is_get_all, 0);
			if($is_get_all != 1) $text_data_arr = array($text_data_arr[0]);
			$text_arr = array_merge((array)$text_arr, (array)$text_data_arr);
		}	
		
	}
	unset($html);
	if($is_return_array == 1) {
		$text = $text_arr;//以数组形式返回
	}else{
		$rules = str_replace(array('{UUU', 'UUU}'), array('{', '}'), $rules);
		if($r['search']) {//获取内容才用这个，过滤等不用这样
			$r['replace'] = $text_arr;
			$text = str_replace($r['search'], $r['replace'], $rules);
		}else{
			$text = implode('', $text_arr);
		}
	}
	return $text;	
}

function get_attr_value($v_rules, $text_type){
	$exp_rules_info = explode('->', $v_rules);
	$v_rules = $exp_rules_info[0];
	$get_value_rules = $exp_rules_info[1];
	if($get_value_rules){
		$get_value_rules = $get_value_rules == 'out' ? 'outertext' : $get_value_rules;
		$get_value_rules = $get_value_rules == 'in' ? 'innertext' : $get_value_rules;
		$get_value_rules = $get_value_rules == 'text' ? 'plaintext' : $get_value_rules;
		$get_value_rules = $get_value_rules == 'xml' ? 'xmltext' : $get_value_rules;
	}else{
		$get_value_rules = $text_type ? $text_type : 'innertext';
	}
	return array('attr_value' => $get_value_rules, 'v_rules' => $v_rules);
}

function get_value_index($v_rules, $is_get_all, $is_last){
	$start_index = $end_index = $index = -1;
	$last_flag = 1234567;//取最后一个的标记
	if(strexists($v_rules, '[*]')){//取全部
		$start_index = 0;
		$end_index = $last_flag;
		$v_rules = str_replace('[*]', '', $v_rules);
	}else{
		preg_match("/\[(.*)?]/is", $v_rules, $index_arr);
		if($index_arr){
			if(strexists($index_arr[1], ',')){//li[0,x] li[0,4]
				$index_exp_arr = explode(',', $index_arr[1]);
				$start_index = $index_exp_arr[0];
				if(is_numeric($index_exp_arr[1])){//是数字
					$end_index = $index_exp_arr[1]+1;//因为是从0算起的，所有加1
				}else{//类似last x
					$end_index = $last_flag;
				}
				
			}else{//单个
				if(is_numeric($index_arr[1])){//是数字 li[4]
					$index = $index_arr[1];
				}else{//不是数字，类似 [last] [x]这种
					if(in_array($index_arr[1], array('last', 'x'))){
						$index = $last_flag;
					}
				}
			}
		}else{//默认
			if($is_get_all == 1 || !$is_last){//取全部
				$start_index = 0;
				$end_index = $last_flag;
			}else{
				$index = 0;
			}
		}
		$v_rules = $index_arr[0] && (is_numeric($index_arr[1]) || $index_arr[1] == 'last' || $index_arr[1] == 'x') ? str_replace($index_arr[0], '', $v_rules) : $v_rules;
	}
	return array('start_index' => $start_index, 'index' => $index, 'end_index' => $end_index, 'v_rules' => $v_rules, 'last_flag' => $last_flag);	
}



//递归查找
function dom_find_value($rules_arr, $html_obj, $text_type = 'innertext', $text_arr = array(), $is_get_all = 0, $i = 0){
	$is_last = $rules_arr[($i+1)] ? FALSE : TRUE;
	$value_index_data = get_value_index($rules_arr[$i], $is_get_all, $is_last);
	extract($value_index_data);
	$get_attr_data = get_attr_value($v_rules, $text_type);
	extract($get_attr_data);
	$html_dom_arr = $html_obj->find($v_rules);
	$all_count = count($html_dom_arr);
	if($all_count == 0) return $text_arr;
	$index = ($index > ($all_count - 1) || $index == $last_flag) ? ($all_count - 1) : $index;
	$end_index = ($end_index == -1 || $end_index == $last_flag) ? $all_count : $end_index;
	foreach($html_dom_arr as $index_i => $child) {
		if($index > -1){//取单个
			if($index != $index_i) continue;
		}else{//取范围
			if($index_i < $start_index || $index_i > $end_index) continue;
		}
		if(!$is_last){//如果还有下一层
			$text_arr = dom_find_value($rules_arr, $child, $text_type, $text_arr, $is_get_all, $i+1);
		}else{//已经是最后一层了，开始收集
			if($child->$attr_value) $text_arr[] = $child->$attr_value;
		}
	}
	return $text_arr;
}

//字符串截取
function str_get_str($content, $rules, $get_flag = 'data', $limit = 1, $is_return_array = 0){
	$c_arr = $r = $text_arr = array();
	$rules = trim($rules);
	if(empty($rules)) return;
	$rules = str_replace(array('\{', '\}'), array('{UUU', 'UUU}'), $rules);
	if(strexists($rules, '{') && strexists($rules, '}')){
		preg_match_all("/\{(.*)?}/isU", $rules , $c_arr, PREG_SET_ORDER);
		foreach($c_arr as $k => $v){
			$r['search'][] = $v[0];
			$c_dom_arr[] = $v[1];
		}
	}else{
		$c_dom_arr = array($rules);
	}
	foreach($c_dom_arr as $k => $v){
		$arr = pregmessage($content, $v, $get_flag, $limit);
		if($get_flag == 'reply') return $arr;
		if($limit == 1){
			$text_arr[] = $arr ? $arr[0] : '';
		}else{
			if($arr) $text_arr = array_merge($text_arr, $arr);
		}
	}
	if($is_return_array == 1) return $text_arr;
	$rules = str_replace(array('{UUU', 'UUU}'), array('{', '}'), $rules);
	if($r['search']) {
		$r['replace'] = $text_arr;
		$text = str_replace($r['search'], $r['replace'], $rules);
	}else{
		$text = implode('', $text_arr);
	}
	return $text;	
}



function rules_get_value($content, $get_type, $get_rules, $get_data = 'data', $html = ''){
	if($get_type == 1){//dom
		$html = $html ? $html : get_htmldom_obj($content);
		$value = dom_get_str($html, $get_rules);
	}else if($get_type == 2){//字符串
		$value = str_get_str($content, $get_rules);
	}
	return $value;
}

function get_best_answer($type, $rules, $contents){
	if($type == 1) $html = get_htmldom_obj($contents);
	$best_answer = $type == 1 ? dom_get_str($html, $rules) : str_get_str($contents, $rules, 'data');
	return $best_answer;
}


function rules_get_article($content,$rules_info){
	$url = $_GET['url'];
	$rules_info = pstripslashes($rules_info);
	$rules_info['title_filter_rules'] = dstripslashes(unserialize($rules_info['title_filter_rules']));
	$rules_info['content_filter_rules'] = dstripslashes(unserialize($rules_info['content_filter_rules']));
	require_once libfile('function/home');
	$base_url  = get_base_url($content);
	//先取标题
	if($rules_info['theme_get_type'] == 3){//智能识别
		pload('C:HtmlExtractor');
		pload('F:article');
		$he = new HtmlExtractor($content, $url);
		$data = (array)$he->get_text();
	}else if($rules_info['theme_get_type'] == 1){//dom获取
		$data = dom_single_article($content, array('title' => $rules_info['theme_rules']));
	}else if($rules_info['theme_get_type'] == 2){//字符串
		$re = pregmessage($content, '<title>[title]</title>', 'title', -1);
		$data['other']['old_title'] = $re[0];
		$re = pregmessage($content, $rules_info['theme_rules'], 'title', -1);
		$data['title'] = $re[0];
	}
	if(!trim($data['title'])) return $data;//如果标题都取不到，不必浪费时间获取内容
	
	
	$data['content'] = $data['content'] ? $data['content'] : rules_get_contents($content, $rules_info);
	
	if($rules_info['content_page_rules'] && $data['content']){//分页文章
		$content_page_arr = get_content_page($url, $content, $rules_info);
		if($content_page_arr){
			$rules_info['content_page_get_mode'] = 2;
			$args = array('oldurl' => array(), 'content_arr' => array(), 'content_page_arr' => $content_page_arr, 'page_hash' => array(), 'rules' => $rules_info, 'url' => $url);
			$data['content_arr'] = page_get_content($content, $args);
			foreach((array)$data['content_arr'] as $k => $v){
				$content_arr[] = $v['content'];
			}
			$data['content'] = implode('', $content_arr);
			
		}	
		
	}
	
	$data['title'] = unhtmlentities(strip_tags($data['title'], '&nbsp;'));
	//$data['content'] = unhtmlentities($data['content']);
	$data['title'] = getstr(trim($data['title']), 80, 1, 1, 0, 1);
	$data['content'] = getstr($data['content'], 0, 1, 1, 0, 1);
	//处理文章标题和内容，包括替换和过滤
	$format_args_title = array(
		'is_fiter' => $rules_info['is_fiter_title'],
		'show_type' => 'title',
		'test' => 2,
		'result_data' => $data['title'],
		'replace_rules' => $rules_info['title_replace_rules'],
		'filter_data' => $rules_info['title_filter_rules'],
	);
	$data['title'] = filter_article($format_args_title);
	
	$data['content'] = dstripslashes($data['content']);
	
	
	
	$format_args_content = array(
		'is_fiter' => $rules_info['is_fiter_content'],
		'show_type' => 'title',
		'test' => 2,
		'filter_html' => dunserialize($rules_info['content_filter_html']),
		'result_data' => $data['content'],
		'replace_rules' => $rules_info['content_replace_rules'],
		'filter_data' => $rules_info['content_filter_rules'],
	);
	$data['content'] = filter_article($format_args_content);
	
	$format_arr = format_article_imgurl($url, $data['content'], $base_url);
	$data['content'] = $format_arr['message'];
	
	unset($data['other']);
	return $data;
}


function rules_get_contents($content, $rules){
	//再取内容
	if($rules['content_get_type'] == 3){//智能识别
		if($rules['theme_get_type'] != 3){
			$he = new HtmlExtractor($content, '');
			$info_arr = (array)$he->get_text();
			return $info_arr['content'];
		}
	}else if($rules['content_get_type'] == 1){//dom获取
		$info_arr = dom_single_article($content, array('content' => $rules['content_rules']));
		return $info_arr['content'];
	}else if($rules['content_get_type'] == 2){//字符串
		return str_get_str($content, $rules['content_rules'], 'body', 1);
	}
	return FALSE;
}

//取得分页内容
function page_get_content($content, $args = array() ){
	extract($args);
	if(!$content_arr) {
		$page_hash[] = md5($content);
		$re_info['content'] = rules_get_contents($content, $rules);
		$re_info['page_url'] = $url;
		$re_info['page'] = 1;
		if(!$re_info){
			unset($content_arr);
			return FALSE;
		}
		if(intval($re_info) != -1) $content_arr[md5($url)] = $re_info;
	}
	foreach((array)$content_page_arr as $k => $v){
		if($v == '#' || !$v || $v == $url || in_array($v, $oldurl)) continue;
		$url_parse_arr = parse_url(strtolower($v));
		parse_str($url_parse_arr['query'], $page_temp_arr);
		if($page_temp_arr['page'] == 1) continue;
		$content = get_contents($v, array('cookie' => $rules['login_cookie']));
		$hash = md5($content);
		if(in_array($hash, $page_hash)) continue;
		$oldurl[] = $v;
		$page_hash[] = $hash;
		$num = count($content_arr) + 1;
		$re_info['content'] = rules_get_contents($content, $rules);
		$re_info['page_url'] = $v;
		$re_info['page'] = $num;
		$content_arr[md5($v)] = $re_info;
		
		if($rules['content_page_get_mode'] != 1){//上下页模式
			$content_page_arr = get_content_page($v, $content, $rules);
			$args = array('oldurl' => $oldurl, 'content_arr' => $content_arr, 'content_page_arr' => $content_page_arr, 'page_hash' => $page_hash, 'rules' => $rules, 'url' => $url);
			return page_get_content($content, $args);
		}			
	}
	return $content_arr;
}


//获取分页url列表
function get_content_page($url, $content, $rules){
	$base_url  = get_base_url($content);
	$base_url = $base_url ? $base_url : $url;
	if($rules['content_page_get_type'] == 1){
		$html = get_htmldom_obj($content);
		if(!$html) return false;
		foreach($html->find($rules['content_page_rules']) as $v) {
			$a_url = convert_url($v->attr['href']);
			if(!$a_url || $a_url == '#' || $v->innertext == milu_lang('up_page')) continue; 
			$item[] = _expandlinks($a_url, $base_url);
			$re_arr = sarray_unique($item);
			
		}
		$html->clear();
		unset($html);
	}else if($rules['content_page_get_type'] == 2){
		$re_arr = string_page_link($content, $rules['content_page_rules'], $url);//字符串
	}else{//表达式
		preg_match('/\[(.*)\]/is', $rules['content_page_rules'], $v_arr);
	}
	return $re_arr;
}

// get_type 1是单帖 2是内置规则 3学习到的规则

//服务端搜索规则
function cloud_match_rules($get_type, $url, $content){
	global $_G;
	pload('F:fastpick');
	$setting = get_pick_set();
	$pick_config = $_G['cache']['evn_milu_pick']['pick_config'];
	$server_cache_time = $pick_config['index_server_cache_time'];
	if($get_type == '3'){//智能学习规则索引过期时间比较短
		$server_cache_time = $pick_config['evo_index_server_cache_time'];
	}
	$milu_set = pick_common_get();
	if($setting['open_cloud_pick'] != 1 ) return FALSE;
	pload('F:copyright');
	$host_info = GetHostInfo($url);
	$domain = $host_info['host'];
	$domain_hash = md5($domain);
	$url_temp = preg_replace('/\d+/', '', $url);
	$arr_temp = parse_url($url_temp);
	$path_hash = md5($arr_temp['path']);
	$over_dateline = $_G['timestamp'] - $server_cache_time;
	$count = DB::result(DB::query("SELECT COUNT(*) FROM ".DB::table('strayer_searchindex')." WHERE  domain_hash='".$domain_hash."' AND path_hash='".$path_hash."' AND type='".$get_type."3' AND dateline > $over_dateline"), 0);//3是服务端 4是本地的缓存
	if($count) return FALSE;
	$args = array(
		'get_type' => $get_type,
		'url' => $url,
		//'content' => $content,//改成服务端自己取内容
	);
	$rpcClient = rpcClient();
	$client_info = get_client_info();
	$re = $rpcClient->cloud_match_rules($args, $client_info);
	if(is_object($re) || $re->Number == 0){
		if($re->Message) return  milu_lang('phprpc_error', array('msg' => $re->Message));
		$re = (array)$re;
	}
	$data = array();
	if($re['data_type'] == 1){//返回规则
		$rules_info = $re['data'];
		if($get_type == 3){
			$data = evo_rules_get_article($content, $rules_info);
		}else{
			$data = rules_get_article($content, $rules_info);
		}
		if($data || ($data['content'] && $get_type == 3)){//规则验证有效，下载到本地
			if($get_type == 3){
				$data_id = import_evo_data($rules_info);
			}else{
				$data_id = import_fastpick_data($rules_info);
			}
			if($data_id) {
				//先清除之前的索引
				DB::query('DELETE FROM '.DB::table('strayer_searchindex')." WHERE domain_hash='".$domain_hash."' AND path_hash='".$path_hash."'");
				add_search_index($domain_hash, $path_hash, $get_type.'4', $data_id);//添加索引
			}	
		}
		
	}else if($re['data_type'] == 2){//返回内容
		$data = $re['data'];
		
	}else{//一无所获,那也要告诉客户端，别再骚扰服务端了
		add_search_index($domain_hash, $path_hash, $get_type.'3', 0);
	}
	return $data;
}

function evo_rules_get_article($str, $rules_info){
	$rules_info['theme_get_type'] = $rules_info['theme_get_type'] ? $rules_info['theme_get_type'] : 1;
	$get = 0;
	if($rules_info['theme_get_type'] == 1 && $rules_info['content_get_type'] == 1){
		$re = dom_single_article($str, array('title' => $rules_info['theme_rules'], 'content' => $rules_info['content_rules']));
		$data['title'] = $re['title'];
		$data['content'] = $re['content'];
		$get = 1;
	}
	if($get != 1){
		if($rules_info['theme_get_type'] == 1){
			$re = dom_single_article($str, array('content' => $rules_info['content_rules']));
			$data['title'] = $re['title'];
		}else if($rules_info['theme_get_type'] == 2){
			$re = pregmessage($str, $rules_info['theme_rules'], 'title', -1);
			$data['title'] = $re[0];
		}
		if($rules_info['content_get_type'] == 1){
			$re = dom_single_article($str, array('content' => $rules_info['content_rules']));
			$data['content'] = $re['content'];
		}else if($rules_info['content_get_type'] == 2){
			$data['content'] = str_get_str($str, $rules_info['content_rules'], 'body', -1);
		}
	}
	
	//过滤
	
	if($rules_info['is_fiter_title'] == 1 && $data['title']){
		$format_args = array(
			'is_fiter' => $rules_info['is_fiter_title'],
			'show_type' => 'title',
			'result_data' => $data['title'],
			'replace_rules' => $rules_info['title_replace_rules'],
			'filter_data' => dunserialize($rules_info['title_filter_rules']),
			'test' => 2,
			'filter_html' => '',
		);
		$data['title'] = filter_article($format_args);
	}
	if($rules_info['is_fiter_content'] == 1 && $data['content']){
		$format_args = array(
			'is_fiter' => $rules_info['is_fiter_content'],
			'show_type' => 'body',
			'result_data' => $data['content'],
			'replace_rules' => $rules_info['content_replace_rules'],
			'filter_data' => dunserialize($rules_info['content_filter_rules']),
			'test' => 2,
			'filter_html' => dunserialize($rules_info['content_filter_html']),
		);
		$data['content'] = filter_article($format_args);
	}
	return $data;
}

//计算出多个相似url中的分页位置
function get_url_diff($url_arr){
	if(!$url_arr) return;
	foreach($url_arr as $k => $v){
		preg_match_all("/[\d]+/", $v, $arr);
		$c_arr[$k] = count($arr[0]); 
		$v_arr[$k] = $arr[0];
	}
	$avg = get_avg($c_arr);
	foreach($v_arr as $k => $v){
		if(!$v || $c_arr[$k] < $avg) {
			if($c_arr[$k] < $avg) $re['index'] = $url_arr[$k];
			unset($url_arr[$k]);
			continue;
		}	
		$split_arr[] = $v;
	}
	$t_arr = $split_arr;
	$split_rand_key = array_rand($split_arr);
	unset($t_arr[$split_rand_key]);
	$t_rand_key = array_rand($t_arr);
	$t_v = $t_arr[$t_rand_key];
	foreach($split_arr[$split_rand_key] as $k => $v){
		if($v == $t_v[$k]) continue;
		$diff_key = $k;
	}
	
	$min_v = $split_arr[0][$diff_key];
	$re['auto'] = $min_v < 10 && strlen($min_v) != 1 ? 1 : 0;
	
	$rand_key = array_rand($url_arr);
	$temp_url = $url_arr[$rand_key];
	$s_arr = preg_split("/[\d]+/", $temp_url);
	$split_arr[$split_rand_key][$diff_key] = '(*)';
	$url = '';
	foreach($s_arr as $k => $v){
		$url .= $v.$split_arr[$split_rand_key][$k];
	}
	$re['url'] = $url;
	return $re;
}


//判断一个地址是否是文章页
function check_fastpick_viewurl($url, $lilely_page = array()){
	$url_arr = parse_url($url);
	if($url_arr['path'] == '/' || !$url_arr['path']) return FALSE;
	if($url_arr['query']){
		parse_str($url_arr['query'], $url_info);
		if(!preg_match('/\d+/', $url_arr['query'])) return FALSE;
		if($url_info['page']) return FALSE;
	}else{
		$file_ext = addslashes(strtolower(substr(strrchr($url_arr['path'], '.'), 1, 10)));
		if(!$file_ext) {//形如 http://kb.cnblogs.com/page/146617/
			if(preg_match('/\d+/', $url_arr['path'])) {
				if(!filter_something($url_arr['path'], array('list'), TRUE))  return FALSE;
				return TRUE;
			}
		}	
		$ext_arr = array('html', 'htm', 'shtml');
		if(!in_array($file_ext,  $ext_arr)) return FALSE;
		if(!preg_match('/\d+/', $url_arr['path'])) return FALSE;//宁可错杀一千，不放过一个
	}
	$lilely_page_arr = !is_array($lilely_page) ? array($lilely_page) : $lilely_page;
	foreach($lilely_page_arr as $k => $v){
		similar_text($v, $url, $percent);
		if($percent > 90) return FALSE;
	}
	return TRUE;
}


function _striplinks($document, $base_url = ''){	
	if(!trim($document)) return;
	preg_match_all("'<\s*a\s.*?href\s*=\s*			# find <a href=
					([\"\'])?					# find single or double quote
					(?(1) (.*?)\\1 | ([^\s\>]+))		# if quote found, match up to next matching
												# quote, otherwise match up to next space
					'isx",$document,$links);
					

	// catenate the non-empty matches from the conditional subpattern
	while(list($key,$val) = each($links[2])) {
		if(!empty($val))
			$match[] = _expandlinks($val, $base_url);
	}				
	
	while(list($key,$val) = each($links[3])){
		if(!empty($val))
			$match[] = _expandlinks($val, $base_url);
	}		
	// return the links
	return $match;
}


//统计一段html中有多少条指向自己的链接
function own_link_count($html, $url) {
	$domain_name = get_domain($url);
	$link_arr = _striplinks($html);
	if($link_arr){
		$re_link_arr = is_array($link_arr) ? array_unique($link_arr) : $link_arr;
		$i = 0;
		foreach($re_link_arr as $v){
			$v = convert_url($v);
			if(strexists(trim(_expandlinks($v, $url)), $domain_name)){
				$i++;
			}
		}
		return $i;
	}
}

function evo_get_pagelink($content, $url, $list = array()){
	$list = $list ? $list : $url;
	$rules_info = match_rules($url, $content, 4, 0);
	if($rules_info && is_array($rules_info)){	
		if($rules_info['page_get_type'] == 1){
			$link_arr = dom_page_link($content, array('page_link_rules' => $rules_info['page_link_rules'], 'url' => $url) );
		}else if($rules_info['page_get_type'] == 2){
			$link_arr = string_page_link($content, trim($rules_info['page_link_rules']), $url);
		}
		
	}
	if($link_arr) return $link_arr;
	$base_url  = get_base_url($content);
	$base_url = $base_url ? $base_url : $url;
	$link_arr = _striplinks($content, $base_url);
	if(!$link_arr) return array();
	foreach((array)$link_arr as $k => $v_url){
		if(!check_fastpick_viewurl($v_url, $url)) {
			unset($link_arr[$k]);
			continue;
		}	
		$c_arr[$k] = strlen($v_url);
	}	
	
	$value_count_arr = array_count_values($c_arr);
	arsort($value_count_arr);
	$value_count_arr = array_keys ($value_count_arr);
	$view_lenth = array_shift ($value_count_arr);
	$link_arr = array_resolve($link_arr);
	foreach($link_arr as $k => $v){
		if(abs(strlen($v) - $view_lenth) > 5) {
			unset($link_arr[$k]);
		}
	}
	$link_arr = array_filter($link_arr, 'filter_url_callback');
	return $link_arr;
}


function json_get_pagelink($content, $rule, $url){
	
}

function exp_get_pagelink($rule, $url){
	$rule = str_replace(array('[url]'), array($url), $rule);
	preg_match_all('/\[(.*?)\]/is', $rule, $v_arr);
	$start = 0;
	foreach($v_arr[1] as $k => $v){
		if(strexists($v, ',')){
			$rule = str_replace($v_arr[0][$k], '[page]', $rule);
			$page_info = explode(',', $v);
			$start = $page_info[0];
		}
	}
	return array('url' => $rule, 'start' => $start);
}

function get_redirect_attach_url($rules, $url, $content, $login_cookie = ''){
	extract($rules);
	
	$data_arr = array();
	if($attach_redirect_url_get_type == 1){//dom
		$html = get_htmldom_obj($content);
		$link_arr = (array)dom_get_str($html, $attach_redirect_url_get_rules, array('is_return_array' => 1, 'text_type' => 'href'));
		if(!$link_arr[0]) return $data_arr;
		$base_url  = get_base_url($content);
		$base_url = $base_url ? $base_url : $url;
		$attach_redirect_url = _expandlinks($link_arr[0], $base_url);
	}else{
		$link_arr = (array)string_page_link($content, $attach_redirect_url_get_rules, $url);
	}
	
	if(!$attach_redirect_url) return $data_arr;
	
	$content = get_contents($attach_redirect_url, array('cookie' => $login_cookie));
	if($attach_download_url_get_type == 1){//dom
		$html = get_htmldom_obj($content);
 		$link_arr = (array)dom_get_str($html, $attach_download_url_get_rules, array('is_return_array' => 1, 'text_type' => 'href'));
		$base_url  = get_base_url($content);
		$base_url = $base_url ? $base_url : $attach_redirect_url;
		$attach_download_url = $link_arr[0] ? _expandlinks($link_arr[0], $base_url) : '';
	}else{
		$link_arr = (array)string_page_link($content, $attach_download_url_get_rules, $url);
		$attach_download_url = $link_arr[0];
	}
	return array('attach_redirect_url' => $attach_redirect_url, 'attach_download_url' => $attach_download_url);
}

function get_other_info($content, $args){
	if(!$content) return false;
	extract($args);
	if(!$from_get_rules && !$author_get_rules && !$dateline_get_rules) return false;
	$html = get_htmldom_obj($content);
	if(!$html) return false;
	if($from_get_rules){
		if($from_get_type == 1){
			$re['from'] = dom_get_str($html, $from_get_rules);
		}else{
			$re['from'] = str_get_str($content, $from_get_rules, 'data');
		}
	}
	if($author_get_rules){
		if($author_get_type == 1){
			$re['author'] = dom_get_str($html, $author_get_rules);
		}else{
			$re['author'] = str_get_str($content, $author_get_rules, 'data');
		}	
	}
	if($dateline_get_rules){
		if($dateline_get_type == 1){
			$re['article_dateline'] =dom_get_str($html, $dateline_get_rules);
			unset($div);
		}else{
			$re['article_dateline'] = str_get_str($content, $dateline_get_rules, 'data');
		}		
	}
	foreach((array)$re as $k => $v){
		$v = _striptext(trim($v));
		$re[$k] = format_html($v);
	}
	$html->clear();
	unset($html);
	return $re;
}


?>