<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];

$idFactura = $_POST['nrocominterno'];
$tipoRev = $_POST['tipo'];
$tipoArray = explode("-",$tipoRev);
$tipo = $tipoArray[0];
$tipoDescrip = $tipoArray[1];

$posibleReversion = 0;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Nueva Reversiones Presentaciones S.S.S. :.</title>
<script src="/madera/lib/jquery.js" type="text/javascript"></script>
<script src="/madera/lib/jquery.maskedinput.js" type="text/javascript"></script>
<script src="/madera/lib/funcionControl.js" type="text/javascript"></script>
<script src="/madera/lib/jquery.blockUI.js" type="text/javascript"></script>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.reversiones.nueva.php?id=<?php echo $idPresentacion ?>'" /></p>
	 	<h2>Nueva Reversion para la Presentacion</h2>
	 	<?php include_once("include/detalle.php")?>
	 	<h3>Reversion Id Factura <span style="color: blue"><?php echo $idFactura ?></span> - Tipo <span style="color: blue"><?php echo $tipoDescrip ?></span></h3>
	 	<?php if ($posibleReversion == 1) { ?>
	 		<form action="presentacion.reversiones.vistaprevia.php?id=<?php echo $idPresentacion ?>" method="post" onSubmit="return validar()">
	 			<p><input type="submit" value="Guardar Reversion" style="margin-top: 15px"></input></p>
	 		</form>
	 	<?php } else { ?>
	 			<h3 style="color: red">No se encontro la informacion necesaria para realizar la reversion</br>Revise la informacion cargada</h3>
	 	<?php } ?>
	 </div>
</body>
</html>
