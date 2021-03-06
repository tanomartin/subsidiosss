<?php include_once 'include/conector.php';

$sqlPromedio = "SELECT
					count(*) as cantidad,
					sum(importesolicitado) as totsoli, 
					sum(importeliquidado) as totliqui,
					sum(importesolicitado) - sum(importeliquidado) as dif,
					sum(importesolicitado) / count(*) as promsol,
					sum(importeliquidado) / count(*) as promliq,
					(sum(importesolicitado) - sum(importeliquidado)) / count(*) as promdif,
					(sum(importeliquidado) / sum(importesolicitado)) * 100 as porcentaje
				FROM interendicioncontrol";
$resPromedio = mysql_query($sqlPromedio);
$rowPromedio = mysql_fetch_array($resPromedio);

$today = date("Y-m-d");
$sqlCarpetaActual = "SELECT id FROM intecronograma i where fechacierre >= '$today' LIMIT 1";
$resCarpetaActual = mysql_query($sqlCarpetaActual);
$rowCarpetaActual = mysql_fetch_array($resCarpetaActual);
$idCarpetaActual = $rowCarpetaActual['id'];

$sqlCantidadPresentada = "SELECT count(id) as cantPresentadas 
							FROM intepresentacion 
							WHERE idcronograma != $idCarpetaActual";
$resCantidadPresentada = mysql_query($sqlCantidadPresentada);
$rowCantidadPresentada = mysql_fetch_array($resCantidadPresentada); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<title>.: Presentaciones S.S.S. :.</title>
<style type="text/css" media="print">
.nover {display:none}
</style>

</head>
<body bgcolor="#CCCCCC">
	<div align="center">
	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'informes.php'" /></p>
	<h2>Datos Totalizadores de Presentaciones</h2>
	<div class="grilla" style="width: 90%">
		<table>
			<thead>
				<tr>
					<th>Can.</br>Pres.</th>
					<th>Can.</br>Pres/Car</th>
					<th>Tot.</br>Solicitado</th>
					<th>Tot.</br>Liquidado</th>
					<th>Tot.</br>Diferencia</th>
					<th>Prom.</br>Solicitado</th>
					<th>Prom.</br>Liquidado</th>
					<th>Prom.</br>Diferencia</th>
					<th>%.</br>Recuperado</th>
				</tr>
			</thead>
				<tr>
					<td><?php echo $rowPromedio['cantidad']?></td>
					<td><?php echo number_format($rowCantidadPresentada['cantPresentadas'] / $rowPromedio['cantidad'],"2",",",".") ?></td>
					<td><?php echo "$ ".number_format($rowPromedio['totsoli'],"2",",",".") ?></td>
					<td><?php echo "$ ".number_format($rowPromedio['totliqui'],"2",",",".") ?></td>
					<td><?php echo "$ ".number_format($rowPromedio['dif'],"2",",",".") ?></td>
					<td><?php echo "$ ".number_format($rowPromedio['promsol'],"2",",",".") ?></td>
					<td><?php echo "$ ".number_format($rowPromedio['promliq'],"2",",",".") ?></td>
					<td><?php echo "$ ".number_format($rowPromedio['promdif'],"2",",",".") ?></td>
					<td><?php echo number_format($rowPromedio['porcentaje'],"2",",","")." %" ?></td>
				</tr>
			<tbody>
			</tbody>
		</table>
	</div>
	<h2>Detalle Presentaciones Finalizadas</h2>
	<?php $sqlIds = "SELECT idpresentacion FROM interendicioncontrol order by idpresentacion DESC";
		  $resIds = mysql_query($sqlIds);
		  while ($rowIds = mysql_fetch_array($resIds)) {
				$idPresentacion = $rowIds['idpresentacion'];
				include("include/detalle.php");
	  	  } ?>
	<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
</body>
</html>