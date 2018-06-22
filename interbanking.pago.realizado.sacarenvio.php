<?php
include_once 'include/conector.php';

$id = $_GET['id'];
$cuit = $_GET['cuit'];

$updateDetalle = "UPDATE inteinterbanking SET fechaenvio = NULL WHERE idpresentacion = $id and cuit = '$cuit'";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	
	$dbh->exec($updateDetalle);
	//echo $updateDetalle."<br>";
	
	$dbh->commit();
	Header("Location: interbanking.pago.php");
} catch (PDOException $e) {
	$dbh->rollback();
	$error = $e->getMessage()." (SQL: ".$sqlinsert.")";
	$redire = "Location: presentacion.error.php?page='Generar Archivo Pago'&error=$error";
	Header($redire);
	exit -1;
}


?>
