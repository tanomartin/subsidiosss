<?php 
include_once 'include/conector.php';
$sqlCronograma = "SELECT c.*,DATE_FORMAT(c.fechacierre,'%d/%m/%Y') as fechacierre FROM intecronograma c ORDER By carpeta DESC";
$resCronograma = mysql_query($sqlCronograma);

$today = date("Y-m-d");
$carpeta = date("Ym");
$sqlCarpetaActual = "SELECT id FROM intecronograma i where fechacierre >= '$today' and carpeta < '$carpeta'";
$resCarpetaActual = mysql_query($sqlCarpetaActual);
$rowCarpetaActual = mysql_fetch_array($resCarpetaActual);
$idCarpetaActual = $rowCarpetaActual['id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Cronograma S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 <p><input type="reset" name="volver" value="Volver" onClick="location.href = 'menu.php'" /></p>
	 <h2>Cronograma Subsidio S.S.S.</h2>
	 <div class="grilla">
		 <table>
		 	<thead>
		 		<tr>
		 			<th>Período</th>
		 			<th>Carpeta</th>
		 			<th>Fecha de Cierre</th>
		 			<th>Periodos Incluidos</th>
		 		</tr>
		 	</thead>
		 	<tbody>
		<?php while ($rowCronograma = mysql_fetch_array($resCronograma)) { 
				$color = "";
				if ($idCarpetaActual == $rowCronograma['id']) { $color = 'style = "color: red"'; }?>
				<tr>
					<td <?php echo $color ?>><?php echo $rowCronograma['periodo'] ?></td>
					<td <?php echo $color ?>><?php echo $rowCronograma['carpeta'] ?></td>
					<td <?php echo $color ?>><?php echo $rowCronograma['fechacierre'] ?></td>
					<td <?php echo $color ?>><?php echo $rowCronograma['periodosincluidos'] ?></td>
				</tr>
		<?php } ?>
		  	</tbody>
		</table>
	</div>
	</div>
</body>
</html>