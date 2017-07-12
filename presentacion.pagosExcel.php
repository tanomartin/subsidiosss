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
		$sqlPagos = "SELECT p.*, DATE_FORMAT(p.fechatransferencia, '%d-%m-%Y') as fechatransferencia FROM pagos p WHERE idpresentacion = $idPresentacion and nrocominterno = ".$rowfactura['nrocominterno'];
		$resPagos = mysql_query($sqlPagos);
		while($rowPagos = mysql_fetch_array($resPagos)) {
			$arrayCompleto[$index]['f'][$rowfactura['nrocominterno']]['p'][$rowPagos['nrodepago']] = $rowPagos;
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
	$linea .= "<td style='font-size: 7px'>".$subsidio['periodopresentacion']."</td>";
	$linea .= "<td style='font-size: 7px'>".$subsidio['periodoprestacion']."</td>";
	$linea .= "<td style='font-size: 7px'>".$subsidio['cuil']."</td>";
	$linea .= "<td style='font-size: 7px'>".$subsidio['codigopractica']."</td>";
	$linea .= "<td style='font-size: 7px'>".$subsidio['descripcion']."</td>";
	$linea .= "<td style='font-size: 7px'>".number_format($subsidio['impsubsidiado'],"2",",",".")."</td>";
	$contadorFacturas = 0;
	foreach ($subsidio['f'] as $nrointerno => $factura) {
		$totalSolicitado += (float) $factura['impsolicitado'];
		if ($contadorFacturas != 0) {
			$linea .= "<tr>";
			$linea .= "<td colspan='6'></td>";
			$linea .= "<td style='font-size: 7px'>".$factura['periodo']."</td>";
			$linea .= "<td style='font-size: 7px'>".$factura['cuit']."</td>";
			$linea .= "<td style='font-size: 7px'>".$factura['comprobante']."</td>";
			$linea .= "<td style='font-size: 7px'>".$factura['puntoventa']."</td>";
			$linea .= "<td style='font-size: 7px'>".$factura['nrocomprobante']."</td>";
			$linea .= "<td style='font-size: 7px'>".number_format($factura['impsolicitado'],"2",",",".")."</td>";
		} else {
			$linea .= "<td style='font-size: 7px'>".$factura['periodo']."</td>";
			$linea .= "<td style='font-size: 7px'>".$factura['cuit']."</td>";
			$linea .= "<td style='font-size: 7px'>".$factura['comprobante']."</td>";
			$linea .= "<td style='font-size: 7px'>".$factura['puntoventa']."</td>";
			$linea .= "<td style='font-size: 7px'>".$factura['nrocomprobante']."</td>";
			$linea .= "<td style='font-size: 7px'>".number_format($factura['impsolicitado'],"2",",",".")."</td>";
		}
		$contadorFacturas++;
		
		$contadorPagos = 0;
		foreach ($subsidio['f'][$nrointerno]['p'] as $nropago => $pago) {
			$nrotran = $pago['nrotransferencia'];
			$tipo = substr($nrotran, 0, 2);
			if ($tipo == 'TS') {
				$totalPagoS += (float) $pago['importepagado'] + $pago['retganancias'];
				$imporPagoS = (float) $pago['importepagado'] + $pago['retganancias'];
				$imporPagoO = 0;
			} else {
				$totalPagoO += (float) $pago['importepagado'] + $pago['retganancias'];
				$imporPagoO = (float) $pago['importepagado'] + $pago['retganancias'];
				$imporPagoS = 0;
			}
			if ($contadorPagos != 0) {
				$linea .= "<tr>";
				$linea .= "<td colspan='12'></td>";
				$linea .= "<td style='font-size: 7px'>".$pago['nroordenpago']."</td>";
				$linea .= "<td style='font-size: 7px'>".$pago['fechatransferencia']."</td>";
				$linea .= "<td style='font-size: 7px'>'".$factura['cbu']."'</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($pago['importepagado'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($pago['retganancias'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($pago['retingresosbrutos'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format("0","2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($imporPagoS,"2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($imporPagoO,"2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".$pago['recibo']."</td>";
				$linea .= "<td style='font-size: 7px'>".$pago['asiento']."</td>";
				$linea .= "<td style='font-size: 7px'>".$pago['folio']."</td>";		
				$linea .= "</tr>";
			} else {
				$linea .= "<td style='font-size: 7px'>".$pago['nroordenpago']."</td>";
				$linea .= "<td style='font-size: 7px'>".$pago['fechatransferencia']."</td>";
				$linea .= "<td style='font-size: 7px'>'".$factura['cbu']."'</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($pago['importepagado'],'2',',','.')."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($pago['retganancias'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($pago['retingresosbrutos'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format("0","2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($imporPagoS,"2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".number_format($imporPagoO,"2",",",".")."</td>";
				$linea .= "<td style='font-size: 7px'>".$pago['recibo']."</td>";
				$linea .= "<td style='font-size: 7px'>".$pago['asiento']."</td>";
				$linea .= "<td style='font-size: 7px'>".$pago['folio']."</td>";
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
					 <td style='font-size: 7px' colspan='5'></td>
					 <td style='font-size: 7px'>".number_format($totalSubsidio,"2",",",".")."</td>
					 <td style='font-size: 7px' colspan='5'></td>
					 <td style='font-size: 7px'>".number_format($totalSolicitado,"2",",",".")."</td>
					 <td style='font-size: 7px' colspan='7'></td>
					 <td style='font-size: 7px'>".number_format($totalPagoS,"2",",",".")."</td>
					 <td style='font-size: 7px'>".number_format($totalPagoO,"2",",",".")."</td>
					 <td style='font-size: 7px' colspan='3'></td>
				</tr>";
$lineas[$indexLinea] = $lineaTotales;

$file= "INFORME DE APLICACIÓN DE FONDOS Y AUDITORIA-PRESENTACION ".$idPresentacion.".xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
?>


<body>
	<div align="center">	 	
		 	<table border="1">
				 <thead>
				 	<tr>
				 		<th style="font-size: 7px" colspan="6">LIQ SSSALUD</th>
				 		<th style="font-size: 7px" colspan="6">INF. PRESENTADA POR LA OS EN CADA PERIODO</th>
				 		<th style="font-size: 7px" colspan="12">INFORMACION ADICIONAL QUE DEBE COMPLETAR LA OBRA SOCIAL</th>
				 	</tr>
				 	<tr>
				 		<th style="font-size: 7px">MES<br> PROCESO</th>
				 		<th style="font-size: 7px">MES<br> PRESTAC</th>
				 		<th style="font-size: 7px">CUIL<br> BENEFIC</th>
				 		<th style="font-size: 7px">COD<br> PREST</th>
				 		<th style="font-size: 7px; width: 120px">PRESTACION</th>
				 		<th style="font-size: 7px">IMPORTE<br> LIQUIDADO</th>
				 		
				 		<th style="font-size: 7px">PERIODO<br> FC</th>
				 		<th style="font-size: 7px">CUIT</th>
				 		<th style="font-size: 7px">LETRA</th>
				 		<th style="font-size: 7px">PV</th>
				 		<th style="font-size: 7px">Nº FC</th>
				 		<th style="font-size: 7px">IMPORTE<br> SOLICITADO</th>
				 		
				 		<th style="font-size: 7px">ORDEN DE<br> PAGO</th>
				 		<th style="font-size: 7px">FECHA DE<br> TRANSF.</th>
				 		<th style="font-size: 7px">CBU</th>
				 		<th style="font-size: 7px">IMPORTE<br> TRANSF.</th>
				 		<th style="font-size: 7px">RETENCION<br> GCIAS.</th>
				 		<th style="font-size: 7px">RETENCION<br> INGRESOS<br> BRUTOS</th>
				 		<th style="font-size: 7px">OTRAS<br> RETENC</th>
				 		<th style="font-size: 7px">IMPORTE<br> APOLICADO<br> SSS</th>
				 		<th style="font-size: 7px">IMPORTE<br> FONDOS<br> PROPIOS</th>
				 		<th style="font-size: 7px">NRO.<br> RECIBO</th>
				 		<th style="font-size: 7px">ASI</th>
				 		<th style="font-size: 7px">FOL</th>
				 	</tr>
				 </thead>
				 <tbody>
		 			<?php foreach($lineas as $linea) {
		 				echo $linea;
		 			}?>
		 		 </tbody>
		 	</table>
	</div>
</body>
</html>