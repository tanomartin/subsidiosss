
<?php if ($rowPresentacion['fechadevformato'] != null) {  ?>
	 		<h3>[DEV. FORMATO]</h3>
	 		<h3 style="color: blue">Aceptados: <?php echo $rowPresentacion['cantformatook']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesformatook'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadoformatook'],"2",",",".") ?></h3>
	 		<h3 style="color: red">Rechazados: <?php echo $rowPresentacion['cantformatonok']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesformatonok'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadoformatonok'],"2",",",".") ?></h3>
	 	<?php } ?>
	 	
	 	<?php if ($rowPresentacion['fechaintegral'] != null) {  ?>
	 		<h3>[DEV. INTEGRAL]</h3>
	 		<h3 style="color: blue">Aceptados: <?php echo $rowPresentacion['cantintegralok']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesintegralok'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadointegranlok'],"2",",",".") ?></h3>
	 		<h3 style="color: red">Rechazados: <?php echo $rowPresentacion['cantintegralnok']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesintegralnok'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadointegranlnok'],"2",",",".") ?></h3>
	 	<?php } ?>
	 	
	 	<?php if ($rowPresentacion['fechasubsidio'] != null) {  ?>
	 		<h3>[DEV. SUBSIDIO]</h3>
	 		<h3>Num. Liquidacion: <?php echo $rowPresentacion['numliquidacion'] ?></h3>
	 		<h3>Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadosubsidio'],"2",",",".") ?> - Imp. Subsidiado: <?php echo number_format($rowPresentacion['montosubsidio'],"2",",",".") ?></h3>
	 	<?php } ?>
	 	
		<?php if ($rowPresentacion['fechadeposito'] != null) {  ?>
	 		<h3>[INFO. DEPOSITO]</h3>
	 		<h3>Imp. Depositado: <?php echo number_format($rowPresentacion['montodepositado'],"2",",",".") ?></h3>
	 	<?php } ?>
