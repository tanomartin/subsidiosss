<?php 
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];
$updateFinPresentacion = "UPDATE intepresentacion SET fechacierrepagos = CURDATE() WHERE id = $idPresentacion";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	$dbh->exec($updateFinPresentacion);
	$dbh->commit();
	Header("Location: pagos.php");
} catch (PDOException $e) {
	$dbh->rollback();
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Cancelacion'&error=".$e->getMessage();
	Header($redire);
}

?>