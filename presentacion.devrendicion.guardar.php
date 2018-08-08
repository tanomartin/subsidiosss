<?php 
include_once 'include/conector.php';
set_time_limit(0);
$idPresentacion = $_POST['id'];
$sqlPresentacion = "SELECT p.*, pi.impsolicitadointegraldok as totdebito, pi.cantintegralok as totalintegralok, c.periodo, c.carpeta 
					FROM intepresentacion p, intepresentacionintegral pi, intecronograma c 
					WHERE p.id = $idPresentacion and p.idcronograma = c.id and p.id = pi.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$archivo = $_FILES['archivoEnvio']['tmp_name'];
$archivoControl = $_FILES['archivoControl']['tmp_name'];

$anio = substr($rowPresentacion['carpeta'],0,4);
$carpetaResultados= "archivos/$anio/".$rowPresentacion['carpeta']."/resultados";
try {
	$archivodestino = $carpetaResultados."/".$_FILES['archivoEnvio']['name'];
	$archivodestinoControl = $carpetaResultados."/".$_FILES['archivoControl']['name'];
	copy($archivo, $archivodestino);
	copy($archivoControl, $archivodestinoControl);
} catch (Exception $e) {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Rendicion'&error=".$e->getMessage();
	Header($redire);
	exit -1;
}

$arrayUpdate = array();
$indexUpdate = 0;

$sumsoli = 0;
$sumliqui = 0;
$cantregi = 0;
$fpok = fopen($archivo, "r");
$insertRendicion = "INSERT INTO interendicion VALUES ";

$arrayLiquidado = array();
$index = "";
while(!feof($fpok)) {
	$linea = fgets($fpok);
	if ($linea != '') {
		$cantregi++;
		$arraylinea = explode("|", $linea);
		$importeLiquidado = (float) str_replace(",",".",$arraylinea[7]);
		$sumliqui += (float) $importeLiquidado;
		$importeSolicitado = (float) str_replace(",",".",$arraylinea[8]);
		$sumsoli += (float) $importeSolicitado;
		
		$insertRendicion .= "($arraylinea[0], $idPresentacion, '$arraylinea[1]', '$arraylinea[2]', $arraylinea[3], $arraylinea[4], '$arraylinea[5]', $arraylinea[6], $importeLiquidado, $importeSolicitado, '$arraylinea[9]', $arraylinea[10], $arraylinea[11], $arraylinea[12], $arraylinea[13]),";
	
		$index = $arraylinea[5]."-".$arraylinea[4]."-".(int)$arraylinea[6];
		if (isset($arrayLiquidado[$index])) {
			$arrayLiquidado[$index] += $importeLiquidado;
		} else {
			$arrayLiquidado[$index] = $importeLiquidado;
		}
	}
}

$insertRendicion = substr($insertRendicion, 0, -1);
$insertRendicion .= ";";
fclose($fpok);

$fpcontrol = fopen($archivoControl, "r");
$insertControl = "INSERT INTO interendicioncontrol VALUE ";
while(!feof($fpcontrol)) {
	$linea = fgets($fpcontrol);
	if ($linea != '') {
		$nroenvioafip = $arraylinea[0];
		$arraylinea = explode("|", $linea);
		$importeSolicitado = (float) str_replace(",",".",$arraylinea[2]);
		$importeLiquidado = (float) str_replace(",",".",$arraylinea[3]);
		
		if (round($importeSolicitado,2) != round($sumsoli,2)) {
			$error = "La suma del IMPORTE SOLICITADO ($sumsoli) de la rendicion no concuerda con el control ($importeSolicitado)";
			$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Rendicion'&error=$error";
			Header($redire);
			exit -1;
		}
		if (round($importeLiquidado,2) != round($sumliqui,2)) {
			$error = "La suma del IMPORTE LIQUIDADO ($sumliqui) de la rendicion no concuerda con el control ($importeLiquidado)";
			$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Rendicion'&error=$error";
			Header($redire);
			exit -1;
		}
		$insertControl .= "($arraylinea[0], $idPresentacion, CURDATE(), $arraylinea[1], $importeSolicitado, $importeLiquidado),";
	}
}
$insertControl = substr($insertControl, 0, -1);
$insertControl .= ";";
fclose($fpcontrol);

