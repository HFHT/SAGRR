<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;

if (isset($_GET['x'])) {
	$pid = $_GET['x'];
	$vid = $_GET['v'];
	$did = $_GET['d'];
	$rid = $_GET['r'];
	$fid = $_GET['f'];
	$mode = $_GET['m'];	
	if ($mode=='dog' || $mode=='vet' || $mode=='member') {
		$multiple = '';
		$mm = 'p';
	} else {
//		$multiple = 'multiple';								//!!!! Need to fix bug associated with handling multiple file upload getting mixed up
		$multiple = '';
		$mm = '';
	}
} else {
	$error = true;
}
?>
<!DOCTYPE html> 
<html> 
<head> 
</head>
<body>
<div data-role="page" id="media">
	<script>
	var mediamode = '<?php echo $mm; ?>';
	</script>
	<form id="mediaForm" class="forms" enctype="multipart/form-data">
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" class="myNav" data-transition="slide" data-direction="reverse">Back</a></li>	
				<li class="cancelBtn"><a href="#" data-icon="recycle" data-transition="slide" data-direction="reverse">Cancel</a></li>				
				<!--li><a href="#home" data-icon="back" class="myNav" data-transition="slide" data-direction="reverse">Back</a></li-->	
				<!--li><a href="#home" data-icon="recycle" data-transition="slide" data-direction="reverse">Cancel</a></li-->	
				<li id="mediaSaveBtn"><a href="#media" data-icon="action" class="myNav">Save</a></li>			
			</ul>
		</div><!-- /navbar -->
		<div>
			<ul data-role="listview" id="mediaListy" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li data-role="list-divider" role="heading" style="font-size:initial;">Media <?php echo $_GET['n'] ?></li>			
			<li>
			<div id="drop_zone">
			<input type="file" id="files" name="file[]" <?php echo $multiple;?> style="min-height:5em;background-image:url(../img/backdrop.png);background-repeat:no-repeat;background-position-y:bottom;"/>
			</div>
			</li>
		</div>
	</div><!-- /header -->

	<div data-role="main" class="ui-content my-narrow">	<?php
	if ($error) {
	?>
	<script>
		$('#drop_zone').hide();
	</script>
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
			<ul data-role="listview" id="mediaListx" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
			<li>
				<ul data-role="listview" id="mediaList" data-autodividers="false" data-filter="false" style="margin-top: -0.5em;">
				<!-- javascript manages this area -->
				</ul>
			</li>
			</ul>			
			<input type="hidden" name="Fk_PeopleT_id" id="Fk_PeopleT_id" value="<?php echo $pid ?>">			
			<input type="hidden" name="Fk_Dog_id" id="Fk_Dog_id" value="<?php echo $did ?>">
			<input type="hidden" name="Fk_Vet_id" id="Fk_Vet_id" value="<?php echo $vid ?>">
			<input type="hidden" name="Fk_VetVisit_id" id="Fk_VetVisit_id" value="<?php echo $rid ?>">
			<input type="hidden" name="fk_applid" id="fk_applid" value="<?php echo $fid ?>">
			<input type="hidden" name="mediamode" id="mediamode" value="<?php echo $mode ?>">	
	<?php
	}
	?>
	</div><!-- /content -->	
	</form>
	<div data-role="footer" data-position="fixed">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
	
	<div data-role="popup" id="mediaSuccess" data-dismissible="true" data-theme="a" data-position="#mediaForm">
		<div data-role="header" data-theme="a">
		<h1>Upload Successful</h1>
		</div>
		<div data-theme="a">
			<p>Files were uploaded to the site.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="mediaError" data-dismissible="false" data-theme="a" data-position="#mediaForm">
		<div data-role="header" data-theme="b">
		<h1>Upload Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The file upload failed!</p>
			<p id="mediaErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	

	<div data-role="popup" id="filecount" data-dismissible="false" data-overlay-theme="b" data-theme="b" data-position-to="window" style="max-width:320px;">
		<div data-role="header" data-theme="b">
		<h1>Too many files</h1>
		</div>
		<div data-theme="a">
			<!--p>There can only be one profile picture, please reselect the one you want.</p-->
			<p>You can only upload one file at a time, please select the one you want.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
	
</div><!-- /page -->

</body>
</html>