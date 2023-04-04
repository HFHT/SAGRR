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
    $prep_stmt = "SELECT * FROM WishList WHERE w_id = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
	} else {
        $error = true;
    }
} else {
	$error = true;
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
function checkBox($decision) {
	if ($decision==1) {
		echo ('checked');
	}
}
function dropDownAry($SelAry,$Selected,$SelText) {
	echo "<option value='0'>-$SelText-</option>";
	foreach ($SelAry as $key=>$value) {
		if ($Selected == $key) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='".$key."' ".$strSel.">".$value."</option>";
	}
}

?>
<!DOCTYPE html> 
<html> 
<head> 
<?php
var_dump($row);
?>
</head>
<body>
<div data-role="page" id="wishInfo" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" >Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle">Cancel</a></li>	
				<li id="wishSaveBtn"><a href="#" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">

		<form id="wishEditForm" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li><div id="valErr"></div>
				<h3>Describe problem or wishlist item</h3>
				<table style="border-spacing:0;width:100%">
					<tbody>
						<tr><td><textarea name="w_desc" id="w_desc" data-mini="true" placeholder="Description..."><?php echo $row[0]['w_desc']; ?></textarea></td></tr>
					</tbody>
				</table>		
				<h3>Priority</h3>
				<table style="border-spacing:0;width:100%"><tbody>				
					<tr><td style="width:50%;">Bug:</td><td><input type="checkbox" data-role="flipswitch" name="w_bug" id="w_bug" data-on-text="Yes" data-off-text="No" data-mini="true" data-wrapper-class="" value="Y" <?php checkBox($row[0]['w_bug']=='Y');?>/></td></tr>
					<tr><td>Priority:</td><td>
						<select name="w_sev" id="w_sev" data-mini="true">
							<?php
							$selOpt = ["1" => "1. High - Showstopper", "2" => "2. Medium - Can workaround", "3" => "3. Low - Inconvenient", "4" => "4. Suggestion - Could help"];
							dropDownAry($selOpt,$row[0]['w_sev'],'Select');
							?>
						</select>								
					</td></tr>
				</tbody></table>
			</li>
			<li>
				<h3>Status and Response</h3>
				<p>This section can only be updated by the web site developer</p>
				<table style="border-spacing:0;width:100%"><tbody>
					<tr><td>
						<fieldset id="w_statusBtn" data-role="controlgroup" data-type="horizontal" data-mini="true">
							<input type="radio" name="w_status" id="SO" value="Open" <?php checkBox($row[0]['w_status']=='Open');?> ><label for="SO">Open</label>
							<input type="radio" name="w_status" id="SW" value="Working" <?php checkBox($row[0]['w_status']=='Working');?>><label for="SW">Working</label>
							<input type="radio" name="w_status" id="SC" value="Closed" <?php checkBox($row[0]['w_status']=='Closed');?>><label for="SC">Closed</label>							
							<input type="radio" name="w_status" id="SR" value="Reject" <?php checkBox($row[0]['w_status']=='Reject');?>><label for="SR">Reject</label>							
						</fieldset>
					</td></tr>
					<tr><td><textarea name="w_ans" id="w_ans" data-mini="true" placeholder="Response..."><?php echo $row[0]['w_ans']; ?></textarea></td></tr>
				</tbody></table>
			</li>			
			</ul>
			<input type="hidden" name="w_id" id="w_id" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="wishSuccess" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p>Request record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="wishError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for the Request failed, please try again later!</p>
			<p id="wishErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>