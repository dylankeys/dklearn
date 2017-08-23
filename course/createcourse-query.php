<?php
		include ("../config.php");

		$title=$_POST["title"];
		$description=$_POST["description"];
		$start=$_POST["start"];
		$end=$_POST["end"];
		$usemedia=$_POST["usemedia"];
		$media=$_POST["media"];
		$activitycount=$_POST["activitycount"];
		$active=$_POST["active"];

		 //$password = md5($password);

		$dbQuery=$db->prepare("insert into courses values (null,:title,:description,:start,:end,:usemedia,:media,:activitycount,:active)");
		$dbParams=array('title'=>$title, 'description'=>$description, 'start'=>$start, 'end'=>$end, 'usemedia'=>$usemedia, 'media'=>$media, 'activitycount'=>$activitycount, 'active'=>$active);
		$dbQuery->execute($dbParams);

		header("Location: index.php?course=created");

	    //header("Location: registerKillSession.php");
?>
