<?php 
include_once 'include/conector.php';
$sqlErrores = "SELECT * FROM inteerror c ORDER BY id";
$resErrores = mysql_query($sqlErrores);

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
	});
});
</script>

<title>.: Errores S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="reset" name="volver" value="Volver" onClick="location.href = 'menu.php'" /></p>
	 	<h2>Errores Subsidio S.S.S.</h2>
		 <table class="tablesorter" id="listado">
		 	<thead>
		 		<tr>
		 			<th>Código</th>
		 			<th class="filter-select" data-placeholder="Seleccione Campo">Campo del error</th>
		 			<th>Descripcion</th>
		 			<th>Accion a realizar</th>
		 		</tr>
		 	</thead>
		 	<tbody>
		<?php while ($rowErrores = mysql_fetch_array($resErrores)) {  ?>
				<tr>
					<td><?php echo $rowErrores['id'] ?></td>
					<td><?php echo $rowErrores['campo'] ?></td>
					<td><?php echo $rowErrores['descripcion'] ?></td>
					<td><?php echo $rowErrores['accion'] ?></td>
				</tr>
		<?php } ?>
		  	</tbody>
		</table>
	</div>
</body>
</html>