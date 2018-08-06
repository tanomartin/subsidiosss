<?php
include_once 'include/conector.php';
$usuario = $_SESSION['usuario'];
$idPresentacion = $_GET['id'];

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

$sqlTotalesCuit = "SELECT inteinterbanking.*,
	CASE
		WHEN (madera.prestadores.situacionfiscal in (0,1,4) || (madera.prestadores.situacionfiscal = 3 and madera.prestadores.vtoexento >= CURDATE())) THEN 0
		WHEN (madera.prestadores.situacionfiscal = 2 || (madera.prestadores.situacionfiscal = 3 and madera.prestadores.vtoexento < CURDATE())) THEN 1
	END as retiene
	FROM inteinterbanking 
	LEFT JOIN madera.prestadores on inteinterbanking.cuit = madera.prestadores.cuit
	WHERE idpresentacion = $idPresentacion";
$resTotalesCuit = mysql_query($sqlTotalesCuit);

while ($rowTotalesCuit = mysql_fetch_assoc($resTotalesCuit)) {
	$indexT = $rowTotalesCuit['cuit']."TOTAL";
	$arrayFacturas[$indexT] = $rowTotalesCuit;
}

ksort($arrayFacturas);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Facturas Presentaciones S.S.S. :.</title>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>
<script src="include/jquery.blockUI.js" type="text/javascript"></script>
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
			 	 4:{sorter:false},
			 	 5:{sorter:false, filter:false},
			 	 6:{sorter:false, filter:false},
			 	 7:{sorter:false, filter:false},
			 	 8:{sorter:false, filter:false},
			 	 9:{sorter:false, filter:false},
			 	 10:{sorter:false, filter:false},
			 	 11:{sorter:false, filter:false},
			 	 12:{sorter:false, filter:false},
			 	 13:{sorter:false},
			 	 14:{sorter:false, filter:false},
			 	 15:{sorter:false, filter:false}},
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

function actualizarDatos(imputrete, retevieja, cuit, comp, debito, apagarviejo) {
	var nuevaRete = imputrete.value;
	var nombreApagar = "apagar"+cuit;
	var imputApagar = document.getElementById(nombreApagar);
	
	if (nuevaRete < 0 || !isNumber(nuevaRete) || nuevaRete == "") {
		alert("La retencion debe ser numerica y positiva");
		imputrete.value = retevieja.toFixed(2);
		imputApagar.value = apagarviejo.toFixed(2);
	} else {
		var nuevoApagar = parseFloat(comp - debito - nuevaRete).toFixed(2);
		imputApagar.value = nuevoApagar;
	}
}

function validar(fomulario) {
	$.blockUI({ message: "<h1>Actualizando Retenciones y Calculando Totales... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
	return true;
}

function cerrarPresentacion(idpresentacion) {
	$.blockUI({ message: "<h1>Cerrando Presentacino... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
	$pagina = "presentacion.retenciones.cerrar.php?idpresentacion="+idpresentacion;
	window.location.href = $pagina;
}

</script>
<title>.: Retenciones :.</title>
<style type="text/css" media="print">
.nover {display:none}
</style>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
		<?php include_once("include/detalle.php")?>
		<form action="presentacion.retenciones.guardar.php" onSubmit="return validar(this)"  method="post">
			<h2>Retenciones</h2>
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
				 		<th class="filter-select" data-placeholder="Selccione" >Ret.</th>
				 		<th>$ Ret.</th>
				 		<th>No Pagar</th>
				 		<th>$ A Pagar</th>
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
				 	$totNopagar = 0;
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
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
					<?php } else {
							$cuit = substr($key,0,11);  
							$totRete += $rowFactura['impretencion']; 
							if ($rowFactura['nopagar'] == 1) {
								$totNopagar += $rowFactura['impapagar'];
							} else {
								$totApagar += $rowFactura['impapagar'];
							} ?>
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
					 			<td style="background-color: #99bfe6"><b><?php if($rowFactura['retiene'] == 1) { echo "SI"; } else { echo "NO"; } ?></b></td>
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impretencion'],2,",",".") ?></b></td>
					 			<td style="background-color: #99bfe6"><b><?php if($rowFactura['nopagar'] == 1) { echo "X"; } ?></b></td>
					 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impapagar'],2,",",".") ?></b></td>
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
						<th rowspan="2" colspan="2"><?php echo number_format($totRete,2,",",".") ?></th>
						<th rowspan="2"><?php echo number_format($totNopagar,2,",",".") ?></th>
						<th rowspan="2"><?php echo number_format($totApagar,2,",",".") ?></th>
					</tr>
					<tr>					
						<th colspan="2"><?php echo number_format($totMonNOI + $totMonOS,2,",",".") ?></td>
					</tr>
					<tr>
						<th>COM<br><?php echo number_format($totComSub,2,",",".") ?></td>
						<th colspan="3">DEB+SOL+NOI<br><?php echo number_format($totMonDeb + $totSolSub + $totMonNOI,2,",",".") ?></td>
						<th colspan="2">NOI+OS+SUB+DEB<br><?php echo number_format($totMonNOI + $totMonOS + $totMonSub + $totMonDeb,2,",",".") ?></th>
						<th colspan="4">RET+DEB+NO PAG+PAG<br><?php echo number_format($totRete + $totMonDeb + $totNopagar + $totApagar,2,",",".") ?></th>
					</tr>	
			</table>
		</form>
	</div>
</body>
</html>