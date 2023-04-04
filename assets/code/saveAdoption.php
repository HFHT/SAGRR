<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/psl-config.php';
//Following array determines the setting of the Dog's process and availability status
$arySQL = array("oka"=>["Adopted","NotAvailable"],
				"okf"=>["Fostered","Available"],
				"okt"=>["Match-Trial","Available"],
				"mema"=>["Adopted","NotAvailable"],
				"memf"=>["Fostered","Available"],
				"memt"=>["Match-Trial","Available"],				
				"return"=>["InProcess","Available"]);
//Following array determines the setting of the Applicant's adoption, foster state & status				
$aryAppl = array("reassignBtn"=>["oka"=>["Active","WaitList"],"okf"=>["Active","WaitList"],"okt"=>["Active","WaitList"]],
				 "adoptBtn"=>["oka"=>["Complete","Matched-Adopted"],"okf"=>["Active","Matched-Foster"],"okt"=>["Active","Match-Trial"]],
				 "swapBtn"=>["oka"=>["Complete","Matched-Adopted"],"okf"=>["Active","Matched-Foster"],"okt"=>["Active","Match-Trial"]]);
$error_msg = '';
$error_str = '';
$error_sql = '';
//var_dump($_POST);
if (isset($_POST['formData'])) {
//	$state = '';
	$formData = json_decode($_POST['formData']);
	$fk_applid 		= $formData->{'fk_applid'};
	$fk_PeopleT_id	= $formData->{'fk_PeopleT_id'};
	$createBy		= $formData->{'createBy'};
	$applmode		= $formData->{'applmode'};										// oka - Adoption, okf - Fostering
	$family			= $formData->{'family'};
	$priorState		= $formData->{'state'};
	$dogcnt			= $formData->{'dogcnt'};
	$memorappl		= $formData->{'memorappl'};	
	$dogCntAfter 	= $dogcnt+sizeof($formData->{'DogList'})-sizeof($formData->{'RetList'});
	$foraStatus		= $arySQL[$applmode][0];
	$foraAvail		= $arySQL[$applmode][1];
	$applState		= $aryAppl[$formData->{'applcomp'}][$applmode][0];
	$applStatus		= $aryAppl[$formData->{'applcomp'}][$applmode][1];
	if ($dogCntAfter>0 && $priorState=='Complete') {								// Override state if the applicant still has a dog
		$applState = 'Complete';
	}
//	if (sizeof($formData->{RetList})>0) {
//		$foraStatus		= $arySQL['return'][0];
//		$foraAvail		= $arySQL['return'][1];		
//	}
	try {
		$strmsg1 = sizeof($formData->{'DogList'})==0 ? "$family " : "$family $foraStatus: ";
//		if ($applmode=='oka') {
//			if (sizeof($formData->{DogList})==0) {
//				$status = 'WaitList';
//			} else {
//				$status = 'Matched-Adopted';
//				$state = ",applState='Complete'";
//				$stateSet = ",applProcState";
//				$stateVal = ",'Complete'";
//			}
//		} else {
//			$status = 'Matched-Foster';
//		}

		foreach ($formData->{'DogList'} as $dogid) {
			$dogkey = $dogid->{'dogkey'};
			$dogname = $dogid->{'dogname'};
			$strmsg1 = $strmsg1.$dogname.' ';
//			$memorappl = $memorappl=='fk_PeopleT_id' ? $memorappl : 'fk_applid';
			if ($memorappl=='fk_PeopleT_id') {
				$field = 'fk_PeopleT_id='.$fk_applid.',fk_applid=0';
			} else {
				$field = 'fk_PeopleT_id=0,fk_applid='.$fk_applid;
			}
			$prep_stmt="UPDATE DogT SET DogProcStatus='$foraStatus',DogCurStatus='$foraAvail',$field WHERE DogT_id=$dogkey";
			if (!$mysqli->query("UPDATE DogT SET DogProcStatus='$foraStatus',DogCurStatus='$foraAvail',$field WHERE DogT_id=$dogkey")) {
				throw new RuntimeException("Update of Dog id: $dogkey failed");
			}
			$prep_stmt="INSERT INTO DStatusTrackT ". 
				"(fk_DogT_id,StatusDate,DogProcStatus,DogCurStatus,applProcStatus,fk_PeopleT_id,createBy,StatusComment) VALUES ".
				"(".$dogid->{'dogkey'}.",CURDATE(),'$foraStatus','$foraAvail','X',$fk_PeopleT_id,'$createBy','Created by adoption processing. $family $foraStatus $dogname.')";
			$error_sql = $prep_stmt;
			if (!$mysqli->query($prep_stmt)) {
				throw new RuntimeException("Update of Status id: $dogkey failed");
			}
		}
		$strmsg2 = sizeof($formData->{'RetList'})==0 ? "" : "- Returned: ";	
		foreach ($formData->{'RetList'} as $dogid) {
			$dogkey = $dogid->{'dogkey'};
			$dogname = $dogid->{'dogname'};
			$strmsg2 = $strmsg2.$dogname.' ';
			if (!$mysqli->query("UPDATE DogT SET DogProcStatus='InProcess',DogCurStatus='Available',fk_applid=0,fk_PeopleT_id=0 WHERE DogT_id=$dogkey")) {
				throw new RuntimeException("Update of Dog Return id: $dogkey failed");
			}
			$prep_stmt="INSERT INTO DStatusTrackT ". 
				"(fk_DogT_id,StatusDate,DogProcStatus,DogCurStatus,applProcStatus,fk_PeopleT_id,createBy,StatusComment) VALUES ".
				"(".$dogid->{'dogkey'}.",CURDATE(),'InProcess','Available','X',$fk_PeopleT_id,'$createBy','Created by adoption return processing. $family returned $dogname.')";
			$error_sql = $prep_stmt;
			if (!$mysqli->query($prep_stmt)) {
				throw new RuntimeException("Update of Return Status id: $dogkey failed");
			}
		}
		if ($memorappl=='fk_applid') {
			if (!$mysqli->query("UPDATE Applications SET applStatus='$applStatus', applState='$applState' WHERE applid=$fk_applid ")) {
				throw new RuntimeException('Update of Application record failed.');
			}
			$prep_stmt="INSERT INTO DStatusTrackT ".
				"(StatusDate,DogProcStatus,DogCurStatus,applProcStatus,fk_PeopleT_id,fk_applid,createBy,applProcState,StatusComment) VALUES ".
				"(CURDATE(),'X','X','$applStatus',$fk_PeopleT_id,$fk_applid,'$createBy','$applState','Created by adoption processing. $strmsg1 $strmsg2')";
			if (!$mysqli->query($prep_stmt)) {
				throw new RuntimeException("Update of Application Status failed. ".$prep_stmt);
			}	
		}		
	} catch (RuntimeException $e) {
		$error_msg = $e->getMessage();
		$error_sql = $prep_stmt;
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
'data' =>  $aryAppl[$formData->{'applcomp'}][$applmode][0].' '.$aryAppl[$formData->{'applcomp'}][$applmode][1].' '.$priorState.' '.$dogcnt.' '.$dogCntAfter);
echo json_encode($results);
//var_dump($aryAppl);
$mysqli->close();
?>