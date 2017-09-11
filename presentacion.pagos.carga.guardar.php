<?php
include_once 'include/conector.php';


$arrayDatosPagos = array();
foreach ($_POST as $key => $data) {
	if ($key == "idpresentacion") {
		$idPresentacion = $data;
	} else {
		$keyArray = explode("-",$key);
		$norord = $keyArray[1];
		$nrocomint = $keyArray[2];
		$index = $norord."-".$nrocomint;
		$arrayDatosPagos[$index][$keyArray[0]] = $data;
	}
}

$i = 0;
foreach ($arrayDatosPagos as $key => $data) {
	$keyArray = explode("-",$key);
	$norord = $keyArray[0];
	$nrocomint = $keyArray[1];
	$sqlUpdatePago[$i] = "UPDATE pagos SET recibo = '".$data['recibo']."', asiento = '".$data['asiento']."', folio = '".$data['folio']."' WHERE idpresentacion = $idPresentacion and nrocominterno = $nrocomint and nroordenpago = $norord";
	$i++;
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();

	foreach ($sqlUpdatePago as $sql) {
		//echo $sql."<br>";
		$dbh->exec($sql);
	}
	
	$dbh->commit();
	
	$pagina_anterior=$_SERVER['HTTP_REFERER'];
	if (strpos($pagina_anterior, "cuit") === false) {
		$redirect = "presentacion.pagos.php?id=$idPresentacion";
	} else {
		$redirect = "presentacion.pagoscuit.php?id=$idPresentacion";
	}
	Header("Location: $redirect");
	
} catch (PDOException $e) {
	$dbh->rollback();
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Carga Info Pagos'&error=".$e->getMessage();
	Header($redire);
}

?>