<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = array();
$s= $_GET['s'];
if (isset($_GET['l'])) {
	$prep_stmt = "SELECT DogS_id,DogS_Name,DogS_Contact,DogS_Phone,DogS_Addr,DogS_Email FROM DogSources ORDER BY DogS_Name";
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
<div data-role="page" id="dogSourceList">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li><a href="#dogMenu" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
				<li><a href="#home" data-icon="home">Home</a></li>
				<li class="addSource" id="addSource"><a href="#" data-icon="plus" class="myNav">Add Source</a></li>				
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
		<ul data-role="listview" id="dogSource_list" data-autodividers="false" data-filter="true">
			<?php
				foreach ($row as $rcd) {
				?>
					<li>
					<a href="/assets/pages/dogSource.php?x=<?php echo $rcd['DogS_id']; ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" style="padding-bottom:0.7em;padding-top:0" data-transition="slide">
					<h3><?php echo $rcd['DogS_Name']; ?></h3>
					<p>Contact: <?php echo $rcd['DogS_Contact']; ?>
					<br>Email: <?php echo $rcd['DogS_Email']; ?>
					<br>Phone: <?php echo $rcd['DogS_Phone']; ?>
					<br><?php echo $rcd['DogS_Addr']; ?>
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