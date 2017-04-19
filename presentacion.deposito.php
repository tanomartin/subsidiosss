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

<script src="include/funcionControl.js" type="text/javascript"></script><base />
<script type="text/javascript">
	function validar(formulario) {
		var monto = formulario.monto.value;
		if (!esFechaValida(formulario.fecha.value)) {
			alert("La fecha de deposito no es valida");
			return false;
		}
		if(!isNumberPositivo(formulario.monto.value) || formulario.monto.value == "") {
			alert("El mongo depositado debe ser un nuero positivo");
			return false;
		}
		return true;
	}
</script>

<title>.: Deposito Subsidio S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Depósito Subsidio S.S.S.</h2>
	 	<h2>Detalle Presentacion</h2>
	 	<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	<form onSubmit="return validar(this)" action="presentacion.deposito.guardar.php" method="post">
 			<input style="display: none" type="text" name="id" id="id" value="<?php echo $rowPresentacion['id']?>" />
 			<h3>Cargar Datos del Deposito</h3>
 			<p><b>FECHA: </b> <input type="text" name="fecha" id="fecha" /></p>
 			<p><b>MONTO: </b> <input type="text" name="monto" id="monto" /></p>
 			<p><input type="submit" name="guardar"  value="Cargar Deposito"/></p>
 		</form>
	</div>
</body>
</html>