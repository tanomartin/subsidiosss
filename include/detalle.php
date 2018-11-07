<?php $sqlPresentacion = "SELECT
p.id as idpres,
DATE_FORMAT(p.fechapresentacion, '%d-%m-%Y') as fechapresentacion,
DATE_FORMAT(p.fechacancelacion, '%d-%m-%Y') as fechacancelacion,
p.motivocancelacion,
p.cantfactura,
p.impcomprobantes,
p.impdebito,
p.impnointe,
p.impsolicitado,
p.impcomprobantesd,
p.impdebitod,
p.impnointed,
p.impsolicitadod,
intecronograma.periodo,
intecronograma.carpeta,
DATE_FORMAT(intepresentacionformato.fechadevformato, '%d-%m-%Y') as fechadevformato,
intepresentacionformato.*,
DATE_FORMAT(intepresentacionintegral.fechaintegral, '%d-%m-%Y') as fechaintegral,
intepresentacionintegral.*,
DATE_FORMAT(interendicioncontrol.fecharendicion, '%d-%m-%Y') as fecharendicion,
interendicioncontrol.importesolicitado,
interendicioncontrol.importeliquidado,
DATE_FORMAT(p.fechadeposito, '%d-%m-%Y') as fechadeposito,
p.montodepositado
FROM intepresentacion p
INNER JOIN intecronograma on p.idcronograma = intecronograma.id
LEFT JOIN intepresentacionformato on p.id = intepresentacionformato.id
LEFT JOIN intepresentacionintegral on p.id = intepresentacionintegral.id
LEFT JOIN interendicioncontrol on p.id = interendicioncontrol.idpresentacion
WHERE p.id = $idPresentacion";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion); ?>

	 	<h3>ID: <?php echo $rowPresentacion['idpres']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	 	
	 	<?php if ($rowPresentacion['fechacancelacion'] != null) {  ?>
	 		<h3 style="color: red">PRESENTACION CANCELADA</h3>
	 		<p><b>MOTIVO: </b><?php echo $rowPresentacion['motivocancelacion'] ?></p>
	 	<?php } ?>

