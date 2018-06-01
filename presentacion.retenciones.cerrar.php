<?php
include_once 'include/conector.php';
$usuario = $_SESSION['usuario'];
$idPresentacion = $_GET['idpresentacion'];
$sqlUpdate = "UPDATE intepresentacion SET fechacierre = CURDATE() WHERE id = $idPresentacion";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();

	$dbh->exec($sqlUpdate);
	//echo $sqlUpdate;
	
	$dbh->commit();
	Header("Location: presentacion.php");
	
} catch (PDOException $e) {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Cierre Retenciones'&error=".$e->getMessage();
	Header($redire);
}





?>