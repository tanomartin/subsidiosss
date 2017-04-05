<?php
include_once("claves.php");
$db =  mysql_connect($hostLocal,$usuarioLocal,$claveLocal);
if (!$db) {
	die('No pudo conectarse: ' . mysql_error());
	exit(0);
}
mysql_select_db($dbname);
?>