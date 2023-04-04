<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$hardcode_span = -305;
$error = false;
$row = array();
if (isset($_GET['x'])) {
	$id = $_GET['x'];
    $prep_stmt = "SELECT PeopleT_id,Member_id,FirstName,LastName,Title,Salutation,Email,Phone,Cell,Fax,Address,City,StateProvince,MemberLevel,".
				"Country,MailCode,Company,WorkPhone,WorkEmail,Contact,Vet,Membership,MemberBlob,Deleted,Interests,Teams,MemberType,PhotoLink,OtherName,".
				"DATE_FORMAT(MemberSince,'%m/%d/%Y') AS MemberSince,DATE_FORMAT(MemberRenewed,'%m/%d/%Y') AS MemberRenewed, ".
				"DATEDIFF(MemberRenewed, NOW()) AS MemberExpire, MemberQuickNote,MemberCharter,Adopter,Foster,MemCount ".
				"FROM PeopleT WHERE PeopleT_id = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$interests = json_decode($row[0]['Interests']);	
		$teams = json_decode($row[0]['Teams']);	
//		$prep_stmt = "SELECT Count(*) AS Qty FROM DogT WHERE DogCurMemNo = '".$row[0]['PeopleT_id']."'";
//		if ($stmt = $mysqli->query($prep_stmt)) {
//			$dogCnt = $stmt->fetch_row();
//		}
		$prep_stmt = "SELECT DogProcStatus, Count(*) AS Qty FROM DogT WHERE Fk_PeopleT_id = $id GROUP BY DogProcStatus";
		$dogQty = 0;
		if ($stmt = $mysqli->query($prep_stmt)) {
			$dogCnt = new StdClass();
			// $rowx = $stmt->fetch_all();
			while ($rowx = $stmt->fetch_assoc()) {
				$idx = $rowx['DogProcStatus'];
				$dogCnt->$idx = $rowx['Qty'];
				// $dogCnt->$rowx['DogProcStatus'] = $rowx['Qty'];     // Array to string conversion warning!
				// $dogQty = $dogQty + $rowx['Qty'];
			}
		}		
		$prep_stmt = "SELECT Count(*) AS Qty FROM FileAsset WHERE Fk_PeopleT_id = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$mediaCnt = $stmt->fetch_row();
		}
		$prep_stmt = "SELECT Count(*) AS Qty FROM ContactLog WHERE Fk_PeopleT_id = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$contactCnt = $stmt->fetch_row();
		}			
		$prep_stmt = "SELECT Count(*) AS Qty FROM Applications WHERE Fk_PeopleT_id = $id";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$ApplicationCnt = $stmt->fetch_row();
		}	

	} else {
        $error = true;;
    }
} else {
	$error = true;;
}
function ckExpire($d1,$span) {
	$d1 = $d1=='' ? date("Y-m-d") : $d1;	
	return (($d1-$span<0));
}
function checkBox($decision) {
	if ($decision=='Y') {
		return ('checked');
	} else {
		return ('');
	}
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
var_dump($rowx);
var_dump($dogCnt);
echo $dogCnt->Fostered;
echo $dogCnt->{'Fostered'};
?>
</head>
<body>
<div data-role="page" id="memberPage">
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
				<p>Could not access the Member Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
		if ($id<>'0') {
	?>
		<ul data-role="listview" id="member_info" data-autodividers="false" data-filter="false">		
			<li data-icon="edit"><a href="/assets/pages/memInfo.php?x=<?php echo $id ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>&n=<?php echo $row[0]['FirstName'] ?>" data-transition="slide" style="padding:0.5em 1em;" >
				<table style="border-spacing:0;width:100%;font-weight:400;border-bottom:1pt dotted rgb(196,196,196)"><tbody>
					<tr>
						<td style="font-weight:600"><?php echo $row[0]['LastName'] ?>,&nbsp;<?php echo $row[0]['FirstName'] ?>:&nbsp;&nbsp; # <?php echo $row[0]['Member_id'] ?></td>				
						<td rowspan="4">
							<?php if (isset($row[0]['PhotoLink'])) { ?>				
								<img src="<?php echo $row[0]['PhotoLink'] ?>"/>
							<?php } else { ?>			
								<img src="/assets/img/person.jpg"/>
							<?php } ?>					
						</td>
					</tr>
					<?php if (!$row[0]['OtherName']=='') { ?>
					<tr><td>Family:&nbsp;<?php echo $row[0]['OtherName'];?></td></tr>
					<?php } ?>
					<tr><td><?php echo $row[0]['Membership'] ?>&nbsp;:&nbsp;<?php echo $row[0]['MemberLevel'] ?></td></tr>
					<tr><td>Email: <?php echo $row[0]['Email'] ?></td></tr>
					<tr><td>Phone: <?php echo $row[0]['Phone'] ?></td>
				</tbody></table>				
				<table style="border-spacing:0;width:100%;font-weight:400;"><tbody>					
					<tr><td style="font-weight:600">Address:</td><td></td></tr>
					<tr><td colspan="2"><?php echo $row[0]['Address'];?></td></tr>
					<tr><td colspan="2"><?php echo $row[0]['City'];?>, <?php echo $row[0]['StateProvince'];?></td>
					<tr><td colspan="2"><?php echo $row[0]['Country']==''?'':$row[0]['Country'].'&nbsp;&nbsp;';?><?php echo $row[0]['MailCode'];?>
				</tbody></table>
				<?php if (!$row[0]['MemberQuickNote'] == '') { ?>
					<table style="border-spacing:0;width:100%;font-weight:400;border-top:1pt dotted rgb(196,196,196)"><tbody>	
						<tr><td><?php echo $row[0]['MemberQuickNote'];?></td></tr>
					</tbody></table>
				<?php } ?>
				
			</a></li>
			<li>
			<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<form id="memChecks" class="forms">
				<input type="checkbox" class="memChecks" name="MemberCharter" id="MemberCharter" value="Y" <?php echo checkBox($row[0]['MemberCharter']); ?> ><label for="MemberCharter" style="padding: 0.7em 0.5em;border-right: white solid thin;">Charter Member</label>			
				<input type="checkbox" class="memChecks" name="Adopter" id="Adopter" value="Y" <?php echo checkBox($row[0]['Adopter']); ?> ><label for="Adopter" style="padding: 0.7em 0.5em;border-right: white solid thin;">Willing to Adopt</label>
				<input type="checkbox" class="memChecks" name="Foster" id="Foster" value="Y" <?php echo checkBox($row[0]['Foster']); ?> ><label for="Foster" style="padding: 0.7em 0.5em;">Willing to Foster</label>
				<input type="hidden" name="PeopleT_id" id="PeopleT_id" value="<?php echo $id ?>">
				<input type="hidden" name="MemRT" id="MemRT" value="Y">
				</form>
			</fieldset>
			</li>			
			<li data-role="collapsible" data-collapsed="true" role="heading" data-iconpos="right" data-inset="true" style="padding:0;">
				<h3 style="margin:0">Special Skills/Other Information</h3>
				<ul data-role="listview"><li data-icon="edit"><a href="/assets/pages/memSkills.php?x=<?php echo $id ?>&n=<?php echo $row[0]['FirstName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >				
				<p style="padding: 0px 8px 8px 8px;white-space:pre-line;"><?php echo $row[0]['MemberBlob'] ?></p>
				</a></li></ul>
			</li>
			<li data-role="collapsible" data-collapsed="true" role="heading" data-iconpos="right" data-inset="true" style="padding:0;border:0;">
				<h3 style="margin:0">Company Information</h3>
				<ul data-role="listview"><li data-icon="edit"><a href="/assets/pages/memCompany.php?x=<?php echo $id ?>&n=<?php echo $row[0]['FirstName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >
				<p style="padding: 0px 8px 8px 8px;white-space:normal;">
					Company: <?php echo $row[0]['Company'] ?><br>
					Title: <?php echo $row[0]['Title'];?><br>
					Email: <?php echo $row[0]['WorkEmail'];?><br>
					Phone: <?php echo $row[0]['WorkPhone'];?><br>
					Contact: <?php echo $row[0]['Contact'];?><br>
				</p>
				</a></li></ul>
			</li>
			<li><a href="/assets/pages/memMembership.php?x=<?php echo $id ?>&n=<?php echo $row[0]['FirstName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >	
				Membership
				<span>
				<?php 
					if (ckExpire($row[0]['MemberExpire'],-365)) {
						echo '<span class="myErrMsg">&nbsp;&nbsp;Expired!</span>';
					} else {
						if (ckExpire($row[0]['MemberExpire'],$hardcode_span)) {
							echo '<span class="myErrMsg">&nbsp;&nbsp;Expiring!</span>';							
						}
					}
				?>			
				</span><span class="ui-li-count"><?php echo $row[0]['MemCount']; ?></span>				
				</a>
			</li>			
			<li><a href="/assets/pages/memInterests.php?x=<?php echo $id ?>&n=<?php echo $row[0]['FirstName'] ?>&m=team&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >	
				Team Assignments<span class="ui-li-count"><?php echo $teams->Qty; ?></span></a>
			</li>
			<li><a href="/assets/pages/memInterests.php?x=<?php echo $id ?>&n=<?php echo $row[0]['FirstName'] ?>&m=&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >	
				Member's Interests<span class="ui-li-count"><?php echo $interests->Qty; ?></span></a>
			</li>
			<li><a href="/assets/pages/memContact.php?x=<?php echo $id ?>&n=<?php echo $row[0]['FirstName'] ?>&l=<?php echo $_GET['l']; ?>&q=<?php echo $_GET['q']; ?>" data-transition="slide" style="padding:0.5em 1em;" >	
				Contact Log<span class="ui-li-count"><?php echo $contactCnt[0] ?></span></a>
			</li>	
			<li><a href="/assets/pages/adoptDog.php?x=1&n=<?php echo $row[0]['FirstName'] ?>&l=memf&q=<?php echo $row[0]['PeopleT_id'] ?>&s=<?php echo $row[0]['applState']; ?>&a=<?php echo $dogCnt->{'InProcess'};?>" data-transition="slide" style="padding:0.5em 1em;" >	
				In Process Dogs<span class="ui-li-count"><?php echo $dogCnt->{'InProcess'}; ?></span></a>
			</li>					
			<li><a href="/assets/pages/adoptDog.php?x=1&n=<?php echo $row[0]['FirstName'] ?>&l=memt&q=<?php echo $row[0]['PeopleT_id'] ?>&s=<?php echo $row[0]['applState']; ?>&a=<?php echo $dogCnt->{'Match-Trial'};?>" data-transition="slide" style="padding:0.5em 1em;" >	
				Trial Match<span class="ui-li-count"><?php echo $dogCnt->{'Match-Trial'}; ?></span></a>
			</li>	
			<li><a href="/assets/pages/adoptDog.php?x=1&n=<?php echo $row[0]['FirstName'] ?>&l=memf&q=<?php echo $row[0]['PeopleT_id'] ?>&s=<?php echo $row[0]['applState']; ?>&a=<?php echo $dogCnt->{'Fostered'};?>" data-transition="slide" style="padding:0.5em 1em;" >	
				Fostered Dogs<span class="ui-li-count"><?php echo $dogCnt->{'Fostered'}; ?></span></a>
			</li>						
			<li><a href="/assets/pages/adoptDog.php?x=1&n=<?php echo $row[0]['FirstName'] ?>&l=mema&q=<?php echo $row[0]['PeopleT_id'] ?>&s=<?php echo $row[0]['applState']; ?>&a=<?php echo $dogCnt->{'Adopted'};?>" data-transition="slide" style="padding:0.5em 1em;" >	
				Adopted Dogs<span class="ui-li-count"><?php echo $dogCnt->{'Adopted'}; ?></span></a>
			</li>							
			<li><?php if ($ApplicationCnt[0]==0) { ?>	
				<a href="#">		
			<?php } else { ?>
				<a href="/assets/pages/adoptList.php?x=2&n=<?php echo $row[0]['FirstName'] ?>&l=cm&q=<?php echo $row[0]['PeopleT_id']; ?>&s=m&a=n" data-transition="slide" style="padding:0.5em 1em;" >	
			<?php } ?>
				Applications<span class="ui-li-count"><?php echo $ApplicationCnt[0] ?></span></a>
			</li>					
			<li><a href="/assets/pages/mediaList.php?x=<?php echo $id ?>&m=member&d=0&v=0&r=0&s=s" data-transition="slide" style="padding:0.5em 1em;" >	
				Media Attachments<span class="ui-li-count"><?php echo $mediaCnt[0] ?></span></a>
			</li>

			<!-- javascript fills in the contents of this list on pagebeforeshow-->
		</ul>
	<?php
		} else { ?>
		<h3>No Member has been selected</h3>
		<p>You will need to assign a member by first updating the Status.</p>
	<?php
		}
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>