<?php
include_once 'include/conector.php';
require_once 'include/phpExcel/Classes/PHPExcel.php';
set_time_limit(0);

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
$impDebitoTotal = 0;
$impNoInteTotal = 0;
$impPedido = 0;
$impCompTotalD = 0;
$impDebitoTotalD = 0;
$impNoInteTotalD = 0;
$impPedidoD = 0;
$cantFacturas = 0;
$sqlInsertFacturas = array();

$sqlCodVto = "SELECT cuil, codigocertificado, DATE_FORMAT(vencimientocertificado,'%d/%m/%Y') as vencimientocertificado
				FROM madera.discapacitados 
				WHERE codigocertificado is not null and codigocertificado != ''";
$resCodVto = mysql_query($sqlCodVto);
$arrayCodVto = array();
while ($rowCodVto = mysql_fetch_array($resCodVto)) {
	$arrayCodVto[$rowCodVto['cuil']] = array('codigo' => $rowCodVto['codigocertificado'], 'vto' => $rowCodVto['vencimientocertificado']);
}

while ($data = fgetcsv ($fp, 1000, ";")) { 
	if ($data['1'] == 'DS' || $data['1'] == 'DC') {
		$impCompTotal += str_replace(',','.',$data['13']);
		$impDebitoTotal += str_replace(',','.',$data['14']);
		$impNoInteTotal += str_replace(',','.',$data['15']);
		$impPedido += str_replace(',','.',$data['16']);
	}
	if ($data['1'] == 'DB') {
		$impCompTotalD += str_replace(',','.',$data['13']);
		$impDebitoTotalD += str_replace(',','.',$data['14']);
		$impNoInteTotalD += str_replace(',','.',$data['15']);
		$impPedidoD += str_replace(',','.',$data['16']);
	}

	try {
		$cuil = $data['3'];
		if (!esValidoCUIT($cuil)) {
			$error = "Error en el C.U.I.L. $cuil nro comprobante interno ".$data['0'];
			throw new Exception($error);
		}
		$cuit = $data['5'];
		$codpractica = $data['17'];
		if ($codpractica != 97 && $codpractica != 98 && $codpractica != 99) {
			if (!esValidoCUIT($cuit)) {
				$error = "Error en el C.U.I.T. $cuit nro comprobante interno ".$data['0'];
				throw new Exception($error);
			}
		} else {
			$cuit = (int) $cuit;
		}
		
		if ($data['20'] != 'S' and $data['20'] != 'N') {
			$error = "Error en la dependencia nro comprobante interno ".$data['0'];
			throw new Exception($error);
		}
		
		$impFactura = str_replace(',','.',$data['13']);
		$impDebito = str_replace(',','.',$data['14']);
		$impNoInte = str_replace(',','.',$data['15']);
		$impSolicit = str_replace(',','.',$data['16']);
		if ($data['1'] == 'DS' || $data['1'] == 'DC') { 
			if ($impFactura < $impSolicit) {
				$error = "El monto solicitado no puede ser superior al monto de la facutra nor comprobante interno ".$data['0'];
				throw new Exception($error);
			}
			$calculoSolicitado = $impFactura - $impDebito - $impNoInte;
			$calculoSolicitado = round($calculoSolicitado,2);
			if ($calculoSolicitado != $impSolicit) {
				$error = "El monto solicitado no concuerda con el comprobante, el debito y lo no integral ($calculoSolicitado)".$data['0'];
				throw new Exception($error);
			}
		}
		
		if (!array_key_exists($cuil,$arrayCodVto)) {
			$error = "El cuil '$cuil' no tiene cargada toda la inforacion completa del certificado de discapacidad";
			throw new Exception($error);
		}
	} catch (Exception $e) {
		$error = $e->getMessage();
		//echo $error;
		$redire = "Location: presentacion.error.php?page='Nueva Presentacion'&error=$error";
		Header($redire);
		exit -1;
	}
	
    $linea = "INSERT INTO intepresentaciondetalle VALUES (".str_replace('.','',$data['0']).",idpres,
    		'".$data['1']."',
    		".$data['2'].",
    		'".$cuil."',
    		'".$arrayCodVto[$cuil]['codigo']."',
    		'".$arrayCodVto[$cuil]['vto']."',
    		'".$data['4']."',
    		'".$cuit."',
    		'".$data['6']."',
    		".$data['7'].",
    		'".strtoupper($data['8'])."',
    		'".$data['9']."',
    		'".str_pad($data['10'],14,'0',STR_PAD_LEFT)."',
    		".$data['11'].",
    		'".$data['12']."',
    		".$impFactura.",
    		".$impDebito.",
    		".$impNoInte.",		
    		".$impSolicit.",
    		".$codpractica.",
    		".$data['18'].",
    		".$data['19'].",
    		'".strtoupper($data['20'])."',
    		NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)";
    $sqlInsertFacturas[$cantFacturas] = $linea;
    $cantFacturas++;
}

fclose ($fp);
$sqlInsertPresentacion = "INSERT INTO intepresentacion VALUES(DEFAULT, ".$_POST['idCronograma'].", NULL, NULL, NULL,
						$cantFacturas,$impCompTotal,$impDebitoTotal,$impNoInteTotal,$impPedido,
						$impCompTotalD,$impDebitoTotalD,$impNoInteTotalD,$impPedidoD,NULL,NULL,NULL,NULL,NULL)";

$anio = substr($_POST['carpeta'],0,4);
$carpetaanio = "archivos/$anio";
$carpetaGeneracion = "archivos/$anio/".$_POST['carpeta']."/generacion";
$carpetaResultados= "archivos/$anio/".$_POST['carpeta']."/resultados";
$carpetaFondos= "archivos/$anio/".$_POST['carpeta']."/fondos";

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
	if (!file_exists($carpetaFondos)) {
		mkdir($carpetaFondos, 0777, true);
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
	
	$sqlConsultaUlitmoID = "SELECT id FROM intepresentacion i ORDER BY id desc limit 1";
	$resConsultaUlitmoID = mysql_query($sqlConsultaUlitmoID);
	$rowConsultaUlitmoID = mysql_fetch_array($resConsultaUlitmoID);
	$idRestart = $rowConsultaUlitmoID['id'] + 1;
	
	$dbh->beginTransaction();
	$sqlUpdateAutoAuto = "ALTER TABLE intepresentacion AUTO_INCREMENT = $idRestart";
	$dbh->exec($sqlUpdateAutoAuto);
	$dbh->commit();
	
	$error = $e->getMessage()." (INSERT: ".$sqlinsert.")";
	//echo $error;
	$redire = "Location: presentacion.error.php?page='Nueva Presentacion'&error=$error";
	Header($redire);
	exit -1;
}



?>