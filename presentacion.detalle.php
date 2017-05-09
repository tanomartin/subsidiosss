<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];
$sqlFactura = "SELECT * FROM facturas WHERE idpresentacion = $idPresentacion order by cuit, cuil, nrocomprobante";
$resFactura = mysql_query($sqlFactura);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Detalle Presentaciones S.S.S. :.</title>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	
	 	<?php include_once("include/detalle.php")?>
	 	
	 	<h2>Facturas</h2>
	 	
	 	<div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th style="font-size: 11px">Comp. Interno</th>
			 			<th style="font-size: 11px">Tipo</th>
			 			<th style="font-size: 11px">C.U.I.L.</th>
			 			<th style="font-size: 11px">Periodo</th>
			 			<th style="font-size: 11px">C.U.I.T.</th>
			 			<th style="font-size: 11px">C.A.E.</th>
			 			<th style="font-size: 11px">Fec. Comp.</th>
			 			<th style="font-size: 11px">Num. Comp.</th>
			 			<th style="font-size: 11px">Cod. Prac.</th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Formato</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Integral</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Subsidio</th>
			 		</tr>
			 		<tr>
			 			<th style="font-size: 11px" colspan="11"></th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">$ Subsidiado</th>
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
				while ($rowFactura = mysql_fetch_array($resFactura)) {  ?>
					<tr>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuil'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['periodo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuit'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cae'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['codpractica'] ?></td>
				 
				  <?php if ($rowFactura['tipoarchivo'] == 'DB') { 
				  			$totCom -= $rowFactura['impcomprobante'];
				  			$totSol -= $rowFactura['impsolicitado']; ?>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impcomprobante'],2,",",".").")" ?></td>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impsolicitado'],2,",",".").")" ?></td>
				   <?php } else { 
							$totCom += $rowFactura['impcomprobante'];
				  			$totSol += $rowFactura['impsolicitado']; ?>
						    <td style="font-size: 11px"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						    <td style="font-size: 11px"><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
					<?php } 
					   
						  if ($rowFactura['deverrorformato'] != null ) { ?>
							<td colspan="2" style="font-size: 11px; color: red"><?php  echo "ERROR: ".$rowFactura['deverrorformato'] ?></td>
					<?php } else { 
								if ($rowFactura['impcomprobanteformato'] != null && $rowFactura['impsolicitadoformato'] != null) { 
									$controlComp = $rowFactura['impcomprobanteformato'] - $rowFactura['impcomprobante']; 
									$controlSoli = $rowFactura['impsolicitadoformato'] - $rowFactura['impsolicitado'];
									if ($controlComp != 0) $colorComp = 'red'; else $colorComp = '';
									if ($controlSoli != 0) $colorSoli = 'red'; else $colorSoli = ''; 
									$totComFor += $rowFactura['impcomprobanteformato'];
									$totSolFor += $rowFactura['impsolicitadoformato'];
									?>
									<td style="font-size: 11px; color: <?php echo $colorComp ?>"><?php echo number_format($rowFactura['impcomprobanteformato'],2,",",".") ?></td>
									<td style="font-size: 11px; color: <?php echo $colorSoli ?>"><?php echo number_format($rowFactura['impsolicitadoformato'],2,",",".") ?></td>
					<?php 		} else {  ?>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
					<?php 		}
						  }  
					      if ($rowFactura['deverrorintegral'] != null) { ?>
							<td colspan="2" style="font-size: 11px; color: red"><?php  echo "ERROR: ".$rowFactura['deverrorintegral'] ?></td>
					<?php } else { 
								if ($rowFactura['impcomprobanteintegral'] != null && $rowFactura['impsolicitadointegral'] != null) { 
									$controlCompInt = $rowFactura['impcomprobanteintegral'] - $rowFactura['impcomprobanteformato']; 
									$controlSoliInt = $rowFactura['impsolicitadointegral'] - $rowFactura['impsolicitadoformato'];
									if ($controlCompInt != 0) $colorCompInt = 'red'; else $colorCompInt = '';
									if ($controlSoliInt != 0) $colorSoliInt = 'red'; else $colorSoliInt = ''; 
									$totComInt += $rowFactura['impcomprobanteintegral'];
									$totSolInt += $rowFactura['impsolicitadointegral'];
									?>
									<td style="font-size: 11px; color: <?php echo $colorCompInt ?>"><?php if ($rowFactura['impcomprobanteintegral'] != null) echo number_format($rowFactura['impcomprobanteintegral'],2,",","."); else echo "-";  ?></td>
									<td style="font-size: 11px; color: <?php echo $colorSoliInt ?>"><?php if ($rowFactura['impsolicitadointegral'] != null) echo number_format($rowFactura['impsolicitadointegral'],2,",","."); else echo "-";  ?></td>
					<?php		} else { ?>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
					<?php		} 	
						  }  
						  if ($rowFactura['impsolicitadosubsidio'] != null && $rowFactura['impmontosubsidio'] != null) { 
							$controlMontoSub = $rowFactura['impsolicitadosubsidio'] - $rowFactura['impmontosubsidio'];
							if ($controlMontoSub != 0) $colorMontInt = 'red'; else $colorMontInt = ''; 
							$totSolSub += $rowFactura['impsolicitadosubsidio'];
							$totMonSub += $rowFactura['impmontosubsidio'];
							?>
								<td style="font-size: 11px"><?php if ($rowFactura['impsolicitadosubsidio'] != null) echo number_format($rowFactura['impsolicitadosubsidio'],2,",","."); else echo "-";  ?></td>
								<td style="font-size: 11px; color: <?php echo $colorMontInt ?>"><?php if ($rowFactura['impmontosubsidio'] != null) echo number_format($rowFactura['impmontosubsidio'],2,",","."); else echo "-";  ?></td>
					<?php 	} else { ?>
								<td style="font-size: 11px">-</td>
								<td style="font-size: 11px">-</td>
					  <?php } ?> 
					</tr>
			<?php } ?>
					<tr>
						<td colspan="9">TOTALES</td>
						<td style="font-size: 11px"><?php echo number_format($totCom,2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($totSol,2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($totComFor,2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($totSolFor,2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($totComInt,2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($totSolInt,2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($totSolSub,2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($totMonSub,2,",",".") ?></td>
					</tr>
			  	</tbody>
			</table>
		</div>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>