<?php
	session_start();
	include("../../config.php");
	include("../../lib.php");
	
	$userID = $_GET["userid"];
	$txt = "";
	$completedcoursestxt = "txt/completedcourses_".$userID.".txt";
	
	$dbQuery=$db->prepare("select courses.id, courses.title, courses.description from course_completions inner join courses on course_completions.courseid = courses.id where course_completions.userid=:id");
	$dbParams=array('id'=>$userID);
	$dbQuery->execute($dbParams);
	$rows = $dbQuery->rowCount();
	
	if ($rows < 1)
	{
		error("You must complete a course before viewing your learning record", "../../");
	}
				
	while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
	{
		$title=$dbRow["title"];
		
		$txt .= $title . "\n";
	}
	// Write completed courses to file
	$completedcourses = fopen($completedcoursestxt, "w") or die("Unable to open file!");
	fwrite($completedcourses, $txt);
	fclose($completedcourses);
	
	redirect("index.php?userid=".$userID."&txt=".$completedcoursestxt);

?>