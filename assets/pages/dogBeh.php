<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$vetVisit = '';
$row = array();
$today = date("Y-m-d");
$id = $_GET['x'];
if (isset($_GET['x'])) {
	$prep_stmt = "SELECT Behaviors FROM DogT WHERE DogT_id=$id";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);
		$behaviors = json_decode($row[0]['Behaviors']);
	} else {
		$error = true;
	}
} else {
	$error = true;
}
function checkBox($DBConn, $DBtable, $Selected)
{
	$dropSQL = "SELECT * FROM $DBtable ORDER BY SelDesc DESC, SelText";
	if ($dropResp = $DBConn->query($dropSQL)) {
		$strSelY = $strSelN = '';
		while ($dropRow = $dropResp->fetch_object()) {
			if ($dropRow->SelDesc == 'Y') {
				$strStyle = ' STYLE="color:green;line-height:1em;font-size:0.9em;" ';
			} else {
				$strStyle = ' STYLE="color:red;line-height:1em;font-size:0.9em;" ';
			}
			if (in_array($dropRow->SelId, $Selected)) {
				$strSel = "checked";
				$strSelY = $strSelY . '<input type="checkbox" name="beh[]" id="b' . $dropRow->SelId . '" value="' . $dropRow->SelId . '" ' . $strSel . '><label for="b' . $dropRow->SelId . '"' . $strStyle . '>' . $dropRow->SelText . '</label>';
			} else {
				$strSel = "";
				$strSelN = $strSelN . '<input type="checkbox" name="beh[]" id="b' . $dropRow->SelId . '" value="' . $dropRow->SelId . '" ' . $strSel . '><label for="b' . $dropRow->SelId . '"' . $strStyle . '>' . $dropRow->SelText . '</label>';
			}
		}
		echo $strSelY . $strSelN;
	} else {
		echo "<option value='0'>!!DB Error!!</option>";
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
	<div data-role="page" id="dogBeh">
		<script>
		</script>
		<div data-role="header" data-position="fixed">
			<img src="../img/SAGGR-DBLogo1hdr-320.gif">
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li class="backBtn"><a href="#" data-icon="back" class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>
					<li class="cancelBtn"><a href="#" data-icon="recycle" data-transition="slide" data-direction="reverse">Cancel</a></li>
					<li id="dogBehSaveBtn" class="DogE" style="display:none"><a href="#" data-icon="action" class="myNav">Save</a></li>
				</ul>
			</div><!-- /navbar -->
		</div><!-- /header -->

		<div data-role="main" class="ui-content my-narrow">
			<?php
			if ($error) {
			?>
				<div class="ui-corner-all">
					<div class="ui-bar ui-bar-a">
						<h3>Error Occurred</h3>
					</div>
					<div class="ui-body ui-body-a">
						<p>Could not access the Dog Database, try again later </p>
					</div>
				</div>
			<?php
			} else {
			?>
				<form id="DogBehForm" class="forms">
					<ul data-role="listview" id="dog_behupdate" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
						<li data-role="list-divider" role="heading" style="font-size:initial;">Behaviors for <?php echo $_GET['n'] ?></li>
						<li>
							<fieldset data-role="controlgroup">
								<?php
								checkBox($mysqli, 'BehaviorT', $behaviors->{'Beh'});
								?>
							</fieldset>
							<input type="hidden" name="DogT_id" id="DogT_id" value="<?php echo $id ?>">
							<!--input name="SubmitBtn" id="SubmitBtn" type="submit" class="SubmitBtn"-->
						</li>
				</form>
			<?php
			}
			?>
		</div><!-- /content -->
		<div data-role="footer" data-position="fixed">
			<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
		</div><!-- /footer -->

		<div data-role="popup" id="dogBehSuccess" data-dismissible="true" data-theme="a">
			<div data-role="header" data-theme="a">
				<h1>Update Successful</h1>
			</div>
			<div data-theme="a">
				<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
				<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>
			</div>
		</div> <!-- /popup -->

		<div data-role="popup" id="dogBehError" data-dismissible="false" data-theme="a">
			<div data-role="header" data-theme="b">
				<h1>Update Failed!</h1>
			</div>
			<div data-theme="a">
				<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
				<p id="dogBehErrorText" class="myErrMsg"></p>
				<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
			</div>
		</div> <!-- /popup -->

	</div><!-- /page -->

</body>

</html>