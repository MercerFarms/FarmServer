<html>
<head>
	<title>Manual Fueling Update Form</title>
	<script>
		function edit(id) {
			var sv = document.getElementById('save');
			sv.disabled = false;

			var found = false;
			for (var i=0; i<updates.length; i++) {
				if (updates[i] == id) {
					found = true;
				}
			}
			if (found == false)
				updates.push(id);
		}

		function del(id) {
			var sv = document.getElementById('save');
			sv.disabled = false;

			var found = false;
			for (var i=0; i<deletes.length; i++) {
				if (deletes[i] == id) {
					found = true;
				}
			}
			if (found == false)
				deletes.push(id);

			var dimg = document.getElementById('img_'+id);
			if (dimg)
				dimg.style.visibility='hidden';
		}

		function sendpostreq(u, p) {
			xmlhttp = new XMLHttpRequest();
			xmlhttp.open("POST",u,false);
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
			var fid = document.getElementById('id');
			if (dt.value.length > 0) {
				sendpostreq("updateservice.php", "ID="+fid.value+"&TruckID="+trk.value+"&StartMeter="+start.value+"&Day="+dt.value);
				sendpostreq("updateservice.php", "ID="+fid.value+"&TruckID="+trk.value+"&EndMeter="+end.value+"&Day="+dt.value);

				for (var x in updates) {
					var f = document.getElementById('field_'+updates[x]);
					var i = document.getElementById('imp_'+updates[x]);
					var op = document.getElementById('op_'+updates[x]);
					var h = document.getElementById('hours_'+updates[x]);
					var srv = document.getElementById('service_'+updates[x]);
					var die = document.getElementById('diesel_'+updates[x]);
					var def = document.getElementById('def_'+updates[x]);
					var moil = document.getElementById('moil_'+updates[x]);
					var hoil = document.getElementById('hoil_'+updates[x]);
					var cab = document.getElementById('cab_'+updates[x]);

					if (f.value.length > 0) {
						sendpostreq("updateservice.php", "ID="+updates[x]+"&FuelID="+trk.value+"&OpID="+op.value+"&ImpID="+i.value+"&Field="+f.value+"&UnitHours="+h.value+"&ServiceDue="+srv.value+"&Diesel="+die.value+"&DEF="+def.value+"&MotorOil="+moil.value+"&HydraulicOil="+hoil.value+"&CabInsp="+cab.value+"&Time="+dt.value);
					}
				}

				for (var x in deletes) {
					sendpostreq("deleteservice.php", "ID="+deletes[x]);
				}
			}
			sv.disabled = false;
			alert("data has been submitted");
		}
	</script>
</head>

<body>
	<h1>Manual Fueling Data Entry</h1>

<?php
    //Customer#: 83629660
    //PWD: Edgar11MVP
    
	$hostname="localhost";
	$username="farmadmin";
	$password="Prosser#1";
	$dbname="farmdata";

	mysql_connect($hostname,$username, $password) or die ("<html><script language='JavaScript'>alert('Unable to connect to database! Please try again later.'),history.go(-1)</script></html>");
	mysql_select_db($dbname);

	if (isset($_POST['date'])) {
		$sql = "SELECT ID, TruckID, StartMeter, EndMeter, Day FROM TruckFueling WHERE DAY <= '".$_POST['date']."' ORDER BY DAY DESC LIMIT 0,1;";
	}
	else {
		$sql = "SELECT ID, TruckID, StartMeter, EndMeter, Day  FROM TruckFueling ORDER BY DAY DESC LIMIT 0,1;";
	}
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$date = $row['Day'];
?>

	<form method='post'>
		<input id='id' type='hidden' value='<?php echo $row['ID']; ?>'>
	<div>
		<table border='0'>
			<tr><td>Date: <input id='date' name='date' size='30' value='<?php echo $row['Day'];?>'></td>
				<td>Gallons Loaded Diesel: <input id='galsDiesel' name='galsDiesel' size='10'></td>
				<td>Driver: <input id='driver' name='driver' value='<?php echo $row['TruckID'];?>'></td></tr>
			<tr><td>Truck Meter Start: <input id='meterstart' name='meterstart' size='10' value='<?php echo $row['StartMeter'];?>'></td>
				<td>Truck Meter End: <input id='meterend' name='meterend' size='10' value='<?php echo $row['EndMeter'];?>'></td>
				<td><input type='submit' value='refresh'></td>
				<td><button id='save' disabled onclick='submitdata()'>save</button></td></tr>
		</table>
	</div>
	</form>

	<table border='0'>
		<tr><th></th><th>Unit #</th><th>Field</th><th>Implement</th><th>Operator #</th><th>Unit Hours</th><th>Service Due</th><th>Gallons Diesel</th><th>Gallons DEF</th><th>Motor Oil</th><th>Hydra Oil</th><th>Cab Inspection</th></tr>
<?php
	$sql = "select * from FuelService where DATE(Timestamp) = '".$date."' ORDER BY UnitID;";
	echo "\n<!-- ".$sql."-->\n";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	while ($row) {
		$uid = $row['UnitID'];
		$id = $row['ID'];
		echo "<tr>";
		echo "<td><img id='img_".$id."' src='delete.png' onclick='del(".$id.")'></td>";
		echo "<td>".$uid."</td><td><input id='field_".$id."' placeholder='Field' size='10' value='".$row['Field']."' onchange='edit(".$id.")'></td>";
		echo "<td><input id='imp_".$id."' placeholder='Implement' size='10' value='".$row['ImplementID']."' onchange='edit(".$id.")'></td>";
		echo "<td><input id='op_".$id."' placeholder='Operator #' size='10' value='".$row['OperatorID']."' onchange='edit(".$id.")'></td>";
		echo "<td><input id='hours_".$id."' placeholder='Unit Hours' size='10' value='".$row['UnitHours']."' onchange='edit(".$id.")'></td>";
		echo "<td><input id='service_".$id."' placeholder='Service Due' size='10' value='".$row['ServiceDue']."' onchange='edit(".$id.")'></td>";
		echo "<td><input id='diesel_".$id."' placeholder='Gallons Diesel' size='10' value='".$row['Diesel']."' onchange='edit(".$id.")'></td>";
		echo "<td><input id='def_".$id."' placeholder='Gallons DEF' size='10' value='".$row['DEF']."' onchange='edit(".$id.")'></td>";
		echo "<td><input id='moil_".$id."' placeholder='Motor Oil' size='10' value='".$row['MotorOil']."' onchange='edit(".$id.")'></td>";
		echo "<td><input id='hoil_".$id."' placeholder='Hydra Oil' size='10' value='".$row['HydraulicOil']."' onchange='edit(".$id.")'></td>";
		echo "<td><input id='cab_".$id."' placeholder='Cab Inspection' size='10' value='".$row['CabInspection']."' onchange='edit(".$id.")'></td></tr>\n";
		$row = mysql_fetch_array($res);
	}
?>
	</table>

	<script>
		var updates = new Array();
		var deletes = new Array();
	</script>

</body>
</html>