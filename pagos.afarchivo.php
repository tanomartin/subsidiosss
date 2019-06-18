<?php include_once 'include/conector.php';
set_time_limit(0);

$idPresentacion = $_GET['id'];
$carpeta = $_GET['carpeta'];

$sqlApliFondo = "SELECT
					d.nrocominterno,
					i.clave,
					i.tipoarchivo,
					i.periodopresentacion,
				 	i.periodoprestacion,
					i.cuil,
					i.codpractica,
					p.descripcion,
					i.impsubsidiado,
					d.impmontosubsidio,
					d.periodo,
					i.cuit,
					i.tipocomprobante,
					i.puntoventa,
					i.nrocomprobante,
					i.impsolicitado,
					madera.prestadoresauxiliar.cbu,
					intepagosdetalle.nroordenpago, 
					DATE_FORMAT(intepagoscabecera.fechatransferencia,'%d-%m-%Y') as fechatransferencia,
					IFNULL(intepagosdetalle.impretencion,0) as impretencion,
					(d.impmontosubsidio - IFNULL(intepagosdetalle.impretencion,0)) as imppago,
					IF(i.tipoarchivo != 'DB', IF((d.impsolicitado - d.impmontosubsidio)<0,0,(d.impsolicitado - d.impmontosubsidio)),d.impsolicitado + d.impmontosubsidio) as impos,
					0 as impoc,
					intepagosdetalle.recibo,
					intepagosdetalle.imprecupero
				 FROM interendicion i, madera.practicas p, intepresentaciondetalle d
				 LEFT JOIN intepagosdetalle ON d.idpresentacion = intepagosdetalle.idpresentacion AND 
											   d.nrocominterno = intepagosdetalle.nrocominterno
				 LEFT JOIN intepagoscabecera ON intepagosdetalle.idpresentacion = intepagoscabecera.idpresentacion AND 
												intepagosdetalle.nroordenpago = intepagoscabecera.nroordenpago
				 LEFT JOIN madera.prestadores ON d.cuit = madera.prestadores.cuit
				 LEFT JOIN madera.prestadoresauxiliar ON madera.prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
				 WHERE
					i.idpresentacion = $idPresentacion and
					i.idpresentacion = d.idpresentacion and
					i.tipoarchivo = d.tipoarchivo and
					i.periodoprestacion = d.periodo and
					i.cuil = d.cuil and
					i.codpractica = d.codpractica and
					i.cuit = d.cuit and
					i.tipocomprobante = d.tipocomprobante and
					i.puntoventa = d.puntoventa and
					i.nrocomprobante = d.nrocomprobante and 
					p.nomenclador = 7 and i.codpractica = p.codigopractica
				 ORDER BY i.clave";
$resApliFondo = mysql_query($sqlApliFondo);
$canApliFondo = mysql_num_rows($resApliFondo);

$arrayReversiones = array();
$sqlReversionesFuturas = "SELECT d.nrocominterno
							FROM intepresentaciondetalle d, intepresentacion p
							WHERE d.idpresentacion > $idPresentacion AND 
								  d.tipoarchivo = 'DB' AND 
 								  d.idpresentacion = p.id AND
								  p.fechacancelacion is NULL";
$resReversionesFuturas = mysql_query($sqlReversionesFuturas);
$canReversionesFuturas = mysql_num_rows($resReversionesFuturas);
if ($canReversionesFuturas > 0) {
	while ($rowReversionesFuturas = mysql_fetch_assoc($resReversionesFuturas)) {
		$arrayReversiones[$rowReversionesFuturas['nrocominterno']] = $rowReversionesFuturas['nrocominterno'];
	}
}

$arrayCredito = array();
$sqlDebitos = "SELECT d.nrocominterno FROM intepresentaciondetalle d 
				WHERE d.idpresentacion = $idPresentacion AND d.tipoarchivo = 'DB'";
