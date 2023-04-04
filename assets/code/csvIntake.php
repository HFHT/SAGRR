<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$hardcode_span = 60;
$error_msg = '';
$error_str = '';
$error_sql = '';
$today = date("Y-m-d");
if (isset($_POST['x']) || isset($_GET['x'])) {
    $prep_stmt = "SELECT DogT_id,SAGRR_id,fk_DStatusTrackT_id,DogName,FormerName,Age_YY,Age_MM,Weight,AKC_id,DMChipData,DMChipMfg_id,".
					"fk_BreedT_id,a.fk_PeopleT_id,fk_ColorT_id,DogSex,AlteredBy,DateLastChanged,fk_SourceT_id,DogBlob,DogProcStatus,".
					"DogCurStatus,DogPhotoLink,DogCurMember,Behaviors,fk_applid,Medical,".
					"DATE_FORMAT(InTakeDate,'%m/%d/%Y') AS InTakeDate,".
					"DATE_FORMAT(Birthdate,'%m/%d/%Y') AS BirthDate,". 
					"DATE_FORMAT(DeathDate,'%m/%d/%Y') AS DeathDate,".
					"DATE_FORMAT(AlteredDate,'%m/%d/%Y') AS AlteredDate,".
					"DATEDIFF(Rabies, NOW()) AS Rabies,".
					"DATEDIFF(DA2PP_DHLPP, NOW()) AS DA2PP_DHLPP,".
					"DATEDIFF(Bordetella, NOW()) AS Bordetella,".
					"(PERIOD_DIFF( DATE_FORMAT(NOW(), '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) DIV 12 AS ageY,".
					"(PERIOD_DIFF( DATE_FORMAT(NOW(), '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) MOD 12 AS ageM,".
					"(PERIOD_DIFF( DATE_FORMAT(DeathDate, '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) DIV 12 AS lifeY,".
					"(PERIOD_DIFF( DATE_FORMAT(DeathDate, '%Y%m') , DATE_FORMAT(Birthdate, '%Y%m') )) MOD 12 AS lifeM,".
					"b.DogS_Name, c.applContact, d.SelText as color, e.SelText as ChipMfg ".
					"FROM DogT AS a ".
					"LEFT JOIN DogSources AS b ON b.DogS_id=a.fk_SourceT_id ".
					"LEFT JOIN Applications AS c ON c.applid=a.fk_applid ".
					"LEFT JOIN ColorT AS d ON d.SelId=a.fk_ColorT_id ".
					"LEFT JOIN MChipMfg AS e ON e.SelId=a.DMChipMfg_id ".
					"WHERE a.Deleted <> 'Y'";
	if ($stmt = $mysqli->query($prep_stmt)) {
		Outputcsv($stmt);
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

function Outputcsv($rs) {
	$hdr = array('No.',
				'Id',
				'Intake',
				'Status',
				'Name',
				'Sex',
				'Age',
				'Source',
				'AKC',
				'Color',
				'Weight',
				'Microchip',
				'ChipMfg',
				'Altered',
				'Shots',
				'Medical',
				'Good w',
				'Family');
	// output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=SAGRRIntake.csv');

	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// output the column headings
	fputcsv($output, $hdr);
	while ($rcd = $rs->fetch_assoc()) {
		$vac = '';
		if (ckVaccine($rcd['Rabies'],$rcd['Bordetella'],$rcd['DA2PP_DHLPP'],0)) {
			$vac='Expired';
		} else {
			if (ckVaccine($rcd['Rabies'],$rcd['Bordetella'],$rcd['DA2PP_DHLPP'],$hardcode_span)) {
				$vac='Expiring';							
			}
		}
		$med = json_decode($rcd['Medical']);
		$beh = json_decode($rcd['Behaviors']);
		$b = in_array('19',$beh->{'Beh'})==true ? 'dog ':'';
		$b = $b.(in_array('20',$beh->{'Beh'})==true ? 'cat ':'');
		$b = $b.(in_array('21',$beh->{'Beh'})==true ? 'kid ':'');
		$applContact = !empty($rcd['applContact']) ? json_decode($rcd['applContact']) : '';
		$family = $applContact->{'LName'} == '' ? '' : $applContact->{'LName'}.', '.$applContact->{'FName'};					
		$csv = array($rcd['DogT_id'],
					$rcd['SAGRR_id'],
					$rcd['InTakeDate'],
					$rcd['DogProcStatus'].':'.$rcd['DogCurStatus'],
					$rcd['DogName'],
					$rcd['DogSex'],
					$rcd['AgeY'].':'.$rcd['AgeM'],
					$rcd['DogS_Name'],
					$rcd['AKC_id'],
					$rcd['color'],
					$rcd['Weight'],
					$rcd['DMChipData']."\t",
					$rcd['ChipMfg'],
					$rcd['AlteredBy'],
					$vac,
					$med->{'MedicalClass'},
					$b,
					$family);
		fputcsv($output, $csv);
	}
}
?>
