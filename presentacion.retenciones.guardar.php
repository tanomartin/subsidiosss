<?php
include_once 'include/conector.php';
$usuario = $_SESSION['usuario'];
$idPresentacion = $_POST['idpresentacion'];

$arrayUpdate = array();
foreach ($_POST as $key => $datos) {
	$pos = strpos($key, "rete");
	if ($pos !== false) {
		$cuit = substr($key,4,11);
		$nombreRete = "rete".$cuit;
		$nombreAPagar = "apagar".$cuit;
		$sqlUpdate = "UPDATE inteinterbanking SET impretencion = ".$_POST[$nombreRete].", impapagar = ".$_POST[$nombreAPagar]." WHERE idpresentacion = $idPresentacion and cuit = '$cuit'";
		$arrayUpdate[$cuit] = $sqlUpdate;
	}
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();

	foreach ($arrayUpdate as $updateRetencion) {
		$dbh->exec($updateRetencion);
		//echo $updateRetencion."<br>";
	}
	
	$dbh->commit();
	Header("Location: presentacion.retenciones.php?id=$idPresentacion");
} catch (PDOException $e) {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Guardar Retenciones'&error=".$e->getMessage();
	Header($redire);
}





?>