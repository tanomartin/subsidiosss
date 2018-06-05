<?php 
include_once 'include/conector.php';
set_time_limit(0);

$sqlRecibos = "SELECT
	f.idpresentacion,
	c.carpeta,
	c.periodo as periodocarpeta,
	prestadores.nombre,
	f.cuit,
	f.nrocominterno,
	f.cuil,
	CASE
     WHEN (titulares.codidelega is not null) THEN titulares.codidelega
 		WHEN (titularesdebaja.codidelega is not null) THEN titularesdebaja.codidelega
 		WHEN (titufami.codidelega is not null) THEN titufami.codidelega
 		WHEN (titubajafami.codidelega is not null) THEN titubajafami.codidelega
 		WHEN (titufamibaja.codidelega is not null) THEN titufamibaja.codidelega
 		WHEN (titubajafamibaja.codidelega is not null) THEN titubajafamibaja.codidelega
	END as codidelega,
	m.descripcion,
	f.nrocomprobante,
	f.periodo,
	f.impcomprobante,
	pc.nrotransferencia,
	DATE_FORMAT(pc.fechatransferencia,'%d-%m-%Y') as fechatrasferencia,
	prestadores.email1,
	prestadores.email2,
	prestadores.ddn1,
	prestadores.telefono1,
	prestadores.ddn2,
	prestadores.telefono2
FROM 
	intepagosdetalle p, 
	intepagoscabecera pc, 
	intepresentacion pre, 
	intecronograma c, 
	tipocomprobante m, 
	intepresentaciondetalle f
		
LEFT JOIN titulares on f.cuil = titulares.cuil
LEFT JOIN titularesdebaja on f.cuil = titularesdebaja.cuil
LEFT JOIN familiares on f.cuil = familiares.cuil
LEFT JOIN titulares titufami on familiares.nroafiliado = titufami.nroafiliado
LEFT JOIN titularesdebaja titubajafami on familiares.nroafiliado = titubajafami.nroafiliado
LEFT JOIN familiaresdebaja on f.cuil = familiaresdebaja.cuil
LEFT JOIN titulares titufamibaja on familiaresdebaja.nroafiliado = titufamibaja.nroafiliado
LEFT JOIN titularesdebaja titubajafamibaja on familiaresdebaja.nroafiliado = titubajafamibaja.nroafiliado
		
LEFT JOIN prestadores on f.cuit = prestadores.cuit	
WHERE
	p.recibo = '' and
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