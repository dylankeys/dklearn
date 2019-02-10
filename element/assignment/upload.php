<?php
    include ("../../config.php");
	include ("../../lib.php");

    if(isset($_FILES['file'])) 
    {   
        $file = $_FILES['file'];
        
        //file properties
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        
        //add to database
        $userid = $_POST["userid"];
        $assignid = $_POST["assignid"];

        //extension
        $file_ext = explode('.', $file_name);
        $file_ext = strtolower(end($file_ext));

        $allowed = array('pdf', 'doc', 'docx', 'zip', 'jpg', 'jpeg', 'png');

        if(in_array($file_ext, $allowed))
        {
            if($file_error === 0)
            {
                if($file_size <= 5242880)
                {
					$time=time();
                    $file_destination = '/kunden/homepages/20/d664340320/htdocs/lms.dylankeys.com/element/assignment/submissions/';
                    $temp = explode(".", $_FILES["file"]["name"]);
                    $newfilename = round(microtime(true)) . '.' . end($temp);
                    move_uploaded_file($_FILES["file"]["tmp_name"], $file_destination . $newfilename);

                    $dbQuery=$db->prepare("insert into assignment_submissions values (null,:assignid,:userid,:submitted,:filename)");
                    $dbParams=array('assignid'=>$assignid, 'filename'=>$newfilename, 'submitted'=>$time, 'userid'=>$userid);
                    $dbQuery->execute($dbParams);

                    $dbQuery=$db->prepare("insert into assignment_grades values (null,:assignid,:userid,:graded,null,null)");
                    $dbParams=array('assignid'=>$assignid, 'userid'=>$userid, 'graded'=>'0');
                    $dbQuery->execute($dbParams);

                    redirect("view.php?id=" . $assignid . "&submitted=true");
                }
            }
        }
    }
    redirect("view.php?id=" . $assignid . "&submitted=false");
?>