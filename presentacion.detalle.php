<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];

$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$sqlFactura = "SELECT * FROM facturas WHERE idpresentacion = $idPresentacion";
$resFactura = mysql_query($sqlFactura);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Nueva Presentaciones S.S.S. :.</title>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Detalle Presentacio</h2>
	 	<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	<h2>Facturas</h2>
	 	<h3>Cantidad: <?php echo $rowPresentacion['cantfactura']?> - Total Importe Comprobantes: <?php echo $rowPresentacion['sumimpcomprobante'] ?> - Total Imporate Solicitado: <?php echo $rowPresentacion['sumimpsolicitado'] ?></h3>
	 	<div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th>Comp. Interno</th>
			 			<th>C.U.I.L.</th>
			 			<th>Vto. Certificado</th>
			 			<th>Periodo</th>
			 			<th>C.U.I.T.</th>
			 			<th>Imp. Comprobante</th>
			 			<th>Imp. Solicitado</th>
			 			<th>Cod. Practica</th>
			 			<th>Cantidad</th>
			 			<th>Provincia</th>
			 			<th>Dependencia</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php while ($rowFactura = mysql_fetch_array($resFactura)) { ?>
					<tr>
						<td><?php echo $rowFactura['nrocominterno'] ?></td>
						<td><?php echo $rowFactura['cuil'] ?></td>
						<td><?php echo $rowFactura['vtocertificado'] ?></td>
						<td><?php echo $rowFactura['periodo'] ?></td>
						<td><?php echo $rowFactura['cuit'] ?></td>
						<td><?php echo $rowFactura['impcomprobante'] ?></td>
						<td><?php echo $rowFactura['impsolicitado'] ?></td>
						<td><?php echo $rowFactura['codpractica'] ?></td>
						<td><?php echo $rowFactura['cantidad'] ?></td>
						<td><?php echo $rowFactura['provincia'] ?></td>
						<td><?php echo $rowFactura['dependencia'] ?></td>
					</tr>
			<?php } ?>
			  	</tbody>
			</table>
		</div>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>