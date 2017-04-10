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
<title>.: Detalle Presentaciones S.S.S. :.</title>

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
	 	
	 	<?php if ($rowPresentacion['fechacancelacion'] != null) {  ?>
	 		<h3 style="color: red">PRESENTACION CANCELADA</h3>
	 		<p><b>MOTIVO: </b><?php echo $rowPresentacion['motivocancelacion'] ?></p>
	 	<?php } ?>
	 	
	 	<h3>Cantidad: <?php echo $rowPresentacion['cantfactura']?> - Total Importe Comprobantes: <?php echo number_format($rowPresentacion['sumimpcomprobante'],"2",",",".") ?> - Total Imporate Solicitado: <?php echo number_format($rowPresentacion['sumimpsolicitado'],"2",",",".") ?></h3>
	 	<div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th>Comp. Interno</th>
			 			<th>C.U.I.L.</th>
			 			<th>Vto. Certificado</th>
			 			<th>Periodo</th>
			 			<th>C.U.I.T.</th>
			 			<th>$ Comprobante</th>
			 			<th>$ Solicitado</th>
			 			<th>Cod. Practica</th>
			 			<th>Cantidad</th>
			 			<th>Provincia</th>
			 			<th>Dependencia</th>
			 			<th colspan="2">Resultado Formato</th>
			 			<th>Resultado Subsidio</th>
			 		</tr>
			 		<tr>
			 			<th colspan="11"></th>
			 			<th>$ Comprobante</th>
			 			<th>$ Solicitado</th>
			 			<th>$</th>	
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php while ($rowFactura = mysql_fetch_array($resFactura)) { ?>
					<tr>
						<td><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td><?php echo $rowFactura['cuil'] ?></td>
						<td><?php echo $rowFactura['vtocertificado'] ?></td>
						<td><?php echo $rowFactura['periodo'] ?></td>
						<td><?php echo $rowFactura['cuit'] ?></td>
						<td><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						<td><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
						<td><?php echo $rowFactura['codpractica'] ?></td>
						<td><?php echo $rowFactura['cantidad'] ?></td>
						<td><?php echo $rowFactura['provincia'] ?></td>
						<td><?php echo $rowFactura['dependencia'] ?></td>
						<td><?php if ($rowFactura['impcomprobanteformato'] != null) echo number_format($rowFactura['impcomprobanteformato'],2,",","."); else echo "-"; ?></td>
						<td><?php if ($rowFactura['impsolicitadoformato'] != null) echo number_format($rowFactura['impsolicitadoformato'],2,",","."); else echo "-"; ?></td>
						<td><?php if ($rowFactura['montosubsidio'] != null) echo number_format($rowFactura['montosubsidio'],2,",","."); else echo "-";  ?></td>
					</tr>
			<?php } ?>
			  	</tbody>
			</table>
		</div>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>