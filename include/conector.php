<?php
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

$maquina = $_SERVER['SERVER_NAME'];
$hostLocal = "localhost";
if(strcmp("poseidon",$maquina)==0) {
	$hostLocal = "poseidon";
}
$usuarioLocal = $_SESSION['usuario'];
$claveLocal = $_SESSION['clave'];
$dbname = "subsidiosss";

$db =  mysql_connect($hostLocal,$usuarioLocal,$claveLocal);
if (!$db) {
	die('No pudo conectarse: ' . mysql_error());
	exit(0);
}
mysql_select_db($dbname);
?>