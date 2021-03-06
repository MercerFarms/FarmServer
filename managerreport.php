<html>
<head><title>Mercer Farms Manager's Report</title></head>
<body>

<?php
	$monthstart = date("Y-m-01");
	if (isset($_REQUEST['startdate']))
		$monthstart = $_REQUEST['startdate'];
	$monthend = date("Y-m-t");
	if (isset($_REQUEST['enddate']))
		$monthend = $_REQUEST['enddate'];
	$unitid = ""; $field = ""; $opid = ""; $fid = "";
	if (isset($_REQUEST['group'])) {
		if ($_REQUEST['group'] == "Field")
			$field = "checked";
		else if ($_REQUEST['group'] == "OperatorID")
			$opid = "checked";
		else if ($_REQUEST['group'] == "FuelOperatorID")
			$fid = "checked";
		else
			$unitid = "checked";
	}
	else
		$unitid = "checked";

	$dayframe = ""; $weekframe = ""; $monthframe = ""; $timefn = "";
	if (isset($_REQUEST['timeframe'])) {
		if ($_REQUEST['timeframe'] == 'DayFrame') {
			$dayframe = "checked";
			$timefn = "DAYOFYEAR";
		}
		else if ($_REQUEST['timeframe'] == 'WeekFrame') {
			$weekframe = "checked";
			$timefn = "WEEK";
		}
		else if ($_REQUEST['timeframe'] == 'MonthFrame') {
			$monthframe = "checked";
			$timefn = "MONTH";
		}
	}
	else
		$dayframe = "checked";

?>

	<form method="get">
		start date: <input name='startdate' value='<?php echo $monthstart;?>'><br/>
		end date: <input name='enddate' value='<?php echo $monthend;?>'><br/>
		<div style="margin:10px;float:left">
			<input type='radio' name='group' <?php echo $unitid?> value='UnitID'>Unit #<br/>
			<input type='radio' name='group' <?php echo $field?> value='Field'>Field<br/>
			<input type='radio' name='group' <?php echo $opid?> value='OperatorID'>Operator<br/>
			<input type='radio' name='group' <?php echo $fid?> value='FuelOperatorID'>Fuel Operator<br/>
		</div>
		<div style="margin:10px;float:left">
			<input type='radio' name='timeframe' <?php echo $dayframe?> value='DayFrame'>Per Day<br/>
			<input type='radio' name='timeframe' <?php echo $weekframe?> value='WeekFrame'>Per Week<br/>
			<input type='radio' name='timeframe' <?php echo $monthframe?> value='MonthFrame'>Per Month<br/>
		</div>
		<div style="clear:both"></div>
		<input type='submit'>
	</form>

