<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Devolucion Formato S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Devolucion Formato S.S.S.</h2>
	 	<?php include_once("include/detalle.php")?>
	 	<form  action="presentacion.devformato.guardar.php" enctype="multipart/form-data" method="post">
 			<input style="display: none" type="text" name="id" id="id" value="<?php echo $idPresentacion ?>" />
 			<h3>Cargar Archivo Formato OK</h3>
 			<p><input type="file" name="archivook" id="archivook" accept=".ok" /></p>
 			<h3>Cargar Archivo Formato ERROR</h3>
 			<p><input type="file" name="archivoerror" id="archivoerror" accept=".err" /></p>
 			<p><input type="submit" name="importar"  value="Cargar Devolucion Formato"/></p>
 		</form>
	</div>
</body>
</html>