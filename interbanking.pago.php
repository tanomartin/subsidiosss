<?php
include_once 'include/conector.php';

$sqlPagos = "SELECT inteinterbanking.*, prestadoresauxiliar.cbu,
			DATE_FORMAT(inteinterbanking.fechaenvio, '%d-%m-%Y') as fechaenvio,
			madera.prestadoresauxiliar.cbu
			FROM inteinterbanking
			LEFT JOIN madera.prestadores on inteinterbanking.cuit = madera.prestadores.cuit
			LEFT JOIN madera.prestadoresauxiliar on madera.prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
			WHERE inteinterbanking.fechaenvio is not null and inteinterbanking.idpago is null";
$resPagos = mysql_query($sqlPagos);
$numPagos = mysql_num_rows($resPagos);

$arrayFacturas = array();
if ($numPagos > 0) {
	while ($rowTotalesCuit = mysql_fetch_assoc($resPagos)) {
		$indexT = $rowTotalesCuit['cuit']."TOTAL";
		$arrayFacturas[$indexT] = $rowTotalesCuit;
		
		$sqlFactura = "SELECT * FROM intepresentaciondetalle
						WHERE idpresentacion = ".$rowTotalesCuit['idpresentacion']." and 
							  cuit = ".$rowTotalesCuit['cuit']." and
							  deverrorintegral is null and 
							  codpractica not in (97,98,99)
						ORDER BY cuit, periodo, codpractica";
		$resFactura = mysql_query($sqlFactura);
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
	}
}
ksort($arrayFacturas);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>
<script src="/madera/lib/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["filter"],
			headers:{0:{sorter:false, filter:false},
				 1:{sorter:false, filter:false},
			 	 2:{sorter:false, filter:false},
			 	 3:{sorter:false, filter:false},
			 	 4:{sorter:false, filter:false},
			 	 5:{sorter:false, filter:false},
			 	 6:{sorter:false, filter:false},
			 	 7:{sorter:false, filter:false},
			 	 8:{sorter:false, filter:false},
			 	 9:{sorter:false, filter:false},
			 	 10:{sorter:false, filter:false},
			 	 11:{sorter:false, filter:false},
			 	 12:{sorter:false, filter:false}},
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

