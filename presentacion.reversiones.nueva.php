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
<title>.: Nueva Reversiones Presentaciones S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.reversiones.php?id=<?php echo $idPresentacion ?>'" /></p>
	 	<h2>Nueva Reversion para la Presentacion</h2>
	 	<?php include_once("include/detalle.php")?>
	 	<form action="presentacion.reversiones.guardar.php?id=<?php echo $idPresentacion ?>" method="post" onSubmit="return validar()">
	 		<p><input type="submit" value="Guardar" style="margin-top: 15px"></input></p>
	 	</form>
	 </div>
</body>
</html>
