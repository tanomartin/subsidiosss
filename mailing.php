<?php include_once 'include/conector.php'; 

$canPrestadoresMailing = 0;
if (isset($_POST['filtro'])) {
	$cartel = "";
	$filtro = $_POST['filtro'];
	if ($filtro == 0) {
		$sqlPrestadoresMailing = "SELECT p.*, intemailing.asunto, intemailing.fecha
				FROM madera.prestadorservicio s, madera.prestadores p
				LEFT JOIN intemailing ON p.codigoprestador = intemailing.codigoprestador
				WHERE p.codigoprestador = s.codigoprestador and s.codigoservicio = 8 and (email1 is not null or email2 is not null)";
		$resPrestadoresMailing = mysql_query($sqlPrestadoresMailing);
		$canPrestadoresMailing = mysql_num_rows($resPrestadoresMailing);
		$cartel = "Listado Completo de Prestadores [$canPrestadoresMailing]";
	}
	if ($filtro == 1) {
		$sqlPrestadoresMailing = "SELECT p.*, count(f.cuit) as canRecibos, intemailing.asunto, intemailing.fecha
				FROM madera.prestadorservicio s, intepagosdetalle r, intepresentaciondetalle f, madera.prestadores p 
				LEFT JOIN intemailing ON p.codigoprestador = intemailing.codigoprestador
				WHERE (r.recibo is null or r.recibo = '') and
					  r.nrocominterno = f.nrocominterno and
					  f.cuit = p.cuit and
					  p.codigoprestador = s.codigoprestador and s.codigoservicio = 8 and
					  (email1 is not null or email2 is not null) 
				GROUP BY f.cuit";
		$resPrestadoresMailing = mysql_query($sqlPrestadoresMailing);
		$canPrestadoresMailing = mysql_num_rows($resPrestadoresMailing);
		$cartel = "Listado Prestadores Con deuda de Recibos [$canPrestadoresMailing]";
	}
	if ($filtro == 2) {
		$arrayDelega = explode("-",$_POST['delega']);
		$sqlPrestadoresMailing = "SELECT p.*, intemailing.asunto, intemailing.fecha
				FROM madera.prestadorservicio s, madera.prestadorjurisdiccion j, madera.delegaciones d, madera.prestadores p
				LEFT JOIN intemailing ON p.codigoprestador = intemailing.codigoprestador
				WHERE j.codidelega = ".$arrayDelega[0]." and 
					  j.codidelega = d.codidelega and
					  j.codigoprestador = p.codigoprestador and
					  p.codigoprestador = s.codigoprestador and s.codigoservicio = 8 and
					  (email1 is not null or email2 is not null)";
		$resPrestadoresMailing = mysql_query($sqlPrestadoresMailing);
		$canPrestadoresMailing = mysql_num_rows($resPrestadoresMailing);
		$cartel = "Listado Prestadores Por Delegacion ".$arrayDelega[1]." [$canPrestadoresMailing]";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet"href="include/jquery.tablesorter/themes/theme.blue.css" />
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script>
<script src="include/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {
	$("#listado")
	.tablesorter({
		theme: 'blue', 
		widthFixed: true, 
		widgets: ["zebra", "filter"],
		widgetOptions : { 
			filter_cssFilter   : '',
			filter_childRows   : false,
			filter_hideFilters : false,
			filter_ignoreCase  : true,
			filter_searchDelay : 300,
			filter_startsWith  : false,
			filter_hideFilters : false,	
		}		
	})
});

