<?php
	session_start();
	include("../config.php");
	include("../lib.php");

	$courseID=$_POST["courseID"];
	$userID=$_POST["userID"];
	$role="student"; //student for test purposes

	$dbQuery=$db->prepare("insert into enrolments values (null,:courseID,:userID,:role)");
	$dbParams=array('courseID'=>$courseID, 'userID'=>$userID, 'role'=>$role);
	$dbQuery->execute($dbParams);

	session_start();

	redirect("view.php?id=". $courseID);
?>
