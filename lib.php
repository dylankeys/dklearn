<?php //Rocket Learn functions

//page access check

function has_capability($cap, $userID) {
	
	include("config.php");
	
	$dbQuery=$db->prepare("select roleid from role_assignments where userid=:userid");
	$dbParams=array('userid'=>$userID);
    $dbQuery->execute($dbParams);

	while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
	{
		$roleID = $dbRow["roleid"];
		
		$dbQuery2=$db->prepare("select * from role_capabilities where roleid=:roleid and capability=:cap");
		$dbParams2=array('roleid'=>$roleID,'cap'=>$cap);
		$dbQuery2->execute($dbParams2);
		$results=$dbQuery2->rowCount();
		
		if ($results > 0)
		{
			return true;
		}
	}
	return false;
}

?>