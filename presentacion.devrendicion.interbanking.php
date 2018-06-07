<?php
include_once 'include/conector.php';

$usuario = $_SESSION['usuario'];
$idPresentacion = $_GET['id'];
$sqlFactura = "SELECT intepresentaciondetalle.*, madera.prestadoresauxiliar.cbu,
CASE
WHEN (madera.prestadores.situacionfiscal in (0,1,4) || (madera.prestadores.situacionfiscal = 3 and madera.prestadores.vtoexento >= CURDATE())) THEN 0
WHEN (madera.prestadores.situacionfiscal = 2 || (madera.prestadores.situacionfiscal = 3 and madera.prestadores.vtoexento < CURDATE())) THEN 1
END as retiene
FROM intepresentaciondetalle
LEFT JOIN madera.prestadores on intepresentaciondetalle.cuit = madera.prestadores.cuit
LEFT JOIN madera.prestadoresauxiliar on madera.prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
WHERE idpresentacion = $idPresentacion and deverrorintegral is null and codpractica not in (97,98,99)
ORDER BY cuit, periodo, codpractica";
$resFactura = mysql_query($sqlFactura);
$arrayFacturas = array();

while ($rowFactura = mysql_fetch_array($resFactura)) {
	$cuit = $rowFactura['cuit'];
	if ($rowFactura['tipoarchivo'] == 'DB') {
		$rowFactura['impdebito'] = (-1)*$rowFactura['impdebito'];
		$rowFactura['impnointe'] = (-1)*$rowFactura['impnointe'];
		$rowFactura['impcomprobanteintegral'] = (-1)*$rowFactura['impcomprobanteintegral'];
	}
	$monOS = $rowFactura['impcomprobanteintegral'] - $rowFactura['impmontosubsidio'] - $rowFactura['impdebito'] - $rowFactura['impnointe'];
	$rowFactura['montoobrasocial'] = round($monOS,2);

	if (isset($arrayFacturas[$cuit])) {
		$arrayFacturas[$cuit]['impcomprobanteintegral'] += $rowFactura['impcomprobanteintegral'];
		$arrayFacturas[$cuit]['impsolicitadosubsidio'] += $rowFactura['impsolicitadosubsidio'];
		$arrayFacturas[$cuit]['impdebito'] += $rowFactura['impdebito'];
		$arrayFacturas[$cuit]['impnointe'] += $rowFactura['impnointe'];
		$arrayFacturas[$cuit]['montoobrasocial'] += $rowFactura['montoobrasocial'];
		$arrayFacturas[$cuit]['impmontosubsidio'] += $rowFactura['impmontosubsidio'];
	} else {
		$arrayFacturas[$cuit] = array('impcomprobanteintegral' => $rowFactura['impcomprobanteintegral'],
				'impsolicitadosubsidio' => $rowFactura['impsolicitadosubsidio'],
				'impdebito' => $rowFactura['impdebito'],
				'retiene' => $rowFactura['retiene'],
				'impnointe' => $rowFactura['impnointe'],
				'montoobrasocial' => $rowFactura['montoobrasocial'],
				'impmontosubsidio' => $rowFactura['impmontosubsidio'],);
	}
}

foreach ($arrayFacturas as $key => $rowFactura) {
	$montoCalculo = $rowFactura['impcomprobanteintegral'] - $rowFactura['impdebito'];
	$montoRet = 0;
	if($rowFactura['retiene'] == 1) {
		if ($montoCalculo > 30000) {
			$montoRet = ($montoCalculo - 30000) * 0.02;
			if ($montoRet <= 90) {
				$montoRet = 0;
			}
		}
	}
	$arrayFacturas[$key]['imprete'] = round($montoRet,2);
	$apagar = $rowFactura['impcomprobanteintegral'] - $rowFactura['impdebito'] - $arrayFacturas[$key]['imprete'];
	$arrayFacturas[$key]['apagar'] = round($apagar,2);
}

$arrayInsertInter = array();
foreach ($arrayFacturas as $key => $rowFactura) {
	$arrayInsertInter[$key] = "INSERT INTO inteinterbanking VALUES($idPresentacion,'$key',
								".$rowFactura['impcomprobanteintegral'].",
								".$rowFactura['impdebito'].",			
								".$rowFactura['impnointe'].",
								".$rowFactura['impsolicitadosubsidio'].",
								".$rowFactura['montoobrasocial'].",
								".$rowFactura['impmontosubsidio'].",
								".$rowFactura['imprete'].",
								".$rowFactura['apagar'].",NULL,NULL)";
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	
	foreach ($arrayInsertInter as $insertInterbanking) {
		$dbh->exec($insertInterbanking);
		//echo $key."=>".$insertInterbanking."<br>";
	}
	
	$dbh->commit();
	Header("Location: presentacion.detalle.php?id=$idPresentacion");
} catch (PDOException $e) {
	$dbh->rollback();
	
	$dbh->beginTransaction();
	$sqlUpdteDetalle = "UPDATE intepresentaciondetalle SET impsolicitadosubsidio = NULL, impmontosubsidio = NULL where idpresentacion = $idPresentacion";
	$dbh->exec($sqlUpdteDetalle);
	$sqlDelteRendicion = "DELETE FROM interendicion where idpresentacion = $idPresentacion";
	$dbh->exec($sqlDelteRendicion);
	$sqlDeleteControl = "DELETE FROM interendicioncontrol where idpresentacion = $idPresentacion";
	$dbh->exec($sqlDeleteControl);
	$dbh->commit();
	
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Subsidio'&error=".$e->getMessage();
	Header($redire);
}


?>