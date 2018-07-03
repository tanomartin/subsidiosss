<?php 
include_once 'include/conector.php';
set_time_limit(0);
$idPresentacion = $_GET['id'];
$carpeta = $_GET['carpeta'];
$sqlFactura = "SELECT
  CASE WHEN (tipoarchivo = 'DB') THEN SUM(-impcomprobanteintegral) ELSE SUM(impcomprobanteintegral) END as impcomprobanteintegral,
  CASE WHEN (tipoarchivo = 'DB') THEN SUM(-impdebito) ELSE SUM(impdebito) END as impdebito,
  CASE WHEN (tipoarchivo = 'DB') THEN SUM(-impnointe) ELSE SUM(impnointe) END as impnointe,
  SUM(impsolicitadosubsidio) as impsolicitadosubsidio,
  SUM(impmontosubsidio) as impmontosubsidio,
	CASE
     WHEN (madera.titulares.codidelega is not null) THEN madera.titulares.codidelega
 		WHEN (madera.titularesdebaja.codidelega is not null) THEN madera.titularesdebaja.codidelega
 		WHEN (madera.titufami.codidelega is not null) THEN madera.titufami.codidelega
 		WHEN (madera.titubajafami.codidelega is not null) THEN madera.titubajafami.codidelega
 		WHEN (madera.titufamibaja.codidelega is not null) THEN madera.titufamibaja.codidelega
 		WHEN (madera.titubajafamibaja.codidelega is not null) THEN madera.titubajafamibaja.codidelega
	END as codidelega
FROM intepresentaciondetalle
LEFT JOIN madera.titulares on intepresentaciondetalle.cuil = madera.titulares.cuil
LEFT JOIN madera.titularesdebaja on intepresentaciondetalle.cuil = madera.titularesdebaja.cuil
LEFT JOIN madera.familiares on intepresentaciondetalle.cuil = madera.familiares.cuil
LEFT JOIN madera.titulares titufami on madera.familiares.nroafiliado = madera.titufami.nroafiliado
LEFT JOIN madera.titularesdebaja titubajafami on madera.familiares.nroafiliado = madera.titubajafami.nroafiliado
LEFT JOIN madera.familiaresdebaja on intepresentaciondetalle.cuil = madera.familiaresdebaja.cuil
LEFT JOIN madera.titulares titufamibaja on madera.familiaresdebaja.nroafiliado = madera.titufamibaja.nroafiliado
LEFT JOIN madera.titularesdebaja titubajafamibaja on madera.familiaresdebaja.nroafiliado = madera.titubajafamibaja.nroafiliado
WHERE idpresentacion = $idPresentacion AND deverrorintegral is null
GROUP BY codidelega
ORDER BY codidelega";
$resFactura = mysql_query($sqlFactura);

$sqlDelega = "SELECT codidelega, nombre FROM madera.delegaciones";
$reslDelega = mysql_query($sqlDelega);
$arrayDele = array();
while ($rowDelega = mysql_fetch_array($reslDelega)) {  
	$codidelega = $rowDelega['codidelega'];
	$arrayDele[$codidelega] = $rowDelega['nombre'];
}

$file= "DELE ".$carpeta."-".$idPresentacion.".xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");

?>
<body>
	<div align="center"> 	
	 	<?php include_once("include/detalle.php")?>
	 	<h3>DETALLE X DELEGACION</h3>
		<table border="1">
			 <thead>
			 	<tr>
			 		<th>Delegacion</th>
			 		<th>$ Comp.</th>
			 		<th>$ Deb.</th>
			 		<th>$ No Int.</th>
			 		<th>$ Soli.</th>
			 		<th>$ Subsidio</th>
			 		<th>$ Trans O.S.</th>
			 	</tr>
			 </thead>
			 <tbody>
			<?php 
				$totCom = 0;
				$totDeb = 0;
				$totNOI = 0;
				$totSol = 0;
				$totMonSub = 0;
				$totOS = 0;
				while ($rowFactura = mysql_fetch_array($resFactura)) {  
					$totCom += $rowFactura['impcomprobanteintegral'];
					$totDeb += $rowFactura['impdebito'];
					$totNOI += $rowFactura['impnointe'];
					$totSol += $rowFactura['impsolicitadosubsidio'];
					$totMonSub += $rowFactura['impmontosubsidio']; 
					$monOS =  $rowFactura['impcomprobanteintegral'] - $rowFactura['impdebito'] -  $rowFactura['impnointe'] - $rowFactura['impmontosubsidio']; 
					$totOS += $monOS;
					
					$delegacion = "3200 - DELEGACION AUXILIAR";
					if ($rowFactura['codidelega'] != NULL) {
						$delegacion = $rowFactura['codidelega']." - ".$arrayDele[$rowFactura['codidelega']];
					} ?>
					<tr>
						<td><?php echo $delegacion ?> </td>
						<td><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></td>
						<td><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></td>
						<td><?php echo number_format($rowFactura['impnointe'],2,",",".") ?></td>
						<td><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".") ?></td>
						<td><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></td>
						<td><?php echo number_format($monOS,2,",",".") ?></td>
					</tr>
		  <?php } ?>
			  		<tr>
			  			<th>TOTAL</th>
			  			<th><?php echo number_format($totCom,2,",",".") ?></th>
			  			<th><?php echo number_format($totDeb,2,",",".") ?></th>
			  			<th><?php echo number_format($totNOI,2,",",".") ?></th>
			  			<th><?php echo number_format($totSol,2,",",".") ?></th>
			  			<th><?php echo number_format($totMonSub,2,",",".") ?></th>
			  			<th><?php echo number_format($totOS,2,",",".") ?></th>
			  		</tr>
			  		<tr>
			  			<th rowspan="2">CONTROL</th>
			  			<th><?php echo "COM" ?></th>
			  			<th colspan="3"><?php echo "DEB+SOL+NOI"; ?></th>
			  			<th colspan="2"><?php echo "NOI+OS+SUB+DEB"; ?></th>
			  	 	</tr>
			  		<tr>
			  			<th><?php echo number_format($totCom,2,",",".") ?></th>
			  			<th colspan="3"><?php echo number_format($totDeb + $totNOI + $totSol,2,",",".") ?></th>
			  			<th colspan="2"><?php echo number_format($totMonSub + $totOS + $totDeb + $totNOI ,2,",",".") ?></th>
			  		</tr>
			  	</tbody>
			</table>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>