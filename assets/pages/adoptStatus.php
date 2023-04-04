<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$error = false;
$row = array();
if (isset($_GET['x'])) {
	$id = $_GET['x'];
	$prep_stmt = "SELECT a.DStatusTrackT_id, a.StatusComment, b.SelText AS DStatus, c.SelText AS Location, d.FirstName, d.LastName,	
				a.DogProcStatus, a.DogCurStatus, a.applProcStatus, a.applProcState, a.createBy, DATE_FORMAT(a.StatusDate,'%m/%d/%Y') AS StatusDate 				
				FROM DStatusTrackT as a 
				LEFT JOIN DStatusT as b ON b.SelId=a.fk_DStatusT_id
				LEFT JOIN LocationT as c ON c.SelId=a.fk_Location_id
				LEFT JOIN PeopleT as d ON d.PeopleT_id=a.fk_PeopleT_id
				WHERE a.fk_applid =".$id." AND a.Deleted <> 'Y' ORDER BY a.StatusDate DESC, a.DStatusTrackT_id DESC";
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
var_dump($prep_stmt);
?>
</head>
<body>
<div data-role="page" id="adoptStatus">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back"  class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
				<li><a href="#home" data-icon="home">Home</a></li>	
				<li class="ApplE" style="display:none"><a href="/assets/pages/adoptStatusEdit.php?v=0&x=<?php echo $id ?>&n=<?php echo $_GET['n'] ?>&a=y&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>&s=<?php echo $_GET['s']; ?>" data-icon="plus">New Status</a></li>
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
				<p>Could not access the Application Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="adoptstatuslist" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Status History for <?php echo $_GET['n'] ?></li>
			<?php
				$latestStatus = "y";
				foreach ($row as $rcd) {
				?>
				<li><a href="/assets/pages/adoptStatusEdit.php?v=<?php echo $rcd['DStatusTrackT_id'] ?>&x=<?php echo $id ?>&n=<?php echo $_GET['n'] ?>&m=<?php echo $rcd['memSelected'] ?>&a=<?php echo $latestStatus ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >
						<table><tbody>
						<tr>
						<td width="70%"><h3><?php echo $rcd['applProcState'].' &#187 '.$rcd['applProcStatus'];?></h3></td>
						<td width="30%"><h3><?php echo $rcd[StatusDate] ?></h3></td>
						</tr>
						</tbody></table>						
						<p style="margin:0">
						<?php if ($rcd['applProcStatus']=='HomeVisit') { ?>
						Home Visit Volunteer: <?php echo $rcd['LastName'].', '.$rcd['FirstName'] ?><br>
						<?php } ?>
						Comment: <?php echo $rcd['StatusComment'] ?><br>
						Made By: <?php echo $rcd['createBy'] ?>
						</p>							
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