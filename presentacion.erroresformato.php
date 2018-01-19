<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];

$sqlFactura = "SELECT * FROM intepresentaciondetalle WHERE idpresentacion = $idPresentacion and deverrorformato is not null";
$resFactura = mysql_query($sqlFactura);

$sqlErrores = "SELECT * FROM inteerror";
$resErrores = mysql_query($sqlErrores);
$arrayErrores = array();
while ($rowErrores = mysql_fetch_array($resErrores)) {
	$arrayErrores[$rowErrores['id']] = array("campo" => $rowErrores['campo'], "descrip" => $rowErrores['descripcion']);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Detalle Errores Formato S.S.S. :.</title>
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
	 	
	 	<h2>Errores Formato</h2>
	 	
			 <table id="listaResultado" class="tablesorter" style="text-align: center;">
			 	<thead>
			 		<tr>
			 			<th style="font-size: 11px">Comp. Interno</th>
			 			<th style="font-size: 11px">Tipo</th>
			 			<th style="font-size: 11px">C.U.I.L.</th>
			 			<th style="font-size: 11px">Periodo</th>
			 			<th style="font-size: 11px">C.U.I.T.</th>
			 			<th style="font-size: 11px">Practica</th>
			 			<th style="font-size: 11px">Dep</th>
			 			<th style="font-size: 11px">Fec. Comp.</th>
			 			<th style="font-size: 11px">Num. Comp.</th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">FORMATO ERROR</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php 
				$totCom = 0;
				$totSol = 0;
				while ($rowFactura = mysql_fetch_array($resFactura)) { 
					$totCom += $rowFactura['impcomprobante'];
					$totSol += $rowFactura['impsolicitado'];?>
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
				 		<td style="font-size: 12px; color: red">
				  <?php $explodeErrores = explode("-",$rowFactura['deverrorformato']);
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
						<th colspan="9">TOTALES</th>
						<th><?php echo number_format($totCom,2,",",".") ?></th>
						<th><?php echo number_format($totSol,2,",",".") ?></th>
						<th></th>
					</tr>
			  	</tbody>
			</table>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>