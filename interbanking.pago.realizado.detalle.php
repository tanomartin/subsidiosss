<?php
include_once 'include/conector.php';
$id = $_GET['id'];
$sqlTotales = "SELECT i.*, DATE_FORMAT(i.fechapago,'%d/%m/%Y') as fechapago FROM inteinterbankingcabecera i WHERE id = $id";
$resTotales = mysql_query($sqlTotales);
$rowTotales = mysql_fetch_assoc($resTotales);

$sqlPagos = "SELECT inteinterbanking.*, madera.prestadoresauxiliar.cbu,
			DATE_FORMAT(inteinterbanking.fechaenvio, '%d-%m-%Y') as fechaenvio,
			madera.prestadoresauxiliar.cbu
			FROM inteinterbanking
			LEFT JOIN madera.prestadores on inteinterbanking.cuit = madera.prestadores.cuit
			LEFT JOIN madera.prestadoresauxiliar on madera.prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
			WHERE idpago = $id";
$resPagos = mysql_query($sqlPagos);
$numPagos = mysql_num_rows($resPagos);

$arrayFacturas = array();
if ($numPagos > 0) {
	while ($rowTotalesCuit = mysql_fetch_assoc($resPagos)) {
		$indexT = $rowTotalesCuit['cuit']."TOTAL";
		$arrayFacturas[$indexT] = $rowTotalesCuit;

		$sqlFactura = "SELECT * FROM intepresentaciondetalle
						WHERE idpresentacion = ".$rowTotalesCuit['idpresentacion']." and
							  cuit = ".$rowTotalesCuit['cuit']." and
							  deverrorintegral is null and
							  codpractica not in (97,98,99)
						ORDER BY cuit, periodo, codpractica";
		$resFactura = mysql_query($sqlFactura);
		while ($rowFactura = mysql_fetch_assoc($resFactura)) {
			$index = $rowFactura['cuit'].$rowFactura['nrocominterno'].$rowFactura['tipoarchivo'];
			if ($rowFactura['tipoarchivo'] == 'DB') {
				$rowFactura['impdebito'] = (-1)*$rowFactura['impdebito'];
				$rowFactura['impnointe'] = (-1)*$rowFactura['impnointe'];
				$rowFactura['impcomprobanteintegral'] = (-1)*$rowFactura['impcomprobanteintegral'];
			}
			$monOS = $rowFactura['impcomprobanteintegral'] - $rowFactura['impmontosubsidio'] - $rowFactura['impdebito'] - $rowFactura['impnointe'];
			$rowFactura['impobrasocial'] = $monOS;
			$arrayFacturas[$index] = $rowFactura;
		}
	}
}
ksort($arrayFacturas);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Facturas Presentaciones S.S.S. :.</title>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>
<script src="/madera/lib/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["filter"],
			headers:{0:{sorter:false, filter:false},
				 1:{sorter:false, filter:false},
			 	 2:{sorter:false, filter:false},
			 	 3:{sorter:false, filter:false},
			 	 4:{sorter:false, filter:false},
			 	 5:{sorter:false, filter:false},
			 	 6:{sorter:false, filter:false},
			 	 7:{sorter:false, filter:false},
			 	 8:{sorter:false, filter:false},
			 	 9:{sorter:false, filter:false},
			 	 10:{sorter:false, filter:false},
			 	 11:{sorter:false, filter:false},
			 	 12:{sorter:false, filter:false}},
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

function sacarPago(id, cuit) {
	var redire = "interbanking.pago.realizado.sacar.php?id="+id+"&cuit="+cuit;
	var r = confirm("Desea Quitar el Pago del cuit "+ cuit +" del pago con id "+ id);
	if (r == true) {
		window.location.href = redire;
	} 
}

