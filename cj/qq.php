<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<?php

function showpage()
{
    global $page,$pages,$prepage,$nextpage,$queryString; //param from genpage function
    $shownum =20/2;
    $startpage = ($page>=$shownum)?$page-$shownum:1;
    $endpage = ($page+$shownum<=$pages)?$page+$shownum:$pages;
   
    $s1="共".($pages)."页 "; 
    if($page>0)$s2="<a href=".$queryString."=1>&lt;&lt;</a>";
    if($startpage>1)
	  {   $s3=" ... <b><a href=".$queryString."=".($page-$shownum).">&laquo;</a></b>"; }
    for($i=$startpage;$i<=$endpage;$i++)
    {
        if($i==$page)  {  $s41="&nbsp;<b><u>".($i)."</u></b>&nbsp;";}
        else    {    $s41="&nbsp;<a href=".$queryString."=".$i.">".$i."</a>&nbsp;";}
		$s4.=$s41;
    }
    if($endpage<$pages)
		  { $s5="<b><a href=".$queryString."=".($page+$shownum*2).">&raquo;</a></b> ... "; }
    if($page<$pages)
			  { $s5="<a href=".$queryString."=".$pages.">&gt;&gt;</a>"; }

  return $s1.$s2.$s3.$s4.$s5;
}

$page=$_GET['page'] ? $_GET['page'] : 1;
$qq=$_GET['qq'] ? $_GET['qq'] : 947914;

$data=implode("",file("http://e.qzone.qq.com/cgi-bin/cgi_emotion_indexlist.cgi?uin=".$qq."&emotionarchive=1"));
preg_match("/<channel archive=\"(\d+)\" count/is",$data,$k1);

unset($data);
$pages = trim($k1[1]);
$prepage = ($page>1)?$page-1:1;
$nextpage = ($page<$pages)?$page+1:$pages; 
$queryString="qq.php?qq=".$qq."&page";
$npage=$pages-$page+1;

$data=implode("",file("http://e.qzone.qq.com/cgi-bin/cgi_emotion_indexlist.cgi?uin=".$qq."&emotionarchive=".$npage));
preg_match_all("/<title>(.*?)<\/title>/is",$data,$k1);
preg_match_all("/<pubDate>(.*?)<\/pubDate>/is",$data,$k2);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>QQ:<?=$qq?> QQ签名偷看器</title>
<link rel="stylesheet" type="text/css" id="css" href="./css.css" />
</head>
<body>
<table width=550px border=0 cellpadding=2 cellspacing=0 bordercolor=#ffffff bgcolor="#FFFFFF" class="list">
<tr><form action="qq.php" method="GET"><td>输入QQ号码 <INPUT TYPE="text" NAME="qq" value="<?=$qq?>"> <INPUT TYPE="submit" Value="偷看QQ签名"></td></form></tr>
<tr class="BlueBG"><td><?=showpage()?></td></tr>
<?php
for($i=0;$i<sizeof($k1[1]);$i++)
{
	$title=trim(str_replace("<![CDATA[","",str_Replace("]]>","",$k1[1][$i])));
	$time=trim($k2[1][$i]);
	echo '<tr><td>● '.$title."&nbsp;&nbsp;&nbsp;(".$time.")</td></tr>";
}
?>
<tr><td></td></tr>
<tr><td><a href="http://webpix.cn/qq.php">QQ签名偷看器</a></td></tr>
</table>
</body>
</html>