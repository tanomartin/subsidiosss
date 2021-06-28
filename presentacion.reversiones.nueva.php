<?php 
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];
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
<script type="text/javascript">

jQuery(function($){
	$("#nrocominterno").mask("999999");
});

function validar(formulario) {
	if (formulario.nrocominterno.value == '') {
		alert("Debe ingresar el id de Factura a realizar la reversion");
		return false;
	}
	if (formulario.tipo.value == 0) {
		alert("Debe ingresar el tipo de reversion a realizar");
		return false;
	}
	formulario.vista.disbled = true;
	return true;
}

</script>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.reversiones.php?id=<?php echo $idPresentacion ?>'" /></p>
	 	<h2>Nueva Reversion para la Presentacion</h2>
	 	<?php include_once("include/detalle.php")?>
	 	<form action="presentacion.reversiones.vistaprevia.php?id=<?php echo $idPresentacion ?>" method="post" onSubmit="return validar(this)">
	 		<p><b>Id Factura: </b><input style="text-align: center;" type="text" name="nrocominterno" id="nrocominterno" maxlength="6" size="6"/></p>
	 		<p><b>Tipo de Reversion: </b>
	 			<select name="tipo" id="tipo">
	 				<option value='0' selected="selected">Seleccione tipo de Reversion</option>
	 				<option value='1-SOLO DB'>SOLO DB</option>
	 				<option value='2-DB/DC'>DB/DC</option>
	 				<option value='3-DB/DS (misma presentacion)'>DB/DS (misma presentacion)</option>
	 				<option value='4-DB-DS (dos presentaciones)'>DB-DS (dos presentaciones)</option>
	 			</select>
	 		</p>
	 		<p><input type="submit" value="Vista Previa" id="vista" style="margin-top: 15px"></input></p>
	 	</form>
	 </div>
</body>
</html>
