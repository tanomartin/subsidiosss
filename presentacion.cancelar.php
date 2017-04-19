<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];
$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Cancelar Presentaciones S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Confirmación de Cancelación de Presentacion</h2>
	 	<h2>Detalle Presentacion</h2>
	 	<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	<h2>Facturas</h2>
	 	<h3>Cantidad: <?php echo $rowPresentacion['cantfactura']?> - Total Importe Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantes'],"2",",",".") ?> - Total Imporate Solicitado: <?php echo number_format($rowPresentacion['impsolicitado'],"2",",",".") ?></h3>
		<form action="presentacion.cancelar.guardar.php?id=<?php echo $rowPresentacion['id']?>" method="post">
			<p><h3>Montivo</h3><textarea id="motivo" name="motivo" rows="8" cols="70"></textarea></p>
			<p><input type="submit" name="cancelar" value="Cancelar Presentación" /></p>
		</form>
	</div>
</body>
</html>