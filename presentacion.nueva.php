<?php 
include_once 'include/conector.php';

$sqlAPresentar = "SELECT c.*,DATE_FORMAT(c.fechacierre,'%m/%d/%Y') as fechacierre FROM cronograma c WHERE fechacierre >=  CURDATE() LIMIT 1";
$resAPresentar = mysql_query($sqlAPresentar);
$rowAPresentar = mysql_fetch_array($resAPresentar)
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>.: Nueva Presentaciones S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Nueva Presentacion S.S.S.</h2>
	 	<table border="1" style="text-align: center">
	 		<tr><td><p><b>Periodo:</b> <?php echo $rowAPresentar['periodo']?></p></td></tr>
	 		<tr><td><p><b>Carpeta:</b> <?php echo $rowAPresentar['carpeta']?></p></td></tr>
	 		<tr><td><p><b>Fecha de Cierre:</b> <?php echo $rowAPresentar['fechacierre']?></p></td></tr>
	 		<tr><td><p><b>Periodos Incluidos:</b> <?php echo $rowAPresentar['periodosincluidos']?></p></td></tr>
	 	</table>
	 	<form  action="presentacion.nueva.guardar.php" enctype="multipart/form-data" method="post">
 			<p><input style="display: none" type="text" name="idCronograma" id="idCronograma" value="<?php echo $rowAPresentar['id']?>"/></p>
 			<p><input style="display: none" type="text" name="carpeta" id="carpeta" value="<?php echo $rowAPresentar['carpeta']?>"/></p>
 			<h3>Cargar Excel con las facturas a incluir</h3>
 			<p><input type="file" name="archivo" id="archivo" accept=".csv" /></p>
 			<p><input type="submit" name="importar"  value="Cargar Facturas"/></p>
 		</form>
	</div>
</body>
</html>