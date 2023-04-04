<?php
// Refer to ListDash.php for a good example of using php to create an object from reading SQL.

// Following code reads in the columns and sets a row array to the Default values from the table
	$prep_stmt = "SHOW COLUMNS FROM DogT";
	if ($stmt = $mysqli->query($prep_stmt)) {
		while ($col = $stmt->fetch_array(MYSQLI_ASSOC)) {
			echo $col['Field'].', ';
			$field = $col['Field'];
			if ($col['Default']=='NULL') {
				$row[0][$col['Field']]='';
			} else {
				$row[0][$col['Field']]=$col['Default'];
			}
		}
		$row[0]['DogT_id'] = 0;
		$row[0]['Rabies'] = $row[0]['DA2PP_DHLPP'] = $row[0]['Bordetella'] = 365;  
		$visitCnt = $statusCnt = $mediaCnt = $beh = 0;
	} else {
		$error = true;
	}
?>