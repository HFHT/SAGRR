<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$arySQL = array("all"=>"",
				"vx"=>"(DATEDIFF(Rabies, NOW()) < 0 OR DATEDIFF(DA2PP_DHLPP, NOW()) <0 OR DATEDIFF(Bordetella, NOW()) < 0 OR DATEDIFF(Leptospirosis, NOW()) < 0) AND",
				"ve"=>"(DATEDIFF(Rabies, NOW()) BETWEEN 1 AND {q} OR DATEDIFF(DA2PP_DHLPP, NOW()) BETWEEN 1 AND {q} OR DATEDIFF(Bordetella, NOW()) BETWEEN 1 AND {q} OR DATEDIFF(Leptospirosis, NOW()) BETWEEN 1 AND {q}) AND",
				"cm"=>"DogCurMemNo='{q}' AND",
				"ps"=>"DogProcStatus='{q}' AND",
				"ai"=>"(NCI <> 'Y' AND Bridge <> 'Y') AND (DogProcStatus= 'Inprocess') AND (DogCurStatus<> 'Hold-Behavior' AND DogCurStatus<>'Hold-Medical' AND DogCurStatus<>'InTransit') AND",
				"af"=>"(NCI <> 'Y' AND Bridge <> 'Y') AND (DogProcStatus= 'Fostered') AND (DogCurStatus<> 'Hold-Behavior' AND DogCurStatus<>'Hold-Medical' AND DogCurStatus<>'InTransit') AND",
				"at"=>"(NCI <> 'Y' AND Bridge <> 'Y') AND (DogProcStatus= 'Match-Trial') AND (DogCurStatus<> 'Hold-Behavior' AND DogCurStatus<>'Hold-Medical' AND DogCurStatus<>'InTransit') AND",
				"cs"=>"DogCurStatus='{q}' AND",
				"ok"=>"fk_applid={q} AND",
				"ic"=>"b.Deleted='X' AND"
				);
$aryMenu = array("f"=>'<li><a href="#fupMenu" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>',
				 "a"=>'<li><a href="#dogActiveMenu" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>',
				 "d"=>'<li><a href="#dogMenu" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>',
				 "b"=>'<li class="backBtn"><a href="#" data-icon="back" >Back</a></li>');
				
$error = false;
$row = array();
$s= (is_null($_GET['s'])) ? 'd' : $_GET['s'];
if (isset($_GET['l'])) {
	$where = str_replace('{q}',$_GET['q'],$arySQL[$_GET['l']]);
	$prep_stmt = "SELECT DogT_id,SAGRR_id,DogProcStatus,DogCurStatus,DogCurMember,DogCurMemNo,DogName,DogPhotoLink,DogSex,a.fk_PeopleT_id,InTake_fk_PeopleT,fk_ColorT_id,d.SelText as color,b.applContact,b.applid FROM DogT AS a LEFT JOIN Applications AS b ON b.applid=a.fk_applid LEFT JOIN ColorT AS d ON d.SelId=a.fk_ColorT_id WHERE $where a.Deleted <> 'Y' ORDER BY DogName";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);		
	} else {
        $error = true;;
    }
} else {
	$error = true;;
}
function OutputName($s,$j,$k,$m) {
	$contact = json_decode($j);
	echo ($s=='Adopted') ? 'Adopter: ' : (($s=='Fostered') ? 'Foster: ' : 'Trial: ');
	if ($m!=0) {
		echo '<i>Member</i>&nbsp;&nbsp;#:&nbsp;'.$m;
	} else {
		echo $contact->{'LName'}.', '.$contact->{'FName'};
		echo '&nbsp;&nbsp;#:&nbsp;'.$k;
	}
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
//var_dump($where);
?>
</head>
<body>
<div data-role="page" id="dogList">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<?php 
					echo $aryMenu[$s];
				?>
				<li><a href="#home" data-icon="home">Home</a></li>
				<?php if ($_GET['a']=='n') { ?>
					<li>&nbsp;</li>
				<?php } else { ?>
					<li class="" id="addDog" style="display:none"><a href="#" data-icon="plus" class="myNav">Add Dog</a></li>
				<?php } ?>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content">
	<?php
	if ($error) {
	?>
		<div class="ui-corner-all">
			<div class="ui-bar ui-bar-a">
				<h3>Error Occurred</h3>
			</div>
			<div class="ui-body ui-body-a">
				<p>Could not access the Dog Database, try again later </p>
				<p><?php echo $mysqli->error;?><br>
					<?php echo $prep_stmt;?></p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="doglist" data-autodividers="false" data-filter="true" style="">
			<?php
				if ($stmt->num_rows == 0) { 
					$ary_msgs = array("No Dogs matching search criteria.","No Dogs assigned to Applicant.","No Dogs assigned to Member."); ?>
					<li><p style="font-size: 1em;font-style: italic;"><?php echo $ary_msgs[$_GET['x']];?></p></li>
				<?php
				} else {
				foreach ($row as $rcd) {
				?>
					<li style="padding-bottom:0.1em;padding-top:0.2em">
					<a href="/assets/pages/dog.php?x=<?php echo $rcd['DogT_id']; ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>&s=<?php echo $_GET['s']; ?>" style="padding-bottom:0;padding-top:0" data-transition="slide">
					<img src="<?php echo $rcd['DogPhotoLink']; ?>"/>
					<h3><?php echo $rcd['DogName']; ?>&nbsp;&nbsp; #: <?php echo $rcd['SAGRR_id']; ?></h3><p>
					<?php if ($rcd['DogProcStatus']=='Adopted' || $rcd['DogProcStatus']=='Fostered' || $rcd['DogProcStatus']=='Match-Trial') { 
						OutputName($rcd['DogProcStatus'],$rcd['applContact'],$rcd['applid'],$rcd['fk_PeopleT_id']);
					} else {
						if (!($rcd['DogCurStatus']=='NCI' || $rcd['DogCurStatus']=='Bridge')) {
						?>					
							Responsible: <i>Member</i>&nbsp;#:&nbsp;<?php echo $rcd['InTake_fk_PeopleT'];?>
						<?php } 
					}?>
					<br>Status: <?php echo $rcd['DogProcStatus']; ?> : <?php echo $rcd['DogCurStatus']; ?>
					<br>Sex: <?php echo $rcd['DogSex']; ?>&nbsp;&nbsp;Color: <?php echo $rcd['fk_ColorT_id']=='0' ? '' : $rcd['color']; ?>
					</p></a></li>						
			<?php	
				}
				}
			?>	
		</ul>
	<?php
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>