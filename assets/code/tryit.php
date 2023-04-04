<?php
/*
 
 * Downloaded from http://devzone.co.in
 */
 echo $_SERVER["DOCUMENT_ROOT"];
 echo "<br>";
 echo dirname(__FILE__);
$upload_dir = "../../uploads/";
if (isset($_FILES["myfile"])) { // it is recommended to check file type and size here
    if ($_FILES["myfile"]["error"] > 0) {
        echo "Error: " . $_FILES["file"]["error"] . "<br>";
    } else {
        move_uploaded_file($_FILES["myfile"]["tmp_name"], $upload_dir . $_FILES["myfile"]["name"]);
        //echo "Uploaded File :" . $_FILES["myfile"]["name"];
        echo "<pre>";
        print_r($_POST);
		echo "Files<br>";
        print_r($_FILES);
    }
}
?>