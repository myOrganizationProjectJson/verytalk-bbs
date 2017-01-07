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

if($_GET['op'] == 'delete') {
	if ( $_GET['formhash'] != formhash()) {		showmessage('error!');	}
	C::t('#sanseqiu#caipiao_pub')->delete_by_id($_GET['pid']);
	ajaxshowheader();
	echo $Plang['deleted'];
	ajaxshowfooter();
	exit() ;
}

echo <<<EOF
 <div class="ad_pub" ><p style="ad_pub_p">$Plang[buylistTips]</p>
EOF;
showtableheader();
showformheader('plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_publist','ok');
showsubmit('sbsousuo','submit' , $Plang['qishu'].': <input name="qishu" value="'.htmlspecialchars( $qishu).'" class="txt" />');
showformfooter();

echo '<tr class="header"><th>'.$Plang['pid'].'</th><th>'.$Plang['qishu'].'</th><th>'.$Plang['pubstr'].'</th><th>'.$Plang['pubdate'].'</th><th>'.$Plang['pubMulti'].'</th><th>'.$Plang['ope'].'</th><th></th></tr>';
if(!empty($qishu)){
$totalNum=C::t('#sanseqiu#caipiao_pub')->count_by_search(' AND issue='.$qishu); 
$arrList=C::t('#sanseqiu#caipiao_pub')->fetch_all_byissue($qishu,$page,10);
}else{
$totalNum=C::t('#sanseqiu#caipiao_pub')->count_by_search();
$arrList=C::t('#sanseqiu#caipiao_pub')->fetch_all($page,10);
}	 	

	foreach($arrList as $key=>$val){
		 echo '<tr>';
		 echo '<td>'.$val[pid].'</td><td>'.$val[issue].'</td> <td>'.$val[pubstr].'</td> <td>'.dgmdate($val['date'],'Y-m-d H:i:s').'</td> <td>'.$val[total_multiple].'</td>';
		 echo '<td><a id="p'.$key.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_publist&pid='.$val['pid'].'&formhash='.FORMHASH.'&op=delete">['.$lang['delete'].']</a></td>';
		 echo '</tr/>';
	}
showtablefooter();
showtips($Plang[tipsList],'tips');
echo multi($totalNum,10,$page, ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_publist&qishu='.$qishu);
 }
?>