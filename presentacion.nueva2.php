<?php 
include_once 'include/conector.php';
set_time_limit(0);
$sqlAPresentar = "SELECT c.*,DATE_FORMAT(c.fechacierre,'%d/%m/%Y') as fechacierre FROM intecronograma c WHERE fechacierre >= CURDATE() LIMIT 1";
$resAPresentar = mysql_query($sqlAPresentar);
$rowAPresentar = mysql_fetch_array($resAPresentar);

$sqlUltimaActiva = "SELECT * FROM intepresentacion WHERE fechacancelacion is null ORDER BY id DESC LIMIT 1";
$resUltimaActiva = mysql_query($sqlUltimaActiva);
$rowUltimaActiva = mysql_fetch_assoc($resUltimaActiva);
$idPresActiva = $rowUltimaActiva['id'];

$sqlControl = "SELECT f.*, prestadores.cuit, prestadores.nombre, DATE_FORMAT(p.fechapractica,'%Y%m') as periodo, 
					  i.totalsolicitado, i.idEscuela, pr.codigopractica, p.totaldebito , p.cantidad
				FROM madera.facturasprestaciones p, 
					 madera.facturasintegracion i,  
					 madera.practicas pr, 
					 madera.facturas f
				LEFT JOIN madera.prestadores ON madera.prestadores.codigoprestador = f.idPrestador
				WHERE
					autorizacionpago = 0 AND
					f.id = p.idfactura AND
					p.id = i.idfacturaprestacion AND
					p.idPractica = pr.idpractica AND
					f.id NOT IN (SELECT DISTINCT nrocominterno
								FROM intepresentaciondetalle
								WHERE idpresentacion = $idPresActiva)";
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

$sqlRevDosPasos = "SELECT * FROM intepresentacionreversion WHERE estado = 0";
$relRevDosPasos = mysql_query($sqlRevDosPasos);
$canRevDosPasos = mysql_num_rows($relRevDosPasos); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>.: Nueva Presentaciones S.S.S. :.</title>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<link rel="stylesheet" href="css/tablas.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>
<script src="include/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["zebra", "filter"],
			headers:{4:{sorter:false},
					 5:{sorter:false, filter:false},
				     6:{sorter:false,filter:false},
				     7:{sorter:false,filter:false},
				     8:{filter:false},
				     9:{sorter:false,filter:false},
				     10:{sorter:false,filter:false},
				     11:{filter:false},
				     12:{sorter:false},
				     13:{filter:false},
				     14:{sorter:false, filter:false},
				     15:{sorter:false, filter:false}},
			widgetOptions : { 
				filter_cssFilter   : '',
				filter_childRows   : false,
				filter_hideFilters : false,
				filter_ignoreCase  : true,
				filter_searchDelay : 300,
				filter_startsWith  : false,
				filter_hideFilters : false,
			}
		})
});

