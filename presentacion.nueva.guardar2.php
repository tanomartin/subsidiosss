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

$sqlControl = "SELECT f.id
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
echo $sqlControl."<br><br>";
$resControl = mysql_query($sqlControl);
$canControl = mysql_num_rows($resControl);
$arrayControl = array();
if ($canControl > 0) {
	$whereIn = "(";
	while ($rowControl = mysql_fetch_assoc($resControl)) {
		$arrayControl[$rowControl['id']] = $rowControl;
		$whereIn .= $rowControl['id'].",";
	}
	$whereIn = substr($whereIn, 0, -1);
	$whereIn .= ")";
}

echo "$whereIn.<br><br>"; 
?>