</script>
<title>.: Generacion Pagos :.</title>
<style type="text/css" media="print">
.nover {display:none}
</style>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'interbanking.pago.realizado.php'" /></p>
		<h2>Pago Realizados Interbanking el <?php echo $rowTotales['fechapago'] ?> - Id <?php echo  $rowTotales['id']?></h2>
		<table id="listaResultado" class="tablesorter" style="text-align: center; font-size: 15px" >
			<thead>
				<tr>
				 	<th>Nº Comp.</th>
				 	<th>Tipo</th>
				 	<th>Periodo</th>
				 	<th>C.U.I.T.</th>
				 	<th>C.B.U.</th>
				 	<th>Fecha</th>
				 	<th>Nº</th>
				 	<th>$ Comp.</th>
				 	<th>$ Deb.</th>
				 	<th>$ O.S.</th>
				 	<th>$ S.S.S</th>
				 	<th>$ Ret.</th>
				 	<th>$ Transf.</th>
				 </tr>
			</thead>
		    <tbody>
	  <?php foreach ($arrayFacturas as $key => $rowFactura) {  
				$pos = strpos($key, "TOTAL");
				if ($pos === false) {?>
					<tr>
						<td><?php echo $rowFactura['nrocominterno'] ?></td>
						<td><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td><?php echo $rowFactura['periodo'] ?></td>
						<td><?php echo $rowFactura['cuit'] ?></td>
						<td></td>
						<td><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td><?php echo $rowFactura['nrocomprobante'] ?></td>
						<td><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></td>
						<td><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></td>
						<td><?php echo number_format($rowFactura['impnointe'] + $rowFactura['impobrasocial'] ,2,",",".") ?></td>
						<td><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></td>
						<td></td>
						<td></td>
					</tr>
		<?php } else { 
					$cuit = substr($key,0,11);  ?>
					 <tr>
					 	<th colspan="3">
					 	<?php if ($_SESSION['usuario'] == "sistemas") {?>
					 			<input type="button" value="SACAR PAGO" onclick="sacarPago(<?php echo $rowTotales['id'] ?>, <?php echo $rowFactura['cuit'] ?>)"/>
					 	<?php }?>
					 	</th>
					 	<th><?php echo $cuit ?></th>
					 	<th><?php echo $rowFactura['cbu'] ?></th>
					 	<th colspan="2"></th>
					 	<th><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></th>	
					 	<th><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></th>	
					 	<th><?php echo number_format($rowFactura['impobrasocial'] + $rowFactura['impnointe'], 2,",",".") ?></th>
					 	<th><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></th>
					 	<th><?php echo number_format($rowFactura['impretencion'],2,",",".") ?></th>
					 	<th><?php echo number_format($rowFactura['impapagar'],2,",",".") ?></th>
					 </tr>	
		<?php	}
			} ?>
				<tr>
					<th colspan="7">TOTALES</th>
					<th><?php echo number_format($rowTotales['impcomprobanteintegral'],2,",",".") ?></th>	
					<th><?php echo number_format($rowTotales['impdebito'],2,",",".") ?></th>	
					<th><?php echo number_format($rowTotales['impobrasocial'] + $rowTotales['impnointe'], 2,",",".") ?></th>
					<th><?php echo number_format($rowTotales['impmontosubsidio'],2,",",".") ?></th>
					<th><?php echo number_format($rowTotales['impretencion'],2,",",".") ?></th>
					<th><?php echo number_format($rowTotales['impapagar'],2,",",".") ?></th>
				</tr>
				<tr>
					<th colspan="7">CONTROLES</th>
					<th><?php echo number_format($rowTotales['impcomprobanteintegral'],2,",",".") ?></th>	
					<th colspan="3"><?php echo number_format($rowTotales['impdebito']+$rowTotales['impobrasocial'] + $rowTotales['impnointe'] +$rowTotales['impmontosubsidio'] ,2,",",".") ?></th>	
					<th colspan="2"><?php echo number_format($rowTotales['impretencion']+$rowTotales['impapagar'],2,",",".") ?></th>
				</tr>
			</tbody>
		</table>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	</div>
</body>
</html>