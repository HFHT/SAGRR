// $('li[data-desire="AF"]').css("visibility", "hidden");

<!DOCTYPE html> 
<html> 
<head> 
</head>
<body>
<div data-role="page" id="adoptFilter" class="my-multi">
	<script>
	</script>
	<div data-role="header" data-position="fixed" class="my-multi">
		<img src="../img/SAGGR-DBLogo1hdr-320.gif"> 
		<div data-role="navbar" data-iconpos="left" data-theme="b">
			<ul>
				<li><a href="#adoptActivePage" data-icon="back" data-transition="slide" data-direction="reverse">Back</a></li>		
				<li><a href="#home" data-icon="home">Home</a></li>
				<li>&nbsp;</li>
			</ul>
		</div><!-- /navbar -->	
	</div><!-- /header -->

	<div data-role="content" class="ui-content my-multi" role="main">
		<div class="content-secondary" style="margin-top:0px; padding-top: 0px">
			<table>
			<tr><td>Records</td><td class="filterQty">0</td>
			<tr><td>Status</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="Status" id="StatusA" value="P">
				<label for="StatusA">Pending</label>
				<input type="radio" name="Status" id="StatusB" value="W">
				<label for="StatusB">Waiting</label>
				<input type="radio" name="Status" id="StatusC" value="">
				<label for="StatusC">All</label>				
				
				</fieldset>
			</td></tr>				
			<tr><td>Desire</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="Des" id="desireA" value="A">
				<label for="desireA">Adopt</label>
				<input type="radio" name="Des" id="desireB" value="F">
				<label for="desireB">Foster</label>
				<input type="radio" name="Des" id="desireC" value="AF">
				<label for="desireC">Both</label>				
				</fieldset>
			</td></tr>	
			<tr><td>Sex</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="Sex" id="sexM" value="Male">
				<label for="sexM">Male</label>
				<input type="radio" name="Sex" id="sexF" value="Female">
				<label for="sexF">Female</label>
				<input type="radio" name="Sex" id="sexE" value="Either">
				<label for="sexE">Either</label>			
				</fieldset>
			</td></tr>	
			<tr><td>Family Ages</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="child" id="childA" value="">
				<label for="childA">N/A</label>				
				<input type="radio" name="child" id="child3" value="3">
				<label for="child3">3+</label>
				<input type="radio" name="child" id="child5" value="6">
				<label for="child5">6+</label>
				<input type="radio" name="child" id="child6" value="18">
				<label for="child6">18+</label>				
				</fieldset>
			</td></tr>	
			<tr><td>Dogs</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="dogs" id="dogA" value="">
				<label for="dogA">N/A</label>				
				<input type="radio" name="dogs" id="dog0" value="0">
				<label for="dog0">None</label>				
				<input type="radio" name="dogs" id="dog1" value="1">
				<label for="dog1">One</label>
				<input type="radio" name="dogs" id="dog2" value="2">
				<label for="dog2">Multi</label>
				</fieldset>
			</td></tr>
			<tr><td>Cats</td><td>			
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">				
				<input type="radio" name="cats" id="catA" value="">
				<label for="catA">N/A</label>
				<input type="radio" name="cats" id="cat0" value="0">
				<label for="cat0">None</label>
				<input type="radio" name="cats" id="cat1" value="1">
				<label for="cat1">One</label>
				<input type="radio" name="cats" id="cat2" value="2">
				<label for="cat2">Multi</label>				
				</fieldset>
			</td></tr>	
			<tr><td>Hours Gone</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="hours" id="hoursA" value="">
				<label for="hoursA">N/A</label>
				<input type="radio" name="hours" id="hours0" value="0">
				<label for="hours0"><4</label>
				<input type="radio" name="hours" id="hours4" value="4">
				<label for="hours4"><6</label>
				<input type="radio" name="hours" id="hours6" value="6">
				<label for="hours6"><8</label>
				<input type="radio" name="hours" id="hours8" value="8">
				<label for="hours8"><10</label>				
				</fieldset>
			</td></tr>	
			<tr><td>Has Minor Health Issues</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="healthminor" id="hmin" value="">
				<label for="hmin">N/A</label>				
				<input type="radio" name="healthminor" id="hminA" value="Y">
				<label for="hminA">Yes</label>
				<input type="radio" name="healthminor" id="hminB" value="N">
				<label for="hminB">No</label>
				<input type="radio" name="healthminor" id="hminC" value="M">
				<label for="hminC">Maybe</label>
				</fieldset>
			</td></tr>	
			<tr><td>Has Major Health Issues</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="healthmajor" id="hmaj" value="">
				<label for="hmaj">N/A</label>
				<input type="radio" name="healthmajor" id="hmajA" value="Y">
				<label for="hmajA">Yes</label>
				<input type="radio" name="healthmajor" id="hmajB" value="N">
				<label for="hmajB">No</label>
				<input type="radio" name="healthmajor" id="hmajC" value="M">
				<label for="hmajC">Maybe</label>
				</fieldset>
			</td></tr>	
			<tr><td>Has Special Needs</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="spec" id="spec" value="">
				<label for="spec">N/A</label>
				<input type="radio" name="spec" id="specY" value="Y">
				<label for="specY">Yes</label>
				<input type="radio" name="spec" id="specN" value="N">
				<label for="specN">No</label>
				<input type="radio" name="spec" id="specM" value="M">
				<label for="specM">Maybe</label>
				</fieldset>
			</td></tr>	
			<tr><td>Isn't House Broken</td><td> 
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="hb" id="hb" value="">
				<label for="hb">N/A</label>
				<input type="radio" name="hb" id="hbY" value="Y">
				<label for="hbY">Yes</label>
				<input type="radio" name="hb" id="hbN" value="N">
				<label for="hbN">No</label>
				<input type="radio" name="hb" id="hbM" value="M">
				<label for="hbM">Maybe</label>
				</fieldset>
			</td></tr>	
			<tr><td>Has Behavior Issues</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="bi" id="bi" value="">
				<label for="bi">N/A</label>
				<input type="radio" name="bi" id="biY" value="Y">
				<label for="biY">Yes</label>
				<input type="radio" name="bi" id="biN" value="N">
				<label for="biN">No</label>
				<input type="radio" name="bi" id="biM" value="M">
				<label for="biM">Maybe</label>
				</fieldset>
			</td></tr>	
			
			<tr><td colspan="2">
				<table><tr>
				<td>Age</td><td>Min:</td><td><input type="number" name="age1" id="age1" min="0" max="20" value="0"></td><td>Max:</td><td><input type="number" name="age2" id="age2" min="0" max="20" value="20" data-inline="true"></td>
				</tr></table>
			</td></tr>
			<tr><td>Dog Door</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="fdoor" id="fdoor" value="">
				<label for="fdoor">N/A</label>
				<input type="radio" name="fdoor" id="fdoorY" value="Y">
				<label for="fdoorY">Yes</label>
				<input type="radio" name="fdoor" id="fdoorN" value="N">
				<label for="fdoorN">No</label>
				</fieldset>
			</td></tr>	
			<tr><td>Pool</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="fpool" id="fpool" value="">
				<label for="fpool">N/A</label>
				<input type="radio" name="fpool" id="fpoolY" value="Y">
				<label for="fpoolY">Yes</label>
				<input type="radio" name="fpool" id="fpoolN" value="N">
				<label for="fpoolN">No</label>
				</fieldset>
			</td></tr>	
			<tr><td>Fence</td><td>
				<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
				<input type="radio" name="ffence" id="ffence" value="">
				<label for="ffence">N/A</label>
				<input type="radio" name="ffence" id="ffenceY" value="Y">
				<label for="ffenceY">Yes</label>
				<input type="radio" name="ffence" id="ffenceN" value="N">
				<label for="ffenceN">No</label>
				</fieldset>
			</td></tr>				
			</table>
			</div>
		<div class="content-primary" style="margin-top:0px; padding-top: 0px">
				<ul data-role="listview" id="filt_list" data-autodividers="false" data-filter="true" style="">			
				</ul>
	
	</div>

	</div><!-- /content -->	
	<div data-role="footer" data-position="fixed"  class="my-multi">
		<h4>Copyright Southern Az Golden Retriever Rescue 2015</h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>