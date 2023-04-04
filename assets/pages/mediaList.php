<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';

// error_reporting(E_ALL & ~E_NOTICE);
// ini_set("display_errors", 1);

$error = false;
$row = array();
$s= $_GET['s'];
$l= $_GET['l'];
$mode = $_GET['m'];
if (isset($_GET['s'])) {
	switch ($mode) {
		case 'dog': $where = 'a.Fk_Dog_id='.$_GET['d']; break;
		case 'member': $where = 'a.Fk_PeopleT_id='.$_GET['x']; break;
		case 'vet': $where = 'a.Fk_Vet_id='.$_GET['v']; break;
		case 'visit': $where = 'a.Fk_VetVisit_id='.$_GET['r'];break;
		case 'appl': $where = 'a.fk_applid='.$_GET['f'];break;
		default: $where = "a.attachType='$l'";
	} 
	$prep_stmt = "SELECT a.*,b.DogName,b.SAGRR_id,c.Member_id,c.FirstName,c.LastName,d.SelText,e.VetInvoice,e.fk_VetClinicT FROM FileAsset as a 
					LEFT JOIN DogT as b ON b.DogT_id=a.Fk_Dog_id
					LEFT JOIN PeopleT as c ON c.PeopleT_id=a.Fk_PeopleT_id
					LEFT JOIN VetVisit as e ON e.VetVisit_id=a.Fk_VetVisit_id
					LEFT JOIN VetClinicT as d ON d.SelId=e.fk_VetClinicT
					LEFT JOIN Applications as f ON f.applid=a.fk_applid					
					WHERE $where AND a.Deleted<>'Y' ORDER BY Caption";	
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
var_dump($prep_stmt);
?>
</head>
<body>
<div data-role="page" id="mediaList">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<?php switch($s) { 
					case 'm':
				?>
					<li><a href="#mediaMenu" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>
					<li><a href="#home" data-icon="home">Home</a></li>
					<li>&nbsp;</li>									
				<?php break;
					default:
				?>
					<li class="backBtn"><a href="#" data-icon="back" >Back</a></li>	
					<li><a href="#home" data-icon="home">Home</a></li>
					<li class="MemE DogE" style="display:none"><a href="/assets/pages/media.php?x=<?php echo $_GET['x']; ?>&m=&d=<?php echo $_GET['d'];?>&v=<?php echo $_GET['v'];?>&r=<?php echo $_GET['r'];?>&s=s&f=<?php echo $_GET['f'];?>"" data-icon="plus" class="myNav">Add Media</a></li>
				<?php } ?>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="main" class="ui-content imgOver">
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
		<ul data-role="listview" id="media_list" data-autodividers="false" data-filter="true">
			<?php
				if ($stmt->num_rows == 0) { 
					$ary_msgs = array("No Media matching search criteria."); ?>
					<li><p style="font-size: 1em;font-style: italic;"><?php echo $ary_msgs[0];?></p></li>
				<?php
				} else {
					foreach ($row as $rcd) {
					$imgSubject ='';
					if ($rcd['DogName']!='') {
						$imgSubject	= 'Dog: '.$rcd['DogName'].'&nbsp;#:'.$rcd['SAGRR_id']; 
					}
					if ($rcd['LastName']!='') {
						$imgSubject	= 'Member: '.$rcd['LastName'].', '.$rcd['FirstName'].'&nbsp;#:'.$rcd['Member_id'];
					}
					if ($rcd['SelText']!='') {
						$imgSubject	= 'Vet: '.$rcd['SelText'].'&nbsp;#:'.$rcd['VetInvoice'];
					}
					if ($rcd['applContact']!='') {
						$contact = json_decode($rcd['applContact']);
						$imgSubject = 'Applicant: '.$contact->{'LName'}.', '.$contact->{'Fname'}.'&nbsp;#:'.$rcd['applDocId'];
					}
				?>
					<li style="padding-bottom:0.1em;padding-top:0.2em">
					<div class="ui-grid-a">
					<div class="ui-block-a" style="width:30%"><div class="ui-bar ui-bar-a" id="mediaImg" style="padding-bottom:0;">
					<div class="ui-block-a"><div class="ui-bar ui-bar-a">
					<?php if (strpos($rcd['MIME_Type'],'image/')!== false) {
						$hndlr = 'handleImgClick(this)';
					} else {
						if (strpos($rcd['MIME_Type'],'application/pdf')!== false) {
							$hndlr = 'handleMediaClick(this)';
						} else {
							$hndlr = 'handleDocClick(this)';
						}
					} ?>					
					<img src="<?php echo $rcd['ThumbName']?>" size="80" class="imgThumb" alt="<?php echo $rcd['FileName']?>" onclick="<?php echo $hndlr; ?>">
					</div></div>
					</div></div>
					<a href="/assets/pages/mediaEdit.php?x=<?php echo $rcd['FileAsset_id']; ?>&m=&s=s" style="padding-bottom:0.7em;padding-top:0" data-transition="slide">
					<div class="ui-block-b my-sel-icon my-edit-icon" style="width:70%"><div class="ui-bar ui-bar-a">
					<p style="font-size:1em;margin:2px;"><?php echo $imgSubject;?></p>
					<p style="font-size:1em;margin:2px;"><?php echo $rcd['FileOrigDate'];?></p>
					<p style="font-size:1em;margin:2px;">Type: <?php echo $rcd['attachType'];?></p>
					<p style="font-size:1em;margin:2px;white-space:pre-line;"><?php echo $rcd['Caption'];?></p>
					</div></div>
					</a>
					</div>
					<div class="ui-grid-solo">
					<div class="ui-block-a"><div class="ui-bar ui-bar-a"><p style="font-size:1em;margin:2px;white-space:pre-line;"><?php echo $rcd['FileDesc'];?></p></div></div>
					</div>
					</li>						
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
	
	<div data-role="popup" id="imgPanel" data-overlay-theme="b" data-theme="b" data-display="overlay" style="max-width:320px;">
	<div id="imgContainer"></div>
	</div> <!-- /popup -->	

	<div data-role="popup" id="mediaPanel" data-overlay-theme="a" data-theme="a" data-display="overlay" style="max-width:320px;">
	<iframe src="" id="mediaFrame" seamless="" style="width:100%;height:100%;background-image:url(../img/backdown.png);background-repeat:no-repeat;background-position:top center;"></frame>
	</div> <!-- /popup -->	

	
</div><!-- /page -->

</body>
</html>