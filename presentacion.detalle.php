<?php 
include_once 'include/conector.php';
set_time_limit(0);
$idPresentacion = $_GET['id'];
$sqlFactura = "SELECT intepresentaciondetalle.*, madera.prestadoresauxiliar.cbu,
	CASE
	  WHEN (madera.prestadores.situacionfiscal in (0,1,4) || (madera.prestadores.situacionfiscal = 3 and madera.prestadores.vtoexento >= CURDATE())) THEN 0
	  WHEN (madera.prestadores.situacionfiscal = 2 || (madera.prestadores.situacionfiscal = 3 and madera.prestadores.vtoexento < CURDATE())) THEN 1
	END as retiene
FROM intepresentaciondetalle
LEFT JOIN madera.prestadores on intepresentaciondetalle.cuit = madera.prestadores.cuit
LEFT JOIN madera.prestadoresauxiliar on prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
WHERE idpresentacion = $idPresentacion order by cuil, periodo, codpractica";
$resFactura = mysql_query($sqlFactura);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Facturas Presentaciones S.S.S. :.</title>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>

<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["zebra", "filter"],
			widgetOptions : { 
				filter_cssFilter   : '',
				filter_childRows   : false,
				filter_hideFilters : false,
				filter_ignoreCase  : true,
				filter_searchDelay : 300,
				filter_startsWith  : false,
				filter_hideFilters : false,
			}
		});
});


</script>

