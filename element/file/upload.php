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
        $topicid = $_POST["topicid"];
        $courseid = $_POST["courseid"];
        $name = $_POST["name"];
        $visibility = $_POST["visibility"];

        //extension
        $file_ext = explode('.', $file_name);
        $file_ext = strtolower(end($file_ext));

        $allowed = array('pdf', 'doc', 'docx');

        if(in_array($file_ext, $allowed))
        {
            if($file_error === 0)
            {
                if($file_size <= 5242880)
                {
					$time=time();
                    $file_destination = '/kunden/homepages/20/d664340320/htdocs/lms.dylankeys.com/element/file/uploads/';
                    $temp = explode(".", $file_name);
                    $newfilename = $temp[0] . "_" . round(microtime(true)) . '.' . end($temp);
                    move_uploaded_file($file_tmp, $file_destination . $newfilename);

                    $dbQuery=$db->prepare("insert into files values (null,:name,:filename,:courseid,:visibility)");
                    $dbParams=array('courseid'=>$courseid, 'name'=>$name, 'filename'=>$newfilename, 'visibility'=>$visibility);
                    $dbQuery->execute($dbParams);

                    redirect("create.php?action=addToCourse&courseid=".$courseid."&topicid=".$topicid);
                }
            }
        }
    }
    redirect("create.php?submitted=false");
?>