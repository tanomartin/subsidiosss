<?php
include_once 'include/conector.php';
set_time_limit(0);
$idPresentacion = $_GET['id'];
$sqlSubsidio = "SELECT s.*, n.descripcion, ps.idsss FROM subsidio s, nomenclador n, presentacionsubsidio ps WHERE s.idpresentacion = $idPresentacion and s.idpresentacion = ps.id and s.codigopractica = n.codigo";
$resSubsidio = mysql_query($sqlSubsidio);
$arrayCompleto = array();
$index = 0;
while ($rowSubsidio = mysql_fetch_array($resSubsidio)) {
	$arrayCompleto[$index] = $rowSubsidio;
	$sqlSelectFactura = "SELECT f.*, DATE_FORMAT(f.fechacomprobante, '%d/%m/%Y') as fechacomprobante, comprobante.descripcion as comprobante, prestadores.cbu  
							FROM facturas f
							LEFT JOIN prestadores ON f.cuit = prestadores.cuit
              				LEFT JOIN comprobante ON f.tipocomprobante = comprobante.codigo
							WHERE f.idpresentacion = $idPresentacion and f.deverrorformato is null and 
								  f.deverrorintegral is null and f.cuil = '".$rowSubsidio['cuil']."' and 
								  f.periodo = '".$rowSubsidio['periodoprestacion']."' and 
								  f.tipoarchivo != 'DB' and
								  f.codpractica = ".(int) $rowSubsidio['codigopractica'];
	$resSelectFactura = mysql_query($sqlSelectFactura);
	while($rowfactura = mysql_fetch_array($resSelectFactura)) {
		$arrayCompleto[$index]['f'][$rowfactura['nrocominterno']] = $rowfactura;
		$sqlPagos = "SELECT p.*, DATE_FORMAT(p.fechatransferencia, '%d/%m/%Y') as fechatransferencia FROM pagos p WHERE idpresentacion = $idPresentacion and nrocominterno = ".$rowfactura['nrocominterno'];
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
	$carpeta = $subsidio['periodopresentacion'];
	$idssss = $subsidio['idsss'];
	foreach ($subsidio['f'] as $nrointerno => $factura) {
		if (isset($subsidio['f'][$nrointerno]['p'])) {
			foreach ($subsidio['f'][$nrointerno]['p'] as $nropago => $pago) {
				$tipo = substr($pago['nrotransferencia'], 0, 2);
				if ($tipo == 'TS') {
					$imporPagoS = (float) $pago['importepagado'] + $pago['retganancias'];
					$imporPagoO = 0;
				} else {
					$imporPagoO = (float) $pago['importepagado'] + $pago['retganancias'];
					$imporPagoS = 0;
				}
				
				$linea = "<tr>";
					$linea .= "<td></td>";
					$linea .= "<td>".$subsidio['idsss']."</td>";
					$linea .= "<td>".$subsidio['nroliquidacion']."</td>";
					$linea .= "<td></td>";
					$linea .= "<td>".$factura['tipoarchivo']."</td>";
					$linea .= "<td>".$factura['codigoob']."</td>";
					$linea .= "<td>".$factura['cuil']."</td>";
					$linea .= "<td>".$factura['periodo']."</td>";
					$linea .= "<td>".$factura['cuit']."</td>";
					$linea .= "<td>".$factura['tipocomprobante']."</td>";
					$linea .= "<td>".$factura['tipoemision']."</td>";
					$linea .= "<td>".$factura['fechacomprobante']."</td>";
					$linea .= "<td>'".$factura['cae']."'</td>";
					$linea .= "<td>".$factura['puntoventa']."</td>";
					$linea .= "<td>".$factura['nrocomprobante']."</td>";
					$linea .= "<td>".number_format($factura['impcomprobante'],"2",",",".")."</td>";
					$linea .= "<td>".number_format($factura['impsolicitado'],"2",",",".")."</td>";
					$linea .= "<td>".$subsidio['periodopresentacion']."</td>";
					$linea .= "<td>".number_format($subsidio['impsubsidiado'],"2",",",".")."</td>";
					$linea .= "<td>".$pago['nroordenpago']."</td>";
					$linea .= "<td>".$pago['fechatransferencia']."</td>";
					$linea .= "<td>".number_format($pago['retganancias']+$pago['retingresosbrutos'],"2",",",".")."</td>";			
					$linea .= "<td>".number_format($imporPagoS,"2",",",".")."</td>";
					$linea .= "<td>".number_format($imporPagoS+$imporPagoO,"2",",",".")."</td>";
					$linea .= "<td></td>";
					$linea .= "<td></td>";
					$linea .= "<td></td>";
					$linea .= "<td>".$pago['recibo']."</td>";
					$linea .= "<td></td>";
					$linea .= "<td></td>";
				$linea .= "</tr>";
				$lineas[$indexLinea] = $linea;
				$indexLinea++;
			}
		}
	}
}

$file= "INFORME SINDICO ".$idPresentacion." - Carpeta ".$carpeta." - Id SSS - ".$idssss.".xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
?>

<body>
	<div align="center">	 	
		 	<table border="1">
				 <thead>
				 	<tr>
				 		<td colspan="19">RNOS 1-1100-1 PERSONAL INDUSTRIA MADERERA</td>
				 		<th colspan="2">DATOS DEL PAGO</th>
				 		<th colspan="3">IMPORTES</th>
				 		<th colspan="3">INFORMACION BANCO</th>
				 		<th colspan="2">INFORMACION PRESTADOR</th>
				 		<th></th>
				 	</tr>
				 	<tr>
				 		<th>ORDEN</th>
				 		<th>LIQ</th>
				 		<th>NUMERO<br>LIQUIDACION</th>
				 		<th>IMPORTE<br>CALCULADO</th>
				 		<th>TIPO<br>ARCHIVO</th>
				 		<th>RNOS</th>
				 		<th>CUIL</th>
				 		<th>PERDIODO<br>PRESTACION</th>
				 		<th>CUIT PRESTADOR</th>
				 		<th>TIPO<br>COMPROBANTE</th>
				 		<th>TIPO<br>EMISION</th>
				 		<th>FECHA<br>EMISION<br>COMPROBANTE</th>
				 		<th>CAE CAI</th>
				 		<th>PUNTO<br>VENTA</th>
				 		<th>NUMERO<br>COMPROBANTE</th>
				 		<th>IMPORTE<br>COMPROBANTE</th>
				 		<th>IMPORTE<br>SOLICITADO</th>
				 		<th>PERDIODO<br>PRESENTACION</th>
				 		<th>IMPORTE<br>SUBSIDIAR</th>		 		
				 		<th>Orden de<br>Pago Nº</th>	
				 		<th>Fecha de<br>Pago según<br>O.P.</th>	
				 		<th>Imp. Ret.</th>
				 		<th>Importe <br> Abonado con <br> Integración</th>
				 		<th>IMPORTE <br> NETO <br> ABONADO</th>
				 		<th>Forma</th>
				 		<th>Comprobante <br> del Pago Nº</th>
				 		<th>Fecha de <br>Debito</th>
				 		<th>Recibo<br> Prestador Nº</th>
				 		<th>Fecha de<br> Recibo</th>
				 		<th>Observaciones</th>
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