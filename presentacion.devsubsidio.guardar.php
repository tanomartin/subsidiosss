<?php 
include_once 'include/conector.php';

$idPresentacion = $_POST['id'];
$sqlPresentacion = "SELECT p.*, pi.impsolicitadointegraldok as totdebito, c.periodo, c.carpeta FROM presentacion p, presentacionintegral pi, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id and p.id = pi.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$archivo = $_FILES['archivo']['tmp_name'];

$anio = substr($rowPresentacion['carpeta'],0,4);
$carpetaResultados= "archivos/$anio/".$rowPresentacion['carpeta']."/resultados";
try {
	$archivodestino = $carpetaResultados."/".$_FILES['archivo']['name'];
	copy($archivo, $archivodestino);
} catch (Exception $e) {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Subsidio'&error=".$e->getMessage();
	Header($redire);
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

		$sqlSelectFactura = "SELECT tipoarchivo, nrocominterno,impcomprobanteintegral,impsolicitadointegral, codpractica FROM facturas WHERE idpresentacion = $idPresentacion and deverrorformato is null and deverrorintegral is null and cuil = '".$arraylinea[3]."' and periodo = '".$arraylinea[4]."' and codpractica = ".(int) $arraylinea[6]." and tipoarchivo != 'DB'";
		$resSelectFactura = mysql_query($sqlSelectFactura);
		$canSelectFactura = mysql_num_rows($resSelectFactura);
		if ($canSelectFactura > 0) {
			$importeSolicitadoRestante = $importeSolicitado;
			$montoSubsidioRestante = $montoSubsidio;
			while ($rowSelectFactura = mysql_fetch_array($resSelectFactura)) {
				if ($rowSelectFactura['tipoarchivo'] == 'DS') {
					$importeSolicitadoRestante -= (float) $rowSelectFactura['impsolicitadointegral'];
					$montoSubsidioRestante -= (float) $rowSelectFactura['impsolicitadointegral'];
					$importeSolicitadoRestante = round($importeSolicitadoRestante, 2);
					$montoSubsidioRestante = round($montoSubsidioRestante, 2);		
					if ($importeSolicitadoRestante >= 0 and $montoSubsidioRestante >= 0) {
						$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impsolicitadosubsidio = ".(float) $rowSelectFactura['impsolicitadointegral'].", impmontosubsidio = ".(float) $rowSelectFactura['impsolicitadointegral']." WHERE nrocominterno = ".$rowSelectFactura['nrocominterno']. " and idpresentacion = $idPresentacion and codpractica = ".$rowSelectFactura['codpractica']." and deverrorformato is null and deverrorintegral is null and tipoarchivo != 'DB'";
						$indexUpdate++;
					} else {
						$montoNuevo = (float) $rowSelectFactura['impsolicitadointegral'] + $montoSubsidioRestante;
						$montoNuevo = round($montoNuevo, 2);
						if ($montoNuevo < 0) { $montoNuevo = 0; }
						$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impsolicitadosubsidio = ".(float) $rowSelectFactura['impsolicitadointegral'].", impmontosubsidio = ".(float) $montoNuevo." WHERE nrocominterno = ".$rowSelectFactura['nrocominterno']. " and idpresentacion = $idPresentacion and codpractica = ".$rowSelectFactura['codpractica']." and deverrorformato is null and deverrorintegral is null and tipoarchivo != 'DB'";;
						$indexUpdate++;
					}
				} else {
					$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impsolicitadosubsidio = ".(float) $importeSolicitado.", impmontosubsidio = ".(float) $montoSubsidio." WHERE nrocominterno = ".$rowSelectFactura['nrocominterno']. " and idpresentacion = $idPresentacion and codpractica = ".$rowSelectFactura['codpractica']." and deverrorformato is null and deverrorintegral is null and tipoarchivo != 'DB'";
					$indexUpdate++;
				}
			}
		} else {
			$error = "No se encontro la facutra. CONSULTA: $sqlSelectFactura";
			$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Subsidio'&error=$error";
			Header($redire);
			exit -1;
		}
	}
}

fclose($fpok);

//$sumsoli -= $rowPresentacion['totdebito'];
$sqlInsertPresentacionSubsidio = "INSERT INTO presentacionsubsidio VALUES($idPresentacion, CURDATE(),'$numliqui',$sumsoli,$summonto)";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();

	foreach ($arrayUpdate as $sqlUpdate) {
		//echo $sqlUpdate."<br>";
		$dbh->exec($sqlUpdate);
	}

	//echo $sqlInsertPresentacionSubsidio."<br>";
	$dbh->exec($sqlInsertPresentacionSubsidio);
	$dbh->commit();
	Header("Location: presentacion.detalle.php?id=$idPresentacion");
} catch (PDOException $e) {
	$dbh->rollback();
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Subsidio'&error=".$e->getMessage();
	Header($redire);
}
?>