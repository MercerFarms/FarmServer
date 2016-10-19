<?php
    header('Content-Type: text/xml');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<fields>";

    //Customer#: 83629660
    //PWD: Edgar11MVP

    $farmid = 1;    //default to Mercer Farms if the farmid isn't given
    if (isset($_REQUEST['farmid'])) {
        $farmid = $_REQUEST['farmid'];
    }

	$hostname="localhost";
	$username="farmadmin";
	$password="Prosser#1";
	$dbname="farmdata";

	mysql_connect($hostname,$username, $password) or die ("<html><script language='JavaScript'>alert('Unable to connect to database! Please try again later.'),history.go(-1)</script></html>");
	mysql_select_db($dbname);
	
	$catres = mysql_query("SELECT Name, Latitude, Longitude FROM Fields WHERE FarmID=".$farmid.";") or die("<qry>".mysql_error()."</qry>");
	while ($row = mysql_fetch_array($catres)) {
		echo "<f name=\"".trim($row['Name'])."\" lat=\"".$row['Latitude']."\" long=\"".$row['Longitude']."\" />";
	}
    
    echo "</fields>";
?>
