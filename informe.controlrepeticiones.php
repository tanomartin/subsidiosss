<?php 
include_once 'include/conector.php';
set_time_limit(0);

$sqlRepeticiones = "SELECT 
						tipoarchivo,periodo,cuit,cuil,codpractica,count(*) as cantidad,
						sum(impsolicitadointegral) as solicitado, sum(impmontosubsidio) as subsidio, 
						if (tipoarchivo = 'DB',sum(impsolicitadointegral) + sum(impmontosubsidio) ,sum(impsolicitadointegral) - sum(impmontosubsidio)) as diferencia
					FROM intepresentaciondetalle i, interendicioncontrol s
					WHERE i.idpresentacion = s.idpresentacion and codpractica not in (90,96)
					GROUP BY tipoarchivo,periodo,cuit,cuil,codpractica 
					HAVING cantidad > 1 and solicitado > subsidio
					ORDER BY diferencia ASC, periodo ASC";
$resRepeticiones = mysql_query($sqlRepeticiones);

$today = date("m-d-y");
$file= "Control Repeticiones $today.xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");

?>
<body>
	<div align="center">
		<table border="1">
			<thead>
				<tr>
				 	<th>Tipo</th>
				 	<th>Periodo</th>
				 	<th>C.U.I.T.</th>
				 	<th>C.U.I.L.</th>
				 	<th>Cod. Practica</th>
				 	<th>Cant. Repe.</th>
				 	<th>Total Solicitado</th>
				 	<th>Total Reintegro</th>
				 	<th>Diferencia</th>
				 	<th>Observacion</th>
				</tr>
			</thead>
			<tbody>
			<?php while ($rowRepeticiones = mysql_fetch_assoc($resRepeticiones)) {  ?>
				<tr>
			 		<td><?php echo $rowRepeticiones['tipoarchivo'] ?></td>
			 		<td><?php echo $rowRepeticiones['periodo'] ?></td>
			 		<td><?php echo $rowRepeticiones['cuit'] ?></td>
			 		<td><?php echo $rowRepeticiones['cuil'] ?></td>
			 		<td><?php echo $rowRepeticiones['codpractica'] ?></td>
			 		<td><?php echo $rowRepeticiones['cantidad'] ?></td>
			 		<td><?php echo number_format($rowRepeticiones['solicitado'],"2",",",".") ?></td>
			 		<td><?php echo number_format($rowRepeticiones['subsidio'],"2",",",".") ?></td>
			 		<td><?php echo number_format($rowRepeticiones['diferencia'],"2",",",".") ?></td>
			 		<td></td>
			 	</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</body>
