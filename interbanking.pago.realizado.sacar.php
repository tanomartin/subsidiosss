<?php
include_once 'include/conector.php';

$id = $_GET['id'];
$cuit = $_GET['cuit'];

$sqlDetalle = "SELECT * FROM inteinterbanking WHERE idpago = $id and cuit = '$cuit'";
$resDetalle = mysql_query($sqlDetalle);
$rowDetalle = mysql_fetch_assoc($resDetalle);

$sqlCabecera = "SELECT * FROM inteinterbankingcabecera WHERE id = $id";
$resCabecera = mysql_query($sqlCabecera);
$rowCabecera = mysql_fetch_assoc($resCabecera);

$cantidad = $rowCabecera['cantidad'] - 1;
$impcomprobanteintegral = $rowCabecera['impcomprobanteintegral'] - $rowDetalle['impcomprobanteintegral'];
$impdebito = $rowCabecera['impdebito'] - $rowDetalle['impdebito'];
$impnointe = $rowCabecera['impnointe'] - $rowDetalle['impnointe'];
$impobrasocial = $rowCabecera['impobrasocial'] - $rowDetalle['impobrasocial'];
$impmontosubsidio = $rowCabecera['impmontosubsidio'] - $rowDetalle['impmontosubsidio'];
$impretencion = $rowCabecera['impretencion'] - $rowDetalle['impretencion'];
$impapagar = $rowCabecera['impapagar'] - $rowDetalle['impapagar'];

$updateCabecera = "UPDATE inteinterbankingcabecera 
						SET 
							cantidad = $cantidad,
							impcomprobanteintegral = $impcomprobanteintegral,
							impdebito = $impdebito,
							impnointe = $impnointe,
							impobrasocial = $impobrasocial,
							impmontosubsidio = $impmontosubsidio,
							impretencion = $impretencion,
							impapagar = $impapagar
						WHERE id = $id";

$updateDetalle = "UPDATE inteinterbanking SET idpago = null WHERE idpago = $id and cuit = '$cuit'";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	
	$dbh->exec($updateDetalle);
	//echo $updateDetalle."<br>";
	$dbh->exec($updateCabecera);
	//echo $updateCabecera."<br>";
	
	$dbh->commit();
	Header("Location: interbanking.pago.realizado.detalle.php?id=$id");
} catch (PDOException $e) {
	$dbh->rollback();
	$error = $e->getMessage()." (SQL: ".$sqlinsert.")";
	$redire = "Location: presentacion.error.php?page='Generar Archivo Pago'&error=$error";
	Header($redire);
	exit -1;
}


?>
