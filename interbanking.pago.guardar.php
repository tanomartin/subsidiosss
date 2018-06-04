<?php
include_once 'include/conector.php';

$totMonCom = $_POST["totMonCom"];
$totMonDeb = $_POST["totMonDeb"];
$totMonNOI = $_POST["totMonNOI"];
$totMonOS = $_POST["totMonOS"];
$totMonSub = $_POST["totMonSub"];
$totRete = $_POST["totRete"];
$totApagar = $_POST["totApagar"];
$totCantidad = $_POST["totCantidad"];
$nrosecuencia = bin2hex(rand(0, 9999));
$nrosecuencia = str_pad($nrosecuencia,8,0,STR_PAD_LEFT);

$insertCabecera = "INSERT INTO inteinterbankingcabecera VALUES(DEFAULT, CURDATE(), $totCantidad, '$nrosecuencia', $totMonCom, $totMonDeb, $totMonNOI, $totMonOS, $totMonSub, $totRete, $totApagar)";

$arrayUpdateInter = array();
$index = 0;
foreach ($_POST as $key => $datos) {
	$pos = strpos($key, "cuit");
	if ($pos !== false) {
		$index++;
		$cuit = $datos;
		$nompres = "pres".$cuit;
		$idpres = $_POST[$nompres];
		$arrayUpdateInter[$index] = "UPDATE inteinterbanking SET idpago = idcabecera WHERE idpresentacion = $idpres and cuit = '$cuit'";
	}
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	$dbh->exec($insertCabecera);
	//echo $insertCabecera."<br>";
	$lastId = $dbh->lastInsertId();
	foreach ($arrayUpdateInter as $sqlUpdate) {
		$sqlUpdate = str_replace("idcabecera", $lastId, $sqlUpdate);
		//echo $sqlUpdate."<br>";
		$dbh->exec($sqlUpdate);
	}
	$dbh->commit();
	Header("Location: interbanking.pago.archivo.php?id=$lastId");
} catch (PDOException $e) {
	$dbh->rollback();
	
	$sqlConsultaUlitmoID = "SELECT id FROM inteinterbankingcabecera ORDER BY id desc limit 1";
	$resConsultaUlitmoID = mysql_query($sqlConsultaUlitmoID);
	$rowConsultaUlitmoID = mysql_fetch_array($resConsultaUlitmoID);
	$idRestart = $rowConsultaUlitmoID['id'] + 1;
	
	$dbh->beginTransaction();
	$sqlUpdateAutoAuto = "ALTER TABLE inteinterbankingcabecera AUTO_INCREMENT = $idRestart";
	$dbh->exec($sqlUpdateAutoAuto);
	$dbh->commit();
	
	$error = $e->getMessage()." (SQL: ".$sqlinsert.")";
	$redire = "Location: presentacion.error.php?page='Generar Archivo Pago'&error=$error";
	Header($redire);
	exit -1;
}

?>
