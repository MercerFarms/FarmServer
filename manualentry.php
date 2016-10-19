<html>
<head>
	<title>Manual Fueling Entry Form</title>
	<script>
		function sendpostreq(p) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.open("POST","newservice.php",false);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send(p);
		}

		function submitdata() {
			var sv = document.getElementById('save');
			sv.disabled = true;

			var dt = document.getElementById('date');
			var trk = document.getElementById('driver');
			var start = document.getElementById('meterstart');
			var end = document.getElementById('meterend');
			if (dt.value.length > 0) {
				sendpostreq("TruckID="+trk.value+"&StartMeter="+start.value+"&Day="+dt.value);
				sendpostreq("TruckID="+trk.value+"&EndMeter="+end.value+"&Day="+dt.value);

				for (var x in units) {
					var u = units[x];
					var f = document.getElementById('field_'+u);
					var i = document.getElementById('imp_'+u);
					var op = document.getElementById('op_'+u);
					var h = document.getElementById('hours_'+u);
					var srv = document.getElementById('service_'+u);
					var die = document.getElementById('diesel_'+u);
					var def = document.getElementById('def_'+u);
					var moil = document.getElementById('moil_'+u);
					var hoil = document.getElementById('hoil_'+u);
					var cab = document.getElementById('cab_'+u);

					if (f.value.length > 0) {
						sendpostreq("FuelID="+trk.value+"&OpID="+op.value+"&UnitID="+u+"&ImpID="+i.value+"&Field="+f.value+"&UnitHours="+h.value+"&ServiceDue="+srv.value+"&Diesel="+die.value+"&DEF="+def.value+"&MotorOil="+moil.value+"&HydraulicOil="+hoil.value+"&CabInsp="+cab.value+"&Time="+dt.value);
					}
				}
			}
			sv.disabled = false;
			alert("data has been submitted");
		}
	</script>
</head>

<body>
	<h1>Manual Fueling Data Entry</h1>

	<div>
		<table border='0'>
			<tr><td>Date: <input id='date' size='30' value='<?php echo Date("Y-m-d");?>'></td><td>Gallons Loaded Diesel: <input id='galsDiesel' size='10'></td><td>Driver: <input id='driver'></td></tr>
			<tr><td>Truck Meter Start: <input id='meterstart' size='10'></td><td>Truck Meter End: <input id='meterend' size='10'></td><td><button id='save' onclick='submitdata()'>save</button></td></tr>
		</table>
	</div>

	<table border='0'>
		<tr><th>Unit #</th><th>Field</th><th>Implement</th><th>Operator #</th><th>Unit Hours</th><th>Service Due</th><th>Gallons Diesel</th><th>Gallons DEF</th><th>Motor Oil</th><th>Hydra Oil</th><th>Cab Inspection</th></tr>
<?php
    //Customer#: 83629660
    //PWD: Edgar11MVP
    
	$hostname="localhost";
	$username="farmadmin";
	$password="Prosser#1";
	$dbname="farmdata";

	mysql_connect($hostname,$username, $password) or die ("<html><script language='JavaScript'>alert('Unable to connect to database! Please try again later.'),history.go(-1)</script></html>");
	mysql_select_db($dbname);

	$res = mysql_query("select distinct UnitID from FuelService order by UnitID;");
	$row = mysql_fetch_array($res);
	$units = array();
	while ($row) {
		$uid = $row['UnitID'];
		$units[] = "'".$uid."'";
		echo "<tr><td>".$uid."</td><td><input id='field_".$uid."' placeholder='Field' size='10'></td><td><input id='imp_".$uid."' placeholder='Implement' size='10'></td>";
		echo "<td><input id='op_".$uid."' placeholder='Operator #' size='10'></td><td><input id='hours_".$uid."' placeholder='Unit Hours' size='10'></td>";
		echo "<td><input id='service_".$uid."' placeholder='Service Due' size='10'></td><td><input id='diesel_".$uid."' placeholder='Gallons Diesel' size='10'></td>";
		echo "<td><input id='def_".$uid."' placeholder='Gallons DEF' size='10'></td><td><input id='moil_".$uid."' placeholder='Motor Oil' size='10'></td>";
		echo "<td><input id='hoil_".$uid."' placeholder='Hydra Oil' size='10'></td><td><input id='cab_".$uid."' placeholder='Cab Inspection' size='10'></td></tr>\n";
		$row = mysql_fetch_array($res);
	}
?>



<?php
/*
	if (isset($_POST['Field'])) {
		$sql = "insert into FuelService (FuelOperatorID, OperatorID, UnitID, ImplementID, Field, UnitHours, ServiceDue, Diesel, DEF, MotorOil, HydraulicOil, CabInspection, Timestamp) ";
		$sql .= "values (" . $_POST['FuelID'];
		$sql .= ", " . $_POST['OpID'];
		$sql .= ", " . $_POST['UnitID'];
		$sql .= ", " . $_POST['ImpID'];
		$sql .= ", '" . $_POST['Field'] . "'";
		$sql .= ", " . $_POST['UnitHours'];
		$sql .= ", " . $_POST['ServiceDue'];
		$sql .= ", " . $_POST['Diesel'];
		$sql .= ", " . $_POST['DEF'];
		$sql .= ", " . $_POST['MotorOil'];
		$sql .= ", " . $_POST['HydraulicOil'];
		$sql .= ", " . $_POST['CabInsp'];
		$sql .= ", '" . $_POST['Time'] . "'";
		$sql .= ");";
	}
	else if (isset($_POST['StartMeter'])) {
		$sql = "insert into TruckFueling (TruckID, StartMeter, Day) ";
		$sql .= "values ('" . $_POST['TruckID'] . "'";
		$sql .= ", " . $_POST['StartMeter'];
		$sql .= ", '" . $_POST['Day'] . "'";
		$sql .= ");";
	}
	else if (isset($_POST['EndMeter'])) {
		$sql = "update TruckFueling set EndMeter='".$_POST['EndMeter']."' where TruckID='".$_POST['TruckID']."' and Day='".$_POST['Day']."';";
	}
	echo "<sql>".$sql."</sql>";
	
	if (strlen($sql) > 0) {
		$res = mysql_query($sql);
		if (!$res) echo "<err>" . mysql_error() . "</err>";
	}

	echo "success";
	echo "</results>";
	*/
?>
	</table>

<?php
	echo "<script>\n";
	echo "units = [".implode(",", $units)."];";
	echo "\n</script>\n";
?>

</body>
</html>