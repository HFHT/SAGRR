<?php
// This module performs a series of queries to obtain counts of key metrics that are used in JQM ui-li-count spans on the main menus.
// An object is created with a unique key field and associated value, be careful with the SQL AS clauses, these are used to create the key field
// The <span> class is derived by prepending d_ to the object's key field
//
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

$arySQL = array(array(false,"SELECT count(*) AS DogActive FROM DogT WHERE (Deleted <> 'Y' AND NCI <> 'Y' AND Bridge <> 'Y') AND (DogProcStatus= 'Inprocess' OR DogProcStatus= 'Fostered' OR DogProcStatus= 'Match-Trial' OR DogCurStatus= 'Hold-Behavior' OR DogCurStatus='Hold-Medical' OR DogCurStatus='InTransit')"),
				array(false,"SELECT count(*) AS DogOnlyInProc FROM DogT WHERE (Deleted <> 'Y' AND NCI <> 'Y' AND Bridge <> 'Y') AND (DogProcStatus= 'Inprocess') AND (DogCurStatus<> 'Hold-Behavior' AND DogCurStatus<>'Hold-Medical' AND DogCurStatus<>'InTransit')"),
				array(false,"SELECT count(*) AS DogOnlyFost FROM DogT WHERE (Deleted <> 'Y' AND NCI <> 'Y' AND Bridge <> 'Y') AND (DogProcStatus= 'Fostered') AND (DogCurStatus<> 'Hold-Behavior' AND DogCurStatus<>'Hold-Medical' AND DogCurStatus<>'InTransit')"),
				array(false,"SELECT count(*) AS DogOnlyTrial FROM DogT WHERE (Deleted <> 'Y' AND NCI <> 'Y' AND Bridge <> 'Y') AND (DogProcStatus= 'Match-Trial') AND (DogCurStatus<> 'Hold-Behavior' AND DogCurStatus<>'Hold-Medical' AND DogCurStatus<>'InTransit')"),
				array(false,"SELECT count(*) AS dogT FROM DogT WHERE(DELETED <> 'Y')"),
				array(true,"SELECT DogProcStatus AS keyx, COUNT(*) AS value FROM DogT WHERE Deleted <> 'Y' GROUP BY DogProcStatus","dogT"),
				array(true,"SELECT DogCurStatus AS keyx, COUNT(*) AS value FROM DogT WHERE Deleted <> 'Y' GROUP BY DogCurStatus","dogT"),
				array(true,"SELECT MemberLevel AS keyx, COUNT(*) AS value FROM PeopleT WHERE Membership <> 'Inactive' AND Deleted <> 'Y' GROUP BY MemberLevel"),
				array(true,"SELECT Membership AS keyx, COUNT(*) AS value FROM PeopleT WHERE Deleted <> 'Y' GROUP BY Membership","memT"),
				array(true,"SELECT attachType AS keyx, COUNT(*) AS value FROM FileAsset WHERE Deleted <> 'Y' GROUP BY attachType"),
				array(true,"SELECT w_status AS keyx, COUNT(*) AS value FROM WishList GROUP BY w_status"),
				array(true,"SELECT applStatus AS keyx, COUNT(*) AS value FROM Applications WHERE Deleted <> 'Y' GROUP BY applStatus","appT"),				
				array(true,"SELECT applStatus AS keyx, COUNT(*) AS value FROM Applications WHERE Deleted <> 'Y' AND applState='Active' GROUP BY applStatus","appA","A_"),				
				array(true,"SELECT applState AS keyx, COUNT(*) AS value FROM Applications WHERE Deleted <> 'Y' GROUP BY applState","appS","S_"),				
				array(false,"SELECT COUNT(*) AS notinactive FROM PeopleT WHERE Membership <> 'Inactive' AND Deleted <> 'Y'"),
				array(false,"SELECT SUM(MemCount) AS trueMem FROM PeopleT WHERE Membership <> 'Inactive' AND Deleted <> 'Y'"),
				array(false,"SELECT COUNT(*) AS dogX FROM DogT WHERE Deleted = 'X'"),
				array(false,"SELECT COUNT(*) AS vaccP FROM DogT WHERE (DATEDIFF(Rabies, NOW()) BETWEEN 1 AND 59 OR DATEDIFF(DA2PP_DHLPP, NOW()) BETWEEN 1 AND 59 OR DATEDIFF(Bordetella, NOW()) BETWEEN 1 AND 59 OR DATEDIFF(Leptospirosis, NOW()) BETWEEN 1 AND 59) AND Deleted <> 'Y'"),
				array(false,"SELECT COUNT(*) AS vaccX FROM DogT WHERE (DATEDIFF(Rabies, NOW()) < 0 OR DATEDIFF(DA2PP_DHLPP, NOW()) <0 OR DATEDIFF(Bordetella, NOW()) < 0 OR DATEDIFF(Leptospirosis, NOW()) < 0) AND Deleted <> 'Y'"),
				array(false,"SELECT COUNT(*) AS memPend FROM PeopleT WHERE Membership <> 'Inactive' AND Deleted <> 'Y' AND (DATEDIFF(NOW(),MemberRenewed) BETWEEN 344 AND 364)"),
				array(false,"SELECT COUNT(*) AS memExp FROM PeopleT WHERE Membership <> 'Inactive' AND Deleted <> 'Y' AND (DATEDIFF(NOW(),MemberRenewed) > 364)"),
				array(false,"SELECT COUNT(*) AS memNull FROM PeopleT WHERE Deleted = 'N' AND MemberRenewed IS NULL"),
				array(false,"SELECT COUNT(*) AS adopter FROM PeopleT WHERE Membership <> 'Inactive' AND Deleted <> 'Y' AND Adopter='Y'"),
				array(false,"SELECT COUNT(*) AS foster FROM PeopleT WHERE Membership <> 'Inactive' AND Deleted <> 'Y' AND Foster='Y'"),
				array(false,"SELECT COUNT(*) AS charter FROM PeopleT WHERE MemberCharter = 'Y' AND Deleted <> 'Y'"),
				array(false,"SELECT COUNT(*) AS memX FROM PeopleT WHERE Deleted = 'X'"),
				array(false,"SELECT COUNT(*) AS applyAll FROM Applications WHERE Deleted <> 'Y'"),
				array(false,"SELECT COUNT(*) AS vetX FROM VetVisit WHERE (DATEDIFF(VetFollowup, NOW()) < 0) AND VetFollowupComp='N' AND Deleted <> 'Y'"),
				array(false,"SELECT COUNT(*) AS vetP FROM VetVisit WHERE (DATEDIFF(VetFollowup, NOW()) BETWEEN 1 AND 59) AND VetFollowupComp='N' AND Deleted <> 'Y'"),
				array(false,"SELECT COUNT(*) AS uedit FROM members WHERE (INSTR(Role,'\"Dog\":\"Y')>0 OR INSTR(Role,'\"Mem\":\"Y')>0) AND Deleted<>'Y'"),
				array(false,"SELECT COUNT(*) AS uadm FROM members WHERE INSTR(Role,'\"Admin\":\"Y')>0 AND Deleted<>'Y'"),
				array(false,"SELECT COUNT(*) AS uunlink FROM members WHERE fk_PeopleT_id IS NULL AND Deleted<>'Y'"),
				array(false,"SELECT COUNT(*) AS unew FROM members WHERE Validated='N' AND Deleted<>'Y'"),
				array(false,"SELECT COUNT(*) AS locked FROM members WHERE Locked='Y' AND Deleted<>'Y'"),
				array(false,"SELECT COUNT(*) AS ulinked FROM members WHERE fk_PeopleT_id IS NOT NULL AND Deleted<>'Y'"),
//				array(false,"SELECT MAX(SAGRR_id) as MaxDog FROM DogT where substring(SAGRR_id,1,2)='".date('y')."'"),
				array(false,"SELECT MAX(SAGRR_id) as MaxDog FROM DogT WHERE SAGRR_id != 'NCI'"),
				array(false,"SELECT MAX(CAST(applid as SIGNED)) as MaxAppl FROM Applications"),
				array(false,"SELECT MAX(CAST(Member_id as SIGNED)) as MaxMem FROM PeopleT")
				);
