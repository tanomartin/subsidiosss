<?php 
include_once 'include/conector.php';

$fecha = $_POST['fecha'];
$date = new DateTime($fecha);
$fecha = $date->format('Y-m-d');
$monto = $_POST['monto'];

$idPresentacion = $_POST['id'];
$sqlUpdatePresentacion = "UPDATE presentacion 
							SET fechadeposito = '$fecha', 
								montodepositado = $monto
							WHERE id = $idPresentacion";
try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
		
	//echo $sqlUpdatePresentacion."<br>";
	$dbh->exec($sqlUpdatePresentacion);
	$dbh->commit();
	Header("Location: presentacion.detalle.php?id=$idPresentacion");
} catch (PDOException $e) {
	echo $e->getMessage();
	$dbh->rollback();
}

?>