<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];
$sqlSubsidio = "SELECT s.*, n.descripcion FROM subsidio s, nomenclador n WHERE  idpresentacion = $idPresentacion and s.codigopractica = n.codigo";
$resSubsidio = mysql_query($sqlSubsidio);
$arrayCompleto = array();
$index = 0;
while ($rowSubsidio = mysql_fetch_array($resSubsidio)) {
	$arrayCompleto[$index] = $rowSubsidio;
	$sqlSelectFactura = "SELECT f.*, comprobante.descripcion as comprobante, cbu.cbu  
							FROM facturas f
							LEFT JOIN cbu ON f.cuit = cbu.cuit
              				LEFT JOIN comprobante ON f.tipocomprobante = comprobante.codigo
							WHERE f.idpresentacion = $idPresentacion and f.deverrorformato is null and 
								  f.deverrorintegral is null and f.cuil = '".$rowSubsidio['cuil']."' and 
								  f.periodo = '".$rowSubsidio['periodoprestacion']."' and 
								  f.codpractica = ".(int) $rowSubsidio['codigopractica'];
	$resSelectFactura = mysql_query($sqlSelectFactura);
	while($rowfactura = mysql_fetch_array($resSelectFactura)) {
		$arrayCompleto[$index]['f'][$rowfactura['nrocominterno']] = $rowfactura;
		$sqlPagos = "SELECT * FROM pagos WHERE idpresentacion = $idPresentacion and nrocominterno = ".$rowfactura['nrocominterno'];
		$resPagos = mysql_query($sqlPagos);
		$control = 0;
		while($rowPagos = mysql_fetch_array($resPagos)) {
			if ($control != 0) {
				$rowPagos['retingresosbrutos'] = "0.00";
				$rowPagos['retganancias'] = "0.00";
			}
			$arrayCompleto[$index]['f'][$rowfactura['nrocominterno']]['p'][$rowPagos['nrodepago']] = $rowPagos;
			$control++;
		}
	}
	$index++;
}

$lineas = array();
$indexLinea = 0;
$totalSubsidio = 0;
$totalSolicitado = 0;
$totalPagoS = 0;
$totalPagoO = 0;
foreach ($arrayCompleto as $key => $subsidio) {
	$totalSubsidio += (float) $subsidio['impsubsidiado'];
	$linea = "<tr>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['periodopresentacion']."</td>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['periodoprestacion']."</td>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['cuil']."</td>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['codigopractica']."</td>";
	$linea .= "<td style='font-size: 9px'>".$subsidio['descripcion']."</td>";
	$linea .= "<td style='font-size: 9px'>".number_format($subsidio['impsubsidiado'],"2",",",".")."</td>";
	$contadorFacturas = 0;
	foreach ($subsidio['f'] as $nrointerno => $factura) {
		$totalSolicitado += (float) $factura['impsolicitado'];
		if ($contadorFacturas != 0) {
			$linea .= "<tr>";
			$linea .= "<td colspan='6'></td>";
			$linea .= "<td style='font-size: 9px'>".$factura['nrocominterno']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['periodo']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['cuit']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['comprobante']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['puntoventa']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['nrocomprobante']."</td>";
			$linea .= "<td style='font-size: 9px'>".number_format($factura['impsolicitado'],"2",",",".")."</td>";
		} else {
			$linea .= "<td style='font-size: 9px'>".$factura['nrocominterno']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['periodo']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['cuit']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['comprobante']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['puntoventa']."</td>";
			$linea .= "<td style='font-size: 9px'>".$factura['nrocomprobante']."</td>";
			$linea .= "<td style='font-size: 9px'>".number_format($factura['impsolicitado'],"2",",",".")."</td>";
		}
		$contadorFacturas++;
		
		$contadorPagos = 0;
		foreach ($subsidio['f'][$nrointerno]['p'] as $nropago => $pago) {
			
			$nrotran = $pago['nrotransferencia'];
			$tipo = substr($nrotran, 0, 2);
			if ($tipo == 'TS') {
				$totalPagoS += (float) $pago['importepagado'];
				$imporPagoS = (float) $pago['importepagado'];
				$imporPagoO = 0;
			} else {
				$totalPagoO += (float) $pago['importepagado'];
				$imporPagoO = (float) $pago['importepagado'];
				$imporPagoS = 0;
			}
			
			if ($contadorPagos != 0) {
				$linea .= "<tr>";
				$linea .= "<td colspan='13'></td>";
				$linea .= "<td style='font-size: 9px'>".$pago['nroordenpago']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['fechatransferencia']."</td>";
				$linea .= "<td style='font-size: 9px'>".$factura['cbu']."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($pago['importepagado'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($pago['retganancias'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($pago['retingresosbrutos'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format("0","2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($imporPagoS,"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($imporPagoO,"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['recibo']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['asiento']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['folio']."</td>";	
				$linea .= "<td style='font-size: 9px'><input type='button' value='Cargar' onclick='location=\"presentacion.pagos.carga.php?idpresentacion=$idPresentacion&nrocomint=".$factura['nrocominterno']."&norord=".$pago['nroordenpago']."\"'/></td>";
				$linea .= "</tr>";
			} else {
				$linea .= "<td style='font-size: 9px'>".$pago['nroordenpago']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['fechatransferencia']."</td>";
				$linea .= "<td style='font-size: 9px'>".$factura['cbu']."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($pago['importepagado'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($pago['retganancias'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($pago['retingresosbrutos'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format("0","2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($imporPagoS,"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".number_format($imporPagoO,"2",",",".")."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['recibo']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['asiento']."</td>";
				$linea .= "<td style='font-size: 9px'>".$pago['folio']."</td>";
				$linea .= "<td style='font-size: 9px'><input type='button' value='Cargar' onclick='location=\"presentacion.pagos.carga.php?idpresentacion=$idPresentacion&nrocomint=".$factura['nrocominterno']."&norord=".$pago['nroordenpago']."\"'/></td>";
			}
			$contadorPagos++;
		}
	}	
	$totalFilas = $contadorFacturas + $contadorPagos - 1;
	$linea .= "</tr>";
	$linea = str_replace("cantFilas", (string) $totalFilas, $linea);
	$lineas[$indexLinea] = $linea;
	$indexLinea++;
}

$lineaTotales = "<tr>
					 <td colspan='5'></td>
					 <td>".number_format($totalSubsidio,"2",",",".")."</td>
					 <td colspan='6'></td>
					 <td>".number_format($totalSolicitado,"2",",",".")."</td>
					 <td colspan='7'></td>
					 <td>".number_format($totalPagoS,"2",",",".")."</td>
					 <td>".number_format($totalPagoO,"2",",",".")."</td>
					 <td colspan='4'></td>
				</tr>";
$lineas[$indexLinea] = $lineaTotales;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
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
	 	
	 	<p><input type="button" value="Generar Informe" onclick="location='presentacion.pagosExcel.php?id=<?php echo $idPresentacion?>'"/></p>
	 	
	 	<div class="grilla">
		 	<table>
				 <thead>
				 	<tr>
				 		<th  style="font-size: 9px" colspan="6">LIQ SSSALUD</th>
				 		<th  style="font-size: 9px" colspan="7">INF. PRESENTADA POR LA OS EN CADA PERIODO</th>
				 		<th  style="font-size: 9px" colspan="13">INFORMACION ADICIONAL QUE DEBE COMPLETAR LA OBRA SOCIAL</th>
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
				 		<th style="font-size: 9px"></th>
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