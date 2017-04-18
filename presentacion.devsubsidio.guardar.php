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
		$importeSolicitado = $arraylinea[5];
		$sumsoli += $importeSolicitado;
		$montoSubsidio =$arraylinea[7];
		$summonto += $montoSubsidio;
		
		/*$arrayUpdate[$indexUpdate] = "UPDATE facturas SET impcomprobanteintegral = $importeComprobante, impsolicitadointegral = $importeSolicitado
												WHERE idpresentacion = $idPresentacion and cuil = '".$arraylinea[2]."' and periodo =  '".$arraylinea[5]."' and
													  cuit = '".$arraylinea[6]."' and tipocomprobante = ".(int)$arraylinea[7]." and tipoemision = '".$arraylinea[8]."' and
													  fechacomprobante = '".$arraylinea[9]."' and cae = '".trim($arraylinea[10])."' and puntoventa = ".(int)$arraylinea[11]." and
													  nrocomprobante = '".(int)$arraylinea[12]."'";
		$indexUpdate++;*/
	}
}
echo "SOLI ".$sumsoli."<br>";
echo "SUBS ".$summonto."<br>";

$sqlUpdatePresentacion = "UPDATE presentacion
							SET fechasubsidio = CURDATE(),
							impsolicitadosubsidio = $sumsoli,
							montosubsidio = $summonto
							WHERE id = $idPresentacion";
echo $sqlUpdatePresentacion;
fclose($fpok);


?>