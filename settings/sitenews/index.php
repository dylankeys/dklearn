<?php
	session_start();
	include("../../config.php");
	include("../../lib.php");
?>
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
	
    <title><?php echo $sitename;?> | Site News</title>
	
	<!--DK CSS-->
	<link href="../../styles.css" rel="stylesheet">

	<!--CKEDITOR JS-->
	<script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
	
	</head>

	<body onload="disableField()">

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: <?php echo $theme;?>;">
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
		 <?php
			if(isset($_GET["success"]))
			{
				if ($_GET["success"] == "deleted")
				{
					echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> Post deleted.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
				}
				else if ($_GET["success"] == "add")
				{
					echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> Site news post added.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
				}
				else if ($_GET["success"] == "hidden")
				{
					echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> Site news post hidden.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
				}
				else if ($_GET["success"] == "visible")
				{
					echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> Site news post now visible.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
				}
			}
		 ?>
         <h1>Site news</h1>
		 <br>
		
         <?php
		 	if (isset($_GET["action"]) && $_GET["action"] == "Hide")
            {
				$hideid = $_GET["id"];
				
				$dbQuery=$db->prepare("update site_news set `visible`='0' where id=:id");
         		$dbParams=array('id'=>$hideid);
         		$dbQuery->execute($dbParams);
				
				redirect("index.php?success=hidden");
			}
			else if (isset($_GET["action"]) && $_GET["action"] == "Show")
            {
				$showid = $_GET["id"];

				$dbQuery=$db->prepare("update site_news set `visible`='1' where id=:id");
         		$dbParams=array('id'=>$showid);
         		$dbQuery->execute($dbParams);
				
				redirect("index.php?success=visible");
			}
			else if (isset($_POST["addPost"]))
			{
				$heading = $_POST["heading"];
				$content = $_POST["content"];
				$postedtime = $_POST["postedtime"];
				$postedby = $_POST["postedby"];
				$visible = $_POST["visible"];

				$dbQuery=$db->prepare("insert into site_news values (null,:heading,:content,:postedtime,:postedby,:visible)");
         		$dbParams=array('heading'=>$heading,'content'=>$content,'postedtime'=>$postedtime,'postedby'=>$postedby,'visible'=>$visible);
         		$dbQuery->execute($dbParams);

				redirect("index.php?success=add");
			}
            else if (isset($_GET["action"]) && $_GET["action"] == "delete")
            {
				$deleteid = $_GET["id"];
				
				echo '<h3>Are you sure?</h3>';
				echo '<p>This post will be permanently deleted from the system.</p>';
				echo '<form class="confirm-delete" method="post" action="index.php">
						<input type="hidden" name="deleteid" value="'.$deleteid.'" />
						<button type="submit" class="btn btn-success">Yes</button>
					</form>';
				echo '<button type="button" onclick="window.location.href=\'index.php\'" class="btn btn-danger confirm-delete">No</button>';
			}
			else if(isset($_POST["deleteid"]))
			{
				$deleteid = $_POST["deleteid"];
				
				$dbQuery=$db->prepare("delete from site_news where id=:id");
         		$dbParams=array('id'=>$deleteid);
         		$dbQuery->execute($dbParams);
				
				redirect("index.php?success=deleted");
			}
			else if (isset($_GET["action"]) && $_GET["action"] == "add")
			{
			?>

			<h3>Add a post</h3>
			<form method="post" action="index.php">
				<div class="form-group">
					<label for="heading">Heading</label>
					<input class="form-control" id="heading" name="heading" type="text">
				</div>
			
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="content">Post content</label>
						<textarea class="form-control" id="content" name="content" rows="5"></textarea>

						<script>
							CKEDITOR.replace( 'content' );
						</script>
					</div>
				</div>
				
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="visible">Visibility</label>
						<select class="form-control" id="visible" name="visible">
							<option value="1" selected>Show</option>
							<option value="0">Hide</option>
						</select>
					</div>
				</div>
				
				<input type="hidden" name="postedtime" value="<?php echo date('d-m-y'); ?>" />
				<input type="hidden" name="postedby" value="<?php echo $username; ?>" />
				<button type="submit" name="addPost" class="btn btn-primary">Add post</button>
			</form>
		<?php
			}
			else {
		?>

		<table class="table table-hover">
			<thead>
				<tr>
					<th scope="col">Heading</th>
					<th scope="col">Content</th>
					<th scope="col">Posted by</th>
					<th scope="col">Posted time</th>
					<th scope="col">Actions</th>
				</tr>
			</thead>
			<tbody>
			
               <?php
                  $dbQuery=$db->prepare("select * from site_news");
   			      $dbQuery->execute();

      		      while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
                    $id=$dbRow["id"];
                    $heading=$dbRow["heading"];
					$content=$dbRow["content"];
					$postedby=$dbRow["postedby"];
					$postedtime=$dbRow["postedtime"];
					$visible=$dbRow["visible"];

                     echo '<tr>
							<td>'.$heading.'</td>
							<td>'.$content.'</td>
							<td>'.$postedby.'</td>
							<td>'.$postedtime.'</td>';

					if ($visible == "1")
					{
						$visaction = "Hide";
					}
					else {
						$visaction = "Show";
					}
					
					echo '<th scope="row"><a href="index.php?action='.$visaction.'&id='.$id.'">'.$visaction.'</a> | <a href="index.php?action=delete&id='.$id.'">Delete</a></th>
						</tr>';
                  }
               ?>
			
			</tbody>
		</table>

		<a class="btn btn-primary" href="index.php?action=add">Add a new post</a>
		
		<?php
			}
		?>
      </div>
	  <br>
	  <footer>
		<p class="copyright"><?php echo $sitename ." | &copy ". date("Y"); ?></p>
		<ul class="v-links">
			<li><a href="../../">Home</a></li>
			<li><a href="../../course">Courses</a></li>
			<li><a href="../../dashboard">Dashboard</a></li>
			<li><a href="../../contact">Contact</a></li>
			<li><a href="../../profile">Profile</a></li>
		</ul>
	  </footer>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
	</body>
</html>
