<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$arySQL = array("all"=>"",
				"vx"=>"(DATEDIFF(Rabies, NOW()) < 0 OR DATEDIFF(DA2PP_DHLPP, NOW()) <0 OR DATEDIFF(Bordetella, NOW()) < 0) AND",
				"ve"=>"(DATEDIFF(Rabies, NOW()) BETWEEN 1 AND {q} OR DATEDIFF(DA2PP_DHLPP, NOW()) BETWEEN 1 AND {q} OR DATEDIFF(Bordetella, NOW()) BETWEEN 1 AND {q}) AND",
				"cm"=>"DogCurMemNo='{q}' AND",
				"ps"=>"DogProcStatus='{q}' AND",
				"cs"=>"DogCurStatus='{q}' AND",
				"ok"=>"fk_applid={q} AND",
				"oka"=>"fk_applid={q} AND DogProcStatus='Adopted' AND ",
				"okf"=>"fk_applid={q} AND DogProcStatus='Fostered' AND ",
				"okt"=>"fk_applid={q} AND DogProcStatus='Match-Trial' AND ",
				"mema"=>"fk_PeopleT_id={q} AND DogProcStatus='Adopted' AND ",
				"memf"=>"fk_PeopleT_id={q} AND DogProcStatus='Fostered' AND ",
				"memt"=>"fk_PeopleT_id={q} AND DogProcStatus='Match-Trial' AND ",				
				"ic"=>"Deleted='X' AND",
				"av"=>"DogCurStatus NOT IN('NotAvailable','Bridge','NCI') AND"
				);
$aryMsg = array("oka"=>["Dogs Available for Adoption","Matched-Adopted","Adopted"],
				"okf"=>["Dogs Available for Fostering","Matched-Foster","Fostered"],
				"okt"=>["Dogs Available for Trial Match","Match-Trial","Match-Trial"],
				"mema"=>["Dogs Available for Adoption","Matched-Adopted","Adopted"],
				"memf"=>["Dogs Available for Fostering","Matched-Foster","Fostered"],
				"memt"=>["Dogs Available for Trial Match","Match-Trial","Match-Trial"]);
