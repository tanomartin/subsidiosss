<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];

$sqlFactura = "SELECT * FROM facturas WHERE idpresentacion = $idPresentacion and deverrorintegral is not null";
$resFactura = mysql_query($sqlFactura);

$sqlErrores = "SELECT * FROM errorsss WHERE id > 390";
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
<title>.: Detalle Errores Integral S.S.S. :.</title>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	
	 	<?php include_once("include/detalle.php")?>
	 	
	 	<h2>Errores Integrales</h2>
	 	
	 	<div class="grilla">
			 <table>
			 	<thead>
			 		<tr>
			 			<th style="font-size: 11px">Comp. Interno</th>
			 			<th style="font-size: 11px">Tipo</th>
			 			<th style="font-size: 11px">C.U.I.L.</th>
			 			<th style="font-size: 11px">Periodo</th>
			 			<th style="font-size: 11px">C.U.I.T.</th>
			 			<th style="font-size: 11px">C.A.E.</th>
			 			<th style="font-size: 11px">Fec. Comp.</th>
			 			<th style="font-size: 11px">Num. Comp.</th>
			 			<th style="font-size: 11px" colspan="2">Presentacion</th>
			 			<th style="font-size: 11px" colspan="2">Resultado Formato</th>
			 			<th style="font-size: 11px">ERROR INTEGRAL</th>
			 		</tr>
			 		<tr>
			 			<th style="font-size: 11px" colspan="8"></th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th style="font-size: 11px">$ Comprobante</th>
			 			<th style="font-size: 11px">$ Solicitado</th>
			 			<th></th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php 
				$totCom = 0;
				$totSol = 0;
				$totComFor = 0;
				$totSolFor = 0;
				while ($rowFactura = mysql_fetch_array($resFactura)) { 
					$totCom += $rowFactura['impcomprobante'];
					$totSol += $rowFactura['impsolicitado'];
					$totComFor += $rowFactura['impcomprobanteformato'];
					$totSolFor += $rowFactura['impsolicitadoformato'];	?>
					<tr>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuil'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['periodo'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cuit'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['cae'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impsolicitado'],2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impcomprobanteformato'],2,",",".") ?></td>
						<td style="font-size: 11px"><?php echo number_format($rowFactura['impsolicitadoformato'],2,",",".") ?></td>
				 		<td style="font-size: 11px; color: red">
				  <?php $explodeErrores = explode("-",$rowFactura['deverrorintegral']);
						foreach ($explodeErrores as $error) {
							if ($error != "") {
								$error = (int) $error;
								echo "ERROR: $error - COLUMNA: ".$arrayErrores[$error]['campo']." (".$arrayErrores[$error]['descrip'].")<br>";
				  			}
						}?>
				  		</td>
					</tr>
			<?php } ?>
					<tr>
						<td colspan="8">TOTALES</td>
						<td><?php echo number_format($totCom,2,",",".") ?></td>
						<td><?php echo number_format($totSol,2,",",".") ?></td>
						<td><?php echo number_format($totComFor,2,",",".") ?></td>
						<td><?php echo number_format($totSolFor,2,",",".") ?></td>
						<td></td>
					</tr>
			  	</tbody>
			</table>
			</div>
		
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>