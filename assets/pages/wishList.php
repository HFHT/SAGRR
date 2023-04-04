<?php
include_once '../inc/functions.php';
sec_session_start();
include_once '../inc/db_connect.php';
$error = false;
$row = array();
$s= $_GET['s'];
if (isset($_GET['l'])) {
	$where = '';
	if ($_GET['l'] != 'all') {
		if ($_GET['l'] == 'vac') {
			if ($_GET['q'] != '') {
				$where = '(DATEDIFF(Rabies, NOW()) BETWEEN 1 AND 59 OR DATEDIFF(DA2PP_DHLPP, NOW()) BETWEEN 1 AND 59 OR DATEDIFF(Bordetella, NOW()) BETWEEN 1 AND 59) AND';			
			} else {
				$where = '(DATEDIFF(Rabies, NOW()) < 0 OR DATEDIFF(DA2PP_DHLPP, NOW()) <0 OR DATEDIFF(Bordetella, NOW()) < 0) AND';
			}
		} else {
			if ($_GET['q'] != '') {
				$where = $_GET['q']."='".$_GET['l']."' AND";
			} else {
				$where = " DogProcStatus='".$_GET['l']."' AND";
			}
		}
	}
	$prep_stmt = "SELECT * FROM WishList $where ORDER BY w_date DESC";
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
var_dump($row[0]);
var_dump($where);
var_dump($_GET);
?>
</head>
<body>
<div data-role="page" id="wishList">
	<script>
	</script>
	<div data-role="header" data-position="fixed">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul> 
				<li><a href="#admin" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>									
				<li><a href="#home" data-icon="home">Home</a></li>
				<li class="addWish" id="addWish"><a href="#" data-icon="plus" class="myNav">Add Item</a></li>
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
		<ul data-role="listview" id="wishlist" data-autodividers="false" data-filter="true">
			<?php
				foreach ($row as $rcd) {
				?>
					<li style="padding-bottom:0.1em;padding-top:0.2em">
					<a href="/assets/pages/wish.php?x=<?php echo $rcd['w_id']; ?>&l=&q=&s=" style="padding-bottom:0;padding-top:0" data-transition="slide">
					<h3><?php echo $rcd['w_by']; ?>&nbsp;&nbsp; <?php echo $rcd['w_date']; ?></h3>
					<p>Priority: <?php echo $rcd['w_sev']; ?>&nbsp;&nbsp;Status: <?php echo $rcd['w_status']; ?></p>
					<p style="white-space:pre-line;"><?php echo $rcd['w_desc'];?></p>
					<p><?php echo $rcd['w_ans'];?></p>
					</a></li>						
			<?php	
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
</div><!-- /page -->

</body>
</html>