<?php 
include_once 'include/conector.php';

$idPresentacion = $_POST['id'];
$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$archivo = $_FILES['archivo']['tmp_name'];

$anio = substr($rowPresentacion['carpeta'],0,4);
$carpetaResultados= "archivos/$anio/".$rowPresentacion['carpeta']."/resultados";
try {
	$archivodestino = $carpetaResultados."/".$_FILES['archivo']['name'];
	copy($archivo, $archivodestino);
} catch (Exception $e) {
	echo $e->getMessage();
	exit -1;
}

$arrayUpdate = array();
$indexUpdate = 0;

$sumsoli = 0;
$summonto = 0;
$fpok = fopen($archivo, "r");
while(!feof($fpok)) {
	$linea = fgets($fpok);
	if ($linea != '') {
		
		$arraylinea = explode("|", $linea);	
		$numliqui = $arraylinea[0];
		$importeSolicitado = (float) str_replace(",",".",$arraylinea[5]);
		$sumsoli += $importeSolicitado;
		$montoSubsidio = (float) str_replace(",",".",$arraylinea[7]);
		$summonto += $montoSubsidio;
		
		$sqlSelectFactura = "SELECT nrocominterno,impcomprobanteintegral,impsolicitadointegral FROM facturas WHERE idpresentacion = $idPresentacion and deverrorformato is null and deverrorintegral is null and  cuil = '".$arraylinea[3]."' and periodo = '".$arraylinea[4]."' and impsolicitadointegral = $importeSolicitado and codpractica = ".(int) $arraylinea[6];
		$resSelectFactura = mysql_query($sqlSelectFactura);
		$canSelectFactura = mysql_num_rows($resSelectFactura);
		if ($canSelectFactura == 1) {
			$rowSelectFactura = mysql_fetch_array($resSelectFactura);
			$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impsolicitadosubsidio = ".(float) $importeSolicitado.", impmontosubsidio = ".(float) $montoSubsidio." WHERE nrocominterno = ".$rowSelectFactura['nrocominterno']." and idpresentacion = $idPresentacion and deverrorformato is null and deverrorintegral is null";
			$indexUpdate++;
		} else {
			$sqlSelectFactura = "SELECT nrocominterno,impcomprobanteintegral,impsolicitadointegral FROM facturas WHERE idpresentacion = $idPresentacion and deverrorformato is null and deverrorintegral is null and cuil = '".$arraylinea[3]."' and periodo = '".$arraylinea[4]."' and codpractica = ".(int) $arraylinea[6];
			$resSelectFactura = mysql_query($sqlSelectFactura);
			$canSelectFactura = mysql_num_rows($resSelectFactura);
			if ($canSelectFactura > 0) {
				$importeSolicitadoRestante = $importeSolicitado;
				$montoSubsidioRestante = $montoSubsidio;
				while ($rowSelectFactura = mysql_fetch_array($resSelectFactura)) {
					$importeSolicitadoRestante -= (float) $rowSelectFactura['impsolicitadointegral'];
					$montoSubsidioRestante -= (float) $rowSelectFactura['impsolicitadointegral'];
					$importeSolicitadoRestante = round($importeSolicitadoRestante, 2);
					$montoSubsidioRestante = round($montoSubsidioRestante, 2);		
					if ($importeSolicitadoRestante >= 0 and $montoSubsidioRestante >= 0) {
						$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impsolicitadosubsidio = ".(float) $rowSelectFactura['impsolicitadointegral'].", impmontosubsidio = ".(float) $rowSelectFactura['impsolicitadointegral']." WHERE nrocominterno = ".$rowSelectFactura['nrocominterno']. " and idpresentacion = $idPresentacion and deverrorformato is null and deverrorintegral is null";
						$indexUpdate++;
					} else {
						$montoNuevo = (float) $rowSelectFactura['impsolicitadointegral'] + $montoSubsidioRestante;
						$montoNuevo = round($montoNuevo, 2);
						$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impsolicitadosubsidio = ".(float) $rowSelectFactura['impsolicitadointegral'].", impmontosubsidio = ".(float) $montoNuevo." WHERE nrocominterno = ".$rowSelectFactura['nrocominterno']. " and idpresentacion = $idPresentacion and deverrorformato is null and deverrorintegral is null";;
						$indexUpdate++;
					}
				}
			} else {
				echo "PROBLEMAS<br>";
				exit -1;
			}
		}
	}
}
fclose($fpok);

$sqlUpdatePresentacion = "UPDATE presentacion
							SET fechasubsidio = CURDATE(),
								numliquidacion = '$numliqui',
								impsolicitadosubsidio = $sumsoli,
								montosubsidio = $summonto
							WHERE id = $idPresentacion";

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
?>