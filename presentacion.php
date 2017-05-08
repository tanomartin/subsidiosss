<?php 
include_once 'include/conector.php';

$sqlPresentacion = "SELECT 
						p.id,
						DATE_FORMAT(p.fechapresentacion, '%d-%m-%Y') as fechapresentacion,
						DATE_FORMAT(p.fechacancelacion, '%d-%m-%Y') as fechacancelacion,
						p.cantfactura, 
						p.impcomprobantes, 
						p.impsolicitado , 
						cronograma.periodo, 
						cronograma.carpeta,
						DATE_FORMAT(presentacionformato.fechadevformato, '%d-%m-%Y') as fechadevformato,
						presentacionformato.cantformatonok,
						DATE_FORMAT(presentacionintegral.fechaintegral, '%d-%m-%Y') as fechaintegral,
						presentacionintegral.cantintegralnok,
						DATE_FORMAT(presentacionsubsidio.fechasubsidio, '%d-%m-%Y') as fechasubsidio,
						DATE_FORMAT(p.fechadeposito, '%d-%m-%Y') as fechadeposito
					FROM presentacion p
          			INNER JOIN cronograma on p.idcronograma = cronograma.id
				  	LEFT JOIN presentacionformato on p.id = presentacionformato.id
          			LEFT JOIN presentacionintegral on p.id = presentacionintegral.id
          			LEFT JOIN presentacionsubsidio on p.id = presentacionsubsidio.id
					ORDER BY p.id DESC";
$resPresentacion = mysql_query($sqlPresentacion);
$canPresentacion = mysql_num_rows($resPresentacion);

$sqlAPresentar = "SELECT c.*,DATE_FORMAT(c.fechacierre,'%d/%m/%Y') as fechacierre 
					FROM cronograma c 
					WHERE fechacierre >=  CURDATE() LIMIT 1";
$resAPresentar = mysql_query($sqlAPresentar);
$rowAPresentar = mysql_fetch_array($resAPresentar);

$sqlPresentacionPeriodo = "SELECT *
							FROM presentacion p
							LEFT JOIN presentacionsubsidio on p.id = presentacionsubsidio.id
							WHERE p.fechacancelacion is null and presentacionsubsidio.fechasubsidio is null";
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
  <?php if ($canPresentacionPeriodo == 0) {?>
	 		<p><input type="button" name="nueva" value="Nueva Presentacion" onClick="location.href = 'presentacion.nueva.php'" /></p>
  <?php } 
        if ($canPresentacion > 0) {?>
		 <div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th style="font-size: 11px">Id</th>
			 			<th style="font-size: 11px">Periodo</th>
			 			<th style="font-size: 11px">Carpeta</th>
			 			<th style="font-size: 11px">Facturas</th>
			 			<th style="font-size: 11px" colspan="2">Credito</th>
			 			<th style="font-size: 11px">Fecha Presentacion</th>
			 			<th style="font-size: 11px">Fecha Cancelacion</th>
			 			<th style="font-size: 11px">Fecha Dev. Formato</th>
			 			<th style="font-size: 11px">Fecha Dev. Integral</th>
			 			<th style="font-size: 11px">Fecha Dev. Subsidio</th>
			 			<th style="font-size: 11px">Fecha Deposito</th>
			 			<th style="font-size: 11px">Informacion</th>
			 			<th style="font-size: 11px">Errores</th>
			 			<th style="font-size: 11px">Acciones</th>
			 		</tr>
			 		<tr>
			 			<th style="font-size: 11px" colspan="4"></th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px" colspan="9"></th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php while ($rowPresentacion = mysql_fetch_array($resPresentacion)) { ?>
					<tr>
						<td style="font-size: 11px"><?php echo $rowPresentacion['id'] ?></td>
						<td style="font-size: 11px"><?php echo $rowPresentacion['periodo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowPresentacion['carpeta'] ?></td>
						<td style="font-size: 11px"><?php echo $rowPresentacion['cantfactura'] ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantes'],2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitado'],2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo $rowPresentacion['fechapresentacion'] ?></td>
						<td style="font-size: 11px"><?php echo $rowPresentacion['fechacancelacion'] ?></td>
						<td style="font-size: 11px"><?php echo $rowPresentacion['fechadevformato'] ?></td>
						<td style="font-size: 11px"><?php echo $rowPresentacion['fechaintegral'] ?></td>
						<td style="font-size: 11px"><?php echo $rowPresentacion['fechasubsidio'] ?></td>
						<td style="font-size: 11px"><?php echo $rowPresentacion['fechadeposito'] ?></td>
						<td style="font-size: 11px">
							<input type="button" value="Facturas" onClick="location.href = 'presentacion.facturas.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
							<input type="button" value="Detalle" onClick="location.href = 'presentacion.detalle.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
						</td>
						<td style="font-size: 11px">
					<?php	if ($rowPresentacion['fechadevformato'] != NULL && $rowPresentacion['cantformatonok'] != 0) { ?>
								<input type="button" value="Err. Formato" onClick="location.href = 'presentacion.erroresformato.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					<?php   } ?>
					<?php	if ($rowPresentacion['fechaintegral'] != NULL && $rowPresentacion['cantintegralnok'] != 0) { ?>
								<input type="button" value="Err. Integral" onClick="location.href = 'presentacion.erroresintegral.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
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
												<input type="button" value="Formato" onClick="location.href = 'presentacion.devformato.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					  			  	  <?php } else { 
					  			  	  			if ($rowPresentacion['fechaintegral'] == NULL) { ?>
					  			  	  				<input type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
													<input type="button" value="Integral" onClick="location.href = 'presentacion.devintegral.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					  			  	  	  <?php } else { 	
				      								if ($rowPresentacion['fechasubsidio'] == NULL) { ?>
				      									<input type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
				      									<input type="button" value="Subsidio" onClick="location.href = 'presentacion.devsubsidio.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
				     			 		  	  <?php } else { 
				     			 		  				if ($rowPresentacion['fechadeposito'] == NULL) {?>
				     										<input type="button" value="Deposito" onClick="location.href = 'presentacion.deposito.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
				     							<?php 	} else {  ?>
				     										<font color="blue" size="2px">FINALIZADA</font>	
				     			 		  		<?php	} 
				     			 		 			}
					  			  	  	  		}
					  			  	  		}
					  			  	  	}	
				      				} else { ?>
				      					<font color="red" size="2px">CANCELADA</font>	
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