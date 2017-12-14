<?php 
include_once 'include/conector.php';
$sqlNomenclador = "SELECT * FROM nomenclador ORDER BY codigo";
$resNomenclador =  mysql_query($sqlNomenclador);

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
						<td><?php echo $rowNomenclador['codigo'] ?></td>
						<td><?php echo $rowNomenclador['descripcion'] ?></td>
					</tr>
			<?php } ?>
			  	</tbody>
			</table>
		</div>
	</div>
</body>
</html>