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
if (isset($_POST['PeopleT_id'])) {
	$MemberCharter = YesNo($_POST['MemberCharter']);
	$Adopter = YesNo($_POST['Adopter']);
	$Foster = YesNo($_POST['Foster']);	
	$PeopleT_id = dbPrep('PeopleT_id','i');	
	if (isset($_POST['MemRT'])) {
		$update_stmt = "UPDATE PeopleT SET MemberCharter=$MemberCharter,Adopter=$Adopter,Foster=$Foster WHERE PeopleT_id=$PeopleT_id";	
	} else {	
		$PeopleT_id = $_POST['PeopleT_id'];	
		$Membership = dbPrep('Membership','s');
		$MemberType = dbPrep('MemberType','s');
		$MemberLevel = dbPrep('MemberLevel','s');
		$mBy_PeopleT_id = dbPrep('mBy_PeopleT_id','i');
		$MemberSince = dbPrep('MemberSince','d');
		$MemberRenewed = dbPrep('MemberRenewed','d');
		$MemberInactive = dbPrep('MemberInactive','d');
		$MemCount = dbPrep('MemCount','i');
		$Vet = YesNo($_POST['Vet']);
		$update_stmt = "UPDATE PeopleT SET Membership=$Membership,MemberType=$MemberType,MemberSince=$MemberSince,MemberRenewed=$MemberRenewed,".
					"MemberInactive=$MemberInactive,MemberLevel=$MemberLevel,MemberCharter=$MemberCharter,Adopter=$Adopter,Foster=$Foster,".
					"MemCount=$MemCount WHERE PeopleT_id=$PeopleT_id";		
	}
	if ($result = $mysqli->query($update_stmt)) {
		$id = $PeopleT_id;
		if ($_POST[memHistory]=='Y') {
			$mAction = dbPrep('mAction','s');	
			if ($mAction=='Deactivate') {$MemberRenewed=$MemberInactive;}
			$insert_stmt = "INSERT INTO MembershipTrack SET ".
							"fk_PeopleT_id=$PeopleT_id,".
							"mRenewDate=$MemberRenewed,".
							"mMemCount=$MemCount,".
							"mMembership=$Membership,".
							"mMemberLevel=$MemberLevel,".
							"mAction=$mAction,".
							"mBy_PeopleT_id=$mBy_PeopleT_id";
			if ($result = $mysqli->query($insert_stmt)) {
			} else {
				$error_sql = $insert_stmt;
				$error_str = $mysqli->error;
				$error_msg = 'Database Error on update';
			}
		}
	} else {
		$error_sql = $update_stmt;
		$error_str = $mysqli->error;
		$error_msg = 'Database Error on update';
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

function YesNo($decision) {
	if (isset($decision)) {
		return("'Y'");
	} else {
		return("'N'");
	}	
}	
?>