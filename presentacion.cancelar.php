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
	 	<h2>Detalle Presentacio</h2>
	 	<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	<h2>Facturas</h2>
	 	<h3>Cantidad: <?php echo $rowPresentacion['cantfactura']?> - Total Importe Comprobantes: <?php echo $rowPresentacion['sumimpcomprobante'] ?> - Total Imporate Solicitado: <?php echo $rowPresentacion['sumimpsolicitado'] ?></h3>
		<p><input type="button" name="cancelar" value="Cancelar Presentación" onClick="location.href = 'presentacion.cancelar.guardar.php?id=<?php echo $rowPresentacion['id']?>'" /></p>
	</div>
</body>
</html>