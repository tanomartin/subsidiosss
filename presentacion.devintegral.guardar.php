<?php 
include_once 'include/conector.php';

$idPresentacion = $_POST['id'];
$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$archivook = $_FILES['archivook']['tmp_name'];
$archivoerror = $_FILES['archivoerror']['tmp_name'];


$arrayUpdate = array();
$indexUpdate = 0;

$sumcompok = 0;
$sumsoliok = 0;
if ($archivook != null) {
	$fpok = fopen($archivook, "r");
	while(!feof($fpok)) {
		$linea = fgets($fpok);
		if ($linea != '') {
			$arraylinea = explode("|", $linea);	
			$importeComprobante = $arraylinea[13];
			$sumcompok += $importeComprobante;
			$importeSolicitado =$arraylinea[14];
			$sumsoliok += $importeSolicitado;
			
			$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impcomprobanteintegral = $importeComprobante, impsolicitadointegral = $importeSolicitado
												WHERE idpresentacion = $idPresentacion and cuil = '".$arraylinea[2]."' and periodo =  '".$arraylinea[5]."' and
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
if ($archivoerror != null) {
	$fpnok = fopen($archivoerror, "r");
	while(!feof($fpnok)) {
		$linea = fgets($fpnok);
		if ($linea != '') {
			$arraylinea = explode("|", $linea);	
			$importeComprobante = substr($arraylinea[13],0,8).".".substr($arraylinea[13],-2);
			$sumcompnok += $importeComprobante;
			$importeSolicitado = substr($arraylinea[14],0,8).".".substr($arraylinea[14],-2);
			$sumsolinok += $importeSolicitado;
			
			$arrayUpdate[$indexUpdate] = "UPDATE facturas SET deverrorintegral = '".$arraylinea[19]."'
												WHERE idpresentacion = $idPresentacion and cuil = '".$arraylinea[2]."' and periodo =  '".$arraylinea[5]."' and
													  cuit = '".$arraylinea[6]."' and tipocomprobante = ".(int)$arraylinea[7]." and tipoemision = '".$arraylinea[8]."' and
													  fechacomprobante = '".$arraylinea[9]."' and cae = '".trim($arraylinea[10])."' and puntoventa = ".(int)$arraylinea[11]." and
													  nrocomprobante = '".(int)$arraylinea[12]."'";
			$indexUpdate++;
		}
	}
	fclose($fpnok);
}
$contadornok = sizeof($arrayUpdate) - $contadorok;
$sqlUpdatePresentacion = "UPDATE presentacion 
							SET fechaintegral = CURDATE(), 
								cantintegralok = $contadorok, 
								impcomprobantesintegralok = $sumcompok,
								impsolicitadointegranlok = $sumsoliok,
								cantintegralnok = $contadornok,
								impcomprobantesintegralnok = $sumcompnok,
								impsolicitadointegranlnok = $sumsolinok
							WHERE id = $idPresentacion";

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
		
		//echo $sqlUpdatePresentacion."<br>";
		$dbh->exec($sqlUpdatePresentacion);
		$dbh->commit();
		Header("Location: presentacion.detalle.php?id=$idPresentacion");
	} catch (PDOException $e) {
		echo $e->getMessage();
		$dbh->rollback();
	}
} else {
	echo "NO CUADRA";
}

?>