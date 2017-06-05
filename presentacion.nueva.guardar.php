<?php
include_once 'include/conector.php';
require_once 'include/phpExcel/Classes/PHPExcel.php';

function esValidoCUIT($cuit) {
	$aMult = '5432765432';
    $aMult = str_split($aMult);
    if (strlen($cuit) == 11) {
        $aCUIT = str_split($cuit);
        $iResult = 0;
        for($i = 0; $i <= 9; $i++) {
           $iResult += $aCUIT[$i] * $aMult[$i];
        }
        $iResult = ($iResult % 11);
		if ($iResult == 1) $iResult = 0;
		if ($iResult != 0) $iResult = 11 - $iResult;	
        if ($iResult == $aCUIT[10]) {
			return true;	
        } else {
			return false;
		}
    } else {
		return false;	
	}
}

$archivo = $_FILES['archivo']['tmp_name'];
$fp = fopen ($archivo,"r");

$impCompTotal = 0;
$impPedido = 0;
$impCompTotalD = 0;
$impPedidoD = 0;
$cantFacturas = 0;
$sqlInsertFacturas = array();
while ($data = fgetcsv ($fp, 1000, ";")) { 
	if ($data['1'] == 'DS' || $data['1'] == 'DC') {
		$impCompTotal += str_replace(',','.',$data['15']);
		$impPedido += str_replace(',','.',$data['16']);
	}
	if ($data['1'] == 'DB') {
		$impCompTotalD += str_replace(',','.',$data['15']);
		$impPedidoD += str_replace(',','.',$data['16']);
	}

	try {
		$cuil = $data['3'];
		if (!esValidoCUIT($cuil)) {
			$error = "Error en el C.U.I.L. $cuil nro comprobante interno ".$data['0'];
			throw new Exception($error);
		}
		$cuit = $data['7'];
		if (!esValidoCUIT($cuit)) {
			$error = "Error en el C.U.I.T. $cuit nro comprobante interno ".$data['0'];
			throw new Exception($error);
		}
		
		$impFactura = str_replace(',','.',$data['15']);
		$impSolicit = str_replace(',','.',$data['16']);
		if ($impFactura < $impSolicit) {
			$error = "El monto solicitado no puede ser superior al monto de la facutra nor comprobante interno ".$data['0'];
			throw new Exception($error);
		}
	} catch (Exception $e) {
		$error = $e->getMessage();
		$redire = "Location: presentacion.error.php?page='Nueva Presentacion'&error=$error";
		Header($redire);
		exit -1;
	}
	
    $linea = "INSERT INTO facturas VALUES (".str_replace('.','',$data['0']).",idpres,
    		'".$data['1']."',
    		".$data['2'].",
    		'".$data['3']."',
    		'".$cuil."',
    		'".$data['5']."',
    		'".$data['6']."',
    		'".$data['7']."',
    		'".$cuit."',
    		".$data['9'].",
    		'".strtoupper($data['10'])."',
    		'".$data['11']."',
    		'".$data['12']."',
    		".$data['13'].",
    		'".$data['14']."',
    		".$impFactura.",
    		".$impSolicit.",
    		".$data['17'].",
    		".$data['18'].",
    		".$data['19'].",
    		'".strtoupper($data['20'])."',
    		NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)";
    $sqlInsertFacturas[$cantFacturas] = $linea;
    $cantFacturas++;
}

fclose ($fp);
$sqlInsertPresentacion = "INSERT INTO presentacion VALUES(DEFAULT, ".$_POST['idCronograma'].", NULL, NULL, NULL,$cantFacturas,$impCompTotal,$impPedido,$impCompTotalD,$impPedidoD,NULL,NULL)";

$anio = substr($_POST['carpeta'],0,4);
$carpetaanio = "archivos/$anio";
$carpetaGeneracion = "archivos/$anio/".$_POST['carpeta']."/generacion";
$carpetaResultados= "archivos/$anio/".$_POST['carpeta']."/resultados";

try {
	if (!file_exists($carpetaanio)) {
		mkdir($carpetaanio, 0777, true);
	}
	if (!file_exists($carpetaGeneracion)) {
		mkdir($carpetaGeneracion, 0777, true);
	}
	if (!file_exists($carpetaResultados)) {
		mkdir($carpetaResultados, 0777, true);
	}
	$archivocsv = $carpetaGeneracion."/mi".$_POST['carpeta'].".csv";
	copy($archivo, $archivocsv);
} catch (Exception $e) {
	$error = $e->getMessage();
	$redire = "Location: presentacion.error.php?page='Nueva Presentacion'&error=$error";
	Header($redire);
	exit -1;
}
	
try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	
	$dbh->exec($sqlInsertPresentacion);
	//echo $sqlInsertPresentacion."<br>";
	$lastId = $dbh->lastInsertId(); 
	
	foreach ($sqlInsertFacturas as $sqlinsert) {
		$sqlinsert = str_replace("idpres", $lastId, $sqlinsert);
		//echo $sqlinsert."<br>";
		$dbh->exec($sqlinsert);
	}
	
	$dbh->commit();
	
	Header("Location: presentacion.detalle.php?id=$lastId");
} catch (PDOException $e) {
	$dbh->rollback();
	$error = $e->getMessage()." (INSERT: ".$sqlinsert.")";
	$redire = "Location: presentacion.error.php?page='Nueva Presentacion'&error=$error";
	Header($redire);
	exit -1;
}



?>