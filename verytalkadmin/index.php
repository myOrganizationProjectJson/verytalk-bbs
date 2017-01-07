<?php
		function getIp() {
			if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) $ip = getenv("HTTP_CLIENT_IP");
			else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) $ip = getenv("HTTP_X_FORWARDED_FOR");
			else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) $ip = getenv("REMOTE_ADDR");
			else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) $ip = $_SERVER['REMOTE_ADDR'];
			else $ip = "unknown";
			return ($ip);
		}
    $verytalk=md5('verytalk');
	$time=(time()+10*60)*250;
	$ip=getIp();
	$ip=md5($ip);
	$times=$time+60*3;
	$times=md5($times);
	$key=$verytalk.$ip.$times;
    $key= md5($key);	
?>

<html>
<script>
	function test()
	{
		document.getElementById("adminid").submit();    
	}
</script>
<body onload="test()">
	<form action="../admin.php" method="post" id="adminid">
		<input type="hidden" value="<?=$verytalk?>" name="verytalka"/>
		<input type="hidden" value="<?=$time?>" name="verytalkb"/>
		<input type="hidden" value="<?=$ip?>" name="verytalkc"/>
		<input type="hidden" value="<?=$key?>" name="verytalkd"/>
		<input type="hidden" value="<?=$times?>" name="verytalke"/>
	</form>
</body>
</html>