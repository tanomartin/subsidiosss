<?php 
include_once 'include/conector.php';
set_time_limit(0);
$idPresentacion = $_GET['id'];
$carpeta = $_GET['carpeta'];
$sqlFactura = "SELECT intepresentaciondetalle.*, titulares.codidelega as deletitu, titufami.codidelega as delefami, prestadoresauxiliar.cbu,
CASE
  WHEN (prestadores.situacionfiscal in (0,1,4) || (prestadores.situacionfiscal = 3 and prestadores.vtoexento >= CURDATE())) THEN 0
  WHEN (prestadores.situacionfiscal = 2 || (prestadores.situacionfiscal = 3 and prestadores.vtoexento < CURDATE())) THEN 1
END as retiene
FROM intepresentaciondetalle
LEFT JOIN titulares on  intepresentaciondetalle.cuil = titulares.cuil
LEFT JOIN familiares on  intepresentaciondetalle.cuil = familiares.cuil
LEFT JOIN titulares titufami on  familiares.nroafiliado = titufami.nroafiliado
LEFT JOIN prestadores on intepresentaciondetalle.cuit = prestadores.cuit
LEFT JOIN prestadoresauxiliar on prestadores.codigoprestador = prestadoresauxiliar.codigoprestador
WHERE idpresentacion = $idPresentacion order by cuil, periodo, codpractica";
$resFactura = mysql_query($sqlFactura);

$file= $carpeta."-".$idPresentacion.".xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");