function control(formulario) {
	formulario.guardar.disabled = true;
	$.blockUI({ message: "<h1>Generando Presentacion... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
	return true;
}


</script>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<h2>Nueva Presentacion S.S.S.</h2>
	 	<table border="1" style="text-align: center;">
	 		<tr><td><p><b>Periodo:</b> <?php echo $rowAPresentar['periodo']?></p></td></tr>
	 		<tr><td><p><b>Carpeta:</b> <?php echo $rowAPresentar['carpeta']?></p></td></tr>
	 		<tr><td><p><b>Fecha de Cierre:</b> <?php echo $rowAPresentar['fechacierre']?></p></td></tr>
	 		<tr><td><p><b>Periodos Incluidos:</b> <?php echo $rowAPresentar['periodosincluidos']?></p></td></tr>
	 	</table>
	 	<form id="nuevapresentacion" name="nuevapresentacion" action="presentacion.nueva.guardar2.php"  method="post" onsubmit="return control(this)">
 			<p><input style="display: none" type="text" name="idCronograma" id="idCronograma" value="<?php echo $rowAPresentar['id']?>"/></p>
 			<p><input style="display: none" type="text" name="carpeta" id="carpeta" value="<?php echo $rowAPresentar['carpeta']?>"/></p>
 			<h3>Reversiones de dos pasos con DB ya presentados</h3>
 	 <?php if ($canRevDosPasos > 0) { ?>
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
    		<?php 	while ($rowRevDosPasos = mysql_fetch_assoc($relRevDosPasos)) { ?>
    					<tr>
    						<td style="font-size: 12px"><?php echo number_format($rowRevDosPasos['nrocominterno'],0,"",".") ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['tipoarchivo'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['codigoob'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['cuil'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['codcertificado'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['vtocertificado'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['periodo'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['cuit'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['tipocomprobante'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['tipoemision'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['fechacomprobante'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['cae'] ?></td>
    						<td style="font-size: 12px"><?php echo (int) $rowRevDosPasos['puntoventa'] ?></td>
    						<td style="font-size: 12px"><?php echo (int) $rowRevDosPasos['nrocomprobante'] ?></td>					
    						<td style="font-size: 12px"><?php echo number_format($rowRevDosPasos['impcomprobante'],2,",",".") ?></td>
    						<td style="font-size: 12px"><?php echo number_format($rowRevDosPasos['impsolicitado'],2,",",".") ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['codpractica'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['cantidad'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['provincia'] ?></td>
    						<td style="font-size: 12px"><?php echo $rowRevDosPasos['dependencia'] ?></td>
    					</tr>
    		<?php   } ?>
    				</tbody>
				</table>
 	 		</div>
 	 <?php } else { ?>
	  			<h4 style="color: blue">No existen reversiones de dos pasos para esta presentacion</h4>
	  <?php } ?>
 			
 			<h3>Facturas a incluir</h3>
 			<h4 style="color: blue">Tildar las facturas que no se quieren incluir en la presentación</h4>
 	  <?php if ($canControl > 0) { ?>
		 	<table id="listaResultado" class="tablesorter" style="text-align: center;">
				<thead>
					<tr>
						<th style="font-size: 11px">Id Factura</th>
						<th style="font-size: 11px">C.U.I.T.</th>
						<th style="font-size: 11px">Prestador</th>
						<th class="filter-select" data-placeholder="--" style="font-size: 11px">Periodo</th>
						<th style="font-size: 11px">Nro. Comprobante</th>
						<th style="font-size: 11px">Importe</th>
						<th style="font-size: 11px">Debito</th>
						<th style="font-size: 11px">Solicitado</th>
						<th style="font-size: 11px">CONTROL</th>
						<th style="font-size: 11px">Practica</th>
						<th style="font-size: 11px">Cantidad</th>
						<th style="font-size: 11px">Escuela</th>
						<th class="filter-select" data-placeholder="--" style="font-size: 11px">Liquidadora</th>
						<th style="font-size: 11px">Pres. Anterior</th>
						<th style="font-size: 11px">Errores</th>
						<th style="font-size: 11px">SACAR</th>
					</tr>
				</thead>
				<tbody>
			<?php foreach ($arrayControl as $factura) { ?>
					<tr>
						<td><?php echo $factura['id'] ?></td>
						<td><?php echo $factura['cuit'] ?></td>
						<td><?php echo strtoupper($factura['nombre']) ?></td>
						<td><?php echo $factura['periodo'] ?></td>
						<td><?php echo $factura['puntodeventa']."-".$factura['nrocomprobante'] ?></td>
						<td><?php echo number_format($factura['importecomprobante'],2,',','.'); ?></td>
						<td><?php echo number_format($factura['totaldebito'],2,',','.'); ?></td>
						<td><?php echo number_format($factura['totalsolicitado'],2,',','.'); ?></td>
						<td><b><?php echo number_format(($factura['importecomprobante']-$factura['totaldebito']-$factura['totalsolicitado']),2,',','.'); ?></b></td>
						<td><?php echo $factura['codigopractica'] ?></td>
						<td><?php echo number_format($factura['cantidad'],0,',','.') ?></td>
						<td><?php echo $factura['idEscuela'] ?></td>
						<td><?php echo $factura['usuarioliquidacion'] ?></td>
						<td><?php if (isset($arrayAnteriores[$factura['id']])) { echo $arrayAnteriores[$factura['id']]['idpres'];  } ?></td>
						<td><?php if (isset($arrayAnteriores[$factura['id']])) { echo $arrayAnteriores[$factura['id']]['formato']." ".$arrayAnteriores[$factura['id']]['integral'];  } ?></td>
						<td><input type="checkbox" name="sacar-<?php echo $factura['id']?>" id="sacar-<?php echo $factura['id']?>" value="<?php echo $factura['id']?>"</td>
					</tr>
			<?php } ?>		
				</tbody>
			</table>
	  <?php } else { ?>
	  			<h3 style="color: blue">No existen facturas fuera de circuito de integracion</h3>
	  <?php } ?>	
	  		<p><input type="submit" name="guardar" id="guardar" value="Crear Presentacion"/></p>		
 		</form>
	</div>
</body>
</html>