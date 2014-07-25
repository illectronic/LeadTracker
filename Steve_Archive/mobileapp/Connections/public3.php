<?php

$hostname_public = "localhost";
$database_public = "officedev";
$username_public = "time_dev";
//$password_public = "t1m3d3v";
$password_public = "T1m3s";


$public_conn = oci_connect($username_public, $password_public, "$database_public" );

if(!$public_conn){
   print "could not connect to db<br>\n";
   exit;
}

/*
$stid = oci_parse($public_conn, 'SELECT * FROM lead_tracker');
oci_execute($stid);

  while ($row = oci_fetch_assoc($stid)) {
     print $row['CALL_ID'] . "<br>\n";
  }

*/

?>
