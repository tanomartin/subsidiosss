<?php
include_once 'include/conector.php';
$idPresentacion = $_POST['idpresentacion'];
$nrocomint = $_POST['nrocomint'];
$norord = $_POST['norord'];
$recibo = $_POST['recibo'];
$asiento = $_POST['asiento'];
$folio = $_POST['folio'];

$sqlUpdatePago = "UPDATE pagos SET recibo = '$recibo', asiento = '$asiento', folio = '$folio' WHERE idpresentacion = $idPresentacion and nrocominterno = $nrocomint and nroordenpago = $norord";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();

	//echo $sqlUpdatePago;
	$dbh->exec($sqlUpdatePago);

	$dbh->commit();
	Header("Location: presentacion.pagos.php?id=$idPresentacion");
} catch (PDOException $e) {
	$dbh->rollback();
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Carga Info Pagos'&error=".$e->getMessage();
	Header($redire);
}

?>