<?php
	if (isset($_REQUEST['startdate'], $_REQUEST['enddate'])) {
		echo "<p><a href='http://www.mercerdata.com/managerreport_csv.php?startdate=".$_REQUEST['startdate']."&enddate=".$_REQUEST['enddate']."&group=".$_REQUEST['group']."&timeframe=".$_REQUEST['timeframe']."'>CSV</a></p>";

		$hostname="localhost";
		$username="farmadmin";
		$password="Prosser#1";
		$dbname="farmdata";

		mysql_connect($hostname,$username, $password) or die ("<html><script language='JavaScript'>alert('Unable to connect to database! Please try again later.'),history.go(-1)</script></html>");
		mysql_select_db($dbname);

		$startdate = $_REQUEST['startdate'];
		$enddate = $_REQUEST['enddate'];
		$grouping = $_REQUEST['group'];

		$sql = "select sum(Diesel) as Total from FuelService WHERE Timestamp >= '".$_REQUEST['startdate']."' AND DATE_SUB(Timestamp, INTERVAL 1 DAY) <= '".$_REQUEST['enddate']."';";
		$totres = mysql_query($sql);
		$row = mysql_fetch_array($totres);
		$totalsum = $row['Total'];
		mysql_close($totres);

		$sql = "SELECT M.TotalDiesel, D.".$grouping.", D.Diesel, D.UnitHours, D.ServiceDue, D.Total, D.DEF, YEAR(D.Timestamp) AS YearFrame, ".$timefn."(D.Timestamp) AS TimeGroup, ".$timefn."('".$startdate."') AS StartFrame, ".$timefn."('".$enddate."') AS EndFrame FROM ";
		$sql .= "(select ".$grouping.", sum(Diesel) as TotalDiesel from FuelService WHERE Timestamp >= '".$_REQUEST['startdate']."' AND DATE_SUB(Timestamp, INTERVAL 1 DAY) <= '".$_REQUEST['enddate']."' Group BY ".$grouping.") as M, ";
		$sql .= "(SELECT ".$grouping.", sum(Diesel) as Diesel, max(UnitHours) as UnitHours, max(ServiceDue) as ServiceDue, ServiceDue-UnitHours as Total, sum(DEF) as DEF, Timestamp ";
		$sql .= "FROM FuelService WHERE Timestamp >= '".$_REQUEST['startdate']."' AND DATE_SUB(Timestamp, INTERVAL 1 DAY) <= '".$_REQUEST['enddate']."' ";
		$sql .= "GROUP BY ".$grouping.", YEAR(Timestamp), ".$timefn."(Timestamp)) as D ";
		$sql .= "where M.".$grouping."=D.".$grouping." ";
		$sql .= "order by D.".$grouping.", ".$timefn."(D.Timestamp) asc;";
		echo "\n<!--".$sql."-->\n";
		
		$catres = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_array($catres);

		$startframe = intval($row['StartFrame']);
		$curryear = intval($row['YearFrame']);
		$currframe = $startframe;
		$endframe = intval($row['EndFrame']);

		echo "<table border='1'>\n";
		echo "<tr><th></th><th></th>";
		$showdate = $monthstart;
		$shows = array();
		if ($timefn == "WEEK") {
			$day = date('w', strtotime($monthstart));
			$showdate = date('Y-m-d', strtotime('-'.$day.' days', strtotime($monthstart)));
		}
		else if ($timefn == "MONTH")
			$showdate = date('Y-m-01', strtotime($monthstart));
		while ($currframe <= $endframe) {
			echo "<th colspan='6'>".$showdate."</th>";
			$shows[] = $showdate;
			$currframe = $currframe + 1;
			if ($timefn == "DAYOFYEAR")
				$showdate = date('Y-m-d', strtotime('+1 day', strtotime($showdate)));
			else if ($timefn == "WEEK")
				$showdate = date('Y-m-d', strtotime('+7 days', strtotime($showdate)));
			else if ($timefn == "MONTH")
				$showdate = date('Y-m-d', strtotime('+1 month', strtotime($showdate)));
		}
		echo "</tr>\n";
		echo "<tr><th></th><th></th>";
		for ($i=0; $i<sizeof($shows); $i++) {
			$dres = mysql_query("select sum(Diesel) as galused from FuelService where ".$timefn."(Timestamp)=".$timefn."('".$shows[$i]."') group by ".$timefn."(Timestamp);") or die(mysql_error());
			$drow = mysql_fetch_array($dres);
			echo "<th colspan='5'>gal used</th><th>".$drow['galused']."</th>";
		}
		echo "</tr>";
		echo "<tr><th></th><th></th>";
		for ($i=0; $i<sizeof($shows); $i++) {
			$dres = mysql_query("select sum(EndMeter-StartMeter) as meter from TruckFueling where ".$timefn."(Day)=".$timefn."('".$shows[$i]."') group by ".$timefn."(Day);") or die(mysql_error());
			$drow = mysql_fetch_array($dres);
			echo "<th colspan='5'>meter</th><th>".$drow['meter']."</th>";
		}
		echo "</tr>";
		$currframe = $startframe;
		echo "<tr><th>Total Per Unit</th><th>".$grouping."</th>";
		while ($currframe <= $endframe) {
			echo "<th>Gallons Fuel</th><th>Hours</th><th>Service Hours</th><th>Total</th><th>DEF</th><th>Time</th>";
			$currframe = $currframe + 1;
		}
		$currframe = $startframe;
		echo "</tr>\n<tr><th>".$totalsum."</th><th>".$grouping."</th>";
		while ($currframe <= $endframe) {
			echo "<th>Gallones Fuel</th><th>Metre/Hores</th><th>Hores</th><th>Toda</th><th></th><th>Tiempo</th>";
			$currframe = $currframe + 1;
		}
		$currframe = $startframe;
		echo "</tr>";

		while ($row) {
			$currframe = $startframe;
			$curunit = $row[$grouping];
			echo "<tr><th>".$row['TotalDiesel']."</th><th>".$row[$grouping]."</th>";
			while ($row && $curunit == $row[$grouping] && $currframe <= $endframe) {
				$nextframe = $row['TimeGroup'];
				echo "\n<!--".$currframe."-->\n";
				while ($currframe < $nextframe) {
					echo "<td></td><td></td><td></td><td>0.0</td><td></td><td></td>";
					$currframe = $currframe + 1;
				}
				echo "<td>".$row['Diesel']."</td>";
				echo "<td>".$row['UnitHours']."</td>";
				echo "<td>".$row['ServiceDue']."</td>";
				echo "<td>".$row['Total']."</td>";
				echo "<td>".$row['DEF']."</td>";
				echo "<td>".$row['TimeGroup']."</td>";
				$currframe = $currframe + 1;
				$row = mysql_fetch_array($catres);
			}
			while ($currframe <= $endframe) {
				echo "<td></td><td></td><td></td><td>0.0</td><td></td><td></td>";
				$currframe = $currframe + 1;
			}
			echo "</tr>\n";
			//$row = mysql_fetch_array($catres);
	    }
	    echo "</table>";
	}
?>

</body>
</html>