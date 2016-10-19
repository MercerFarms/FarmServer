<?php
    header('Content-Type: text/xml');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<results>";

    //Customer#: 83629660
    //PWD: Edgar11MVP
    
	$hostname="localhost";
	$username="farmadmin";
	$password="Prosser#1";
	$dbname="farmdata";

	mysql_connect($hostname,$username, $password) or die ("<html><script language='JavaScript'>alert('Unable to connect to database! Please try again later.'),history.go(-1)</script></html>");
	mysql_select_db($dbname);

	if (isset($_POST['Field'])) {
		$sql = "insert into FuelService (FuelOperatorID, OperatorID, UnitID, ImplementID, Field, UnitHours, ServiceDue, Diesel, DEF, MotorOil, HydraulicOil, CabInspection, Timestamp) ";
		$sql .= "values (" . mysql_real_escape_string($_POST['FuelID']);
		$sql .= ", " . mysql_real_escape_string($_POST['OpID']);
		$sql .= ", '" . mysql_real_escape_string($_POST['UnitID']) . "'";
		$sql .= ", '" . mysql_real_escape_string($_POST['ImpID']) . "'";
		$sql .= ", '" . mysql_real_escape_string($_POST['Field']) . "'";
		$sql .= ", " . mysql_real_escape_string($_POST['UnitHours']);
		$sql .= ", '" . mysql_real_escape_string($_POST['ServiceDue']) . "'";
		$sql .= ", " . mysql_real_escape_string($_POST['Diesel']);
		$sql .= ", " . mysql_real_escape_string($_POST['DEF']);
		$sql .= ", " . mysql_real_escape_string($_POST['MotorOil']);
		$sql .= ", " . mysql_real_escape_string($_POST['HydraulicOil']);
		$sql .= ", " . mysql_real_escape_string($_POST['CabInsp']);
		$sql .= ", '" . mysql_real_escape_string($_POST['Time']) . "'";
		$sql .= ");";
	}
	else if (isset($_POST['StartMeter'])) {
		$sql = "insert into TruckFueling (TruckID, StartMeter, Day) ";
		$sql .= "values ('" . mysql_real_escape_string($_POST['TruckID']) . "'";
		$sql .= ", " . mysql_real_escape_string($_POST['StartMeter']);
		$sql .= ", '" . mysql_real_escape_string($_POST['Day']) . "'";
		$sql .= ");";
	}
	else if (isset($_POST['EndMeter'])) {
		$sql = "update TruckFueling set EndMeter='".mysql_real_escape_string($_POST['EndMeter'])."' where TruckID='".mysql_real_escape_string($_POST['TruckID'])."' and Day='".mysql_real_escape_string($_POST['Day'])."';";
	}
	echo "<sql>".$sql."</sql>";
	
	if (strlen($sql) > 0) {
		$res = mysql_query($sql);
		if (!$res) echo "<err>" . mysql_error() . "</err>";
	}

	echo "success";
	echo "</results>";
?>
