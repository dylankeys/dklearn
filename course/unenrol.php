<?php
		include ("../config.php");

		$courseID=$_GET["courseid"];
		$userID=$_GET["userid"];

		$dbQuery=$db->prepare("delete from enrolments where userid=:userid and courseid=:courseid");
		$dbParams=array('userid'=>$userID, 'courseid'=>$courseID);
		$dbQuery->execute($dbParams);

		session_start();

		header("Location: index.php?unenrol=1");
?>
