<?php $sqlPresentacion = "SELECT
p.id,
DATE_FORMAT(p.fechapresentacion, '%d-%m-%Y') as fechapresentacion,
DATE_FORMAT(p.fechacancelacion, '%d-%m-%Y') as fechacancelacion,
p.motivocancelacion,
p.cantfactura,
p.impcomprobantes,
p.impsolicitado ,
cronograma.periodo,
cronograma.carpeta,
DATE_FORMAT(presentacionformato.fechadevformato, '%d-%m-%Y') as fechadevformato,
presentacionformato.cantformatook,
presentacionformato.impcomprobantesformatook,
presentacionformato.impsolicitadoformatook,
presentacionformato.cantformatonok,
presentacionformato.impcomprobantesformatonok,
presentacionformato.impsolicitadoformatonok,
DATE_FORMAT(presentacionintegral.fechaintegral, '%d-%m-%Y') as fechaintegral,
presentacionintegral.cantintegralok,
presentacionintegral.impcomprobantesintegralok,
presentacionintegral.impsolicitadointegranlok,
presentacionintegral.cantintegralnok,
presentacionintegral.impcomprobantesintegralnok,
presentacionintegral.impsolicitadointegranlnok,
DATE_FORMAT(presentacionsubsidio.fechasubsidio, '%d-%m-%Y') as fechasubsidio,
presentacionsubsidio.numliquidacion,
presentacionsubsidio.impsolicitadosubsidio,
presentacionsubsidio.montosubsidio,
DATE_FORMAT(p.fechadeposito, '%d-%m-%Y') as fechadeposito,
p.montodepositado
FROM presentacion p
INNER JOIN cronograma on p.idcronograma = cronograma.id
LEFT JOIN presentacionformato on p.id = presentacionformato.id
LEFT JOIN presentacionintegral on p.id = presentacionintegral.id
LEFT JOIN presentacionsubsidio on p.id = presentacionsubsidio.id
WHERE p.id = $idPresentacion";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);
?>

	<h2>Detalle Presentacion</h2>
	 	<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	 	
	 	<?php if ($rowPresentacion['fechacancelacion'] != null) {  ?>
	 		<h3 style="color: red">PRESENTACION CANCELADA</h3>
	 		<p><b>MOTIVO: </b><?php echo $rowPresentacion['motivocancelacion'] ?></p>
	 	<?php } ?>

<div class="grilla">
	<table>
		<thead>
		  <tr>
		    <th colspan="3" style="font-size: 11px">Presentacion</th>
		    <th rowspan="2" style="font-size: 11px"></th>
		    <th colspan="3" style="font-size: 11px">Dev. Formato</th>
		    <th colspan="3" style="font-size: 11px">Dev. Integral</th>
		    <th colspan="3" style="font-size: 11px">Dev. Subsidio</th>
		    <th style="font-size: 11px">Info. Deposito</th>
		  </tr>
		  <tr>	  
		  	<th style="font-size: 11px">Cantidad</th>
		    <th style="font-size: 11px">$ Comprobante</th>
		    <th style="font-size: 11px">$ Solicitado</th>
		    <th style="font-size: 11px">Cantidad</th>
		    <th style="font-size: 11px">$ Comprobante</th>
		    <th style="font-size: 11px">$ Solicitado</th>
		    <th style="font-size: 11px">Cantidad</th>
		    <th style="font-size: 11px">$ Comprobante</th>
		    <th style="font-size: 11px">$ Solicitado</th>
		    <th style="font-size: 11px">$ Solicitado</th>
		    <th style="font-size: 11px">$ Subsidiado</th>
		    <th style="font-size: 11px">$ Dif.</th>
		    <th style="font-size: 11px">$ Monto</th>
		  </tr>
		</thead>
		<tbody>
			<tr>
				
				<td rowspan="3" style="font-size: 11px"><?php echo number_format($rowPresentacion['cantfactura'],"0","",".") ?></td>
				<td rowspan="3" style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantes'],"2",",",".") ?> </td>
				<td rowspan="3" style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitado'],"2",",",".") ?></td>
				
				<td style="color: blue; font-size: 11px">OK</td>
				
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['cantformatook'],"0","",".")?></td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesformatook'],"2",",",".") ?> </td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadoformatook'],"2",",",".") ?></td>
				
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['cantintegralok'],"0","",".")?></td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesintegralok'],"2",",",".") ?> </td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadointegranlok'],"2",",",".") ?></td>
				
				<td rowspan="3" style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadosubsidio'],"2",",",".") ?></td>
				<td rowspan="3" style="font-size: 11px"><?php echo number_format($rowPresentacion['montosubsidio'],"2",",",".") ?></td>
				<td rowspan="3" style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadosubsidio']-$rowPresentacion['montosubsidio'],"2",",",".") ?></td>
				
				<td rowspan="3" style="font-size: 11px"> <?php echo number_format($rowPresentacion['montodepositado'],"2",",",".") ?> </td>
			</tr>
			<tr>
				<td style="color: red; font-size: 11px">RECH</td>
				
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['cantformatonok'],"0","",".")?></td>
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesformatonok'],"2",",",".") ?> </td>
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadoformatonok'],"2",",",".") ?></td>
				
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['cantintegralnok'],"0","",".")?></td>
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesintegralnok'],"2",",",".") ?> </td>
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadointegranlnok'],"2",",",".") ?></td>

			</tr>
			<tr>
				<td style="font-size: 11px">TOTAL</td>
				
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['cantformatook'] + $rowPresentacion['cantformatonok'],"0","",".") ?></td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesformatook'] + $rowPresentacion['impcomprobantesformatonok'],"2",",",".") ?> </td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadoformatook'] + $rowPresentacion['impsolicitadoformatonok'],"2",",",".") ?></td>
				
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['cantintegralok'] + $rowPresentacion['cantintegralnok'],"0","",".") ?></td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesintegralok'] + $rowPresentacion['impcomprobantesintegralnok'],"2",",",".") ?> </td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadointegranlok'] + $rowPresentacion['impsolicitadointegranlnok'],"2",",",".") ?></td>
			</tr>
		</tbody>
	</table>
</div>

