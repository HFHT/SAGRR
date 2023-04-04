<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$hardcode_span = -305;
$error = false;
$row = array();
if (isset($_GET['x'])) {
	$id = $_GET['x'];
    $prep_stmt = "SELECT * FROM VetClinicT WHERE SelId = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$prep_stmt = "SELECT Count(*) AS Qty FROM VetVisit WHERE fk_VetClinicT = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$visitCnt = $stmt->fetch_row();
		}
		$prep_stmt = "SELECT Count(*) AS Qty FROM FileAsset WHERE Fk_Vet_id = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$mediaCnt = $stmt->fetch_row();
		}		
	} else {
        $error = true;;
    }
} else {
	$error = true;;
}
function ckExpire($d1,$span) {
	return (($d1-$span<0));
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
?>
</head>
<body>
<div data-role="page" id="vetPage">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back">Back</a></li>	
				<li><a href="#home" data-icon="home">Home</a></li>	
				<li style="display:none"><a href="#" data-icon="gear">xxx</a></li>
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
				<p>Could not access the Veterinay Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="vet_info" data-autodividers="false" data-filter="false">
			<li data-icon="edit"><a href="/assets/pages/vetInfo.php?x=<?php echo $id ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >			
				<table style="border-spacing:0;width:100%;font-weight:400;"><tbody>
					<tr><td style="font-weight:600"><?php echo $row[0]['vetClinic'];?></td></tr>
					<tr><td>Phone: <?php echo $row[0]['vetPhone'];?></td></tr>
					<tr><td>Emergency: <?php echo $row[0]['vetEmergencyPhone'];?></td></tr>
					<tr><td>Contact(s): <?php echo $row[0]['vetContact'];?></td></tr>
					<tr><td><?php echo $row[0]['vetAddress'];?></td></tr>
					
				</tbody></table>
			</a></li>
			<li><a href="/assets/pages/vetVisits.php?x=<?php echo $id ?>&n=<?php echo $row[0]['SelText'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >	
				Office Visits<span class="ui-li-count"><?php echo $visitCnt[0]; ?></span></a>
			</li>
			<!--li><a href="/assets/pages/mediaList.php?x=0&v=<?php echo $id;?>&r=0&d=<?php echo $id;?>&s=s&m=vet" data-transition="slide"> Attachments <span class="ui-li-count"><?php echo $mediaCnt[0];?></span></a></li-->						

			<!-- javascript fills in the contents of this list on pagebeforeshow-->
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