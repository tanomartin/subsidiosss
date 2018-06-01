<?php
include_once 'include/conector.php';

$id = $_GET['id'];
$deleteCabecera = "DELETE FROM inteinterbankingcabecera WHERE id = $id";
$updateDetalle = "UPDATE inteinterbanking SET idpago = null WHERE idpago = $id";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	$dbh->exec($updateDetalle);
	//echo $updateDetalle."<br>";
	$dbh->exec($deleteCabecera);
	//echo $deleteCabecera."<br>";
	
	$dbh->commit();
	Header("Location: interbanking.pago.realizado.php");
} catch (PDOException $e) {
	$dbh->rollback();
	$error = $e->getMessage()." (SQL: ".$sqlinsert.")";
	$redire = "Location: presentacion.error.php?page='Generar Archivo Pago'&error=$error";
	Header($redire);
	exit -1;
}


?>
