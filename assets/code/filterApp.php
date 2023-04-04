<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
error_reporting(E_ERROR | E_PARSE);
$error = false;
$row = array();
$objErr = new StdClass();
$arySex = [];
$arySexVals = 	array("No Preference"=>"Either","Female"=>"Female","Prefer Female"=>"Either","0"=>"Either","Male"=>"Male","Prefer Male"=>"Either","Female Prefer"=>"Either","Male Prefer"=>"Either");
$aryFAVals = array("A"=>"Adopt","F"=>"Foster","AF"=>"Adopt or Foster");
$aryStatusQry = array("P"=>"applStatus IN ( 'New','HomeVisit', 'Decision' )","W"=>"applStatus IN ( 'WaitList','ShortList' )","A"=>"");

$aryKids = array("3"=>array(3,100),"5"=>array(6,100),"6"=>array(18,100));
//$aryKids = array(array("0",0,17),array("3",0,17),array("5",4,17),array("6",7,17));
$objFilter = $_POST;
$templateo =	'<li class="appfilter" style="padding-bottom:0.1em;padding-top:0.2em"><a href="/assets/pages/adopt.php?x={applid}&l=&q=" style="padding:0.5em 1em;" data-transition="slide">'.
			'<h3 style="margin-bottom:0;">{LName}, {FName}&nbsp; #: {applid}</h3>'.
			'<p style="margin-top:0;">Submitted: {applDateTime}&nbsp;&nbsp;State: {applState}&nbsp;&nbsp;Status: {applStatus}</p>'.
			'<p>{addr}'.
			'<br>Email: {Email}'.
			'<br>Phone: {Phone}</p>'.					
			'</a></li>';
$template =	'<li class="appfilter" style="padding-bottom:0.1em;padding-top:0.2em"><a href="/assets/pages/adopt.php?x={applid}&l=&q=" style="padding:0.5em 1em;" data-transition="slide">'.
			'<h3 style="margin-bottom:0;">{LName}, {FName}&nbsp; #: {applid}</h3>'.
			'<p style="margin-top:0;">Submitted: {applDateTime}&nbsp;&nbsp;State: {applState}&nbsp;&nbsp;Status: {applStatus}</p>'.
			'<p>Type: {af} Sex: {Sex} Dog Age: {min}->{max}'.
			'<p>Children: {kids} Dogs: {dogs} Cats: {cats} Hours: {hours}'.
			'<p>Health Minor: {minor}  Major: {major}   Special: {spec}  Housebroken: {hb}  Behavioral: {bi}'.
			'<p>Dog Door: {door}   Pool: {pool}   Fence:  {fence}'.
			'<br>Email: {Email}'.
			'<br>Phone: {Phone}'.
			'<br>{debug}</p>'.					
			'</a></li>';			

