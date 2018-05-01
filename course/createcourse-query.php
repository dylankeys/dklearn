<?php
	session_start();
	include ("../config.php");
	include ("../lib.php");

	$title=$_POST["courseName"];
	$useDates=$_POST["useCourseDates"];
	$description=$_POST["description"];
	$topicCount=$_POST["topicCount"];
	$active=$_POST["active"];
	
	if($useDates == "yes")
	{
		$startDate=$_POST["dayStart"]."-".$_POST["monthStart"]."-".$_POST["yearStart"];
		$endDate=$_POST["dayEnd"]."-".$_POST["monthEnd"]."-".$_POST["yearEnd"];
	}
	else
	{
		$startDate = null;
		$endDate = null;
	}

	$dbQuery=$db->prepare("insert into courses values (null,:title,:description,:start,:end,:topiccount,:active,1)");
	$dbParams=array('title'=>$title, 'description'=>$description, 'start'=>$startDate, 'end'=>$endDate, 'topiccount'=>$topicCount, 'active'=>$active);
	$dbQuery->execute($dbParams);

	redirect("index.php?course=created");
?>
