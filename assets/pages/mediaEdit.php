<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = array();
$s= $_GET['s'];
$mode = $_GET['m'];
if (isset($_GET['x'])) {
	switch ($mode) {
		case 'dog': $where = 'Fk_Dog_id='.$_GET['x']; break;
		case 'member': $where = 'Fk_PeopleT_id='.$_GET['x']; break;
		case 'vet': $where = 'Fk_Vet_id='.$_GET['x']; break;
		default: $where = "FileAsset_id=".$_GET['x'];
	} 
	$prep_stmt = "SELECT a.*,b.DogName,b.SAGRR_id,c.Member_id,c.FirstName,c.LastName,d.SelText,e.VetInvoice,e.fk_VetClinicT FROM FileAsset as a 
					LEFT JOIN DogT as b ON b.DogT_id=a.Fk_Dog_id
					LEFT JOIN PeopleT as c ON c.PeopleT_id=a.Fk_PeopleT_id
					LEFT JOIN VetVisit as e ON e.VetVisit_id=a.Fk_VetVisit_id
					LEFT JOIN VetClinicT as d ON d.SelId=e.fk_VetClinicT					
					WHERE $where AND a.Deleted<>'Y' ORDER BY Caption";	
	if ($stmt = $mysqli->query($prep_stmt)) {
		$row = myFetch($stmt);			
	} else {
        $error = true;;
    }
} else {
	$error = true;;
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
<div data-role="page" id="mediaEdit">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<?php switch($s) { 
					case 'm':
				?>
					<li><a href="#mediaList" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
					<li><a href="#home" data-icon="home">Home</a></li>
					<li>&nbsp;</li>									
				<?php break;
					default:
				?>
					<li class="backBtn"><a href="#" data-icon="back" class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
					<li class="cancelBtn"><a href="#" data-icon="recycle" data-transition="slide" data-direction="reverse">Cancel</a></li>
					<li class="saveMedia MemE DogE" id="saveMedia" style="display:none"><a href="#" data-icon="action" class="myNav">Save</a></li>														
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
				<p>Could not access the Media Database, try again later </p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" id="savemedia_list" data-autodividers="false" data-filter="false">
		<form id="mediaEditForm" class="forms">		
			<li style="padding-bottom:0.1em;padding-top:0.2em">
			<div class="ui-grid-solo">
				<div class="ui-block-a"><div class="ui-bar ui-bar-a">
					<?php if (strpos($row[0]['MIME_Type'],'image/')!== false) {
						$hndlr = 'handleImgClick(this)';
					} else {
						if (strpos($row[0]['MIME_Type'],'application/pdf')!== false) {
							$hndlr = 'handleMediaClick(this)';
						} else {
							$hndlr = 'handleDocClick(this)';
						}
					} ?>
					<img src="<?php echo $row[0]['ThumbName']?>" size="80" class="imgThumb" alt="<?php echo $row[0]['FileName']?>" onclick="<?php echo $hndlr; ?>">

				</div></div>
			</div>
			<div class="ui-grid-a">
				<div class="ui-block-a" style="width:65%"><div class="ui-bar ui-bar-a" style="padding-bottom:0;">
					<input name="Caption" id="Caption" type="text" placeholder="Caption.." data-mini="true" style="display:flex;margin:2px;" value="<?php echo $row[0]['Caption'];?>"/>			
				</div></div>
				<div class="ui-block-b" style="width:35%"><div class="ui-bar ui-bar-a">
					<?php if ($row[0]['attachType']=='Profile') { ?>
					<select name="attachType" id="attachType" data-mini="true" dir="ltr"  data-iconpos="noicon" style="margin:2px;">
						<?php
						$selOpt = ["Profile" => "Profile"];
						dropDownAry($selOpt,$row[0]['attachType'],'Type');
						?>
					</select>
					<?php } else { ?>
					<select name="attachType" id="attachType" data-mini="true" dir="ltr"  data-iconpos="noicon" style="margin:2px;">
						<?php
						$selOpt = ["Photo" => "Photo", "Document" => "Document", "XRay" => "XRay", "Invoice" => "Invoice"];
						dropDownAry($selOpt,$row[0]['attachType'],'Type');
						?>
					</select>
					<?php } ?>
				</div></div>
			</div>
			<div class="ui-grid-solo">
				<div class="ui-block-a"><div class="ui-bar ui-bar-a">
				<textarea name="FileDesc" id="FileDesc" data-mini="true" placeholder="Description.." style="width:95%;"><?php echo $row[0]['FileDesc'];?></textarea>
				</div></div>
			</div>
			</li>
			<li data-role="list-divider" role="heading"></li>			
			<li>
				<table><tbody>
				<tr><th colspan="2">File Information</th><tr>
				<tr><td>File Name:</td><td><?php echo $row[0]['OriginalFileName'];?></td></tr>
				<tr><td>File Date:</td><td><?php echo $row[0]['FileOrigDate'];?></td></tr>				
				<tr><td>Upload by:</td><td><?php echo $row[0]['Uploaded_by'];?></td></tr>
				<tr><td>Upload date:</td><td><?php echo $row[0]['Upload_date'];?></td></tr>
				<tr><td>Member:</td><td><?php echo $row[0]['LastName'];?></td></tr>
				<tr><td>Dog:</td><td><?php echo $row[0]['DogName'];?></td></tr>
				<tr><td>Clinic:</td><td><?php echo $row[0]['SelText'];?></td></tr>
				<tr><td>Visit:</td><td><?php echo $row[0]['Invoice'];?></td></tr>
				</tbody><table>
			</li>
			<input type="hidden" name="FileAsset_id" id="FileAsset_id" value="<?php echo $_GET['x']; ?>">			
		</form>
		</ul>
	<?php
	}
	?>
	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="mediaEditSuccess" data-dismissible="true" data-theme="a">
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $_GET['n'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="mediaEditError" data-dismissible="false" data-theme="a">
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $_GET['n'] ?> failed, please try again later!</p>
			<p id="mediaEditErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
	<div data-role="popup" id="imgPanel" data-overlay-theme="b" data-theme="b" data-position-to="window" data-display="overlay" style="max-width:320px;">
	<div id="imgContainer"></div>
	</div> <!-- /popup -->		
</div><!-- /page -->

</body>
</html>