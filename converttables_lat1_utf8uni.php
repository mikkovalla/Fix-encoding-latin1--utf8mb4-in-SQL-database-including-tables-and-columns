<?php

//php scripti joka muuntaa MySQL taulut Latin1 koodauksesta nykyaikaiseen UTF-8 muotoon. Script to modify database tables encoding.

$host = "localhost"; //serveri
$db_name = 'poo'; //tietokannan nimi
$db_username = "root"; //käyttis
$db_password = ""; //passu
$convert_to = "utf8mb4_unicode_ci"; //koodaus
 
try {
 $conn = new PDO("mysql:host=$host;dbname=$db_name", $db_username, $db_password);
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
 $sql = "SHOW TABLES";
 
 print "Sql statements executed / Suoritetut sql komennot. ";
 
 foreach ($conn->query($sql) as $row) {
  	 $table_name = $row['Tables_in_' . $db_name];
  	 $sql = 'ALTER TABLE ' . $table_name . ' CONVERT TO CHARACTER SET utf8mb4 COLLATE ' . $convert_to;
  	 $output = $sql . '
';
 
  	 print $output;
  	 $conn->query($sql);
 }
 
}
catch(PDOException $e)
{
 echo "Connection failed: " . $e->getMessage();
}