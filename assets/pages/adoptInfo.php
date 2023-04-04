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
    $prep_stmt = "SELECT a.applid,a.applDocId,a.applContact,a.applComplete,a.applApproveDate,a.applProgress,a.applFostAdopt,a.applSource,a.applNote,
				a.applContact,a.applStatus,a.fk_PeopleT_id,a.applHV_fk_PeopleT,DATE_FORMAT(a.applDateTime,'%Y-%m-%d') AS applDateTime
				FROM Applications AS a 
				WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$contact = json_decode($row[0][applContact]);
		if (strpos($contact->{FName},'FirstName')!==false) {
			$contact->{FName}='';
		}
		if (strpos($contact->{LName},'LastName')!==false) {
			$contact->{LName}='';
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
var_dump($prep_stmt);
var_dump($row);
var_dump($contact);
?>
</head>
<body>
<div data-role="page" id="adoptInfo" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" >Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle">Cancel</a></li>	
				<li id="adoptSaveBtn" class="ApplE" style="display:none"><a href="#" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
	
		<form id="adoptEditForm" class="forms">
		<ul data-role="listview" id="adopt_update" data-autodividers="false" data-filter="false">
		<li>
			<table style="border-spacing:0;width:100%" id="adoptInfoTab">
				<table style="border-spacing:0;width:100%;font-weight:400;border-bottom:1pt dotted rgb(196,196,196)"><tbody>
					<tr><td>Last Name*:</td><td><input type="text" name="LName" id="LName" size = "20" class="my-input-text" placeholder="Last Name..." required value="<?php echo $contact->{LName} ?>" data-mini="true"></td></tr>
					<tr><td>First Name*:</td><td><input type="text" name="FName" id="FName" size = "20" class="my-input-text" placeholder="First Name..." required value="<?php echo $contact->{FName} ?>" data-mini="true"></td></tr>
					<tr><td>Family Members:</td><td><input type="text" name="OName" id="OName" size = "20" class="my-input-text" placeholder="Other Family Members..." value="<?php echo $contact->{OName} ?>" data-mini="true"></td></tr>				
					<tr><td>Email:<?php email($contact->{Email})?></td><td><input type="email" name="Email" id="Email" size = "20" class="my-input-text" placeholder="Email Address..." value="<?php echo $contact->{Email} ?>" data-mini="true"></td></tr>
					<tr><td>Phone*:<?php phone($contact->{Phone})?></td><td><input type="tel" name="Phone" id="Phone" size = "20" class="my-input-text" placeholder="(   )___-___" required value="<?php echo $contact->{Phone} ?>" pattern="^(?:\([2-9]\d{2}\)\ ?|[2-9]\d{2}(?:\-?|\ ?))[2-9]\d{2}[- ]?\d{4}$" data-mini="true"></td></tr>
					<tr><td>Cell:<?php phone($contact->{Cell})?></td><td><input type="tel" name="Cell" id="Cell" size = "20" class="my-input-text" placeholder="(   )___-___" value="<?php echo $contact->{Cell} ?>" data-mini="true"></td></tr>
					<tr><td width="40%">Apply Date:</td><td width="60%"><input type="date" data-clear-btn="false" name="applDateTime" id="applDateTime" max="<?php echo $today ?>" value="<?php echo $row[0]['applDateTime'] ?>" required></td></tr>					
					</tbody></table>							
			</table>
			</li>
			<li>
			<table style="border-spacing:0;width:100%">
				<tbody>
					<tr><td>Street:</td><td><input type="text" name="Addr" id="Addr" size = "20" class="my-input-text" placeholder="Street..."  value="<?php echo $contact->{Addr} ?>" data-mini="true"></td></tr>
					<tr><td>City:</td><td><input type="text" name="City" id="City" size = "20" class="my-input-text" placeholder="City..."  value="<?php echo $contact->{City} ?>" data-mini="true"></td></tr>
					<tr><td>State:</td>
					<td>
						<select name="St" id="St" data-mini="true" dir="ltr">
							<?php
							dropDownAry($us_state_abbrevs_names,$contact->{St},' Select State ');
							?>
						</select>					
					</td></tr>
					<tr><td>Zip:</td><td><input type="number" name="Zip" id="Zip" size = "20" class="my-input-text" placeholder="Zip..." value="<?php echo $contact->{Zip} ?>" data-mini="true"></td></tr>
				</tbody>
			</table>			
			</li>
			<li>
			<table style="border-spacing:0;width:100%">
				<tbody>
					<tr><td colspan="2"><textarea data-mini="true" rows="4" name="applNote" id="applNote" placeholder="Notes..."><?php echo $row[0][applNote] ?></textarea></td></tr>
				</tbody>
			</table>			
			</li>

			<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</ul>
		</form>

	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="adoptSuccess" data-dismissible="true" data-theme="a" data-position="#adoptInfoTab">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptError" data-dismissible="false" data-theme="a"  data-position="#adoptInfoTab">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="adoptErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>