function validarBusqueda(formulario) {
	$.blockUI({ message: "<h1>Listando Prestadores por Filtro Seleccionado... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
}


function validar(formulario) {
	var grupo = formulario.seleccion;
	var total = grupo.length;
	var mensaje = "Debe seleccionar por lo menos un prestador para enviar";
	if (total == null) {
		if (!grupo.checked) {
			alert(mensaje);
			return false;
		}
	} else {
		var checkeados = 0; 
		for (var i = 0; i < total; i++) {
			if (grupo[i].checked) {
				checkeados++;
			}
		}
		if (checkeados == 0) {
			alert(mensaje);
			return false;
		}
	}
	formulario.submit.disabled = "true";
	formulario.selecAll.disabled = "true";
	return true;
}

function checkall(seleccion, formulario) {
 	var grupo = formulario.seleccion;
	var total = grupo.length;
	if (total == null) {
		if (seleccion.checked) {
			grupo.checked = 1;
		} else {
			grupo.checked = 0;
		}
	}
	if (seleccion.checked) {
		 for (var i=0;i< grupo.length;i++) 
			 if(grupo[i].type == "checkbox")	
				 grupo[i].checked=1;  
	} else {
		 for (var i=0;i<grupo.length;i++) 
			 if(grupo[i].type == "checkbox")	
				 grupo[i].checked=0;  
	}
} 

function habilitarDelega(vista) {
	document.getElementById("delega").style.display = "none"
	if (vista == 1) {
		document.getElementById("delega").style.display = "block"
	}
}

</script>
<title>.: Mailing S.S.S. :.</title>
</head>
<body bgcolor="#CCCCCC">
<div align="center">
	 <p><input type="reset" name="volver" value="Volver" onClick="location.href = 'menu.php'" /></p>
	 <h2>Mailing Prestadores Integración</h2>
	 <?php if (isset($_GET['envio'])) { ?><h3 style="color: blue">Se guardaron en cola de envio los correos a enviar</h3> <?php } ?>
	 <h3>Filtros de busqueda</h3>
	 <form id="buscador" name="buscador" action="mailing.php" onsubmit="return validarBusqueda(this)" method="post">
	 	<table>
	 		<tr>
	 			<td><input type="radio" name="filtro" value="0" checked="checked" onclick="habilitarDelega(0);"></td> 
	 			<td><b>Todos</b></td>
	 		</tr>
	 		<tr>
  				<td><input type="radio" name="filtro" value="1" onclick="habilitarDelega(0);"></td> 
  				<td><b>Deudares Recibos</b></td>
  			</tr>
  			<tr>
 				<td><input type="radio" name="filtro" value="2" onclick="habilitarDelega(1);"></td>  
 				<td><b>Por delegacion </b></td>
 			</tr>
 		</table>
 		<p><select name="delega" id="delega" style="display: none">
	 		<?php $sqlDelega = "SELECT * FROM madera.delegaciones WHERE codidelega not in (1000,1001,3200,3500,4000,4001)";
	 			  $resDelega = mysql_query($sqlDelega);
	 			  while ($rowDelega = mysql_fetch_assoc($resDelega)) { ?>
	 				<option value="<?php echo $rowDelega['codidelega']."-".$rowDelega['nombre']?>"><?php echo $rowDelega['nombre'] ?></option>
	 		<?php } ?>
	 	</select></p>
	 	<p><input type="submit" name="submit" value="Listar Prestadores"/></p>
	 </form>
<?php if (isset($_POST['filtro'])) { ?>
		 <h3><?php echo $cartel ?></h3>
<?php	 if ($canPrestadoresMailing > 0) {?>
		 <form id="form1" name="form1" action="mailing.redaccion.php" onsubmit="return validar(this)" method="post">
			
			 <table class="tablesorter" id="listado" style="width: 1200px">
				 <thead>
				 	<tr>
				 		<th>Código</th>
				 		<th>Razón Social</th>
				 		<th>Email</th>
				 		<th width="30%">Asunto</th>
				 		<th width="10%">Fecha</th>
				 		<?php if ($filtro == 1) { ?><th># Recibos</th><?php } ?>
				 		<th><input type="checkbox" name="selecAll" id="selecAll" onchange="checkall(this, this.form)" /></th>
				 	</tr>
				 </thead>
				 <tbody>
				 <?php while ($rowPrestadoresMailing = mysql_fetch_array($resPrestadoresMailing)) { ?>
				 	<tr>
						<td><?php echo $rowPrestadoresMailing['codigoprestador'] ?></td>
						<td><?php echo $rowPrestadoresMailing['nombre'] ?></td>
						<?php 	$email = $rowPrestadoresMailing['email1'];
								if ($email == NULL) { $email = $rowPrestadoresMailing['email2']; } ?>
						<td><?php echo $email ?></td>
						<td><?php echo $rowPrestadoresMailing['asunto'] ?></td>
						<td><?php echo $rowPrestadoresMailing['fecha'] ?></td>
						<?php if ($filtro == 1) { ?> <td><?php echo $rowPrestadoresMailing['canRecibos'] ?></td> <?php } ?>
						<td><input type="checkbox" name="<?php echo $rowPrestadoresMailing['codigoprestador'] ?>" id="seleccion" value="<?php echo $email ?>" /></td>
					</tr>
				 <?php } ?>
				</tbody>
			</table>
			<p><input type="submit" name="submit" value="Seleccionar Prestadores"/></p>
		  </form>
	<?php } else { ?>
	 		<h3 style="color: blue">No existe Prestadores con el filtro seleccionado</h3>
	<?php }?>
<?php }?>
</div>
</body>