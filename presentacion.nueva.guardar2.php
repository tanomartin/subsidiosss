<?php
include_once 'include/conector.php';
set_time_limit(0);

var_dump($_POST);echo "<br><br>";
$idCarpeta = $_POST['idCronograma'];

$whereNotIn = " f.id not in (";
$banderaOut = 0;
foreach ($_POST as $key => $datos) {
	$pos = strpos($key, "sacar");
	if ($pos !== false) {
		$banderaOut = 1;
		$whereNotIn .= $datos.",";
	}
}
$whereNotIn = substr($whereNotIn, 0, -1);
$whereNotIn .= ") AND ";

if ($banderaOut == 0) { $whereNotIn = ""; }

echo $idCarpeta."<br><br>";
echo $whereNotIn."<br><br>";

$sqlAPresentar = "SELECT c.*,DATE_FORMAT(c.fechacierre,'%d/%m/%Y') as fechacierre FROM intecronograma c WHERE fechacierre >= CURDATE() LIMIT 1";
$resAPresentar = mysql_query($sqlAPresentar);
$rowAPresentar = mysql_fetch_array($resAPresentar);

$sqlUltimaActiva = "SELECT * FROM intepresentacion WHERE fechacancelacion is null ORDER BY id DESC LIMIT 1";
$resUltimaActiva = mysql_query($sqlUltimaActiva);
$rowUltimaActiva = mysql_fetch_assoc($resUltimaActiva);
$idPresActiva = $rowUltimaActiva['id'];

$sqlIdFactura = "SELECT f.id
				FROM madera.facturasprestaciones p, 
					 madera.facturasintegracion i,  
					 madera.practicas pr, 
					 madera.facturas f
				LEFT JOIN madera.prestadores ON madera.prestadores.codigoprestador = f.idPrestador
				WHERE
					$whereNotIn
					autorizacionpago = 0 AND
					f.id = p.idfactura AND
					p.id = i.idfacturaprestacion AND
					p.idPractica = pr.idpractica AND
					f.id NOT IN (SELECT DISTINCT nrocominterno
								FROM intepresentaciondetalle
								WHERE idpresentacion = $idPresActiva)";
echo $sqlIdFactura."<br><br>";
$resIdFactura = mysql_query($sqlIdFactura);
$canIdFactura = mysql_num_rows($resIdFactura);
if ($canIdFactura != 0) {
	$arrayIdFactura = array();
	$whereIn = "(";
	while ($rowControl = mysql_fetch_assoc($resIdFactura)) {
		$arrayControl[$rowControl['id']] = $rowControl;
		$whereIn .= $rowControl['id'].",";
	}
	$whereIn = substr($whereIn, 0, -1);
	
	echo "$whereIn.<br><br>"; 
	
	$sqlFacturas = "SELECT f.id, 'DS' as tipo, 111001 as codigoOS,
					       (CASE
					           WHEN titulares.cuil is not NULL THEN titulares.cuil
					           WHEN titularesdebaja.cuil is not NULL THEN titularesdebaja.cuil
					           WHEN familiares.cuil is not NULL THEN familiares.cuil
					           WHEN familiaresdebaja.cuil is not NULL THEN familiaresdebaja.cuil
					       END) as cuil,
					       DATE_FORMAT(fp.fechapractica,'%Y%m') as periodo,
					       p.cuit, p.nombre, f.idTipocomprobante, 'E',
					       DATE_FORMAT(f.fechacomprobante,'%d/%m/%Y') as fechacomprobante,
					       f.nroautorizacion, f.puntodeventa, f.nrocomprobante, f.importecomprobante,
					       f.totaldebito, 
						   0 as nointe, 
						   fi.totalsolicitado,
					       FORMAT(ps.codigopractica,0) as codigopractica,
					       IF (ps.codigopractica = 86 or ps.codigopractica = 87, 1, ROUND(fp.cantidad,0)) as cantidad, 
						   0 as Prov,
					       IF (fi.dependencia = 1, 'S', 'N') as DEP
					FROM facturas f, facturasprestaciones fp, facturasintegracion fi, prestadores p, practicas ps, facturasbeneficiarios fb
					LEFT JOIN titulares ON fb.nroorden = 0 AND
					                       titulares.nroafiliado = fb.nroafiliado
					LEFT JOIN titularesdebaja ON fb.nroorden = 0 AND
					                             titularesdebaja.nroafiliado = fb.nroafiliado
					LEFT JOIN familiares ON fb.nroorden != 0 AND
					                        familiares.nroafiliado = fb.nroafiliado AND
					                        familiares.nroorden = fb.nroorden
					LEFT JOIN familiaresdebaja ON fb.nroorden != 0  AND
					                              familiaresdebaja.nroafiliado = fb.nroafiliado AND
					                              familiaresdebaja.nroorden = fb.nroorden
					WHERE 
					f.id in $whereIn and
					f.id = fb.idFactura and 
					f.autorizacionpago = 0 and 
					fb.id = fp.idFacturabeneficiario and 
					fp.id = fi.idFacturaprestacion and
					f.idPrestador = p.codigoprestador and
					fp.idpractica = ps.idpractica
					ORDER BY periodo, f.id";
	
	echo $sqlFacturas."<br><br>";
	
	$sqlEscuelas = "SELECT f.id, 'DS', 111001,
							(CASE
							    WHEN titulares.cuil is not NULL THEN titulares.cuil
							    WHEN titularesdebaja.cuil is not NULL THEN titularesdebaja.cuil
							    WHEN familiares.cuil is not NULL THEN familiares.cuil
							    WHEN familiaresdebaja.cuil is not NULL THEN familiaresdebaja.cuil
							END) as cuil,
							DATE_FORMAT(fp.fechapractica,'%Y%m') as periodo,
							e.cue,
							concat(e.nombre,' - ',p.descripcion),
							0,'N',
							DATE_FORMAT(fp.fechapractica,'01/%m/%Y') as fechaescuela,
							0,0,0,0.00,0.00,0.00,0.00,0.00,
							FORMAT(p.codigopractica,0) as codigopractica,
							1,0,'N'
					FROM facturas f, facturasprestaciones fp, facturasintegracion fi, practicas p, escuelas e, facturasbeneficiarios fb
					LEFT JOIN titulares ON fb.nroorden = 0 AND
					                       titulares.nroafiliado = fb.nroafiliado
					LEFT JOIN titularesdebaja ON fb.nroorden = 0 AND
					                             titularesdebaja.nroafiliado = fb.nroafiliado
					LEFT JOIN familiares ON fb.nroorden != 0 AND
					                        familiares.nroafiliado = fb.nroafiliado AND
					                        familiares.nroorden = fb.nroorden
					LEFT JOIN familiaresdebaja ON fb.nroorden != 0  AND
					                              familiaresdebaja.nroafiliado = fb.nroafiliado AND
					                              familiaresdebaja.nroorden = fb.nroorden
					WHERE 
					f.id in $whereIn and
					f.id = fb.idFactura and fb.id = fp.idFacturabeneficiario and
					f.autorizacionpago = 0 and
					f.id = fp.idFactura and
					fp.id = fi.idFacturaprestacion and
					fi.tipoescuela is not NULL and
					fi.tipoescuela = p.idpractica and
					fi.idEscuela = e.id";
	
	echo $sqlEscuelas."<br><br>";
} else {
	echo "NO HAY FACTURAS PARA PRESENTAR";
}
?>