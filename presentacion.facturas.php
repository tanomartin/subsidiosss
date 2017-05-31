<?php 
include_once 'include/conector.php';

$idPresentacion = $_GET['id'];
$sqlFactura = "SELECT * FROM facturas WHERE idpresentacion = $idPresentacion order by cuit, cuil, nrocomprobante ";
$resFactura = mysql_query($sqlFactura);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Facturas Presentaciones S.S.S. :.</title>
<script src="include/jquery.js"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>

<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["zebra", "filter"],
			headers:{2:{sorter:false, filter:false},4:{sorter:false, filter:false},5:{sorter:false},10:{sorter:false}},
			widgetOptions : { 
				filter_cssFilter   : '',
				filter_childRows   : false,
				filter_hideFilters : false,
				filter_ignoreCase  : true,
				filter_searchDelay : 300,
				filter_startsWith  : false,
				filter_hideFilters : false,
			}
		});
});


</script>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	
		<?php include_once("include/detalle.php")?>
		
		<h2>Facturas</h2>
	 	
			 <table id="listaResultado" class="tablesorter" style="text-align: center;">
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
			<?php 
				while ($rowFactura = mysql_fetch_array($resFactura)) { ?>
					<tr>
						<td style="font-size: 12px"><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['codigoob'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['cuil'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['codcertificado'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['vtocertificado'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['periodo'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['cuit'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['tipocomprobante'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['tipoemision'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['cae'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['puntoventa'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['nrocomprobante'] ?></td>					
				  <?php if ($rowFactura['impcomprobante'] != $rowFactura['impsolicitado']) {
				  			$fontcolor = "Blue";
				  		} else {
				  			$fontcolor = "";
				  		}
				  		if ($rowFactura['tipoarchivo'] == 'DB') { ?>
							<td style="font-size: 12px;"><?php echo "(".number_format($rowFactura['impcomprobante'],2,",",".").")" ?></td>
							<td style="font-size: 12px; color: <?php echo $fontcolor ?>"><?php echo "(".number_format($rowFactura['impsolicitado'],2,",",".").")" ?></td>
				  <?php } else { ?>
							<td style="font-size: 12px;"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
							<td style="font-size: 12px; color: <?php echo $fontcolor ?>"><?php echo number_format($rowFactura['impsolicitado'],2,",",".")  ?></td>
				  <?php }?>
						<td style="font-size: 12px"><?php echo $rowFactura['codpractica'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['cantidad'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['provincia'] ?></td>
						<td style="font-size: 12px"><?php echo $rowFactura['dependencia'] ?></td>
					</tr>
			<?php } ?>
			  	</tbody>
			</table>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>