?>
<body>
	<div align="center"> 	
	 	<?php include_once("include/detalle.php")?>
	 	<h2>Facturas</h2>
			 <table border="1">
			 	<thead>
			 		<tr>
			 			<th rowspan="2">Comp. Interno</th>
			 			<th rowspan="2">Tipo</th>
			 			<th rowspan="2">C.U.I.L.</th>
			 			<th rowspan="2">Dele</th>
			 			<th rowspan="2">Periodo</th>
			 			<th rowspan="2">C.U.I.T.</th>
			 			<th rowspan="2">C.B.U.</th>
			 			<th rowspan="2">C.A.E.</th>
			 			<th rowspan="2">Fec. Comp.</th>
			 			<th rowspan="2">Num. Comp.</th>
			 			<th rowspan="2">Cod. Prac.</th>
			 			<th rowspan="2">$ Comp.</th>
			 			<th rowspan="2">$ Soli.</th>
			 			<th colspan="2">Resultado Formato</th>
			 			<th colspan="2">Resultado Integral</th>
			 			<th colspan="4">Resultado Subsidio</th>
			 			<th rowspan="2">Ret.</th>
			 			<th rowspan="2">Cant. Rec. Debe</th>
			 		</tr>
			 		<tr>
			 			<th>Comp.</th>
			 			<th>Soli.</th>
			 			<th>Comp.</th>
			 			<th>Soli.</th>
			 			
			 			<th>Soli.</th>
			 			<th>Subs.</th>
			 			<th>Tr O.S.</th>
			 			<th>Debito / Pago O.S.</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php 
				$totCom = 0;
				$totSol = 0;
				$totComFor = 0;
				$totSolFor = 0;
				$totComInt = 0;
				$totSolInt = 0;
				$totSolSub = 0;
				$totMonSub = 0;
				$totMonOsp = 0;
				$totMonChOsp = 0;
				while ($rowFactura = mysql_fetch_array($resFactura)) {  ?>
					<tr>
						<td><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td><?php echo $rowFactura['cuil'] ?></td>
						<td><?php echo $rowFactura['deletitu']." ".$rowFactura['delefami'] ?></td>
						<td><?php echo $rowFactura['periodo'] ?></td>
						<td><?php echo $rowFactura['cuit'] ?></td>
						<td><?php echo "'".$rowFactura['cbu']."'" ?></td>
						<td><?php echo "'".$rowFactura['cae']."'" ?></td>
						<td><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td><?php echo $rowFactura['codpractica'] ?></td>
				 
				  <?php if ($rowFactura['tipoarchivo'] == 'DB') { 
				  			$totCom -= $rowFactura['impcomprobante'];
				  			$totSol -= $rowFactura['impsolicitado']; ?>
						    <td><?php echo "(".number_format($rowFactura['impcomprobante'],2,",",".").")" ?></td>
						    <td><?php echo "(".number_format($rowFactura['impsolicitado'],2,",",".").")" ?></td>
				   <?php } else { 
							$totCom += $rowFactura['impcomprobante'];
				  			$totSol += $rowFactura['impsolicitado']; ?>
						    <td><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						    <td><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
					<?php } 
					   
						  if ($rowPresentacion['fechadevformato'] != null) {			
							  if ($rowFactura['deverrorformato'] != null ) { ?>
								<td colspan="2" style="font-size: 11px; color: red"><?php  echo "ERROR: ".$rowFactura['deverrorformato'] ?></td>
						<?php } else { 
									if ($rowFactura['impcomprobanteformato'] != null && $rowFactura['impsolicitadoformato'] != null) { 
										$controlComp = $rowFactura['impcomprobanteformato'] - $rowFactura['impcomprobante']; 
										$controlSoli = $rowFactura['impsolicitadoformato'] - $rowFactura['impsolicitado'];
										if ($controlComp != 0) $colorComp = 'red'; else $colorComp = '';
										if ($controlSoli != 0) $colorSoli = 'red'; else $colorSoli = ''; 
										if ($rowFactura['tipoarchivo'] == 'DB') {
											$totComFor -= $rowFactura['impcomprobanteformato'];
											$totSolFor -= $rowFactura['impsolicitadoformato']; ?>
											<td style="font-size: 11px; color: <?php echo $colorComp ?>"><?php echo "(".number_format($rowFactura['impcomprobanteformato'],2,",",".").")" ?></td>
											<td style="font-size: 11px; color: <?php echo $colorSoli ?>"><?php echo "(".number_format($rowFactura['impsolicitadoformato'],2,",",".").")" ?></td>
								  <?php } else { 
								  			$totComFor += $rowFactura['impcomprobanteformato'];
								  			$totSolFor += $rowFactura['impsolicitadoformato']; ?>
								  			<td style="font-size: 11px; color: <?php echo $colorComp ?>"><?php echo number_format($rowFactura['impcomprobanteformato'],2,",",".") ?></td>
								  			<td style="font-size: 11px; color: <?php echo $colorSoli ?>"><?php echo number_format($rowFactura['impsolicitadoformato'],2,",",".") ?></td>
							
								  <?php }
									} else {  ?>
										<td>-</td>
										<td>-</td>
						<?php 		}
							  } 
						  } else { ?>
						  		<td>-</td>
						  		<td>-</td>
					<?php }
					
						  if ($rowPresentacion['fechaintegral'] != null) {
						      if ($rowFactura['deverrorintegral'] != null && $rowFactura['deverrorformato'] == null) { ?>
								<td colspan="2" style="font-size: 11px; color: red"><?php  echo "ERROR: ".$rowFactura['deverrorintegral'] ?></td>
						<?php } else { 
									if ($rowFactura['impcomprobanteintegral'] != null && $rowFactura['impsolicitadointegral'] != null) { 
										$controlCompInt = $rowFactura['impcomprobanteintegral'] - $rowFactura['impcomprobanteformato']; 
										$controlSoliInt = $rowFactura['impsolicitadointegral'] - $rowFactura['impsolicitadoformato'];
										if ($controlCompInt != 0) $colorCompInt = 'red'; else $colorCompInt = '';
										if ($controlSoliInt != 0) $colorSoliInt = 'red'; else $colorSoliInt = ''; 
										
										if ($rowFactura['tipoarchivo'] == 'DB') {
											$totComInt -= $rowFactura['impcomprobanteintegral'];
											$totSolInt -= $rowFactura['impsolicitadointegral']; ?>
											<td style="font-size: 11px; color: <?php echo $colorCompInt ?>"><?php if ($rowFactura['impcomprobanteintegral'] != null) echo "(".number_format($rowFactura['impcomprobanteintegral'],2,",",".").")"; else echo "-";  ?></td>
											<td style="font-size: 11px; color: <?php echo $colorSoliInt ?>"><?php if ($rowFactura['impsolicitadointegral'] != null) echo "(".number_format($rowFactura['impsolicitadointegral'],2,",",".").")"; else echo "-";  ?></td>
						<?php			} else { 
											$totComInt += $rowFactura['impcomprobanteintegral'];
											$totSolInt += $rowFactura['impsolicitadointegral'];  ?>
											<td style="font-size: 11px; color: <?php echo $colorCompInt ?>"><?php if ($rowFactura['impcomprobanteintegral'] != null) echo number_format($rowFactura['impcomprobanteintegral'],2,",","."); else echo "-";  ?></td>
											<td style="font-size: 11px; color: <?php echo $colorSoliInt ?>"><?php if ($rowFactura['impsolicitadointegral'] != null) echo number_format($rowFactura['impsolicitadointegral'],2,",","."); else echo "-";  ?></td>
								  <?php }
									} else { ?>
										<td>-</td>
										<td>-</td>
						<?php		} 	
							  }  
						  } else { ?>
						  		<td>-</td>
								<td>-</td>
			<?php		  }
			
						  if ($rowPresentacion['fechasubsidio'] != null) {
						  		if ($rowFactura['deverrorintegral'] == null && $rowFactura['deverrorformato'] == null) { 
									$controlMontoSub = $rowFactura['impsolicitadosubsidio'] - $rowFactura['impmontosubsidio'];
									if ($controlMontoSub != 0) $colorMontInt = 'red'; else $colorMontInt = ''; 
									$totSolSub += $rowFactura['impsolicitadosubsidio'];
									$totMonSub += $rowFactura['impmontosubsidio'];
									
								  	$impOsp = $rowFactura['impsolicitadosubsidio'] - $rowFactura['impmontosubsidio'];
									$totMonOsp += $impOsp;
		
									$importeFactura = $rowFactura['impcomprobanteintegral'];
									if ($rowFactura['tipoarchivo'] == 'DB') {
										$importeFactura = (-1)*$rowFactura['impcomprobanteintegral'];
									}
									
									$impChOsp = $importeFactura - $rowFactura['impsolicitadosubsidio'];
									$totMonChOsp += $impChOsp;
									
									$retiene = "NO";
									if ($rowFactura['retiene'] == 1) {
										$retiene = "SI";
									} 
									
									$sqlDebeRecibo = "SELECT f.nrocomprobante FROM intepagos p, intepresentaciondetalle f
														WHERE
														p.recibo = '' and
														p.nrocominterno = f.nrocominterno and codpractica not in (97,98,99) and
														f.cuit = ".$rowFactura['cuit']." and
														f.impsolicitadosubsidio is not null and
														f.impmontosubsidio is not null
														group by f.nrocomprobante";
									$resDebeRecibo = mysql_query($sqlDebeRecibo);
									$canDebeRecibo = mysql_num_rows($resDebeRecibo);
									?>
									<td><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".");  ?></td>
									<td style="font-size: 11px; color: <?php echo $colorMontInt ?>"><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".");  ?></td>
									<td><?php echo number_format($impOsp,2,",",".");  ?></td>
									<td><?php echo number_format($impChOsp,2,",","."); ?></td>
									<td><?php echo $retiene ?></td>
									<td><?php echo $canDebeRecibo ?></td>
						<?php 	} else { ?>
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
						  <?php }
						} else { ?>		
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
						 <?php } ?>		  	
					</tr>	
			<?php } ?>
					<tr>
						<th colspan="11">TOTALES</td>
						<th><?php echo number_format($totCom,2,",",".") ?></td>
						<th><?php echo number_format($totSol,2,",",".") ?></td>
						<th><?php echo number_format($totComFor,2,",",".") ?></td>
						<th><?php echo number_format($totSolFor,2,",",".") ?></td>
						<th><?php echo number_format($totComInt,2,",",".") ?></td>
						<th><?php echo number_format($totSolInt,2,",",".") ?></td>
						<th><?php echo number_format($totSolSub,2,",",".") ?></td>
						<th><?php echo number_format($totMonSub,2,",",".") ?></td>
						<th><?php echo number_format($totMonOsp,2,",",".") ?></td>
						<th><?php echo number_format($totMonChOsp,2,",",".") ?></td>
						<th></td>
						<th></td>
					</tr>
			  	</tbody>
			</table>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>