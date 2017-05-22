<?php 
include_once 'include/conector.php';

$idPresentacion = $_POST['id'];
$sqlPresentacion = "SELECT
p.id,
DATE_FORMAT(p.fechapresentacion, '%d-%m-%Y') as fechapresentacion,
DATE_FORMAT(p.fechacancelacion, '%d-%m-%Y') as fechacancelacion,
p.motivocancelacion,
p.cantfactura,
p.impcomprobantes,
p.impsolicitado ,
cronograma.periodo,
cronograma.carpeta,
presentacionformato.cantformatook
FROM presentacion p
INNER JOIN cronograma on p.idcronograma = cronograma.id
LEFT JOIN presentacionformato on p.id = presentacionformato.id
WHERE p.id = $idPresentacion";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$archivook = $_FILES['archivook']['tmp_name'];
$archivoerror = $_FILES['archivoerror']['tmp_name'];

$anio = substr($rowPresentacion['carpeta'],0,4);
$carpetaResultados= "archivos/$anio/".$rowPresentacion['carpeta']."/resultados";
try {
	$archivodestinook = $carpetaResultados."/".$_FILES['archivook']['name'];
	$archivodestinoerr = $carpetaResultados."/".$_FILES['archivoerror']['name'];
	copy($archivook, $archivodestinook);
	copy($archivoerror, $archivodestinoerr);
} catch (Exception $e) {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Integral'&error=".$e->getMessage();
	Header($redire);
	exit -1;
}

$arrayUpdate = array();
$indexUpdate = 0;

$sumcompok = 0;
$sumsoliok = 0;
$sumcompdok = 0;
$sumsolidok = 0;
if ($archivook != null) {
	$fpok = fopen($archivook, "r");
	while(!feof($fpok)) {
		$linea = fgets($fpok);
		if ($linea != '') {
			$arraylinea = explode("|", $linea);	
			$importeComprobante = (float) str_replace(",",".",$arraylinea[13]);
			$importeSolicitado = (float) str_replace(",",".",$arraylinea[14]);
		
			
			if ($arraylinea[0] == 'DB') {
				$sumcompdok += $importeComprobante;
				$sumsolidok += $importeSolicitado;
			} else {
				$sumcompok += $importeComprobante;
				$sumsoliok += $importeSolicitado;
			}
			
			$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impcomprobanteintegral = $importeComprobante, impsolicitadointegral = $importeSolicitado
												WHERE idpresentacion = $idPresentacion and tipoarchivo = '".$arraylinea[0]."' and cuil = '".$arraylinea[2]."' and periodo =  '".$arraylinea[5]."' and
													  cuit = '".$arraylinea[6]."' and tipocomprobante = ".(int)$arraylinea[7]." and tipoemision = '".$arraylinea[8]."' and
													  fechacomprobante = '".$arraylinea[9]."' and cae = '".trim($arraylinea[10])."' and puntoventa = ".(int)$arraylinea[11]." and
													  nrocomprobante = '".(int)$arraylinea[12]."'";
			$indexUpdate++;
		}
	}
	fclose($fpok);
}
$contadorok = sizeof($arrayUpdate);

$sumcompnok = 0;
$sumsolinok = 0;
$sumcompdnok = 0;
$sumsolidnok = 0;
if ($archivoerror != null) {
	$fpnok = fopen($archivoerror, "r");
	while(!feof($fpnok)) {
		$linea = fgets($fpnok);
		if ($linea != '') {
			$arraylinea = explode("|", $linea);	
			$importeComprobante = (float) str_replace(",",".",$arraylinea[13]);
			$importeSolicitado = (float) str_replace(",",".",$arraylinea[14]);
			
			if ($arraylinea[0] == 'DB') {
				$sumcompdnok += $importeComprobante;
				$sumsolidnok += $importeSolicitado;
			} else {
				$sumcompnok += $importeComprobante;
				$sumsolinok += $importeSolicitado;
			}
			$arrayUpdate[$indexUpdate] = "UPDATE facturas SET deverrorintegral = '".$arraylinea[19]."'
												WHERE idpresentacion = $idPresentacion and tipoarchivo = '".$arraylinea[0]."' and cuil = '".$arraylinea[2]."' and periodo =  '".$arraylinea[5]."' and
													  cuit = '".$arraylinea[6]."' and tipocomprobante = ".(int)$arraylinea[7]." and tipoemision = '".$arraylinea[8]."' and
													  fechacomprobante = '".$arraylinea[9]."' and cae = '".trim($arraylinea[10])."' and puntoventa = ".(int)$arraylinea[11]." and
													  nrocomprobante = '".(int)$arraylinea[12]."'";
			$indexUpdate++;
		}
	}
	fclose($fpnok);
}
$contadornok = sizeof($arrayUpdate) - $contadorok;

$sqlInsertPresentacionIntegral= "INSERT INTO presentacionintegral VALUES($idPresentacion,CURDATE(),$contadorok,$sumcompok,$sumsoliok,$sumcompdok,$sumsolidok,$contadornok,$sumcompnok,$sumsolinok,$sumcompdnok,$sumsolidnok)"; 
$total = $contadorok + $contadornok;
if ($total == $rowPresentacion['cantformatook']) {
	try {
		$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->beginTransaction();
		
		foreach ($arrayUpdate as $sqlUpdate) {
			//echo $sqlUpdate."<br>";
			$dbh->exec($sqlUpdate);
		}
		
		//echo $sqlInsertPresentacionIntegral."<br>";
		$dbh->exec($sqlInsertPresentacionIntegral);
		$dbh->commit();
		Header("Location: presentacion.detalle.php?id=$idPresentacion");
	} catch (PDOException $e) {
		$dbh->rollback();
		$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Integral'&error=".$e->getMessage();
		Header($redire);
	}
} else {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Integral'&error='No coninciden la cantidad de facturas'";
	Header($redire);
}
?>