<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$arySQL = array("all"=>"Deleted <> 'Y'",
				"nr"=>"(MemberRenewed IS NULL) AND Deleted = 'N'",
				"me"=>"(DATEDIFF(NOW(),MemberRenewed) BETWEEN 344 AND 364) AND Membership <> 'Inactive' AND Deleted <> 'Y'",
				"mx"=>"(DATEDIFF(NOW(),MemberRenewed) > 364) AND Membership <> 'Inactive' AND Deleted <> 'Y'",
				"am"=>"Membership <> 'Inactive' AND Deleted <> 'Y'",
				"im"=>"Membership = 'Inactive' AND Deleted <> 'Y'",
				"pm"=>"Membership = 'Pending' AND Deleted <> 'Y'",
				"ml"=>"MemberLevel='{q}' AND Membership <> 'Inactive' AND Deleted <> 'Y'",
				"ch"=>"MemberCharter = 'Y' AND Deleted <> 'Y'",
				"fa"=>"{q}='Y' AND Membership <> 'Inactive' AND Deleted <> 'Y'",
				"ic"=>"Deleted='X' AND Membership <> 'Inactive'"
				);
$error = false;
$row = array();
$s= $_GET['s'];
if (isset($_GET['l'])) {
	$where = str_replace('{q}',$_GET['q'],$arySQL[$_GET['l']]);		
	$prep_stmt = "SELECT PeopleT_id,Member_id,FirstName,LastName,OtherName,MemCount,Membership,MemberType,MemberLevel,Email,Phone,PhotoLink,Company FROM PeopleT WHERE $where ORDER BY LastName,FirstName";
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
var_dump($_GET);
var_dump($where);
?>
</head>
<body>
<div data-role="page" id="memberList">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<?php switch($s) { 
					case 'm':
				?>
					<li><a href="#memberMenu" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
				<?php break;
					case 's':
				?>
					<li><a href="#membership" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
				<?php break;
					default:
				?>
					<li class="backBtn"><a href="#" data-icon="back" >Back</a></li>	
				<?php } ?>
				<li><a href="#home" data-icon="home">Home</a></li>
				<?php if ($_GET['a']=='n') { ?>
					<li>&nbsp;</li>
				<?php } else { ?>
					<li class="addMem MemE" id="addMem" style="display:none"><a href="#" data-icon="plus" class="myNav">Add Member</a></li>
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
		<ul data-role="listview" id="member_list" data-autodividers="false" data-filter="true">
			<?php
				if ($stmt->num_rows == 0) { 
					$ary_msgs = array("No Members matching search criteria."); ?>
					<li><p style="font-size: 1em;font-style: italic;"><?php echo $ary_msgs[0];?></p></li>
				<?php
				} else {
				foreach ($row as $rcd) {
				?>
					<li style="padding-bottom:0.1em;padding-top:0.2em">
					<a href="/assets/pages/member.php?x=<?php echo $rcd['PeopleT_id']; ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" style="padding-bottom:0;padding-top:0" data-transition="slide">
					<img src="<?php echo $rcd['PhotoLink']; ?>"/>
					<h3><?php echo $rcd['LastName']; ?>,<?php echo $rcd['FirstName']; ?>&nbsp; #: <?php echo $rcd['Member_id']; ?></h3>
					<p>Family: <?php echo $rcd['OtherName']; ?>
					<br>Membership: <?php echo $rcd['MemCount'];?>&nbsp;&nbsp;<?php echo $rcd['Membership']; ?>&nbsp;:&nbsp;<?php echo $rcd['MemberLevel']; ?>
					<br>Company: <?php echo $rcd['Company']; ?>
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