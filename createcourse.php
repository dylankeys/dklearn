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
    
    <title>Create course</title>
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="images/favicon.ico">

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="assets/js/ie-emulation-modes-warning.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	</head>

	<body>

		<!-- The justified navigation menu is meant for single line per list item.
           Multiple lines will require custom code not provided by Bootstrap. -->
      <div class="masthead" style="background-image: url('images/bg-color.png');background-repeat: repeat-yx; width: 100%;">
	  <!--
        <h3 class="text-muted">Project name</h3> -->
		
		<p style="text-align:center; font-size:2.5em"><a class="logo" href="index.php"><b>dk</b>learn</a></p>
		
        <nav>
          <ul class="nav nav-justified">
            <li class="active"><a href="index.php">Home</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="admin.php">Administration</a></li>
            
      
          </ul>
        </nav>
      </div>

      <div class="container">

		<form method="post" action="createcourse-query.php">
			<table>
				<tr> <td>Course title</td> <td> <input type="text" name="title"> </td> </tr>

				<tr> <td>Description</td> <td> <textarea name="description" rows="10" cols="30">Course description</textarea> </td> </tr>

				<tr> <td>Start date (dd/mm/yy)</td> <td> <input type="text" name="start"> </td> </tr>

				<tr> <td>End date (dd/mm/yy)</td> <td> <input type="text" name="end"> </td> </tr>

				<tr> <td>Use desc. media (y/n)</td> <td> <input type="text" name="usemedia"> </td> </tr>

				<tr> <td>Embed media</td> <td> <textarea name="media" rows="10" cols="30">HTML embed code</textarea> </td> </tr>

				<tr> <td>Activity count (max 10)</td> <td> <input type="number" name="activitycount"> </td> </tr>

				<tr> <td>Course active (y/n)</td> <td> <input type="text" name="active"> </td> </tr>
			</table>
			<input type="submit" />
		</form>
	  </div>
	</body>
</html>