<?php
include_once 'include/conector.php';
set_time_limit(0);
$idPresentacion = $_GET['id'];
$carpeta = $_GET['carpeta'];

$sqlFactura = "SELECT * FROM intepresentaciondetalle
WHERE idpresentacion = $idPresentacion and deverrorintegral is null and codpractica not in (97,98,99)
ORDER BY cuit, periodo, codpractica";
$resFactura = mysql_query($sqlFactura);

$arrayFacturas = array();
while ($rowFactura = mysql_fetch_assoc($resFactura)) {
	$index = $rowFactura['cuit'].$rowFactura['nrocominterno'].$rowFactura['tipoarchivo'];
	if ($rowFactura['tipoarchivo'] == 'DB') {
		$rowFactura['impdebito'] = (-1)*$rowFactura['impdebito'];
		$rowFactura['impnointe'] = (-1)*$rowFactura['impnointe'];
		$rowFactura['impcomprobanteintegral'] = (-1)*$rowFactura['impcomprobanteintegral'];
	}
	$monOS = $rowFactura['impcomprobanteintegral'] - $rowFactura['impmontosubsidio'] - $rowFactura['impdebito'] - $rowFactura['impnointe'];
	$rowFactura['impobrasocial'] = $monOS;
	$arrayFacturas[$index] = $rowFactura;
}

$sqlTotalesCuit = "SELECT inteinterbanking.*,
	CASE
		WHEN (prestadores.situacionfiscal in (0,1,4) || (prestadores.situacionfiscal = 3 and prestadores.vtoexento >= CURDATE())) THEN 0
		WHEN (prestadores.situacionfiscal = 2 || (prestadores.situacionfiscal = 3 and prestadores.vtoexento < CURDATE())) THEN 1
	END as retiene
	FROM inteinterbanking 
	LEFT JOIN prestadores on inteinterbanking.cuit = prestadores.cuit
	WHERE idpresentacion = $idPresentacion";
$resTotalesCuit = mysql_query($sqlTotalesCuit);

while ($rowTotalesCuit = mysql_fetch_assoc($resTotalesCuit)) {
	$indexT = $rowTotalesCuit['cuit']."TOTAL";
	$arrayFacturas[$indexT] = $rowTotalesCuit;
}

ksort($arrayFacturas);

$file= "RETENCIONES ".$carpeta."-".$idPresentacion.".xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
?>
<body>
	<div align="center">
		<table border="1">
			<thead>
				 <tr>
				 	<th>Nº Comp.</th>
				 	<th>Tipo</th>
				 	<th>C.U.I.L.</th>
				 	<th>Periodo</th>
				 	<th>C.U.I.T.</th>
				 	<th>Fecha</th>
				 	<th>Nº</th>
				 	<th>$ Comp.</th>
				 	<th>$ Deb.</th>
				 	<th>$ Soli.</th>
				 	<th>$ No Int</th>
				 	<th>$ O.S.</th>
				 	<th>$ S.S.S</th>
				 	<th>Ret.</th>
				 	<th>$ Ret.</th>
				 	<th>$ A Pagar</th>
				 </tr>
			</thead>
			<tbody>
		<?php	foreach ($arrayFacturas as $key => $rowFactura) {  
				 	$pos = strpos($key, "TOTAL");
				 	if ($pos === false) { ?>
						<tr>
							<td><?php echo $rowFactura['nrocominterno'] ?></td>
							<td><?php echo $rowFactura['tipoarchivo'] ?></td>
							<td><?php echo $rowFactura['cuil'] ?></td>
							<td><?php echo $rowFactura['periodo'] ?></td>
							<td><?php echo $rowFactura['cuit'] ?></td>
							<td><?php echo $rowFactura['fechacomprobante'] ?></td>
							<td><?php echo $rowFactura['nrocomprobante'] ?></td>
							<td><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></td>
							<td><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></td>
							<td><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".") ?></td>					
							<td><?php echo number_format($rowFactura['impnointe'],2,",",".") ?></td>
							<td><?php echo number_format($rowFactura['impobrasocial'],2,",",".") ?></td>
							<td><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
			   <?php } else {
						$cuit = substr($key,0,11); ?>
					 	<tr>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"><b><?php echo $cuit ?></b></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></b></td>	
					 		<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></b></td>	
					 		<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".") ?></b></td>	
					 		<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impnointe'],2,",",".") ?></b></td>
					 		<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impobrasocial'],2,",",".") ?></b></td>
					 		<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></b></td>
					 		<td style="background-color: #99bfe6"><b><?php if($rowFactura['retiene'] == 1) { echo "SI"; } else { echo "NO"; } ?></b></td>
					 		<th style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impretencion'],2,".","") ?></b></th>
					 		<th style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impapagar'],2,",",".") ?></b></th>
					 	</tr>	
			   <?php  }
				 	} ?>
			</tbody>
		</table>
	</div>
</body>