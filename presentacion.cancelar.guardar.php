<?php
include_once 'include/conector.php';
require_once 'include/phpExcel/Classes/PHPExcel.php';

$idPresentacion = $_GET['id'];
$updateCancelarPresentacion = "UPDATE presentacion SET fechacancelacion = CURDATE(), motivocancelacion = '".$_POST['motivo']."' WHERE id = $idPresentacion";

$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$anio = substr($rowPresentacion['carpeta'],0,4);
$carpetaanio = "archivos/$anio";
$carpetaGeneracion = "archivos/$anio/".$rowPresentacion['carpeta']."/generacion";
$carpetaResultados= "archivos/$anio/".$rowPresentacion['carpeta']."/resultados";

$arcvivocsv = $carpetaGeneracion."/mi".$rowPresentacion['carpeta'].".csv";
$arcvivotxt = $carpetaGeneracion."/111001_ds.txt";
$archivook = $carpetaResultados."/111001-".$rowPresentacion['carpeta']."_ds.ok";
$archivoerr = $carpetaResultados."/111001-".$rowPresentacion['carpeta']."_ds.err";
$archivointok = $carpetaResultados."/111001-".$rowPresentacion['carpeta']."_DS.DEVERR";
$archivointerr = $carpetaResultados."/111001-".$rowPresentacion['carpeta']."_DS.DEVOK";

try {
	if (file_exists($arcvivocsv)){ unlink($arcvivocsv); }
	if (file_exists($arcvivotxt)){ unlink($arcvivotxt); }
	if (file_exists($archivook)){ unlink($archivook); }
	if (file_exists($archivoerr)){ unlink($archivoerr); }
	if (file_exists($archivointok)){ unlink($archivointok); }
	if (file_exists($archivointerr)){ unlink($archivointerr); }
} catch (Exception $e) {
	echo $e->getMessage();
	exit -1;
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	$dbh->exec($updateCancelarPresentacion);
	$dbh->commit();
	Header("Location: presentacion.php");
} catch (PDOException $e) {
	echo $e->getMessage();
	$dbh->rollback();
}

?>