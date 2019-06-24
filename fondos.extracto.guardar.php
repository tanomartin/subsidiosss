<?php
include_once 'include/conector.php';
$carpeta = $_POST['carpeta'];
$idFondos = $_POST['idfondos'];
$idPresentacion = $_POST['idpresentacion'];

if ($_FILES['archivoextracto']['tmp_name'] != "") {
	$archivoextracto = $_FILES['archivoextracto']['tmp_name'];
	$anio = substr($carpeta,0,4);
	$carpetaFondos = "archivos/$anio/$carpeta/fondos";
	try {
		$nombreExtracto = "111001-".$carpeta."_EB.pdf";
		$archivodestino = $carpetaFondos."/".$nombreExtracto;
		copy($archivoextracto, $archivodestino);
	} catch (Exception $e) {
		$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Fondos Extracto'&error=".$e->getMessage();
		Header($redire);
		exit -1;
	}	
	$sqlUpdateCabecera = "UPDATE intefondos SET pathExtracto = '$archivodestino', fechafinalizacion = CURDATE() WHERE id = $idFondos";
} else {
	$error = "No se cargo archivo de errores";
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Fondos'&error=".$error;
	Header($redire);
	exit -1;
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	//echo $sqlUpdateCabecera."<br>";
	$dbh->exec($sqlUpdateCabecera);
	$dbh->commit();
	Header("Location: fondos.php");
} catch (PDOException $e) {
	$dbh->rollback();
	$error = $e->getMessage();
	$redire = "Location: presentacion.error.php?page='Fondos Devolucion'&error=$error";
	Header($redire);
	exit -1;
}

?>
