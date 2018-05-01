<?php
	session_start();
	include("../config.php");
	include("../lib.php");
	
	$courseID=$_GET["courseid"];
	$userID=$_GET["userid"];

	$dbQuery=$db->prepare("delete from enrolments where userid=:userid and courseid=:courseid");
	$dbParams=array('userid'=>$userID, 'courseid'=>$courseID);
	$dbQuery->execute($dbParams);

	redirect("index.php?unenrol=1");
?>
