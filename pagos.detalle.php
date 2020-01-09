<?php
include_once 'include/conector.php';
$usuario = $_SESSION['usuario'];
$idPresentacion = $_GET['id'];

$sqlPagos = "SELECT c.idpresentacion, c.nroordenpago, nrotransferencia, nrocominterno, impretencion,
DATE_FORMAT(c.fechatransferencia,'%d/%m/%Y') as fechatransferencia, recibo, asiento, folio
FROM intepagoscabecera c, intepagosdetalle d
WHERE c.idpresentacion = $idPresentacion and c.idpresentacion = d.idpresentacion and c.nroordenpago = d.nroordenpago";
$resPagos = mysql_query($sqlPagos);
$arrayPagos = array();
while ($rowPagos = mysql_fetch_assoc($resPagos)) {
	if (isset($arrayPagos[$rowPagos['nrocominterno']])) {
		$arrayPagos[$rowPagos['nrocominterno']]['nroordenpago'] .= "<br>".$rowPagos['nroordenpago'];
		$arrayPagos[$rowPagos['nrocominterno']]['fechatransferencia'] .= "<br>".$rowPagos['fechatransferencia'];
		$arrayPagos[$rowPagos['nrocominterno']]['nrotransferencia'] .= "<br>".$rowPagos['nrotransferencia'];
		$arrayPagos[$rowPagos['nrocominterno']]['recibo'] .= "<br>".$rowPagos['recibo'];
		$arrayPagos[$rowPagos['nrocominterno']]['asiento'] .= "<br>".$rowPagos['asiento'];
		$arrayPagos[$rowPagos['nrocominterno']]['folio'] .= "<br>".$rowPagos['folio'];
		$arrayPagos[$rowPagos['nrocominterno']]['impretencion'] += $rowPagos['impretencion'];
	} else {
		$arrayPagos[$rowPagos['nrocominterno']] = $rowPagos;
	}
}

$sqlFactura = "SELECT * FROM intepresentaciondetalle
WHERE idpresentacion = $idPresentacion and deverrorintegral is null and codpractica not in (97,98,99)
ORDER BY cuit, periodo, codpractica";
$resFactura = mysql_query($sqlFactura);
$arrayFacturas = array();
while ($rowFactura = mysql_fetch_assoc($resFactura)) {
	$index = $rowFactura['cuit'].$rowFactura['nrocominterno'].$rowFactura['tipoarchivo'];
	if ($rowFactura['tipoarchivo'] == 'DB') {
		$rowFactura['impdebito'] = (-1)*$rowFactura['impdebito'];
		$rowFactura['impnointe'] = (-1)*$rowFactura['impnointe'];
		$rowFactura['impcomprobanteintegral'] = (-1)*$rowFactura['impcomprobanteintegral'];
	}
	$monOS = $rowFactura['impcomprobanteintegral'] - $rowFactura['impmontosubsidio'] - $rowFactura['impdebito'] - $rowFactura['impnointe'];
	$rowFactura['impobrasocial'] = $monOS;
	$arrayFacturas[$index] = $rowFactura;
}

$sqlTotalesCuit = "SELECT inteinterbanking.*
	FROM inteinterbanking 
	WHERE idpresentacion = $idPresentacion";
$resTotalesCuit = mysql_query($sqlTotalesCuit);

while ($rowTotalesCuit = mysql_fetch_assoc($resTotalesCuit)) {
	$indexT = $rowTotalesCuit['cuit']."TOTAL";
	$arrayFacturas[$indexT] = $rowTotalesCuit;
}

ksort($arrayFacturas);

$arrayReversiones = array();
$sqlReversionesFuturas = "SELECT d.nrocominterno, d.idpresentacion
							FROM intepresentaciondetalle d, intepresentacion p 
							WHERE d.idpresentacion > $idPresentacion AND d.tipoarchivo = 'DB' AND 
								  d.idpresentacion = p.id AND p.fechacancelacion is null";
