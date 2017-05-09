<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];
$carpeta = $_GET['carpeta'];
$sqlFactura = "SELECT * FROM facturas WHERE idpresentacion = $idPresentacion order by cuit, cuil, nrocomprobante";
$resFactura = mysql_query($sqlFactura);


$file= $carpeta."-".$idPresentacion.".xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");

?>
<body>
	<div align="center">
		<?php include_once("include/detalle.php")?>
		
		<h2>Facturas</h2>
		
		<table>
			 		<tr>
			 			<th>Comp. Interno</th>
			 			<th>Tipo</th>
			 			<th>C.U.I.L.</th>
			 			<th>Periodo</th>
			 			<th>C.U.I.T.</th>
			 			<th>C.A.E.</th>
			 			<th>Fec. Comp.</th>
			 			<th>Num. Comp.</th>
			 			<th>Cod. Prac.</th>
			 			<th>$ Comprobante</th>
			 			<th>$ Solicitado</th>
			 			<th colspan="2">Resultado Formato</th>
			 			<th colspan="2">Resultado Integral</th>
			 			<th colspan="2">Resultado Subsidio</th>
			 		</tr>
			 		<tr>
			 			<th colspan="11"></th>
			 			<th>$ Comprobante</th>
			 			<th>$ Solicitado</th>
			 			<th>$ Comprobante</th>
			 			<th>$ Solicitado</th>
			 			<th>$ Solicitado</th>
			 			<th>$ Subsidiado</th>
			 		</tr>
			<?php 
				$totCom = 0;
				$totSol = 0;
				$totComFor = 0;
				$totSolFor = 0;
				$totComInt = 0;
				$totSolInt = 0;
				$totSolSub = 0;
				$totMonSub = 0;
				while ($rowFactura = mysql_fetch_array($resFactura)) { 
					$totCom += $rowFactura['impcomprobante'];
					$totSol += $rowFactura['impsolicitado'];
					?>
					<tr>
						<td><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td><?php echo $rowFactura['cuil'] ?></td>
						<td><?php echo $rowFactura['periodo'] ?></td>
						<td><?php echo $rowFactura['cuit'] ?></td>
						<td><?php echo $rowFactura['cae'] ?></td>
						<td><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td><?php echo $rowFactura['codpractica'] ?></td>
						<td><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						<td><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
					<?php if ($rowFactura['deverrorformato'] != null ) { ?>
							<td colspan="2" style="color: red"><?php  echo "ERROR: ".$rowFactura['deverrorformato'] ?></td>
					<?php } else { 
								if ($rowFactura['impcomprobanteformato'] != null && $rowFactura['impsolicitadoformato'] != null) { 
									$controlComp = $rowFactura['impcomprobanteformato'] - $rowFactura['impcomprobante']; 
									$controlSoli = $rowFactura['impsolicitadoformato'] - $rowFactura['impsolicitado'];
									if ($controlComp != 0) $colorComp = 'red'; else $colorComp = '';
									if ($controlSoli != 0) $colorSoli = 'red'; else $colorSoli = ''; 
									$totComFor += $rowFactura['impcomprobanteformato'];
									$totSolFor += $rowFactura['impsolicitadoformato'];
									?>
									<td style="color: <?php echo $colorComp ?>"><?php echo number_format($rowFactura['impcomprobanteformato'],2,",",".") ?></td>
									<td style="color: <?php echo $colorSoli ?>"><?php echo number_format($rowFactura['impsolicitadoformato'],2,",",".") ?></td>
					<?php 		} else {  ?>
									<td>-</td>
									<td>-</td>
					<?php 		}
						  }  
					      if ($rowFactura['deverrorintegral'] != null) { ?>
							<td colspan="2" style="color: red"><?php  echo "ERROR: ".$rowFactura['deverrorintegral'] ?></td>
					<?php } else { 
								if ($rowFactura['impcomprobanteintegral'] != null && $rowFactura['impsolicitadointegral'] != null) { 
									$controlCompInt = $rowFactura['impcomprobanteintegral'] - $rowFactura['impcomprobanteformato']; 
									$controlSoliInt = $rowFactura['impsolicitadointegral'] - $rowFactura['impsolicitadoformato'];
									if ($controlCompInt != 0) $colorCompInt = 'red'; else $colorCompInt = '';
									if ($controlSoliInt != 0) $colorSoliInt = 'red'; else $colorSoliInt = ''; 
									$totComInt += $rowFactura['impcomprobanteintegral'];
									$totSolInt += $rowFactura['impsolicitadointegral'];
									?>
									<td style="color: <?php echo $colorCompInt ?>"><?php if ($rowFactura['impcomprobanteintegral'] != null) echo number_format($rowFactura['impcomprobanteintegral'],2,",","."); else echo "-";  ?></td>
									<td style="color: <?php echo $colorSoliInt ?>"><?php if ($rowFactura['impsolicitadointegral'] != null) echo number_format($rowFactura['impsolicitadointegral'],2,",","."); else echo "-";  ?></td>
					<?php		} else { ?>
									<td>-</td>
									<td>-</td>
					<?php		} 	
						  }  
						if ($rowFactura['impsolicitadosubsidio'] != null && $rowFactura['impmontosubsidio'] != null) { 
							$controlMontoSub = $rowFactura['impsolicitadosubsidio'] - $rowFactura['impmontosubsidio'];
							if ($controlMontoSub != 0) $colorMontInt = 'red'; else $colorMontInt = ''; 
							$totSolSub += $rowFactura['impsolicitadosubsidio'];
							$totMonSub += $rowFactura['impmontosubsidio'];
							?>
							<td><?php if ($rowFactura['impsolicitadosubsidio'] != null) echo number_format($rowFactura['impsolicitadosubsidio'],2,",","."); else echo "-";  ?></td>
							<td style="color: <?php echo $colorMontInt ?>"><?php if ($rowFactura['impmontosubsidio'] != null) echo number_format($rowFactura['impmontosubsidio'],2,",","."); else echo "-";  ?></td>
				<?php 	} else { ?>
							<td>-</td>
							<td>-</td>
				<?php } ?>
					</tr>
			<?php } ?>
					<tr>
						<td colspan="9">TOTALES</td>
						<td><?php echo number_format($totCom,2,",",".") ?></td>
						<td><?php echo number_format($totSol,2,",",".") ?></td>
						<td><?php echo number_format($totComFor,2,",",".") ?></td>
						<td><?php echo number_format($totSolFor,2,",",".") ?></td>
						<td><?php echo number_format($totComInt,2,",",".") ?></td>
						<td><?php echo number_format($totSolInt,2,",",".") ?></td>
						<td><?php echo number_format($totSolSub,2,",",".") ?></td>
						<td><?php echo number_format($totMonSub,2,",",".") ?></td>
					</tr>
		</table>
	</div>
</body>