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
   
	C::t('#sanseqiu#caipiao_bp')->delete_by_id($_GET['id']);
	ajaxshowheader();
	echo $Plang['deleted'];
	ajaxshowfooter();
	exit() ;
}
 
echo <<<EOF
 <div class="ad_pub" ><p style="ad_pub_p">$Plang[buylistTips]</p>
EOF;
showtableheader();
showformheader('plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_bplist','ok');
showsubmit('sbsousuo','submit' , $Plang['qishu'].': <input name="qishu" value="'.htmlspecialchars( $qishu).'" class="txt" /><input name="formhash" value="'.FORMHASH.'"   type="hidden" />');
showformfooter();

echo '<tr class="header"><th>'.$Plang['bpid'].'</th><th>'.$Plang['qishu'].'</th><th>'.$Plang['userstr'].'</th><th>'.$Plang['uname'].'</th><th>'.$Plang['prize'].'</th><th>'.$Plang['multiple'].'</th><th>'.$Plang['prizemoney'].'</th><th>'.$Plang['ope'].'</th><th></th></tr>';
if(!empty($qishu)){
$totalNum=C::t('#sanseqiu#caipiao_bp')->count_by_search(' AND issue='.$qishu); 
$arrList=C::t('#sanseqiu#caipiao_bp')->fetch_all_byissue($qishu,$page,10);
}else{
$totalNum=C::t('#sanseqiu#caipiao_bp')->count_by_search();
$arrList=C::t('#sanseqiu#caipiao_bp')->fetch_all($page,10);
}	 	

	foreach($arrList as $key=>$val){
		 echo '<tr>';
		 echo '<td>'.$val[bpid].'</td><td>'.$val[issue].'</td> <td>'.$val[userstr].'</td> <td>'.$val[uname].'</td> <td>'.$val[prize].'</td><td>'.$val[multiple].'</td><td>'.$val[prizemoney].'</td>';
		 echo '<td><a id="p'.$key.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_bplist&id='.$val['bpid'].'&formhash='.FORMHASH.'&op=delete">['.$Plang['delete'].']</a></td>';
		 echo '</tr/>';
	}
showtablefooter();
showtips($Plang[tipsList],'tips');
echo multi($totalNum,10,$page, ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=sanseqiu&pmod=admincp_bplist&qishu='.$qishu);
 }
?>