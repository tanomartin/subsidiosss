<?php 
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];

$sqlDebitos = "SELECT * FROM intepresentaciondetalle WHERE idpresentacion = $idPresentacion and tipoarchivo = 'DB'";
$resDebitos = mysql_query($sqlDebitos);
$canDebitos = mysql_num_rows($resDebitos);
$canReversiones = 0;
if ($canDebitos != 0) {
    $whereIn = "(";
    while ($rowDebitos = mysql_fetch_assoc($resDebitos)) {
        $whereIn .= $rowDebitos['nrocominterno'].",";
    }
    $whereIn = substr($whereIn, 0, -1);
    $whereIn .= ")";
    
    $sqlReversiones = "SELECT * FROM intepresentaciondetalle WHERE idpresentacion = $idPresentacion and nrocominterno in $whereIn";
    $resReversiones = mysql_query($sqlReversiones);
    $canReversiones = mysql_num_rows($resReversiones);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Reversiones Presentaciones S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Listado de Reversiones de Presentacion</h2>
	 	<?php include_once("include/detalle.php")?>
	 	<p><input type="button" name="nueva" id="nueva" value="Nueva Reversion" onClick="location.href = 'presentacion.reversiones.nueva.php?id=<?php echo $idPresentacion ?>'"/></p>
	 	<?php if ($canReversiones != 0) { ?>
	 			<div class="grilla">
		 			<table>
					 	<thead>
					 		<tr>
					 			<th style="font-size: 11px">Comp. Interno</th>
					 			<th class="filter-select" data-placeholder="Selccione" style="font-size: 11px">Tipo</th>
					 			<th style="font-size: 11px">Codigo O.S.</th>
					 			<th style="font-size: 11px">C.U.I.L.</th>
					 			<th style="font-size: 11px">Cod. Certif.</th>
					 			<th style="font-size: 11px">Vto. Certif.</th>
					 			<th class="filter-select" data-placeholder="Selccione" style="font-size: 11px">Periodo</th>
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
					 			<th class="filter-select" data-placeholder="Selccione" style="font-size: 11px">Dep.</th>
					 		</tr>
					 	</thead>
					 	<tbody>
			 			<?php while ($reversion = mysql_fetch_assoc($resReversiones)) {  ?>
			 					<tr>
								<td style="font-size: 12px"><?php echo number_format($reversion['nrocominterno'],0,"",".") ?></td>
								<td style="font-size: 12px"><?php echo $reversion['tipoarchivo'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['codigoob'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['cuil'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['codcertificado'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['vtocertificado'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['periodo'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['cuit'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['tipocomprobante'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['tipoemision'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['fechacomprobante'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['cae'] ?></td>
								<td style="font-size: 12px"><?php echo (int) $reversion['puntoventa'] ?></td>
								<td style="font-size: 12px"><?php echo (int) $reversion['nrocomprobante'] ?></td>					
								<?php if ( $reversion['tipoarchivo'] == "DB") { ?>
									<td style="font-size: 12px;"><?php echo "(".number_format($reversion['impcomprobante'],2,",",".").")" ?></td>
									<td style="font-size: 12px;"><?php echo "(".number_format($reversion['impsolicitado'],2,",",".").")" ?></td>
								<?php } else { ?>
									<td style="font-size: 12px;"><?php echo number_format($reversion['impcomprobante'],2,",",".") ?></td>
									<td style="font-size: 12px;"><?php echo number_format($reversion['impsolicitado'],2,",",".") ?></td>
								<?php } ?>
								<td style="font-size: 12px"><?php echo $reversion['codpractica'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['cantidad'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['provincia'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['dependencia'] ?></td>
							</tr>
			 			<?php } ?>
			 			</tbody>
		 			</table>
	 			</div>
	 	<?php } else { ?>
	 	   		<h3 style="color: blue">No hay reversiones cargadas para esta presentacion</h3>
	 	<?php }?> 		
	</div>
</body>
</html>