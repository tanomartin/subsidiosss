<?php include_once 'include/conector.php'; 

$informe = $_GET['informe'];
$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM intepresentacion p, interendicioncontrol s, intecronograma c 
							WHERE s.idpresentacion = p.id and p.idcronograma = c.id ORDER BY p.id DESC";
$resPresentacion = mysql_query($sqlPresentacion);
$arrayPresentacion = array();
while ($rowPresentacion = mysql_fetch_array($resPresentacion)) {
	$arrayPresentacion[$rowPresentacion['id']."-".$rowPresentacion['carpeta']] = $rowPresentacion['periodo'];
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Menú Seleccion Presentacion Informes S.S.S. :.</title>

<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="include/jquery.blockUI.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">

function redireccion(formulario) {
	//$.blockUI({ message: "<h1>Generando Informe... <br>Esto puede tardar unos minutos.<br> Aguarde por favor</h1>" });
	var tipo = formulario.tipo.value;
	var idCarpeta = formulario.id.options[formulario.id.selectedIndex].value;
	var datos = idCarpeta.split('-');

	var action = "";
	if (tipo == "detalle") {
		action = "informe.detalle.php?id="+datos[0]+"&carpeta="+datos[1];
	}
	if (tipo == "pagos") {
		action = "informe.pagos.php?id="+datos[0]+"&carpeta="+datos[1];
	}
	if (tipo == "sindicos") {
		action = "informe.sindico.php?id="+datos[0]+"&carpeta="+datos[1];
	}
	this.seleccionPresentacion.generar.disabled = true;
	this.seleccionPresentacion.action = action;
	this.seleccionPresentacion.submit();
}

</script>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input type="button" name="volver" value="Volver" onClick="location.href = 'informes.php'" /></p>
		<h2>Informes S.S.S.</h2>
		<h2>Seleccione Presentacion - Informe "<?php echo $informe?>"</h2>
		<form onsubmit="redireccion(this)" name="seleccionPresentacion" id="seleccionPresentacion" method="post" >
			<input type="text" value="<?php echo $informe?>" id="tipo" name="tipo" style="display: none"/>
			<select id="id" name="id">
			<?php foreach($arrayPresentacion as $id => $presentacion) { ?>
					<option value="<?php echo $id ?>"><?php echo $id." (".$presentacion.")" ?></option>
			<?php } ?>
			</select>
			<p><input type="submit" id="generar" name="generar" value="Generar Informe"/></p>
		</form>
	</div>
</body>
</head>
</html>