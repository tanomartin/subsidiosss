<?php include_once 'include/conector.php';
set_time_limit(0);

$sqlPendientes = "SELECT inteinterbanking.*,
						 DATE_FORMAT(inteinterbanking.fechaenvio, '%d-%m-%Y') as fechaenvio,
						 madera.prestadoresauxiliar.cbu,
						 DATE_FORMAT(madera.prestadoresauxiliar.fechainterbanking, '%d-%m-%Y') as fechainterbanking
				  FROM inteinterbanking
				  LEFT JOIN madera.prestadores on inteinterbanking.cuit = madera.prestadores.cuit
				  LEFT JOIN madera.prestadoresauxiliar on madera.prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
				  WHERE idpago is null ORDER BY idpresentacion";
$resPendientes = mysql_query($sqlPendientes);

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
				<th>C.U.I.T.</th>
				<th>C.B.U.</th>
				<th>Interbanking</th>
				<th>$ Comp.</th>
				<th>$ Deb.</th>
				<th>$ No Int</th>
				<th>$ Soli.</th>
				<th>$ O.S.</th>
				<th>$ S.S.S</th>
				<th>$ Ret.</th>
				<th>$ A Pagar</th>
			</thead>
			<tbody>
			<?php while ($rowPendientes = mysql_fetch_assoc($resPendientes)) { ?>
					<tr>
						<td><?php echo $rowPendientes['idpresentacion'] ?></td>
						<td><?php echo $rowPendientes['cuit'] ?></td>
						<td><?php if ($rowPendientes['cbu'] != null) { echo "'".$rowPendientes['cbu']."'"; } ?></td>
						<td><?php echo $rowPendientes['fechainterbanking'] ?></td>
						<td><?php echo number_format($rowPendientes['impcomprobanteintegral'],2,",",".") ?></td>
						<td><?php echo number_format($rowPendientes['impdebito'],2,",",".") ?></td>
						<td><?php echo number_format($rowPendientes['impnointe'],2,",",".") ?></td>
						<td><?php echo number_format($rowPendientes['impsolicitadosubsidio'],2,",",".") ?></td>					
						<td><?php echo number_format($rowPendientes['impobrasocial'],2,",",".") ?></td>
						<td><?php echo number_format($rowPendientes['impmontosubsidio'],2,",",".") ?></td>				
						<td><?php echo number_format($rowPendientes['impretencion'],2,",",".") ?></td>
						<td><?php echo number_format($rowPendientes['impapagar'],2,",",".") ?></td>
					</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</body>