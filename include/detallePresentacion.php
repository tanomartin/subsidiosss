	<h2>Detalle Presentacion</h2>
	 	<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	 	
	 	<?php if ($rowPresentacion['fechacancelacion'] != null) {  ?>
	 		<h3 style="color: red">PRESENTACION CANCELADA</h3>
	 		<p><b>MOTIVO: </b><?php echo $rowPresentacion['motivocancelacion'] ?></p>
	 	<?php } ?>
	 	
	 	<h3>[PRESENTACION]</h3><h3>Cantidad: <?php echo $rowPresentacion['cantfactura']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantes'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitado'],"2",",",".") ?></h3>