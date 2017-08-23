<?php //DK Learn configuration file

// set up database connection
$dbHost = 'db672227184.db.1and1.com';
$databaseName 	= 'db672227184';
$username 		= 'dbo672227184';
$password 		= 'Artip!2007';


// make the database connection
$db = new PDO("mysql:host=$dbHost;dbname=$databaseName;charset=utf8", "$username", "$password");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 	// enable error handling
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 			// turn off emulation mode

// get config variables
$dbQuery=$db->prepare("select * from config");
$dbQuery->execute();

while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
{
   $setting = $dbRow["setting"];

   if ($setting == "theme-colour")
   {
      $theme = $dbRow["value"];
   }
}

?>
