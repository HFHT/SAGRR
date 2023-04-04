<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$arySQL = array(
				"dl"=>"SELECT DogT_id,SAGRR_id,DogName FROM DogT WHERE Deleted<>'Y'",
				"pl"=>"SELECT PeopleT_id,Member_id,LastName FROM PeopleT WHERE Deleted<>'Y'",
				"al"=>"SELECT applid,applDocId,applContact FROM Applications WHERE Deleted<>'Y'"
				);
$error = false;
$row = array();
$s= $_GET['s'];
if (isset($_GET['l'])) {
	$prep_stmt = str_replace('{q}',$_GET['q'],$arySQL[$_GET['l']]);
//	$prep_stmt = "SELECT * FROM WishList $where ORDER BY w_date DESC";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);	
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data.csv');

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		// output the column headings
		fputcsv($output, array('Key', 'ID', 'Name'));
		foreach ($row as $rcd) {
			fputcsv($output, $rcd);
		}
	} else {
        $error = true;;
    }
} else {
	$error = true;;
}
?>
