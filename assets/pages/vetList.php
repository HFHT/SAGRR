<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$error = false;
$row = array();
$s= $_GET['s'];
if (isset($_GET['l'])) {
	$prep_stmt = "SELECT SelId,SelText,SelDesc,vetClinic,vetAddress,vetPhone,vetEmergencyPhone,vetContact FROM VetClinicT ORDER BY vetClinic";
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
<div data-role="page" id="vetList">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<?php switch($s) { 
					case 'm':
				?>
					<li><a href="#home" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
				<?php break;
					default:
				?>
					<li class="backBtn"><a href="#" data-icon="back" >Back</a></li>	
				<?php } ?>
				<li><a href="#home" data-icon="home">Home</a></li>
				<li class="addVet" id="addVet" style="display:none"><a href="#" data-icon="plus" class="myNav">Add Clinic</a></li>				
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
				<p>Could not access the Veterinary Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="vet_list" data-autodividers="false" data-filter="true">
			<?php
				foreach ($row as $rcd) {
				?>
					<li>
					<a href="/assets/pages/vet.php?x=<?php echo $rcd['SelId']; ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" style="padding-bottom:0.7em;padding-top:0" data-transition="slide">
					<h3><?php echo $rcd['vetClinic']; ?></h3>
					<p>Phone: <?php echo $rcd['vetPhone']; ?>
					<br><?php echo $rcd['vetAddress']; ?>
					</p></a></li>						
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