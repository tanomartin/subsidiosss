<?php 
include_once 'include/conector.php';

$fecha = $_POST['fecha'];
$date = new DateTime($fecha);
$fecha = $date->format('Y-m-d');
$monto = $_POST['monto'];

$idPresentacion = $_POST['id'];
$sqlUpdatePresentacion = "UPDATE intepresentacion 
							SET fechadeposito = '$fecha', 
								montodepositado = $monto
							WHERE id = $idPresentacion";

$sqlUpdateFacturas = "UPDATE madera.facturas SET autorizacionpago = 1 WHERE id in 
					(SELECT DISTINCT nrocominterno FROM intepresentaciondetalle WHERE idpresentacion = $idPresentacion)";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
		
	//echo $sqlUpdatePresentacion."<br>";
	$dbh->exec($sqlUpdatePresentacion);
	//echo $sqlUpdateFacturas."<br>";
	$dbh->exec($sqlUpdateFacturas);
	
	$dbh->commit();
	Header("Location: presentacion.php");
} catch (PDOException $e) {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Detalle Deposito'&error=".$e->getMessage();
	Header($redire);
}

?>