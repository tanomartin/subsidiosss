<?php include_once 'include/conector.php';
set_time_limit(0);

$sqlPresPagos = "SELECT idpresentacion FROM intepagosdetalle GROUP BY idpresentacion";
$resPresPagos = mysql_query($sqlPresPagos);
$whereInPres = "(";
while ($rowPresPagos = mysql_fetch_assoc($resPresPagos)) {
	$whereInPres .= $rowPresPagos['idpresentacion'].",";
}
$whereInPres = substr($whereInPres, 0, -1);
$whereInPres .= ")";

$sqlPendientes = "SELECT * 
					FROM intepresentaciondetalle i, interendicioncontrol r
					WHERE
						r.idpresentacion in $whereInPres AND
						i.idpresentacion = r.idpresentacion AND 
						i.codpractica not in (97,98,99) AND
						i.deverrorintegral IS NULL AND 
						i.tipoarchivo != 'DB' AND
						i.nrocominterno NOT IN (select nrocominterno FROM intepagosdetalle) 
					ORDER BY i.idpresentacion, i.nrocominterno";
$resPendientes = mysql_query($sqlPendientes);

$arrayReversiones = array();
$sqlReversionesFuturas = "SELECT d.nrocominterno, d.idpresentacion
							FROM intepresentaciondetalle d, intepresentacion p, interendicioncontrol r
							WHERE d.tipoarchivo = 'DB' AND p.id = r.idpresentacion AND
							d.idpresentacion = p.id AND p.fechacancelacion is null";
$resReversionesFuturas = mysql_query($sqlReversionesFuturas);
$canReversionesFuturas = mysql_num_rows($resReversionesFuturas);
if ($canReversionesFuturas > 0) {
	while ($rowReversionesFuturas = mysql_fetch_assoc($resReversionesFuturas)) {
		$arrayReversiones[$rowReversionesFuturas['nrocominterno']] = $rowReversionesFuturas['idpresentacion'];
	}
}



$today = date("m-d-y");
$file= "Pagos Pendientes al $today.xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
?>

<body>
	<div align="center">
		<table border="1">
			<thead>
				<th>Pres</th>
				<th>Com. Int.</th>
				<th>C.U.I.T.</th>
				<th>$ Comp.</th>
				<th>$ Deb.</th>
				<th>$ No Int</th>
				<th>$ Soli.</th>
				<th>$ O.S.</th>
				<th>$ S.S.S</th>
				<th>$ A Pagar</th>
			</thead>
			<tbody>
			<?php while ($rowPendientes = mysql_fetch_assoc($resPendientes)) {
					if (!array_key_exists ($rowPendientes['nrocominterno'], $arrayReversiones)) { ?>
					<tr>
						<td><?php echo $rowPendientes['idpresentacion'] ?></td>
						<td><?php echo $rowPendientes['nrocominterno'] ?></td>
						<td><?php echo $rowPendientes['cuit'] ?></td>
						<td><?php echo number_format($rowPendientes['impcomprobanteintegral'],2,",",".") ?></td>
						<td><?php echo number_format($rowPendientes['impdebito'],2,",",".") ?></td>
						<td><?php echo number_format($rowPendientes['impnointe'],2,",",".") ?></td>
						<td><?php echo number_format($rowPendientes['impsolicitadosubsidio'],2,",",".") ?></td>					
						<td><?php echo number_format($rowPendientes['impcomprobanteintegral'] - $rowPendientes['impdebito'] - $rowPendientes['impmontosubsidio'],2,",",".") ?></td>
						<td><?php echo number_format($rowPendientes['impmontosubsidio'],2,",",".") ?></td>				
						<td><?php echo number_format($rowPendientes['impcomprobanteintegral'] - $rowPendientes['impdebito'],2,",",".") ?></td>
					</tr>
			<?php	}
				 } ?>
			</tbody>
		</table>
	</div>
</body>