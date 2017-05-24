<?php
include_once("claves.php");
session_start();
$redire = "Location: sesionCaducada.php";
if (isset($_SESSION['aut'])) {
	if ($_SESSION['aut'] != 1) {
		header($redire);
		exit(0);
	}
} else {
	header($redire);
	exit(0);
}

$db =  mysql_connect($hostLocal,$usuarioLocal,$claveLocal);
if (!$db) {
	die('No pudo conectarse: ' . mysql_error());
	exit(0);
}
mysql_select_db($dbname);
?>