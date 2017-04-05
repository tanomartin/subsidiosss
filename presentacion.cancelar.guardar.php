<?php
include_once 'include/conector.php';
require_once 'include/phpExcel/Classes/PHPExcel.php';

$idPresentacion = $_GET['id'];
$updateCancelarPresentacion = "UPDATE presentacion SET fechacancelacion = CURDATE() WHERE id = $idPresentacion";

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