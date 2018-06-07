<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];
$nrocom = $_GET['nro'];
$sqlFactura = "SELECT d.*,
				CASE
				  WHEN (madera.prestadores.situacionfiscal in (0,1,4) || (madera.prestadores.situacionfiscal = 3 and madera.prestadores.vtoexento >= CURDATE())) THEN 0
				  WHEN (madera.prestadores.situacionfiscal = 2 || (madera.prestadores.situacionfiscal = 3 and madera.prestadores.vtoexento < CURDATE())) THEN 1
				END as retiene
				FROM intepresentaciondetalle d
				LEFT JOIN madera.prestadores on d.cuit = madera.prestadores.cuit
				LEFT JOIN madera.prestadoresauxiliar on madera.prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
				WHERE d.idpresentacion = $idPresentacion and d.nrocominterno = $nrocom order by d.cuit, d.cuil, d.nrocomprobante";
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
	 	
	 	<?php include_once("include/detalle.php")?>
	 	
	 	<h2>Facturas</h2>
	 	
	 	<div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th rowspan="2" style="font-size: 11px">Comp. Interno</th>
			 			<th rowspan="2" style="font-size: 11px">Tipo</th>
			 			<th rowspan="2" style="font-size: 11px">C.U.I.L.</th>
			 			<th rowspan="2" style="font-size: 11px">Periodo</th>
			 			<th rowspan="2" style="font-size: 11px">C.U.I.T.</th>
			 			<th rowspan="2" style="font-size: 11px">C.A.E.</th>
			 			<th rowspan="2" style="font-size: 11px">Fec. Comp.</th>
			 			<th rowspan="2" style="font-size: 11px">Num. Comp.</th>
			 			<th rowspan="2" style="font-size: 11px">Cod. Prac.</th>
			 			<th rowspan="2" style="font-size: 11px">$ Comp.</th>
			 			<th rowspan="2" style="font-size: 11px">$ Deb.</th>
			 			<th rowspan="2" style="font-size: 11px">$ No inte.</th>
			 			<th rowspan="2" style="font-size: 11px">$ Soli.</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Formato</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Integral</th>
			 			<th style="font-size: 11px" colspan="3">Resultado Subsidio</th>
			 			<th rowspan="2" style="font-size: 11px">Ret.</th>
			 		</tr>
			 		<tr>
			 			<th style="font-size: 11px">$ Comp.</th>
			 			<th style="font-size: 11px">$ Soli.</th>
			 			<th style="font-size: 11px">$ Comp.</th>
			 			<th style="font-size: 11px">$ Soli.</th>
			 			
			 			<th style="font-size: 11px">$ Soli.</th>
			 			<th style="font-size: 11px">$ Subs.</th>
			 			<th style="font-size: 11px">$ O.S.</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php 
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
				 
				  <?php if ($rowFactura['tipoarchivo'] == 'DB') { ?>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impcomprobante'],2,",",".").")" ?></td>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impdebito'],2,",",".").")" ?></td>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impnointe'],2,",",".").")" ?></td>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impsolicitado'],2,",",".").")" ?></td>
				  <?php } else { ?>
						    <td style="font-size: 11px"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						    <td style="font-size: 11px"><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></td>
						    <td style="font-size: 11px"><?php echo number_format($rowFactura['impnointe'],2,",",".") ?></td>
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
								if ($rowFactura['tipoarchivo'] == 'DB') { ?>
									<td style="font-size: 11px; color: <?php echo $colorComp ?>"><?php echo "(".number_format($rowFactura['impcomprobanteformato'],2,",",".").")" ?></td>
									<td style="font-size: 11px; color: <?php echo $colorSoli ?>"><?php echo "(".number_format($rowFactura['impsolicitadoformato'],2,",",".").")" ?></td>
						  <?php } else { ?>
								  	<td style="font-size: 11px; color: <?php echo $colorComp ?>"><?php echo number_format($rowFactura['impcomprobanteformato'],2,",",".") ?></td>
								  	<td style="font-size: 11px; color: <?php echo $colorSoli ?>"><?php echo number_format($rowFactura['impsolicitadoformato'],2,",",".") ?></td>
						  <?php }
							} else { ?>
								<td style="font-size: 11px">-</td>
								<td style="font-size: 11px">-</td>
				 	<?php 	}
						} 
						if ($rowFactura['deverrorintegral'] != null && $rowFactura['deverrorformato'] == null && $rowFactura['impcomprobanteintegral'] != null) { ?>
							<td colspan="2" style="font-size: 11px; color: red"><?php  echo "ERROR: ".$rowFactura['deverrorintegral'] ?></td>
				  <?php } else { 
							if ($rowFactura['impcomprobanteintegral'] != null && $rowFactura['impsolicitadointegral'] != null) { 
								$controlCompInt = $rowFactura['impcomprobanteintegral'] - $rowFactura['impcomprobanteformato']; 
								$controlSoliInt = $rowFactura['impsolicitadointegral'] - $rowFactura['impsolicitadoformato'];
								if ($controlCompInt != 0) $colorCompInt = 'red'; else $colorCompInt = '';
								if ($controlSoliInt != 0) $colorSoliInt = 'red'; else $colorSoliInt = ''; 		
								if ($rowFactura['tipoarchivo'] == 'DB') { ?>
									<td style="font-size: 11px; color: <?php echo $colorCompInt ?>"><?php if ($rowFactura['impcomprobanteintegral'] != null) echo "(".number_format($rowFactura['impcomprobanteintegral'],2,",",".").")"; else echo "-";  ?></td>
									<td style="font-size: 11px; color: <?php echo $colorSoliInt ?>"><?php if ($rowFactura['impsolicitadointegral'] != null) echo "(".number_format($rowFactura['impsolicitadointegral'],2,",",".").")"; else echo "-";  ?></td>
						<?php	} else {   ?>
									<td style="font-size: 11px; color: <?php echo $colorCompInt ?>"><?php if ($rowFactura['impcomprobanteintegral'] != null) echo number_format($rowFactura['impcomprobanteintegral'],2,",","."); else echo "-";  ?></td>
									<td style="font-size: 11px; color: <?php echo $colorSoliInt ?>"><?php if ($rowFactura['impsolicitadointegral'] != null) echo number_format($rowFactura['impsolicitadointegral'],2,",","."); else echo "-";  ?></td>
						  <?php }
							} else { ?>
								<td style="font-size: 11px">-</td>
								<td style="font-size: 11px">-</td>
					  <?php } 	 
						  } 
						  if ($rowFactura['deverrorintegral'] == null && $rowFactura['deverrorformato'] == null && $rowFactura['impmontosubsidio'] != null) { 
								$controlMontoSub = $rowFactura['impsolicitadosubsidio'] - $rowFactura['impmontosubsidio'];
								if ($controlMontoSub != 0) $colorMontInt = 'red'; else $colorMontInt = ''; 
									
								$impOsp = $rowFactura['impsolicitadosubsidio'] - $rowFactura['impmontosubsidio'];
		
								$importeFactura = $rowFactura['impcomprobanteintegral'];
								if ($rowFactura['tipoarchivo'] == 'DB') {
									$importeFactura = (-1)*$rowFactura['impcomprobanteintegral'];
								}

						  		$retiene = "NO";
								if ($rowFactura['retiene'] == 1) {
									$retiene = "SI";
								} ?>
									<td style="font-size: 11px"><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".");  ?></td>
									<td style="font-size: 11px; color: <?php echo $colorMontInt ?>"><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".");  ?></td>
									<td style="font-size: 11px"><?php echo number_format($impOsp,2,",",".");  ?></td>
									<td style="font-size: 11px"><?php echo $retiene ?></td>
					<?php 	} else { ?>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
						  <?php } ?>		  	
					</tr>	
			<?php } ?>
			  	</tbody>
			</table>
		</div>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>