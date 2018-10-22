<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];

$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM intepresentacion p, intecronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$sqlFactura = "SELECT * FROM intepresentaciondetalle WHERE idpresentacion = $idPresentacion order by tipoarchivo";
$resFactura = mysql_query($sqlFactura);

$anio = substr($rowPresentacion['carpeta'],0,4);
$archivoGeneracion = "archivos/$anio/".$rowPresentacion['carpeta']."/generacion/111001_ds.txt";

$file = fopen($archivoGeneracion, "w");
while ($rowFactura = mysql_fetch_array($resFactura)) {
	$tipoarchivo = $rowFactura['tipoarchivo'];
	$codigoob = $rowFactura['codigoob'];
	$cuil = str_pad($rowFactura['cuil'],11,'0',STR_PAD_LEFT);
	$codcertificado = str_pad($rowFactura['codcertificado'],40,' ',STR_PAD_LEFT);
	$vtocertificado = $rowFactura['vtocertificado'];
	$periodo = $rowFactura['periodo'];
	$cuit = str_pad($rowFactura['cuit'],11,'0',STR_PAD_LEFT);
	$tipocomprobante = str_pad($rowFactura['tipocomprobante'],2,'0',STR_PAD_LEFT);
	$tipoemision = $rowFactura['tipoemision'];
	$fechacomprobante = $rowFactura['fechacomprobante'];
	$cae = str_pad($rowFactura['cae'],14,'0',STR_PAD_LEFT);
	$puntoventa = str_pad($rowFactura['puntoventa'],5,'0',STR_PAD_LEFT);
	$nrocomprobante = str_pad($rowFactura['nrocomprobante'],8,'0',STR_PAD_LEFT);
	$impcomprobante = str_pad(str_replace(".","",$rowFactura['impcomprobante']),10,'0',STR_PAD_LEFT);
	$impsolicitado = str_pad(str_replace(".","",$rowFactura['impsolicitado']),10,'0',STR_PAD_LEFT);
	$codpractica =  str_pad($rowFactura['codpractica'],3,'0',STR_PAD_LEFT);
	$cantidad =  str_pad($rowFactura['cantidad'],6,'0',STR_PAD_LEFT);
	$provincia =  str_pad($rowFactura['provincia'],2,'0',STR_PAD_LEFT);
	$dependencia = $rowFactura['dependencia'];
	
	$linea = "$tipoarchivo|$codigoob|$cuil|$codcertificado|$vtocertificado|$periodo|$cuit|$tipocomprobante|$tipoemision|$fechacomprobante|$cae|$puntoventa|$nrocomprobante|$impcomprobante|$impsolicitado|$codpractica|$cantidad|$provincia|$dependencia";
	fwrite($file, $linea . PHP_EOL);
}
fclose($file);

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();

	$sqlUpdatePresentacion = "UPDATE intepresentacion SET fechapresentacion = CURDATE() WHERE id = $idPresentacion";
	$dbh->exec($sqlUpdatePresentacion);
	$dbh->commit();

	Header("Location: presentacion.php");
} catch (PDOException $e) {
	echo $e->getMessage();
	$dbh->rollback();
	exit -1;
}





?>