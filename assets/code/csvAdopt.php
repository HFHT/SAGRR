<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors", 1);

$error_msg = '';
$error_str = '';
$error_sql = '';
$today = date("Y-m-d");
if (isset($_POST['x']) || isset($_GET['x'])) {
	$s = $_GET['s'];
	$where = ($s=='a') ? "AND a.applState='Active'" : "";	
    $prep_stmt = "SELECT a.applid,a.applDocId,DATE_FORMAT(a.applDateTime,'%m/%d/%Y') AS applDateTime,a.applComplete,
				DATE_FORMAT(a.applApproveDate,'%m/%d/%Y') AS applApproveDate,a.applApproved,a.applApprovedBy,a.applFostAdopt,a.applFost,a.applAdopt,
				a.applContact,a.applSource,a.applProgress,a.applStatus,a.fk_PeopleT_id,a.applHV_fk_PeopleT,a.applAppr_fk_PeopleT,
				a.ApplSec0,a.ApplSec3,a.ApplSec4,
				b.PeopleT_id as rkey,b.Member_id as rid,b.FirstName as rfn,b.LastName as rln,
				c.PeopleT_id as hvkey,c.Member_id as hvid,c.FirstName as hvfn,c.LastName as hvln,
				d.PeopleT_id as apprkey,d.Member_id as apprid,d.FirstName as apprfn,d.LastName as apprln				
				FROM Applications AS a 
				LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) 
				LEFT JOIN PeopleT AS c ON (c.PeopleT_id=a.applHV_fk_PeopleT)
				LEFT JOIN PeopleT AS d ON (d.PeopleT_id=a.applAppr_fk_PeopleT) 				 				
				WHERE a.Deleted <> 'Y' $where";
	if ($stmt = $mysqli->query($prep_stmt)) {
		Outputcsv($stmt,$s);
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

function Outputcsv($rs,$s) {
	$hdr = array('No.',
				'Status',
				'Desire',
				'Qty',
				'Applicant',
				'City',
				'St',
				'Zip',
				'Phone',
				'Cell',
				'Submitted',
				'Approved',
				'HomeVisit',
				'Alone',
				'Mix',
				'Sex',
				'Low',
				'High',				
				'Pets',
				'Kids',
				'Special');
	// output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	if ($s=='a') {
		header('Content-Disposition: attachment; filename=SAGRRFamiliesActive.csv');
	} else {
		header('Content-Disposition: attachment; filename=SAGRRFamilies.csv');
	}
	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// output the column headings
	fputcsv($output, $hdr);
	while ($rcd = $rs->fetch_assoc()) {
		$contact = json_decode($rcd['applContact']);
		$source = json_decode($rcd['applSource']);
		$sec0 = json_decode($rcd['ApplSec0']);
		$sec3 = json_decode($rcd['ApplSec3']);
		$sec4 = json_decode($rcd['ApplSec4']);
		if (strpos($contact->{'FName'},'FirstName')!==false) {
			continue;
		}						
		$addr = $contact->{'Addr'}==''?'{Address}':$contact->{'Addr'}.', '.$contact->{'City'}.', '.$contact->{'St'}.' '.$contact->{'Zip'};
		$fa = '';
		if ($rcd['applFost']=='Y' & $rcd['applAdopt']=='Y') {
			$fa = 'Both';
		} else {
			if ($rcd['applFost']=='Y') {
				$fa = 'Foster';
			}
			if ($rcd['applAdopt']=='Y') {
				$fa = 'Adopt';
			}
		}
		$spec = $sec3->{'Ans27'}=='Y' || $sec3->{'Ans27'}=='M' ?'Minor ':'';
		$spec = $spec.($sec3->{'Ans28'}=='Y' || $sec3->{'Ans28'}=='M' ?'Major ':'');
		$spec = $spec.($sec3->{'Ans29'}=='Y' || $sec3->{'Ans29'}=='M' ?'Treatable ':'');
		$spec = $spec.($sec3->{'Ans30'}=='Y' || $sec3->{'Ans30'}=='M' ?'Cancer ':'');
		$spec = $spec.($sec3->{'Ans31'}=='Y' || $sec3->{'Ans31'}=='M' ?'xTrained ':'');
		$kids = '';
		foreach ($sec0->{'MemAge'} as $nm) {
			if ($nm<18) {
				$kids = $kids.$nm.' ';
			}
		}
		$pets = '';
		foreach ($sec0->{'PetType'} as $type) {
			if ($type<>'' AND $type<>'0') {
					$pets = $pets.$type.' ';
			}
		}

		$csv = array($rcd['applid'],
								$rcd['applStatus'],
								$fa,
								$sec3->{'Ans31a'},
								$contact->{'LName'}==''?'':$contact->{'LName'}.', '.$contact->{'FName'},
								$contact->{'City'},
								$contact->{'St'},
								$contact->{'Zip'},
								$contact->{'Phone'},
								$contact->{'Cell'},
								$rcd['applDateTime'],
								$rcd['applApproveDate'],
								$rcd['hvln']==''?'':$rcd['hvln'].', '.$rcd['hvfn'],
								$sec4->{'Ans33'},
								'mix?',
								$sec3->{'Ans23'},
								$sec3->{'Ans24'}[1]==''?'':$sec3->{'Ans24'}[0],
								$sec3->{'Ans24'}[1],
								$pets,
								$kids,
								$spec);
		fputcsv($output, $csv);
	}
}
?>
