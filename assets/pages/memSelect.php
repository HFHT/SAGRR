<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = array();
if (isset($_GET['m'])) {
	$id = $_GET['m'];
	if ($_GET['l']=='intake') {
		$where = "(INSTR(Teams,'\"2\"') OR INSTR(Teams,'\"4\"') OR INSTR(Teams,'\"6\"') OR INSTR(Teams,'\"9\"')) AND ";
	} else {
		if ($_GET['l']=='visit') {
			$where = "(INSTR(Teams,'\"3\"') OR INSTR(Teams,'\"2\"')) AND ";
		} else {
			$where = '';
		}
	}
	$prep_stmt = "SELECT PeopleT_id,Member_id,FirstName,LastName,Membership,MemberType,Email,Phone,PhotoLink,Teams FROM PeopleT WHERE $where Deleted = 'N' ORDER BY LastName,FirstName";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$Teams = [];
		if ($_GET['l']!='') {
			$prep_stmt = "SELECT SelId,SelText FROM SIGroupT";
			$result = $mysqli->query($prep_stmt);
			while ($r = $result->fetch_assoc()) {
				$Teams[$r['SelId']]=$r['SelText'];
			}
		}
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
var_dump($interests);
?>
</head>
<body>
<div data-role="page" id="memSelect">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="cancelBtn"><a href="#" data-icon="recycle">Cancel</a></li>	
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
		<ul data-role="listview" id="memSelectlist" data-autodividers="false" data-filter="true" style="margin-top: 0;">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Select Responsible Person</li>
			<?php
				foreach ($row as $rcd) {
					$memInterest=json_decode($rcd['Teams']);
				?>
					<li><a href="#" style="padding:0.5em 1em;" data-id="<?php echo $rcd['PeopleT_id'] ?>" data-team="<?php echo $_GET['l'] ?>" >
						<h3><span class="memName"><?php echo ($rcd['LastName'].", ".$rcd['FirstName']) ?></span>&nbsp; #: <span class="memId"><?php echo ($rcd['Member_id']) ?></span></h3>
						<p><?php echo ("Phone: ".$rcd['Phone']." &nbsp; email: ".$rcd['Email']) ?></p>						
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