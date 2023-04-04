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
    $prep_stmt = "SELECT a.applid,a.applDocId,a.applProgress,a.ApplSec6,b.PeopleT_id,b.Member_id,b.FirstName,b.LastName
				FROM Applications AS a LEFT JOIN PeopleT AS b ON (b.PeopleT_id=a.fk_PeopleT_id) WHERE applid = $id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$applSec = json_decode($row[0]['ApplSec6']);
		$applProgress = json_decode($row[0]['applProgress']);
	} else {
        $error = true;
    }
} else {
	$error = true;
}
function checkBox($decision) {
	if ($decision==1) {
		echo ('checked');
	}
}
function dropDownAry($SelAry,$Selected,$SelText) {
	echo "<option value='0'>-$SelText-</option>";
	foreach ($SelAry as $key=>$value) {
		if ($Selected == $value) {
			$strSel = "Selected";
		} else {
			$strSel = "";
		}
		echo "<option value='".$value."' ".$strSel.">".$value."</option>";
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
<div data-role="page" id="adoptS6" data-dom-cache="false">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="" data-icon="back" class="myNav">Back</a></li>	
				<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>	
				<li id="adoptS6SaveBtn" class="ApplE" style="display:none"><a href="#adoptS6" data-icon="action" class="myNav">Save</a></li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">
		<form id="adoptS6Form" class="forms">
			<ul data-role="listview" data-autodividers="false" data-filter="false">
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3><?php echo $applProgress[6]-> {'Sec'} ?></h3></li>
			<li>
				<table><tbody>
				<tr><td colspan="2" style="font-weight:600;">Fee Structure</td></tr>
				<tr><td>$300</td><td>0 through 3 years old</td></tr>
				<tr><td>$250</td><td>4 through 7 years old</td></tr>
				<tr><td>$150</td><td>8 years and over</td></tr>
				</tbody></table>
				<p>*Goldens with special needs will be considered on an individual basis</p>
				<input type="checkbox" name="Ans46" id="Ans46" value="Y" <?php checkBox($applSec->{'Ans46'}=='Y');?> data-mini="false" data-iconpos="right">
				<label for="Ans46" class="my-left-8">I acknowledge that I have read and understand the fee structure<label>
			</li>
			</ul>
			<input type="hidden" name="v" id="v" value="<?php echo $id ?>">
			<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->			
		</form>
	</div><!-- /content -->

	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="adoptS6Success" data-dismissible="true" data-theme="a" data-position="#adoptS6Form">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptS6Error" data-dismissible="false" data-theme="a"  data-position="#adoptS6Form">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="adoptS6ErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
</div><!-- /page -->

</body>
</html>