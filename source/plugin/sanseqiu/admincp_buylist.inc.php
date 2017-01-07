<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) { 
    exit('Access Denied'); 
}
 
$Plang= $scriptlang['sanseqiu'];
$page = max(1, intval($_GET['page']));
$qishu=htmlspecialchars(empty($_POST['qishu'])?$_GET['qishu']:$_POST['qishu']);
 
if(!empty($qishu)&&(false==is_numeric( $qishu ))){
	cpheader();
	cpmsg($Plang[notNum], '', 'error');
}else{
 
echo '<link rel="stylesheet" href="source/plugin/sanseqiu/css/admincp_sanseqiu.css" type="text/css" media="all" />';

//删除操作。。。
if($_GET['op'] == 'delete') {

	if ( $_GET['formhash'] != formhash()) {		showmessage('error!');	}
	//删除buyitem表中
	C::t('#sanseqiu#caipiao_buyitem')->delete_by_bid($_GET['bid']);
	//删除数据发布号码表的总注数,有2中情况,一种是尚未发布本期,则不进行操作,另一种是已经发布,并且统计了,总记录数则需要减1.
	
	
	$feOne=C::t('#sanseqiu#caipiao_pub')->fetch_byissue($_GET['issue']);
	if(!empty($feOne)){
		C::t('#sanseqiu#caipiao_pub')->decrease_byissue($feOne['issue'],1);
	}
	ajaxshowheader();
	echo $Plang['deleted'];
	ajaxshowfooter();
	exit() ;
}

echo <<<EOF
 <div class="ad_pub" ><p style="ad_pub_p">$Plang[buylistTips]</p>
EOF;
showtableheader();
showformheader('plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_buylist','ok');
showsubmit('sbsousuo','submit' , $Plang['qishu'].': <input name="qishu" value="'.htmlspecialchars( $qishu).'" class="txt" />');
showformfooter();

echo '<tr class="header"><th>'.$Plang['bid'].'</th><th>'.$Plang['qishu'].'</th><th>'.$Plang['uname'].'</th><th>'.$Plang['buystr'].'</th><th>'.$Plang['buydate'].'</th><th>'.$Plang['multiple'].'</th><th>'.$Plang['ope'].'</th><th></th></tr>';
 
if(!empty($qishu)){
$totalNum=C::t('#sanseqiu#caipiao_buyitem')->count_by_search(' AND issue='.$qishu);
$arrList=C::t('#sanseqiu#caipiao_buyitem')->fetch_all_byissue($qishu,$page,10);
}else{
$totalNum=C::t('#sanseqiu#caipiao_buyitem')->count_by_search();
$arrList=C::t('#sanseqiu#caipiao_buyitem')->fetch_all($page,10);
}	 	

	foreach($arrList as $key=>$val){
		 echo '<tr>';
		 echo '<td>'.$val[bid].'</td>';
		 echo '<td>'.$val[issue].'</td>'; echo '<td>'.$val[uname].'</td><td>'.$val[buystr].'</td><td>'.dgmdate($val['date'],'Y-m-d H:i:s').'</td><td>'.$val[multiple].'</td>';
		
		 echo '<td><a id="p'.$key.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_buylist&bid='.$val['bid'].'&issue='.$val[issue].'&formhash='.FORMHASH.'&op=delete">['.$lang['delete'].']</a></td>';
		 echo '</tr/>';
	}

showtablefooter();
showtips($Plang[tipsList],'tips');
echo multi($totalNum,10,$page, ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_buylist&qishu='.$qishu);
 }
?>