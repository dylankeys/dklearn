<?php
		include ("../config.php");

		$courseID=$_POST["courseID"];
		$userID=$_POST["userID"];
		$role="student"; //student for test purposes


		 //$password = md5($password);

		$dbQuery=$db->prepare("insert into enrolments values (null,:courseID,:userID,:role)");
		$dbParams=array('courseID'=>$courseID, 'userID'=>$userID, 'role'=>$role);
		$dbQuery->execute($dbParams);

		session_start();

		header("Location: view.php?id=". $courseID);

	    //header("Location: registerKillSession.php");
		?>
