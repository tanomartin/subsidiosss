<?php
include_once 'include/conector.php';
set_time_limit(0);
$idPresentacion = $_GET['id'];
$sqlSubsidio = "SELECT s.*, SUBSTRING(n.descripcion,1,30) as descripcion FROM intesubsidio s, practicas n
				WHERE idpresentacion = $idPresentacion and 
					  s.codpractica not in (97,98,99) and 
					  n.nomenclador = 7 and 
					  s.codpractica = n.codigopractica";
$resSubsidio = mysql_query($sqlSubsidio);
$canSubsidio = mysql_num_rows($resSubsidio);
if ($canSubsidio == 0) {
	$sqlSubsidio = "SELECT s.*, SUBSTRING(n.descripcion,1,30) as descripcion FROM interendicion s, practicas n
						WHERE idpresentacion = $idPresentacion and
							  s.codpractica not in (97,98,99) and
							  n.nomenclador = 7 and
						      s.codpractica = n.codigopractica";
	$resSubsidio = mysql_query($sqlSubsidio);
}
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
								f.tipoarchivo != 'DB' and
								f.codpractica = ".(int) $rowSubsidio['codpractica'];
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

$totTransferido = 0;
$totRetGanancia = 0;
$totRetIngBrutos = 0;
$totOtrasRete = 0;


foreach ($arrayCompleto as $key => $subsidio) {
	$totalSubsidio += (float) $subsidio['impsubsidiado'];
		foreach ($subsidio['f'] as $nrointerno => $factura) {
			$totalSolicitado += (float) $factura['impsolicitado'];
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
				
				$linea = "<tr>";
				$linea .= "<td style='font-size: 11px'>".$subsidio['periodopresentacion']."</td>";
				$linea .= "<td style='font-size: 11px'>".$subsidio['periodoprestacion']."</td>";
				$linea .= "<td style='font-size: 11px'>".$subsidio['cuil']."</td>";
				$linea .= "<td style='font-size: 11px'>".$subsidio['codpractica']."</td>";
				$linea .= "<td style='font-size: 11px'>".$subsidio['descripcion']."</td>";
				$linea .= "<td style='font-size: 11px'>".number_format($subsidio['impsubsidiado'],"2",",",".")."</td>";
					
				$linea .= "<td style='font-size: 11px'>".$factura['nrocominterno']."</td>";
				$linea .= "<td style='font-size: 11px'>".$factura['periodo']."</td>";
				$linea .= "<td style='font-size: 11px'>".$factura['cuit']."</td>";
				$linea .= "<td style='font-size: 11px'>".$factura['comprobante']."</td>";
				$linea .= "<td style='font-size: 11px'>".$factura['puntoventa']."</td>";
				$linea .= "<td style='font-size: 11px'>".$factura['nrocomprobante']."</td>";
				$linea .= "<td style='font-size: 11px'>".number_format($factura['impsolicitado'],"2",",",".")."</td>";
					
				$totTransferido += (float) $pago['importepagado'];
				$totRetGanancia += (float) $pago['retganancias'];
				$totRetIngBrutos += (float) $pago['retingresosbrutos'];
				
				$linea .= "<td style='font-size: 11px'>".$pago['nroordenpago']."</td>";
				$linea .= "<td style='font-size: 11px'>".$pago['fechatransferencia']."</td>";
				$linea .= "<td style='font-size: 11px'>".$factura['cbu']."</td>";
				$linea .= "<td style='font-size: 11px'>".number_format($pago['importepagado'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 11px'>".number_format($pago['retganancias'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 11px'>".number_format($pago['retingresosbrutos'],"2",",",".")."</td>";
				$linea .= "<td style='font-size: 11px'>".number_format("0","2",",",".")."</td>";
				$linea .= "<td style='font-size: 11px'>".number_format($imporPagoS,"2",",",".")."</td>";
				$linea .= "<td style='font-size: 11px'>".number_format($imporPagoO,"2",",",".")."</td>";
				
				if ($pago['recibo'] == null) {
					$linea .= "<td style='font-size: 11px'>-</td>";
				} else {
					$linea .= "<td style='font-size: 11px'>".$pago['recibo']."</td>";
				}
				if ($pago['asiento'] == null) {
					$linea .= "<td style='font-size: 11px'>-</td>";
				} else {
					$linea .= "<td style='font-size: 11px'>".$pago['asiento']."</td>";
				}
				
				if ($pago['folio'] == null) {
					$linea .= "<td style='font-size: 11px'>-</td>";
				} else {
					$linea .= "<td style='font-size: 11px'>".$pago['folio']."</td>";
				}
				
				$linea .= "<td style='font-size: 11px'><input type='button' value='Cargar' onclick='location=\"presentacion.pagos.carga.php?idpresentacion=$idPresentacion&nrocomint=".$factura['nrocominterno']."&norord=".$pago['nroordenpago']."\"'/></td>";
				$linea .= "</tr>";
					
				$lineas[$indexLinea] = $linea;
				$indexLinea++;
			}
		}	
}

$lineaTotales = "<tr>
					 <td colspan='5'></td>
					 <td style='font-size: 11px'>".number_format($totalSubsidio,"2",",",".")."</td>
					 <td colspan='6'></td>
					 <td style='font-size: 11px'>".number_format($totalSolicitado,"2",",",".")."</td>
					 <td colspan='3'></td>
					 <td style='font-size: 11px'>".number_format($totTransferido,"2",",",".")."</td>
					 <td style='font-size: 11px'>".number_format($totRetGanancia,"2",",",".")."</td>
					 <td style='font-size: 11px'>".number_format($totRetIngBrutos,"2",",",".")."</td>
					 <td style='font-size: 11px'>".number_format($totOtrasRete,"2",",",".")."</td>
					 <td style='font-size: 11px'>".number_format($totalPagoS,"2",",",".")."</td>
					 <td style='font-size: 11px'>".number_format($totalPagoO,"2",",",".")."</td>
					 <td colspan='4'></td>
				</tr>";
$lineas[$indexLinea] = $lineaTotales;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 

<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["zebra", "filter"],
			headers:{0:{sorter:false, filter:false},
				1:{sorter:false, filter:false},
				2:{sorter:false, filter:false},
				3:{sorter:false, filter:false},
				4:{sorter:false, filter:false},
				5:{sorter:false, filter:false},
				6:{sorter:false, filter:false},
				7:{sorter:false, filter:false},
				8:{sorter:false},
				9:{sorter:false, filter:false},
				10:{sorter:false, filter:false},
				12:{sorter:false, filter:false},
				13:{sorter:false, filter:false},
				14:{filter:false},
				15:{sorter:false, filter:false},
				16:{sorter:false, filter:false},
				17:{sorter:false, filter:false},
				18:{sorter:false, filter:false},
				19:{sorter:false, filter:false},
				20:{sorter:false, filter:false},
				21:{sorter:false, filter:false},
				22:{sorter:false/*, filter:false*/},
				23:{sorter:false/*, filter:false*/},
				24:{sorter:false/*, filter:false*/},
				25:{sorter:false, filter:false},
				26:{sorter:false},
				27:{sorter:false},
				28:{sorter:false}},
			widgetOptions : { 
				filter_cssFilter   : '',
				filter_childRows   : false,
				filter_hideFilters : false,
				filter_ignoreCase  : true,
				filter_searchDelay : 300,
				filter_startsWith  : false,
				filter_hideFilters : false,
			}
		});
});


