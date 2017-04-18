<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];

$sqlPresentacion = "SELECT p.*, c.periodo, c.carpeta FROM presentacion p, cronograma c WHERE p.id = $idPresentacion and p.idcronograma = c.id";
$resPresentacion = mysql_query($sqlPresentacion);
$rowPresentacion = mysql_fetch_array($resPresentacion);

$sqlFactura = "SELECT * FROM facturas WHERE idpresentacion = $idPresentacion and deverrorformato is not null";
$resFactura = mysql_query($sqlFactura);

$sqlErrores = "SELECT * FROM errorsss WHERE id < 400";
$resErrores = mysql_query($sqlErrores);
$arrayErrores = array();
while ($rowErrores = mysql_fetch_array($resErrores)) {
	$arrayErrores[$rowErrores['id']] = array("campo" => $rowErrores['campo'], "descrip" => $rowErrores['descripcion']);
}

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
	 	
	 	<h2>Detalle Presentacion</h2>
	 	<h3>ID: <?php echo $rowPresentacion['id']?> - PERIODO: <?php echo $rowPresentacion['periodo'] ?> - CARPETA: <?php echo $rowPresentacion['carpeta'] ?></h3>
	 	 	
	 	<?php if ($rowPresentacion['fechacancelacion'] != null) {  ?>
	 		<h3 style="color: red">PRESENTACION CANCELADA</h3>
	 		<p><b>MOTIVO: </b><?php echo $rowPresentacion['motivocancelacion'] ?></p>
	 	<?php } ?>
	 	
	 	<h3>[PRESENTACION]</h3><h3>Cantidad: <?php echo $rowPresentacion['cantfactura']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantes'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitado'],"2",",",".") ?></h3>
	 	
	 	<?php if ($rowPresentacion['fechadevformato'] != null) {  ?>
	 		<h3>[DEV. FORMATO]</h3>
	 		<h3>Cantidad OK: <?php echo $rowPresentacion['cantformatook']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesformatook'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadoformatook'],"2",",",".") ?></h3>
	 		<h3>Cantidad Rech: <?php echo $rowPresentacion['cantformatonok']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesformatonok'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadoformatonok'],"2",",",".") ?></h3>
	 	<?php } ?>
	 	
	 	<?php if ($rowPresentacion['fechaintegral'] != null) {  ?>
	 		<h3>[DEV. INTEGRAL]</h3>
	 		<h3>Cantidad OK: <?php echo $rowPresentacion['cantintegralok']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesintegralok'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadointegranlok'],"2",",",".") ?></h3>
	 		<h3>Cantidad Rech: <?php echo $rowPresentacion['cantintegralnok']?> - Imp. Comprobantes: <?php echo number_format($rowPresentacion['impcomprobantesintegralnok'],"2",",",".") ?> - Imp. Solicitado: <?php echo number_format($rowPresentacion['impsolicitadointegranlnok'],"2",",",".") ?></h3>
	 	<?php } ?>
	 	
	 	<h2>Facturas</h2>
	 	
	 	<div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th style="font-size: 11px">Comp. Interno</th>
			 			<th style="font-size: 11px">C.U.I.L.</th>
			 			<th style="font-size: 11px">Periodo</th>
			 			<th style="font-size: 11px">C.U.I.T.</th>
			 			<th style="font-size: 11px">C.A.E.</th>
			 			<th style="font-size: 11px">Fec. Comp.</th>
			 			<th style="font-size: 11px">Num. Comp.</th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">FORMATO ERROR</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php while ($rowFactura = mysql_fetch_array($resFactura)) { ?>
					<tr>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuil'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['periodo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuit'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cae'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
				 		<td style="font-size: 11px; color: red">
				  <?php $explodeErrores = explode("-",$rowFactura['deverrorformato']);
						foreach ($explodeErrores as $error) {
							if ($error != "") {
								$error = (int) $error;
								echo "ERROR: $error - COLUMNA: ".$arrayErrores[$error]['campo']." (".$arrayErrores[$error]['descrip'].")<br>";
				  			}
						}?>
				  		</td>
					</tr>
			<?php } ?>
			  	</tbody>
			</table>
		</div>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>