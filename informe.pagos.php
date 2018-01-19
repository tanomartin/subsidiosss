<?php
include_once 'include/conector.php';
set_time_limit(0);
$idPresentacion = $_GET['id'];
$sqlSubsidio = "SELECT s.*, SUBSTRING(n.descripcion,1,30) as descripcion FROM intesubsidio s, practicas n
				WHERE idpresentacion = $idPresentacion and 
					  s.codigopractica not in (97,98,99) and 
					  n.nomenclador = 7 and 
					  s.codigopractica = n.codigopractica";
$resSubsidio = mysql_query($sqlSubsidio);
$arrayCompleto = array();
$index = 0;
while ($rowSubsidio = mysql_fetch_array($resSubsidio)) {
	$arrayCompleto[$index] = $rowSubsidio;
	$sqlSelectFactura = "SELECT f.*, tipocomprobante.descripcion as comprobante, prestadoresauxiliar.cbu
							FROM intepresentaciondetalle f
							LEFT JOIN prestadores ON f.cuit = prestadores.cuit
							LEFT JOIN prestadoresauxiliar ON prestadores.codigoprestador = prestadoresauxiliar.codigoprestador
							LEFT JOIN tipocomprobante ON f.tipocomprobante = tipocomprobante.id
							WHERE
								f.idpresentacion = $idPresentacion and f.deverrorformato is null and 
								f.deverrorintegral is null and f.cuil = '".$rowSubsidio['cuil']."' and 
								f.periodo = '".$rowSubsidio['periodoprestacion']."' and 
								f.tipoarchivo != 'DB' and f.codpractica not in (97,98,99) and
								f.codpractica = ".(int) $rowSubsidio['codigopractica'];
	$resSelectFactura = mysql_query($sqlSelectFactura);
	while($rowfactura = mysql_fetch_array($resSelectFactura)) {
		$arrayCompleto[$index]['f'][$rowfactura['nrocominterno']] = $rowfactura;
		$sqlPagos = "SELECT p.*, DATE_FORMAT(p.fechatransferencia, '%d-%m-%Y') as fechatransferencia FROM intepagos p WHERE idpresentacion = $idPresentacion and nrocominterno = ".$rowfactura['nrocominterno'];
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
	$linea .= "<td>".$subsidio['periodopresentacion']."</td>";
	$linea .= "<td>".$subsidio['periodoprestacion']."</td>";
	$linea .= "<td>".$subsidio['cuil']."</td>";
	$linea .= "<td>".$subsidio['codigopractica']."</td>";
	$linea .= "<td>".$subsidio['descripcion']."</td>";
	$linea .= "<td>".number_format($subsidio['impsubsidiado'],"2",",",".")."</td>";
	$contadorFacturas = 0;
	foreach ($subsidio['f'] as $nrointerno => $factura) {
		$totalSolicitado += (float) $factura['impsolicitado'];
		if ($contadorFacturas != 0) {
			$linea .= "<tr>";
			$linea .= "<td></td><td></td><td></td><td></td><td></td><td></td>";
			$linea .= "<td>".$factura['periodo']."</td>";
			$linea .= "<td>".$factura['cuit']."</td>";
			$linea .= "<td>".$factura['comprobante']."</td>";
			$linea .= "<td>".$factura['puntoventa']."</td>";
			$linea .= "<td>".$factura['nrocomprobante']."</td>";
			$linea .= "<td>".number_format($factura['impsolicitado'],"2",",",".")."</td>";
		} else {
			$linea .= "<td>".$factura['periodo']."</td>";
			$linea .= "<td>".$factura['cuit']."</td>";
			$linea .= "<td>".$factura['comprobante']."</td>";
			$linea .= "<td>".$factura['puntoventa']."</td>";
			$linea .= "<td>".$factura['nrocomprobante']."</td>";
			$linea .= "<td>".number_format($factura['impsolicitado'],"2",",",".")."</td>";
		}
		$contadorFacturas++;
		
		$contadorPagos = 0;
		if (isset($subsidio['f'][$nrointerno]['p'])) {
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
					$linea .= "<td></td><td></td><td></td><td></td><td></td><td></td>";
					$linea .= "<td></td><td>".$factura['cuit']."</td><td></td><td></td><td></td><td></td>";
					$linea .= "<td>".$pago['nroordenpago']."</td>";
					$linea .= "<td>".$pago['fechatransferencia']."</td>";
					$linea .= "<td>'".$factura['cbu']."'</td>";
					$linea .= "<td>".number_format($pago['importepagado'],"2",",",".")."</td>";
					$linea .= "<td>".number_format($pago['retganancias'],"2",",",".")."</td>";
					$linea .= "<td>".number_format($pago['retingresosbrutos'],"2",",",".")."</td>";
					$linea .= "<td>".number_format("0","2",",",".")."</td>";
					$linea .= "<td>".number_format($imporPagoS,"2",",",".")."</td>";
					$linea .= "<td>".number_format($imporPagoO,"2",",",".")."</td>";
					$linea .= "<td>".$pago['recibo']."</td>";
					$linea .= "<td>".$pago['asiento']."</td>";
					$linea .= "<td>".$pago['folio']."</td>";		
					$linea .= "</tr>";
				} else {
					$linea .= "<td>".$pago['nroordenpago']."</td>";
					$linea .= "<td>".$pago['fechatransferencia']."</td>";
					$linea .= "<td>'".$factura['cbu']."'</td>";
					$linea .= "<td>".number_format($pago['importepagado'],'2',',','.')."</td>";
					$linea .= "<td>".number_format($pago['retganancias'],"2",",",".")."</td>";
					$linea .= "<td>".number_format($pago['retingresosbrutos'],"2",",",".")."</td>";
					$linea .= "<td>".number_format("0","2",",",".")."</td>";
					$linea .= "<td>".number_format($imporPagoS,"2",",",".")."</td>";
					$linea .= "<td>".number_format($imporPagoO,"2",",",".")."</td>";
					$linea .= "<td>".$pago['recibo']."</td>";
					$linea .= "<td>".$pago['asiento']."</td>";
					$linea .= "<td>".$pago['folio']."</td>";
				}
				$contadorPagos++;
			}
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
					 <td colspan='5'></td>
					 <td>".number_format($totalSolicitado,"2",",",".")."</td>
					 <td colspan='7'></td>
					 <td>".number_format($totalPagoS,"2",",",".")."</td>
					 <td>".number_format($totalPagoO,"2",",",".")."</td>
					 <td colspan='3'></td>
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
				 		<th style="colspan="6">LIQ SSSALUD</th>
				 		<th style="colspan="6">INF. PRESENTADA POR LA OS EN CADA PERIODO</th>
				 		<th style="colspan="12">INFORMACION ADICIONAL QUE DEBE COMPLETAR LA OBRA SOCIAL</th>
				 	</tr>
				 	<tr>
				 		<th>MES<br> PROCESO</th>
				 		<th>MES<br> PRESTAC</th>
				 		<th>CUIL<br> BENEFIC</th>
				 		<th>COD<br> PREST</th>
				 		<th style="font-size: 7px; width: 120px">PRESTACION</th>
				 		<th>IMPORTE<br> LIQUIDADO</th>
				 		
				 		<th>PERIODO<br> FC</th>
				 		<th>CUIT</th>
				 		<th>LETRA</th>
				 		<th>PV</th>
				 		<th>Nº FC</th>
				 		<th>IMPORTE<br> SOLICITADO</th>
				 		
				 		<th>ORDEN DE<br> PAGO</th>
				 		<th>FECHA DE<br> TRANSF.</th>
				 		<th>CBU</th>
				 		<th>IMPORTE<br> TRANSF.</th>
				 		<th>RETENCION<br> GCIAS.</th>
				 		<th>RETENCION<br> INGRESOS<br> BRUTOS</th>
				 		<th>OTRAS<br> RETENC</th>
				 		<th>IMPORTE<br> APOLICADO<br> SSS</th>
				 		<th>IMPORTE<br> FONDOS<br> PROPIOS</th>
				 		<th>NRO.<br> RECIBO</th>
				 		<th>ASI</th>
				 		<th>FOL</th>
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