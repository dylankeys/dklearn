<?php
	include("config.php");
	include("lib.php");
	
	$dbQuery=$db->prepare("select id from courses");
	$dbQuery->execute();

	while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
	{
		$courseid=$dbRow["id"];
		
		$dbQueryUsers=$db->prepare("select id from users");
		$dbQueryUsers->execute();
		
		while ($dbRowUsers = $dbQueryUsers->fetch(PDO::FETCH_ASSOC))
		{
			$userid=$dbRowUsers["id"];
			
			$dbQueryExistingCompletions=$db->prepare("select * from course_completions where userid=:userid and courseid=:courseid");
			$dbParamsExistingCompletions=array('userid'=>$userid, 'courseid'=>$courseid);
			$dbQueryExistingCompletions->execute($dbParamsExistingCompletions);
			$completions = $dbQueryExistingCompletions->rowCount();
			
			if ($completions == 0)
			{
				$courseProgress = checkProgress($courseid, $userid);
				
				if ($courseProgress == 100)
				{
					$dbQueryCompletion=$db->prepare("insert into course_completions values(null,:courseid,:userid)");
					$dbParamsCompletion = array('courseid'=>$courseid, 'userid'=>$userid);
					$dbQueryCompletion->execute($dbParamsCompletion);
				}
			}
		}
	}
		
?>