</script>

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
	 	
	 	<!-- <p>
	 		<input type="button" value="Generar Informe" onclick="location='presentacion.pagosExcel.php?id=<?php echo $idPresentacion?>'"/>
	 		<input type="button" value="Generar Sindico" onclick="location='presentacion.sindicosExcel.php?id=<?php echo $idPresentacion?>'"/>
	 	</p>  -->
	 	
		 	<table id="listaResultado" class="tablesorter" style="text-align: center;">
				 <thead>
				 	<tr>
				 		<th  style="font-size: 11px" colspan="6">LIQ SSSALUD</th>
				 		<th  style="font-size: 11px" colspan="7">INF. PRESENTADA POR LA OS EN CADA PERIODO</th>
				 		<th  style="font-size: 11px" colspan="13">INFORMACION ADICIONAL QUE DEBE COMPLETAR LA OBRA SOCIAL</th>
				 	</tr>
				 	<tr>
				 		<th style="font-size: 11px">MES PROCESO</th>
				 		<th style="font-size: 11px">MES PRESTAC</th>
				 		<th style="font-size: 11px">CUIL BENEFIC</th>
				 		<th style="font-size: 11px">COD PREST</th>
				 		<th style="font-size: 11px">PRESTACION</th>
				 		<th style="font-size: 11px">IMPORTE LIQUIDADO</th>
				 		
				 		<th style="font-size: 11px">NRO INTERNO.</th>
				 		<th style="font-size: 11px">PERIODO FC</th>
				 		<th style="font-size: 11px">CUIT</th>
				 		<th style="font-size: 11px">LETRA</th>
				 		<th style="font-size: 11px">PV</th>
				 		<th style="font-size: 11px">Nº FC</th>
				 		<th style="font-size: 11px">IMPORTE SOLICITADO</th>
				 		
				 		<th style="font-size: 11px">ORDEN DE PAGO</th>
				 		<th style="font-size: 11px">FECHA DE TRANSF.</th>
				 		<th style="font-size: 11px">CBU</th>
				 		<th style="font-size: 11px">IMPORTE TRANSF.</th>
				 		<th style="font-size: 11px">RETENCION GCIAS.</th>
				 		<th style="font-size: 11px">RETENCION INGRESOS BRUTOS</th>
				 		<th style="font-size: 11px">OTRAS RETENC</th>
				 		<th style="font-size: 11px">IMPORTE APOLICADO SSS</th>
				 		<th style="font-size: 11px">IMPORTE FONDOS PROPIOS</th>
				 		<th style="font-size: 11px">NRO. RECIBO</th>
				 		<th style="font-size: 11px">ASI</th>
				 		<th style="font-size: 11px">FOL</th>
				 		<th style="font-size: 11px"></th>
				 	</tr>
				 </thead>
				 <tbody>
		 			<?php foreach($lineas as $linea) {
		 				echo $linea;
		 			}?>
		 		 </tbody>
		 	</table>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>