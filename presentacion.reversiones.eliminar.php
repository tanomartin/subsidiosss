<?php
include_once 'include/conector.php';
$nrocom = $_GET['nrocom'];
$idPresentacion = $_GET['idpres'];

$tiporev = 1;
$sqlGetReversion = "SELECT * FROM intepresentaciondetalle where idpresentacion = $idPresentacion and nrocominterno = $nrocom";
$resGetReversion = mysql_query($sqlGetReversion);
$canGetReversion = mysql_num_rows($resGetReversion);
if ($canGetReversion == 2) {
	$tiporev = 2;
}

$sqlGetReversionFut = "SELECT * FROM intepresentacionreversion where idpresentacion = $idPresentacion and nrocominterno = $nrocom and estado = 0";
$resGetReversionFut = mysql_query($sqlGetReversion);
$canReversionFut = mysql_num_rows($resGetReversionFut);

$index = 0;
$indexCab = 0;
while ($rowGetReversion = mysql_fetch_array($resGetReversion)) {
	$impcomp = $rowGetReversion['impcomprobante'];
	$impdeb = $rowGetReversion['impdebito'];
	$impsoli = $rowGetReversion['impsolicitado'];
	$nointe = $rowGetReversion['impnointe'];
	
	$deleteReversion[$index] = "DELETE FROM intepresentaciondetalle WHERE idpresentacion = $idPresentacion and nrocominterno = $nrocom";
	$index++;

	if ($rowGetReversion['tipoarchivo'] == 'DB') {
		$updateCabecera[$indexCab] = "UPDATE intepresentacion
		SET cantfactura = cantfactura - 1, impcomprobantesd = impcomprobantesd - $impcomp,
		impdebitod = impdebitod - $impdeb, impnointed = impnointed - $nointe,
		impsolicitadod = impsolicitadod - $impsoli
		WHERE id = $idPresentacion";
		$indexCab++;
	}
	
	if ($tiporev == 2 && $rowGetReversion['tipoarchivo'] != 'DB') {
		$updateCabecera[$indexCab] = "UPDATE intepresentacion
		SET cantfactura = cantfactura - 1, impcomprobantes = impcomprobantes - $impcomp,
		impdebito = impdebito - $impdeb, impnointe = impnointe - $nointe,
		impsolicitado = impsolicitado - $impsoli
		WHERE id = $idPresentacion";
		$indexCab++;
	}
	
	if ($canReversionFut == 1) {
		$deleteReversion[$index] = "DELETE FROM intepresentacionreversion WHERE idpresentaciondb = $idPresentacion and nrocominterno = $nrocom";
		$index++;
	}
	
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();

	foreach ($deleteReversion as $regRev) {
		$dbh->exec($regRev);
		//echo $regRev."<br><br>";
	}

	foreach ($updateCabecera as $regCab) {
		$dbh->exec($regCab);
		//echo $regCab."<br><br>";
	}

	$dbh->commit();
	Header("Location: presentacion.reversiones.php?id=$idPresentacion");
} catch (PDOException $e) {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Guardar Retenciones'&error=".$e->getMessage();
	Header($redire);
}
?>