$resReversionesFuturas = mysql_query($sqlReversionesFuturas);
$canReversionesFuturas = mysql_num_rows($resReversionesFuturas);
if ($canReversionesFuturas > 0) {
	while ($rowReversionesFuturas = mysql_fetch_assoc($resReversionesFuturas)) {
		$arrayReversiones[$rowReversionesFuturas['nrocominterno']] = $rowReversionesFuturas['idpresentacion'];
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Informe de Pagos :.</title>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["filter"],
			headers:{0:{filter:false},
				 1:{sorter:false},
			 	 2:{sorter:false, filter:false},
			 	 3:{sorter:false, filter:false},
			 	 5:{sorter:false, filter:false},
			 	 6:{sorter:false, filter:false},
			 	 7:{sorter:false, filter:false},
			 	 8:{sorter:false, filter:false},
			 	 9:{sorter:false, filter:false},
			 	 10:{sorter:false, filter:false},
			 	 11:{sorter:false, filter:false},
			 	 12:{sorter:false, filter:false},
			 	 13:{sorter:false, filter:false},
			 	 14:{sorter:false, filter:false},
			 	 16:{sorter:false, filter:false},
			 	 17:{sorter:false, filter:false}},
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
<title>.: Retenciones :.</title>
<style type="text/css" media="print">
.nover {display:none}
</style>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'pagos.php'" /></p>
		<?php include_once("include/detalle.php")?>
		<form action="presentacion.retenciones.guardar.php" onSubmit="return validar(this)"  method="post">
			<h2>Informe De Pagos</h2>
			<input style="display: none" type="text" value="<?php echo $idPresentacion?>" id="idpresentacion" name="idpresentacion"/>
			<table id="listaResultado" class="tablesorter" style="text-align: center; font-size: 15px" >
				<thead>
				 	<tr>
				 		<th>Nº Comp.</th>
				 		<th>Tipo</th>
				 		<th>C.U.I.L.</th>
				 		<th>Periodo</th>
				 		<th>C.U.I.T.</th>
				 		<th>Fecha</th>
				 		<th>Nº</th>
				 		<th>$ Comp.</th>
				 		<th>$ Deb.</th>
				 		<th>$ Soli.</th>
				 		<th>$ No Int</th>
				 		<th>$ O.S.</th>
				 		<th>$ S.S.S</th>
				 		<th>$ Ret.</th>
				 		<th>$ A Pagar</th>
				 		<th>Ord. Pago</th>
				 		<th>Fecha</th>
				 		<th>Nº Trans</th>
				 		<th>Recibo</th>
				 		<th>Asiento</th>
				 		<th>Folio</th>
				 	</tr>
				 </thead>
				 <tbody>
				 <?php
				 	$totComSub = 0;
				 	$totSolSub = 0;
				 	$totMonDeb = 0;
				 	$totMonNOI = 0;
				 	$totMonSub = 0;
				 	$totMonOS = 0;
				 	$totApagar = 0;	
				 	$totRete = 0;
				 	foreach ($arrayFacturas as $key => $rowFactura) {  
				 		$pos = strpos($key, "TOTAL");
				 		if ($pos === false) {
					 		$totComSub += $rowFactura['impcomprobanteintegral'];
					 		$totSolSub += $rowFactura['impsolicitadosubsidio'];
					 		$totMonDeb += $rowFactura['impdebito'];
					 		$totMonNOI += $rowFactura['impnointe'];
					 		$totMonOS += $rowFactura['impobrasocial'];
					 		$totMonSub += $rowFactura['impmontosubsidio']; ?>
						 	<tr>
								<td><?php echo $rowFactura['nrocominterno'] ?></td>
								<td><?php echo $rowFactura['tipoarchivo'] ?></td>
								<td><?php echo $rowFactura['cuil'] ?></td>
								<td><?php echo $rowFactura['periodo'] ?></td>
								<td><?php echo $rowFactura['cuit'] ?></td>
								<td><?php echo $rowFactura['fechacomprobante'] ?></td>
								<td><?php echo $rowFactura['nrocomprobante'] ?></td>
								<td><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></td>
								<td><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></td>
								<td><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".") ?></td>					
								<td><?php echo number_format($rowFactura['impnointe'],2,",",".") ?></td>
								<td><?php echo number_format($rowFactura['impobrasocial'],2,",",".") ?></td>
								<td><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></td>
								<td>
								<?php if (isset($arrayPagos[$rowFactura['nrocominterno']])) { 
										echo number_format($arrayPagos[$rowFactura['nrocominterno']]['impretencion'],2,",","."); 
									  } else { 
										echo "0,00"; 
									  } ?>
								</td>
								<td></td>
							<?php if (isset($arrayPagos[$rowFactura['nrocominterno']]) && $rowFactura['tipoarchivo'] != "DB") { ?>
									<td><?php echo $arrayPagos[$rowFactura['nrocominterno']]['nroordenpago'] ?></td>
									<td><?php echo $arrayPagos[$rowFactura['nrocominterno']]['fechatransferencia'] ?></td>
									<td><?php echo $arrayPagos[$rowFactura['nrocominterno']]['nrotransferencia'] ?></td>
									<td><?php echo $arrayPagos[$rowFactura['nrocominterno']]['recibo'] ?></td>
									<td><?php echo $arrayPagos[$rowFactura['nrocominterno']]['asiento'] ?></td>
									<td><?php echo $arrayPagos[$rowFactura['nrocominterno']]['folio'] ?></td>
							<?php } else { 
										if (isset($arrayReversiones[$rowFactura['nrocominterno']]) && $rowFactura['tipoarchivo'] != "DB") { ?>
											<td>R <?php echo $arrayReversiones[$rowFactura['nrocominterno']] ?></td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
								  <?php } else {  ?>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
											<td>-</td>
									<?php }
									} ?>
							</tr>
					<?php } else {
							$cuit = substr($key,0,11);  
							$totRete += $rowFactura['impretencion']; 
							$totApagar += $rowFactura['impapagar'];?>
					 		<tr>
					 			<td style="background-color: #99bfe6"></td>
					 			<td style="background-color: #99bfe6"></td>
					 			<td style="background-color: #99bfe6"></td>
					 			<td style="background-color: #99bfe6"></td>
					 			<td style="background-color: #99bfe6"><b><?php echo $cuit ?></b></td>
					 			<td style="background-color: #99bfe6"></td>
					 			<td style="background-color: #99bfe6"></td>
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></b></td>	
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></b></td>	
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".") ?></b></td>	
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impnointe'],2,",",".") ?></b></td>
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impobrasocial'],2,",",".") ?></b></td>
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></b></td>
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impretencion'],2,",",".") ?></b></td>
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impapagar'],2,",",".") ?></b></td>
					 			<td style="background-color: #99bfe6" colspan="6"></td>
					 		</tr>	
				 <?php	}
				 	} ?>
				 	</tbody>
					<tr>
						<th colspan="7" rowspan="3">TOTALES</td>
						<th rowspan="2"><?php echo number_format($totComSub,2,",",".") ?></td>
						<th rowspan="2"><?php echo number_format($totMonDeb,2,",",".") ?></td>
						<th rowspan="2"><?php echo number_format($totSolSub,2,",",".") ?></td>
						<th><?php echo number_format($totMonNOI,2,",",".") ?></td>
						<th><?php echo number_format($totMonOS,2,",",".") ?></td>
						<th rowspan="2"><?php echo number_format($totMonSub,2,",",".") ?></td>
						<th rowspan="2"><?php echo number_format($totRete,2,",",".") ?></th>
						<th rowspan="2"><?php echo number_format($totApagar,2,",",".") ?></th>
						<th colspan="6" rowspan="3"></h>
					</tr>
					<tr>					
						<th colspan="2"><?php echo number_format($totMonNOI + $totMonOS,2,",",".") ?></td>
					</tr>
					<tr>
						<th>COM<br><?php echo number_format($totComSub,2,",",".") ?></td>
						<th colspan="3">DEB+SOL+NOI<br><?php echo number_format($totMonDeb + $totSolSub + $totMonNOI,2,",",".") ?></td>
						<th colspan="2">NOI+OS+SUB+DEB<br><?php echo number_format($totMonNOI + $totMonOS + $totMonSub + $totMonDeb,2,",",".") ?></th>
						<th colspan="2">RET+DEB+PAG<br><?php echo number_format($totRete + $totMonDeb + $totApagar,2,",",".") ?></th>
					</tr>	
			</table>
		</form>
	</div>
</body>
</html>