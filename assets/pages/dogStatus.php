<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = array();
if (isset($_GET['x'])) {
	$id = $_GET['x'];
	$prep_stmt = "SELECT a.DStatusTrackT_id, a.StatusComment, b.SelText AS DStatus, c.SelText AS Location, d.FirstName, d.LastName,	
				a.DogProcStatus, a.DogCurStatus, a.NCI, a.Bridge, a.createBy,
				DATE_FORMAT(a.StatusDate,'%m/%d/%Y') AS StatusDate 				
				FROM DStatusTrackT as a 
				LEFT JOIN DStatusT as b ON b.SelId=a.fk_DStatusT_id
				LEFT JOIN LocationT as c ON c.SelId=a.fk_Location_id
				LEFT JOIN PeopleT as d ON d.PeopleT_id=a.fk_PeopleT_id
				WHERE a.fk_DogT_id =".$id." AND a.Deleted <> 'Y' ORDER BY a.StatusDate DESC, a.DStatusTrackT_id DESC";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);			
	} else {
        $error = true;;
    }
} else {
	$error = true;;
}
function whichClass($b,$n) {
	if ($b=='Y') {
		echo 'dogBridged';
	} else {
		if ($n=='Y') {
			echo 'dogNCI';
		} else {
			echo '';
		}
	}
}

?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
var_dump($prep_stmt);
?>
</head>
<body>
<div data-role="page" id="dogStatus">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back"  class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
				<li><a href="#home" data-icon="home">Home</a></li>	
				<li class="DogE" style="display:none"><a href="/assets/pages/dogStatusEdit.php?v=0&x=<?php echo $id ?>&n=<?php echo $_GET['n'] ?>&a=y&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-icon="plus">New Status</a></li>
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
		<ul data-role="listview" id="dogstatuslist" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Status History for <?php echo $_GET['n'] ?></li>
			<?php
				$latestStatus = "y";
				foreach ($row as $rcd) {
				?>
				<li class="<?php whichClass($rcd['Bridge'],$rcd['NCI']);?>"><a href="/assets/pages/dogStatusEdit.php?v=<?php echo $rcd['DStatusTrackT_id'] ?>&x=<?php echo $id ?>&n=<?php echo $_GET['n'] ?>&m=<?php echo $rcd['memSelected'] ?>&a=<?php echo $latestStatus ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;"  >
						<h3>Date: <?php echo $rcd['StatusDate'] ?></h3>
						<?php if ($rcd['Bridge']=='Y') { ?>
							<h3>Passed to Bridge</h3>
							<p>Comment: <?php echo $rcd['StatusComment'] ?></p>
						<?php } else { 
							if ($rcd['NCI']=='Y') {?>
								<h3>Not Coming In</h3>
								<p>Comment: <?php echo $rcd['StatusComment'] ?></p>
							<? } else { ?>
								<h3><?php echo $rcd['DogProcStatus'].": ".$rcd['DogCurStatus'] ?></h3>
							<?php if ($rcd['LastName']=='') { ?>
								<p>Made By: <?php echo $rcd['createBy'];?><br>
							<?php } else { ?>
								<p>Responsible: <?php echo $rcd['LastName'].', '.$rcd['FirstName'] ?><br>
							<?php } ?>
								Comment: <?php echo $rcd['StatusComment'] ?></p>
						<?php }
						}?>
					</a>
					</li>					
			<?php
				$latestStatus = "n";
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