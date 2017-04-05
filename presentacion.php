<?php 
include_once 'include/conector.php';

$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.idcronograma = c.id ORDER BY p.id";
$resPresentacion = mysql_query($sqlPresentacion);
$canPresentacion = mysql_num_rows($resPresentacion);

$sqlPresentacionAbierta = "SELECT * FROM presentacion c WHERE fechasubsidio is null and fechacancelacion is null";
$resPresentacionAbierta = mysql_query($sqlPresentacionAbierta);
$canPresentacionAbierta = mysql_num_rows($resPresentacionAbierta);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Presentaciones S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'menu.php'" /></p>
	 	<h2>Presentaciones S.S.S.</h2>
  <?php if ($canPresentacionAbierta == 0) {?>
	 		<p><input type="button" name="nueva" value="Nueva Presentacion" onClick="location.href = 'presentacion.nueva.php'" /></p>
  <?php } 
        if ($canPresentacion > 0) {?>
		 <div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th>Id</th>
			 			<th>Periodo</th>
			 			<th>Carpeta</th>
			 			<th>Facturas</th>
			 			<th>Monto Comprobante</th>
			 			<th>Monto Pedido</th>
			 			<th>Acciones</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php while ($rowPresentacion = mysql_fetch_array($resPresentacion)) { ?>
					<tr>
						<td><?php echo $rowPresentacion['id'] ?></td>
						<td><?php echo $rowPresentacion['periodo'] ?></td>
						<td><?php echo $rowPresentacion['carpeta'] ?></td>
						<td><?php echo $rowPresentacion['cantfactura'] ?></td>
						<td><?php echo $rowPresentacion['sumimpcomprobante'] ?></td>
						<td><?php echo $rowPresentacion['sumimpsolicitado'] ?></td>
						<td>
							<input type="button" value="Detalle" onClick="location.href = 'presentacion.detalle.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
							<?php if ($rowPresentacion['fechacancelacion'] == NULL && $rowPresentacion['fechapresentacion'] == NULL) { ?>
								<input type="button" value="Generar Archivo" onClick="location.href = 'presentacion.generararchivo.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
								<input type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
							<?php } ?>
						</td>
					</tr>
			<?php } ?>
			  	</tbody>
			</table>
		</div>
  <?php } else { ?>
			<p style="color: blue"><b>NO HAY PRESENTACIONES HASTA EL MOMENTO</b></p>
  <?php } ?>
	</div>
</body>
</html>