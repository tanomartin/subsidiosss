<?php 
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];

$sqlReversiones = "SELECT * FROM intepresentaciondetalle WHERE idpresentacion = $idPresentacion and tipoarchivo = 'DB'";
$resReversiones = mysql_query($sqlReversiones);
$canReversiones= mysql_num_rows($resReversiones);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Reversiones Presentaciones S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Listado de Reversiones de Presentacion</h2>
	 		<?php include_once("include/detalle.php")?>
	 		<?php if ($canReversiones != 0) { ?>
	 		
	 		<?php } else { ?>
	 		   		<h3 style="color: blue">No hay reversiones cargadas para esta presentacion</h3>
	 		<?php }?>
	 		<input type="button" name="nueva" id="nueva" value="Nueva Reversion" onClick="location.href = 'presentacion.reversiones.nueva.php?id=<?php echo $idPresentacion ?>'"/>
	</div>
</body>
</html>