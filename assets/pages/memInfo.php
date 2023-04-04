<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
include_once '../inc/us_states.php';

$error = false;
$row = array();
$today = date("Y-m-d");
if (isset($_GET['x'])) {
	$id = $_GET['x'];
    $prep_stmt = "SELECT PeopleT_id,Member_id,FirstName,LastName,Title,Salutation,Email,Phone,Cell,Fax,Address,City,StateProvince,".
				"Country,MailCode,Company,WorkPhone,WorkEmail,Contact,Vet,Membership,MemberBlob,Deleted,Interests,MemberType,PhotoLink,".
				"MemberSince,MemberRenewed,OtherName,MemberQuickNote ".						
				"FROM PeopleT WHERE PeopleT_id = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		if ($row[0]['Deleted']=='X') {
			$row[0]['Member_id'] = $id;
			$row[0]['MemberSince'] = $today;
			$row[0]['StateProvince'] = 'AZ';
			$row[0]['FirstName']	= $row[0]['LastName'] ='';	
		}		
	} else {
        $error = true;
    }
} else {
	$error = true;
}

function dropDown($DBConn,$DBtable,$Selected) {	
	$dropSQL = "SELECT * FROM $DBtable";
	if ($dropResp = $DBConn->query($dropSQL)) {
		echo "<option value='0'>--Select--</option>";
		while ($dropRow = $dropResp->fetch_object()) {
			if ($Selected == $dropRow->SelText) {
				$strSel = "Selected";
			} else {
				$strSel = "";
			}
			echo "<option value='".$dropRow->SelText."' ".$strSel.">".$dropRow->SelText."</option>";
		}
	} else {
		echo "<option value='0'>!!DB Error!!</option>";
	}
}
function dropDownAry($SelAry,$Selected,$SelText) {
	echo "<option value='0'>--$SelText--</option>";
	foreach ($SelAry as $key=>$value) {
		if ($Selected == $key) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='".$key."' ".$strSel.">".$value."</option>";
	}
}
function phone($phoneNo) {
	if ($phoneNo != '') {
		echo '<span><a href="tel:'.$phoneNo.'"><img src="/assets/img/phoneicon.png" height="20" style="float:right;"/></a></span>';
	}
}
function email($email) {
	if ($email != '') {
		echo '<span><a href="mailto:'.$email.'"><img src="/assets/img/emailicon.png" height="20" style="float:right;"/></a></span>';
	}
}
?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
?>
</head>
<body>
<div data-role="page" id="memInfo" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" >Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle">Cancel</a></li>	
				<li id="memSaveBtn" class="MemE" style="display:none"><a href="#memInfo" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">

		<form id="MemEdit" class="forms">
			<ul data-role="listview" id="mem_update" data-autodividers="false" data-filter="false">
			<li>
			<table style="border-spacing:0;width:100%" id="memInfoTab">
				<tbody>
					<tr><td rowspan="2">
					<a href="/assets/pages/media.php?x=<?php echo $id ?>&v=0&m=member&d=0&r=0" data-transition="slide" style="padding:0.5em 1em;" >					
					<img src="<?php echo $row[0]['PhotoLink'] ?>"/>
					</a>
					</td><td><input type="text" name="Member_id" id="Member_id" size="20" class="my-input-text" placeholder="Member ID..." required value="<?php echo $row[0]['Member_id'] ?>" data-mini="true"></td></tr>
					<tr><td><input type="text" name="Salutation" id="Salutation" size="20" class="my-input-text" placeholder="Salutation..." value="<?php echo $row[0]['Salutation'] ?>" data-mini="true" >					
					</td></tr>
					<tr><td>Last Name*:</td><td><input type="text" name="LastName" id="LastName" size = "20" class="my-input-text" placeholder="Last Name..." required value="<?php echo $row[0]['LastName'] ?>" data-mini="true"></td></tr>
					<tr><td>First Name*:</td><td><input type="text" name="FirstName" id="FirstName" size = "20" class="my-input-text" placeholder="First Name..." required value="<?php echo $row[0]['FirstName'] ?>" data-mini="true"></td></tr>
					<tr><td>Family Members:</td><td><input type="text" name="OtherName" id="OtherName" size = "20" class="my-input-text" placeholder="Other Family Members..." value="<?php echo $row[0]['OtherName'] ?>" data-mini="true"></td></tr>
					<tr><td>Email:<?php email($row[0]['Email'])?></td><td><input type="email" name="Email" id="Email" size = "20" class="my-input-text" placeholder="Email Address..." value="<?php echo $row[0]['Email'] ?>" data-mini="true"></td></tr>
					<tr><td>Phone*:<?php phone($row[0]['Phone'])?></td><td><input type="tel" name="Phone" id="Phone" size = "20" class="my-input-text" placeholder="(   )___-___" required value="<?php echo $row[0]['Phone'] ?>" pattern="^(?:\([2-9]\d{2}\)\ ?|[2-9]\d{2}(?:\-?|\ ?))[2-9]\d{2}[- ]?\d{4}$" data-mini="true"></td></tr>
					<tr><td>Cell:<?php phone($row[0]['Cell'])?></td><td><input type="tel" name="Cell" id="Cell" size = "20" class="my-input-text" placeholder="(   )___-___" value="<?php echo $row[0]['Cell'] ?>" data-mini="true"></td></tr>
					<tr><td>Fax:</td><td><input type="tel" name="Fax" id="Fax" size = "20" class="my-input-text" placeholder="(   )___-___"  value="<?php echo $row[0]['Fax'] ?>" data-mini="true"></td></tr>
					<tr><td>Member Since:</td><td><input type="date" data-clear-btn="false" name="MemberSince" id="MemberSince" max="<?php echo $today ?>" value="<?php echo $row[0]['MemberSince'] ?>" data-mini="true"></td></tr>
					<tr><td>Renewed:</td><td><input type="date" data-clear-btn="false" name="MemberRenewed" id="MemberRenewed" class="my-date" max="<?php echo $today ?>" value="<?php echo $row[0]['MemberRenewed'] ?>" data-mini="true"></td></tr>
					<tr><td colspan="2"><textarea name="MemberQuickNote" cols="50" rows="2" id="MemberQuickNote" placeholder="Quick Note..." data-mini="true"><?php echo $row[0]['MemberQuickNote']; ?></textarea></td></tr>

				</tbody>
			</table>
			</li>
			<li>
			<table style="border-spacing:0;width:100%">
				<tbody>
					<tr><td>Street:</td><td><input type="text" name="Address" id="Address" size = "20" class="my-input-text" placeholder="Street..."  value="<?php echo $row[0]['Address'] ?>" data-mini="true"></td></tr>
					<tr><td>City:</td><td><input type="text" name="City" id="City" size = "20" class="my-input-text" placeholder="City..."  value="<?php echo $row[0]['City'] ?>" data-mini="true"></td></tr>
					<tr><td>State:</td>
					<td>
						<select name="StateProvince" id="StateProvince" data-mini="true" dir="ltr">
							<?php
							dropDownAry($us_state_abbrevs_names,strtoupper($row[0]['StateProvince']),' Select State ');
							?>
						</select>					
					</td></tr>
					<tr><td>Zip:</td><td><input type="number" name="MailCode" id="MailCode" size = "20" class="my-input-text" placeholder="Zip..." value="<?php echo $row[0]['MailCode'] ?>" data-mini="true"></td></tr>
				</tbody>
			</table>			
			</li>
			</ul>
			<input type="hidden" name="PeopleT_id" id="PeopleT_id" value="<?php echo $id ?>">
			<input type="hidden" name="Country" id="Country" value="US">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="memSuccess" data-dismissible="true" data-theme="a" data-position="#memInfoTab">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p>The record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="memError" data-dismissible="false" data-theme="a"  data-position="#memInfoTab">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update failed, please try again later!</p>
			<p id="memErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>