if ($objFilter[val]=='x') {
//	$where = "applStatus IN ('WaitList','ShortList') AND applState='Active' AND";	
	if ($objFilter[Status]!='') {
		$where = $aryStatusQry[$objFilter[Status]].' AND ';
	} else {
		$where = "";
	}
	$prep_stmt = "SELECT applid,applDocId,applContact,ApplSec0,ApplSec1,ApplSec3,ApplSec4,applStatus,applState,c.FirstName as hvfn,c.LastName as hvln,DATE_FORMAT(applDateTime,'%m/%d/%Y') AS applDateTime FROM Applications AS a LEFT JOIN PeopleT AS c ON (c.PeopleT_id=a.applHV_fk_PeopleT) WHERE $where a.Deleted <> 'Y' ORDER BY applid DESC";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$htmlFull = '';
		$Qty = 0;
		foreach ($row as $rcd) {
			$contact = json_decode($rcd[applContact]);
			$ApplSec0 = json_decode($rcd[ApplSec0]); /* Memage[], PetType[], PetHome[]=Yes */
			$ApplSec1 = json_decode($rcd[ApplSec1]); /* Ans9 Doggie Door, Ans10 Pool, Ans12 Fence */
			$ApplSec3 = json_decode($rcd[ApplSec3]); /* Ans23a [A,F], Ans23 Sex, Ans24 [low, hi] Age, Ans27 Minor, Ans28 Major, Ans29 OR Ans30 Special, Ans31 */
			$ApplSec4 = json_decode($rcd[ApplSec4]); /* Ans33 Hours */
			$fa = implode($ApplSec3->{Ans23a})=='' ? 'AF' : implode($ApplSec3->{Ans23a});
//			if ($objFilter[des]!='' AND $objFilter[des]!=$fa) {continue;}
			if ($objFilter[des]!='' AND !faSel($fa,$objFilter[des])) {continue;}
			$fsex = $ApplSec3->{Ans23}=='' ? "Either" : $arySexVals[$ApplSec3->{Ans23}];
//			if (isset($objFilter[sex]) AND $objFilter[sex]!=$fsex) {continue;}	
//			if ($objFilter[sex]!='' AND $objFilter[sex]!=$fsex) {continue;}	
			if ($objFilter[sex]!='' AND !sexSel($fsex,$objFilter[sex])) {continue;}
			$pCnt = 0;
			$kidlist = '';
			if ($objFilter[child]!='' AND !kidSel($ApplSec0->{MemAge},$objFilter[child])) {continue;}				
			$pCnt = 0;
			if ($objFilter[dogs]!='' AND !petSel($ApplSec0->{PetType},$ApplSec0->{PetHome},$objFilter[dogs],'Dog')) {continue;}	
			$dogCnt = $pCnt;
			$pCnt = 0;
			if ($objFilter[cats]!='' AND !petSel($ApplSec0->{PetType},$ApplSec0->{PetHome},$objFilter[cats],'Cat')) {continue;}		
			$catCnt = $pCnt;
			if ($objFilter[hours]!='' AND !hoursSel($ApplSec4->{Ans33},$objFilter[hours])) {continue;}
			if ($objFilter[healthminor]!='' AND !medSel($ApplSec3->{Ans27},$objFilter[healthminor])) {continue;}
			if ($objFilter[healthmajor]!='' AND !medSel($ApplSec3->{Ans28},$objFilter[healthmajor])) {continue;}
			if ($objFilter[spec]!='' AND !medSel($ApplSec3->{Ans29},$objFilter[spec])) {continue;}
			if ($objFilter[hb]!='' AND !medSel($ApplSec3->{Ans31},$objFilter[hb])) {continue;}
			if ($objFilter[bi]!='' AND !medSel($ApplSec3->{Ans114},$objFilter[bi])) {continue;}
			if ($objFilter[age1]!='' AND !ageSel($ApplSec3->{Ans24},$objFilter[age1],$objFilter[age2])) {continue;}
			if ($objFilter[door]!='' AND !YNSel($ApplSec1->{Ans9},$objFilter[door])) {continue;}
			if ($objFilter[pool]!='' AND !YNSel($ApplSec1->{Ans10},$objFilter[pool])) {continue;}
			if ($objFilter[fence]!='' AND !YNSel($ApplSec1->{Ans12},$objFilter[fence])) {continue;}
			$fhours[$ApplSec11->{Ans9}]='y';	
//			$datas = 'data-Des="'. implode($ApplSec3->{Ans23a}).'" data-Sex="'.$ApplSec3->{Ans23}.'" data-DAL="'.$ApplSec3->{Ans24}[0].'" datat-DAH="'.$ApplSec3->{Ans24}[1].'"';
			$addr = $contact->{Addr}==''?'{Address}':$contact->{Addr}.', '.$contact->{City}.', '.$contact->{St}.' '.$contact->{Zip};					
//			$html = str_replace('{datas)',datas,$template);
			$html = str_replace('{applid}',$rcd[applid],$template);
			$html = str_replace('{LName}',$contact->{LName},$html);
			$html = str_replace('{FName}',$contact->{FName},$html);
			$html = str_replace('{applDateTime}',$rcd[applDateTime],$html);
			$html = str_replace('{applState}',$rcd[applState],$html);
			$html = str_replace('{applStatus}',$rcd[applStatus],$html);
			$html = str_replace('{addr}',$addr,$html);
			$html = str_replace('{Email}',$contact->{Email},$html);
			$html = str_replace('{Phone}',$contact->{Phone},$html);
			$html = str_replace('{af}',$aryFAVals[$fa],$html);
			$html = str_replace('{Sex}',$fsex,$html);
			$html = str_replace('{kids}',$kidlist,$html);
			$html = str_replace('{dogs}',$dogCnt,$html);
			$html = str_replace('{cats}',$catCnt,$html);
			$html = str_replace('{hours}',$ApplSec4->{Ans33},$html);
			$html = str_replace('{minor}',$ApplSec3->{Ans27},$html);
			$html = str_replace('{major}',$ApplSec3->{Ans28},$html);
			$html = str_replace('{spec}',$ApplSec3->{Ans29},$html);
			$html = str_replace('{hb}',$ApplSec3->{Ans31},$html);
			$html = str_replace('{bi}',$ApplSec3->{Ans114},$html);
			$html = str_replace('{door}',$ApplSec1->{Ans9},$html);
			$html = str_replace('{pool}',$ApplSec1->{Ans10},$html);
			$html = str_replace('{fence}',$ApplSec1->{Ans12},$html);
			$html = str_replace('{min}',$ApplSec3->{Ans24}[0],$html);
			$html = str_replace('{max}',$ApplSec3->{Ans24}[1],$html);
			$html = str_replace('{debug}',implode(', ',$ApplSec0->{MemAge}),$html);
			
			$htmlFull = $htmlFull.$html;
			$Qty++;
			
		}
	} else {
		$objErr->error_str = $mysqli->error;
		$objErr->error_sql = $prep_stmt;
		$objErr->error_msg = 'Could not access database, try again later.';		
    }
} else {
	$objErr->error_msg = 'Key was not provided';
	$htmlFull = $objFilter[val];
}

