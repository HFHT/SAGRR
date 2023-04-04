<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$arySQL = array("all"=>"WHERE a.fk_PeopleT_id IS NOT NULL AND a.Deleted<>'Y'",
				"un"=>"WHERE a.Validated='N' AND a.Deleted<>'Y'",
				"nl"=>"WHERE a.fk_PeopleT_id IS NULL AND a.Deleted<>'Y'",
				"ue"=>"WHERE (INSTR(a.Role,'\"Dog\":\"Y')>0 OR INSTR(a.Role,'\"Mem\":\"Y')>0) AND a.Deleted<>'Y'",
				"ua"=>"WHERE INSTR(a.Role,'\"Admin\":\"Y')>0 AND a.Deleted<>'Y'",
				"ul"=>"WHERE a.Locked = 'Y' AND a.Deleted<>'Y'",
				);
$error = false;
$row = array();
$s= $_GET['s'];
if (isset($_GET['l'])) {
	$where = str_replace('{q}',$_GET['q'],$arySQL[$_GET['l']]);		
	$prep_stmt = "SELECT * FROM members a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) $where ORDER BY a.memname,a.BeginDate";
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
//var_dump($row[0]);
?>
</head>
<body>
<div data-role="page" id="userList">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li><a href="#admin" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
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
				<p>Could not access the Users Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="user_list" data-autodividers="false" data-filter="true" style="">
			<?php
				if ($stmt->num_rows == 0) { 
					$ary_msgs = array("No Users matching search criteria."); ?>
					<li><p style="font-size: 1em;font-style: italic;"><?php echo $ary_msgs[0];?></p></li>
				<?php
				} else {
					foreach ($row as $rcd) {
					$role = new StdClass();
					$role = json_decode($rcd[Role]);
				?>
					<li>
					<a href="/assets/pages/user.php?x=<?php echo $rcd[username]; ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" style="padding-bottom:0.7em;padding-top:0" data-transition="slide">
					<h3><?php echo $rcd[memname]; ?></h3>
					<h3><?php echo $rcd[email]; ?></h3>
					<p>Linked Member: <?php echo $rcd[LastName]=='' ? '' : $rcd[LastName].', '.$rcd[FirstName]; ?>
					<br>First Access: <?php echo $rcd[BeginDate]; ?>
					<br>Last Access: <?php echo $rcd[LastLogin]; ?>
					<br>Access Count: <?php echo $rcd[LoginCnt]; ?>
					<br>Editor Rights: <?php echo $role->{'Self'}=='Y'?'Self ':''; echo $role->{'Dog'}=='Y'?'Dog ':''; echo $role->{'Mem'}=='Y'?'Member ':''; echo $role->{'App'}=='Y'?'Approval ':''; echo $role->{'Admin'}=='Y'?'Admin ':''; ?>
					<?php if ($rcd[Locked]=='Y') { ?>
						<br>Locked on: <?php echo $rcd[LockedDate]; ?>
						<br>Reason: <?php echo $rcd[LockedReason]; ?>
					<?php } ?>
					</p></a></li>						
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