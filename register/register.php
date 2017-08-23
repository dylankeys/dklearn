<?php
		include ("../config.php");

		$username=$_POST["username"];
		$password=$_POST["password"];


		 //$password = md5($password);

		$dbQuery=$db->prepare("insert into users values (null,:user,:pass,0)");
		$dbParams=array('user'=>$username, 'pass'=>$password);
		$dbQuery->execute($dbParams);

		session_start();

		$_SESSION["message"]=1;

		header("Location: ../login/index.php");


	    //header("Location: registerKillSession.php");
		?>
