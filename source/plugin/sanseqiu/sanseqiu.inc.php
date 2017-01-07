<?php
if (! defined ('IN_DISCUZ')) {
	exit ('Access Denied');
}

function chartAction($arrList){
	//计算倍数的算法...
	//A[N][4] , A[N][0-2]存储的是百十个位的数字,A[N][3]存储的是倍数...
	$arrZhan=array();
	foreach($arrList as $arrPub){	
	$arrTemp=explode(",",$arrPub[buystr]);	$arrTemp[]= $arrPub[multiple]; 	array_push($arrZhan,  $arrTemp ); 
	}
	//二维数组,A[0-2][0-9],存储 百位十位个位 计数器
	$arrJiShu=array(array(0,0,0,0,0,0,0,0,0,0),array(0,0,0,0,0,0,0,0,0,0),array(0,0,0,0,0,0,0,0,0,0));
	foreach($arrZhan as $arrStr){	
		$arrJiShu[0][$arrStr[0]]+=$arrStr[3];		$arrJiShu[1][$arrStr[1]]+=$arrStr[3];		$arrJiShu[2][$arrStr[2]]+=$arrStr[3];
	}
	//字符串拼接,返回给图表数据.
    $chartArr[baiweidata]='['; $chartArr[shiweidata]='['; $chartArr[geweidata]='[';
 	for($i=0;$i<9;$i++){
		 $chartArr[baiweidata].=$arrJiShu[0][$i].',';
		 $chartArr[shiweidata].=$arrJiShu[1][$i].',';
		 $chartArr[geweidata].=$arrJiShu[2][$i].',';
	}
	$chartArr[baiweidata].=$arrJiShu[0][9].']';
	$chartArr[shiweidata].=$arrJiShu[1][9].']';
	$chartArr[geweidata].=$arrJiShu[2][9].']';
    return $chartArr;
}

if(!$_G['uid']){
	showmessage(lang('plugin/sanseqiu','nopermission'), '', array(), array('login' => true));
	exit;
}
loadcache('plugin');
$ssqPluginVars = $_G['cache']['plugin']['sanseqiu'];
$Plang= $scriptlang['sanseqiu'];

//三色球下注页面act=''
if($_GET['act']==''){

$huobi=(empty($ssqPluginVars['huobi']))?2:$ssqPluginVars['huobi'];
$jishu=(empty($ssqPluginVars['jishu']))?10:$ssqPluginVars['jishu'];
$huobiName=$_G['setting']['extcredits'][$huobi]['title'];
	//检查用户权限以及用户积分是否可以购买
	$memberCount=C::t('common_member_count')->fetch($_G[uid]);
	$userCount=$memberCount['extcredits'.$huobi];

	$qiShu=C::t('#sanseqiu#caipiao_pub')->getMaxQiShu()+1;
	$str=$_GET["caiPiaoStr"];
	$arrList=C::t('#sanseqiu#caipiao_buyitem')->fetch_all_byissue($qiShu);
	//执行图表设置.
	$chartArr=chartAction($arrList);
		  
	include template('sanseqiu:index');
}

if($_GET['act']=='add'){

if ( $_GET['formhash'] != formhash()) {		showmessage('add error!');	}
else{
//设置默认值，货币2 ，投注钱10
$huobi=(empty($ssqPluginVars['huobi']))?2:$ssqPluginVars['huobi'];
$jishu=(empty($ssqPluginVars['jishu']))?10:$ssqPluginVars['jishu'];
$huobiName=$_G['setting']['extcredits'][$huobi]['title'];
	//检查用户权限以及用户积分是否可以购买
	//$memberCount=C::t('common_member_count')->fetch($_POST["uid"]);
	$memberCount=C::t('common_member_count')->fetch($_G[uid]);
	$userCount=$memberCount['extcredits'.$huobi];

	$qiShu=C::t('#sanseqiu#caipiao_pub')->getMaxQiShu()+1;
	$str=$_GET["caiPiaoStr"];
	
	if(strlen($str)<=0){
		 
	}else{
	$arr = explode("|",substr($str,0,strlen($str)-1));
		//此处需要计算下注数金钱总额.
		//A2_2,1,1|A4_7,0,0|  str值
	$needCount=0;$totalZhuShu=0;
	foreach ($arr as $xiaZhuV){
	  	$totalZhuShu +=intval(substr($xiaZhuV,1, strpos($xiaZhuV,'_')-1));
	}
	 
	$needCount= $totalZhuShu*$jishu;
     	//用户积分不足已购买
	 if ($userCount-$needCount<0){
	 	 $varStr=lang('plugin/sanseqiu', 'notMoney');
	 	 showmessage($varStr,NULL,array('uname' => $_POST["uname"],'huobiname' => $huobiName,'duoshao1' => $userCount,'duoshao2' => $needCount),array('alert' => 'error'));
	  
	 }else{
		 	//取得最大期数  
		foreach($arr as $u){
		$pos=strrpos($u,"_");
		if($pos>0){
			C::t('#sanseqiu#caipiao_buyitem')->inertCaiPiao(substr($u,0,1),substr($u,$pos+1,5),substr($u,1,$pos-1),$qiShu,$_G[uid],$_POST["uname"])."|";
			}	 
		}
		//扣除用户的积分
		C::t("common_member_count")->increase($_G[uid],array('extcredits'.$huobi=>'-'.$needCount));	
		//刷新页面；
		 echo "<script language=JavaScript> location.replace(location.href);</script>";
		 exit();
	}
		
	 }
	 //插入数据后再次查询新数据
    $arrList=C::t('#sanseqiu#caipiao_buyitem')->fetch_all_byissue($qiShu);
	$chartArr=chartAction($arrList);
	include template('sanseqiu:index');
}

 
}

if($_GET['act']=='list'){
$maxQiShu=C::t('#sanseqiu#caipiao_pub')->getMaxQiShu(); 
if ( $_GET['formhash'] != formhash()) {		showmessage('list error!');	}
else{

	//如果没有取得期数参数，则取最新的一期数据。
	if(empty($_GET['qiShu'])){
		$qiShu=$maxQiShu; 
		if(empty($qiShu)){$qiShu=0;}
	}else{
		$qiShu=$_GET['qiShu'];
	}
$nowQiShu=C::t('#sanseqiu#caipiao_pub')->fetch_byissue($qiShu); 
$page=$_GET['page'];
if(empty($page)){ $page=1;}
 
$totalNum=C::t('#sanseqiu#caipiao_bp')->count_by_search(' AND issue='.$qiShu); 
$arrList=C::t('#sanseqiu#caipiao_bp')->fetch_all_byissue($qiShu,$page,10);
	include template('sanseqiu:list');
	 
 }
}


if($_GET['act']=='listpub'){
if ( $_GET['formhash'] != formhash()) {		showmessage('list error!');	}
else{

$page=$_GET['page'];
if(empty($page)){ $page=1;}
$qishu=$_GET['qiShu'];
if(!empty($qishu)){
$totalNum=C::t('#sanseqiu#caipiao_pub')->count_by_search(' AND issue='.$qishu); 
$arrList=C::t('#sanseqiu#caipiao_pub')->fetch_all_byissue($qishu,$page,10);
}else{
$totalNum=C::t('#sanseqiu#caipiao_pub')->count_by_search();
$arrList=C::t('#sanseqiu#caipiao_pub')->fetch_all($page,10);
}

include template('sanseqiu:listpub');
	 
 }
}

?>