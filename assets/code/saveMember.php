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
$id = "0";
if (isset($_POST['LastName'])) {
	$PeopleT_id = $_POST['PeopleT_id'];	
	$LastName = dbPrep('LastName','s');
	$FirstName = dbPrep('FirstName','s');
	$Member_id = dbPrep('Member_id','s');
	$Salutation = dbPrep('Salutation','s');
	$OtherName = dbPrep('OtherName','s');
	$MemberSince = dbPrep('MemberSince','d');
	$MemberRenewed = dbPrep('MemberRenewed','d');	
	$MemberQuickNote = dbPrep('MemberQuickNote','s');	
	$Email = dbPrep('Email','s');
	$Phone = dbPrep('Phone','s');
	$Cell = dbPrep('Cell','s');
	$Fax = dbPrep('Fax','s');
	$Address = dbPrep('Address','s');
	$City = dbPrep('City','s');
	$StateProvince = dbPrep('StateProvince','s');	
	$MailCode = dbPrep('MailCode','s');
	$Country = dbPrep('Country','s');	

	if ($_POST['Update'] == 'Create') {
		// if we are inserting a new record check that the dog doesn't already exist
		if ($result = $mysqli->query("SELECT DogT_id FROM DogT WHERE SAGRR_id = $SAGRR_id")) {
			$row_cnt = $result->num_rows;
			if ($row_cnt > 0) {		
				// A dog with this id already exists
				$error_msg .= 'A dog with this SAGRR id already exists!';
			} else {
				// Insert the new dog into the database 																
				$insert_stmt = "INSERT INTO DogT (SAGRR_id, InTakeDate, DogName, FormerName, Birthdate, Age_YY, Age_MM, AKC_id, DeathDate,".
								"DMChipData, DMChipMfg_id, DogSex, AlteredBy, AlteredDate, fk_DStatusTrackT_id, Weight, fk_ColorT_id,".
								"fk_SourceT_id, DogBlob, DogCurMemNo, DogCurMember, Deleted) ". 
								"VALUES($SAGRR_id,$InTakeDate,$DogName,$FormerName,$Birthdate,$Age_YY,$Age_MM,$AKC_id,$DeathDate,".
								"$DMChipData,$DMChipMfg_id,$DogSex,$AlteredBy,$AlteredDate,NULL,$Weight,$fk_ColorT_id,NULL,$DogBlob,".
								"$DogCurMemNo,$DogCurMember,'N')";
//				echo $insert_stmt;
				if ($result = $mysqli->query($insert_stmt)) {
					$id = $mysqli->insert_id;
				} else {
					$error_sql = $insert_stmt;
					$error_str = $mysqli->error;
					$error_msg = 'Database Error, Insert Failed.';
				}	
			}
		} else {
			$error_msg = 'Database Error, check of dog record failed.';
		}
	} else {
		$update_stmt = "UPDATE PeopleT SET LastName=$LastName,FirstName=$FirstName,Member_id=$Member_id,Salutation=$Salutation,".
						"OtherName=$OtherName,MemberSince=$MemberSince,MemberRenewed=$MemberRenewed,Email=$Email,Phone=$Phone,Cell=$Cell,".
						"Fax=$Fax,Address=$Address,City=$City,StateProvince=$StateProvince,MailCode=$MailCode,".
						"Country=$Country,Deleted='N',MemberQuickNote=$MemberQuickNote WHERE PeopleT_id=$PeopleT_id";
		if ($result = $mysqli->query($update_stmt)) {
				$id = $PeopleT_id;
		} else {
			$error_sql = $update_stmt;
			$error_str = $mysqli->error;
			$error_msg = 'Database Error on update';
		}				
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