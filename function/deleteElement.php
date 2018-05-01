<?php
	include("../config.php");
	include("../lib.php");

	if (isset($_GET["topicid"]) && isset($_GET["courseid"]) && isset($_GET["elementid"]))
	{
		if($_GET["topicid"]==null || $_GET["courseid"]==null || $_GET["elementid"]==null)
		{
			error("Variables not set in the URL", "../");
		}
		$topicid = $_GET["topicid"];
		$elementid = $_GET["elementid"];
		$courseid = $_GET["courseid"];
	}
	else {
		error("Variables not set in the URL", "../");
	}
	
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
	
	redirect("../course/view.php?id=".$courseid);
?>