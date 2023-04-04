<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$error = false;
$row = array();
$arySQL = array("x"=>"",
				"h"=>"applStatus='{q}' AND",
				"a"=>"applStatus='{q}' AND applState='Active' AND",
				"s"=>"applState='{q}' AND",
				"m"=>"fk_PeopleT_id='{q}' AND",
				"ic"=>"Deleted='X' AND"
				);
$l= $_GET['l'];
$s= (is_null($_GET['s'])) ? 'x' : $_GET['s'];
if (isset($_GET['l'])) {
//	$where = "";	
	$where = str_replace('{q}',$_GET['q'],$arySQL[$_GET['l']]);
//	if ($_GET['q']!='') {
//		$where = $s=='a' ? "applStatus='$_GET[q]' AND" : "fk_PeopleT_id='$_GET[q]' AND" ;
//	}
	$prep_stmt = "SELECT applid,applDocId,applContact,applStatus,applState,c.FirstName as hvfn,c.LastName as hvln,DATE_FORMAT(applDateTime,'%m/%d/%Y') AS applDateTime FROM Applications AS a LEFT JOIN PeopleT AS c ON (c.PeopleT_id=a.applHV_fk_PeopleT) WHERE $where a.Deleted <> 'Y' ORDER BY applid DESC";
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
var_dump($row);
var_dump($prep_stmt);
?>
</head>
<body>
<div data-role="page" id="adoptList">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<?php switch($s) { 
					case 'a':
				?>
					<li><a href="#adoptActivePage" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
				<?php break;
					case 'h':
				?>
					<li><a href="#adoptPage" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
				<?php break;
					case 'm':
				?>
					<li class="backBtn"><a href="#" data-icon="back" >Back</a></li>	
				<?php break;
					default:
				?>
					<li class="backBtn"><a href="#" data-icon="back" >Back</a></li>	
				<?php } ?>
			
				<li><a href="#home" data-icon="home">Home</a></li>
				<?php if ($_GET['a']=='n') { ?>
					<li>&nbsp;</li>
				<?php } else { ?>
					<li class="addAppl" id="addAppl"  style="display:none"><a href="#" data-icon="plus" class="myNav">New Application</a></li>
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
				<p>Could not access the Member Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="adopt_list" data-autodividers="false" data-filter="true" style="">
			<?php
				if ($stmt->num_rows == 0) { 
					$ary_msgs = array("No Requests matching search criteria."); ?>
					<li><p style="font-size: 1em;font-style: italic;"><?php echo $ary_msgs[0];?></p></li>
				<?php
				} else {
					foreach ($row as $rcd) {
					$contact = json_decode($rcd[applContact]);
					$addr = $contact->{Addr}==''?'{Address}':$contact->{Addr}.', '.$contact->{City}.', '.$contact->{St}.' '.$contact->{Zip};					
				?>
					<li style="padding-bottom:0.1em;padding-top:0.2em"><a href="/assets/pages/adopt.php?x=<?php echo $rcd[applid]; ?>&l=&q=" style="padding:0.5em 1em;" data-transition="slide">
						<h3 style="margin-bottom:0;"><?php echo $contact->{LName}.', '.$contact->{FName};?>&nbsp; #: <?php echo $rcd[applid];?></h3>
						<p style="margin-top:0;">Submitted: <?php echo $rcd[applDateTime];?>&nbsp;&nbsp;State: <?php echo $rcd[applState];?>&nbsp;&nbsp;Status: <?php echo $rcd[applStatus];?></p>
						<?php if ($_GET['q']=='HomeVisit' || $_GET['q']=='VisitComplete') { ?>
							<p style="margin-top:0;">Home Visit Volunteer:&nbsp;<?php echo $rcd['hvfn'];?>&nbsp;<?php echo $rcd['hvln'];?></p>
						<?php } ?>
							<p><?php echo $addr;?>
						<br>Email: <?php echo $contact->{Email};?>
						<br>Phone: <?php echo $contact->{Phone};?></p>						
					</a></li>				
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