<?php 
include_once 'include/conector.php';
$sqlCronograma = "SELECT *, 
						 DATE_FORMAT(fechacierre,'%d/%m/%Y') as fechacierre,
						 DATE_FORMAT(fechapago,'%d/%m/%Y') as fechapago,
						 DATE_FORMAT(fechacierrefondo,'%d/%m/%Y') as fechacierrefondo
					FROM intecronograma ORDER By carpeta DESC";
$resCronograma = mysql_query($sqlCronograma);

$today = date("Y-m-d");
$carpeta = date("Ym");
$sqlCarpetaActual = "SELECT id FROM intecronograma i where fechacierre >= '$today' LIMIT 1";
$resCarpetaActual = mysql_query($sqlCarpetaActual);
$rowCarpetaActual = mysql_fetch_array($resCarpetaActual);
$idCarpetaActual = $rowCarpetaActual['id'];

$sqlCarpetaActualFondo = "SELECT id FROM intecronograma i where fechacierrefondo >= '$today' LIMIT 1";
$resCarpetaActualFondo = mysql_query($sqlCarpetaActualFondo);
$rowCarpetaActualFondo = mysql_fetch_array($resCarpetaActualFondo);
$idCarpetaActualFondo = $rowCarpetaActualFondo['id'];
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
	 <h3><span style="color: red"> Periodo de Presentacion</span> | <span style="color: blue">Periodo Aplicacion de Fondo</span></h3>
	 <div class="grilla">
		 <table>
		 	<thead>
		 		<tr>
		 			<th>Período</th>
		 			<th>Carpeta</th>
		 			<th>Fecha </br>de Cierre</th>
		 			<th>Periodos Incluidos</th>
		 			<th>Fecha </br>Estimada Pago</th>
		 			<th>Fecha </br>Cierre Fondo</th>
		 		</tr>
		 	</thead>
		 	<tbody>
		<?php while ($rowCronograma = mysql_fetch_array($resCronograma)) { 
				$color = "";
				$colorFondo = "";
				if ($idCarpetaActual == $rowCronograma['id']) { $color = 'style = "color: red"'; }
				if ($idCarpetaActualFondo == $rowCronograma['id']) { $colorFondo = 'style = "color: blue"'; }?>
				<tr>
					<td <?php echo $color.$colorFondo ?>><?php echo $rowCronograma['periodo'] ?></td>
					<td <?php echo $color.$colorFondo ?>><?php echo $rowCronograma['carpeta'] ?></td>
					<td <?php echo $color.$colorFondo ?>><?php if ($rowCronograma['fechacierre'] != NULL) { echo $rowCronograma['fechacierre']; } else { echo "-"; } ?></td>
					<td <?php echo $color.$colorFondo ?>><?php echo $rowCronograma['periodosincluidos'] ?></td>
					<td <?php echo $color.$colorFondo ?>><?php if ($rowCronograma['fechapago'] != NULL) { echo $rowCronograma['fechapago']; } else { echo "-"; } ?></td>
					<td <?php echo $color.$colorFondo ?>><?php if ($rowCronograma['fechacierrefondo'] != NULL) { echo $rowCronograma['fechacierrefondo']; } else { echo "-"; } ?></td>
				</tr>
		<?php } ?>
		  	</tbody>
		</table>
	</div>
	</div>
</body>
</html>