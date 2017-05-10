<?php 
include_once 'include/conector.php';

$idPresentacion = $_POST['id'];
$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
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
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Formato'&error=".$e->getMessage();
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
			$importeComprobante = substr($arraylinea[13],0,8).".".substr($arraylinea[13],-2);
			$importeSolicitado = substr($arraylinea[14],0,8).".".substr($arraylinea[14],-2);
			
			if ($arraylinea[0] == 'DB') {
				$sumcompdok += $importeComprobante;
				$sumsolidok += $importeSolicitado;
			} else {
				$sumcompok += $importeComprobante;
				$sumsoliok += $importeSolicitado;
			}
			
			$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impcomprobanteformato = $importeComprobante, impsolicitadoformato = $importeSolicitado
												WHERE idpresentacion = $idPresentacion and tipoarchivo = '".$arraylinea[0]."' and cuil = '".(double)$arraylinea[2]."' and periodo =  '".$arraylinea[5]."' and
													  cuit = '".(double)$arraylinea[6]."' and tipocomprobante = ".(int)$arraylinea[7]." and tipoemision = '".$arraylinea[8]."' and
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
			$importeComprobante = substr($arraylinea[13],0,8).".".substr($arraylinea[13],-2);
			$importeSolicitado = substr($arraylinea[14],0,8).".".substr($arraylinea[14],-2);
			
			if ($arraylinea[0] == 'DB') {
				$sumcompdnok += $importeComprobante;
				$sumsolidnok += $importeSolicitado;
			} else {
				$sumcompnok += $importeComprobante;
				$sumsolinok += $importeSolicitado;
			}
			
			$arrayUpdate[$indexUpdate] = "UPDATE facturas SET deverrorformato = '".$arraylinea[19]."'
												WHERE idpresentacion = $idPresentacion and tipoarchivo = '".$arraylinea[0]."' and cuil = '".(double) $arraylinea[2]."' and periodo =  '".$arraylinea[5]."' and
													  cuit = '".(double)$arraylinea[6]."' and tipocomprobante = ".(int)$arraylinea[7]." and tipoemision = '".$arraylinea[8]."' and
													  fechacomprobante = '".$arraylinea[9]."' and cae = '".trim($arraylinea[10])."' and puntoventa = ".(int)$arraylinea[11]." and
													  nrocomprobante = '".(int)$arraylinea[12]."'";
			$indexUpdate++;
		}
	}
	fclose($fpnok);
}
$contadornok = sizeof($arrayUpdate) - $contadorok;
$sqlInsertPresentacionFormato = "INSERT INTO presentacionformato VALUES($idPresentacion,CURDATE(),$contadorok,$sumcompok,$sumsoliok,$sumcompdok,$sumsolidok,$contadornok,$sumcompnok,$sumsolinok,$sumcompdnok,$sumsolidnok)";
$total = $contadorok + $contadornok;
if ($total == $rowPresentacion['cantfactura']) {
	try {
		$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->beginTransaction();
		
		foreach ($arrayUpdate as $sqlUpdate) {
			//echo $sqlUpdate."<br>";
			$dbh->exec($sqlUpdate);
		}
		
		//echo $sqlInsertPresentacionFormato."<br>";
		$dbh->exec($sqlInsertPresentacionFormato);
		$dbh->commit();
		Header("Location: presentacion.detalle.php?id=$idPresentacion");
	} catch (PDOException $e) {
		$dbh->rollback();
		$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Formato'&error=".$e->getMessage();
		Header($redire);
	}
} else {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Formato'&error='No coninciden la cantidad de facturas'";
	Header($redire);
}

?>