$results = array(
'error' => (! empty($objErr->error_msg)),
'errorDetail' => $objErr,
'sex' => $fhours,
'Qty' => $Qty,
'html' =>  $htmlFull);
echo json_encode($results);
$mysqli->close();

function faSel($Sel,$Fil) { // if Filter is A then accept A or AF, if filter is F then accept F or AF 
	if ($Fil=='A') {
		if ($Sel == 'A' OR $Sel == 'AF') {return true;}
	}
	if ($Fil=='F') {
		if ($Sel == 'F' OR $Sel == 'AF') {return true;}
	}
	if ($Fil=='AF' AND $Sel=='AF') {return true;}
	return false;
}
function sexSel($Sel,$Fil) { // if Filter is Male then accept Male or Either, if filter is Female then accept Female or Either 
	if ($Fil=='Male') {
		if ($Sel == 'Male' OR $Sel == 'Either') {return true;}
	}
	if ($Fil=='Female') {
		if ($Sel == 'Female' OR $Sel == 'Either') {return true;}
	}
	if ($Fil=='Either' AND $Sel=='Either') {return true;}
	return false;
}
function petSel($aryType,$aryHome,$Fil,$Typ) {
	global $pCnt;
	$pCnt = 0;
	for ($i = 0; $i < count($aryType); $i++) {
		if (($aryHome[$i]=='Yes' OR $aryHome[$i]=='') AND $aryType[$i]==$Typ) {$pCnt++;}
	}	
	if ($Fil=="0" AND $pCnt == 0) {return true;}
	if ($Fil=="1" AND $pCnt == 1) {return true;}
	if ($Fil=="2" AND $pCnt > 1) {return true;}
	return false;
}
function hoursSel($Sel,$Fil) { 
	$aryHours = array("0-4"=>0,"4-6"=>4,"6-8"=>6,"8-10"=>8,"10+"=>10);
//	echo $Sel.':'.$Fil.':'.$aryHours[$Sel].'<br>';
	if ($Sel=='') {return true;}
//	if ($aryHours[$Sel]>=$Fil) {return true;}
	if ($aryHours[$Sel]<=$Fil) {return true;}
	return false;
}
function medSel($Sel,$Fil) { 
	if ($Sel=='') {$Sel='M';}
	if ($Sel==$Fil) {return true;}
	return false;
}
function ageSel($Sel,$FMin, $FMax) { 
	if ($FMin=='') {$Fmin=0;}
	if ($Fmax=='') {$Fmax=20;}
	if ($Sel[0]=='') {$Sel[0]=0;}
	if ($Sel[1]=='') {$Sel[1]=20;}
	if ($Sel[0]>=$FMin AND $FMax>=$Sel[1]) {return true;}
	return false;
}
function YNSel($Sel,$Fil) { 
	if ($Sel=='') {$Sel='N';}
	if ($Sel==$Fil) {return true;}
	return false;
}
function kidSel($aryAges,$Fil) {
	global $pCnt,$kidlist;
	$kidlist = '';
	$pCnt = 0;
	$fret = true;
	for ($i = 0; $i < count($aryAges); $i++) {
		if ($aryAges[$i]=='') {continue;}
//		echo $aryAges[$i].'='.$Fil;
		if ((int) $aryAges[$i]< 18) {$kidlist = $kidlist.$aryAges[$i].' ';}
		if ((int) $aryAges[$i] >= $Fil) {$pCnt++;} else {$fret = false;}
	}
	return ($fret);
}
/*
		if (($aryHome[$i]=='Yes' OR $aryHome[$i]=='') AND $aryType[$i]==$Typ) {$pCnt++;}
	}	
	if ($Fil=="0" AND $pCnt == 0) {return true;}
	if ($Fil=="1" AND $pCnt == 1) {return true;}
	if ($Fil=="2" AND $pCnt > 1) {return true;}
	return false;
}

			$fkids = $ApplSec0->{MemAge};
			$kidlist = '';
			if ($objFilter[child]!='' AND $objFilter[child]!='A') {
				$range = $aryKids[$objFilter[child]];
				$kidCnt = $kidx = 0;
				$kidlist = '';
				foreach ($fkids as $kids) {
					if ($kids!='') {
						if ((int) $kids < 18) {$kidlist = $kidlist.$kids.' ';}
						if (($range[0] <= (int) $kids) AND ((int) $kids <= $range[1])) {
							$kidCnt++;
						} else {
							$kidx++;
						}
					}
				}
				if ($kidx>0) {continue 1;}
			}
			$kidlist = $kidlist=='' ? 'None' : '['.$kidlist.']';
*/



?>