<?php 
include_once 'include/conector.php';
$idFondo = $_GET['id'];
$carpeta = $_GET['carpeta'];
$updateCancelaFondos = "UPDATE intefondos SET fechacancelacion = CURDATE() WHERE id = $idFondo";

$anio = substr($carpeta,0,4);
$carpetaanio = "archivos/$anio";
$carpetaFondos = "archivos/$anio/$carpeta/fondos";
$archivo = $carpetaFondos."/111001-".$carpeta."_DR.DEVOLUCION.txt";
$archivoerr = $carpetaFondos."/111001-".$carpeta."_DR.DEVOLUCION.ERR";
$archivook = $carpetaFondos."/111001-".$carpeta."_DR.DEVOLUCION.OK";

try {
	if (file_exists($archivo)){ unlink($archivo); }
	if (file_exists($archivoerr)){ unlink($archivoerr); }
	if (file_exists($archivook)){ unlink($archivook); }
} catch (Exception $e) {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Cancelar Fondos'&error=".$e->getMessage();
	Header($redire);
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	//echo $updateCancelaFondos;
	$dbh->exec($updateCancelaFondos);
	$dbh->commit();
	Header("Location: fondos.php");
} catch (PDOException $e) {
	$dbh->rollback();
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Cancelar Fondos'&error=".$e->getMessage();
	Header($redire);
}
?>