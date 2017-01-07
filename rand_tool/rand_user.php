<?php 
 session_start();
 if(empty($_SESSION['uname']) || empty($_SESSION['uname'])){
    include("../errors/404_1.htm");
    return false;
 }
?>
<html>
   <form action="" method="post">
            生成个数<input type="text" name="menber" />
     <input type="submit" value="开始生成"/>
   </form>
</html>

<?php
 require '../config/config_global.php';
 require 'register.config.inc.php';
 $configs=$_config['db']['1']; 
 $dbhost=$configs['dbhost'];
 $dbuser=$configs['dbuser'];
 $dbpw=$configs['dbpw'];
 $dbname=$configs['dbname'];
     if(empty($_POST['menber'])){
        return;
     }else{
         $menber=$_POST['menber'];
     }
     $password='7fef6171469e80d32c0559f88b377245';//admin
	echo "==============================start==============================";
	$con = mysql_connect("$dbhost","$dbuser","$dbpw");
	if (!$con)
	  {
	     die('Could not connect: ' . mysql_error());
	  }
	if(!mysql_select_db("$dbname", $con)){
	   echo "连接失败";
	}
    	mysql_query('set names utf8');
    	$sql_id="select max(uid) from pre_common_member";
    	$result=mysql_query($sql_id);
    	$row = mysql_fetch_array($result);
    	$id=$row[0];
    	$time=time();
    	$y=0;$s=0;
    	$rando_name='abcdefghrjklmnopqrstuvwxyz1234567890';
    	$menber=$menber+1;
	for($i=0;$i<$menber;$i++){
    	$id=$id+1;
    	for($qqs=0;$qqs<9;$qqs++){
    	    @$qq.= mt_rand(0,9);
    	}
    	@$type= mt_rand(1,5);
		if($type!=2){
    		for($z=0;$z<3;$z++)
            	{
            	   @$key .=  $rando_name{@mt_rand(0,36)};    //生成php随机数
            	}
        	}else if($type==2){
        	for($z=0;$z<6;$z++)
            	{
            	   @$key .=  $rando_name{@mt_rand(0,36)};    //生成php随机数
            	}
    	}
    	$groupid=array('10','11','13');
    	@$groupid=$groupid[array_rand($groupid)];
	    for($c=0;$c<2;$c++){
            @$credits.= mt_rand(1,9);
        }
        for($nn=0;$nn<2;$nn++){
            @$nns.= mt_rand(0,9);
        }
    	@$frist_name=$rando_first_name[array_rand($rando_first_name)];
    	@$last_name=$rando_last_name[array_rand($rando_last_name)];
    	@$english_name=$e_name[array_rand($e_name)];
    	
    	$english='abcdefghrjklmnopqrstuvwxyz1234567890';
    	@$english=$english{@mt_rand(0,36)};
    	if($type==1){
    	$Uname=$english.$english_name.$nns;
    	}else if($type==2){
    	$Uname=$english_name.$english;
    	}else if($type==3){
    	$Uname=$last_name;
    	}else if($type==4){
    	$Uname=$english_name.$nns;
    	}else if($type==5){
    	$Uname=$english.$english_name.$english;
    	}
    	$english='';
    	$nns='';
    	$key='';
    	$qq=$qq."@qq.com";
    	$sql="INSERT INTO `pre_common_member` (`uid`, `email`, `username`, `password`, `status`, `emailstatus`, `avatarstatus`, `videophotostatus`, `adminid`, `groupid`, `groupexpiry`, `extgroupids`, `regdate`, `credits`, `notifysound`, `timeoffset`, `newpm`, `newprompt`, `accessmasks`, `allowadmincp`, `onlyacceptfriendpm`, `conisbind`, `freeze`) VALUES ('$id', '$qq', '$Uname', '$password', '0', '0', '0', '0', '0', '$groupid', '0', '', '$time', '$credits', '0', '9999', '0', '0', '0', '0', '0', '0', '0');
    	";
    	//echo $sql .'</br>';
    	echo '</br>'.$Uname;
    	$credits='';
    	$qq='';
    	$addresult=mysql_query($sql);
    	if(!$addresult){
    	 $ree='</br>'."录入失败".$y."条".'</br>';
    	    $y++;
    	}else{
    	 $res='</br>'."录入成功".$s."条".'</br>';
    	    $s++;
    	}
	}
	echo '</br>'."=====================录入信息=========================".'</br>';
	echo '</br>'."录入失败".$y."条".'</br>'.'</br>'."录入成功".$s."条".'</br>';
	echo '</br>'."=====================录入信息=========================".'</br>';
	$max_uid_sql='select MAX(uid) from pre_common_member';
	$max_uid=mysql_query($max_uid_sql);
	$max_uid=mysql_fetch_array($max_uid);
	$max_uid=$max_uid[0]+1;
	echo '</br>'.'会员max_id:'.$max_uid.'&nbsp;&nbsp;||&nbsp;||&nbsp;&nbsp;';
	$ucenter_sql="alter table pre_ucenter_members auto_increment=$max_uid";
	$max_uid=mysql_query($ucenter_sql);
	if($max_uid){
	    echo 'ucenter 修改成功！'.'</br>';
	}else{
	    echo 'ucenter 修改失败！'.'</br>';
	}

	mysql_close($con);
	echo "==============================end==============================";
?>

