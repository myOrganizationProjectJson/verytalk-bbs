<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) { 
    exit('Access Denied'); 
}
 
echo '<link rel="stylesheet" href="source/plugin/sanseqiu/css/admincp_sanseqiu.css" type="text/css"  />';


function caipiaoseed($size)
{	$str="";$i=0;
 	for($i=0;$i<$size;$i++){ 	$str.= rand(0,9).','; }
	return substr($str,0,strlen($str)-1);
}
loadcache('plugin');
$ssqPluginVars = $_G['cache']['plugin']['sanseqiu'];
$huobi=$ssqPluginVars['huobi'];
$prizeOne=$ssqPluginVars['prizeOne'];
$prizeTwo=$ssqPluginVars['prizeTwo'];
$prizeThree=$ssqPluginVars['prizeThree'];

$Plang= $scriptlang['sanseqiu'];


$qiShu=C::t('#sanseqiu#caipiao_pub')->getMaxQiShu();

 
//Ò¡ºÅformash
$cpUrlPre='admin.php?action=plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_pub&formhash='.FORMHASH;
 echo '<div>'.lang('plugin/sanseqiu', 'pubQiShu', array('qishu1' =>$qiShu,'qishu2' =>$qiShu+1) ).'</div></br/>';
 $areyouPub=lang('plugin/sanseqiu', 'areyouPub');
echo <<<EOT
	<script> function isPubFN(){
				if(window.confirm('$areyouPub')){return true;}else{return false;}	}
    </script>
 <div class="ad_pub"  ><p style="ad_pub_p">  $Plang[pubSuiJiTip];</p>
 <form  action="$cpUrlPre&act=pub"   method="post"> <input type="submit"  value="$Plang[pubSuiJi]"  onclick="return isPubFN()" class="pub_btn" /></form> </div>
EOT;


  
$act=empty($_POST['act'])?$_GET['act']:$_POST['act'];
 
if($act=='pub'){
//Ò¡ºÅformash
if ( $_GET['formhash'] != formhash()) {		showmessage('add error!');	}
$caiPiaoPubStr=caipiaoseed(3);


$totalMul=C::t('#sanseqiu#caipiao_buyitem')->getMultiSizeByQiShu($qiShu+1);
 

$pid=C::t('#sanseqiu#caipiao_pub')->inertCaiPiaoPub($caiPiaoPubStr,$qiShu+1,$totalMul);

 
C::t('#sanseqiu#caipiao_bp')->inertCaiPiaoBp($caiPiaoPubStr,$qiShu+1,$pid,$ssqPluginVars);

 
$arrList=C::t('#sanseqiu#caipiao_bp')->fetch_all_byissue($qiShu+1);

foreach ($arrList as $key => $value) {

	 switch( $value['prize']){
	 	case 1:
	   	C::t("common_member_count")->increase($value['uid'],array('extcredits'.$huobi=>$prizeOne*$value['multiple']));	
		  break;  
		case 2:
		 C::t("common_member_count")->increase($value['uid'],array('extcredits'.$huobi=>$prizeTwo*$value['multiple']));	
		  break;
		case 3:
		 C::t("common_member_count")->increase($value['uid'],array('extcredits'.$huobi=>$prizeThree*$value['multiple']));	
		  break;
		default:

	 }
}
$pubmsg=lang('plugin/sanseqiu', 'pubOK').$caiPiaoPubStr;
echo "<script type=\"text/JavaScript\"> alert('$pubmsg');  window.location.href='$cpUrlPre' ;</script>";
 

}

 


?>