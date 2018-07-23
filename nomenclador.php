<?php 
include_once 'include/conector.php';
$sqlNomenclador = "SELECT * FROM madera.practicas WHERE nomenclador = 7 ORDER BY codigopractica";
$resNomenclador = mysql_query($sqlNomenclador);
$canNomencaldor = mysql_num_rows($resNomenclador);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet"href="include/jquery.tablesorter/themes/theme.blue.css" />
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>

<script>
$(function() {
	$("#listado")
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
	}).tablesorterPager({container: $("#paginador")});
});
</script>

<title>.: Nomenclador S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="reset" name="volver" value="Volver" onClick="location.href = 'menu.php'" /></p>
	 	<h2>Nomenclador Subsidio S.S.S.</h2>
		<div style="width: 900px">
			<table class="tablesorter" id="listado">
			 	<thead>
			 		<tr>
			 			<th>Código</th>
			 			<th>Descripcion</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php while ($rowNomenclador = mysql_fetch_array($resNomenclador)) {  ?>
					<tr>
						<td><?php echo $rowNomenclador['codigopractica'] ?></td>
						<td><?php echo $rowNomenclador['descripcion'] ?></td>
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
							<option value="<?php echo $canNomencaldor?>">Todos</option>
						</select>
					</p>
				</form>
			</div>
		</div>
	</div>
</body>
</html>