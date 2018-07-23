<?php
include_once 'include/conector.php';
$sqlTotales = "SELECT i.*, DATE_FORMAT(i.fechapago,'%d/%m/%Y') as fechapago FROM inteinterbankingcabecera i ORDER BY id DESC";
$resTotales = mysql_query($sqlTotales);
$numTotales = mysql_num_rows($resTotales);

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
<script src="/madera/lib/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["filter"],
			widgetOptions : { 
				filter_cssFilter   : '',
				filter_childRows   : false,
				filter_hideFilters : false,
				filter_ignoreCase  : true,
				filter_searchDelay : 300,
				filter_startsWith  : false,
				filter_hideFilters : false,
			}
		}).tablesorterPager({container: $("#paginador")});
});

function cancelarPresentacion(id) {
	var redire = "interbanking.pago.cancelar.php?id="+id;
	var r = confirm("Desea Elminar el Pago completo con id "+ id);
	if (r == true) {
		window.location.href = redire;
	} 
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
		<h2>Pagos Realizados Interbanking</h2>
		<?php if ($numTotales > 0) { ?>
			<table id="listaResultado" class="tablesorter" style="text-align: center; font-size: 15px; width: 900px" >
				<thead>
					 <tr>
					 	<th>Id</th>
					 	<th>Fecha Pago</th>
					 	<th>Cant Pagos</th>
					 	<th>Nro. Secuencia</th>
					 	<th>$ Comp.</th>
					 	<th>$ Deb.</th>
					 	<th>$ O.S.</th>
					 	<th>$ S.S.S</th>
					 	<th>$ Ret.</th>
					 	<th>$ Transf.</th>
					 	<th></th>
					 </tr>
				</thead>
				<tbody>
		  <?php while ($rowTotales = mysql_fetch_assoc($resTotales)) {  ?>
					<tr>
						<td><?php echo $rowTotales['id'] ?></td>
						<td><?php echo $rowTotales['fechapago'] ?></td>
						<td><?php echo $rowTotales['cantidad'] ?></td>
						<td><?php echo $rowTotales['nrosecuencia'] ?></td>
						<td><?php echo number_format($rowTotales['impcomprobanteintegral'],2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impdebito'],2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impnointe'] + $rowTotales['impobrasocial'] ,2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impmontosubsidio'],2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impretencion'],2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impapagar'],2,",",".") ?></td>
						<td>
							<input type="button" value="DETALLE" onclick="location.href = 'interbanking.pago.realizado.detalle.php?id=<?php echo $rowTotales['id']?>'"/>
					<?php if ($_SESSION['usuario'] == "sistemas" || $_SESSION['usuario'] == "vresch") { ?>
						  	<input type="button" value="CANCELAR" onclick="cancelarPresentacion(<?php echo $rowTotales['id'] ?>)"/>
					<?php }?>
						</td>
					</tr>
			<?php } ?>
				</tbody>
			</table>
			<div id="paginador" class="pager">
				<form>
					<p>
						<img src="img/first.png" width="16" height="16" class="first"/>
						<img src="img/prev.png" width="16" height="16" class="prev"/>
						<input type="text" class="pagedisplay" size="8" readonly="readonly" style="background:#CCCCCC; text-align:center"/>
						<img src="img/next.png" width="16" height="16" class="next"/>
						<img src="img/last.png" width="16" height="16" class="last"/>
					</p>
					<p>
						<select class="pagesize">
							<option selected value="10">10 por pagina</option>
							<option value="20">20 por pagina</option>
							<option value="30">30 por pagina</option>
							<option value="50">50 por pagina</option>
							<option value="<?php echo $numTotales?>">Todos</option>
						</select>
					</p>
				</form>
			</div>
  <?php } else { ?>
			<h3 style="color: blue">No existen pagos realizados</h3>
  <?php }?>
	</div>
</body>
</html>