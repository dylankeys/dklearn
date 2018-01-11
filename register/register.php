<?php
		include ("../config.php");
		
		$username=$_POST["username"];
		$fullName=$_POST["fullName"];
		$password=$_POST["pw"];
		$email=$_POST["email"];
		$bio=$_POST["bio"];
		$dob=$_POST["year"]."-".$_POST["month"]."-".$_POST["day"];
		$country=$_POST["country"];
		$lastlogin=time();
		
		$gravitarhash = md5( strtolower( trim( $email ) ) );
			
		$profileimage = "https://www.gravatar.com/avatar/" . $gravitarhash . "?s=400";

		 //$password = md5($password);

		$dbQuery=$db->prepare("insert into users values (null,:user,:fullname,:pass,:email,:dob,:country,:bio,:profileimage,:lastlogin)");
		$dbParams=array('user'=>$username, 'fullname'=>$fullName, 'pass'=>$password, 'email'=>$email, 'dob'=>$dob, 'country'=>$country, 'bio'=>$bio, 'profileimage'=>$profileimage, 'lastlogin'=>$lastlogin);
		$dbQuery->execute($dbParams);

		header("Location: ../login/index.php?registered=1");
?>