<div class="grilla" style="width: 100%">
	<table>
		<thead>
		  <tr>
		    <th colspan="6" style="font-size: 11px">Presentacion</th>
		    <th rowspan="2" style="font-size: 11px">Estado</th>
		    <th colspan="3" style="font-size: 11px">Dev. Formato</th>
		    <th colspan="3" style="font-size: 11px">Dev. Integral</th>
		    <th colspan="3" style="font-size: 11px">Dev. Rendicion</th>
		    <th style="font-size: 11px">Info. Deposito</th>
		  </tr>
		  <tr>	  
		  	<th style="font-size: 11px">Cant.</th>
		    <th style="font-size: 11px">Tipo</th>
		    <th style="font-size: 11px">$ Comp.</th>
		    <th style="font-size: 11px">$ Deb.</th>
		    <th style="font-size: 11px">$ No Int.</th>
		    <th style="font-size: 11px">$ Soli.</th>
		    <th style="font-size: 11px">Cant.</th>
		    <th style="font-size: 11px">$ Comp.</th>
		    <th style="font-size: 11px">$ Soli.</th>
		    <th style="font-size: 11px">Cant.</th>
		    <th style="font-size: 11px">$ Com.</th>
		    <th style="font-size: 11px">$ Soli.</th>
		   
		    <th style="font-size: 11px">$ Soli,</th>
		    <th style="font-size: 11px">$ Subs.</th>
		    <th style="font-size: 11px">$ Tr. O.S.</th>
		   
		    <th style="font-size: 11px">$ Monto</th>
		  </tr>
		</thead>
		<tbody>
			<tr>
				<td rowspan="5" style="font-size: 11px"><?php echo number_format($rowPresentacion['cantfactura'],"0","",".") ?></td>
				<td rowspan="2" style="font-size: 11px">Credito</td>
				<td rowspan="2" style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantes'],"2",",",".") ?> </td>
				<td rowspan="2" style="font-size: 11px"><?php echo number_format($rowPresentacion['impdebito'],"2",",",".") ?> </td>
				<td rowspan="2" style="font-size: 11px"><?php echo number_format($rowPresentacion['impnointe'],"2",",",".") ?> </td>
			 	<td rowspan="2" style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitado'],"2",",",".") ?></td>
			
				<td rowspan="2" style="color: blue; font-size: 11px">OK</td>
				<td rowspan="2" style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['cantformatook'],"0","",".")?></td>	
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesformatook'],"2",",",".") ?> </td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadoformatook'],"2",",",".") ?></td>
				
				<td rowspan="2" style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['cantintegralok'],"0","",".")?></td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesintegralok'],"2",",",".") ?> </td>
				<td style="color: blue; font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadointegralok'],"2",",",".") ?></td>
				
				<?php 
					$comIntetralTotal = $rowPresentacion['impcomprobantesintegralok'] + $rowPresentacion['impcomprobantesintegralnok'] - $rowPresentacion['impcomprobantesintegraldok'] - $rowPresentacion['impcomprobantesintegraldnok']; 
					$soliIntegralTotal = $rowPresentacion['impsolicitadointegralok'] + $rowPresentacion['impsolicitadointegralnok'] - $rowPresentacion['impsolicitadointegraldok'] - $rowPresentacion['impsolicitadointegraldnok'];	
				?>
				
				<td rowspan="4" style="font-size: 11px"><?php echo number_format($rowPresentacion['importesolicitado'],"2",",",".") ?></td>
				<?php if ($rowPresentacion['importeliquidado'] != 0) { $porcentaje = ( $rowPresentacion['importeliquidado']/$rowPresentacion['importesolicitado']) * 100; } else { $porcentaje = "0"; };?>
				<td rowspan="5" style="font-size: 11px"><?php echo (number_format($rowPresentacion['importeliquidado'],"2",",",".")."<br>".number_format($porcentaje,"2",",",".")." %"); ?></td>
				<?php if ($rowPresentacion['importeliquidado'] != 0) { $porcentaje = ( ($rowPresentacion['importesolicitado']-$rowPresentacion['importeliquidado'])/$rowPresentacion['importesolicitado']) * 100; } else { $porcentaje = "0"; };?>
				<td rowspan="5" style="font-size: 11px"><?php echo (number_format($rowPresentacion['importesolicitado']-$rowPresentacion['importeliquidado'],"2",",",".")."<br>".number_format($porcentaje,"2",",",".")." %"); ?></td>
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
				<td rowspan="2" style="font-size: 11px"><?php echo "(".number_format($rowPresentacion['impdebitod'],"2",",",".").")"; ?> </td>
				<td rowspan="2" style="font-size: 11px"><?php echo "(".number_format($rowPresentacion['impnointed'],"2",",",".").")"; ?> </td>			
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
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impdebito'] - $rowPresentacion['impdebitod'],"2",",",".")?></td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impnointe'] - $rowPresentacion['impnointed'],"2",",",".")?></td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitado'] - $rowPresentacion['impsolicitadod'],"2",",",".")?></td>
				
				<td style="font-size: 11px">TOTAL</td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['cantformatook'] + $rowPresentacion['cantformatonok'],"0","",".") ?></td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impcomprobantesformatook'] + $rowPresentacion['impcomprobantesformatonok'] - $rowPresentacion['impcomprobantesformatodok'] - $rowPresentacion['impcomprobantesformatodnok'],"2",",",".")  ?> </td>
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['impsolicitadoformatook'] + $rowPresentacion['impsolicitadoformatonok'] - $rowPresentacion['impsolicitadoformatodok'] - $rowPresentacion['impsolicitadoformatodnok'],"2",",",".") ?></td>
				
				<td style="font-size: 11px"><?php echo number_format($rowPresentacion['cantintegralok'] + $rowPresentacion['cantintegralnok'],"0","",".") ?></td>
				<td style="font-size: 11px"><?php echo number_format($comIntetralTotal,"2",",",".") ?> </td>
				<td style="font-size: 11px"><?php echo number_format($soliIntegralTotal,"2",",",".") ?></td>
				
				<?php $controlRendiSoli = $rowPresentacion['impsolicitadointegralok'] - $rowPresentacion['impsolicitadointegraldok'] - $rowPresentacion['importesolicitado'] ?>
				<td style="font-size: 11px"><?php echo "DIF: ".number_format($controlRendiSoli,"2",",",".") ?></td>
			</tr>
		</tbody>
	</table>
</div>