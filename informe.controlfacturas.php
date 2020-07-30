<?php include_once 'include/conector.php';

$sqlUltimaActiva = "SELECT * FROM intepresentacion WHERE fechacancelacion is null ORDER BY id DESC LIMIT 1";
$resUltimaActiva = mysql_query($sqlUltimaActiva);
$rowUltimaActiva = mysql_fetch_assoc($resUltimaActiva);
$idPresActiva = $rowUltimaActiva['id'];

$sqlControl = "SELECT f.*, prestadores.cuit, prestadores.nombre 
                FROM madera.facturasprestaciones p, madera.facturasintegracion i, madera.facturas f
                LEFT JOIN madera.prestadores ON madera.prestadores.codigoprestador = f.idPrestador 
                WHERE 
                    autorizacionpago = 0 AND 
                    f.id = p.idfactura AND 
                    p.id = i.idfacturaprestacion AND
                    f.id NOT IN (SELECT DISTINCT nrocominterno 
                                    FROM intepresentaciondetalle 
                                    WHERE idpresentacion = $idPresActiva)
                ORDER BY fechacierreliquidacion";
$resControl = mysql_query($sqlControl);
$canControl = mysql_num_rows($resControl); 
$arrayControl = array();
if ($canControl > 0) {
    $whereIn = "(";
    while ($rowControl = mysql_fetch_assoc($resControl)) { 
        $arrayControl[$rowControl['id']] = $rowControl;
        $whereIn .= $rowControl['id'].",";
    }
    $whereIn = substr($whereIn, 0, -1);
    $whereIn .= ")";
} 

$sqlAnteriores = "SELECT * FROM intepresentaciondetalle WHERE nrocominterno in $whereIn ORDER BY idpresentacion ASC";
$resAnteriores = mysql_query($sqlAnteriores);
$canAnteriores = mysql_num_rows($resAnteriores); 
$arrayAnteriores = array();
if ($canAnteriores > 0) {
    while ($rowAnteriores = mysql_fetch_assoc($resAnteriores)) { 
        $arrayAnteriores[$rowAnteriores['nrocominterno']] = array('idpres'=> $rowAnteriores['idpresentacion'], 'formato' => $rowAnteriores['deverrorformato'], 'integral' => $rowAnteriores['deverrorintegral']);
    }
}
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

<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["zebra", "filter"],
			headers:{4:{sorter:false, filter:false},5:{sorter:false},6:{sorter:false},7:{sorter:false, filter:false},8:{sorter:false, filter:false}},
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
	 	<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'informes.php'" /></p>
	 	
	 	<h2>Facturas marcadas para integracion fuera de la presentacion activa</h2>
	 	<h3 style="color: blue">Presentacion activa - ID:<?php echo $idPresActiva ?> (<?php echo $rowUltimaActiva['fechapresentacion'] ?>)</h3>
  <?php if ($canControl > 0) { ?>
	 	<table id="listaResultado" class="tablesorter" style="text-align: center;">
			<thead>
				<tr>
					<th style="font-size: 11px">Id Factura</th>
					<th style="font-size: 11px">C.U.I.T.</th>
					<th style="font-size: 11px">Prestador</th>
					<th style="font-size: 11px">Nro. Comprobante</th>
					<th style="font-size: 11px">Importe</th>
					<th class="filter-select" data-placeholder="--" style="font-size: 11px">Liquidadora</th>
					<th style="font-size: 11px">Fecha Cierre Liq.</th>
					<th style="font-size: 11px">Pres. Anterior</th>
					<th style="font-size: 11px">Errores</th>
				</tr>
			</thead>
			<tbody>
		<?php foreach ($arrayControl as $factura) { ?>
				<tr>
					<td><?php echo $factura['id'] ?></td>
					<td><?php echo $factura['cuit'] ?></td>
					<td><?php echo strtoupper($factura['nombre']) ?></td>
					<td><?php echo $factura['puntodeventa']."-".$factura['nrocomprobante'] ?></td>
					<td><?php echo number_format($factura['importecomprobante'],2,',','.'); ?></td>
					<td><?php echo $factura['usuarioliquidacion'] ?></td>
					<td><?php echo $factura['fechacierreliquidacion'] ?></td>
					<td><?php if (isset($arrayAnteriores[$factura['id']])) { echo $arrayAnteriores[$factura['id']]['idpres'];  } ?></td>
					<td><?php if (isset($arrayAnteriores[$factura['id']])) { echo $arrayAnteriores[$factura['id']]['formato']." ".$arrayAnteriores[$factura['id']]['integral'];  } ?></td>
				</tr>
		<?php } ?>		
			</tbody>
  <?php } else { ?>
  			<h3 style="color: blue">No existen facturas fuera de circuito de integracion</h3>
  <?php } ?>
  		</table>
  		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
  </div>
</body>
</html>