if ($cantregi != $rowPresentacion['totalintegralok']) {
	$error = "La Cantidad de Registros en la Rendicion ($cantregi) difiere con la cantadidad de registros validos integrales (".$rowPresentacion['totalintegralok'].")";
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Rendicion'&error=$error";
	Header($redire);
	exit -1;
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	//echo $insertRendicion."<br><br>";
	$dbh->exec($insertRendicion);
	//echo $insertControl."<br><br>";
	$dbh->exec($insertControl);
	$dbh->commit();
	
	$dbh->beginTransaction();
	
	$sqlRendicion = "SELECT * FROM interendicion WHERE idpresentacion = $idPresentacion";
	$resRendicion = mysql_query($sqlRendicion);
	$cantidadUpdate = 0;
	
	while ($rowRendicion = mysql_fetch_array($resRendicion)) {
		$indexLiqui = $rowRendicion['cuil']."-".$rowRendicion['periodoprestacion']."-".$rowRendicion['codpractica'];
		$impsoli = $rowRendicion['impsolicitado'];
		if ($rowRendicion['tipoarchivo'] == 'DB') {
			$impsoli = (-1)*$rowRendicion['impsolicitado'];
		}
		$sqlFactura = "SELECT tipoarchivo, nrocominterno,impcomprobanteintegral,impsolicitadointegral, codpractica 
									FROM intepresentaciondetalle 
									WHERE idpresentacion = $idPresentacion and 
										  deverrorformato is null and 
										  deverrorintegral is null and 
										  cuil = '".$rowRendicion['cuil']."' and 
										  periodo = '".$rowRendicion['periodoprestacion']."' and 
										  codpractica = ".$rowRendicion['codpractica']." and 
										  cuit = '".$rowRendicion['cuit']."' and
										  impsolicitadointegral = ".$impsoli." and
										  tipocomprobante = ".$rowRendicion['tipocomprobante']." and
										  nrocomprobante = ".$rowRendicion['nrocomprobante']." and
										  puntoventa = ".$rowRendicion['puntoventa']." and
										  tipoarchivo = '".$rowRendicion['tipoarchivo']."'";
		$resFactura = mysql_query($sqlFactura);
		$canFacutra = mysql_num_rows($resFactura);	
		if ($canFacutra == 1) {
			$cantidadUpdate++;
			if ($rowRendicion['tipoarchivo'] == 'DS') {
				if (round($arrayLiquidado[$indexLiqui],2) >= round($rowRendicion['impsolicitado'],2)) {		
					$arrayLiquidado[$indexLiqui] -= (float) $rowRendicion['impsolicitado'];
					$updateFactura = "UPDATE intepresentaciondetalle
										SET impsolicitadosubsidio = ".(float) $rowRendicion['impsolicitado'].", 
											impmontosubsidio = ".(float) $rowRendicion['impsolicitado']." 
										WHERE idpresentacion = $idPresentacion and 
											  deverrorformato is null and 
											  deverrorintegral is null and 
											  cuil = '".$rowRendicion['cuil']."' and 
											  periodo = '".$rowRendicion['periodoprestacion']."' and 
											  codpractica = ".$rowRendicion['codpractica']." and 
											  cuit = '".$rowRendicion['cuit']."' and
											  impsolicitadointegral = ".$impsoli." and
											  tipocomprobante = ".$rowRendicion['tipocomprobante']." and
											  nrocomprobante = ".$rowRendicion['nrocomprobante']." and
											  puntoventa = ".$rowRendicion['puntoventa']." and
											  tipoarchivo = '".$rowRendicion['tipoarchivo']."'";	
				} else {
					$nuevoMonto = 0.00;
					if ($arrayLiquidado[$indexLiqui] > 0) {
						$nuevoMonto = (float) $arrayLiquidado[$indexLiqui];
						$arrayLiquidado[$indexLiqui] = 0.00;
					} 
					$updateFactura = "UPDATE intepresentaciondetalle
										SET impsolicitadosubsidio = ".(float) $rowRendicion['impsolicitado'].",
											impmontosubsidio = ".(float) $nuevoMonto."
									  WHERE idpresentacion = $idPresentacion and
										 	deverrorformato is null and
											deverrorintegral is null and
											cuil = '".$rowRendicion['cuil']."' and
											periodo = '".$rowRendicion['periodoprestacion']."' and
											codpractica = ".$rowRendicion['codpractica']." and
											cuit = '".$rowRendicion['cuit']."' and
											impsolicitadointegral = ".$impsoli." and
											tipocomprobante = ".$rowRendicion['tipocomprobante']." and
											nrocomprobante = ".$rowRendicion['nrocomprobante']." and
											puntoventa = ".$rowRendicion['puntoventa']." and
											tipoarchivo = '".$rowRendicion['tipoarchivo']."'";
				}
			} else {
				$monto = (float) $arrayLiquidado[$indexLiqui];
				if ($rowRendicion['tipoarchivo'] == "DC" and $monto < 0) {
					$monto = 0;
				} else {
					if (round($arrayLiquidado[$indexLiqui],2) >= round($rowRendicion['impsolicitado'],2)) {
						$arrayLiquidado[$indexLiqui] -= (float) $rowRendicion['impsolicitado'];
						$monto = $rowRendicion['impsolicitado'];
					}
				}
				if ($rowRendicion['tipoarchivo'] == "DB" and $monto >= 0) {
					$monto = 0;
				}
				
				$updateFactura = "UPDATE intepresentaciondetalle
										SET impsolicitadosubsidio = ".(float) $rowRendicion['impsolicitado'].",
											impmontosubsidio = ".(float) $monto."
										WHERE idpresentacion = $idPresentacion and 
											  deverrorformato is null and 
											  deverrorintegral is null and 
											  cuil = '".$rowRendicion['cuil']."' and 
											  periodo = '".$rowRendicion['periodoprestacion']."' and 
											  codpractica = ".$rowRendicion['codpractica']." and 
											  cuit = '".$rowRendicion['cuit']."' and
											  impsolicitadointegral = ".$impsoli." and
											  tipocomprobante = ".$rowRendicion['tipocomprobante']." and
											  nrocomprobante = ".$rowRendicion['nrocomprobante']." and
											  puntoventa = ".$rowRendicion['puntoventa']." and
											  tipoarchivo = '".$rowRendicion['tipoarchivo']."'";	
			}
			//echo $updateFactura."<br>";
			$dbh->exec($updateFactura);
		} else {
			throw new PDOException("No se encontro una factura con $sqlFactura");
		}
	}
	if ($cantidadUpdate != $cantregi) {
		throw new PDOException('No concuerdan la cantidad de Facturas con los update realizados');
	}
	$dbh->commit();
	Header("Location: presentacion.devrendicion.interbanking.php?id=$idPresentacion");
} catch (PDOException $e) {
	$dbh->rollback();
	
	$dbh->beginTransaction();
	$sqlDeleteRendicion = "DELETE FROM interendicion WHERE idpresentacion = $idPresentacion";
	$dbh->exec($sqlDeleteRendicion);
	$sqlDeleteControl =  "DELETE FROM interendicioncontrol WHERE idpresentacion = $idPresentacion";
	$dbh->exec($sqlDeleteControl);
	$dbh->commit();
	
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Subsidio'&error=".$e->getMessage();
	Header($redire);
}
?>