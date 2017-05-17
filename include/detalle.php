<?php $sqlPresentacion = "SELECT
p.id as idpres,
DATE_FORMAT(p.fechapresentacion, '%d-%m-%Y') as fechapresentacion,
DATE_FORMAT(p.fechacancelacion, '%d-%m-%Y') as fechacancelacion,
p.motivocancelacion,
p.cantfactura,
p.impcomprobantes,
p.impsolicitado,
p.impcomprobantesd,
p.impsolicitadod,
cronograma.periodo,
cronograma.carpeta,
DATE_FORMAT(presentacionformato.fechadevformato, '%d-%m-%Y') as fechadevformato,
presentacionformato.*,
DATE_FORMAT(presentacionintegral.fechaintegral, '%d-%m-%Y') as fechaintegral,
presentacionintegral.*,
DATE_FORMAT(presentacionsubsidio.fechasubsidio, '%d-%m-%Y') as fechasubsidio,
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
	 	<h3>ID: <?php echo $rowPresentacion['idpres']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	 	
	 	<?php if ($rowPresentacion['fechacancelacion'] != null) {  ?>
	 		<h3 style="color: red">PRESENTACION CANCELADA</h3>
	 		<p><b>MOTIVO: </b><?php echo $rowPresentacion['motivocancelacion'] ?></p>
	 	<?php } ?>

<div class="grilla">
	<table>
		<thead>
		  <tr>
		    <th colspan="4" style="font-size: 11px">Presentacion</th>
		    <th rowspan="2" style="font-size: 11px">Estado</th>
		    <th colspan="3" style="font-size: 11px">Dev. Formato</th>
		    <th colspan="3" style="font-size: 11px">Dev. Integral</th>
		    <th colspan="3" style="font-size: 11px">Dev. Subsidio</th>
		    <th style="font-size: 11px">Info. Deposito</th>
		  </tr>
		  <tr>	  
		  	<th style="font-size: 11px">Cantidad</th>
		    <th style="font-size: 11px">Tipo</th>
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
				<td rowspan="5" style="font-size: 11px"><?php echo number_format($rowPresentacion['cantfactura'],"0","",".") ?></td>
				<td rowspan="2" style="font-size: 11px">Credito</td>
				<td rowspan="2" style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantes'],"2",",",".") ?> </td>
			 	<td rowspan="2" style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitado'],"2",",",".") ?></td>
			
				<td rowspan="2" style="color: blue; font-size: 11px">OK</td>
				<td rowspan="2" style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['cantformatook'],"0","",".")?></td>	
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesformatook'],"2",",",".") ?> </td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadoformatook'],"2",",",".") ?></td>
				
				<td rowspan="2" style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['cantintegralok'],"0","",".")?></td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesintegralok'],"2",",",".") ?> </td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadointegralok'],"2",",",".") ?></td>
					
				<td rowspan="5" style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadosubsidio'],"2",",",".") ?></td>
				<td rowspan="5" style="font-size: 11px"><?php echo number_format($rowPresentacion['montosubsidio'],"2",",",".") ?></td>
				<td rowspan="5" style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadosubsidio']-$rowPresentacion['montosubsidio'],"2",",",".") ?></td>
					
				<td rowspan="5" style="font-size: 11px"> <?php echo number_format($rowPresentacion['montodepositado'],"2",",",".") ?> </td>
			</tr>
			<tr>
				<td style="color: blue; font-size: 11px"><?php echo "(".number_format($rowPresentacion['impcomprobantesformatodok'],"2",",",".").")" ?> </td>
				<td style="color: blue; font-size: 11px"><?php echo "(".number_format($rowPresentacion['impsolicitadoformatodok'],"2",",",".").")" ?></td>
				
				<td style="color: blue; font-size: 11px"><?php echo "(".number_format($rowPresentacion['impcomprobantesintegraldok'],"2",",",".").")" ?> </td>
				<td style="color: blue; font-size: 11px"><?php echo "(".number_format($rowPresentacion['impsolicitadointegraldok'],"2",",",".").")" ?></td>
			</tr>
			
			<tr>
				<td rowspan="2" style="font-size: 11px">Debito</td>
				<td rowspan="2" style="font-size: 11px"><?php echo "(".number_format($rowPresentacion['impcomprobantesd'],"2",",",".").")"; ?> </td>
				<td rowspan="2" style="font-size: 11px"><?php echo "(".number_format($rowPresentacion['impsolicitadod'],"2",",",".").")" ?></td>
				
				<td rowspan="2" style="color: red; font-size: 11px">RECH</td>
				<td rowspan="2" style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['cantformatonok'],"0","",".")?></td>
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesformatonok'],"2",",",".") ?> </td>
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadoformatonok'],"2",",",".") ?></td>
			
				<td rowspan="2" style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['cantintegralnok'],"0","",".")?></td>
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesintegralnok'],"2",",",".") ?> </td>
				<td style="color: red; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadointegralnok'],"2",",",".") ?></td>
			</tr>
			
			<tr>
				<td style="color: red; font-size: 11px"><?php echo "(".number_format($rowPresentacion['impcomprobantesformatodnok'],"2",",",".").")" ?> </td>
				<td style="color: red; font-size: 11px"><?php echo "(".number_format($rowPresentacion['impsolicitadoformatodnok'],"2",",",".").")" ?></td>
				
				<td style="color: red; font-size: 11px"><?php echo "(".number_format($rowPresentacion['impcomprobantesintegraldnok'],"2",",",".").")" ?> </td>
				<td style="color: red; font-size: 11px"><?php echo "(".number_format($rowPresentacion['impsolicitadointegraldnok'],"2",",",".").")" ?></td>
			</tr>	
			
			<tr>
				<td style="font-size: 11px">TOTAL</td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantes'] - $rowPresentacion['impcomprobantesd'],"2",",",".")?></td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitado'] - $rowPresentacion['impsolicitadod'],"2",",",".")?></td>
				
				<td style="font-size: 11px">TOTAL</td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['cantformatook'] + $rowPresentacion['cantformatonok'],"0","",".") ?></td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesformatook'] + $rowPresentacion['impcomprobantesformatonok'] - $rowPresentacion['impcomprobantesformatodok'] - $rowPresentacion['impcomprobantesformatodnok'],"2",",",".")  ?> </td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadoformatook'] + $rowPresentacion['impsolicitadoformatonok'] - $rowPresentacion['impsolicitadoformatodok'] - $rowPresentacion['impsolicitadoformatodnok'],"2",",",".") ?></td>
				
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['cantintegralok'] + $rowPresentacion['cantintegralnok'],"0","",".") ?></td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesintegralok'] + $rowPresentacion['impcomprobantesintegralnok'] - $rowPresentacion['impcomprobantesintegraldok'] - $rowPresentacion['impcomprobantesintegraldnok'],"2",",",".") ?> </td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadointegralok'] + $rowPresentacion['impsolicitadointegralnok'] - $rowPresentacion['impsolicitadointegraldok'] - $rowPresentacion['impsolicitadointegraldnok'],"2",",",".")  ?></td>
				
			</tr>
		</tbody>
	</table>
</div>