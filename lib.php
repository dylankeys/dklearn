<?php //Rocket Learn functions

// User capability check

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

// Add element to a course topic

function addElement($courseid, $topicid, $type) {
	
	include("config.php");
	
	$dbQuery=$db->prepare("select id, tablename from elements_type where name=:type");
	$dbParams=array('type'=>$type);
	$dbQuery->execute($dbParams);
	$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
	
	$typeid = $dbRow["id"];
	$tablename = $dbRow["tablename"];
	
	$dbQuery=$db->prepare("select max(id) as maxID from ".$tablename);
	$dbQuery->execute();
	$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
	
	$contentid = $dbRow["maxID"];
	
	$dbQuery=$db->prepare("insert into elements values (null,:typeid,:contentid)");
	$dbParams=array('typeid'=>$typeid, 'contentid'=>$contentid);
	$dbQuery->execute($dbParams);
	
	addToTopic($courseid, $topicid);
}

// Supporting function for addElement, finialises adding of element to topic

function addToTopic($courseid, $topicid) {
	
	include("config.php");
	
	$dbQuery=$db->prepare("select max(id) as maxID from elements");
	$dbQuery->execute();
	$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
	
	$elementid = $dbRow["maxID"];
	
	$dbQuery=$db->prepare("insert into topic_content values (null,:topicid,:elementid)");
	$dbParams=array('elementid'=>$elementid, 'topicid'=>$topicid);
	$dbQuery->execute($dbParams);
}

function redirect($dir) {
	
	echo "<script>window.location.href = '".$dir."'</script>";
}

function deleteElement($topicid, $elementid) {
	
	include("config.php");
	
	$dbQuery=$db->prepare("delete from topic_content where elementid=:elementid and topicid=:topicid");
	$dbParams=array('elementid'=>$elementid, 'topicid'=>$topicid);
	$dbQuery->execute($dbParams);
	
	$dbQueryElementContent=$db->prepare("select * from elements where id=:elementid");
	$dbParamsElementContent=array('elementid'=>$elementid);
	$dbQueryElementContent->execute($dbParamsElementContent);
	
	while ($dbRowElementContent = $dbQueryElementContent->fetch(PDO::FETCH_ASSOC))
	{
		$typeid=$dbRowElementContent["typeid"];
		$contentid=$dbRowElementContent["contentid"];
		
		$dbQueryDeleteElement=$db->prepare("delete from elements where id=:elementid");
		$dbParamsDeleteElement=array('elementid'=>$elementid);
		$dbQueryDeleteElement->execute($dbParamsDeleteElement);
		
		$dbQueryElementType=$db->prepare("select tablename from elements_type where id=:typeid");
		$dbParamsElementType=array('typeid'=>$typeid);
		$dbQueryElementType->execute($dbParamsElementType);
	
		while ($dbRowElementType = $dbQueryElementType->fetch(PDO::FETCH_ASSOC))
		{
			$tablename=$dbRowElementType["tablename"];
			
			$dbQueryElementContentDelete=$db->prepare("delete from ".$tablename." where id=:contentid");
			$dbParamsElementContentDelete=array('contentid'=>$contentid);
			$dbQueryElementContentDelete->execute($dbParamsElementContentDelete);
			
		}
	}
	
}

function error($specific,$dirDepth) {

	if(isset($specific) && $specific!=null)
	{
		echo "<script>window.location.href = '".$dirDepth."error/index.php?specific=".$specific."'</script>";
	}
	else
	{
		echo "<script>window.location.href = '".$dirDepth."error/'</script>";
	}
}

function regrade($quizid) {
	
	include("config.php");
	
	$dbParams = array('quizid'=>$quizid);
	
	$dbQuery=$db->prepare("select pass from quiz where id=:quizid");
	$dbQuery->execute($dbParams);
	$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
		
	$pass=$dbRow["pass"];
	
	$dbQuery=$db->prepare("select * from quiz_attempts where quizid=:quizid");
	$dbQuery->execute($dbParams);
	
	while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
	{
		$attemptid=$dbRow["id"];
		$score=$dbRow["score"];
		
		if ($score > $pass)
		{
			$complete = 1;
		}
		else
		{
			$complete = 0;
		}
		
		$dbQueryUpdate=$db->prepare("update quiz_attempts set complete=:complete where id=:attemptid");
		$dbParamsUpdate = array('attemptid'=>$attemptid, 'complete'=>$complete);
		$dbQueryUpdate->execute($dbParamsUpdate);
	}
}

function isEnrolled($courseid, $userid) {
	
	include("config.php");
	
	$dbQuery=$db->prepare("select * from enrolments where userid=:userid and courseid=:courseid");
	$dbParams=array('userid'=>$userid, 'courseid'=>$courseid);
    $dbQuery->execute($dbParams);
	$enrolments=$dbQuery->rowCount();
		
	if ($enrolments > 0)
	{
		return true;
	}
	return false;
}

function checkProgress($courseid, $userid) {
	
	include("config.php");
	
	$completions = array();
	$elementsCompleted = 0;
	
	$dbQuery=$db->prepare("select elementid from course_completion_criteria where courseid=:courseid");
	$dbParams=array('courseid'=>$courseid);
    $dbQuery->execute($dbParams);
	
	while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
	{
		$elementid = $dbRow["elementid"];
		
		$dbQueryType=$db->prepare("select typeid, contentid from elements where id=:elementid");
		$dbParamsType=array('elementid'=>$elementid);
		$dbQueryType->execute($dbParamsType);
		$dbRowType=$dbQueryType->fetch(PDO::FETCH_ASSOC);
		
		$typeid=$dbRowType["typeid"];
		$contentid=$dbRowType["contentid"];
		
		$dbQueryTables=$db->prepare("select name, tablename, completiontable from elements_type where id=:typeid");
		$dbParamsTables=array('typeid'=>$typeid);
		$dbQueryTables->execute($dbParamsTables);
		$dbRowTables=$dbQueryTables->fetch(PDO::FETCH_ASSOC);
		
		$tablename=$dbRowTables["tablename"];
		$completiontable=$dbRowTables["completiontable"];
		$activityType=$dbRowTables["name"];
		
		$dbQueryPass=$db->prepare("select pass from ".$tablename." where id=:contentid");
		$dbParamsPass=array('contentid'=>$contentid);
		$dbQueryPass->execute($dbParamsPass);
		$dbRowPass=$dbQueryPass->fetch(PDO::FETCH_ASSOC);
		
		$pass=$dbRowPass["pass"];

		if ($activityType == "quiz")
		{
			$dbQueryGrade=$db->prepare("select grade from ".$completiontable." where userid=:userid and quizid=:contentid");
		}
		else if ($activityType == "assignment")
		{
			$dbQueryGrade=$db->prepare("select grade from ".$completiontable." where userid=:userid and assignmentid=:contentid");
		}
		
		$dbParamsGrade=array('userid'=>$userid, 'contentid'=>$contentid);
		$dbQueryGrade->execute($dbParamsGrade);
		$dbRowGrade=$dbQueryGrade->fetch(PDO::FETCH_ASSOC);
		
		$grade=$dbRowGrade["grade"];
		
		if ($grade >= $pass)
		{
			array_push($completions,"1");
		}
		else {
			array_push($completions,"0");
		}
	}
	
	foreach ($completions as $completed) {
		if ($completed == "1") {
			$elementsCompleted++;
		}
	}
	
	$totalElements = count($completions);
	
	return ($elementsCompleted / $totalElements) * 100;
}

?>