$error = false;
$dogs = $adopt = array();
if (isset($_GET['l'])) {
	$where = str_replace('{q}',$_GET['q'],$arySQL[$_GET['l']]);
	if ($_GET['l']=='mema' || $_GET['l']=='memf' || $_GET['l'] =='memt') {$field='fk_PeopleT_id';} else { $field='fk_applid';}
	$prep_stmt = "SELECT DogT_id,SAGRR_id,DogProcStatus,DogCurStatus,DogCurMember,DogCurMemNo,DogName,DogPhotoLink,TIMESTAMPDIFF(YEAR,Birthdate,CURDATE()) AS age,Medical,Behaviors,Weight,DogSex,DogBlob,fk_ColorT_id,d.SelText AS color FROM DogT AS a LEFT JOIN ColorT AS d ON d.SelId=a.fk_ColorT_id WHERE $where Deleted <> 'Y' ORDER BY DogName";
	if ($stmt = $mysqli->query($prep_stmt)) {
		$dogs = myFetch($stmt);
		$dogi = $stmt->num_rows;
		if ($dogi==0) {$dogs[0] = array();}
		$where = $arySQL['av']; /* Need to fix this, I think it will include NCI and Bridge */
		$prep_stmt = "SELECT DogT_id,SAGRR_id,DogProcStatus,DogCurStatus,DogCurMember,DogCurMemNo,DogName,DogPhotoLink,TIMESTAMPDIFF(YEAR,Birthdate,CURDATE()) AS age,Medical,Behaviors,Weight,DogSex,DogBlob,fk_ColorT_id,d.SelText AS color FROM DogT AS a LEFT JOIN ColorT AS d ON d.SelId=a.fk_ColorT_id WHERE $where Deleted <> 'Y' ORDER BY DogName";
		if ($stmt = $mysqli->query($prep_stmt)) {
			$adopt = myFetch($stmt);
			$adopti = $stmt->num_rows;
		} else {
			$error = true;;
		}	
	} else {
        $error = true;;
    }
} else {
	$error = true;;
}
function my_search($needle,$aryHaystack) {
	foreach($aryHaystack as $mkey=>$mval) {
		if ($needle==$mval['DogT_id']) {return true;}
	}
	return false;
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
<div data-role="page" id="adoptDog">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li class="backBtn"><a href="#" data-icon="back" >Back</a></li>	
				<li class="cancelBtn"><a href="" data-icon="recycle">Cancel</a></li>	
				<li class="saveAdoption ApplE" style="display:none" id="saveAdoption"><a href="#" data-icon="plus" class="myNav ui-btn-icon-right">Save</a></li>
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
				<p>Could not access the Dog Database, try again later </p>
				<p><?php echo $mysqli->error;?><br>
					<?php echo $prep_stmt;?></p>
			</div>
		</div>
	<?php
	} else {
	?>
		<ul data-role="listview" data-autodividers="false" data-filter="false" style="margin-bottom:0.5em;" class="adoptlists" data-fk_applid="<?php echo $_GET['q']; ?>" data-applmode="<?php echo $_GET['l']; ?>"  data-family="<?php echo $_GET['n'];?>" data-state="<?php echo $_GET['s'];?>" data-memorappl="<?php echo $field;?>" data-dogcnt="<?php echo $_GET['a'];?> ">
			<li data-role="list-divider" role="heading"  style="font-size:initial;">Dogs Assigned to:&nbsp;<?php echo $_GET['n']; ?></li>
			<li  id="dogAssign" style="<?php echo $dogi==0?'':'display:none;';?>"><p style="font-size: 1em;font-style: italic;">No Dogs assigned to Applicant.</p></li>
			<?php
			if ($dogi>0) {
			foreach ($dogs as $rcd) {
				$med = json_decode($rcd['Medical']);
				$beh = json_decode($rcd['Behaviors']);
				$b = in_array('19',$beh->{'Beh'})==true ? 'dog ':'';
				$b = $b.(in_array('20',$beh->{'Beh'})==true ? 'cat ':'');
				$b = $b.(in_array('21',$beh->{'Beh'})==true ? 'kid ':'');				
			?>
				<li data-icon="arrow-d">
					<a href="#" data-transition="slide" style="padding:0.5em 1em;background-color:#F0FACF;" class="noDog assigned" data-dogkey="<?php echo $rcd['DogT_id'];?>" data-dogname="<?php echo $rcd['DogName'];?>">	
					<table style="border-spacing:0;width:100%;font-weight:400;font-size:.9em"><tbody>
					<tr><td style="font-weight:600"><?php echo $rcd['DogName']; ?>&nbsp;&nbsp; #: <?php echo $rcd['SAGRR_id']; ?></td></tr>
					<tr><td>Status: <?php echo $rcd['DogProcStatus']; ?> : <?php echo $rcd['DogCurStatus']; ?></td></tr>
					<tr><td>Age: <?php echo $rcd['age']; ?>,&nbsp;Sex: <?php echo $rcd['DogSex']; ?>,&nbsp;Color: <?php echo $rcd['fk_ColorT_id']=='0' ? '' : $rcd['color']; ?>,&nbsp;Medical: <?php echo $med->{'MedicalClass'}; ?></td></tr>
					<tr><td>Good With: <?php echo $b; ?></td></tr>
					<tr><td style="font-size:.8em"><?php echo $rcd['DogBlob']; ?>
					</tbody></table>
				</a></li>						
			<?php	
			}
			}
			?>
			<div id="nodogsel" class="myErrMsg"></div>
			<li></li>
			<li data-role="list-divider" role="heading" style="font-size:initial;"><h3><?php echo $aryMsg[$_GET['l']][0]; ?></h3></li>
				<li id="dogAvail" style="<?php echo $adopti==0?'':'display:none;';?>><p style="font-size: 1em;font-style: italic;">No Dogs are available at this time.</p></li>
				<?php
				foreach ($adopt as $key=>$rcd) {
					if (my_search($rcd['DogT_id'],$dogs)==false) {
						$med = json_decode($rcd['Medical']);
						$beh = json_decode($rcd['Behaviors']);
						$b = in_array('19',$beh->{'Beh'})==true ? 'dog ':'';
						$b = $b.(in_array('20',$beh->{'Beh'})==true ? 'cat ':'');
						$b = $b.(in_array('21',$beh->{'Beh'})==true ? 'kid ':'');				
				?>
				<li data-icon="arrow-u" data-inarray="<?php echo in_array($rcd['DogT_id'],$dogs[0])==false?'F':'T'; ?>">
					<a href="#" data-transition="slide" style="padding:0.5em 1em;" class="yesDog available" data-dogkey="<?php echo $rcd['DogT_id'];?>" data-dogname="<?php echo $rcd['DogName'];?>">	
					<table style="border-spacing:0;width:100%;font-weight:400;font-size:.9em"><tbody>
					<tr><td style="font-weight:600"><?php echo $rcd['DogName']; ?>&nbsp;&nbsp; #: <?php echo $rcd['SAGRR_id']; ?></td></tr>
					<tr><td>Status: <?php echo $rcd['DogProcStatus']; ?> : <?php echo $rcd['DogCurStatus']; ?></td></tr>
					<tr><td>Age: <?php echo $rcd['age']; ?>,&nbsp;Sex: <?php echo $rcd['DogSex']; ?>,&nbsp;Color: <?php echo $rcd['fk_ColorT_id']=='0' ? '' : $rcd['color']; ?>,&nbsp;Medical: <?php echo $med->{'MedicalClass'}; ?></td></tr>
					<tr><td>Good With: <?php echo $b;?></td></tr>
					<tr><td style="font-size:.8em"><?php echo $rcd['DogBlob']; ?>
					</tbody></table>
				</a></li>						
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

	<div data-role="popup" id="adoptionConfirm" data-dismissible="true" data-theme="a" >
		<div data-role="header" data-theme="a">
		<h1>Please confirm this assignment</h1>
		</div>
		<div data-theme="a" style="margin:5px;">
			<p>The selected dogs will be assigned to the applicant:</p>
			<ul style="padding-left:20px;"><li>The application updated to <b><i class="applStatus"><?php echo $aryMsg[$_GET['l']][1];?></i></b></li><li>The dogs updated to <b><i><?php echo $aryMsg[$_GET['l']][2];?></i></b></li></ul>
			<p>This action cannot be undone.</p>
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li class="myNav"><a href="#" data-icon="delete" data-rel="back"  class="myNav">Cancel</a></li>	
					<li  class="myNav confAdoption"><a href="#" data-icon="check" id="adoptBtn" class="myNav confAdoption">Confirm</a></li>
				</ul>
			</div><!-- /navbar -->			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="reassignConfirm" data-dismissible="true" data-theme="a" >
		<div data-role="header" data-theme="a">
		<h1>Please confirm this return</h1>
		</div>
		<div data-theme="a" style="margin:5px;">
			<p>The selected dogs will be returned:</p>
			<ul style="padding-left:20px;">
			<li>The selected dogs will be <b><i>Available</i></b></li>
			<li>The application updated to <b><i>WaitList</i></b></li>
			</ul>
			<p>This action cannot be undone.</p>
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li class="myNav"><a href="#" data-icon="delete" data-rel="back"  class="myNav">Cancel</a></li>	
					<li  class="myNav confAdoption"><a href="#" data-icon="check" id="reassignBtn" class="myNav confAdoption">Confirm</a></li>
				</ul>
			</div><!-- /navbar -->			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="swapdogConfirm" data-dismissible="true" data-theme="a" >
		<div data-role="header" data-theme="a">
		<h1>Please confirm this dog swap</h1>
		</div>
		<div data-theme="a" style="margin:5px;">
			<p>The selected dogs will be reassigned:</p>
			<ul style="padding-left:20px;">
			<li>The returned dogs will be <b><i>Available</i></b></li>
			<li>The assigned dogs updated to <b><i><?php echo $aryMsg[$_GET['l']][2];?></i></b></li>
			<li>The application updated to <b><i class="applStatus"><?php echo $aryMsg[$_GET['l']][1];?></i></b></li>
			</ul>
			<p>This action cannot be undone.</p>
			<div data-role="navbar" data-iconpos="left" data-theme="b">
				<ul>
					<li class="myNav"><a href="#" data-icon="delete" data-rel="back"  class="myNav">Cancel</a></li>	
					<li class="myNav confAdoption"><a href="#" data-icon="check" id="swapBtn"  class="myNav confAdoption">Confirm</a></li>
				</ul>
			</div><!-- /navbar -->			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptionSuccess" data-dismissible="true" data-theme="a" >
		<div data-role="header" data-theme="a">
		<h1>Update Successful</h1>
		</div>
		<div data-theme="a">
			<p><?php echo $row[0]['FirstName'] ?>'s record was successfully updated.</p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-a">Close</a>			
		</div>
	</div> <!-- /popup -->
	
	<div data-role="popup" id="adoptionError" data-dismissible="false" data-theme="a" >
		<div data-role="header" data-theme="b">
		<h1>Update Failed!</h1>
		</div>
		<div data-theme="a">
			<p>The update for <?php echo $row[0]['FirstName'] ?> failed, please try again later!</p>
			<p id="adoptionErrorText" class="myErrMsg"></p>
			<a href="#" data-rel="back" data-icon="delete" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b">Close</a>
		</div>
	</div> <!-- /popup -->	
	
	
</div><!-- /page -->

</body>
</html>