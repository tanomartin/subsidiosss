<?php
include_once 'include/conector.php';
$carpeta = $_GET['carpeta'];
$idPresentacion = $_GET['idpresentacion'];
$id = $_GET['id'];

$sqlDevolucion = "SELECT * FROM intefondodevolucion i, interendicion r WHERE idpresfondos = $id and i.clave = r.clave";
$resDevolucion = mysql_query($sqlDevolucion);

$sqlErrores = "SELECT * FROM inteerror WHERE proceso = 'APLICACION DE FONDO'";
$resErrores = mysql_query($sqlErrores);
$arrayErrores = array();
while ($rowErrores = mysql_fetch_array($resErrores)) {
	$arrayErrores[$rowErrores['id']] = array("campo" => $rowErrores['campo'], "descrip" => $rowErrores['descripcion']);
}

$sqlCantidad = "SELECT coderror FROM intefondodevolucion WHERE idpresfondos = $id";
$resCantidad = mysql_query($sqlCantidad);
$arrayCantidad = array();
while ($rowCantidad = mysql_fetch_array($resCantidad)) {
	$keyArray = explode("-",$rowCantidad['coderror']);
	foreach ($keyArray as $key => $error) {
		if ($error != "") {
			if (isset($arrayCantidad[$error])) { $arrayCantidad[$error] += 1; } else { $arrayCantidad[$error] = 1; }
		}
	}
}
ksort($arrayCantidad);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Devolucion Fondos Errores S.S.S. :.</title>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/tablas.css"/>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>

<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
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
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'fondos.php'" /></p>
	 	<h2>Devolucion Fondos Errores S.S.S.</h2>
	 	<?php include_once("include/detalle.php")?>
	 	<h3>Errores Aplicacion de Fondos - ID: <?php echo $id ?></h3>
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
	 	  		foreach ($arrayCantidad as $error=>$cantidad) {
	 				$totalErrores += $cantidad;  ?>
	 				<tr>
	 					<td><?php echo $error ?></td>
	 					<td><?php echo $cantidad ?></td>
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
	 	
	 	<h3>Detalle Errores Aplicacion de Fondos</h3>
	 	
		<table id="listaResultado" class="tablesorter" style="text-align: center;">
			 <thead>
			 	<tr>
			 		<th style="font-size: 11px">Clave</th>
			 		<th style="font-size: 11px">Tipo</th>
			 		<th style="font-size: 11px">C.U.I.L.</th>
			 		<th style="font-size: 11px">Periodo</th>
			 		<th style="font-size: 11px">C.U.I.T.</th>
			 		<th style="font-size: 11px">Num. Comp.</th>
			 		<th style="font-size: 11px">Nro. Orden</th>
			 		<th style="font-size: 11px">$ Trans</th>
			 		<th style="font-size: 11px">$ Ret.</th>
			 		<th style="font-size: 11px">$ SSS</th>
			 		<th style="font-size: 11px">$ F.P</th>
			 		<th style="font-size: 11px">$ F.P.O.C.</th>
			 		<th style="font-size: 11px">Recibo</th>
			 		<th style="font-size: 11px">$ Tras</th>
			 		<th style="font-size: 11px">$ Dev</th>
			 		<th style="font-size: 11px">$ No Apli</th>
			 		<th style="font-size: 11px">$ Recu</th>
			 		<th style="font-size: 11px">ERROR</th>
			 	</tr>
			 </thead>
			<tbody>
	 <?php  while ($rowDevolucion = mysql_fetch_array($resDevolucion)) { ?>
				<tr>
					<td style="font-size: 12px"><?php echo $rowDevolucion['clave'] ?></td>
					<td style="font-size: 12px"><?php echo $rowDevolucion['tipoarchivo'] ?></td>
					<td style="font-size: 12px"><?php echo $rowDevolucion['cuil'] ?></td>
					<td style="font-size: 12px"><?php echo $rowDevolucion['periodoprestacion'] ?></td>
					<td style="font-size: 12px"><?php echo $rowDevolucion['cuit'] ?></td>
					<td style="font-size: 12px"><?php echo $rowDevolucion['nrocomprobante'] ?></td>
					<td style="font-size: 12px"><?php echo $rowDevolucion['ordenpago1'] ?></td>
					<td style="font-size: 12px"><?php echo number_format($rowDevolucion['importetranf'],2,",",".") ?></td>
					<td style="font-size: 12px"><?php echo number_format($rowDevolucion['retganancias'],2,",",".") ?></td>
					<td style="font-size: 12px"><?php echo number_format($rowDevolucion['impaplicadosss'],2,",",".") ?></td>
					<td style="font-size: 12px"><?php echo number_format($rowDevolucion['impfondospropios'],2,",",".") ?></td>
					<td style="font-size: 12px"><?php echo number_format($rowDevolucion['impfondosoc'],2,",",".") ?></td>
					<td style="font-size: 12px"><?php echo $rowDevolucion['nrorecibo'] ?></td>
					<td style="font-size: 12px"><?php echo number_format($rowDevolucion['imptrasladado'],2,",",".") ?></td>
					<td style="font-size: 12px"><?php echo number_format($rowDevolucion['impdevuelto'],2,",",".") ?></td>
					<td style="font-size: 12px"><?php echo number_format($rowDevolucion['saldonoaplicado'],2,",",".") ?></td>
					<td style="font-size: 12px"><?php echo number_format($rowDevolucion['recuperofondos'],2,",",".") ?></td>
				 	<td style="font-size: 12px; color: red">
				  <?php $explodeErrores = explode("-",$rowDevolucion['coderror']);
						foreach ($explodeErrores as $error) {
							if ($error != "") {
								$error = (int) $error;
								echo "ERROR: $error - COLUMNA: ".$arrayErrores[$error]['campo']." (".$arrayErrores[$error]['descrip'].")<br>";
					  		}
						} ?>
				  	</td>
				</tr>
	  <?php } ?>
			</tbody>
		</table>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>