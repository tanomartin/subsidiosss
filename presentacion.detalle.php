<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];
$sqlPresentacion = "SELECT 
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

$sqlFactura = "SELECT * FROM facturas WHERE idpresentacion = $idPresentacion order by cuil, periodo, codpractica";
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
	 	
	 	<?php include_once("include/detallePresentacion.php")?>
	 	<?php include_once("include/detalleDevolucion.php")?>
	 	
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
			 			<th style="font-size: 11px">Cod. Prac.</th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Formato</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Integral</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Subsidio</th>
			 		</tr>
			 		<tr>
			 			<th style="font-size: 11px" colspan="10"></th>
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
						<td style="font-size: 11px"><?php echo $rowFactura['codpractica'] ?></td>
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
									$controlCompInt = $rowFactura['impcomprobanteintegral'] - $rowFactura['impcomprobanteformato']; 
									$controlSoliInt = $rowFactura['impsolicitadointegral'] - $rowFactura['impsolicitadoformato'];
									if ($controlCompInt != 0) $colorCompInt = 'red'; else $colorCompInt = '';
									if ($controlSoliInt != 0) $colorSoliInt = 'red'; else $colorSoliInt = ''; ?>
									<td style="font-size: 11px; color: <?php echo $colorCompInt ?>"><?php if ($rowFactura['impcomprobanteintegral'] != null) echo number_format($rowFactura['impcomprobanteintegral'],2,",","."); else echo "-";  ?></td>
									<td style="font-size: 11px; color: <?php echo $colorSoliInt ?>"><?php if ($rowFactura['impsolicitadointegral'] != null) echo number_format($rowFactura['impsolicitadointegral'],2,",","."); else echo "-";  ?></td>
					<?php		} else { ?>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
					<?php		} 	
						  }  
						  if ($rowPresentacion['fechasubsidio'] != null) { 
								if ($rowFactura['impsolicitadosubsidio'] != null && $rowFactura['impmontosubsidio'] != null) { 
									$controlMontoSub = $rowFactura['impsolicitadosubsidio'] - $rowFactura['impmontosubsidio'];
									if ($controlMontoSub != 0) $colorMontInt = 'red'; else $colorMontInt = ''; ?>
									<td style="font-size: 11px"><?php if ($rowFactura['impsolicitadosubsidio'] != null) echo number_format($rowFactura['impsolicitadosubsidio'],2,",","."); else echo "-";  ?></td>
									<td style="font-size: 11px; color: <?php echo $colorMontInt ?>""><?php if ($rowFactura['impmontosubsidio'] != null) echo number_format($rowFactura['impmontosubsidio'],2,",","."); else echo "-";  ?></td>
					<?php 		} else { ?>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
						  <?php } 
						  } else { ?>
								<td style="font-size: 11px">-</td>
								<td style="font-size: 11px">-</td>
					<?php }?>
					</tr>
			<?php } ?>
			  	</tbody>
			</table>
		</div>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>