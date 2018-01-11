<?php
		include ("../config.php");

		$courseID=$_POST["courseID"];
		$userID=$_POST["userID"];

		$dbQuery=$db->prepare("delete from enrolments where userid=:userid and courseid=:courseid");
		$dbParams=array('userid'=>$userID, 'courseid'=>$courseID);
		$dbQuery->execute($dbParams);

		session_start();

		header("Location: index.php?unenrol=1");
?>
