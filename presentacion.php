<?php 
include_once 'include/conector.php';

$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta, 
						DATE_FORMAT(p.fechapresentacion, '%d-%m-%Y') as fechapresentacion, 
						DATE_FORMAT(p.fechacancelacion, '%d-%m-%Y') as fechacancelacion,
						DATE_FORMAT(p.fechasubsidio, '%d-%m-%Y') as fechasubsidio,
						DATE_FORMAT(p.fechadeposito, '%d-%m-%Y') as fechadeposito
					FROM presentacion p, cronograma c WHERE p.idcronograma = c.id ORDER BY p.id";
$resPresentacion = mysql_query($sqlPresentacion);
$canPresentacion = mysql_num_rows($resPresentacion);

$sqlAPresentar = "SELECT c.*,DATE_FORMAT(c.fechacierre,'%d/%m/%Y') as fechacierre FROM cronograma c WHERE fechacierre >=  CURDATE() LIMIT 1";
$resAPresentar = mysql_query($sqlAPresentar);
$rowAPresentar = mysql_fetch_array($resAPresentar);

$sqlPresentacionPeriodo = "SELECT * FROM presentacion c WHERE (fechasubsidio is null and fechacancelacion is null) or (fechasubsidio is not null and idcronograma = ".$rowAPresentar['id'].")";
$resPresentacionPeriodo  = mysql_query($sqlPresentacionPeriodo);
$canPresentacionPeriodo = mysql_num_rows($resPresentacionPeriodo);

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
	 	<table border="1" style="text-align: center; margin-bottom: 15px">
	 		<tr><td><p><b>Periodo:</b> <?php echo $rowAPresentar['periodo']?></p></td></tr>
	 		<tr><td><p><b>Carpeta:</b> <?php echo $rowAPresentar['carpeta']?></p></td></tr>
	 		<tr><td><p><b>Fecha de Cierre:</b> <?php echo $rowAPresentar['fechacierre']?></p></td></tr>
	 		<tr><td><p><b>Periodos Incluidos:</b> <?php echo $rowAPresentar['periodosincluidos']?></p></td></tr>
	 	</table>
  <?php if ($canPresentacionPeriodo == 0) {?>
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
			 			<th>Fecha Presentacion</th>
			 			<th>Fecha Cancelacion</th>
			 			<th>Fecha Dev. Formato</th>
			 			<th>Fecha Dev. Integral</th>
			 			<th>Fecha Dev. Subsidio</th>
			 			<th>Fecha Deposito</th>
			 			<th>Informacion</th>
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
						<td><?php echo number_format($rowPresentacion['impcomprobantes'],2,",",".") ?></td>
						<td><?php echo number_format($rowPresentacion['impsolicitado'],2,",",".") ?></td>
						<td><?php echo $rowPresentacion['fechapresentacion'] ?></td>
						<td><?php echo $rowPresentacion['fechacancelacion'] ?></td>
						<td><?php echo $rowPresentacion['fechadevformato'] ?></td>
						<td><?php echo $rowPresentacion['fechaintegral'] ?></td>
						<td><?php echo $rowPresentacion['fechasubsidio'] ?></td>
						<td><?php echo $rowPresentacion['fechadeposito'] ?></td>
						<td>
							<input type="button" value="Facturas" onClick="location.href = 'presentacion.facturas.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
							<input type="button" value="Detalle Presentacion" onClick="location.href = 'presentacion.detalle.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					<?php	if ($rowPresentacion['fechadevformato'] != NULL) { ?>
								<input type="button" value="Errores Formato" onClick="location.href = 'presentacion.erroresformato.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					<?php   } ?>
					<?php	if ($rowPresentacion['fechaintegral'] != NULL) { ?>
								<input type="button" value="Errores Integral" onClick="location.href = 'presentacion.erroresintegral.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					<?php   } ?>
						</td>
						<td>
				    		 <?php 	if ($rowPresentacion['fechacancelacion'] == NULL) { 
				    					if ($rowPresentacion['fechapresentacion'] == NULL) { ?>
											<input type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
											<input type="button" value="Generar Archivo" onClick="location.href = 'presentacion.archivo.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					  		   	  <?php } else { 
											if ($rowPresentacion['fechadevformato'] == NULL) { ?>
												<input type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
												<input type="button" value="Dev. Formato" onClick="location.href = 'presentacion.devformato.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					  			  	  <?php } else { 
					  			  	  			if ($rowPresentacion['fechaintegral'] == NULL) { ?>
					  			  	  				<input type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
													<input type="button" value="Dev. Integral" onClick="location.href = 'presentacion.devintegral.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					  			  	  	  <?php } else { 	
				      								if ($rowPresentacion['fechasubsidio'] == NULL) { ?>
				      									<input type="button" value="Dev. Subsidio" onClick="location.href = 'presentacion.devsubsidio.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
				     			 		  	  <?php } else { 
				     			 		  				if ($rowPresentacion['fechadeposito'] == NULL) {?>
				     										<input type="button" value="Info. Deposito" onClick="location.href = 'presentacion.deposito.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
				     							<?php 	} else {  ?>
				     										<font color="blue">FINALIZADA</font>	
				     			 		  		<?php	} 
				     			 		 			}
					  			  	  	  		}
					  			  	  		}
					  			  	  	}	
				      				} else { ?>
				      					<font color="red">CANCELADA</font>	
				     			<?php  } ?>
						</td>
					</tr>
			 <?php 	}  ?>
			  	</tbody>
			</table>
		</div>
  <?php } else { ?>
			<p style="color: blue"><b>NO HAY PRESENTACIONES HASTA EL MOMENTO</b></p>
  <?php } ?>
	</div>
</body>
</html>