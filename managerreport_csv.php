<?php
    header('Content-Type: text/plain');
    header('Content-Disposition:attachment;filename=manager.csv');
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
			$timefn = "DAY";
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

	if (isset($_REQUEST['startdate'], $_REQUEST['enddate'])) {

		$hostname="localhost";
		$username="farmadmin";
		$password="Prosser#1";
		$dbname="farmdata";

		mysql_connect($hostname,$username, $password) or die ("<html><script language='JavaScript'>alert('Unable to connect to database! Please try again later.'),history.go(-1)</script></html>");
		mysql_select_db($dbname);

		$startdate = $_REQUEST['startdate'];
		$enddate = $_REQUEST['enddate'];
		$grouping = $_REQUEST['group'];

		$sql = "select sum(Diesel) as Total from FuelService WHERE Timestamp >= '".$_REQUEST['startdate']."' AND Timestamp <= '".$_REQUEST['enddate']."';";
		$totres = mysql_query($sql);
		$row = mysql_fetch_array($totres);
		$totalsum = $row['Total'];
		mysql_close($totres);

		$sql = "SELECT M.TotalDiesel, D.".$grouping.", D.Diesel, D.UnitHours, D.ServiceDue, D.Total, D.DEF, YEAR(D.Timestamp) AS YearFrame, ".$timefn."(D.Timestamp) AS TimeGroup, ".$timefn."('".$startdate."') AS StartFrame, ".$timefn."('".$enddate."') AS EndFrame FROM ";
		$sql .= "(select ".$grouping.", sum(Diesel) as TotalDiesel from FuelService WHERE Timestamp >= '".$_REQUEST['startdate']."' AND Timestamp <= '".$_REQUEST['enddate']."' Group BY ".$grouping.") as M, ";
		$sql .= "(SELECT ".$grouping.", sum(Diesel) as Diesel, max(UnitHours) as UnitHours, max(ServiceDue) as ServiceDue, ServiceDue-UnitHours as Total, sum(DEF) as DEF, Timestamp ";
		$sql .= "FROM FuelService WHERE Timestamp >= '".$_REQUEST['startdate']."' AND Timestamp <= '".$_REQUEST['enddate']."' ";
		$sql .= "GROUP BY ".$grouping.", YEAR(Timestamp), ".$timefn."(Timestamp)) as D ";
		$sql .= "where M.".$grouping."=D.".$grouping." ";
		$sql .= "order by D.".$grouping.", ".$timefn."(D.Timestamp) asc;";
		
		$catres = mysql_query($sql);
		$row = mysql_fetch_array($catres);

		$startframe = intval($row['StartFrame']);
		$curryear = intval($row['YearFrame']);
		$currframe = $startframe;
		$endframe = intval($row['EndFrame']);

		$currframe = $startframe;
		$showdate = $monthstart;
		$shows = array();
		if ($timefn == "WEEK") {
			$day = date('w', strtotime($monthstart));
			$showdate = date('Y-m-d', strtotime('-'.$day.' days', strtotime($monthstart)));
		}
		else if ($timefn == "MONTH")
			$showdate = date('Y-m-01', strtotime($monthstart));
		echo ",,";
		while ($currframe <= $endframe) {
			echo $showdate.",,,,,,";
			$shows[] = $showdate;
			$currframe = $currframe + 1;
			if ($timefn == "DAY")
				$showdate = date('Y-m-d', strtotime('+1 day', strtotime($showdate)));
			else if ($timefn == "WEEK")
				$showdate = date('Y-m-d', strtotime('+7 days', strtotime($showdate)));
			else if ($timefn == "MONTH")
				$showdate = date('Y-m-d', strtotime('+1 month', strtotime($showdate)));
		}
		echo "\n";
		echo ",,";
		for ($i=0; $i<sizeof($shows); $i++) {
			$dres = mysql_query("select sum(Diesel) as galused from FuelService where ".$timefn."(Timestamp)=".$timefn."('".$shows[$i]."') group by ".$timefn."(Timestamp);") or die(mysql_error());
			$drow = mysql_fetch_array($dres);
			echo ",,,,gal used,".$drow['galused'].",";
		}
		echo "\n";
		echo ",,";
		for ($i=0; $i<sizeof($shows); $i++) {
			$dres = mysql_query("select sum(EndMeter-StartMeter) as meter from TruckFueling where ".$timefn."(Day)=".$timefn."('".$shows[$i]."') group by ".$timefn."(Day);") or die(mysql_error());
			$drow = mysql_fetch_array($dres);
			echo ",,,,meter,".$drow['meter'].",";
		}
		echo "\n";

		$currframe = $startframe;
		echo "Total Per Unit,".$grouping.",";
		while ($currframe <= $endframe) {
			echo "Gallons Fuel,Hours,Service Hours,Total,DEF,Time,";
			$currframe = $currframe + 1;
		}
		$currframe = $startframe;
		echo "\n".$totalsum.",".$grouping.",";
		while ($currframe <= $endframe) {
			echo "Gallones Fuel,Metre/Hores,Hores,Toda,,Tiempo,";
			$currframe = $currframe + 1;
		}
		echo "\n";
		while ($row) {
			$currframe = $startframe;
			$nextframe = $row['TimeGroup'];
			$curunit = $row[$grouping];
			echo $row['TotalDiesel'].",".$row[$grouping].",";
			while ($row && $curunit == $row[$grouping] && $currframe <= $endframe) {
				while ($currframe < $nextframe) {
					echo ",,,0.0,,,";
					$currframe = $currframe + 1;
				}
				echo $row['Diesel'].",";
				echo $row['UnitHours'].",";
				echo $row['ServiceDue'].",";
				echo $row['Total'].",";
				echo $row['DEF'].",";
				echo $row['TimeGroup'].",";
				$currframe = $currframe + 1;
				$row = mysql_fetch_array($catres);
			}
			while ($currframe <= $endframe) {
				echo ",,,0.0,,,";
				$currframe = $currframe + 1;
			}
			echo "\n";
			//$row = mysql_fetch_array($catres);
	    }
	}
?>