$resDebitos = mysql_query($sqlDebitos);
$canDebitos = mysql_num_rows($resDebitos);
if ($canDebitos > 0) {
	$whereIn = "(";
	while ($rowDebitos = mysql_fetch_assoc($resDebitos)) {
		$whereIn .= $rowDebitos['nrocominterno'].",";
	}
	$whereIn = substr($whereIn, 0, -1);
	$whereIn .= ")";
	
	$sqlCredito = "SELECT d.nrocominterno FROM intepresentaciondetalle d
					WHERE d.idpresentacion = $idPresentacion AND d.tipoarchivo != 'DB' and nrocominterno in $whereIn";
	$resCredito = mysql_query($sqlCredito);
	$canCredito = mysql_num_rows($resCredito);
	if ($canCredito > 0) {
		$whereIn = "(";
		while ($rowCredito = mysql_fetch_assoc($resCredito)) {
			$arrayCredito[$rowCredito['nrocominterno']] = 0;
			$whereIn .= $rowCredito['nrocominterno'].",";
		}
		$whereIn = substr($whereIn, 0, -1);
		$whereIn .= ")";
		
		/*$sqlCreditoMontos = "SELECT d.nrocominterno, d.impmontosubsidio FROM intepresentaciondetalle d
							WHERE d.idpresentacion < $idPresentacion AND d.tipoarchivo = 'DS' and nrocominterno in $whereIn"; 
		$resCreditoMontos = mysql_query($sqlCreditoMontos);
		$canCreditoMontos = mysql_num_rows($resCreditoMontos);
		if ($canCreditoMontos > 0) {
			while ($rowCreditoMontos = mysql_fetch_assoc($resCreditoMontos)) {
				$arrayCredito[$rowCreditoMontos['nrocominterno']] += $rowCreditoMontos['impmontosubsidio'];
			}
		}*/
	}
}
//var_dump($arrayCredito);
$today = date("m-d-y");
$file= "Aplicacion de fondo $carpeta al $today.xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
?>

