<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

$error = false;
$row = array();
if (isset($_GET['x'])) {
	$id = $_GET['x'];
	if ($_GET['m']=='appl') {
		$where = "fk_applid=$id";
	} else {
		if ($_GET['m']=='dog') {
			$where = "fk_DogT_id=$id";
		} else {
			$where = "fk_PeopleT_id=$id";
		}
	}
	$prep_stmt = "SELECT * FROM ContactLog WHERE $where ORDER BY logDate DESC";
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
<div data-role="page" id="memContact">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
				<li><a href="#home" data-icon="home">Home</a></li>	
				<li class="MemE" style="display:none"><a href="/assets/pages/memContactEdit.php?v=0&x=<?php echo $id ?>&m=<?php echo $_GET['m'];?>&n=<?php echo $_GET['n'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-icon="plus">Add Contact</a></li>
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
				<p>Could not access the Contacts Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="memcontactlist" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Contact Log for <?php echo $_GET['n'] ?></li>
			<?php
				foreach ($row as $rcd) {
				?>
					<li><a href="/assets/pages/memContactEdit.php?v=<?php echo $rcd['logid'] ?>&x=<?php echo $id ?>&n=<?php echo $_GET['n'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >
						<h3>Date of Contact: <?php echo $rcd['logDate'] ?></h3>
						<p>Log Entry by: <?php echo $rcd['logBy'];?></p>
						<?php if ($rcd['logUpdateBy']<>'') { ?>
							<p>Updated by: <?php echo $rcd['logUpdateBy'];?></p>
						<?php } ?>
						<p style="padding: 0px 8px 8px 0px;white-space:pre-line;"><?php echo $rcd['logText'] ?></p>							
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