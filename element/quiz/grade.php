<?php
	include("../../config.php");
	include("../../lib.php");
	
	$userid = $_POST["userid"];
	$quizid = $_POST["quizid"];
	$toPass = $_POST["pass"];
	
	$correctAnswerCount = 0;
	$questionCount = 0;
	$complete = 0;
	
	$dbQuery=$db->prepare("select id from quiz_questions where quizid=:quizid");
	$dbParams = array('quizid'=>$quizid);
	$dbQuery->execute($dbParams);
			
	while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
	{
		$questionid=$dbRow["id"];
		$userAnswer = $_POST[$questionid];
		$questionCount++;
		
		echo "User answer for question id " . $questionid . ": " . $userAnswer . "<br>";
		
		$dbQueryAnswer=$db->prepare("select answer from quiz_answers where questionid=:questionid and correctanswer='1'");
		$dbParamsAnswer = array('questionid'=>$questionid);
		$dbQueryAnswer->execute($dbParamsAnswer);
		$dbRowAnswer=$dbQueryAnswer->fetch(PDO::FETCH_ASSOC);
		
		$correctAnswer=$dbRowAnswer["answer"];
			
		if ($userAnswer == $correctAnswer)
		{
			$correctAnswerCount++;
		}
	}
	
	$percentageCorrect = ($correctAnswerCount / $questionCount) * 100;
	
	if ($percentageCorrect > $toPass)
	{
		$complete = 1;
	}
	
	$percentageCorrect = round($percentageCorrect,2);
	$submitted = time();
	
	$dbQuery=$db->prepare("insert into quiz_attempts values (null,:quizid,:userid,:submitted,:score,:complete)");
	$dbParams=array('quizid'=>$quizid, 'userid'=>$userid, 'submitted'=>$submitted, 'score'=>$percentageCorrect, 'complete'=>$complete);
	$dbQuery->execute($dbParams);
	
	echo "<script>window.location.href = 'view.php?id=".$quizid."'</script>";
?>