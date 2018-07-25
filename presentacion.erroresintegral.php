<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];

$sqlFactura = "SELECT * FROM intepresentaciondetalle WHERE idpresentacion = $idPresentacion and deverrorintegral is not null";
$resFactura = mysql_query($sqlFactura);

$sqlErrores = "SELECT * FROM inteerror";
$resErrores = mysql_query($sqlErrores);
$arrayErrores = array();
while ($rowErrores = mysql_fetch_array($resErrores)) {
	$arrayErrores[$rowErrores['id']] = array("campo" => $rowErrores['campo'], "descrip" => $rowErrores['descripcion']);
}

$sqlCantidad = "SELECT deverrorintegral, count(*) as cantidad FROM intepresentaciondetalle where idpresentacion = $idPresentacion and deverrorintegral is not null group by deverrorintegral";
$resCantidad = mysql_query($sqlCantidad);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Detalle Errores Integral S.S.S. :.</title>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/tablas.css"/>
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
			headers:{9:{sorter:false, filter:false},
					 10:{sorter:false, filter:false},
					 11:{filter:false},
					 12:{sorter:false, filter:false},
					 13:{sorter:false},
					 14:{sorter:false, filter:false},
					 15:{sorter:false, filter:false},
					 16:{sorter:false, filter:false},
					 17:{sorter:false, filter:false}},
			widgets: ["zebra", "filter"], 
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

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	
	 	<?php include_once("include/detalle.php")?>
	 	
	 	<h2>Errores Integrales</h2>
	 	
	 	<div class="grilla" style="width: 400px">
	 		<table>
	 			<thead>
	 				<tr>
		 				<th>Error</th>
		 				<th>Cantidad</th>
	 				</tr>
	 			</thead>
	 			<tbody>
	 	<?php 	$totalErrores = 0;
	 	  		while ($rowCantidad = mysql_fetch_array($resCantidad)) {  
	 				$totalErrores += $rowCantidad['cantidad'];  ?>
	 				<tr>
	 					<td><?php echo substr($rowCantidad['deverrorintegral'],1,4)?></td>
	 					<td><?php echo $rowCantidad['cantidad']?></td>
	 				</tr>
	 	<?php   } ?>
	 			</tbody>
	 			<thead>
		 			<tr>
		 				<th>TOTAL</th>
		 				<th><?php echo $totalErrores?></th>
		 			</tr>
	 			</thead>
	 		</table>
	 	</div>	
	 	
	 	<h2>Detalle Errores Integrales</h2>
			 
			 <table id="listaResultado" class="tablesorter" style="text-align: center;">
			 	<thead>
			 		<tr>
			 			<th style="font-size: 11px" rowspan="2">Comp. Interno</th>
			 			<th style="font-size: 11px" rowspan="2">Tipo</th>
			 			<th style="font-size: 11px" rowspan="2">C.U.I.L.</th>
			 			<th style="font-size: 11px" rowspan="2">Periodo</th>
			 			<th style="font-size: 11px" rowspan="2">C.U.I.T.</th>
			 			<th style="font-size: 11px" rowspan="2">Practica</th>
			 			<th style="font-size: 11px" rowspan="2">Dep</th>
			 			<th style="font-size: 11px" rowspan="2">Fec. Comp.</th>
			 			<th style="font-size: 11px" rowspan="2">Num. Comp.</th>
			 			<th style="font-size: 11px" colspan="2">Presentacion</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Formato</th>
			 			<th style="font-size: 11px" rowspan="2">ERROR INTEGRAL</th>
			 		</tr>
			 		<tr>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php 
				$totCom = 0;
				$totSol = 0;
				$totComFor = 0;
				$totSolFor = 0;
				while ($rowFactura = mysql_fetch_array($resFactura)) { 
					$totCom += $rowFactura['impcomprobante'];
					$totSol += $rowFactura['impsolicitado'];
					$totComFor += $rowFactura['impcomprobanteformato'];
					$totSolFor += $rowFactura['impsolicitadoformato'];	?>
					<tr>
						<td style="font-size: 12px"><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['cuil'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['periodo'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['cuit'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['codpractica'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['dependencia'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td style="font-size: 12px"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						<td style="font-size: 12px"><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
						<td style="font-size: 12px"><?php echo number_format($rowFactura['impcomprobanteformato'],2,",",".") ?></td>
						<td style="font-size: 12px"><?php echo number_format($rowFactura['impsolicitadoformato'],2,",",".") ?></td>
				 		<td style="font-size: 12px; color: red">
				  <?php $explodeErrores = explode("-",$rowFactura['deverrorintegral']);
						foreach ($explodeErrores as $error) {
							if ($error != "") {
								$error = (int) $error;
								echo "ERROR: $error - COLUMNA: ".$arrayErrores[$error]['campo']." (".$arrayErrores[$error]['descrip'].")<br>";
				  			}
						}?>
				  		</td>
					</tr>
			<?php } ?>
					<tr>
						<th colspan="9">TOTALES</td>
						<th><?php echo number_format($totCom,2,",",".") ?></th>
						<th><?php echo number_format($totSol,2,",",".") ?></th>
						<th><?php echo number_format($totComFor,2,",",".") ?></th>
						<th><?php echo number_format($totSolFor,2,",",".") ?></th>
						<th></th>
					</tr>
			  	</tbody>
			</table>
		
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>