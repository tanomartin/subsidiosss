<?php 
include_once 'include/conector.php';
set_time_limit(0);

$sqlRecibos = "SELECT
	f.idpresentacion,
	c.carpeta,
	c.periodo as periodocarpeta,
	madera.prestadores.nombre,
	f.cuit,
	f.nrocominterno,
	f.cuil,
	CASE
     	WHEN (madera.titulares.codidelega is not null) THEN madera.titulares.codidelega
 	 	WHEN (madera.titularesdebaja.codidelega is not null) THEN madera.titularesdebaja.codidelega
 		WHEN (madera.titufami.codidelega is not null) THEN madera.titufami.codidelega
 		WHEN (madera.titubajafami.codidelega is not null) THEN madera.titubajafami.codidelega
 		WHEN (madera.titufamibaja.codidelega is not null) THEN madera.titufamibaja.codidelega
 		WHEN (madera.titubajafamibaja.codidelega is not null) THEN madera.titubajafamibaja.codidelega
	END as codidelega,
	m.descripcion,
	f.nrocomprobante,
	f.periodo,
	f.impcomprobante,
	pc.nrotransferencia,
	DATE_FORMAT(pc.fechatransferencia,'%d-%m-%Y') as fechatrasferencia,
	madera.prestadores.email1,
	madera.prestadores.email2,
	madera.prestadores.ddn1,
	madera.prestadores.telefono1,
	madera.prestadores.ddn2,
	madera.prestadores.telefono2
FROM 
	intepagosdetalle p, 
	intepagoscabecera pc, 
	intepresentacion pre, 
	intecronograma c, 
	madera.tipocomprobante m, 
	intepresentaciondetalle f
		
LEFT JOIN madera.titulares on f.cuil = madera.titulares.cuil
LEFT JOIN madera.titularesdebaja on f.cuil = madera.titularesdebaja.cuil
LEFT JOIN madera.familiares on f.cuil = madera.familiares.cuil
LEFT JOIN madera.titulares titufami on madera.familiares.nroafiliado = madera.titufami.nroafiliado
LEFT JOIN madera.titularesdebaja titubajafami on madera.familiares.nroafiliado = madera.titubajafami.nroafiliado
LEFT JOIN madera.familiaresdebaja on f.cuil = madera.familiaresdebaja.cuil
LEFT JOIN madera.titulares titufamibaja on madera.familiaresdebaja.nroafiliado = madera.titufamibaja.nroafiliado
LEFT JOIN madera.titularesdebaja titubajafamibaja on madera.familiaresdebaja.nroafiliado = madera.titubajafamibaja.nroafiliado
LEFT JOIN madera.prestadores on f.cuit = madera.prestadores.cuit	
WHERE
	p.recibo is null and
	p.nrocominterno = f.nrocominterno and
	p.idpresentacion = f.idpresentacion and 
	p.idpresentacion = pc.idpresentacion and 
	p.nroordenpago = pc.nroordenpago and
	f.codpractica not in (97,98,99) and
	f.impsolicitadosubsidio is not null and
	f.impmontosubsidio is not null and
	f.idpresentacion = pre.id and
	pre.idcronograma = c.id and
	f.tipocomprobante = m.id
GROUP BY f.cuit, f.nrocomprobante
ORDER BY f.idpresentacion, f.cuit";
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
			 		<td><?php echo $rowRecibo['periodocarpeta'] ?></td>
			 		<td><?php echo $rowRecibo['nombre'] ?></td>
			 		<td><?php echo $rowRecibo['cuit'] ?></td>
			 		<td><?php echo $rowRecibo['nrocominterno'] ?></td>
			 		<td><?php echo $rowRecibo['cuil'] ?></td>
			 		<td><?php echo $rowRecibo['codidelega'] ?></td>
			 		<td><?php echo $rowRecibo['descripcion'] ?></td>
			 		<td><?php echo $rowRecibo['periodo'] ?></td>
			 		<td><?php echo $rowRecibo['nrocomprobante'] ?></td>
			 		<td><?php echo number_format($rowRecibo['impcomprobante'],"2",",",".") ?></td>
			 		<td><?php echo $rowRecibo['nrotransferencia'] ?></td>
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