function validar(fomulario) {
	fomulario.submit.disabled = true;
	$.blockUI({ message: "<h1>Generando Archivo de Importacion e Informe... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
	return true;
}

</script>
<title>.: Generacion Pagos :.</title>
<style type="text/css" media="print">
.nover {display:none}
</style>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'interbanking.php'" /></p>
		<h2>Generacion Archivo Interbanking</h2>
		<?php if ($numPagos > 0) { ?>
		<form action="interbanking.pago.guardar.php" onSubmit="return validar(this)"  method="post">	
			<table id="listaResultado" class="tablesorter" style="text-align: center; font-size: 15px" >
				<thead>
				 	<tr>
				 		<th>Nº Comp.</th>
				 		<th>Tipo</th>
				 		<th>Periodo</th>
				 		<th>C.U.I.T.</th>
				 		<th>C.B.U.</th>
				 		<th>Fecha</th>
				 		<th>Nº</th>
				 		<th>$ Comp.</th>
				 		<th>$ Deb.</th>
				 		<th>$ O.S.</th>
				 		<th>$ S.S.S</th>
				 		<th>$ Ret.</th>
				 		<th>$ Transf.</th>
				 	</tr>
				 </thead>
				 <tbody>
				 <?php
				 	$totComSub = 0;
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
					 		$totMonDeb += $rowFactura['impdebito'];
					 		$totMonNOI += $rowFactura['impnointe'];
					 		$totMonOS += $rowFactura['impobrasocial'];
					 		$totMonSub += $rowFactura['impmontosubsidio']; ?>
						 	<tr>
								<td><?php echo $rowFactura['nrocominterno'] ?></td>
								<td><?php echo $rowFactura['tipoarchivo'] ?></td>
								<td><?php echo $rowFactura['periodo'] ?></td>
								<td><?php echo $rowFactura['cuit'] ?></td>
								<td></td>
								<td><?php echo $rowFactura['fechacomprobante'] ?></td>
								<td><?php echo $rowFactura['nrocomprobante'] ?></td>
								<td><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></td>
								<td><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></td>
								<td><?php echo number_format($rowFactura['impnointe'] + $rowFactura['impobrasocial'] ,2,",",".") ?></td>
								<td><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></td>
								<td></td>
								<td></td>
							</tr>
					<?php } else {
							$cuit = substr($key,0,11);  
							$totRete += $rowFactura['impretencion']; 
							$totApagar += $rowFactura['impapagar'];?>
					 		<tr>
					 			<th colspan="3">SUB</th>
					 			<th>
					 				<?php echo $cuit ?></b>
					 				<input style="display: none" type="text" value="<?php echo $cuit?>" id="cuit<?php echo $cuit?>" name="cuit<?php echo $cuit?>"/>
			 						<input style="display: none" type="text" value="<?php echo $rowFactura['idpresentacion']?>" id="pres<?php echo $cuit?>" name="pres<?php echo $cuit?>"/>
					 			
					 			<th><?php echo $rowFactura['cbu'] ?></th>
					 			<th colspan="2"></th>
					 			<th><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></th>	
					 			<th><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></th>	
					 			<th><?php echo number_format($rowFactura['impobrasocial'] + $rowFactura['impnointe'], 2,",",".") ?></th>
					 			<th><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></th>
					 			<th><?php echo number_format($rowFactura['impretencion'],2,",",".") ?></th>
					 			<th><?php echo number_format($rowFactura['impapagar'],2,",",".") ?></th>
					 		</tr>	
				 <?php	}
				 	} ?>
				 	</tbody>
					<tr>
						<th colspan="7">
							TOTALES
							<input style="display: none" type="text" value="<?php echo $numPagos?>" id="totCantidad" name="totCantidad"/>
						</th>
						<th>
							<?php echo number_format($totComSub,2,",",".") ?>
							<input style="display: none" type="text" value="<?php echo number_format($totComSub,2,".","") ?>" id="totMonCom" name="totMonCom"/>
						</th>
						<th>
							<?php echo number_format($totMonDeb,2,",",".") ?>
							<input style="display: none" type="text" value="<?php echo number_format($totMonDeb,2,".","") ?>" id="totMonDeb" name="totMonDeb"/>
						</th>
						<th>
							<?php echo number_format($totMonOS + $totMonNOI,2,",",".") ?>
							<input style="display: none" type="text" value="<?php echo number_format($totMonOS,2,".","") ?>" id="totMonOS" name="totMonOS"/>
							<input style="display: none" type="text" value="<?php echo number_format($totMonNOI,2,".","") ?>" id="totMonNOI" name="totMonNOI"/>
						</th>
						<th>
							<?php echo number_format($totMonSub,2,",",".") ?>
							<input style="display: none" type="text" value="<?php echo number_format($totMonSub,2,".","") ?>" id="totMonSub" name="totMonSub"/>
						</th>
						<th>
							<?php echo number_format($totRete,2,",",".") ?>
							<input style="display: none" type="text" value="<?php echo number_format($totRete,2,".","") ?>" id="totRete" name="totRete"/>
						</th>
						<th>
							<?php echo number_format($totApagar,2,",",".") ?>
							<input style="display: none" type="text" value="<?php echo number_format($totApagar,2,".","") ?>" id="totApagar" name="totApagar"/>
						</th>
					</tr>
					<tr>
						<th colspan="7">CONTROLES</th>
						<th><?php echo number_format($totComSub,2,",",".") ?></th>
						<th colspan="3"><?php echo number_format($totMonDeb + $totMonNOI + $totMonOS + $totMonSub,2,",",".") ?></th>
						<th colspan="2"><?php echo number_format($totRete + $totApagar,2,",",".") ?></th>
					</tr>
				</table>
			<input type="submit" name="submit" value="Generar Archivo" /><br/>
		</form>
  <?php } else { ?>
			<h3 style="color: blue">No existen pagos para realizar en este momento</h3>
  <?php }?>
	</div>
</body>
</html>