<title>.: Detalle Presentaciones S.S.S. :.</title>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	
	 	<?php include_once("include/detalle.php");
	 	      include_once("include/totales.php"); ?>

	 	<h2>Facturas</h2>
	 	
			 <table id="listaResultado" class="tablesorter" style="text-align: center;">
			 	<thead>
			 		<tr>
			 			<th rowspan="2" style="font-size: 11px">Comp. Interno</th>
			 			<th rowspan="2" style="font-size: 11px">Tipo</th>
			 			<th rowspan="2" style="font-size: 11px">C.U.I.L.</th>
			 			<th rowspan="2" style="font-size: 11px">Periodo</th>
			 			<th rowspan="2" style="font-size: 11px">C.U.I.T.</th>
			 			<th rowspan="2" style="font-size: 11px">C.B.U.</th>
			 			<th rowspan="2" style="font-size: 11px">C.A.E.</th>
			 			<th rowspan="2" style="font-size: 11px">Fec. Comp.</th>
			 			<th rowspan="2" style="font-size: 11px">Num. Comp.</th>
			 			<th rowspan="2" style="font-size: 11px">Cod. Prac.</th>
			 			<th rowspan="2" style="font-size: 11px">$ Comp.</th>
			 			<th rowspan="2" style="font-size: 11px">$ Deb.</th>
			 			<th rowspan="2" style="font-size: 11px">$ No Int.</th>
			 			<th rowspan="2" style="font-size: 11px">$ Soli.</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Formato</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Integral</th>
			 			<th style="font-size: 11px" colspan="3">Resultado Subsidio</th>
			 			<th rowspan="2" style="font-size: 11px">Ret.</th>
			 			<th rowspan="2" style="font-size: 11px">Cant. Rec. Debe</th>
			 		</tr>
			 		<tr>
			 			<th style="font-size: 11px">Comp.</th>
			 			<th style="font-size: 11px">Soli.</th>
			 			<th style="font-size: 11px">Comp.</th>
			 			<th style="font-size: 11px">Soli.</th>
			 			
			 			<th style="font-size: 11px">Soli.</th>
			 			<th style="font-size: 11px">Subs.</th>
			 			<th style="font-size: 11px">Tr O.S.</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php 
				$totCom = 0;
				$totDeb = 0;
				$totNOI = 0;
				$totSol = 0;
				$totComFor = 0;
				$totSolFor = 0;
				$totComInt = 0;
				$totSolInt = 0;
				$totSolSub = 0;
				$totMonSub = 0;
				$totMonOsp = 0;
				while ($rowFactura = mysql_fetch_array($resFactura)) {  ?>
					<tr>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuil'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['periodo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuit'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cbu'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cae'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['codpractica'] ?></td>
				 
				  <?php if ($rowFactura['tipoarchivo'] == 'DB') { 
				  			$totCom -= $rowFactura['impcomprobante'];
				  			$totDeb -= $rowFactura['impdebito'];
				  			$totNOI -= $rowFactura['impnointe'];
				  			$totSol -= $rowFactura['impsolicitado']; ?>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impcomprobante'],2,",",".").")" ?></td>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impdebito'],2,",",".").")" ?></td>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impnointe'],2,",",".").")" ?></td>
						    <td style="font-size: 11px"><?php echo "(".number_format($rowFactura['impsolicitado'],2,",",".").")" ?></td>
				   <?php } else { 
							$totCom += $rowFactura['impcomprobante'];
							$totDeb += $rowFactura['impdebito'];
							$totNOI += $rowFactura['impnointe'];
				  			$totSol += $rowFactura['impsolicitado']; ?>
						    <td style="font-size: 11px"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						    <td style="font-size: 11px"><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></td>
						    <td style="font-size: 11px"><?php echo number_format($rowFactura['impnointe'],2,",",".") ?></td>
						    <td style="font-size: 11px"><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
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
										<td style="font-size: 11px">-</td>
										<td style="font-size: 11px">-</td>
						<?php 		}
							  } 
						  } else { ?>
						  		<td style="font-size: 11px">-</td>
						  		<td style="font-size: 11px">-</td>
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
										<td style="font-size: 11px">-</td>
										<td style="font-size: 11px">-</td>
						<?php		} 	
							  }  
						  } else { ?>
						  		<td style="font-size: 11px">-</td>
								<td style="font-size: 11px">-</td>
			<?php		  }
			
						  if ($rowPresentacion['fecharendicion'] != null) {
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
															
									$retiene = "NO";
									if ($rowFactura['retiene'] == 1) {
										$retiene = "SI";
									} 
									
									$sqlDebeRecibo = "SELECT f.nrocomprobante FROM intepagosdetalle p, intepresentaciondetalle f
														WHERE
														(p.recibo is null or p.recibo = '') and
														p.nrocominterno = f.nrocominterno and codpractica not in (97,98,99) and
														f.cuit = ".$rowFactura['cuit']." and
														f.impsolicitadosubsidio is not null and
														f.impmontosubsidio is not null
														group by f.nrocomprobante";
									$resDebeRecibo = mysql_query($sqlDebeRecibo);
									$canDebeRecibo = mysql_num_rows($resDebeRecibo);
									?>
									<td style="font-size: 11px"><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".");  ?></td>
									<td style="font-size: 11px; color: <?php echo $colorMontInt ?>"><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".");  ?></td>
									<td style="font-size: 11px"><?php echo number_format($impOsp,2,",",".");  ?></td>
									<td style="font-size: 11px"><?php echo $retiene ?></td>
									<td style="font-size: 11px"><?php echo $canDebeRecibo ?></td>
						<?php 	} else { ?>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
						  <?php }
						} else { ?>		
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
									<td style="font-size: 11px">-</td>
						 <?php } ?>		  	
					</tr>	
			<?php } ?>
				</tbody>
				<tr>
					<th colspan="10">TOTALES</td>
					<th style="font-size: 11px"><?php echo number_format($totCom,2,",",".") ?></td>	
					<th style="font-size: 11px"><?php echo number_format($totDeb,2,",",".") ?></td>
					<th style="font-size: 11px"><?php echo number_format($totNOI,2,",",".") ?></td>				
					<th style="font-size: 11px"><?php echo number_format($totSol,2,",",".") ?></td>
					<th style="font-size: 11px"><?php echo number_format($totComFor,2,",",".") ?></td>
					<th style="font-size: 11px"><?php echo number_format($totSolFor,2,",",".") ?></td>
					<th style="font-size: 11px"><?php echo number_format($totComInt,2,",",".") ?></td>
					<th style="font-size: 11px"><?php echo number_format($totSolInt,2,",",".") ?></td>
					<th style="font-size: 11px"><?php echo number_format($totSolSub,2,",",".") ?></td>
					<th style="font-size: 11px"><?php echo number_format($totMonSub,2,",",".") ?></td>
					<th style="font-size: 11px"><?php echo number_format($totMonOsp,2,",",".") ?></td>
					<th style="font-size: 11px"></td>
					<th style="font-size: 11px"></td>
				</tr>
			</table>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>