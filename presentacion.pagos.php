<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];
$sqlSubsidio = "SELECT s.*, n.descripcion FROM subsidio s, nomenclador n WHERE  idpresentacion = $idPresentacion and s.codigopractica = n.codigo";
$resSubsidio = mysql_query($sqlSubsidio);
$arrayCompleto = array();
$index = 0;
while ($rowSubsidio = mysql_fetch_array($resSubsidio)) {
	$arrayCompleto[$index] = $rowSubsidio;
	
	$sqlSelectFactura = "SELECT * FROM facturas WHERE idpresentacion = $idPresentacion and deverrorformato is null and deverrorintegral is null and  cuil = '".$rowSubsidio['cuil']."' and periodo = '".$rowSubsidio['periodoprestacion']."' and codpractica = ".(int) $rowSubsidio['codigopractica'];
	$resSelectFactura = mysql_query($sqlSelectFactura);
	while($rowfactura = mysql_fetch_array($resSelectFactura)) {
		$arrayCompleto[$index]['f'][$rowfactura['nrocominterno']] = $rowfactura;
		$sqlPagos = "SELECT * FROM pagos WHERE idpresentacion = $idPresentacion and nrocominterno = ".$rowfactura['nrocominterno'];
		$resPagos = mysql_query($sqlPagos);
		while($rowPagos = mysql_fetch_array($resPagos)) {
			$arrayCompleto[$index]['f'][$rowfactura['nrocominterno']]['p'][$rowPagos['nrodepago']] = $rowPagos;
		}
	}
	$index++;
}

$lineas = array();
$indexLinea = 0;
foreach ($arrayCompleto as $key => $subsidio) {
	$linea = "<tr>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['periodopresentacion']."</td>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['periodoprestacion']."</td>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['cuil']."</td>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['codigopractica']."</td>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['descripcion']."</td>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['impsubsidiado']."</td>";
	$contadorFacturas = 0;
	foreach ($subsidio['f'] as $nrointerno => $factura) {
		if ($contadorFacturas != 0) {
			$linea .= "<tr>";
			$linea .= "<td colspan='6'></td>";
			$linea .= "<td style='font-size: 9px'>".$factura['nrocominterno']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['periodo']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['cuit']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['tipocomprobante']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['puntoventa']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['nrocomprobante']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['impsolicitado']."</td>";
		} else {
			$linea .= "<td style='font-size: 9px'>".$factura['nrocominterno']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['periodo']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['cuit']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['tipocomprobante']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['puntoventa']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['nrocomprobante']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['impsolicitado']."</td>";
		}
		$contadorFacturas++;
		
		$contadorPagos = 0;
		foreach ($subsidio['f'][$nrointerno]['p'] as $nropago => $pago) {
			if ($contadorPagos != 0) {
				$linea .= "<tr>";
				$linea .= "<td colspan='13'></td>";
				$linea .= "<td style='font-size: 9px'>".$pago['nroordenpago']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['fechatransferencia']."</td>";
				$linea .= "<td style='font-size: 9px'>CBU</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['importepagado']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['retganancias']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['retingresosbrutos']."</td>";
				$linea .= "<td></td>";
				$linea .= "<td style='font-size: 9px'>".$pago['importepagado']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['importepagado']."</td>";
				$linea .= "<td style='font-size: 9px'>REC</td>";
				$linea .= "<td style='font-size: 9px'>ASI</td>";
				$linea .= "<td style='font-size: 9px'>FOL</td>";
				$linea .= "</tr>";
			} else {
				$linea .= "<td style='font-size: 9px'>".$pago['nroordenpago']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['fechatransferencia']."</td>";
				$linea .= "<td style='font-size: 9px'>CBU</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['importepagado']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['retganancias']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['retingresosbrutos']."</td>";
				$linea .= "<td style='font-size: 9px'></td>";
				$linea .= "<td style='font-size: 9px'>".$pago['importepagado']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['importepagado']."</td>";
				$linea .= "<td style='font-size: 9px'>REC</td>";
				$linea .= "<td style='font-size: 9px'>ASI</td>";
				$linea .= "<td style='font-size: 9px'>FOL</td>";
			}
			$contadorPagos++;
		}
	}	
	$totalFilas = $contadorFacturas + $contadorPagos - 1;
	$linea .= "</tr>";
	
	$linea = str_replace("cantFilas", (string) $totalFilas, $linea);
	//$linea = str_replace("totalPagos", (string) $contadorPagos, $linea);
	
	$lineas[$indexLinea] = $linea;
	$indexLinea++;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Facturas Presentaciones S.S.S. :.</title>

<title>.: Detalle Pagos S.S.S. :.</title>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	
	 	<?php include_once("include/detalle.php")?>
	 	
	 	<h2>Detalle de Pagos</h2>
	 	<div class="grilla">
		 	<table>
				 <thead>
				 	<tr>
				 		<th  style="font-size: 9px" colspan="6">LIQ SSSALUD</th>
				 		<th  style="font-size: 9px" colspan="7">INF. PRESENTADA POR LA OS EN CADA PERIODO</th>
				 		<th  style="font-size: 9px" colspan="12">INFORMACION ADICIONAL QUE DEBE COMPLETAR LA OBRA SOCIAL</th>
				 	</tr>
				 	<tr>
				 		<th style="font-size: 9px">MES PROCESO</th>
				 		<th style="font-size: 9px">MES PRESTAC</th>
				 		<th style="font-size: 9px">CUIL BENEFIC</th>
				 		<th style="font-size: 9px">COD PREST</th>
				 		<th style="font-size: 9px">PRESTACION</th>
				 		<th style="font-size: 9px">IMPORTE LIQUIDADO</th>
				 		
				 		<th style="font-size: 9px">NRO INTERNO.</th>
				 		<th style="font-size: 9px">PERIODO FC</th>
				 		<th style="font-size: 9px">CUIT</th>
				 		<th style="font-size: 9px">LETRA</th>
				 		<th style="font-size: 9px">PV</th>
				 		<th style="font-size: 9px">Nº FC</th>
				 		<th style="font-size: 9px">IMPORTE SOLICITADO</th>
				 		
				 		<th style="font-size: 9px">ORDEN DE PAGO</th>
				 		<th style="font-size: 9px">FECHA DE TRANSF.</th>
				 		<th style="font-size: 9px">CBU</th>
				 		<th style="font-size: 9px">IMPORTE TRANSF.</th>
				 		<th style="font-size: 9px">RETENCION GCIAS.</th>
				 		<th style="font-size: 9px">RETENCION INGRESOS BRUTOS</th>
				 		<th style="font-size: 9px">OTRAS RETENC</th>
				 		<th style="font-size: 9px">IMPORTE APOLICADO SSS</th>
				 		<th style="font-size: 9px">IMPORTE FONDOS PROPIOS</th>
				 		<th style="font-size: 9px">NRO. RECIBO</th>
				 		<th style="font-size: 9px">ASI</th>
				 		<th style="font-size: 9px">FOL</th>
				 	</tr>
				 </thead>
				 <tbody>
		 			<?php foreach($lineas as $linea) {
		 				echo $linea;
		 			}?>
		 		 </tbody>
		 	</table>
	 	</div>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>