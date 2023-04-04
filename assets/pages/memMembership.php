<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

$error = false;
$row = array();
$today = date("Y-m-d");
$hardcode_span = -305;
if (isset($_GET['x'])) {
	$id = $_GET['x'];
    $prep_stmt = "SELECT FirstName,Membership,MemberType,MemberLevel,MemberCharter,MemberSince,MemberRenewed,MemberRenewed,Adopter,Foster,MemCount,DATEDIFF(MemberRenewed, NOW()) AS MemberExpire FROM PeopleT WHERE PeopleT_id = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);		
		$prep_stmt = "SELECT * FROM MembershipTrack WHERE fk_PeopleT_id = $id ORDER BY mId DESC";
		if ($hist = $mysqli->query($prep_stmt)) {	
			$histRows =myFetch($hist);
		}
	} else {
        $error = true;
    }
} else {
	$error = true;
}
function checkBox($decision) {
	if ($decision=='Y') {
		return ('checked');
	} else {
		return ('');
	}
}
function dropDownAry($SelAry,$Selected,$SelText) {
	if ($SelText!=='') {
		echo "<option value='0'>--$SelText--</option>";
	}
	foreach ($SelAry as $key=>$value) {
		if ($Selected == $value) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='".$value."' ".$strSel.">".$value."</option>";
	}
}
function ckExpire($d1,$span) {
	return (($d1-$span<0));
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
?>
</head>
<body>
<div data-role="page" id="memMembership"  data-memid='<?php echo $id;?>'>
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>	
				<li id="memMembershipSaveBtn" class="MemE" style="display:none"><a href="#memMembership" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="MemMembershipForm" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Membership Information for <?php echo $_GET['n'] ?></li>						
			<li>
			<table style="border-spacing:0;width:100%" id="memInfoTab">
				<tbody>
					<tr><td>Member Since:</td><td><input type="date" data-clear-btn="false" name="MemberSince" id="MemberSince" max="<?php echo $today ?>" value="<?php echo $row[0][MemberSince] ?>" data-mini="true"></td></tr>
					<tr><td>Renewed:</td><td><input type="date" data-clear-btn="false" name="MemberRenewed" id="MemberRenewed" class="my-date" max="<?php echo $today ?>" value="<?php echo $row[0][MemberRenewed] ?>" data-mini="true"></td></tr>				
					<?php if ($row[0][Membership]=='Inactive') { ?>
					<tr><td>Inactive:</td><td><input type="date" data-clear-btn="false" name="MemberInactive" id="MemberInactive" class="my-date" max="<?php echo $today ?>" value="<?php echo $row[0][MemberInactive] ?>" data-mini="true"></td></tr>
					<?php } else { ?>
					<input type="hidden" name="MemberInactive" id="MemberInactive" value="<?php echo $today ?>">
					<?php } ?>
				</tbody>
			</table>
			</li>
			<li>
				<table><thead>
				<tr style="text-align:center"><th width="33%">Membership</th><th width="33%">Level</th><th width="33%">Quantity</th></tr>
				</thead><tbody>
				<tr>
				<td class="">
					<select name="Membership" id="Membership" data-mini="true" required dir="ltr">
						<?php
						$aryDrop = ['Non Member','Pending','Active','Inactive'];
						dropDownAry($aryDrop,$row[0][Membership],' Membership ');
						?>
					</select>
				</td>
				<td class="">
					<select name="MemberLevel" id="MemberLevel" data-mini="true" required dir="ltr">
						<?php
						$aryDrop = ['Basic','Sponsor','Student','Guest'];
						dropDownAry($aryDrop,$row[0][MemberLevel],' MemberLevel ');
						?>
					</select>
				</td>
				<td class="">
					<select name="MemCount" id="MemCount" data-mini="true" required dir="ltr">
						<?php
						$aryDrop = ['0','1','2','3','4','5','6','7','8'];
						dropDownAry($aryDrop,$row[0][MemCount],'');
						?>
					</select>
				</td>
				</tr></tbody></table>
			</fieldset>			
			</li>
			<?php if (ckExpire($row[0]['MemberExpire'],$hardcode_span) || $row[0][Membership]=='Active' || $row[0][Membership]=='Inactive') { ?>						
				<li>
				<div class="hideMemBtn ui-input-btn ui-btn ui-btn-b data-mini="true"">
				<?php if (ckExpire($row[0]['MemberExpire'],$hardcode_span) || $row[0][Membership]=='Inactive') { ?>	
					<span id="hideMemRenew">
					<input type="button" name="memMembershipRenew" id="memMembershipRenew" value="Renew Membership" data-mini="false">
					</span>
				<?php } ?>
				<?php if ($row[0][Membership]=='Active') {	?>	
					<span id="hideMemInactive">				
					<input type="button" name="memMembershipInactive" id="memMembershipInactive" value="Go Inactive" data-mini="true">
					</span>				
				<?php } ?>
				</div>
				</li>			
			<?php } ?>
			
			</ul>
			<input type="hidden" name="PeopleT_id" id="PeopleT_id" value="<?php echo $id ?>">
			<input type="hidden" name="memHistory" id="memHistory" value="N">
			<input type="hidden" name="mAction" id="mAction" value="Adjust">
			<input type="hidden" name="mBy_PeopleT_id" id="mBy_PeopleT_id" value="0">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
		
		<ul data-role="listview" id="memHistory_list" data-autodividers="false" style="margin-top:2em; <?php echo $hist->num_rows>0 ? '' : 'display:none';?>">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Membership Renewal History</li>						
				<table class="memHistoryList" id="memHistoryList" style="border-style:solid;border-width:thin" width="100%"><thead>
				<tr><th>Date</th><th>Membership</th><th>Level</th><th>Qty</th><th>Action</th></tr></thead><tbody>
			<?php
				foreach ($histRows as $rcd) {
				?>
					<tr><td><?php echo $rcd[mRenewDate]?></td><td style="text-align:center"><?php echo $rcd[mMemberShip];?></td>
					<td style="text-align:center"><?php echo $rcd[mMemberLevel];?></td><td style="text-align:center"><?php echo $rcd[mMemCount];?></td><td><?php echo $rcd[mAction];?></td></tr>
			<?php	
				}
			?>
				</tbody></table>
		</ul>
		
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="memRenewConfirm" data-dismissible="true" data-theme="a" >
		<div data-role="header" data-theme="a">
		<h1>Please confirm Renewal</h1>
		</div>
		<div data-theme="a" style="margin:5px;">
			<p>The membership will be renewed.</p>
			<p>This action cannot be undone.</p>
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li class="myNav"><a href="#" data-icon="delete" data-rel="back"  class="myNav">Cancel</a></li>	
					<li  class="myNav confMemRenew"><a href="#" data-icon="check" id="memRenewBtn" class="myNav confMemRenew">Confirm</a></li>
				</ul>
			</div><!-- /navbar -->			
		</div>
	</div> <!-- /popup -->
	<div data-role="popup" id="memInactiveConfirm" data-dismissible="true" data-theme="a" >
		<div data-role="header" data-theme="a">
		<h1>Please confirm Deactivation</h1>
		</div>
		<div data-theme="a" style="margin:5px;">
			<p>The membership will go Inactive.</p>
			<p>This action cannot be undone.</p>
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li class="myNav"><a href="#" data-icon="delete" data-rel="back"  class="myNav">Cancel</a></li>	
					<li  class="myNav confMemInactive"><a href="#" data-icon="check" id="memInactiveBtn" class="myNav confMemInactive">Confirm</a></li>
				</ul>
			</div><!-- /navbar -->			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="memMembershipSuccess" data-dismissible="true" data-theme="a" data-position="#memInfoTab">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $row[0][FirstName] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="memMembershipError" data-dismissible="false" data-theme="a"  data-position="#memInfoTab">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $row[0][FirstName] ?> failed, please try again later!</p>
			<p id="memMembershipErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>