<body>
	<div align="center">
		<table border="1">
			<thead>
				<tr>
					<th style="background-color: silver;">NRO. COM. INT.</th>
				 	<th style="background-color: aqua;">TIPO</BR>REG</th>
				 	<th style="background-color: aqua;">PERIODO</BR>PRESENTAC.</th>
				 	<th style="background-color: aqua;">PERIODO</BR>PRESTACION</th>			 	
				 	<th style="background-color: aqua;">CUIL</BR>BENEFIC</th>
				 	<th style="background-color: aqua;">COD</BR>PRACTICA</th>
				 	<th style="background-color: aqua;">PRACTICA</th>
				 	<th style="background-color: aqua;">IMPORTE</BR>LIQUIDADO</th>
				 	<th style="background-color: lime;">PERIODO</BR>FC</th>
				 	<th style="background-color: lime;">CUIT</th>
				 	<th style="background-color: lime;">TIPO</th>
				 	<th style="background-color: lime;">P</BR>V</th>
				 	<th style="background-color: lime;">Nº FC</th>
				 	<th style="background-color: lime;">IMPORTE</BR>SOLICITADO</th>
				 	<th style="background-color: lime;">CLAVE UNICA REGISTRO</th>
				 	<th>CBU</th>
				 	<th>OR. DE</BR>PAGO</th>
				 	<th>FECHA</BR>TRANSF.</th>
				 	<th>IMPORTE</BR>TRANSF.</th>
				 	<th>RETENCION</BR>GCIAS.</th>
				 	<th>RETENCION</BR>INGRESOS</BR>BRUTOS</th>
				 	<th>IMPORTE</BR>APLICADO</BR>SSSALUD</th>
				 	<th style="background-color: yellow;">IMPORTE</BR>FONDOS</BR>PROPIOS EN</BR>CTA. DISC.</th>
				 	<th style="background-color: yellow;">IMPORTE</BR>FONDOS</BR>OTRO</BR>CUENTA</th>
				 	<th>NRO.</BR>RECIBO</th>
				 	<th>IMPORTE</BR>TRASLADADO</BR>(REVERSION)</th>
				 	<th>IMPORTE</BR>DEVUELTO</BR>CTA SSS</th>
				 	<th>SALDO NO</BR>APLICADO</th>
				 	<th>RECUPERO</BR>FONDOS</BR>PROPIOS</th>
				 	<th style="background-color: silver;">C 622</th>
				 	<th style="background-color: silver;">C 623</th>
				</tr>
			</thead>
			<tbody>
	<?php	$totImpSubidiado = 0;
			$totImpSolicitado = 0;
			$totImpTransferido = 0;
			$totRetGanancias = 0;
			$totRetIB = 0;
			$totImpSSS = 0;
			$totImpOS = 0;
			$totImpOC = 0;
			$totImpTransladado = 0;
			$totImpDevueltoSSS = 0;
			$totImpNoAplicado = 0;
			$totRecuperoFondos = 0;
			
			$totC622 = 0;
			$totC623 = 0;
			while ($rowApliFondo = mysql_fetch_assoc($resApliFondo)) {  
				$impDevolucionSSS = 0;
				$especial = false;
				
				/*if (array_key_exists($rowApliFondo['nrocominterno'],$arrayCredito) && $rowApliFondo['tipoarchivo'] != "DB") {
					$rowApliFondo['impmontosubsidio'] += $arrayCredito[$rowApliFondo['nrocominterno']];
					$rowApliFondo['imppago'] = $rowApliFondo['impmontosubsidio'] - $rowApliFondo['impretencion'];
					$rowApliFondo['impos'] = $rowApliFondo['impsolicitado'] - $rowApliFondo['impmontosubsidio'];
				}*/
				
				if ($rowApliFondo['tipoarchivo'] == "DB") {	
					$rowApliFondo['impos'] = (-1)*$rowApliFondo['impos'];
					$rowApliFondo['recibo'] = "";
					$rowApliFondo['cbu'] = "";
					$rowApliFondo['nroordenpago'] = "";
					$rowApliFondo['fechatransferencia'] = "";
					if (!array_key_exists($rowApliFondo['nrocominterno'],$arrayCredito)) {
						$impDevolucionSSS = $rowApliFondo['impmontosubsidio'];
						$rowApliFondo['impoc'] = $rowApliFondo['impsolicitado'] - $rowApliFondo['impmontosubsidio'];
						$especial = true;
					} else {
						$rowApliFondo['impos'] = 0;
						$rowApliFondo['imprecupero'] = 0;
					}
				}
				
				$impTransladado = 0;
				$impNoAplicado = 0;
				if ($rowApliFondo['nroordenpago'] == NULL and $rowApliFondo['tipoarchivo'] != "DB") {
					$especial = true;
					if (in_array($rowApliFondo['nrocominterno'],$arrayReversiones)) {
						$impTransladado = $rowApliFondo['impmontosubsidio'];
					} else {
						$impNoAplicado =  $rowApliFondo['impmontosubsidio'];
					}
					//$rowApliFondo['impos'] = 0;
				}
				
				if ($especial) {
					$rowApliFondo['imppago'] = 0;
					$rowApliFondo['impretencion'] = 0;
					$rowApliFondo['impmontosubsidio'] = 0;
					$rowApliFondo['impoc'] = 0;
				}
				
				$totImpSubidiado += $rowApliFondo['impsubsidiado'];
				$totImpSolicitado += $rowApliFondo['impsolicitado'];
				$totImpTransferido += $rowApliFondo['imppago'];
				$totRetGanancias += $rowApliFondo['impretencion'];
				$totImpSSS += $rowApliFondo['impmontosubsidio'];
				$totImpOS += $rowApliFondo['impos'];
				$totImpOC += $rowApliFondo['impoc'];
				$totRecuperoFondos += $rowApliFondo['imprecupero'];				
				
				$totImpTransladado += $impTransladado;
				$totImpDevueltoSSS += $impDevolucionSSS;
				$totImpNoAplicado += $impNoAplicado; ?>
				<tr>
					<td style="background-color: silver;"><?php echo $rowApliFondo['nrocominterno']?></td>
					<td style="background-color: aqua;"><?php echo $rowApliFondo['tipoarchivo']?></td>
					<td style="background-color: aqua;"><?php echo $rowApliFondo['periodopresentacion']?></td>
					<td style="background-color: aqua;"><?php echo $rowApliFondo['periodoprestacion']?></td>
					<td style="background-color: aqua;"><?php echo $rowApliFondo['cuil']?></td>
					<td style="background-color: aqua;"><?php echo $rowApliFondo['codpractica']?></td>
					<td style="background-color: aqua;"><?php echo $rowApliFondo['descripcion']?></td>			
					<td style="background-color: aqua;"><?php echo number_format($rowApliFondo['impsubsidiado'],2,",",".");?></td>
					<td style="background-color: lime;"><?php echo $rowApliFondo['periodo']?></td>
					<td style="background-color: lime;"><?php echo $rowApliFondo['cuit']?></td>
					<td style="background-color: lime;"><?php echo $rowApliFondo['tipocomprobante']?></td>
					<td style="background-color: lime;"><?php echo $rowApliFondo['puntoventa']?></td>
					<td style="background-color: lime;"><?php echo $rowApliFondo['nrocomprobante']?></td>
					<td style="background-color: lime;"><?php echo number_format($rowApliFondo['impsolicitado'],2,",","."); ?></td>
					<td style="background-color: lime;"><?php echo $rowApliFondo['clave']?></td>
					<td><?php echo $rowApliFondo['cbu']?></td>
					<td><?php echo $rowApliFondo['nroordenpago']?></td>
					<td><?php echo $rowApliFondo['fechatransferencia']?></td>
					<td><?php echo number_format($rowApliFondo['imppago'],2,",","."); ?></td>
					<td><?php echo number_format($rowApliFondo['impretencion'],2,",","."); ?></td>
					<td><?php echo "0,00" ?></td>
					<td><?php echo number_format($rowApliFondo['impmontosubsidio'],2,",",".");  ?></td>
					<td style="background-color: yellow;"><?php echo number_format($rowApliFondo['impos'],2,",","."); ?></td>
					<td style="background-color: yellow;"><?php echo number_format($rowApliFondo['impoc'],2,",","."); ?></td>
					<td><?php echo $rowApliFondo['recibo'] ?></td>	
					<td><?php echo number_format($impTransladado,2,",","."); ?></td>
					<td><?php echo number_format($impDevolucionSSS,2,",","."); ?></td>
					<td><?php echo number_format($impNoAplicado,2,",","."); ?></td>
					<td><?php echo number_format($rowApliFondo['imprecupero'],2,",","."); ?></td>
					<?php $C622 = $rowApliFondo['impmontosubsidio']-($rowApliFondo['imppago']+$rowApliFondo['impretencion']); 
						  $totC622 += $C622;?>
					<td style="background-color: silver;"><?php echo number_format($C622,2,",","."); ?></td>
					<?php //$C623 = 0;
						  //if ($rowApliFondo['tipoarchivo'] != "DB") {
							$C623 = $rowApliFondo['impsolicitado'] - ($rowApliFondo['impos']+$rowApliFondo['impoc']+$rowApliFondo['impmontosubsidio']+$impTransladado+$impDevolucionSSS+$impNoAplicado+$rowApliFondo['imprecupero']);
						 // }
						  $totC623 += $C623;?>
					<td style="background-color: silver;"><?php echo number_format($C623,2,",","."); ?></td>
				</tr>
	  <?php } ?>
			</tbody>
			<thead>
				<th style="background-color: silver;"><?php echo "TOT REG: ".$canApliFondo; ?></th>
				<th style="background-color: aqua;"></th>
				<th style="background-color: aqua;"></th>
				<th style="background-color: aqua;"></th>
				<th style="background-color: aqua;"></th>
				<th style="background-color: aqua;"></th>
				<th style="background-color: aqua;"></th>
				<th style="background-color: aqua;"><?php echo "A: ".number_format($totImpSubidiado,2,",","."); ?></th>
				<th style="background-color: lime;"></th>
				<th style="background-color: lime;"></th>
				<th style="background-color: lime;"></th>
				<th style="background-color: lime;"></th>
				<th style="background-color: lime;"></th>
				<th style="background-color: lime;"><?php echo "B: ".number_format($totImpSolicitado,2,",","."); ?></th>
				<th style="background-color: lime;"></th>
				<th></th>
				<th></th>
				<th></th>
				<th><?php echo "C: ".number_format($totImpTransferido,2,",","."); ?></th>
				<th><?php echo "D: ".number_format($totRetGanancias,2,",","."); ?></th>
				<th><?php echo "E: ".number_format($totRetIB,2,",","."); ?></th>
				<th><?php echo "F: ".number_format($totImpSSS,2,",","."); ?></th>
				<th style="background-color: yellow;"><?php echo "G: ".number_format($totImpOS,2,",","."); ?></th>
				<th style="background-color: yellow;"><?php echo "H: ".number_format($totImpOC,2,",","."); ?></th>
				<th></th>
				<th><?php echo "I: ".number_format($totImpTransladado,2,",","."); ?></th>
				<th><?php echo "J: ".number_format($totImpDevueltoSSS,2,",","."); ?></th>
				<th><?php echo "K: ".number_format($totImpNoAplicado,2,",","."); ?></th>
				<th><?php echo "L: ".number_format($totRecuperoFondos,2,",","."); ?></th>
				<th style="background-color: silver;"><?php echo number_format($totC622,2,",","."); ?></th>
				<th style="background-color: silver;"><?php echo number_format($totC623,2,",","."); ?></th>
			</thead>
		</table>
		<table border="1" style="text-align: center; margin-top: 15px">
			<tr>
				<th colspan="4">CONTROLES</th>
			</tr>
			<tr>
				<th>CONTROL</th>
				<th>A</th>
				<th>F+I+J+K+L</th>
				<th>DIFERENCIA</th> 
			</tr>
			<tr>
				<td><?php echo "624"; ?></td>
				<td><?php echo number_format($totImpSubidiado,2,",","."); ?></td>
				<td><?php $FIJKL = $totImpSSS+$totImpTransladado+$totImpDevueltoSSS+$totImpNoAplicado+$totRecuperoFondos;
						  echo number_format($FIJKL,2,",","."); ?></td>
				<td><?php echo number_format($totImpSubidiado-$FIJKL,2,",","."); ?></td>
			</tr>
			<tr>
				<th>CONTROL</th>
				<th>B</th>
				<th>A+G+H</th>
				<th>DIFERENCIA</th> 
			</tr>		
			<tr>
				<td><?php echo "623"; ?></td>
				<td><?php echo number_format($totImpSolicitado,2,",","."); ?></td>
				<td><?php $AGH = $totImpSubidiado+$totImpOS+$totImpOC;
						  echo number_format($AGH,2,",","."); ?></td>
				<td><?php echo number_format($totImpSolicitado-$AGH,2,",","."); ?></td>
			</tr>
			<tr>
				<th>CONTROL</th>
				<th>F</th>
				<th>C+D+E</th>
				<th>DIFERENCIA</th> 
			</tr>		
			<tr>
				<td><?php echo "622"; ?></td>
				<td><?php echo number_format($totImpSSS,2,",","."); ?></td>
				<td><?php $CDE = $totImpTransferido+$totRetGanancias+$totRetIB;
						  echo number_format($CDE,2,",","."); ?></td>
				<td><?php echo number_format($totImpSSS-$CDE,2,",","."); ?></td>
			</tr>
		</table>
	</div>
</body>