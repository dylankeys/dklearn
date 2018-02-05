<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../images/favicon.ico">

	<?php
		include("../../config.php");
		include("../../lib.php");
		session_start();
        $userID=$_SESSION["currentUserID"];
		
		if(!has_capability("site:config",$userID))
		{
			echo "<script>window.location.href = '../../index.php?permission=0'</script>";
		}
		
		$dbQuery=$db->prepare("select * from users where id=:id");
        $dbParams = array('id'=>$userID);
        $dbQuery->execute($dbParams);
        //$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);

        while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
        {
           $username=$dbRow["username"];
		   $fullname=$dbRow["fullname"];
		   $profileimage=$dbRow["profileimage"];
        }
	?>
	
    <title><?php echo $sitename;?> | User Management</title>
	
	<!--DK CSS-->
	<link href="../../styles.css" rel="stylesheet">
	
	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1E88FF;">
		<!--<nav class="navbar navbar-expand-lg navbar-light bg-light">-->
		  <a class="navbar-brand" href="../../"><?php echo $sitename;?></a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			  <li class="nav-item">
				<a class="nav-link" href="../../">Home</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../../course/">Courses</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../../dashboard/">Dashboard</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../../contact/">Contact</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../../profile/">Profile</a>
			  </li>
			  <li class="nav-item active">
				<?php if (has_capability("site:config",$userID)) { echo '<a class="nav-link" href="../../settings/">Administration</a>'; } ?>
			  </li>
			</ul>
			<span class="navbar-text">
			
			  <?php
				if (isset($username)) {
					echo "<img src='".$profileimage."' width='28px' alt='Profile Image' class='rounded-circle'>&nbsp;<a href='../profile/'>".$fullname." (<a href='../profile/killSession.php'>Log out</a>)</a>";
				}
				else {
					echo "<a href='../../login/'>Log in or sign up</a>";
				}
			  ?>
			</span>
		  </div>
		</nav>

      <div class="container">
		 <br>
         <h1>User management</h1>
		 <br>
		
         <?php

            if (isset($_GET["delete"]) && $_GET["delete"] == 1)
            {
				$deleteid = $_GET["userid"];
				
				echo '<h3>Are you sure?</h3>';
				echo '<p>All related user data will be deleted from the system (incl. enrolments and completions). Proceed with caution.</p>';
				echo '<form class="confirm-delete" method="post" action="index.php">
						<input type="hidden" name="deleteid" value="'.$deleteid.'" />
						<button type="submit" class="btn btn-success">Yes</button>
					</form>';
				echo '<button type="button" onclick="window.location.href=\'index.php\'" class="btn btn-danger confirm-delete">No</button>';
			}
			else if(isset($_POST["deleteid"]))
			{
				$deleteid = $_POST["deleteid"];
				
				$dbQuery=$db->prepare("delete from users where id=:id");
         		$dbParams=array('id'=>$deleteid);
         		$dbQuery->execute($dbParams);
				
				$dbQuery=$db->prepare("delete from enrolments where userid=:id");
         		$dbQuery->execute($dbParams);
				
				$dbQuery=$db->prepare("delete from role_assignments where userid=:id");
         		$dbQuery->execute($dbParams);
				
				echo "<script>window.location.href = 'index.php?success=deleted'</script>";
			}
			else if (isset($_GET["edit"]) && $_GET["edit"] == 1)
            {
				$deleteid = $_GET["userid"];
				$dbQuery=$db->prepare("delete from users where id=:id");
         		$dbParams=array('id'=>$deleteid);
         		$dbQuery->execute($dbParams);
				
				echo "<script>window.location.href = 'index.php?success=deleted'</script>";
            }
			else 
			{

				if(isset($_GET["success"]))
				{
					if ($_GET["success"] == "deleted")
					{
						echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
							<strong>Success!</strong> User deleted.
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>';
					}
					else if ($_GET["success"] == "edited")
					{
						echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
							<strong>Success!</strong> User edited.
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>';
					}
				}
		?>
		
		<table class="table table-hover">
			<thead>
				<tr>
					<th scope="col">Full name</th>
					<th scope="col">Username</th>
					<th scope="col">Email address</th>
					<th scope="col">Country</th>
					<th scope="col">Last login</th>
					<th scope="col">Actions</th>
				</tr>
			</thead>
			<tbody>
			
               <?php
                  $dbQuery=$db->prepare("select * from users");
   			      $dbQuery->execute();

      		      while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
                    $id=$dbRow["id"];
                    $username=$dbRow["username"];
					$fullname=$dbRow["fullname"];
					$email=$dbRow["email"];
					$country=$dbRow["country"];
					$timestamp=$dbRow["lastlogin"];
					$lastlogin = gmdate("Y-m-d H:i:s", $timestamp);
					
					if ($lastlogin == "1970-01-01 00:00:00")
					{
						$lastlogin = "Never logged in";
					}

                     echo '<tr>
							<td>'.$fullname.'</td>
							<td>'.$username.'</td>
							<td>'.$email.'</td>
							<td>'.$country.'</td>
							<td>'.$lastlogin.'</td>
							<th scope="row"><a href="index.php?edit=1&userid='.$id.'">Edit</a> | <a href="index.php?delete=1&userid='.$id.'">Delete</a></th>
						</tr>';
                  }
               ?>
			
			</tbody>
		</table>
		
		<?php
			}
		?>
      </div>
	  <br>
	  <footer>
		<p class="copyright"><?php echo $sitename ." | &copy ". date("Y"); ?></p>
		<ul class="v-links">
			<li>Home</li>
			<li>Courses</li>
			<li>Dashboard</li>
			<li>Contact</li>
			<li>Profile</li>
		</ul>
	  </footer>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
	</body>
</html>
