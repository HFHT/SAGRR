<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$hardcode_span = -305;
$error = false;
$row = array();
if (isset($_GET['x'])) {
	$id = $_GET['x'];
    $prep_stmt = "SELECT a.applid,a.applDocId,a.applContact,a.applComplete,a.applApproveDate,a.applProgress,a.applFostAdopt,a.applSource,a.applStatus,
				b.PeopleT_id,b.Member_id,b.FirstName,b.LastName,DATE_FORMAT(a.applDateTime,'%m/%d/%Y') AS applDateTime
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applProgress = json_decode($row[0]['applProgress']);
		$contact = json_decode($row[0]['applContact']);
		$source = json_decode($row[0]['applSource']);			
	} else {
        $error = true;;
    }
} else {
	$error = true;;
}
function icon($choice) {
	echo $choice=='N' ? 'action' : 'check';
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
var_dump($applProgress);
?>
</head>
<body>
<div data-role="page" id="adoptAppl">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back">Back</a></li>	
				<li><a href="#home" data-icon="home">Home</a></li>	
				<li>&nbsp;</li>
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
				<p>Could not access the SAGRR Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="adopt_info" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3>Family: <?php echo $contact->{'LName'}.', '.$contact->{'FName'};?>&nbsp;&nbsp;Dated:&nbsp;<?php echo $row[0]['applDateTime'];?></h3></li>
			<ol data-role="listview" data-inset="true" style="margin-top:0">
			<?php 
			$i=0;
			foreach ($applProgress as $value) {
				if ($value->{'Comp'}=='Y') {
					$stricon = 'check';
				} else {
					$stricon = 'action';
				}
			?>
			<li data-icon=<?php echo $stricon;?>><a href="/assets/pages/adoptS<?php echo $i++;?>.php?x=<?php echo $id ?>&n=<?php echo $contact->{'FName'}; ?>" data-transition="slide" style="" >	
				<?php echo $value -> {'Sec'}; ?></a>
			</li>
			<?php 
			}
			?>
			</ol>
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