<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];

$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$sqlFactura = "SELECT * FROM facturas WHERE idpresentacion = $idPresentacion";
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
	 	
	 	<h2>Detalle Presentacio</h2>
	 	<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	 	
	 	<?php if ($rowPresentacion['fechacancelacion'] != null) {  ?>
	 		<h3 style="color: red">PRESENTACION CANCELADA</h3>
	 		<p><b>MOTIVO: </b><?php echo $rowPresentacion['motivocancelacion'] ?></p>
	 	<?php } ?>
	 	
	 	<h3>[PRESENTACION]</h3><h3>Cantidad: <?php echo $rowPresentacion['cantfactura']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantes'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitado'],"2",",",".") ?></h3>
	 	
	 	<?php if ($rowPresentacion['fechadevformato'] != null) {  ?>
	 		<h3>[DEV. FORMATO]</h3>
	 		<h3>Cantidad OK: <?php echo $rowPresentacion['cantformatook']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesformatook'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadoformatook'],"2",",",".") ?></h3>
	 		<h3>Cantidad Rech: <?php echo $rowPresentacion['cantformatonok']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesformatonok'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadoformatonok'],"2",",",".") ?></h3>
	 	<?php } ?>
	 	
	 	<?php if ($rowPresentacion['fechaintegral'] != null) {  ?>
	 		<h3>[DEV. INTEGRAL]</h3>
	 		<h3>Cantidad OK: <?php echo $rowPresentacion['cantintegralok']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesintegralok'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadointegranlok'],"2",",",".") ?></h3>
	 		<h3>Cantidad Rech: <?php echo $rowPresentacion['cantintegralnok']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesintegralnok'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadointegranlnok'],"2",",",".") ?></h3>
	 	<?php } ?>
	 	
	 	<h2>Facturas</h2>
	 	
	 	<div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th style="font-size: 11px">Comp. Interno</th>
			 			<th style="font-size: 11px">C.U.I.L.</th>
			 			<th style="font-size: 11px">Periodo</th>
			 			<th style="font-size: 11px">C.U.I.T.</th>
			 			<th style="font-size: 11px">C.A.E.</th>
			 			<th style="font-size: 11px">Fec. Comp.</th>
			 			<th style="font-size: 11px">Num. Comp.</th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Formato</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Integral</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Subsidio</th>
			 		</tr>
			 		<tr>
			 			<th style="font-size: 11px" colspan="9"></th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">$ Subsidiado</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php while ($rowFactura = mysql_fetch_array($resFactura)) { ?>
					<tr>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuil'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['periodo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuit'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cae'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
					<?php if ($rowFactura['deverrorformato'] != null &&  $rowPresentacion['fechadevformato'] != null ) { ?>
							<td colspan="2" style="font-size: 11px; color: red"><?php  echo "ERROR: ".$rowFactura['deverrorformato'] ?></td>
					<?php } else { 
								if ($rowFactura['impcomprobanteformato'] != null && $rowFactura['impsolicitadoformato'] != null) { 
									$controlComp = $rowFactura['impcomprobanteformato'] - $rowFactura['impcomprobante']; 
									$controlSoli = $rowFactura['impsolicitadoformato'] - $rowFactura['impsolicitado'];
									if ($controlComp != 0) $colorComp = 'red'; else $colorComp = '';
									if ($controlSoli != 0) $colorSoli = 'red'; else $colorSoli = ''; ?>
									<td style="font-size: 11px; color: <?php echo $colorComp ?>"><?php echo number_format($rowFactura['impcomprobanteformato'],2,",",".") ?></td>
									<td style="font-size: 11px; color: <?php echo $colorSoli ?>"><?php echo number_format($rowFactura['impsolicitadoformato'],2,",",".") ?></td>
					<?php 		} else {  ?>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
					<?php 		}
						  }  
					      if ($rowFactura['deverrorintegral'] != null &&  $rowPresentacion['fechaintegral'] != null) { ?>
							<td colspan="2" style="font-size: 11px; color: red"><?php  echo "ERROR: ".$rowFactura['deverrorintegral'] ?></td>
					<?php } else { 
								if ($rowFactura['impcomprobanteintegral'] != null && $rowFactura['impsolicitadointegral'] != null) { 
									$controlCompInt = $rowFactura['impcomprobanteintegral'] - $rowFactura['impcomprobante']; 
									$controlSoliInt = $rowFactura['impsolicitadointegral'] - $rowFactura['impsolicitado'];
									if ($controlCompInt != 0) $colorCompInt = 'red'; else $colorCompInt = '';
									if ($controlSoliInt != 0) $colorSoliInt = 'red'; else $colorSoliInt = ''; ?>
									<td style="font-size: 11px; color: <?php echo $colorCompInt ?>"><?php if ($rowFactura['impcomprobanteintegral'] != null) echo number_format($rowFactura['impcomprobanteintegral'],2,",","."); else echo "-";  ?></td>
									<td style="font-size: 11px; color: <?php echo $colorSoliInt ?>"><?php if ($rowFactura['impsolicitadointegral'] != null) echo number_format($rowFactura['impsolicitadointegral'],2,",","."); else echo "-";  ?></td>
					<?php		} else { ?>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
					<?php		} 	
						  }  
						  if ($rowFactura['deverrorsubsidio'] != null &&  $rowPresentacion['fechasubsidio'] != null) { ?>
							<td colspan="2" style="font-size: 11px; color: red"><?php  echo "ERROR: ".$rowFactura['deverrorsubsidio'] ?></td>
					<?php } else { 
								if ($rowFactura['impsolicitadosubsidio'] != null && $rowFactura['impmontosubsidio'] != null) { ?>
									<td style="font-size: 11px"><?php if ($rowFactura['impsolicitadosubsidio'] != null) echo number_format($rowFactura['impsolicitadosubsidio'],2,",","."); else echo "-";  ?></td>
									<td style="font-size: 11px"><?php if ($rowFactura['impmontosubsidio'] != null) echo number_format($rowFactura['impmontosubsidio'],2,",","."); else echo "-";  ?></td>
					<?php 		} else { ?>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
					<?php		}
						  }?>
					</tr>
			<?php } ?>
			  	</tbody>
			</table>
		</div>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>