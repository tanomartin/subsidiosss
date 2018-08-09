<?php include_once 'include/conector.php'; 
$listadoSerializado = serialize($_POST);
$listadoSerializado = urlencode($listadoSerializado); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="include/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript">

function validar(formulario) {
	if (formulario.asunto.value == "") {
		alert("Debe ingresar un asunto");
		return false;
	}
	if (formulario.mensaje.value == "") {
		alert("Debe ingresar un mensaje");
		return false;
	}
	$.blockUI({ message: "<h1>Guardando Correos para enviar... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
	return true;
}


</script>

<title>.: Mailing Redaccion S.S.S. :.</title>
</head>
<body bgcolor="#CCCCCC">
<div align="center">
	<p><input type="reset" name="volver" value="Volver" onClick="location.href = 'mailing.php'" /></p>
	<h2>Mailing Redaccion</h2>
	<form id="formRedaccion" name="formRedaccion" action="mailing.guardar.php" onsubmit="return validar(this)" method="post">
		<input name="empresas" style="display: none" value="<?php echo $listadoSerializado ?>"/>
		<p><b>ASUNTO </b><input type="text" id="asunto" name="asunto" size="120"/></p>
		<p><b>MENSAJE</b></p>
		<p><textarea rows="20" cols="150" id="mensaje" name="mensaje"></textarea></p>
		<p><input type="submit" name="submit" value="Enviar Correos"/></p>
	</form>
</div>
</body>