<?php
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];
$sqlPresentacion = "SELECT
p.id,
DATE_FORMAT(p.fechapresentacion, '%d-%m-%Y') as fechapresentacion,
DATE_FORMAT(p.fechacancelacion, '%d-%m-%Y') as fechacancelacion,
p.motivocancelacion,
p.cantfactura,
p.impcomprobantes,
p.impsolicitado ,
cronograma.periodo,
cronograma.carpeta
FROM presentacion p
INNER JOIN cronograma on p.idcronograma = cronograma.id
WHERE p.id = $idPresentacion";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Rendición Subsidio S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Rendición Subsidio S.S.S.</h2>
	 	<?php include_once("include/detalle.php") ?>
	 	<form  action="presentacion.devrendicion.guardar.php" enctype="multipart/form-data" method="post">
 			<input style="display: none" type="text" name="id" id="id" value="<?php echo $rowPresentacion['id']?>" />
 			<h3>Cargar Archivo Rendicion</h3>
 			<p><input type="file" name="archivoEnvio" id="archivoEnvio" accept=".ENVIO" /></p>
 			<h3>Cargar Archivo Rendicion Control</h3>
 			<p><input type="file" name="archivoControl" id="archivoControl" accept=".CONTROL" /></p>
 			<p><input type="submit" name="importar"  value="Cargar Rendicion"/></p>
 		</form>
	</div>
</body>
</html>