<?php 
include_once 'include/conector.php';
set_time_limit(0);

$sqlRecibos = "SELECT
f.idpresentacion,
c.carpeta,
c.periodo,
prestadores.nombre,
f.cuit,
f.nrocominterno,
f.cuil,
titulares.codidelega as deletitu, 
titufami.codidelega as delefami,
m.descripcion,
f.nrocomprobante,
f.periodo,
f.impcomprobante,
p.nrotransferencia,
p.importepagado,
DATE_FORMAT(p.fechatransferencia,'%d-%m-%Y') as fechatrasferencia,
prestadores.email1,
prestadores.email2,
prestadores.ddn1,
prestadores.telefono1,
prestadores.ddn2,
prestadores.telefono2
FROM intepagos p, intepresentacion pre, intecronograma c, tipocomprobante m, intepresentaciondetalle f
LEFT JOIN titulares on f.cuil = titulares.cuil
LEFT JOIN familiares on f.cuil = familiares.cuil
LEFT JOIN titulares titufami on  familiares.nroafiliado = titufami.nroafiliado
LEFT JOIN prestadores on f.cuit = prestadores.cuit
WHERE
  p.recibo = '' and
  p.nrocominterno = f.nrocominterno and 
  p.idpresentacion = f.idpresentacion and
  codpractica not in (97,98,99) and
  f.impsolicitadosubsidio is not null and
  f.impmontosubsidio is not null and
  f.idpresentacion = pre.id and
  pre.idcronograma = c.id and
  f.tipocomprobante = m.id
group by f.cuit, f.nrocomprobante
order by f.idpresentacion, f.cuit";
$resRecibos = mysql_query($sqlRecibos);

$today = date("m-d-y");
$file= "Recibos Aduedados al $today.xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");

?>
<body>
	<div align="center">
		<h2>Recibos Adeudados</h2>
		<table border="1">
			<thead>
				<tr>
				 	<th>Id. Pres.</th>
				 	<th>Carpeta</th>
				 	<th>Periodo</th>
				 	<th>Prestador</th>
				 	<th>C.U.I.T.</th>
				 	<th>Nro. Interno</th>
				 	<th>C.U.I.L.</th>
				 	<th>Delegacion</th>
				 	<th>Tipo Compr.</th>
				 	<th>Periodo</th>
				 	<th>Nro Compr.</th>
				 	<th>$ Comprobante</th>
				 	<th>Nro Transf</th>
				 	<th>$ Transferido</th>
				 	<th>Fecha</th>
				 	<th>Emails</th>
				 	<th>Telefonos</th>
				 	<th>Observacion</th>
				</tr>
			</thead>
			<tbody>
			<?php while ($rowRecibo = mysql_fetch_assoc($resRecibos)) {  ?>
				<tr>
			 		<td><?php echo $rowRecibo['idpresentacion'] ?></td>
			 		<td><?php echo $rowRecibo['carpeta'] ?></td>
			 		<td><?php echo $rowRecibo['periodo'] ?></td>
			 		<td><?php echo $rowRecibo['nombre'] ?></td>
			 		<td><?php echo $rowRecibo['cuit'] ?></td>
			 		<td><?php echo $rowRecibo['nrocominterno'] ?></td>
			 		<td><?php echo $rowRecibo['cuil'] ?></td>
			 		<td><?php echo $rowRecibo['deletitu']." ".$rowRecibo['delefami'] ?></td>
			 		<td><?php echo $rowRecibo['descripcion'] ?></td>
			 		<td><?php echo $rowRecibo['periodo'] ?></td>
			 		<td><?php echo $rowRecibo['nrocomprobante'] ?></td>
			 		<td><?php echo number_format($rowRecibo['impcomprobante'],"2",",",".") ?></td>
			 		<td><?php echo $rowRecibo['nrotransferencia'] ?></td>
			 		<td><?php echo number_format($rowRecibo['importepagado'],"2",",",".") ?></td>
			 		<td><?php echo $rowRecibo['fechatrasferencia'] ?></td>
			 		<?php $emails =  $rowRecibo['email1']." ".$rowRecibo['email2'];
			 		  if ($rowRecibo['email1']!= "" && $rowRecibo['email2']!= "") {
			 				$emails = $rowRecibo['email1']." | ".$rowRecibo['email2'];
					  } ?>
			 		<td><?php echo $emails ?></td>
			 		<?php $telefono1 = "(".$rowRecibo['ddn1'].") ".$rowRecibo['telefono1'];
			 			  $telefono2 = "(".$rowRecibo['ddn2'].") ".$rowRecibo['telefono2']; 
			 			  if ($telefono1 == "() ") { $telefono1 = ""; } 
 						  if ($telefono2 == "() ") { $telefono2 = ""; }
 						  $telefonos = $telefono1." ".$telefono2;
 						  if ($telefono1 != "" && $telefono2 != "") { 
 						  		$telefonos = $telefono1." | ".$telefono2; 
 						  }  ?>
			 		<td><?php echo $telefonos ?></td>
			 		<td></td>
			 	</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</body>