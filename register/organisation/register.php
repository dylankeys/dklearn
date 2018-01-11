<?php
		include ("../../config.php");

		$username=$_POST["username"];
		$password=$_POST["password"];
		$orgname=$_POST["org"];
		$uniquename=$_POST["unique"];
		$colour=$_POST["colour"];

		//$password = md5($password);

		$dbQuery=$db->prepare("select * from organisations where uniquename=:unique");
 		$dbParams=array('unique'=>$uniquename);
 		$dbQuery->execute($dbParams);
		$rowcount=$dbQuery->rowCount();

		if ($rowcount > 0)
		{
			header("Location: ../organisation?error=1");
		}
		else
		{
			$dbQuery=$db->prepare("insert into organisations values (null,:name,:unique,:themecolour)");
	 		$dbParams=array('name'=>$orgname, 'unique'=>$uniquename, 'themecolour'=>$colour);
	 		$dbQuery->execute($dbParams);

			$dbQuery=$db->prepare("select * from organisations where uniquename=:unique");
			$dbParams=array('unique'=>$uniquename);
			$dbQuery->execute($dbParams);

			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
				$orgid = $dbRow["id"];
			}

			$dbQuery=$db->prepare("insert into users values (null,:user,:pass,1,:orgid)");
			$dbParams=array('user'=>$username, 'pass'=>$password, 'orgid'=>$orgid);
			$dbQuery->execute($dbParams);

			session_start();

			$_SESSION["message"]=1;

			header("Location: ../../login/");
		}
?>
