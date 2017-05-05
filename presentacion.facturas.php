<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];
$sqlPresentacion = "SELECT 
						p.id,
						DATE_FORMAT(p.fechapresentacion, '%d-%m-%Y') as fechapresentacion,
						DATE_FORMAT(p.fechacancelacion, '%d-%m-%Y') as fechacancelacion,
						p.motivocancelacion,
						p.cantfactura, 
						p.impcomprobantes, 
						p.impsolicitado , 
						cronograma.periodo, 
						cronograma.carpeta
					FROM presentacion p
          			INNER JOIN cronograma on p.idcronograma = cronograma.id
					WHERE p.id = $idPresentacion";
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
<title>.: Facturas Presentaciones S.S.S. :.</title>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	
		<?php include_once("include/detallePresentacion.php")?>
		
		<h2>Facturas</h2>
	 	
	 	<div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th style="font-size: 11px">Comp. Interno</th>
			 			<th style="font-size: 11px">Tipo</th>
			 			<th style="font-size: 11px">Codigo O.S.</th>
			 			<th style="font-size: 11px">C.U.I.L.</th>
			 			<th style="font-size: 11px">Cod. Certif.</th>
			 			<th style="font-size: 11px">Vto. Certif.</th>
			 			<th style="font-size: 11px">Periodo</th>
			 			<th style="font-size: 11px">C.U.I.T.</th>
			 			<th style="font-size: 11px">Tipo Comp.</th>
			 			<th style="font-size: 11px">Tipo Emision</th>
			 			<th style="font-size: 11px">Fec. Comp.</th>
			 			<th style="font-size: 11px">C.A.E.</th>
			 			<th style="font-size: 11px">Pto. Venta</th>
			 			<th style="font-size: 11px">Num. Comp.</th>
			 			<th style="font-size: 11px">$ Comp.</th>
			 			<th style="font-size: 11px">$ Soli.</th>
			 			<th style="font-size: 11px">Cod. Prac.</th>
			 			<th style="font-size: 11px">Cant.</th>
			 			<th style="font-size: 11px">Prov.</th>
			 			<th style="font-size: 11px">Dep.</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php while ($rowFactura = mysql_fetch_array($resFactura)) { ?>
					<tr>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['codigoob'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuil'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['codcertificado'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['vtocertificado'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['periodo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuit'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['tipocomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['tipoemision'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cae'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['puntoventa'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['codpractica'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cantidad'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['provincia'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['dependencia'] ?></td>
					</tr>
			<?php } ?>
			  	</tbody>
			</table>
		</div>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>