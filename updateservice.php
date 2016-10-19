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
		$sql = "update FuelService set FuelOperatorID=".mysql_real_escape_string($_POST['FuelID']).", OperatorID=".mysql_real_escape_string($_POST['OpID']);
		$sql .= ", ImplementID='" . mysql_real_escape_string($_POST['ImpID']) . "'";
		$sql .= ", Field='" . mysql_real_escape_string($_POST['Field']) . "'";
		$sql .= ", UnitHours=" . mysql_real_escape_string($_POST['UnitHours']);
		$sql .= ", ServiceDue='" . mysql_real_escape_string($_POST['ServiceDue']) . "'";
		$sql .= ", Diesel=" . mysql_real_escape_string($_POST['Diesel']);
		$sql .= ", DEF=" . mysql_real_escape_string($_POST['DEF']);
		$sql .= ", MotorOil=" . mysql_real_escape_string($_POST['MotorOil']);
		$sql .= ", HydraulicOil=" . mysql_real_escape_string($_POST['HydraulicOil']);
		$sql .= ", CabInspection=" . mysql_real_escape_string($_POST['CabInsp']);
		$sql .= ", Timestamp='" . mysql_real_escape_string($_POST['Time']) . "'";
		$sql .= " where ID=".mysql_real_escape_string($_POST['ID']).";";
	}
	else if (isset($_POST['StartMeter'])) {
		$sql = "update TruckFueling set TruckID='" . mysql_real_escape_string($_POST['TruckID']) . "'";
		$sql .= ", StartMeter=" . mysql_real_escape_string($_POST['StartMeter']);
		$sql .= ", Day='" . mysql_real_escape_string($_POST['Day']) . "'";
		$sql .= " where ID=".mysql_real_escape_string($_POST['ID']).";";
	}
	else if (isset($_POST['EndMeter'])) {
		$sql = "update TruckFueling set EndMeter='".mysql_real_escape_string($_POST['EndMeter'])."' where ID=".mysql_real_escape_string($_POST['ID']).";";
	}
	echo "<sql>".$sql."</sql>";
	
	if (strlen($sql) > 0) {
		$res = mysql_query($sql);
		if (!$res) echo "<err>" . mysql_error() . "</err>";
	}

	echo "success";
	echo "</results>";
?>
