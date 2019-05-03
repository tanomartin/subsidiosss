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
<title>.: Devolucion Fondos Extracto S.S.S. :.</title>

<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="include/jquery.blockUI.js" type="text/javascript"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>

<script type="text/javascript">

function subirExtracto(formulario) {
	$.blockUI({ message: "<h1>Subiendo Extracto Bancario... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
}

</script>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'fondos.php'" /></p>
	 	<h2>Devolucion Fondos Extracto S.S.S.</h2>
	 	<?php include_once("include/detalle.php")?>
	 	<form action="fondos.extracto.guardar.php" enctype="multipart/form-data" method="post" onSubmit="subirExtracto(this)">
 			<input style="display: none" type="text" name="carpeta" id="carpeta" value="<?php echo $carpeta ?>" />
 			<input style="display: none" type="text" name="idfondos" id="idfondos" value="<?php echo $id ?>" />
 			<input style="display: none" type="text" name="idpresentacion" id=""idpresentacion"" value="<?php echo $idPresentacion ?>" />
 			<h3>Cargar Extracto Bancario</h3>
 			<p><input type="file" name="archivoextracto" id="archivoextracto" accept=".pdf" /></p>
 			<p><input type="submit" name="importar"  value="Cargar Extracto"/></p>
 		</form>
	</div>
</body>
</html>