$objErr = new StdClass();			
$objD = new StdClass();	
$varDebug = '';													// This object will hold all of the metrics			
if (!empty($_POST['userId'])) {
//if (1==1) { 
	try {
		foreach ($arySQL as $qSQL) {										// Process each SQL statement in the above array
//			var_dump($qSQL);
			$result = $mysqli->query($qSQL[1]);
			$total = 0;
			while ($row = $result->fetch_assoc()) {							// Some queries have more than one row returned
//				var_dump($row);
				if ($qSQL[0]) {												// Is this a multi-row query?
					if ($row['keyx']=='') {
						$objErr = 'Invalid key returned by '.$qSQL[1];
					} else {
						$key = $qSQL[3].$row['keyx'];
						$objD->$key = $row['value'];					// Massage the fetch_assoc() result set to object form
						$total=$total+$row['value'];
					}
				} else {
					$k = key($row);											// Massage the fetch_assoc() result set to object form
					$objD->$k = $row[$k];
					$total=$total+$row[1];
				}
			}
			if (isset($qSQL[2])) {											// Does this query need to be totaled?
				$key = $qSQL[2];
				$objD->$key = $total;									// Add a total entry, the key name to be used is in the above array
//				$varDebug = $varDebug . 'v: ' . $qSQL[2] . 't: ' . $total;
			}
		}
	}
	catch(Exception $e) {
		$objErr->error_str = $e->getMessage();
		$objErr->error_sql = $prep_stmt;
		$objErr->error_msg = 'Could not obtain summary counts, try again later!';		
	}
} else {
	$objErr->error_msg = 'Invalid key provided.';
}

$results = array(
'error' => (! empty($objErr->error_msg)),
'errorDetail' => $objErr,
'debug' => $varDebug,
'data' =>  $objD);
echo json_encode($results);
$mysqli->close();
?>