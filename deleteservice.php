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

	$sql = "delete from FuelService where ID=".mysql_real_escape_string($_POST['ID']).";";
	echo "<sql>".$sql."</sql>";
	
	if (strlen($sql) > 0) {
		$res = mysql_query($sql);
		if (!$res) echo "<err>" . mysql_error() . "</err>";
	}

	echo "success";
	echo "</results>";
?>
