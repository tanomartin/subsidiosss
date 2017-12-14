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
cuildelegaciones.codidelega,
m.descripcion,
f.nrocomprobante,
f.periodo,
f.impcomprobante,
p.nrotransferencia,
DATE_FORMAT(p.fechatransferencia,'%d-%m-%Y') as fechatrasferencia,
prestadores.email,
prestadores.telefono
FROM pagos p, presentacion pre, cronograma c, comprobante m, facturas f
LEFT JOIN cuildelegaciones on f.cuil = cuildelegaciones.cuil
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
  f.tipocomprobante = m.codigo
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
			 	<th>Nro Compr.</th>
			 	<th>Periodo</th>
			 	<th>$ Comprobante</th>
			 	<th>Nro Transf</th>
			 	<th>Fecha</th>
			 	<th>Email</th>
			 	<th>Telefono</th>
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
			 		<td><?php echo $rowRecibo['codidelega'] ?></td>
			 		<td><?php echo $rowRecibo['descripcion'] ?></td>
			 		<td><?php echo $rowRecibo['nrocomprobante'] ?></td>
			 		<td><?php echo $rowRecibo['periodo'] ?></td>
			 		<td><?php echo number_format($rowRecibo['impcomprobante'],"2",",",".") ?></td>
			 		<td><?php echo $rowRecibo['nrotransferencia'] ?></td>
			 		<td><?php echo $rowRecibo['fechatrasferencia'] ?></td>
			 		<td><?php echo $rowRecibo['email'] ?></td>
			 		<td><?php echo $rowRecibo['telefono'] ?></td>
			 		<td></td>
			 	</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	
	
</body>