<?php
include_once 'include/conector.php';
$carpeta = $_GET['carpeta'];
$idPresentacion = $_GET['idpresentacion'];
$id = $_GET['id'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Devolucion Fondos S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'fondos.php'" /></p>
	 	<h2>Devolucion Fondos S.S.S.</h2>
	 	<?php include_once("include/detalle.php")?>
	 	<form  action="fondos.devolucion.guardar.php" enctype="multipart/form-data" method="post">
 			<input style="display: none" type="text" name="carpeta" id="carpeta" value="<?php echo $carpeta ?>" />
 			<input style="display: none" type="text" name="idfondos" id="idfondos" value="<?php echo $id ?>" />
 			<input style="display: none" type="text" name="idpresentacion" id=""idpresentacion"" value="<?php echo $idPresentacion ?>" />
 			<h3>Cargar Archivo OK</h3>
 			<p><input type="file" name="archivook" id="archivook" accept=".txt" /></p>
 			<h3>Cargar Archivo ERROR</h3>
 			<p><input type="file" name="archivoerror" id="archivoerror" accept=".txt" /></p>
 			<p><input type="submit" name="importar"  value="Cargar Devolucion"/></p>
 		</form>
	</div>
</body>
</html>