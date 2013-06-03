<?php
	include('db.php');
	$url=$_GET['url'];

	$sql1="UPDATE `moneycontrol` SET `clicks`=`clicks` + 1 WHERE `url`='$url'";
	$result=mysql_query($sql1,$conn);
?>