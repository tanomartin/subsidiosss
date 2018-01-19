<?php 
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Cancelar Presentaciones S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Confirmación de Cancelación de Presentacion</h2>
	 		<?php include_once("include/detalle.php")?>
	 		<form action="presentacion.cancelar.guardar.php?id=<?php echo $idPresentacion?>" method="post">
			<p><h3>Montivo</h3><textarea id="motivo" name="motivo" rows="8" cols="70"></textarea></p>
			<p><input type="submit" name="cancelar" value="Cancelar Presentación" /></p>
		</form>
	</div>
</body>
</html>