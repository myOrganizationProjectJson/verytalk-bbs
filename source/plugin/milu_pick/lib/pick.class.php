<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class pick{
	var $now_url;//正在处理的url
	var $p_arr;//采集器资料
	var $r_arr;//内置规则信息
	var $rid;//内置规则id
	var $pid;//采集器id
	var $max_level;//最大深度
	var $now_url_arr;//采集器的临时url
	var $i;//目前访问的url数
	var $v_i;//被跳过的url数
	var $a;//目前取到的文章数
	var $v_a;//丢弃的文章数
	var $visit_count;//这个是访问的url数，因为后来加了第二层，i已经不适合跳转判断了。
	var $base_url;//根地址
	var $reply_page;//第几页的回复
	var $cache_time;//
	var $snoopy;
	var $msg_args;//提示
	var $plugin_set;
	var $words;//同义词资料
	var $error;
	var $public_info;//文章发布之后，把发布后的信息保存在里面
	var $status_arr;
	var $min_title_len = 2;//标题最低长度
	var $pick_set;
	var $cache_likely_page;
	var $min_own_link = 10;
	var $all_get_time = 0;
	var $pick_cache_data = array();
	var $temp_arr = array();//传递临时数据
	var $pick_config;
	var $rand_reply_data;//自动生成回复
	function pick($pid = 0, $is_cron = 0){
		pload('F:spider');
		pload('F:article');
		pload('F:pick');
		$this->_ini_config($pid, $is_cron);
	}
	
	function _ini_config($pid = 0, $is_cron = 0){
		global $_G;
		$this->error = '';
		if($pid == 0 && $is_cron > 0) {
			$this->error = 1;
			return;
		}	
		$this->pid = $pid >0 ? intval($pid) : intval($_GET['pid']);
		$this->pick_cache_data = load_cache('pick'.$this->pid.'_'.$is_cron);
		$this->now_url = $this->pick_cache_data['now_url'];
		$this->pick_cache_data['now_level'] = $this->pick_cache_data['now_level'] ? $this->pick_cache_data['now_level'] : 0;
		$this->i = $this->pick_cache_data['i'] ? $this->pick_cache_data['i'] : 1;
		$this->visit_count = $this->pick_cache_data['visit_count'] ? $this->pick_cache_data['visit_count'] : 0;
		$this->v_i = $this->pick_cache_data['v_i'] ? $this->pick_cache_data['v_i'] : 0;
		$this->a = $this->pick_cache_data['a'] ? $this->pick_cache_data['a'] : 0;
		$this->v_a = $this->pick_cache_data['v_a'] ? $this->pick_cache_data['v_a'] : 0;
		$this->all_get_time = $this->pick_cache_data['all_get_time'] ? $this->pick_cache_data['all_get_time'] : 0;
		$this->pick_cache_data['have_reply'] = 0;
		$this->plugin_set = get_pick_set();
		$this->pick_set = pick_common_get();//插件设置
		$this->pick_config = $_G['cache']['evn_milu_pick'];
		$this->pick_cache_data['no_check_url'] = isset($_GET['no_check_url']) ? intval($_GET['no_check_url']) : $this->pick_cache_data['no_check_url'];
		$is_log = 0;
		if($is_cron > 0 && $this->pick_set['is_log_cron'] == 1) $is_log = 1;
		$this->msg_args = $this->status_arr = array(
			'is_cron' => $is_cron,
			'pid' => $this->pid,
			'is_log' => $is_log,
		);
		if($this->i == 1) show_pick_info(milu_lang('pick_start'), '', $this->msg_args);
		$p_arr = get_pick_info($this->pid);
		
		if(!VIP) unset($p_arr['reply_rules'],$p_arr['reply_is_extend']);
		if($p_arr['rules_type'] == 3)  $p_arr['url_range_type'] = '';//新增
		
		$format_array = array('rules_var', 'many_page_list', 'title_filter_rules', 'content_filter_rules', 'reply_filter_rules', 'content_filter_html', 'reply_filter_html', 'public_class', 'public_class', 'forum_threadtypes');
		foreach($format_array as $k => $v){
			if(is_array($p_arr[$v])) continue;
			$p_arr[$v] = dunserialize($p_arr[$v]);
		}

		if($p_arr['is_login'] == 2) $p_arr['login_cookie'] = '';
		if(!$p_arr['reply_max_num']) $p_arr['reply_max_num'] = 200000;//如果没有设置回复，这个就是最大数目了
		if($p_arr['reply_is_extend']){//继承内容规则
			$p_arr['reply_get_type'] = $p_arr['content_get_type'];
			$p_arr['reply_rules'] = $p_arr['content_rules'];
			if($p_arr['is_fiter_content'] == 1){//内容是过滤的
				if($p_arr['is_fiter_reply'] == 1){//回复是过滤的
					$p_arr['reply_replace_rules'] = array_merge((array)$p_arr['reply_replace_rules'], (array)$p_arr['content_replace_rules']);
					$p_arr['reply_filter_rules'] = array_merge((array)$p_arr['content_filter_rules'], (array)$p_arr['reply_filter_rules']);
					$p_arr['reply_filter_html'] = array_merge((array)$p_arr['content_filter_html'], (array)$p_arr['reply_filter_html']);
				}else{//内容过滤，回复不过滤
					$p_arr['reply_replace_rules'] = $p_arr['content_replace_rules'];
					$p_arr['reply_filter_rules'] = $p_arr['content_filter_rules'];
					$p_arr['reply_filter_html'] = $p_arr['content_filter_html'];
					$p_arr['is_fiter_reply'] = 1;
				}	
			}
		}
		//设置编码
		pset_charset($p_arr['charset_type']);
		$p_arr['stop_time'] = explode(',', $p_arr['stop_time']);
		$p_arr['stop_time'] = array_map('intval', $p_arr['stop_time']);
		$this->p_arr = $p_arr;
		$rules_hash = $this->p_arr['rules_hash'];
		if($this->p_arr['is_auto_public'] == 1 && !$this->p_arr['public_class'][0]){//既设置了自动发布，又没有设置发布栏目
			$this->error = 1;
			show_pick_info(milu_lang('no_set_public_class'), 'exit', $this->msg_args);
			return;
		}
		if($this->p_arr['is_auto_public'] == 1 && $this->p_arr['is_word_replace'] == 1){//即自动发布,又设置了同义词替换
			$this->words = get_replace_words();
		}
		
		//设置了附件本地化，而且设置了允许下载附件的扩展名
		$this->plugin_set['attach_download_allow_ext'] = $this->p_arr['is_download_file'] == 1 && !empty($this->plugin_set['attach_download_allow_ext']) ? explode('|', $this->plugin_set['attach_download_allow_ext']) : array();
		if($this->p_arr['page_fiter'] == 2){
			$this->p_arr['page_url_other'] = $this->p_arr['page_url_no_other'] = '';
		}
		
		pload('F:rules');
		if($rules_hash) $r_arr = get_rules_info($rules_hash);
		$r_arr['url_var'] = dunserialize($r_arr['url_var']);
		$this->r_arr = $r_arr;
		$this->rid = $r_arr['rid'];
		$this->max_level = $this->pick_cache_data['max_level'];
		if(!$this->pick_cache_data) update_times($this->pid);
		if(!$this->pick_cache_data['start_time']) $this->pick_cache_data['start_time'] = TIMESTAMP;
		$this->cache_time = intval($this->plugin_set['pick_cache_time'])*3600;//缓存
		if($this->cache_time == 0) $this->cache_time = -1;
		$this->min_own_link = !empty($this->plugin_set['min_own_link']) ? $this->plugin_set['min_own_link'] : $this->min_own_link;
		$this->p_arr['content_page_get_mode'] = 2;//去掉了分页模式这个功能，统一都是上下页
		
		//设置采集总数
		if($this->msg_args['is_log'] == 1){//计划任务设定采集总数
			$this->p_arr['pick_num'] = $this->p_arr['pick_num'] ? $this->p_arr['pick_num'] : 10000000;//读取链接数
			$this->p_arr['pick_article_num'] = $this->p_arr['pick_article_num'] ? $this->p_arr['pick_article_num'] : 1000000;//采集文章数
			if($this->i > 0 && $this->p_arr['auto_pick_from_last'] == 1){//加上上一次的
				$this->p_arr['pick_num'] += $this->i;
				$this->p_arr['pick_article_num'] += $this->a - $this->v_a;
			}
		}
		$this->pick_cache_data['jump_flag'] = 0;
		$this->plugin_set['title_length'] = $this->plugin_set['title_length'] ? $this->plugin_set['title_length'] : 80;
		if(!VIP){//免费版
			if($this->p_arr['is_get_reply'] == 1 && $this->p_arr['is_setting_article_page'] == 1) $this->p_arr['is_setting_article_page'] = 2;
		}
		
	}
	
	function get_redirect_attach($url, $content){
		if($this->pick_cache_data['attach']['attach_download_url'] || $this->p_arr['is_attach_setting'] != 1) return;
		$arr = get_redirect_attach_url(array('attach_redirect_url_get_type' => $this->p_arr['attach_redirect_url_get_type'], 'attach_redirect_url_get_rules' => $this->p_arr['attach_redirect_url_get_rules'], 'attach_download_url_get_type' => $this->p_arr['attach_download_url_get_type'], 'attach_download_url_get_rules' => $this->p_arr['attach_download_url_get_rules']), $url, $content, $this->p_arr['login_cookie']);
		$this->pick_cache_data['attach']['attach_redirect_url'] = $arr['attach_redirect_url'];
		$this->pick_cache_data['attach']['attach_download_url'] = $arr['attach_download_url'];
	}
	
	//解析页面，得到文本，链接等
	function parse_page($type = 'content', $content = ''){
		$this->now_url = cnurl($this->now_url);
		$data_arr = load_cache($this->now_url);
		$content = $data_arr['content'];
		if( $this->cache_time > 0 && $content  && $data_arr['cookie'] == $this->p_arr['login_cookie']){
			if($content) $message = $content;
			$this->base_url  = get_base_url($message);
			if(!$this->base_url) $this->base_url = $this->now_url;
			if($type == 'content'){
				return $message;
			}else if($type == 'link'){
				return _striplinks($message, $this->base_url);
			}
		}else{
			$time_out = !empty($this->plugin_set['time_out']) ? $this->plugin_set['time_out'] : 15;
			$max_redirs = !empty($this->plugin_set['max_redirs']) ? $this->plugin_set['max_redirs'] : 3;
			$error = milu_lang('unable_pick');
			if(!function_exists('fsockopen') && !function_exists('pfsockopen') && !function_exists('file_get_contents')){
				show_pick_info($error, 'exit', $this->msg_args);
				return;
			}
			
			if(!function_exists('fsockopen') && !function_exists('pfsockopen')){
				if(!function_exists('file_get_contents')){
					show_pick_info($error, 'exit', $this->msg_args);
					return;
				}
				$content = file_get_contents($this->now_url);
				$content = str_iconv($content);
				return $content;
			}
			
			if(!$this->snoopy){
				require_once(PICK_DIR.'/lib/Snoopy.class.php');
				//顺序不可以随意
				$this->snoopy = new Snoopy;  
				$this->snoopy->maxredirs = $max_redirs;   
				$this->snoopy->expandlinks = TRUE;
				$this->snoopy->offsiteok = TRUE;//是否允许向别的域名重定向
				$this->snoopy->maxframes = 3;
				$this->snoopy->agent = $this->plugin_set['user_agent'] ? $this->plugin_set['user_agent'] : $_SERVER['HTTP_USER_AGENT'];//不设置这里，有些网页没法获取
				$this->snoopy->referer = $this->now_url;
				$this->snoopy->rawheaders["COOKIE"] = $this->p_arr['login_cookie'];
				$this->snoopy->read_timeout = $time_out;
			}	
			if($type == 'content'){
				$this->snoopy->results = get_contents($this->now_url, array(
					'cookie' => $this->p_arr['login_cookie'],
					'max_redirs' =>  $max_redirs ,
					'time_out' => $time_out,
					'cache' => $this->cache_time,
				)); 
				
			}else if($type == 'link'){
				if($this->snoopy->fetchlinks($this->now_url));
			}
			$this->base_url  = get_base_url($this->snoopy->results);
			if(!$this->base_url) $this->base_url = $this->now_url;
			if($this->snoopy->results) cache_data($this->now_url, array('content' => $this->snoopy->results, 'cookie' => $this->p_arr['login_cookie']), $this->cache_time);
			return $this->snoopy->results;
		}	
	}
	//解析内置规则
	function parse_rules(){
		if($this->p_arr['rules_type'] > 1) return ;
		//判断内置规则是列表采集还是直接文章详细页采集
		$page_url = $this->r_arr['page_url'];
		$page_url_arr = explode('(*)', $page_url);
		$url_last_str = array_pop($page_url_arr);
		if(trim($this->p_arr['page_link_rules'])){//填了列表规则就是从列表采集
			$this->p_arr['url_range_type'] = 1;
			$this->max_level = 2;			
		}else{
			$this->p_arr['url_range_type'] = 2;
			$this->max_level = 1;			
		}
		foreach((array)$this->p_arr['rules_var'] as $k => $v){
			$value_arr = $this->get_var_set_value($v);
			$set_arr[$k] = $value_arr['set'];
			$type_arr[$k] = $value_arr['type'];
		}
		$i = 0; //重新构造数组的索引
		foreach((array)$page_url_arr as $k => $v){
			$k = $k + 1;
			if(is_array($set_arr[$k])){
				if($type_arr[$k] == 'range'){//范围
					$args['start'] = $set_arr[$k][0];
					$args['end'] = $set_arr[$k][1];
					$args['step'] = $this->r_arr['url_var'][$k]['var_ext_step'][$k] ? intval($this->r_arr['url_var'][$k]['var_ext_step'][$k]) : 1;
					$args['url'] = $v.'(*)';//函数需要，临时给他安上
					$url_arr[$i] = convert_url_range($args);
				}else{
					foreach($set_arr[$k] as $k1 => $v1){
						$url_arr[$i][$k1] = $v.$v1;
					}
				}
			}else{
				$url_arr[$i] = $v.$set_arr[$k];
			}
			$i++;
		}
		array_push($url_arr, array($url_last_str));
		//利用数组拼接成可以使用的url
		$new_url_arr = my_array_merge($url_arr);
		$this->temp_arr['page_num'] = count($new_url_arr);
		$this->now_url_arr = $new_url_arr;
	}
	
	function get_var_set_value($value){
		if(is_array($value)) return array('type' => 'or', 'set' => $value);
		if(strexists($value, ',')){//范围
			$arr['set'] = format_wrap($value, ',');
			$arr['type'] = 'range';
		}else if(strexists($value, '|')){
			$arr['set'] = format_wrap($value, '|');
			$arr['type'] = 'or';
		}else{
			$arr['set'] = array($value);
			$arr['type'] = 'normal';
		}
		return $arr;
	}
	
	//取得起始url
	function get_start_url(){
		if($this->p_arr['rules_type'] == 1){//如果采集器采用内置规则
			$this->parse_rules();
		}else if($this->p_arr['rules_type'] == 2){//自定义规则
			if($this->p_arr['url_range_type'] == 1 || $this->p_arr['url_range_type'] == 2){//从分页列表采集文章或url范围
				$args['step'] = $this->p_arr['page_url_auto_step'];
				$args['start'] = $this->p_arr['page_url_auto_start'];
				$args['end'] = $this->p_arr['page_url_auto_end'];
				$args['url'] = $this->p_arr['url_page_range'];
				$args['auto'] = $this->p_arr['page_url_auto'];
				$this->now_url_arr = convert_url_range($args);
				$this->max_level = 2;
				if($this->p_arr['url_range_type'] == 2) {
					$this->max_level = 1;
					$this->temp_arr['per_num'] = 1;
				}else{
					$this->temp_arr['page_num'] = count($this->now_url_arr);
				}	
			}else if($this->p_arr['url_range_type'] == 4){//从rss地址
				$this->now_url_arr = get_rss_url(2, $this->p_arr['rss_url']);
				$this->max_level = 1;
			}else if($this->p_arr['url_range_type'] == 5){//多层列表
				$this->now_url_arr = array($this->p_arr['many_list_start_url']);
				$this->max_level = count($this->p_arr['many_page_list'])  + 1;
			}
		}else if($this->p_arr['rules_type'] == 3){//一键采集
			$start_arr = format_wrap($this->p_arr['manyou_start_url']);
			$this->now_url = $start_arr[0];
			$content = $this->parse_page();
			$rules_info = match_rules($this->now_url, $content, 4, 0);
			if($rules_info && is_array($rules_info)){	
				$this->pick_cache_data['lilely_page'][] = $this->now_url; 
				if($rules_info['page_get_type'] == 1){
					$this->now_url_arr = dom_page_link($content, array('page_link_rules' => $rules_info['page_link_rules'], 'url' => $this->now_url) );
				}else{
					$this->now_url_arr = string_page_link($content, trim($rules_info['page_link_rules']), $this->now_url);
				}
			}
			$page_url_arr = parse_url($this->now_url);
			parse_str($page_url_arr['query'], $url_info);
			$index_url = $auto = 0;
			if(is_numeric($url_info['page'])) {
				$var_url = str_replace('page='.$url_info['page'], 'page=(*)', $this->now_url);
				$this->pick_cache_data['lilely_page'][] = $this->now_url; 
			}else{	
				$page_all_link = $this->parse_page('link', $content);
				$page_all_link = array_filter($page_all_link, 'filter_url_callback');
				$likely_arr[0] = $this->now_url;
				foreach((array)$page_all_link as $k => $v){
					similar_text($v, $this->now_url, $percent);
					if($percent < 90) continue;
					$likely_arr[] = $v; 
				}
				$likely_arr = array_resolve($likely_arr);
				$var_arr = get_url_diff($likely_arr);
				$var_url = $var_arr['url'];
				$index_url = $var_arr['index'];
				$auto = $var_arr['auto'];
				if($var_url && is_array($likely_arr)) {
					$key = array_rand($likely_arr);
					$this->pick_cache_data['lilely_page'][] = $likely_arr[$key];
				}
			}
			if($var_url){
				$this->now_url_arr = convert_url_range(array('url' => $var_url, 'step' => 1, 'start' => $var_arr['index'] ? 2 : 1, 'end' => 99, 'auto' => $auto) );
				if($var_arr['index']) array_unshift($this->now_url_arr, $var_arr['index']);
				$this->max_level = 2;
			}else{
				$this->now_url_arr = $start_arr;
				$this->max_level = $this->max_level ? $this->max_level : 2;
			}
			$this->max_level = $this->p_arr['manyou_max_level'] ? $this->p_arr['manyou_max_level'] : 2;
		}
		if($this->p_arr['page_fiter'] == 1  && $this->now_url_arr){//开启了过滤网址功能
			if($this->p_arr['page_url_other']) {
				$this->now_url_arr = array_merge(format_wrap($this->p_arr['page_url_other']), $this->now_url_arr);
				$this->temp_arr['page_num'] = count($this->now_url_arr);	
			}	
		}
		$this->pick_cache_data['max_level'] = $this->max_level;
	}
	
	function run_start($clear_log = 0){
		if($this->error) return FALSE;
		if($_POST['clear_log'] || $clear_log == 1){
			unset($this->pick_cache_data);
			$this->i = 1;
			$this->visit_count = 1;
			show_pick_info(milu_lang('clear_log'), '', $this->msg_args);
			update_times($this->pid);
			DB::query('DELETE FROM '.DB::table('strayer_url')." WHERE pid = '".$this->pid."'");
		}
		if($this->i > 1 ){
			$this->get_now_url_arr($this->pick_cache_data['now_level']);
			if(!$this->now_url_arr && !$this->pick_cache_data['attach']['data_arr'] && intval($this->pick_cache_data['reply']['page']) == 0 && intval($this->pick_cache_data['content_page']['page']) == 0 && !$this->pick_cache_data['post_user_arr']['data_uid']) {
				$this->pick_cache_data = NULL;
				cache_del('pick'.$this->pid.$this->msg_args['is_cron']);
			}	

		}else{
			$this->get_start_url();//获得入口地址
			$this->pick_cache_data['now_level'] = $this->max_level;
		}
		if( $this->p_arr['rules_type'] == 1 && !$this->rid ){
			show_pick_info(milu_lang('pick_no_select_rules'), 'exit', $this->msg_args);
			return FALSE;
		}

		if(!$this->now_url_arr && $this->i == 1){ 
			show_pick_info(milu_lang('pick_no_link'), 'exit', $this->msg_args);
			return FALSE;
		}
		if($this->i == 1) show_pick_info(milu_lang('pick_get_linked').milu_lang('el'), '', $this->msg_args);
		if(!$this->max_level){
			show_pick_info(milu_lang('no_set_level'), 'exit',$this->msg_args);
			return FALSE;
		}
		$this->pick_cache_data['url_del_flag'] = array();
		$this->run_get_other();
		$this->robot($this->pick_cache_data['now_level']);
		$this->finsh();
	}

	function robot($level){
		global $_G;
		$pick_config = $_G['cache']['evn_milu_pick']['pick_config'];
		$del_flag = 0;
		$this->pick_cache_data['now_level'] = $level;
		if(!$this->now_url_arr) $this->restart_robot($this->pick_cache_data['now_level']);
		if(!$this->pick_cache_data['url_arr'][$this->pick_cache_data['now_level']]) {
			$this->pick_cache_data['url_arr'][$this->pick_cache_data['now_level']] = $this->now_url_arr;
		}
		foreach((array)$this->now_url_arr as $k => $url){
			if(!$this->p_arr) return;
			d_s('run');
			if($this->p_arr['pick_num'] && ($this->i == $this->p_arr['pick_num'] + 2) || ( $this->p_arr['pick_num'] && $this->i > $this->p_arr['pick_num'] + 2) ) return;
			$this->now_url = $url;
			if($this->p_arr['url_range_type'] == 3 || $this->pick_cache_data['now_level'] == $this->p_arr['manyou_max_level']){
				$host_arr = $this->GetHostInfo($url);
				$this->base_url = $host_arr['host'];
			}
			//替换网址
			$url = ($this->max_level == $this->pick_cache_data['now_level'] && $this->pick_cache_data['now_level'] == 1) ? $this->page_link_replace($url, $this->p_arr['page_link_replace_rules']) : $url;
			$this->format_url();
			$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1, 'now' => $this->i));
			show_pick_info(array(milu_lang('read_link'), $this->now_url), 'url', $show_args);
			$this->i++;
			$this->visit_count++;
			$this->pick_cache_data['have_reply'] = 0;
			$this->pick_cache_data['i'] = $this->i;
			$visit_flag = $this->check_visit_url();
			if($visit_flag > 0 ){
				$this->insert_url();
				if($this->pick_cache_data['now_level'] == 1){
					if($this->p_arr['rules_type'] == 3){//若是一键采集,判断此网址是否是文章页
						if(!$this->check_fastpick_viewurl($this->now_url)) {
							continue;
						}else{
							
						}
					}
					$content = $this->parse_page();
					$this->status_arr['now'] = $this->i;
					show_pick_info('', 'success', $this->status_arr);
					if(!$this->check_login_cookie($content)){//检测cookie
						$this->error = 1;
						show_pick_info(milu_lang('login_check_fail'), 'exit', $this->msg_args);
						exit();
					}
					
					
					if($this->p_arr['stop_time'][0]) sleep($this->p_arr['stop_time'][0]);
					$get = 0;
					$this->pick_cache_data['have_page'] = 0;
					if($this->p_arr['content_page_rules'] ) {//分页文章
						if($this->p_arr['is_get_reply'] == 1 && ($this->p_arr['reply_rules'] || $this->p_arr['reply_is_extend'])){//回复
						}else{
							$content_page_arr = $this->get_content_page($content);
							if($content_page_arr){
								$get = 1;
								$this->a++;
								$this->pick_cache_data['a'] = $this->a;
								$this->pick_cache_data['have_page'] = 1;
								$this->pick_cache_data['content_page'] = '';
								$this->pick_cache_data['run_mod'] = 'content_page';
								$this->run_get_other($content);
							}
						}
					}
					if($get == 0){//普通文章
						$ori_title = $this->get_ori_title($content);
						$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1));
						show_pick_info(array(milu_lang('read_content'), cutstr($ori_title, 85)), 'left', $show_args);
						//无法获取网页内容
						if(empty($content)){
							show_pick_info(milu_lang('no_read_content_err'), 'err', $this->status_arr);
							$this->v_a++;
							$this->pick_cache_data['v_a'] = $this->v_a;
						}else{
							if(!$this->check_page_content($content)){//检测内容
								show_pick_info(milu_lang('content_check_fail'), 'err', $this->status_arr);
								$this->v_a++;
								$this->pick_cache_data['v_a'] = $this->v_a;
							}else{
								$article_info = $this->get_article($content);
								$this->status_arr['now'] = $now;
								show_pick_info('', 'success', $this->status_arr);
								
								$article_info = $this->format_article($article_info);
								$this->get_pick_status();
								$this->status_arr['now'] =  '-'.($this->i - 1).time().rand(1,9999);
								$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1, 'now' => $now));
								$this->temp_arr['normal_now'] = $now;
								show_pick_info(array(milu_lang('article').' ', cutstr(trim($article_info['title']), 85)), 'left', $show_args);
								
								if($this->check_article($article_info)){
									$this->create_article($article_info);
								}else{
									$this->v_a++;
									$this->pick_cache_data['v_a'] = $this->v_a;
								}
							}
						}
						
					}
					
					
				}
				$msg = '';
				$link_count = 0;
				$next_link = array();
				if($this->pick_cache_data['now_level'] > 1){
					if($this->p_arr['url_range_type'] == 1 || $this->p_arr['url_range_type'] == 5 || $this->p_arr['rules_type'] == 1){//分页列表或多层列表获取是内置规则
						if($this->p_arr['url_range_type'] == 5){
							$key_level  = abs($this->pick_cache_data['now_level'] - 1 - count($this->p_arr['many_page_list'])) + 1;
							$rules_arr = $this->p_arr['many_page_list'][$key_level];
						}else if($this->p_arr['url_range_type'] == 1 || $this->p_arr['rules_type'] == 1){
							$rules_arr['type'] = $this->p_arr['page_get_type'];
							$rules_arr['rules'] = $this->p_arr['page_link_rules'];
						}
						$content = $this->parse_page();
						if($rules_arr['type'] == 1){
							$next_link = dom_page_link($content, array('page_link_rules' => $rules_arr['rules'], 'url' => $this->now_url) );
						}else if($rules_arr['type'] == 2){
							$next_link = string_page_link($content, trim($rules_arr['rules']), $this->now_url);
						}else{
							$next_link = evo_get_pagelink($content, $this->now_url);
						}
						$next_link = $this->page_link_replace($next_link, $this->p_arr['page_link_replace_rules']);
						if($this->p_arr['url_range_type'] == 1 && !$rules_arr['rules']) $msg = ' : '.milu_lang('no_set_list_rules');
						$link_count = $this->temp_arr['per_num'] = count($next_link); 

						if($this->p_arr['url_range_type'] == 1 && $this->p_arr['is_pick_cover_from_listpage'] == 1 && $this->p_arr['pick_cover_rules_get_rules']){//获取封面
							$cover_arr = rules_get_cover($this->now_url, $content, $this->p_arr['pick_cover_rules_get_type'], $this->p_arr['pick_cover_rules_get_rules'], $this->base_url);
							if($link_count != count($cover_arr)) {//封面数量不一致
								$this->get_pick_status(1);
								show_pick_info(milu_lang('cover_count_error'), 'err', $this->status_arr);
								$this->finsh();
							}
							foreach($next_link as $k => $v){
								$hash = md5($v);
								$this->pick_cache_data['cover_arr'][$hash] = $cover_arr[$k];
							}
						}
						if($link_count == 0 && $rules_arr['rules']) $msg = ' : '.milu_lang('check_list_rules');
						$this->get_pick_count(); 
					}else if($this->p_arr['rules_type'] == 3){//一键采集
						$content = $this->parse_page();
						$next_link = evo_get_pagelink($content, $this->now_url, $this->pick_cache_data['lilely_page']);
						$link_count = count($next_link);
					}	
					$this->get_pick_status(1);
					show_pick_info(milu_lang('get_link_c', array('c' => $link_count)).$msg, $link_count > 0 ? 'success' : 'err', $this->status_arr);
				
					if($next_link) $this->pick_cache_data['url_arr'][$this->pick_cache_data['now_level'] - 1] = $this->now_url_arr = $next_link;
				}else{
					$next_link = $this->now_url_arr = $this->pick_cache_data['url_arr'][$this->pick_cache_data['now_level']];
				}

				$this->del_session_arr($this->pick_cache_data['now_level']);
				$is_page_link = 0;
				if($this->pick_cache_data['now_level'] > 1 && $next_link) {
					$this->pick_cache_data['now_level'] -= 1;
					$is_page_link = 1;
				}
				if(!$this->flip()) return;
				if(!$this->pick_cache_data['url_arr']) {
					return;
				}
				$del_flag = 1;
				if($is_page_link == 1) {
					$this->robot($level - 1);
				}
			}else{
				$this->v_i++;
				$this->pick_cache_data['v_i'] = $this->v_i;
				$this->get_pick_status(1);
				show_pick_info(milu_lang('no_visit_err'.$visit_flag), 'err', $this->status_arr);
				$this->del_session_arr($this->pick_cache_data['now_level']);
				if(!$this->flip()) return;
			}
			if($del_flag != 1) $this->del_session_arr($this->pick_cache_data['now_level']);
		}
		$this->pick_cache_data['now_level'] += 1;
		$this->restart_robot($this->pick_cache_data['now_level']);
	}
	
	//递归删除
	function del_session_arr($level){
		if($level > $this->max_level) return;
		if(count($this->pick_cache_data['url_arr'][$level-1]) > 0) return;
		//如果下层没有，那就删除目前的层
		$arr = $this->pick_cache_data['url_arr'][$level];
		$del_key = array_search($this->now_url, (array)$arr);
		if($del_key !== FALSE){
			unset($arr[$del_key]);
			$this->pick_cache_data['url_arr'][$level] = $arr;
		}else{
			if($level > 1){
				$this->pick_cache_data['url_arr'][$level] = array_splice($arr, 1);
			}
		}
		return $this->del_session_arr($level+1);
	}
	
	//翻页进入采集回复、附件、用户信息。
	function run_get_other($content = '', $page_url = ''){
		$mod = $this->pick_cache_data['run_mod'];
		if(empty($mod)) return;
		if($this->pick_cache_data['jump_flag'] == 1) {
			$this->clear_url_cache();//发生跳转才会删除。
		}
		if($mod == 'content_page'){//文章分页
			$this->pick_cache_data['run_mod'] = 'content_page';
			$this->page_get_content($content);
			$this->create_page_article($this->pick_cache_data['content_page']['data_arr']);
			$this->pick_cache_data['run_mod'] = 'attach';
			$this->run_get_other();
		}
		
		if($mod == 'reply'){//回复
			$this->page_get_reply($content, $page_url);
			if($this->pick_cache_data['reply']['data_arr']) {
				$this->create_reply($this->pick_cache_data['reply']['data_arr']);//入库
			}else{
				//生成自动回复
				$this->add_rand_reply_data();
			}
			$this->pick_cache_data['run_mod'] = 'attach';
			$this->pick_cache_data['have_reply'] = 1;
			$this->run_get_other();
			
			
		}else if($mod == 'attach'){//附件
			//采集附件
			if($this->p_arr['is_pick_download_on'] == 1 || $this->p_arr['is_auto_public'] == 1){//如果是自动发布，强制在采集的时候就下载。否则根据设置
				if($this->p_arr['is_download_img'] == 1 || $this->p_arr['is_download_file'] == 1){
					$this->download_attach();
					DB::update("strayer_article_title", array('file_count' => $this->pick_cache_data['attach']['file_count'], 'pic' => $this->pick_cache_data['attach']['count'] - $this->pick_cache_data['attach']['file_count'], 'attach_filesize_count' => $this->pick_cache_data['attach']['attach_filesize_count']), array("aid" => $this->pick_cache_data['aid']));//更新
				}
			
			}
			$this->pick_cache_data['run_mod'] = 'user_info';
			$this->run_get_other();
			
		}else if($mod == 'user_info'){//用户信息
			$this->run_download_avatar();//采集会员数据
			$this->pick_cache_data['run_mod'] = 'public';
			$this->run_get_other();
		}
		//发布文章
		if($mod == 'public'){
			$this->article_public();
		}
		$this->pick_cache_data['run_mod'] = '';
		$this->clear_url_cache();//能执行到这来也清除
	}
	
	function clear_url_cache(){
		$del_key = array_search($this->now_url, $this->pick_cache_data['url_arr'][$this->pick_cache_data['now_level']]);
		if($del_key !== FALSE && count($this->pick_cache_data['url_arr'][$this->pick_cache_data['now_level']]) > 1) {
			if($this->pick_cache_data['url_arr'][$this->pick_cache_data['now_level']]) unset($this->pick_cache_data['url_arr'][$this->pick_cache_data['now_level']][$del_key]);
		}
		$del_key = array_search($this->now_url, $this->now_url_arr);
		if($del_key !== FALSE && count($this->now_url_arr) > 1) {
			if($this->now_url_arr) unset($this->now_url_arr[$del_key]);
		}
	}

	

	//获取原始的标题
	function get_ori_title($content){
		preg_match("/<title>(.*?)<\/title>/is", $content, $arr);
		return trim($arr[1]);
	}
	//检测cookie是否失效
	function check_login_cookie($content){
		if(($this->p_arr['cookie_test_hava'] && !strexists($content, $this->p_arr['cookie_test_hava'])) || ($this->p_arr['cookie_test_no_hava'] && strexists($content, $this->p_arr['cookie_test_no_hava']))) {
			return FALSE;
			show_pick_info(milu_lang('login_check_fail'), 'err', $this->status_arr);
		}
		return TRUE;
	}
	
	//检测内容是否符合要求
	function check_page_content($content){
		if(!empty($content) && $this->p_arr['is_fiter_page_link'] == 1 && !empty($this->p_arr['content_no_contain']) && !filter_something($content, $this->p_arr['content_no_contain'], TRUE)){
			return FALSE;
		}
		return TRUE;
	}
	
	
	//生成随机回复
	function create_rand_reply(){
		if($this->p_arr['is_auto_add_reply'] == 2) return -1;
		$rand_arr = explode(',', $this->p_arr['auto_add_reply_num']);
		if(count($rand_arr) == 2){
			$create_count = rand($rand_arr[0], $rand_arr[1]);
		}else{
			$create_count = $rand_arr[0];
		}
		if(!$this->rand_reply_data){
			$this->rand_reply_data = $reply_data_arr = pick_rand_reply_data();
		}else{
			$reply_data_arr = $this->rand_reply_data;
		}
		if(!$reply_data_arr) return -2;
		$rand_key_arr = array_rand($reply_data_arr, $create_count);
		$result_data = array();
		if($rand_key_arr && !is_array($rand_key_arr)) $rand_key_arr = array($rand_key_arr);
		foreach($rand_key_arr as $k => $v){
			$result_data[] = $reply_data_arr[$v];
		}

		return $result_data;
	}
	
	
	//网址替换
	function page_link_replace($link_arr, $rules, $type = 1){
		if(!$link_arr) return;
		$type_name = $type == 1 ? 'is_fiter_page_link' : 'is_fiter_content_page_link';//1是列表分页 2是文章分页
		if($this->p_arr[$type_name] != 1 || empty($rules)) return $link_arr;
		if(!is_array($link_arr)) return replace_something($link_arr, $rules);
		foreach($link_arr as $k => $v){
			$link_arr[$k] = replace_something($v, $rules);
		}
		return $link_arr;
	}
	
	function get_reply_max_num(){
		if($this->pick_cache_data['reply']['max_num']) return;
		if(strexists($this->p_arr['reply_max_num'], ',')) {
			$arr = explode(',', $this->p_arr['reply_max_num']);
			$this->pick_cache_data['reply']['max_num'] = rand($arr[0], $arr[1]);
		}else{
			$this->pick_cache_data['reply']['max_num'] = intval($this->p_arr['reply_max_num']);
		}
	}

	//判断一个地址是否是文章页
	function check_fastpick_viewurl($url){
		$lilely_page_arr = $this->pick_cache_data['lilely_page'];
		return check_fastpick_viewurl($url, $lilely_page_arr);
	}
	
	
	
	function get_postid_rules($content){
		if(!$this->pick_config['postid_pick_get_rules']) return;
		foreach($this->pick_config['postid_pick_get_rules'] as $k => $v){
			if(strexists($content, $v['check_str'])){
				return $v;
			}
		}
	}
	

	
	//获取分页回复
	function page_get_reply($content = ''){
		if($this->p_arr['is_get_reply'] != 1 || empty($this->p_arr['reply_rules'])) return;
		if($this->pick_cache_data['reply']['page'] > 0){//从翻页进入回复
			if(!$this->pick_cache_data['reply']['page_arr']) return;
		}else{//初次运行
			$this->get_reply_max_num();
			$this->pick_cache_data['sec_i'] = 1;
			$this->pick_cache_data['reply']['count'] = 0;
			$this->pick_cache_data['reply']['visit_arr'] = array($this->now_url);
			$this->get_reply($content, '');
		}
		if($this->pick_cache_data['reply']['count'] == $this->pick_cache_data['reply']['max_num'] || $this->pick_cache_data['reply']['count'] > $this->pick_cache_data['reply']['max_num']) return;
		
		foreach((array)$this->pick_cache_data['reply']['page_arr'] as $k => $v){
			if($v == '#' || !$v || $v == $this->now_url || in_array($v, $this->pick_cache_data['reply']['visit_arr'])) {
				$key = array_search($page_url, $this->pick_cache_data['reply']['page_arr']);
				if($key != FALSE) unset($this->pick_cache_data['reply']['page_arr'][$key]);
				continue;
			}	
			$page_url_arr = parse_url($v);
			parse_str($page_url_arr['query'], $url_info);
			if($url_info['page'] == 1) continue;//有些论坛有两个第一页
			$this->pick_cache_data['reply']['visit_arr'][] = $v;
			$get_num = $this->pick_cache_data['reply']['max_num'] - $this->pick_cache_data['reply']['count'];
			if($get_num  < 0 || $get_num == 0) return ;
			if($this->p_arr['content_page_get_mode'] == 1){//全部列出模式
				$this->get_reply('', $v);
			}else{
				$this->get_reply('', $v);
				$this->page_get_reply();
			}
		}
	}
	
	//最佳答案
	function get_best_answer($contents, $reply_arr){
		$this->pick_cache_data['reward']['best_key'] = -1;
		if($this->p_arr['is_setting_best_answer'] == 2) return $reply_arr;
		if($this->p_arr['best_answer_get_type'] != 3){//单独获取
			$best_answer = get_best_answer($this->p_arr['best_answer_get_type'], $this->p_arr['best_answer_get_rules'], $contents);
			if($best_answer){
				array_unshift($reply_arr, $best_answer);
				$this->pick_cache_data['reward']['best_key'] = 0;
			}else{//获取不到
				
			}
		}else{
			if(strexists($contents, $this->p_arr['best_answer_flag'])){
				$this->pick_cache_data['reward']['best_key'] = intval(trim($this->p_arr['best_answer_get_rules'])) - 1;//因为是从1算起的
			}
		}
		$this->pick_cache_data['reward']['price'] = get_reward_price($this->p_arr['ask_reward_price_get_type'], $this->p_arr['ask_reward_price_get_rules'], $contents); 
		return $reply_arr;
	}
	
	//获取回复
	function get_reply($content, $page_url = ''){
		$data_arr_count = $this->pick_cache_data['reply']['count'];
		$reply_num = $this->pick_cache_data['reply']['max_num'] - $data_arr_count;
		$this->pick_cache_data['reply']['page'] = $this->pick_cache_data['reply']['page'] ? ($this->pick_cache_data['reply']['page']+1) : 1;
		
		//网址替换
		$url_replace = 0;
		$page_url  = $page_url ? $page_url : $this->now_url;
		if($this->p_arr['is_fiter_content_page_link'] == 1 && !empty($this->p_arr['content_page_link_replace_rules'])){
			$page_url = $this->now_url = $this->page_link_replace($page_url, $this->p_arr['content_page_link_replace_rules'], 2);
			$url_replace = 1;			
		}
		
		$this->pick_cache_data['reply']['page_url'] = $page_url;
		d_s();
		$this->get_pick_status();
		$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1, 'now' => $this->i - 1, 'sec_now' => $this->pick_cache_data['sec_i']));
		show_pick_info(array( milu_lang('pick_page_reply', array('p' => $this->pick_cache_data['reply']['page'])) , $page_url), 'url', $show_args);
		$content = $content && $url_replace!=1 ? $content : get_contents($page_url, array('cookie' => $this->p_arr['login_cookie'], 'cache' => $this->cache_time));
		$content_hash = md5($content);
		if($this->pick_cache_data['reply']['page'] > 1 && in_array($content_hash, $this->pick_cache_data['content_hash'])){
			$this->get_pick_status(1);
			$this->status_arr = array_merge($this->msg_args, $this->status_arr);
			show_pick_info(milu_lang('reply_content_same'), 'err', $this->status_arr);
			return FALSE;
		}
		$this->pick_cache_data['content_hash'][$content_hash] = $content_hash;
		$this->pick_cache_data['reply']['page_arr'] =  $this->get_content_page($content);
		$post_user_arr = $this->get_member($content, 1, 0);//统一不删掉第一个了。因为根据回复来截取就不会出问题
		$type = $data_arr_count == 0 && ($this->p_arr['reply_rules'] == $this->p_arr['content_rules'] ) ? 'reply' : 'all';
		if($this->p_arr['reply_get_type'] == 1){
			$reply_arr = dom_get_manytext($content, $this->p_arr['reply_rules'], $reply_num);
			
		}else{
			$reply_rules = str_replace(array('[body]', '[reply]'), '[data]', $this->p_arr['reply_rules']);
			$reply_arr = str_get_str($content, $reply_rules, 'data', -1, 1);
			$reply_arr = count($reply_arr) > $reply_num ? array_slice($reply_arr, 0, $reply_num) : $reply_arr;
		}
		$postid_arr = (array)$this->get_postid($content);
		if($this->pick_cache_data['reply']['count'] == 0){//第一页，获取问答
			$reply_arr = (array)$this->get_best_answer($content, $reply_arr);
			//如果是第一页，而且规则是继承，那么删除第一个
			if($this->p_arr['reply_is_extend']) {
				unset($reply_arr[0], $postid_arr[0]);
			}
			
			if($this->p_arr['is_use_thread_setting']){
				//去掉第一个
				foreach($post_user_arr as $k => $v){
					if(!is_array($v)) $v = array();
					//如果继承的话，直接删掉第一个，如果不是，保持索引一致
					if($this->p_arr['reply_is_extend']){
						unset($v[0]);
					}else{
						array_shift($v);
					}
					$post_user_arr[$k] = $v;
				}
			}
		}
		if(count($reply_arr) == 0){
			$this->get_pick_status(1);
			$this->status_arr = array_merge($this->msg_args, $this->status_arr);
			show_pick_info(milu_lang('no_get_reply'), 'err', $this->status_arr);
			return FALSE;
		}
		
		//重新构造数组
		foreach($reply_arr as $k => $v){
			$key = md5($v).rand(1,100000);
			$this->pick_cache_data['reply']['data_arr'][$key] = $v;
			$this->pick_cache_data['reply']['postid_arr'][$key] = $postid_arr[$k];
			foreach($post_user_arr as $k1 => $v1){
				$this->pick_cache_data['post_user_arr'][$k1][$key] = $v1[$k];
			}
		}
		$this->pick_cache_data['reply']['count'] = count($this->pick_cache_data['reply']['data_arr']);
		$this->status_arr = array_merge($this->msg_args, $this->status_arr);
		show_pick_info(milu_lang('reply_finsh'), 'success', $this->status_arr);
		$this->pick_cache_data['sec_i']++;
		$key = array_search($page_url, $this->pick_cache_data['reply']['page_arr']);
		if($key != FALSE) unset($this->pick_cache_data['reply']['page_arr'][$key]);
		if(!$this->flip()) return;//翻页
	}
	
	//取得文章，并且格式化，并且判断是否合格
	function get_article_result($content, $url = ''){
		$url = $url ? $url : $this->now_url;
		//网址替换
		$url_replace = 0;
		if($this->p_arr['is_fiter_content_page_link'] == 1 && !empty($this->p_arr['content_page_link_replace_rules'])){
			$url = $this->now_url = $this->page_link_replace($url, $this->p_arr['content_page_link_replace_rules'], 2);
			$url_replace = 1;			
		}

		$content = $content && $url_replace!=1 ? $content : get_contents($url, array('cookie' => $this->p_arr['login_cookie'], 'cache' => $this->cache_time));
		$hash = md5($content);
		if (array_key_exists($hash, $this->pick_cache_data['content_page']['page_hash_arr'])) return -3;
		if(!$content) return -4;
		$this->pick_cache_data['content_page']['page_hash_arr'][] = $hash;
		$this->pick_cache_data['content_page']['page_arr'] =  $this->get_content_page($content);
		
		
		$this->pick_cache_data['content_page']['page'] = $this->pick_cache_data['content_page']['page'] ? ($this->pick_cache_data['content_page']['page']+1) : 1;
		
		$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1, 'now' => $this->i - 1, 'sec_now' => $this->pick_cache_data['sec_i']));
		show_pick_info(array(milu_lang('pick_page',array('page' => $this->pick_cache_data['content_page']['page'])), '<a href="'.$url.'" target="_blank">'.cutstr(trim($url), 75).'</a>'), 'left', $show_args);
		$this->pick_cache_data['sec_i']++;
		//检测第一页的网址,网址过滤
		if($info['page'] == 1 && $this->p_arr['is_fiter_content_page_link'] == 1){
			if(filter_something($url, $this->p_arr['content_page_url_contain']) || !filter_something($url, $this->p_arr['content_page_url_no_contain'], TRUE)) {
				$this->get_pick_status(1);
				$this->status_arr = array_merge($this->msg_args, $this->status_arr);
				show_pick_info(milu_lang('no_visit_err-1'), 'err', $this->status_arr);
				return FALSE;
			 }
		}
		
		
		if(!$this->check_page_content($content)){//检测内容
			$this->get_pick_status(1);
			$this->status_arr = array_merge($this->msg_args, $this->status_arr);
			show_pick_info(milu_lang('content_check_fail'), 'err', $this->status_arr);
			return -2;
		}
			
		$info = $this->get_article($content, $url);
		$info['page'] = $this->pick_cache_data['content_page']['page'];
		$info['url'] = $url;
		
		if(!$info['content']){
			if($this->p_arr['content_page_get_type'] == 3) {
				return -2;//如果是表达式分页，一个分页取不到，立即结束
			}else{
				$this->get_pick_status(1);
				$this->status_arr = array_merge($this->msg_args, $this->status_arr);
				show_pick_info(milu_lang('no_get_content'), 'err', $this->status_arr);
				return FALSE;
			}
		}				
		
		$info = $this->format_article($info);//格式化文章内容
		$check_re = $this->check_article($info, $this->pick_cache_data['content_page']['page']);
		if(!$check_re) return -2;
		$num = DB::result_first('SELECT COUNT(*) FROM '.DB::table('strayer_article_title')." WHERE pid='".$this->pid."' AND url_hash = '".md5($url)."'");
		if($num) {
			$this->get_pick_status(1);
			$this->status_arr = array_merge($this->msg_args, $this->status_arr);
			show_pick_info(milu_lang('article_exist'), 'err', $this->status_arr);
			return -2;
		}else{
			$this->status_arr = array_merge($this->msg_args, $this->status_arr);
			show_pick_info(milu_lang('finsh'), 'success', $this->status_arr);
		}
		
		if(!$info) return 1;//有些分页虽然没获取到内容，但是不代表整个采集就中断
		unset($info['ori_content'], $info['other']);
		$this->pick_cache_data['content_page']['data_arr'][$info['page']] = $info;
		return $info;
	}
	
	
	function page_get_content($content = ''){
		if($this->p_arr['is_setting_article_page'] != 1 || empty($this->p_arr['content_page_rules'])) return;
		if($this->pick_cache_data['content_page']['page'] > 0){//从翻页进入
			if(!$this->pick_cache_data['content_page']['page_arr']) return;
		}else{//初次运行
			$this->pick_cache_data['sec_i'] = 1;
			$this->pick_cache_data['content_page']['visit_arr'] = array($this->now_url);
			$re_info = $this->get_article_result($content, '');
			if(intval($re_info) == -2) {//一旦任何一个分页不合格，整篇文章不入库，全部不合格
				unset($this->pick_cache_data['content_page']);
				return FALSE;
			}
		}
		foreach((array)$this->pick_cache_data['content_page']['page_arr'] as $k => $v){
			if($v == '#' || !$v || $v == $this->now_url || in_array($v, $this->pick_cache_data['content_page']['visit_arr'])) continue;
			$url_parse_arr = parse_url(strtolower($v));
			parse_str($url_parse_arr['query'], $page_temp_arr);
			if($page_temp_arr['page'] == 1) continue;
			$this->pick_cache_data['content_page']['visit_arr'][] = $v;
			$re_info = $this->get_article_result('', $v);
			if(intval($re_info) == -2) {//一旦任何一个分页不合格，整篇文章不入库，全部不合格
				unset($this->pick_cache_data['content_page']);
				return FALSE;
			}
			if(!$this->flip()) return;//翻页
			return $this->page_get_content();
		}
	}

	//获取分页url列表
	function get_content_page($content, $page_url = ''){
		if(!$this->p_arr['content_page_rules'] || $this->p_arr['is_setting_article_page'] != 1) return;
		if($this->p_arr['content_page_get_type'] != 3){
			if(!$this->base_url) $this->base_url = get_base_url($content);
			if($page_url && !$this->base_url) $this->base_url = $page_url;
			if(!$this->base_url) $this->base_url = $this->now_url;
		}
		if($this->p_arr['content_page_get_type'] == 1){//dom
			$html = get_htmldom_obj($content);
			if(!$html) return false;
			foreach($html->find($this->p_arr['content_page_rules']) as $v) {
				$a_url = $this->format_url($v->attr['href']);
				if(!$a_url || $a_url == '#' || $v->innertext == milu_lang('up_page')) continue; 
				$item[] = _expandlinks($a_url, $this->base_url);
				$re_arr = sarray_unique($item);
				
			}
			$html->clear();
			unset($html);
		}else if($this->p_arr['content_page_get_type'] == 2){//字符串
			$re_arr = string_page_link($content, $this->p_arr['content_page_rules'], $this->base_url);//字符串
		}else if($this->p_arr['content_page_get_type'] == 3){//表达式
			$exp_data_arr = exp_get_pagelink($this->p_arr['content_page_rules'], $this->now_url);
			if(intval($this->pick_cache_data['content_page']['x_now']) == 0){
				$this->pick_cache_data['content_page']['x_now'] = $exp_data_arr['start'];
			}else{
				$this->pick_cache_data['content_page']['x_now'] += 1;
			}
			$re_arr[] = str_replace('[page]', $this->pick_cache_data['content_page']['x_now'], $exp_data_arr['url']);
		}
		
		//对网址进行过滤
		if($this->p_arr['is_fiter_content_page_link'] == 1){
			foreach((array)$re_arr as $k => $v){
				if(filter_something($v, $this->p_arr['content_page_url_contain']) || !filter_something($v, $this->p_arr['content_page_url_no_contain'], TRUE)) unset($re_arr[$k]);
			}
		}
		return $re_arr;
	}
	
	


	
	function restart_robot($level){
		if($level > $this->max_level) return;
		$this->get_now_url_arr($level);
		if($this->now_url_arr) $this->robot($level);
	}

	//获取当前的url数组
	function get_now_url_arr($level){
		if(!$level) $level = $this->pick_cache_data['now_level'];
		if(!$level || ( $level > $this->max_level ) ) return;
		$this->now_url_arr = $this->pick_cache_data['url_arr'][$level];//取得目前层的url数组
		if(!$this->now_url_arr) {//如果目前的层没有取到，再往上一层去取
			$this->now_url_arr = $this->get_now_url_arr($level + 1);
			return $this->now_url_arr;
		}else{//如果取到了
			$arr = array();
			$this->pick_cache_data['now_level'] = $level;
			return $this->now_url_arr;
		}
	}

	function format_url($url = array()){
		$this->now_url = convert_url($this->now_url);
		if($url){
			if(is_array($url)){
				foreach($url as $k => $v){
					$new_arr[$k] = convert_url($v);
				}
				return $new_arr;
			}else{
				return convert_url($url);
			}
		}
	}
	
	function get_article($content, $url = ''){
		global $_G;
		$url = $url ? $url : $this->now_url;
		$pick_config = $_G['cache']['evn_milu_pick']['pick_config'];
		require_once libfile('function/home');
		if($this->pick_cache_data['have_page'] != 1) $this->a++;
		$article_info = array();
		if($this->p_arr['rules_type'] == 3){//一键采集
			$article_info = get_single_article($content, $url);
		}else{
			//标题和内容都是dom，那就一起获取,每篇文章能节省0.2秒。
			if($this->p_arr['content_get_type'] == 1 && $this->p_arr['theme_get_type'] == 1){
				$article_info = dom_single_article($content, array('title' => $this->p_arr['theme_rules'], 'content' => $this->p_arr['content_rules']));
			}

			//先取标题
			if($this->p_arr['theme_get_type'] == 3){//智能识别
				$article_info = get_single_article($content, $url);
			}else if($this->p_arr['theme_get_type'] == 1){//dom获取
				$article_info = $this->p_arr['content_get_type'] == 1 && $this->p_arr['theme_get_type'] == 1 ? $article_info : dom_single_article($content, array('title' => $this->p_arr['theme_rules']));
			}else if($this->p_arr['theme_get_type'] == 2){//字符串
				$re = pregmessage($content, '<title>[title]</title>', 'title', -1);
				$article_info['other']['old_title'] = $re[0];
				$article_info['title'] = str_get_str($content, trim($this->p_arr['theme_rules']), 'title', 1);
			}
			//标题要做额外的工作，清理掉多余的html
			$article_info['title'] = format_html($article_info['title']);
			
			if(!trim($article_info['title'])) return $article_info;//如果标题都取不到，不必浪费时间获取内容
			
			
			//再取内容
			if($this->p_arr['content_get_type'] == 3){//智能识别
				if($this->p_arr['theme_get_type'] != 3){
					$info_arr = get_single_article($content, $url);
					$article_info['content'] = $info_arr['content'];
				}
			}else if($this->p_arr['content_get_type'] == 1){//dom获取
				if($this->p_arr['content_get_type'] == 1 && $this->p_arr['theme_get_type'] == 1){
					
				}else{
					$info_arr = dom_single_article($content, array('content' => $this->p_arr['content_rules']));
					$article_info['content'] = $info_arr['content'];
				}
			}else if($this->p_arr['content_get_type'] == 2){//字符串
				$article_info['content'] = str_get_str($content, $this->p_arr['content_rules'], 'body', 1);
			}
			if(!$article_info['content'] && $this->p_arr['is_setting_best_answer'] == 1 && $this->p_arr['best_answer_get_rules']) $article_info['content'] = $article_info['title'];
			
			//取其他内容
			$other_arr = $this->get_article_other($content);
			$other_arr = $other_arr ? $other_arr : array();
			$article_info = array_merge($article_info, $other_arr);
		}
		$article_info['ori_content'] = $content;//原始的内容
		//标签
		if($this->pick_set['open_tag'] == 1) $article_info['article_tag'] = dz_get_tag($article_info['title'], $article_info['content']);
		$content_hash = md5($content);
		$this->pick_cache_data['content_hash'][$content_hash] = $content_hash;//这个用来判断第一页回复和主贴页面是不是同一个页面
		$this->pick_cache_data['attach'] = array();
		
		//附件
		$article_info['content'] = attach_format($this->base_url, $article_info['content']); 

		//采集会员数据
		if(VIP){
			$this->pick_cache_data['post_user_arr'] = $this->get_member($content);
			//采集分类信息数据
			$this->get_typeoptionvar($content);
			//需要回复才能看到的内容
			$article_info['content'] = pick_reply_post($article_info['content'], array('cookie' => $this->p_arr['login_cookie'], 'page_url' => $url));
		}
		
		
		$article_info['url'] = $url;
		$article_info['content'] = getstr($article_info['content'], 0, 0, 0, 0, 1);
		//因为getstr函数会去掉hide标签，所以要恢复回来
		if(VIP) {
			$article_info['content'] = str_replace('dxchidecode', 'hide', $article_info['content']);
			if($this->pick_set['is_reply_hide_on'] == 1){
				preg_match_all('/<div class="showhide"><h4>.*<\/h4>(.*)<\/div><br \/>/i', $article_info['content'], $matchs, PREG_SET_ORDER);//把回复可见的内容设置成回复可见
				if($matchs[0]){
					$article_info['content'] = str_replace($matchs[0][0], '[hide]'.$matchs[0][1].'[/hide]', $article_info['content']);
				}
			}
		}
		return $article_info;
	}
	

		
	function check_data_title($title){
		return DB::result_first('SELECT COUNT(*) FROM '.DB::table('strayer_article_title')." WHERE title='".daddslashes($title)."'");
	}
	
	//对文章内容进行处理
	function format_article($article_info){
		
		//处理文章标题和内容，包括替换和过滤
		$format_args_title = array(
			'is_fiter' => $this->p_arr['is_fiter_title'],
			'show_type' => 'title',
			'test' => 2,
			'result_data' => $article_info['title'],
			'replace_rules' => $this->p_arr['title_replace_rules'],

			'filter_data' => $this->p_arr['title_filter_rules'],
		);
		$article_info['title'] = filter_article($format_args_title);
		
		$article_info['title'] = unhtmlentities(strip_tags($article_info['title'], '&nbsp;'));
		$article_info['title'] = getstr(trim($article_info['title']), $this->plugin_set['title_length'], 0, 0, 0, 1);
		$article_info['content'] = media_format($article_info['content'], $this->now_url);//
		
		$content_filter_html = (array)$this->p_arr['content_filter_html'];
		if($this->p_arr['is_download_file'] == 1){
			$a_key = array_search('0', $content_filter_html);
			if(!$a_key) unset($content_filter_html[$a_key]); //如果要下载附件，不要去除a标签
		}
		
		$article_info['content'] = trip_runma($article_info['content']);
		$format_args_content = array(
			'is_fiter' => $this->p_arr['is_fiter_content'],
			'show_type' => 'body',
			'test' => 2,
			'result_data' => $article_info['content'],
			'filter_html' => $content_filter_html,
			'replace_rules' => $this->p_arr['content_replace_rules'],
			'filter_data' => $this->p_arr['content_filter_rules'],
		);
		$article_info['content'] = filter_article($format_args_content);
		$format_arr = format_article_imgurl($this->now_url, $article_info['content'], $this->base_url);
		$article_info['content'] = $format_arr['message'];
		$article_info['content'] = clear_ad_html($article_info['content']);//去掉js、注释和框架等
		$article_info['pic'] = $format_arr['pic'];
		return $article_info;
	}
	//对回复进行处理
	function format_reply($reply_data){
		if(!$this->base_url) $this->base_url = $this->now_url;
		$reply_filter_html = $this->p_arr['reply_filter_html'];
		if($this->p_arr['is_download_file'] == 1){
			$a_key = array_search('0', $reply_filter_html);
			if(!$a_key) unset($reply_filter_html[$a_key]); //如果要下载附件，不要去除a标签
		}
		$reply_replace_rules = array();
		if(is_array($this->p_arr['reply_replace_rules'])){
			foreach($this->p_arr['reply_replace_rules'] as $k => $v){
				$reply_replace_rules += (array)format_wrap(trim($v));
			}
		}else{
			$reply_replace_rules = $this->p_arr['reply_replace_rules'];
		}
		$format_args = array(
			'is_fiter' => $this->p_arr['is_fiter_reply'],
			'show_type' => 'reply',
			'test' => 2,
			'result_data' => $reply_data,
			'replace_rules' => $reply_replace_rules,
			'filter_html' => $reply_filter_html,
			'filter_data' => $this->p_arr['reply_filter_rules'],
		);
		$reply_data = filter_article($format_args);
		
		//处理图片附件
		foreach($reply_data as $k => $v){
			//附件
			$v = attach_format($this->base_url, $v);
			$v = media_format($v, $this->base_url);//
			$format_arr = format_article_imgurl($this->base_url, $v);//处理图片路径
			$reply_data[$k] = $format_arr['message'];
		}
		
		return $reply_data;
	}
	
	//采集封面
	function download_cover($aid){
		if(!$this->pick_cache_data['cover_arr']) return;
		$hash = md5($this->now_url);
		$cover_url = $this->pick_cache_data['cover_arr'][$hash];
		if(!$cover_url) return;
		$this->get_pick_status();
		$now = '-'.($this->pick_cache_data['i'] - 1).time().rand(1,9999);
		$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1, 'now' => $now));
		show_pick_info(array(milu_lang('cover_download', array('url' => '')), $cover_url), 'url', $show_args);
		$snoop_obj = get_snoopy_obj($snoopy_args);
		$attach_info = get_img_content($cover_url, $snoop_obj, array('referer' => $this->now_url, 'is_set_referer' => $this->p_arr['is_set_referer']));
		$error = '';
		$attach_dir = PICK_ATTACH_PATH.'/'.$this->pid.'/'.$aid;
		dmkdir($attach_dir);
		if($attach_info['file_size'] && strlen($attach_info['content'])){
			$hash = md5($attach_info['content']);
			$url_hash = md5($cover_url);
			$save_name = $url_hash.'.'.$attach_info['file_ext'];
			file_put_contents($attach_dir.'/'.$save_name, $attach_info['content']);
			unset($this->pick_cache_data['cover_arr'][$hash]);
			$is_image = pis_image_ext($attach_info['file_ext']) ? 1 : 0;
			if($is_image != 1) {
				$error = milu_lang('download_fail').milu_lang('cover_download_error1');
			}
			$setarr = array('tid' =>$aid, 'url_hash' => $url_hash, 'hash' => $hash, 'pid' => $this->pid, 'save_name' => $save_name, 'file_name' => $attach_info['file_name'], 'filesize' => $attach_info['file_size'], 'description' => $value[2], 'isimage' => $is_image);
			$coverid = DB::insert('strayer_attach', paddslashes($setarr), TRUE);
			DB::update("strayer_article_title", array('cover_pic' => $cover_url), array("aid" => $aid));//更新
			$this->get_pick_status(1);
			$this->status_arr = array_merge($this->msg_args, $this->status_arr);
			$this->status_arr['now'] = $now;
			show_pick_info(milu_lang('finsh'), 'success', $this->status_arr);
		}else{
			$error = milu_lang('download_fail');
		}
		
		if($error){
			$this->pick_cache_data['sec_i']++;
			show_pick_info($error, 'err', $this->status_arr);
			return FALSE;
		}
		
	}
	
	//获取分类信息数据
	function get_typeoptionvar($content){
		global $_G;
		if($this->p_arr['is_get_threadtypes'] != 1) return;
		loadcache(array('threadsort_option_'.$this->p_arr['forum_threadtype_id']));
		$sortoptionarray = $_G['cache']['threadsort_option_'.$this->p_arr['forum_threadtype_id']];
		foreach($this->p_arr['forum_threadtypes']['get_type'] as $k => $v){
			$get_type = $v;
			$get_rules = $this->p_arr['forum_threadtypes']['get_rules'][$k];
			$value = '';
			if($get_rules){
				preg_match('/\{@(.*)\}/is', $get_rules, $get_rules_vars);
				$value = trim($get_rules_vars[1]);
				//日期
				if($value && strexists($value, 'now')){
					$date_value = 0;
					if(strexists($value, 'now+')){
						$date_value = str_replace('now+', '', $value);
					}else if(strexists($value, 'now-')){
						$date_value = str_replace('now-', '', $value);
					}
					$value = TIMESTAMP+$date_value;
					$value = date('Y-m-d', $value);
				}
			}
			if(empty($value)){
				if($get_type == 1){
					$html = get_htmldom_obj($content);
					$value = dom_get_str($html, $get_rules);
				}else{
					$value = str_get_str($content, $get_rules, 'data');
				}
			}
			
			$var_type = $sortoptionarray[$k]['type'];//获取数据类型
			$var_title = $sortoptionarray[$k]['title'];//获取数据名称
			if($var_type == 'image' || ($var_type == 'text' && (strexists($var_title, '手机') || strexists($var_title, '电话')))){//图片
			//if($var_type == 'image'){//图片
				preg_match_all("/\<img.+src=('|\"|)?(.*)(\\1)(.*)?\>/isU", $value, $image_arr, PREG_SET_ORDER);
				$value = $image_arr[0][2] ? $image_arr[0][2] : $value;
				$value = _expandlinks($value, $this->base_url);
				$this->pick_cache_data['forum_threadtypes']['attach_arr'][$k] = $value;
			}else if($var_type == 'calendar'){//日期类型
				
			}
			
			$this->pick_cache_data['forum_threadtypes']['value_arr'][$k] = $value;
		}

	}
	
	//分类信息入库
	function create_typeoptionvar($aid){
		if(count($this->pick_cache_data['forum_threadtypes']['value_arr']) == 0) return;
		foreach($this->pick_cache_data['forum_threadtypes']['value_arr'] as $k => $v){
			$setarr = array('optionid' => $k, 'value' => $v, 'aid' => $aid);
			DB::insert('strayer_typeoptionvar', paddslashes($setarr), TRUE);
		}
		if(count($this->pick_cache_data['forum_threadtypes']['attach_arr']) == 0) return;//下载附件
		$attach_dir = PICK_ATTACH_PATH.'/'.$this->pid.'/'.$aid;
		dmkdir($attach_dir);		
		$snoopy_args = array();
		$snoopy_args['cookie'] = $this->p_arr['login_cookie'];
		foreach($this->pick_cache_data['forum_threadtypes']['attach_arr'] as $k => $v){
			$image_url = $v;
			$snoop_obj = get_snoopy_obj($snoopy_args);
			$attach_info = get_img_content($image_url, $snoop_obj, array('referer' => $this->now_url, 'is_set_referer' => $this->p_arr['is_set_referer']));
			if(!$attach_info['file_size'] || strlen($attach_info['content']) == 0) continue;
			$hash = md5($attach_info['content']);
			$save_name = md5($imageurl).'.'.$attach_info['file_ext'];
			file_put_contents($attach_dir.'/'.$save_name, $attach_info['content']);
			$is_image = pis_image_ext($attach_info['file_ext']) ? 1 : 0;
			$setarr = array('tid' =>$aid, 'url_hash' => md5($image_url), 'hash' => $hash, 'pid' => $this->pid, 'save_name' => $save_name, 'file_name' => $attach_info['file_name'], 'filesize' => $attach_info['file_size'], 'description' => $value[2], 'isimage' => $is_image);
			$aid = DB::insert('strayer_attach', paddslashes($setarr), TRUE);
		}
		
	}
	
	// 下载文章附件
	function download_attach($content = '', $page_url = '', $aid = ''){
		if($this->p_arr['is_pick_download_on'] != 1) return FALSE;
		if(intval($this->pick_cache_data['sec_i']) < 2){//初次运行
			$hash_arr = array();
			$attach_link_hava_arr = $attach_link_text_hava_arr = array();
			if($this->p_arr['is_download_file'] == 1){
				$attach_link_hava_arr = format_wrap($this->p_arr['attach_link_hava']);
				$attach_link_text_hava_arr = format_wrap($this->p_arr['attach_link_text_hava']);
			}
			$this->pick_cache_data['attach']['file_count'] = $this->pick_cache_data['attach']['attach_filesize_count'] = 0;//文件数目和附件总大小
			foreach((array)$this->pick_cache_data['attach']['data_arr'] as $k => $v){
				$hash = md5($v[1]);
				$is_image = $v[4]==1 ? 0 : 1;
				if(in_array($hash, $hash_arr) || ($this->p_arr['is_download_img'] != 1 && $is_image == 1)) {//去重复
					unset($this->pick_cache_data['attach']['data_arr'][$k]);
					continue;
				}
				$hash_arr[] = $hash;
				
				//特殊附件处理				
				$this->pick_cache_data['attach']['data_arr'][$k][1] = $v[1] = $v[1] == $this->pick_cache_data['attach']['attach_redirect_url'] ? $this->pick_cache_data['attach']['attach_download_url'] : $v[1];
				
				//过滤掉一些附件
				if(!$this->pick_cache_data['attach']['data_arr'][$k][1] || ($v[4] == 1 && filter_something($v[1], $attach_link_hava_arr)) || ($v[4] == 1 && filter_something($v[2], $attach_link_text_hava_arr)) ) {
					unset($this->pick_cache_data['attach']['data_arr'][$k]);
					continue;
				}
			}
			
			$this->pick_cache_data['attach']['count'] = count($this->pick_cache_data['attach']['data_arr']);
		}
		$attach_arr = $this->pick_cache_data['attach']['data_arr'];
		if(!$attach_arr) return FALSE;
		
		$aid = $this->pick_cache_data['aid'];
		$snoopy_args = array();
		$snoopy_args['cookie'] = $this->p_arr['login_cookie'];
		$attach_dir = PICK_ATTACH_PATH.'/'.$this->pid.'/'.$aid;
		dmkdir($attach_dir);
		foreach($attach_arr as $key => $value) {
			$image_url = $value[1];//原本以为只有图片，后来加入了附件
			$image_title = $value[2];
			if(strlen($image_url)) {
				d_s();
				$this->get_pick_status();
				$now = '-'.($this->pick_cache_data['sec_i'] - 1).time().rand(1,9999);
				$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1, 'now' => $this->i - 1, 'sec_now' => $this->pick_cache_data['sec_i']));
				show_pick_info(array(milu_lang('download_attach', array('url' => '')), $image_url), 'url', $show_args);
				$snoop_obj = get_snoopy_obj($snoopy_args);
				$attach_info = get_img_content($image_url, $snoop_obj, array('referer' => $this->now_url, 'is_set_referer' => $this->p_arr['is_set_referer']));
				if(empty($attach_info['file_ext']) && $value[2]){//discuz有些种子附件获取不到扩展名
					$attach_info['file_ext'] = addslashes(strtolower(substr(strrchr(_striptext($value[2]), '.'), 1, 10)));
					if(!$attach_info['file_name'] && $attach_info['file_ext']) $attach_info['file_name'] = _striptext($value[2]);
				}
				
				$is_image = pis_image_ext($attach_info['file_ext']) ? 1 : 0;
				//判断是否是允许的扩展名
				$is_attach_allow = 1;//允许
				if($is_image != 1){
					if($this->p_arr['is_download_file'] == 2){//不允许附件本地化
						$is_attach_allow = 0;
					}else if($this->p_arr['is_download_file'] == 1){//允许附件本地化
						if($this->plugin_set['attach_download_allow_ext'] && !in_array($attach_info['file_ext'], $this->plugin_set['attach_download_allow_ext'])){//设置了允许的扩展名，但是不符合
						 	$is_attach_allow = 0;
						}
					}
				}
				
				//系统不允许下载html、shtml、php扩展名
				if(in_array($attach_info['file_ext'], array('shtml', 'html', 'php'))) $is_attach_allow = 0;
				
				if(is_array($attach_info) && $attach_info['file_size'] && strlen($attach_info['content']) && $is_attach_allow == 1 && $attach_info['file_ext']){
					$hash = md5($attach_info['content']);
					$url_hash = md5($image_url);
					if($this->pick_cache_data['attach']['attach_download_url'] == $image_url) {
						$url_hash = md5($this->pick_cache_data['attach']['attach_redirect_url']);
					}
					$save_name = $url_hash.'.'.$attach_info['file_ext'];
					file_put_contents($attach_dir.'/'.$save_name, $attach_info['content']);
					unset($this->pick_cache_data['attach']['data_arr'][$key]);
					
					$setarr = array('tid' =>$aid, 'url_hash' => $url_hash, 'hash' => $hash, 'pid' => $this->pid, 'save_name' => $save_name, 'file_name' => $attach_info['file_name'], 'filesize' => $attach_info['file_size'], 'description' => $value[2], 'isimage' => $is_image);
					DB::insert('strayer_attach', paddslashes($setarr), TRUE);
					$this->get_pick_status(1);
					$this->status_arr = array_merge($this->msg_args, $this->status_arr);
					$this->status_arr['now'] = $now;
					show_pick_info(milu_lang('_finsh', array('p' => percent_format($this->pick_cache_data['sec_i'], $this->pick_cache_data['attach']['count']))), 'success', $this->status_arr);
					$this->pick_cache_data['sec_i']++;
					$this->pick_cache_data['attach']['attach_filesize_count'] += $attach_info['file_size'];
					if($is_image != 1) $this->pick_cache_data['attach']['file_count']++;
					if(!$this->flip()) return;//翻页
				}else{
					$this->pick_cache_data['sec_i']++;
					unset($this->pick_cache_data['attach']['data_arr'][$key]);
					show_pick_info(milu_lang('download_fail'), 'err', $this->status_arr);
					continue;
				}
			}
		}
	}
	
	
	//获取会员数据
	function get_member($content, $is_reply = 0, $is_del_first = 0){
		if(!VIP || $this->p_arr['is_get_thread_user'] != 1) return array();
		if($is_reply && $this->p_arr['is_use_thread_setting'] != 1){//获取回帖用户数据
			$dateline_get_type = $this->p_arr['post_dateline_get_type'];
			$dateline_get_rules = $this->p_arr['post_dateline_get_rules'];
			$user_get_type = $this->p_arr['post_user_get_type'];
			$user_get_rules = $this->p_arr['post_user_get_rules'];
		}else{
			$dateline_get_type = $this->p_arr['thread_dateline_get_type'];
			$dateline_get_rules = $this->p_arr['thread_dateline_get_rules'];
			$user_get_type = $this->p_arr['thread_user_get_type'];
			$user_get_rules = $this->p_arr['thread_user_get_rules'];
		}
		$user_other_rules  = $this->p_arr['is_get_user_other'] == 1 ? $this->p_arr['user_other_rules'] : '';
		$post_user_data = get_public_user_data(array('dateline_get_type' => $dateline_get_type, 'dateline_get_rules' => $dateline_get_rules, 'user_get_type' => $user_get_type, 'user_get_rules' => $user_get_rules, 'is_use_thread_setting' => $is_use_thread_setting, 'is_reply_user' => $is_reply, 'content' => $content, 'user_other_rules' => $user_other_rules, 'postid_rules' => $postid_rules), $is_del_first);
		return $post_user_data;
	}
	
	//获取回复的postid
	function get_postid($content){
		if(!VIP) return array();
		$postid_rules = $this->get_postid_rules($content);
		$postid_arr = $postid_rules['get_type'] == 1 ? dom_get_str($html, $postid_rules['get_rules'], array('is_get_all' => $is_get_all, 'is_return_array' => 1)) : str_get_str($content, $postid_rules['get_rules'], 'data',  $limit, 1);
		$postid_arr = array_map('intval',(array)$postid_arr);
		return $postid_arr;
	}
	
	//会员入库
	function add_member(){
		if(!VIP) return;
		global $_G;
		$user_arr = $this->pick_cache_data['post_user_arr'];
		if(count($user_arr['username']) == 0) return;
		$get_web_url = $this->pick_cache_data['post_user_arr']['get_web_url'];
		if(!$get_web_url){
			$url_info = $this->GetHostInfo($this->now_url);
			$get_web_url = 'http://'.$url_info['host'].'/';
			$this->pick_cache_data['post_user_arr']['get_web_url'] = $get_web_url;
		}
		require_once libfile('function/editor');
		foreach($user_arr['username'] as $k => $v){
			$username_hash = md5($v);
			$username = trim($v);
			if(!$username) continue;
			$info = DB::fetch_first("SELECT uid FROM ".DB::table('strayer_member')." WHERE username='".daddslashes($username)."'");
			if($info['uid']) {
				$this->pick_cache_data['post_user_arr']['member_uid'][$username_hash] = $info['uid'];
				continue;
			}
			$set['username'] = $username;
			$set['get_uid'] = $user_arr['uid'][$k];
			$set['get_web_url'] = $get_web_url;
			$set['avatar_root_url'] = $user_arr['avatar_root_url'][$k];
			$set['sightml'] = pick_html2bbcode($user_arr['sign'][$k]);
			$set['get_dateline'] = $_G['timestamp'];
			$uid = DB::insert('strayer_member', paddslashes($set), TRUE);
			$this->pick_cache_data['post_user_arr']['data_uid'][$k] = $this->pick_cache_data['post_user_arr']['member_uid'][$username_hash] = $uid;
		}
	}
	
	function run_download_avatar(){
		if(!VIP) return;
		pload('F:member');
		$data_uid_count = count($this->pick_cache_data['post_user_arr']['data_uid']);
		if($data_uid_count == 0) return;
		if($this->pick_cache_data['post_user_arr']['data_count'] == 0){//初次运行
			$this->pick_cache_data['post_user_arr']['data_count'] = $data_uid_count;
			$this->pick_cache_data['sec_i'] = 1;
		}
		$get_web_url = $this->pick_cache_data['post_user_arr']['get_web_url'];
		foreach($this->pick_cache_data['post_user_arr']['data_uid'] as $k => $v){
			d_s();
			$this->get_pick_status();
			$now = '-'.($this->pick_cache_data['sec_i'] - 1).time().rand(1,9999);
			$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1, 'now' => $this->i - 1, 'sec_now' => $this->pick_cache_data['sec_i']));
			$get_url = $get_web_url.'home.php?mod=space&uid='.$this->pick_cache_data['post_user_arr']['uid'][$k].'&do=profile';
			show_pick_info(array(milu_lang('download_user_info', array('name' => $this->pick_cache_data['post_user_arr']['username'][$k], 'url' => $get_url))), 'left', $show_args);
	
			//下载头像。
			$this->download_avatar($this->pick_cache_data['post_user_arr']['avatar_url'][$k], $this->pick_cache_data['post_user_arr']['data_uid'][$k]);
			unset($this->pick_cache_data['post_user_arr']['data_uid'][$k]);
			
			$this->get_pick_status(1);
			$this->status_arr = array_merge($this->msg_args, $this->status_arr);
			$this->status_arr['now'] = $now;
			show_pick_info(milu_lang('_finsh', array('p' => percent_format($this->pick_cache_data['sec_i'], $this->pick_cache_data['post_user_arr']['data_count']))), 'success', $this->status_arr);
			$this->pick_cache_data['sec_i']++;
			if($this->pick_cache_data['run_mod']){//文章发布用户不需要跳转，因为只有一个
				if(!$this->flip()) return;//翻页
			}
		}
	}
	
	//下载头像	
	function download_avatar($avatar_url, $uid){
		$size_arr = array('middle', 'big', 'small');//顺序一定不可以变
		$attach_dir = PICK_PATH.''.'/'.get_avatar($uid, '', '', true);
		dmkdir($attach_dir);
		$snoopy_args = array();
		foreach($size_arr as $size){
			$avatar_file_name = PICK_PATH.'/'.get_avatar($uid, $size);
			if(file_exists($avatar_file_name)) continue;
			$icon_url = str_replace('middle', $size, $avatar_url);
			$snoopy_obj = get_snoopy_obj($snoopy_args);
			$img_arr = get_img_content($icon_url, $snoopy_obj);
			$img_re = $img_arr['content'];
			if(empty($img_re)) continue;
			$put_re = file_put_contents($avatar_file_name, $img_re);//写入头像
			if(!$put_re) {
				continue;
			}	
		}		
	}
	
	//写入头像临时缓存
	function download_avatar_temp($avatar_url, $get_uid){
		pload('F:member');
		$attach_dir = PICK_PATH.''.'/'.get_avatar($uid, '', '', true);
		dmkdir($attach_dir);
		$size_arr = array('middle', 'big', 'small');//顺序一定不可以变
		$snoopy_args = array();
		foreach($size_arr as $size){
			$avatar_file_name = PICK_PATH.'/'.get_avatar($uid, $size);
			if(file_exists($avatar_file_name)) continue;
			$icon_url = str_replace('middle', $size, $avatar_url);
			$snoopy_obj = get_snoopy_obj($snoopy_args);
			$img_arr = get_img_content($icon_url, $snoopy_obj);
			$img_re = $img_arr['content'];
			$put_re = file_put_contents($avatar_file_name, $img_re);//写入头像
			if(!$put_re) {
				continue;
			}	
		}		
	}
	
	//写入头像
	function create_avatar($get_uid, $uid){
		$temp_root_dir = PICK_PATH.'/temp_data/';
		$attach_dir = PICK_PATH.''.'/'.get_avatar($uid, '', '', true);
		dmkdir($attach_dir);
		$size_arr = array('middle', 'big', 'small');//顺序一定不可以变
		$snoopy_args = array();
		foreach($size_arr as $size){
			$temp_file_name = $temp_root_dir.'/'.$get_uid.$size.'.jpg';
			$avatar_file_name = PICK_PATH.'/'.get_avatar($uid, $size);
			@unlink($temp_file_name);
			copy($temp_file_name, $avatar_file_name);//写入头像
		}
	}

	function add_article_attach($aid, $cid){
		$attach_dir = PICK_ATTACH_PATH;
		$attach_dir .= '/'.$this->pid.'/'.$aid;
		dmkdir($attach_dir);
		$setarr = array();
		file_put_contents($attach_dir.'/'.$v['save_name'], $content);
		$num = DB::result_first('SELECT COUNT(*) FROM '.DB::table('strayer_attach')." WHERE pid='".$this->pid."' AND aid = '".$aid."' AND hash='".$k."'");
		if($num > 0) return; 
		DB::insert('strayer_attach', paddslashes($setarr), TRUE);
	}
	
	function create_reply($reply_arr){
		$end = $this->pick_cache_data['have_reply'] == 2 ? 0 : 1;
		$this->get_pick_status($end);
		$this->status_arr = array_merge($this->msg_args, $this->status_arr);
		$reply_arr = $this->format_reply($reply_arr);
		$publiced  = FALSE;
		$this->add_member();
	
		
		$r_n = 0;
		$reply_attach_arr = $setarr = array();
		$best_key = $this->pick_cache_data['reward']['best_key'];
		$best_answer_cid = 0;
		$last_dateline  = 0;
		foreach((array)$reply_arr as $k => $v){
			$r_n ++;
			$username = $this->pick_cache_data['post_user_arr']['username'][$k];
			if($username || $this->pick_cache_data['post_user_arr']['dateline'][$k]){
				$username_hash = md5($username);
				$uid = $this->pick_cache_data['post_user_arr']['member_uid'][$username_hash];
				$dateline = $this->pick_cache_data['post_user_arr']['dateline'][$k];
			}
			$setarr['aid'] = $this->pick_cache_data['aid'];
			$setarr['pageorder'] = $r_n + 1; 
			$setarr['content'] = $v; 
			$setarr['postid'] = intval($this->pick_cache_data['reply']['postid_arr'][$k]);
			$setarr['uid'] = $uid;
			$setarr['dateline'] = $last_dateline = $dateline ? $dateline : TIMESTAMP;
			$rid = DB::insert('strayer_article_content', paddslashes($setarr), TRUE);
			$best_answer_cid = ($best_key > -1 && $r_n == ($best_key + 1)) ? $rid : $best_answer_cid;
			$reply_attach_arr = array_merge($reply_attach_arr, (array)get_article_attach($v, $this->p_arr['is_download_file'], $this->now_url));
		}
		if(count($reply_arr) > $this->p_arr['auto_add_reply_min_num']){
			
		}else{
			$this->add_rand_reply_data();//生成自动回复
		}
		$special = ($this->p_arr['is_setting_best_answer'] == 1 && $this->p_arr['best_answer_get_rules']) ? 3 : 0;//3是悬赏主题
		$price = $this->pick_cache_data['reward']['price'];
		$price = $price ? $price : rand(1,50);
		$price = $best_key > -1 ? -$price : $price;//如果问题已解决，悬赏分为负。
		

		//将回复里面的附件加入到文章附件里面
		$this->pick_cache_data['attach']['data_arr'] = array_merge((array)$this->pick_cache_data['attach']['data_arr'], $reply_attach_arr);
		$this->pick_cache_data['sec_i'] = 1;
		$this->public_info = null;
		DB::update("strayer_article_title", array('reply_num' => $r_n, 'special' => $special, 'reward_price' => $price, 'best_answer_cid' => $best_answer_cid, 'pic' => $this->pick_cache_data['attach']['count']), array("aid" => $this->pick_cache_data['aid']));		
		
		unset($this->pick_cache_data['reply']);
		

	}

	function add_rand_reply_data(){
		if($this->p_arr['is_auto_add_reply'] == 2) return;
		$rand_create_reply = $this->create_rand_reply();
		if(!$rand_create_reply) return;
		$setarr = array();
		$r_n = 1;
		foreach((array)$rand_create_reply as $k => $v){
			$r_n++;
			$setarr['aid'] = $this->pick_cache_data['aid'];
			$setarr['pageorder'] = $r_n + 1; 
			$setarr['content'] = $v; 
			$setarr['postid'] = 0;
			$setarr['uid'] = 0;
			$setarr['dateline'] = $last_dateline ? $last_dateline : TIMESTAMP;
			$rid = DB::insert('strayer_article_content', paddslashes($setarr), TRUE);
		}
		if($r_n > 1) DB::update("strayer_article_title", array('reply_num' => $r_n - 1, 'is_bbs' => 1), array("aid" => $this->pick_cache_data['aid']));		
	}
	
	//判断一篇文章的内容是否符合要求
	function check_article($arr, $page = 0){
		global $_G;
		$evo_rules = $_G['cache']['evn_milu_pick']['evo_rules'];
		if(!$this->pick_cache_data['have_reply']) {
			$this->pick_cache_data['have_reply'] = 2;
			$this->get_pick_status(1);
		}
		$this->status_arr['now'] = $this->status_arr['now'] - 1;	
		$this->status_arr = array_merge($this->msg_args, $this->status_arr);
		
		if($arr['content'] == 'list'){
			show_pick_info(milu_lang('is_page_web'), 'err', $this->status_arr);
			return FALSE;
		}
		
		$arr['title'] =trim($arr['title']);
		
		if(!$arr['title']){
			show_pick_info(milu_lang('no_get_title'), 'err', $this->status_arr);
			return FALSE;
		}
		$title_len = strlen(_striptext(trim($arr['title'])));
		if($title_len < 1) {
			show_pick_info(milu_lang('title_too_short'), 'err', $this->status_arr);
			return FALSE;
		}
		
		if(strlen($arr['title']) < $this->min_title_len){
			show_pick_info(milu_lang('so_short_title'), 'err', $this->status_arr);
			return FALSE;
		}
		
		//检测标题重复
		$check_title = 0;
		if($this->p_arr['is_check_title'] == 1 && (($page && $page == 1) || $page == 0)){
			$check_title = $this->check_data_title($arr['title']);
		}
		if($check_title){
			show_pick_info(milu_lang('same_title_exist'), 'err', $this->status_arr);
			return FALSE;
		}
		
		if(array_key_exists('evo', $arr)){
			if($arr['evo'] != 2){
				if(!$arr['evo_title_info'] && !$arr['title']) {
					show_pick_info(milu_lang('no_article_view'), 'err', $this->status_arr);
					return FALSE;
				}
				if($arr['evo'] == 0){
					$link_count = own_link_count($arr['content'], $this->now_url);
					if($link_count > $this->min_own_link) {
						show_pick_info(milu_lang('is_list_page'), 'err', $this->status_arr);
						return FALSE;
					}
				}
			}
		}
		
		
		$arr['content'] = trim($arr['content']);
		 if(!$arr['content']){
			show_pick_info(milu_lang('no_get_content'), 'err', $this->status_arr);
			return FALSE;
		}
		
		$content_len = strlen($arr['content']);
		if($content_len < $this->p_arr['article_min_len']*2 && $this->p_arr['article_min_len']){
			show_pick_info(milu_lang('data_too_short'), 'err', $this->status_arr);
			return FALSE;
		}	
		if($content_len > 600000){
			show_pick_info(milu_lang('data_too_long'), 'err', $this->status_arr);
			return FALSE;
		}
		
		if($this->p_arr['keyword_flag'] == 1){//按关键词过滤
			if(filter_something($arr['title'], $this->p_arr['keyword_title'])) {//必须包含
				show_pick_info(milu_lang('title_must_keyword'), 'err', $this->status_arr);
				return FALSE;
			}
			if(!filter_something($arr['title'], $this->p_arr['keyword_title_exclude'], TRUE)) {//不包含
				show_pick_info(milu_lang('title_no_must_keyword'), 'err', $this->status_arr);
				return FALSE;
			}
			if(filter_something($arr['content'], $this->p_arr['keyword_content'])) {//必须包含
				show_pick_info(milu_lang('content_must_keyword'), 'err', $this->status_arr);
				return FALSE;
			}
			if(!filter_something($arr['content'], $this->p_arr['keyword_content_exclude'], TRUE)) {//不包含
				show_pick_info(milu_lang('content_no_must_keyword'), 'err', $this->status_arr);
				return FALSE;
			}
		}
		return TRUE;	
	}
	
	function create_article($arr){
		global $_G;
		$this->add_member();
		$this->status_arr = array_merge($this->msg_args, $this->status_arr);
		$this->status_arr['now'] = $this->temp_arr['normal_now'];
		//采集到的
		$username = $this->pick_cache_data['post_user_arr']['username'][0];
		if($username || $this->pick_cache_data['post_user_arr']['dateline'][0]){
			$username_hash = md5($username);
			$arr['article_dateline'] = $this->pick_cache_data['post_user_arr']['dateline'][0];
			$uid =  $this->pick_cache_data['post_user_arr']['member_uid'][$username_hash];
		}
		//标题表
		if($this->p_arr['reply_rules'] || $this->p_arr['reply_is_extend'] ) $setarr['is_bbs'] = 1;
		$url_hash = md5($this->now_url);
		$setarr['pid'] = $this->pid;
		$setarr['url'] = $this->now_url;
		$setarr['pic'] = $arr['pic'];
		$setarr['title'] = $arr['title'];
		$setarr['from'] = $arr['from'];
		$setarr['article_dateline'] = $arr['article_dateline'];
		$setarr['fromurl'] = $arr['fromurl'];
		$setarr['author'] = $arr['author'];
		$setarr['article_dateline'] = $arr['article_dateline'];
		$setarr['contents'] = 1;
		$setarr['uid'] = $uid;
		$setarr['sortid'] = $this->p_arr['forum_threadtype_id'];
		$setarr['summary'] = portalcp_get_summary($arr['content']);
		$setarr['summary'] = $setarr['summary'];
		$setarr['dateline'] = $_G['timestamp'];
		$setarr['url_hash'] = $url_hash;
		$setarr['article_tag'] = $arr['article_tag'];
		$setarr['special'] = ($this->p_arr['is_setting_best_answer'] == 1 && $this->p_arr['best_answer_get_rules']) ? 3 : 0;//3是悬赏主题
		unset($arr['other']);
		$update_aid = $this->pick_cache_data['repick']['update_aid'] = $this->pick_cache_data['repick'][$url_hash];
		if(!$update_aid){
			$num = DB::result_first('SELECT COUNT(*) FROM '.DB::table('strayer_article_title')." WHERE pid='".$setarr['pid']."' AND url_hash = '".$url_hash."'");
			if($num) {
				$this->v_a++;
				$this->pick_cache_data['v_a'] = $this->v_a;
				show_pick_info(milu_lang('article_exist'), 'err', $this->status_arr);
				return FALSE;
			}
			//如果设置自动发布，再检测文章是否已发布。
			
			if($this->check_article_public($setarr['title'])) {
				$this->v_a++;
				$this->pick_cache_data['v_a'] = $this->v_a;
				show_pick_info(milu_lang('article_publiced'), 'err', $this->status_arr);
				return FALSE;
			}
			
			$this->pick_cache_data['aid'] = DB::insert('strayer_article_title', paddslashes($setarr), TRUE);
		}else{//如果是更新的
			$this->pick_cache_data['aid'] = $update_aid;
			DB::update('strayer_article_title', paddslashes($setarr), array('aid' => $update_aid));
			//删掉其他东西
			DB::query('DELETE FROM '.DB::table('strayer_article_content')." WHERE aid='$update_aid'");
		}
		
		$setarr = array();
		//内容表
		$setarr['aid'] = $this->pick_cache_data['aid'];
		$setarr['content'] = $arr['content'];
		$setarr['pageorder'] = 1;
		$setarr['uid'] = $uid;
		$setarr['dateline'] = $arr['article_dateline'] ? $arr['article_dateline'] : $_G['timestamp'];
		$setarr = paddslashes($setarr);
		DB::insert('strayer_article_content', $setarr, TRUE);
		
		if($this->pick_cache_data['aid']) {
			$this->pick_cache_data['attach']['data_arr'] = array_merge((array)$this->pick_cache_data['attach']['data_arr'], (array)get_article_attach($arr['content'], $this->p_arr['is_download_file'], $this->now_url));
			$this->get_redirect_attach($this->now_url, $arr['ori_content']);
			$this->article_pick_misc($arr['ori_content']);
		}
		
	}
	//检测文章是否已经发布，已经发布返回TRUE
	function check_article_public($title){
		if(empty($title)) return FALSE;
		if($this->p_arr['is_auto_public'] != 1) return FALSE;
		$public_type = $this->p_arr['public_type'];
		if($public_type == 0) return FALSE;
		$title = daddslashes($title);
		if($public_type == 1){//门户
			$num = DB::result_first('SELECT COUNT(*) FROM '.DB::table('portal_article_title')." WHERE title='".$title."'");
		}else if($public_type == 2){//论坛
			$num = DB::result_first('SELECT COUNT(*) FROM '.DB::table('forum_thread')." WHERE subject='".$title."' AND displayorder > '-1'");
		}else if($public_type == 3){//博客
			$num = DB::result_first('SELECT COUNT(*) FROM '.DB::table('home_blog')." WHERE subject='".$title."'");
		}
		if($num) return TRUE;
		return FALSE;
	}
	
	//文章入库之后采集封面、附件、回复等
	function article_pick_misc($ori_content = '', $start_run_mod = 'reply'){
		//更新采集器的文章数目
		if(!$this->pick_cache_data['repick']['update_aid']){
			DB::query('UPDATE  '.DB::table('strayer_picker')." SET article_num=article_num+1 WHERE pid= '".$this->pid."'");
		}
		show_pick_info(milu_lang('add_data'), 'success', $this->status_arr);
		//采集封面
		$this->download_cover($this->pick_cache_data['aid']);
		
		
		//分类信息入库
		$this->create_typeoptionvar($this->pick_cache_data['aid']);
		
		
		$this->pick_cache_data['sec_i'] = 1;
		
		$this->pick_cache_data['jump_flag'] = 0;
		$this->pick_cache_data['reply'] = array();//初始化
		$this->pick_cache_data['run_mod'] = $start_run_mod;//从哪开始 分页文章从分页采集开始，否则从回复开始
		//采集杂项
		$this->run_get_other($ori_content, $this->now_url);
		
	}
	
	//有分页的文章要进行一些处理
	function page_article_format($article_arr){
		$pic = 0;
		foreach((array)$article_arr as $k => $v){
			if($k == 0){
				$arr['article_dateline'] = $v['article_dateline'];
				$arr['from'] = $v['from'];
				$arr['fromurl'] = $k;
				$arr['author'] = $v['author'];
			}
			$pic += $v['pic'];
			$new_arr[] = $v;
		}
		$arr = $new_arr[0];
		$arr['pic'] = $pic;
		$arr['contents'] = count($article_arr);
		$arr['content_arr'] = $new_arr;
		return $arr;
	}
	
	//有分页文章的入库
	function create_page_article($article_info_arr){	
		global $_G;
		if(!$article_info_arr) return;
		$arr = $this->page_article_format($article_info_arr);
		
		$this->get_pick_status();
		$this->status_arr['now'] =  '-'.($this->i - 1).time().rand(1,9999);
		$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1, 'now' => $now));
		show_pick_info(array(milu_lang('article').' ', cutstr(trim($arr['title']), 85)), 'left', $show_args);
		
		//标题表
		$setarr['pid'] = $this->pid;
		$setarr['url'] = $this->now_url;
		$setarr['pic'] = $arr['pic'];
		$setarr['title'] = daddslashes($arr['title']);
		$setarr['contents'] = $arr['contents'];
		$setarr['summary'] = portalcp_get_summary($arr['content']);
		$setarr['summary'] = daddslashes($setarr['summary']);
		$setarr['from'] = $arr['from'];
		$setarr['article_dateline'] = $arr['article_dateline'];
		$setarr['from'] = $arr['from'];
		$setarr['fromurl'] = $arr['fromurl'];
		$setarr['author'] = $arr['author'];
		$setarr['dateline'] = $_G['timestamp'];
		$setarr['url_hash'] = md5($setarr['url']);
		unset($arr['other']);
		$this->pick_cache_data['aid'] = DB::insert('strayer_article_title', paddslashes($setarr), TRUE);
		
		$setarr = array();
		//内容表
		foreach($arr['content_arr'] as $k => $v){
			$setarr['aid'] = $this->pick_cache_data['aid'];
			$setarr['title'] = $v['title'];
			$setarr['uid'] = $uid;
			$setarr['content'] = $v['content'];
			$this->pick_cache_data['attach']['data_arr'] = array_merge((array)$this->pick_cache_data['attach']['data_arr'], (array)get_article_attach($v['content'], $this->p_arr['is_download_file'], $this->now_url));//统计附件
			$this->get_redirect_attach($this->now_url, $v['content']);
			$setarr['pageorder'] = $v['page'];
			$setarr['dateline'] = $_G['timestamp'];
			$insert_id = DB::insert('strayer_article_content', paddslashes($setarr), TRUE);
		}
		if($this->pick_cache_data['aid']) {
			$this->article_pick_misc('', 'attach');
		}
		
	}
	
	function article_timing_update($data_id, $id){
		if(!$id || !$data_id) return;
		DB::update('strayer_timing', array('data_id' => intval($data_id)), array('id' => $id));
		unset($this->temp_arr['timing_id']);
	}
	
	//文章发布
	function article_public(){
		global $_G;
		$check_title = 1;
		if($this->pick_cache_data['repick']['update_aid']){//如果是更新
			$data = DB::fetch_first("SELECT forum_id,blog_id,portal_id FROM ".DB::table('strayer_article_title')." WHERE aid='".$this->pick_cache_data['repick']['update_aid']."'");
			if($data['forum_id'] || $data['blog_id'] || $data['portal_id']){//文章已经发布，要重新发布
				if(empty($this->p_arr['public_type']) || empty($this->p_arr['public_class'][0])){//如果没有设定发布栏目
					if($data['forum_id']){
						$this->p_arr['public_type'] = 1;//论坛
					}
					if($data['portal_id']){
						$this->p_arr['public_type'] = 2;//门户
					}
					if($data['blog_id']){
						$this->p_arr['public_type'] = 3;//博客
					}
				}

				$this->p_arr['is_auto_public'] = 1;
				$check_title = 0;
			} 
		}
		if($this->p_arr['is_auto_public'] != 1) return;
		pload('F:article,C:article');
		$optype_arr = array(1 => 'move_portal', 2 => 'move_forums', 3 => 'move_blog');
		pcache_del('article_bat_run_pick');
		$article_obj = new article(array('optype' => $optype_arr[$this->p_arr['public_type']], 'aid' => $this->pick_cache_data['aid'], 'forums' => $this->p_arr['public_class'][0], 'portal' => $this->p_arr['public_class'][0], 'blog' => $this->p_arr['public_class'][0], 'threadtypeid' => $this->p_arr['public_class'][1], 'check_title' => $check_title, 'run_type' => 'pick', 'pid' => $this->pid));
		if($this->pick_cache_data['repick']['update_aid']){
			$article_obj->cache['p_arr']['public_type'] = $this->p_arr['public_type'];
		}
		$this->get_pick_status(1);
		$now = '-'.($this->i - 1).time().rand(1,9999);
		$show_args = array_merge($this->msg_args, array('li_no_end' => 1, 'no_border' => 1, 'now' => $now));
		
		show_pick_info(array(milu_lang('article'), $article_obj->cache['article_info']['title']), 'left', $show_args);
		$this->msg_args['now'] = $now; 
		
		$article_obj->run_start();
		$errno = $article_obj->errno;
		if($errno < 0) {//发布失败
			$errno_array = array('-3' => 'article_publiced', '-1452' => 'article_public_error_no_sort', '-111' => milu_lang('article_public_limit', array('n' => 120)) );
			$error_msg_lang = $errno_array[$errno];
			$result_msg = $error_msg_lang ? milu_lang($error_msg_lang) : milu_lang('public_article_fail_').$errno;
			show_pick_info($result_msg, 'err', $this->msg_args);
			$this->v_a++;
			$this->pick_cache_data['v_a'] = $this->v_a;
			return FALSE;
		}else{
			show_pick_info(milu_lang('public_data'), 'success', $this->msg_args);
			return;
		}	
	}
	
	
	function get_article_other($contents){
		if($this->p_arr['is_get_other'] != 1) return array();
		$args = $this->p_arr;
		$dateline_info = format_wrap($this->p_arr['dateline_get_rules'], '@@');
		$args['dateline_get_rules'] = $dateline_info[0];
		$data = (array)get_other_info($contents, $args);
		$data['article_dateline'] = str_format_time($data['article_dateline'], $dateline_info[1]);
		return $data;
	}
	
	//判断一个网址是否值得访问 不可以访问则返回false
	function check_visit_url(){
		global $_G;
		$this->format_url();
		$evo_rules = $_G['cache']['evn_milu_pick']['evo_rules'];
		$no_url = $evo_rules['no_url'];
		if(!filter_something($this->now_url, $no_url, TRUE)) return FALSE;
		if($this->p_arr['page_fiter'] == 1 && ( $this->pick_cache_data['now_level'] < $this->max_level || $this->max_level == 1)){//开启了网址过滤器 入口地址不要过滤
			if($this->p_arr['page_url_no_other']){//要过滤的网址
				$user_no_arr = format_wrap(trim($this->p_arr['page_url_no_other']));
				$user_no_arr = $this->format_url($user_no_arr);
				if(in_array($this->now_url, $user_no_arr)) return -1;
			}
			
		}
		if($this->p_arr['is_fiter_page_link'] == 1  && ( $this->pick_cache_data['now_level'] < $this->max_level || $this->max_level == 1) ){
			if(filter_something($this->now_url, $this->p_arr['page_url_contain'])) return -2;//必须包含
			if(!filter_something($this->now_url, $this->p_arr['page_url_no_contain'], TRUE)) return -3;//不包含
		}
		
		if($this->p_arr['rules_type'] == 3){
			$this->p_arr['only_in_domain'] = $this->p_arr['only_in_domain'] ? $this->p_arr['only_in_domain'] : 1;
			if(($this->p_arr['only_in_domain'] == 0) && !strexists($this->now_url, $this->base_url)) return -4;//指定域名内
		}
		if(!$this->pick_cache_data['no_check_url']){
			$check = DB::result_first('SELECT COUNT(*) FROM '.DB::table('strayer_url')." WHERE pid='".$this->pid."' AND hash = '".md5(daddslashes($this->now_url))."'");
			if($check && $this->pick_cache_data['now_level'] == 1 ) return -5;//有些列表还是要重复访问的
		}
		return 1;
	
	}

	function insert_url($url = ''){
		if(!$url) $url = $this->now_url;
		$check = DB::result_first('SELECT COUNT(*) FROM '.DB::table('strayer_url')." WHERE pid='".$this->pid."' AND hash = '".md5(daddslashes($this->now_url))."'");
		if($check) return;
		$host_arr = $this->GetHostInfo($url);
		$arr = array('dateline' => TIMESTAMP, 'pid' => $this->pid, 'host' => $host_arr['host'], 'hash' => md5($url));
		DB::query('UPDATE  '.DB::table('strayer_picker')." SET visit_url_num=visit_url_num+1 WHERE pid= '".$this->pid."'");
		return DB::insert('strayer_url', $arr, TRUE);
	}
	
	function get_pick_count(){
		if($this->pick_cache_data['get_count']) return $this->pick_cache_data['get_count'];
		
		if($this->temp_arr['page_num'] && $this->temp_arr['per_num']){
			$get_count = $this->temp_arr['page_num'] * $this->temp_arr['per_num'] + $this->temp_arr['page_num'];
		}else{
			$get_count = 0;
		}
		if($this->p_arr['pick_num'] && $ths->p_arr['pick_num'] < $get_count){
			$get_count = $this->p_arr['pick_num'];
		}
		$this->pick_cache_data['get_count'] = $get_count;
		return $get_count;	
	}
	
	function GetHostInfo($gurl){
		$gurl = preg_replace("/^http:\/\//i", "", trim($gurl));
		$garr['host'] = preg_replace("/\/(.*)$/i", "", $gurl);
		$garr['query'] = "/".preg_replace("/^([^\/]*)\//i", "", $gurl);
		return $garr;
	}
	
	function get_pick_status($end = 0){
		$get_count = $this->get_pick_count();
		$this->status_arr = $this->status_arr ? $this->status_arr : $this->msg_args;
		if($end == 1) {
			$get_time = d_e(0, 'run');
			$this->all_get_time += $get_time;
			$this->pick_cache_data['all_get_time'] = $this->all_get_time;
		}
		if($get_count){
			$pro = ceil(100 * ($this->i/$get_count));
			if($pro == 101 || $pro > 101) return; 
		
			$avg_get_time = $this->all_get_time/$this->i;
			$wait_count = $get_count - $this->i;
			$wait_time = $avg_get_time * $wait_count;
		}else{
			$pro = $wait_time = $wait_count = milu_lang('un_know');
		}	
		
		
		if(function_exists('php_set')){	
			$memory_limit = php_set('memory_limit');
			$memory = $memory_limit > 0 ? 100 * (get_memory()/$memory_limit) : 0; 
			$memory = ($memory || $memory != 0) ? sprintf('%.0f%%',$memory) :  milu_lang('un_know');
		} 
		
		$wait_time = is_numeric($wait_time)  ? round($wait_time) : $wait_time;
		$this->status_arr = array('pro' => $pro, 'wait_time' => $wait_time, 'memory' => $memory, 'wait_count' => $wait_count, 'now' => $this->i, 'is_cron' => $this->msg_args['is_cron']);
		$this->status_arr = array_merge($this->status_arr, $this->msg_args);
	}
	
	//翻页
	function flip(){
		$get_count = $this->pick_cache_data['get_count'] + 1;
		$this->pick_cache_data['now_url'] = $this->now_url;
		$this->pick_cache_data['now_level'] = $this->pick_cache_data['now_level'];
		$this->pick_cache_data['visit_count'] = $this->visit_count;
		$this->pick_cache_data['a'] = $this->a;
		$this->pick_cache_data['i'] = $this->i;
		$this->pick_cache_data['visit_count'] = $this->visit_count;
		$this->pick_cache_data['v_a'] = $this->v_a;
		if(!$this->pick_cache_data['run_mod']){//如果正在进行一些模块，比如回复翻页这些不能停止。
			if($this->p_arr['pick_num'] > 1 && $this->visit_count >= $this->p_arr['pick_num']) {//达到指定的数目
				$this->finsh();
				return FALSE;
			}
			if(intval($this->pick_config['pick_config']['max_memory_per']) < intval($this->status_arr['memory'])) {
				show_pick_info(milu_lang('to_max_memory'), 'finsh', $this->status_arr);
				$this->finsh();
				exit();
			}
			//计划任务时，采集总数或者采集的文章数达到要求就停止任务
			if($this->msg_args['is_cron'] == 1 && (($this->a - $this->v_a) >= $this->p_arr['pick_article_num'] || ($this->i - 1) >= $this->p_arr['pick_num'])) {
				show_pick_info(milu_lang('cron_pick_finsh'), 'finsh', $this->status_arr);
				$this->finsh();
				return FALSE;
			}
		}
			
		$jump_num = $this->p_arr['jump_num'] ? $this->p_arr['jump_num'] : $pick_config['pick_num'];
		$j = intval($this->i + $this->pick_cache_data['sec_i']) - 1;
		if(is_int($j / $jump_num) && $j != 0 && $this->msg_args['is_cron'] !=1 ){
			if($this->p_arr['stop_time'][1]) sleep($this->p_arr['stop_time'][1]);
			if($this->pick_cache_data['run_mod']) $this->pick_cache_data['jump_flag'] = 1;//标记发生跳转
			cache_data('pick'.$this->pid.'_'.$this->msg_args['is_cron'], $this->pick_cache_data);
			data_go('picker_manage&pid='.$this->pid.'&myaction=get_article&submit=1&no_check_url='.$this->pick_cache_data['no_check_url']);
			exit();
		}
		return TRUE;
	}
	
	function finsh(){
		if(!$this->p_arr) return;//已经结束的标记
		$cache_name = 'pick'.$this->pid.'_'.$this->msg_args['is_cron'];
		if($this->msg_args['is_cron'] == 1 && $this->p_arr['auto_pick_from_last'] == 1){//如果是计划任务，而且从上次开始，缓存不删掉。否则删掉
			cache_data($cache_name, $this->pick_cache_data);
		}else{
			cache_del($cache_name);
		}
		
		$this->all_get_time = $this->pick_cache_data['all_get_time'];
		$all_get_time_str = diff_time($this->all_get_time, 1);
		$get_url_count = $this->i - 1; 
		$avg_get_time = $this->a > 0 ? $this->all_get_time/$this->a : 0;
		$avg_get_time_str = diff_time($avg_get_time, 1);
		$all_get_time_str = $all_get_time_str ? $all_get_time_str : sprintf('%.2f',$this->all_get_time).milu_lang('sec');
		$avg_get_time_str = $avg_get_time_str ? $avg_get_time_str : sprintf('%.2f',$avg_get_time).milu_lang('sec');
		if( $this->a == 0) $this->v_a = 0;
		$finsh_output = milu_lang('pick_finsh', array('guc' => $get_url_count, 'g_v' => $get_url_count - $this->v_i, 'v_i' => $this->v_i, 'a' => $this->a, 'a_va' => $this->a - $this->v_a, 'v_a' => $this->v_a, 'all' => $all_get_time_str, 'avg' => $avg_get_time_str));
		if(empty($this->pick_cache_data['repick']['return_url'])) $this->pick_cache_data['repick']['return_url'] = '?'.PICK_GO.'picker_manage&myac=article_manage&pid='.$this->pid.'&p=1';
		$return_output =  milu_lang('_hit_return_listpage', array('url' => $this->pick_cache_data['repick']['return_url'], 'p_url' => '?'.PICK_GO.'picker_manage&myaction=edit_pick&pid='.$this->pid));
		if($this->msg_args['is_cron'] == 1) $return_output = '';
		$this->get_pick_status(1);
		$this->status_arr = array_merge((array)$this->status_arr, (array)$this->msg_args);	
		$this->status_arr['pro'] = 100;	
		$this->status_arr['wait_time'] = $this->status_arr['wait_count'] = 0;
		show_pick_info($finsh_output.$return_output, 'finsh', $this->status_arr);
		$this->words = null;
		$this->snoopy = null;
		$this->p_arr = null;
		if($this->msg_args['is_cron'] !=1) exit();
	}
	
	function __destruct(){
		
	}
	
}
?>