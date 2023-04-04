<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/psl-config.php';
 
$error_msg = '';
$error_str = '';
$error_sql = '';
if (isset($_POST['v'])) {
	$applid = $_POST['v'];
	$result = $mysqli->query("SELECT MAX(CAST(Member_id as SIGNED)) as id FROM PeopleT");
	$row = $result->fetch_assoc();
	$id = $row['id']+1;
	$sel_stmt = "SELECT * FROM Applications WHERE applid=$applid";
	if ($result = $mysqli->query($sel_stmt)) {
		$row = $result->fetch_assoc();
//		var_dump($row);
		$contact = json_decode($row['applContact']);
//		var_dump($contact);
		$fn	=	$mysqli->real_escape_string($contact->{'FName'});
		$ln	=	$mysqli->real_escape_string($contact->{'LName'});
		$on	=	$mysqli->real_escape_string($contact->{'OName'});
		$addr = $mysqli->real_escape_string($contact->{'Addr'});
		$city = $mysqli->real_escape_string($contact->{'City'});
		$sta = $contact->{'St'};
		$zip = $contact->{'Zip'};
		$Phone = $contact->{'Phone'};
		$Cell = $contact->{'Cell'};
		$Email = $contact->{'Email'};
		$MemberSince =  date("Y-m-d");

		if (1==1) {
		$insert_stmt = "INSERT INTO PeopleT ".
						"SET PeopleT_id=NULL,".
							"Member_id='$id',".
							"FirstName='$fn',".
							"LastName='$ln',".
							"OtherName='$on',".
							"Address='$addr',".
							"City='$city',".
							"StateProvince='$sta',".
							"MailCode='$zip',".
							"Email='$Email',".
							"Phone='$Phone',".
							"Cell='$Cell',".
							"MemberSince='$MemberSince',".
							"MemberBlob='Applicant joined SAGRR',".
							"Deleted='N'";
//		$insert_stmt = "INSERT INTO PeopleT (PeopleT_id,Member_id) VALUES (NULL,'$id')";
		if ($result = $mysqli->query($insert_stmt)) {
			$id = $mysqli->insert_id;
			$update_stmt = "UPDATE Applications SET fk_PeopleT_id=$id WHERE applid=$applid";
			if ($result = $mysqli->query($update_stmt)) {
//				$id = $id;
			} else {
				$error_sql = $update_stmt;
				$error_str = $mysqli->error;
				$error_msg = 'Database Error on update';
			}				
			
		} else {
			$error_sql = $insert_stmt;
			$error_str = $mysqli->error;
			$error_msg = 'Database Error, Insert Failed.';
		}
		}
	} else {
			$error_sql = $sel_stmt;
			$error_str = $mysqli->error;
			$error_msg = 'Database Error, Read Failed.';
		
	}
}
else {
	$error_msg = 'Key was not provided';
}

$results = array(
'error' => (! empty($error_msg)),
'errorDetail' => array(
	'error_sql' => $error_sql,
	'error_str' => $error_str,
	'error_msg' => $error_msg),
'data' =>  $id);
echo json_encode($results);
$mysqli->close();
?>