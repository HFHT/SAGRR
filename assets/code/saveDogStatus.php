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
$id = ''; 
if (isset($_POST['fk_DogT_id'])) {

    $fk_DogT_id = dbPrep('fk_DogT_id', 'i');
    $fk_DStatusT_id = dbPrep('fk_DStatusT_id', 'i');
    $applHV_fk_PeopleT = dbPrep('applHV_fk_PeopleT', 'i');
	$applInTake_fk_PeopleT = dbPrep('applInTake_fk_PeopleT', 'i');
    $fk_PeopleT_id = dbPrep('fk_PeopleT_id', 'i');
	$fk_applid = dbPrep('fk_applid', 'i');
    $fk_Location_id = dbPrep('fk_Location_id', 'i');
    $DogProcStatus = dbPrep('DogProcStatus', 's');
    $DogCurStatus = dbPrep('DogCurStatus', 's');
	$applProcStatus = dbPrep('applProcStatus', 's');
	$applProcState = dbPrep('applProcState', 's');
	$StatusDate = dbPrep('StatusDate', 'd');
	$StatusComment = dbPrep('StatusComment', 's');
	$createBy = dbPrep('createBy', 's');
    $DStatusTrackT_id = dbPrep('DStatusTrackT_id', 'i');
	if (isset($_POST['DogNCIBridgeSel'])) {
		$NCI = $_POST['DogNCIBridgeSel'] == 'dogNCI' ? ",NCI='Y'" : ",NCI='N'";
		$Bridge = $_POST['DogNCIBridgeSel'] == 'dogBridge' ? ",Bridge='Y'" : ",Bridge='N'";	
		$DogCurStatus = $_POST['DogNCIBridgeSel'] == 'dogBridge' ? "'Bridge'" : $DogCurStatus;
		$DogCurStatus = $_POST['DogNCIBridgeSel'] == 'dogNCI' ? "'NCI'" : $DogCurStatus;
		$prep_stmt = "INSERT INTO DStatusTrackT SET ".
					"fk_DogT_id=$fk_DogT_id,".
					"fk_DStatusT_id=$fk_DStatusT_id,".
					"fk_Location_id=$fk_Location_id,".
					"fk_PeopleT_id=$fk_PeopleT_id,".
					"fk_applid=$fk_applid,".
					"StatusDate=$StatusDate,".
					"DogProcStatus=$DogProcStatus,".
					"DogCurStatus=$DogCurStatus,".
					"applProcStatus=$applProcStatus,".
					"applProcState=$applProcState,".
					"StatusComment=$StatusComment,".
					"createBy=$createBy,".
					"Deleted='N' ".$NCI.$Bridge;

		if ($result = $mysqli->query($prep_stmt)) {
			$id = $DStatusTrackT_id<>'0' ? $DStatusTrackT_id : $mysqli->insert_id;
			$Bridge = $_POST['DogNCIBridgeSel'] == 'dogBridge' ? ",Bridge='Y',DeathDate=$StatusDate" : ",Bridge='N'";	
			$update_stmt = "UPDATE DogT SET DogProcStatus=$DogProcStatus,DogCurStatus=$DogCurStatus $NCI $Bridge WHERE DogT_id=$fk_DogT_id";
			if (!($result = $mysqli->query($update_stmt))) {
				$error_sql = $update_stmt;
				$error_str = $mysqli->error;
				$error_msg = 'Database Error on updating dog';
			}				
		} else {
			$error_sql = $prep_stmt;
			$error_str = $mysqli->error;
			$error_msg = 'Database Error on saving status!';
		}
	} else {
		if ($DStatusTrackT_id<>'0') {
			$prep_stmt = "UPDATE DStatusTrackT SET fk_DogT_id=$fk_DogT_id,fk_DStatusT_id=$fk_DStatusT_id,fk_PeopleT_id=$fk_PeopleT_id,fk_applid=$fk_applid,fk_Location_id=$fk_Location_id,DogProcStatus=$DogProcStatus,DogCurStatus=$DogCurStatus,StatusDate=$StatusDate,applProcStatus=$applProcStatus,applProcState=$applProcState,StatusComment=$StatusComment,Deleted='N' WHERE DStatusTrackT_id=$DStatusTrackT_id";
		} else {
			$prep_stmt = "INSERT INTO DStatusTrackT (fk_DogT_id,fk_DStatusT_id,fk_Location_id,fk_PeopleT_id,fk_applid,StatusDate,DogProcStatus,DogCurStatus,applProcStatus,applProcState,StatusComment,createBy,Deleted) VALUES($fk_DogT_id,$fk_DStatusT_id,$fk_Location_id,$fk_PeopleT_id,$fk_applid,$StatusDate,$DogProcStatus,$DogCurStatus,$applProcStatus,$applProcState,$StatusComment,$createBy,'N')";
		}

		if ($result = $mysqli->query($prep_stmt)) {
			$id = $DStatusTrackT_id<>'0' ? $DStatusTrackT_id : $mysqli->insert_id;
			if ($_POST['a']=="y") {														// Is this a new or most current status ?
				$DogCurMemNo = dbPrep('DogCurMemNo', 's');
				$DogCurMember = dbPrep('DogCurMember', 's');
				$set = $applProcStatus=="'HomeVisit'"?',applHV_fk_PeopleT='.$fk_PeopleT_id : ',applInTake_fk_PeopleT='.$fk_PeopleT_id;
				if ($fk_applid<>'0') {													// Are we saving status for an application or dog?
//					$update_stmt = "UPDATE Applications SET applStatus=$applProcStatus,applState=$applProcState,fk_PeopleT_id=$fk_PeopleT_id $set WHERE applid=$fk_applid";
					$update_stmt = "UPDATE Applications SET applStatus=$applProcStatus,applState=$applProcState $set WHERE applid=$fk_applid";
				} else {
					$update_stmt = "UPDATE DogT SET DogCurMemNo=$DogCurMemNo,DogCurMember=$DogCurMember,DogProcStatus=$DogProcStatus,DogCurStatus=$DogCurStatus,fk_PeopleT_id=$fk_PeopleT_id WHERE DogT_id=$fk_DogT_id";
				}
				if (!($result = $mysqli->query($update_stmt))) {
					$error_sql = $update_stmt;
					$error_str = $mysqli->error;
					$error_msg = 'Database Error on updating member';
				}				
			}
		} else {
			$error_sql = $prep_stmt;
			$error_str = $mysqli->error;
			$error_msg = 'Database Error on saving status!';
		}
	}
}
$results = array(
'error' => (! empty($error_msg)),
'errorDetail' => array(
	'error_sql' => $error_sql,
	'error_str' => $error_str,
	'error_msg' => $error_msg),
'data' =>  $id,
'DogNCIBridge'=>$_POST['DogNCIBridgeSel']);
echo json_encode($results);
$mysqli->close();
?>