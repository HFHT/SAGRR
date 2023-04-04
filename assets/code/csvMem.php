<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error_msg = '';
$error_str = '';
$error_sql = '';
$today = date("Y-m-d");
if (isset($_POST['x']) || isset($_GET['x'])) {
    $prep_stmt = "SELECT PeopleT_id,Member_id,FirstName,LastName,Title,Salutation,Email,Phone,Cell,Fax,Address,City,StateProvince,MemberLevel,".
				"Country,MailCode,Company,WorkPhone,WorkEmail,Contact,Vet,Membership,MemCount,MemberBlob,Deleted,Interests,Teams,MemberType,PhotoLink,OtherName,".
				"DATE_FORMAT(MemberSince,'%m/%d/%Y') AS MemberSince,DATE_FORMAT(MemberRenewed,'%m/%d/%Y') AS MemberRenewed, ".
				"DATEDIFF(MemberRenewed, NOW()) AS MemberExpire ".
				"FROM PeopleT WHERE Deleted <> 'Y'";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$result = $mysqli->query("SELECT * FROM SIGroupT");
		$aryInt = array();
		while ($rcd = $result->fetch_assoc()) {
			$aryInt[$rcd['SelId']] =  preg_replace('/\s+/', '',$rcd['SelText']);
		}
		Outputcsv($stmt,$aryInt);
	} else {
		$error_sql = $update_stmt;
		$error_str = $mysqli->error;
		$error_msg = 'Database Error on download';
	}
} else {
	$error_msg = 'Key was not provided';
}
$results = array(
'error' => (! empty($error_msg)),
'errorDetail' => array(
	'error_sql' => $error_sql,
	'error_str' => $error_str,
	'error_msg' => $error_msg),
'data' =>  '');
//echo json_encode($results);
//var_dump($update_stmt);
$mysqli->close();
function ckVaccine($d1,$d2,$d3,$span) {
	return (($d1-$span<0) OR ($d2-$span<0) OR ($d3-$span<0));
}

function Outputcsv($rs,$aryi) {
	$hdr = array('No.',
				'Id',
				'Since',
				'Renewed',
				'Qty',
				'Name',
				'Address',
				'City,St,Zip',
				'Phone',
				'Cell',
				'Email',
				'Membership',
				'Interests',
				'Teams',
				'Skills');
	// output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=SAGRRMembers.csv');

	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// output the column headings
	fputcsv($output, $hdr);
	while ($rcd = $rs->fetch_assoc()) {
		$int = json_decode($rcd['Interests']);
		$tms = json_decode($rcd['Teams']);
		$interest = '';
		foreach ($int->{'Interests'} as $in) {
			$interest = $interest.$aryi[$in].' ';
		}
		$team = '';
		foreach ($tms->{'Interests'} as $in) {
			$team = $team.$aryi[$in].' ';
		}
		if ($rcd['OtherName']=='') {
			$nm = $rcd['LastName'].', '.$rcd['FirstName'];
		} else {
			$nm = $rcd['LastName'].', '.$rcd['FirstName'].' ('.$rcd['OtherName'].')';
		}
		$csv = array($rcd['PeopleT_id'],
					$rcd['Member_id'],
					$rcd['MemberSince'],
					$rcd['MemberRenewed'],
					$rcd['MemCount'],
					$nm,
					$rcd['Address'],
					$rcd['City']==''?'':$rcd['City'].', '.$rcd['StateProvince'].' '.$rcd['MailCode'],
					$rcd['Phone'],
					$rcd['Cell'],
					$rcd['Email'],
					$rcd['Membership'].':'.$rcd['MemberLevel'],
					$interest,
					$team,
					$rcd['MemberBlob']);
		fputcsv($output, $csv);
	}
}
?>
