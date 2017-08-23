<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../images/favicon.ico">

    <title>dklearn</title>
    <!-- Bootstrap core CSS -->
    <link href="../dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../assets/js/ie-emulation-modes-warning.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

		<?php
			include("../config.php");
			session_start();
         $userID=$_SESSION["currentUserID"];

         $dbQuery=$db->prepare("select * from users where id=:id");
         $dbParams=array('id'=>$userID);
         $dbQuery->execute($dbParams);

         while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
         {
            $admin=$dbRow["admin"];
         }
		?>

	</head>

	<body>



      <!-- The justified navigation menu is meant for single line per list item.
           Multiple lines will require custom code not provided by Bootstrap. -->
      <div class="masthead" style="background-image: url('../images/<?php echo $theme; ?>');background-repeat: repeat-yx; width: 100%;">
	  <!--
        <h3 class="text-muted">Project name</h3> -->

		<p style="text-align:center; font-size:2.5em"><a class="logo" href="index.php"><b>dk</b>learn</a></p>



        <nav>
          <ul class="nav nav-justified">
            <li><a href="../">Home</a></li>
            <li><a href="../course/">Courses</a></li>
            <li><a href="../dashboard/">Dashboard</a></li>
            <li><a href="../contact/">Contact</a></li>
            <li><a href="../profile/">Profile</a></li>
            <?php if ($admin == 1) { echo '<li><a href="../settings">Administration</a></li>'; } ?>


          </ul>
        </nav>
      </div>

      <div class="container">

         <h1>Settings</h1>
         <ul class="list">
            <li><a href="theme/"><h3>Theme</h3></a></li>
         </ul>

      </div>
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    	<script src="../assets/js/ie10-viewport-bug-workaround.js"></script>
	</body>
</html>
