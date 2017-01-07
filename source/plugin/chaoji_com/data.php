<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}
// 
// include dirname(__FILE__) . '/include/function.php';
$formhash = isset($_GET['formhash']) ? trim($_GET['formhash']) : '';
if($formhash === formhash()){
	ob_end_clean();
	// header('Content-type:application/json');
	$act = isset($_GET['act']) ? trim($_GET['act']) : '';
	
	if($act == 'data1'){
		// 客户端信息
		$result1 = chaojiapp_soap('App_ClientInfo', array('accessToken' => getcookie('chaojiapp_access_token')));
		$result11 = $result1['App_ClientInfoResult'];
		echo $_GET['callback'] . '(' . $result11 . ')';
	}elseif($act == 'data2'){
		// 网站页面信息
		 $result14 = chaojiapp_soap('Seo_GetWebsiteSummary', array('actionType' => '14', 'accessToken' => getcookie('chaojiapp_access_token')));
		 $result141 = $result14['Seo_GetWebsiteSummaryResult'];
		 echo $_GET['callback'] . '(' . $result141 . ')';
	}elseif($act == 'data3'){
		// 网站关键词数据、关键词概述
		 $result12=chaojiapp_soap('Seo_GetWebsiteSummary', array('actionType' => '12', 'accessToken' => getcookie('chaojiapp_access_token')));
		 $result121 = $result12['Seo_GetWebsiteSummaryResult'];
		 echo $_GET['callback'] . '(' . $result121 . ')';
	}elseif($act == 'data4'){
		// 概要关键词24小时趋势
		 $result13=chaojiapp_soap('Seo_GetWebsiteSummary', array('actionType' => '13', 'accessToken' => getcookie('chaojiapp_access_token')));
		 $result131 = $result13['Seo_GetWebsiteSummaryResult'];
		 $jsondata = $cjson->decode($result131);
		 chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=data4&callback=?');
		 echo $result131;
	}elseif($act == 'today'){
		// 今日
		$result29 = chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => '29', 'beginDate'=> $_GET['begindate'], 'endDate' => $_GET['enddate'],'accessToken' => getcookie('chaojiapp_access_token')));
		$result291 = $result29['Seo_GetWebsiteDataReportResult'];
		$jsondata = $cjson->decode($result291);
		chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=today&callback=?');
		echo $_GET['callback'] . '(' . $result291 . ')';
	}elseif($act == 'site'){
		// 收录数据
		 $result21=chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => '21', 'beginDate' => $_GET['begindate'], 'endDate' => $_GET['enddate'], 'accessToken' => getcookie('chaojiapp_access_token')));
		 $result211 = $result21['Seo_GetWebsiteDataReportResult'];
		 $jsondata = $cjson->decode($result211);
		 chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=site&callback=?');
		 session_start();
		 $_SESSION['tempdata'] = $result211;
		 echo $_GET['callback'] . '(' . $result211 . ')';
	}elseif($act == 'link'){	
		// 反链数据
		$result22 = chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => '22', 'beginDate' => $_GET['begindate'], 'endDate' => $_GET['enddate'], 'accessToken' => getcookie('chaojiapp_access_token')));
		$result221 = $result22['Seo_GetWebsiteDataReportResult'];
		$jsondata = $cjson->decode($result221);
		chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=link&callback=?');
		session_start();
		$_SESSION['tempdata'] = $result221;
		echo $_GET['callback'] . '(' . $result221 . ')';
	}elseif($act == 'rank'){
		// PR及权重
		$result23 = chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => '23', 'beginDate' => $_GET['begindate'], 'endDate' => $_GET['enddate'], 'accessToken' => getcookie('chaojiapp_access_token')));
		$result231 = $result23['Seo_GetWebsiteDataReportResult'];
		$jsondata = $cjson->decode($result231);
		chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=rank&callback=?');
		session_start();
		$_SESSION['tempdata'] = $result231;
		echo $_GET['callback'] . '(' . $result231 . ')';
	}elseif($act == 'keyword'){
		// 关键词数据
		$result24 = chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => '24', 'beginDate' => $_GET['begindate'], 'endDate' => $_GET['enddate'], 'accessToken' => getcookie('chaojiapp_access_token')));
		$result241 = $result24['Seo_GetWebsiteDataReportResult'];
		$jsondata = $cjson->decode($result241);
		chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=keyword&callback=?');
		// var_dump($result241);
		session_start();
		$_SESSION['tempdata'] = $result241;
		echo $_GET['callback'] . '(' . $result241 . ')';		
	}elseif($act == 'trend'){
		// 关键词趋势图
		$result28 = chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => '28', 'beginDate' => $_GET['begindate'], 'endDate' => $_GET['keywordid'], 'accessToken' => getcookie('chaojiapp_access_token')));
		$result281 = $result28['Seo_GetWebsiteDataReportResult'];
		$jsondata = $cjson->decode($result281);
		chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=trend&callback=?');
		session_start();
		$_SESSION['tempdata'] = $result281;
		echo $_GET['callback'] . '(' . $result281 . ')';
		
	}elseif($act == 'baidupages'){
		// 网站收录24小时数据
		session_start();
		$submit_var['searchdate'] = $_GET['searchdate'];
		if(isset($_SESSION['baidupages_data_' . $submit_var['searchdate']])){
			$result271 = $_SESSION['baidupages_data_' . $submit_var['searchdate']];
		}else{
			
			$result27 = chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => 27, 'beginDate' => $_GET['searchdate'], 'endDate' => $_GET['searchdate'], 'accessToken' => getcookie('chaojiapp_access_token')));
			$result271 = $result27['Seo_GetWebsiteDataReportResult'];
			$jsondata = $cjson->decode($result271);
			chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=baidupages&callback=?');
			
			
			$_SESSION['baidupages_data_' . $submit_var['searchdate']] = $result271;
		}
		
		echo $_GET['callback'] . '(' . $result271 . ')';
	}elseif($act == 'alexadata'){
		// Alexa排名
		$result25 = chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => 25, 'beginDate' => $_GET['begindate'], 'endDate' => $_GET['enddate'], 'accessToken' => getcookie('chaojiapp_access_token')));
		$result251 = $result25['Seo_GetWebsiteDataReportResult'];
		$jsondata = $cjson->decode($result251);
		chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=alexadata&callback=?');
		session_start();
		$_SESSION['tempdata'] = $result251;
		echo $_GET['callback'] . '(' . $result251 . ')';
	}elseif($act == 'snapdate'){
		// 百度快照
		
		$result26 = chaojiapp_soap('Seo_GetWebsiteDataReport', array('actionType' => 26, 'beginDate' => $_GET['begindate'], 'endDate' => $_GET['enddate'], 'accessToken' => getcookie('chaojiapp_access_token')));
		$result261 = $result26['Seo_GetWebsiteDataReportResult'];
		$jsondata = $cjson->decode($result261);
		chaojiapp_api_error($jsondata['code'], 'setting&op=data&act=snapdate&callback=?');
		session_start();
		$_SESSION['tempdata'] = $result261;
		echo $_GET['callback'] . '(' . $result261 . ')';
	}elseif($act == 'ipchange'){
		// IP变更历史
		echo $_GET['callback'] . '({"data":[["2012-11-27","117.28.255.43","\u798f\u5efa\u7701\u798f\u5dde\u5e02 \u7535\u4fe1"],["2012-11-26","117.28.255.43","\u798f\u5efa\u7701\u53a6\u95e8\u5e02 \u7535\u4fe1"],["2012-11-26","117.125.34.131","\u5317\u4eac\u5e02 \u5317\u4eac\u4e07\u7f51\u5fd7\u6210\u79d1\u6280\u6709\u9650\u516c\u53f8"],["2012-10-27","117.28.255.43","\u798f\u5efa\u7701\u798f\u5dde\u5e02 \u7535\u4fe1"],["2012-06-06","125.90.88.81", "\u5e7f\u4e1c\u7701\u8302\u540d\u5e02 \u7535\u4fe1"]]})';
	}elseif($act == 'pageseo'){
		$host = isset($_GET['host']) ? trim($_GET['host']) : '';
		$websiteid = isset($_GET['websiteid']) ? intval($_GET['websiteid']) : 0;	
		
		include_once(dirname(__FILE__) . '/include/simple_html_dom.php');
		
		if(function_exists('curl_init')){
		// $time1 = microtime();
		// create a new cURL resource
			$ch = curl_init();

		  // set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "http://" . $host . "/");
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
			if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			}
		  // grab URL and pass it to the browser
			$output = curl_exec($ch);
			
		  // Check if any error occured - Output Response Headers
			if(!curl_errno($ch))
			 $arr2 = curl_getinfo($ch);
			
		  // close cURL resource, and free up system resources
			curl_close($ch);

			$results = $output;
		
				preg_match("/Content-Encoding: (.*)/", $output, $arr1);
				
				echo "jq('#compresstype_" . $websiteid . "').html('" . trim($arr1[1]) . "');";
				
				echo "jq('#accessspeed_" . $websiteid . "').html('" . ($arr2['total_time']) . " " . chaojiapp_lang('second') . "');";
				
				echo "jq('#pagesize_" . $websiteid . "').html('" . trim(sizecount($arr2['size_download'])) . "');";
				
				$html = str_get_html($results);
				// 全部链接
				$links = array();
				// 站外链接
				$outlinks = array();
				
				foreach($html->find('meta') as $meta){
					$str1 = 'http-equiv';
					if(strtolower($meta->{$str1}) === 'content-type'){
						$metacontent = $meta->content;
						$pagecode = trim(substr(strtolower($metacontent), strpos(strtolower($metacontent), 'charset=') + 8));
						echo "jq('#pagecode_" . $websiteid . "').html('" . $pagecode . "');";
					}
					if(strtolower($meta->name) == 'keywords'){
						echo "jq('#kwNum_" . $websiteid . "').html('" . chaojiapp_strLength(chaojiapp_code($meta->content), $pagecode) . "');";
						echo "jq('#kw_" . $websiteid . "').html('" . chaojiapp_code($meta->content) . "');";
					}
					if(strtolower($meta->name) == 'description'){
						echo "jq('#descNum_" . $websiteid . "').html('" . chaojiapp_strLength(chaojiapp_code($meta->content), $pagecode) . "');";
						echo "jq('#desc_" . $websiteid . "').html('" . chaojiapp_code($meta->content) . "');";
					}
				}
				
				foreach($html->find('title') as $title){
					echo "jq('#titleNum_" . $websiteid . "').html('" . chaojiapp_strLength(diconv($title->innertext, $pagecode, CHARSET), '') . "');";
				
					echo "jq('#title_" . $websiteid . "').html('" . chaojiapp_code($title->innertext) . "');";
				}
				
				foreach($html->find('a') as $a){
					if(in_array($a->href, $links) === FALSE){
						$arr1 = parse_url($a->href);
						if(isset($arr1['host'])){
							if($host == $arr1['host']){
								// 站内
							}else{
								// 站外
								$outlinks[] = $a->href;
							}
						}
						$links[] = $a->href;
					}
				}
				
				echo "jq('#linkcount_" . $websiteid . "').html('" . count($links) . "');";
				echo "jq('#linkout_" . $websiteid . "').html('" . count($outlinks) . "');";
				echo "jq('#linkinside_" . $websiteid . "').html('" . (count($links) - count($outlinks)) . "');";
		}	
	}elseif($act == 'getkeywords'){
		$host = isset($_GET['host']) ? trim($_GET['host']) : '';
		// var_dump($host);
		include_once(dirname(__FILE__) . '/include/simple_html_dom.php');
		
		if(function_exists('curl_init')){
		// $time1 = microtime();
		// create a new cURL resource
			$ch = curl_init();

		  // set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, "http://" . $host . "/");
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
			if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			}
		  // grab URL and pass it to the browser
			$output = curl_exec($ch);
			
		  // Check if any error occured - Output Response Headers
			if(!curl_errno($ch))
			 $arr2 = curl_getinfo($ch);
			
		  // close cURL resource, and free up system resources
			curl_close($ch);

			$results = $output;
				$html = str_get_html($results);
				$response = new stdClass;
				$response->success = true;
				foreach($html->find('meta') as $meta){
					
					if(strtolower($meta->name) == 'keywords'){
						$response->result = chaojiapp_code($meta->content);
					}
					
				}
			echo $cjson->encode($response);
		}
	}else{
		$array = array(7, 4, 2, 8, 4, 1, 9, 3, 2, 16, 7, 12);
		echo $_GET['callback'] . '(' . json_encode($array) . ')';	
	}
	define(FOOTERDISABLED, false);
	exit();
}else{

}
?>