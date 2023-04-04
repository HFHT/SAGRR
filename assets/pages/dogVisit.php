<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = '';
if (isset($_GET['x'])) {
	$id = $_GET['x'];
	if ($_GET['l'] == 'pend') {
		$where = "(DATEDIFF(VetFollowup, NOW()) BETWEEN 1 AND 59) AND VetFollowupComp='N' ";
	} else {
		if ($_GET['l'] == 'miss') {
			$where = "(DATEDIFF(VetFollowup, NOW()) <0) AND VetFollowupComp='N' ";
		} else {
			$where = "a.fkV_DogT_id=$id";
		}
	}
	$prep_stmt = "SELECT a.VetVisit_id,a.fkV_DogT_id,a.fk_VetClinicT,c.DogName,c.SAGRR_id,
					DATE_FORMAT(a.VetDate,'%m/%d/%Y') AS VetDate, 
					DATE_FORMAT(a.VetFollowup,'%m/%d/%Y') AS VetFollowup,
					a.VetCost,a.VetInvoice,a.VetReason,a.VetResult,
					b.SelText AS Vet, b.SelDesc AS VetAddr
				 FROM VetVisit AS a LEFT JOIN VetClinicT AS b ON (b.SelId=a.fk_VetClinicT) LEFT JOIN DogT as c ON c.DogT_id=a.fkV_DogT_id
				 WHERE $where AND a.Deleted <> 'Y' ORDER BY a.VetDate DESC";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);			
	} else {
        $error = true;;
    }
} else {
	$error = true;;
}

?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
?>
</head>
<body>
<div data-role="page" id="dogVisit">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<?php if ($_GET['s']=='f') { ?>
					<li><a href="#fupMenu" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>													
				<?php } else { ?>
					<li class="backBtn"><a href="#" data-icon="back" class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
				<?php } ?>
				<li><a href="#home" data-icon="home">Home</a></li>	
				<?php if ($_GET['a']=='n') { ?>
					<li>&nbsp;</li>
				<?php } else { ?>				
					<li class="DogE" style="display:none"><a href="/assets/pages/dogVisitEdit.php?v=0&x=<?php echo $id; ?>&n=<?php echo $_GET['n'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-icon="plus">Add Visit</a></li>
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
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="dogvisitlist" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Veterinarian Visits </li>
			<?php
				foreach ($row as $rcd) {
				?>
					<li><a href="/assets/pages/dogVisitEdit.php?v=<?php echo $rcd['VetVisit_id'] ?>&x=<?php echo $rcd['fkV_DogT_id'] ?>&n=<?php echo $rcd['DogName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >
						<h3><?php echo $rcd['DogName'] ?>&nbsp;&nbsp;#:<?php echo $rcd['SAGRR_id'] ?></h3>
						<h3>Visit Date: <?php echo $rcd['VetDate'] ?></h3>
						<h3>Clinic: <?php echo $rcd['Vet'] ?></h3>
						<p>Invoice: <?php echo $rcd['VetInvoice'] ?>, Cost: <?php echo $rcd['VetCost'] ?>, Followup: <?php echo $rcd['VetFollowup'] ?><br>
						Reason for Visit: <?php echo $rcd['VetReason'] ?><br>
						Result of Visit: <?php echo $rcd['VetResult'] ?></p>							
					</a>
					</li>					
			<?php	
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