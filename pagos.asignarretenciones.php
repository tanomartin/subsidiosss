<?php include_once 'include/conector.php';
$usuario = $_SESSION['usuario'];
$idPresentacion = $_GET['id']; 

$sqlTotalesCuit = "SELECT *
					FROM inteinterbanking 
					WHERE idpresentacion = $idPresentacion and impretencion > 0";
$resTotalesCuit = mysql_query($sqlTotalesCuit);

$arrayFacturas = array();
$whereIn = "";
while ($rowTotalesCuit = mysql_fetch_assoc($resTotalesCuit)) {
	$indexT = $rowTotalesCuit['cuit']."TOTAL";
	$arrayFacturas[$indexT] = $rowTotalesCuit;
	$whereIn .= $rowTotalesCuit['cuit'].",";
}
$whereIn = substr($whereIn, 0, -1);

$sqlFactura = "SELECT * FROM intepresentaciondetalle
				WHERE idpresentacion = $idPresentacion and 
					  cuit in ($whereIn) and 
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

ksort($arrayFacturas);

$sqlPagos = "SELECT c.nroordenpago, nrocominterno, impretencion
				FROM intepagoscabecera c, intepagosdetalle d
				WHERE c.idpresentacion = $idPresentacion and 
					  c.idpresentacion = d.idpresentacion and 
					  c.nroordenpago = d.nroordenpago";
$resPagos = mysql_query($sqlPagos);
$arrayPagos = array();
while ($rowPagos = mysql_fetch_assoc($resPagos)) {
	if (isset($arrayPagos[$rowPagos['nrocominterno']])) {
		$arrayPagos[$rowPagos['nrocominterno']]['nroordenpago'] = $rowPagos['nroordenpago'];
		$arrayPagos[$rowPagos['nrocominterno']]['impretencion'] += $rowPagos['impretencion'];
	} else {
		$arrayPagos[$rowPagos['nrocominterno']]['nroordenpago'] = $rowPagos['nroordenpago'];
		$arrayPagos[$rowPagos['nrocominterno']]['impretencion'] = $rowPagos['impretencion'];
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Informe de Pagos :.</title>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/jquery.blockUI.js" type="text/javascript"></script>
<script src="include/funcionControl.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["filter"],
			headers:{0:{sorter:false},
				 1:{sorter:false, filter:false},
			 	 2:{sorter:false, filter:false},
			 	 3:{sorter:false, filter:false},
			 	 4:{sorter:false},
			 	 5:{sorter:false, filter:false},
			 	 6:{sorter:false, filter:false},
			 	 7:{sorter:false, filter:false},
			 	 8:{sorter:false, filter:false},
			 	 9:{sorter:false, filter:false},
			 	 10:{sorter:false, filter:false},
			 	 11:{sorter:false, filter:false},
			 	 12:{sorter:false, filter:false},
			 	 13:{sorter:false, filter:false},
			 	 14:{sorter:false, filter:false},
			 	 16:{sorter:false, filter:false},
			 	 17:{sorter:false, filter:false}},
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

function checkInput(cuit) {
	var id = cuit + "-check";
	document.getElementById(id).value = 1;
}

function validar(formulario, cuits) {
	var arrayCuits = cuits.split(",");
	try {
		arrayCuits.forEach(function callback(currentValue) {
		    var id = currentValue + "-check";
		    if (formulario[id].value == 0) {
				alert("Debe seleccionar una factura del C.U.I.T. "+currentValue);
				throw "break"; 
		    }
		});
	} catch (e) { 
		return false; 
	}

	$.blockUI({ message: "<h1>Asignando Retenciones a Facturas seleccionadas... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
 	return true;
}

</script>
<title>.: Asignacion Retenciones :.</title>
<style type="text/css" media="print"> .nover {display:none} </style>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'pagos.php'" /></p>
		<?php include_once("include/detalle.php")?>
		<h2>Asignacion de Retenciones</h2>
		<form action="pagos.asignarretenciones.guardar.php?id=<?php echo $idPresentacion?>" onSubmit="return validar(this,'<?php echo $whereIn ?>')"  method="post">
			<table id="listaResultado" class="tablesorter" style="text-align: center; font-size: 15px" >
				<thead>
				 	<tr>
				 		<th>Nº Comp.</th>
				 		<th>Tipo</th>
				 		<th>C.U.I.L.</th>
				 		<th>Periodo</th>
				 		<th>C.U.I.T.</th>
				 		<th>Fecha</th>
				 		<th>Nº</th>
				 		<th>$ Comp.</th>
				 		<th>$ Deb.</th>
				 		<th>$ Soli.</th>
				 		<th>$ No Int</th>
				 		<th>$ O.S.</th>
				 		<th>$ S.S.S</th>
				 		<th>$ Ret.</th>
				 		<th>Asignar</th>
				 	 	</tr>
				 </thead>
				 <tbody>
		<?php	$arrayChecked = array();
				foreach ($arrayFacturas as $key => $rowFactura) {  
					$pos = strpos($key, "TOTAL");
					if ($pos === false) {
						$cuit = $rowFactura['cuit']; ?>
						<tr>
							<td><?php echo $rowFactura['nrocominterno'] ?></td>
							<td><?php echo $rowFactura['tipoarchivo'] ?></td>
							<td><?php echo $rowFactura['cuil'] ?></td>
							<td><?php echo $rowFactura['periodo'] ?></td>
							<td><?php echo $cuit ?></td>
							<td><?php echo $rowFactura['fechacomprobante'] ?></td>
							<td><?php echo $rowFactura['nrocomprobante'] ?></td>
							<td><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></td>
							<td><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></td>
							<td><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".") ?></td>					
							<td><?php echo number_format($rowFactura['impnointe'],2,",",".") ?></td>
							<td><?php echo number_format($rowFactura['impobrasocial'],2,",",".") ?></td>
							<td><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></td>
							<td>
						<?php $checked = '';
							  if (isset($arrayPagos[$rowFactura['nrocominterno']]) && $rowFactura['tipoarchivo'] != 'DB') { 
									echo number_format($arrayPagos[$rowFactura['nrocominterno']]['impretencion'],2,",","."); 
									if ($arrayPagos[$rowFactura['nrocominterno']]['impretencion'] > 0) { 
										$checked = 'checked="checked"';
										$arrayChecked[$cuit] = 1;
									}
							  } else { 
									echo "0,00";
							  }  ?>
							</td>						
							<td>
							<?php if ($arrayFacturas[$cuit."TOTAL"]['impretencion'] <= $rowFactura['impcomprobanteintegral'] && 
										isset($arrayPagos[$rowFactura['nrocominterno']]['nroordenpago']) && $rowFactura['tipoarchivo'] != 'DB') {   ?>
									<input <?php echo $checked ?> onclick="checkInput(<?php echo $cuit ?>)" type="radio" name="<?php echo $rowFactura['cuit']?>" value="<?php echo $rowFactura['nrocominterno']."-".$arrayPagos[$rowFactura['nrocominterno']]['nroordenpago'] ?>"/>
							<?php } ?>
							</td>
						</tr>
			  <?php } else {
						$cuit = substr($key,0,11); ?>
					 	<tr>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"><b><?php echo $cuit ?></b></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"></td>
					 		<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impcomprobanteintegral'],2,",",".") ?></b></td>	
							<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impdebito'],2,",",".") ?></b></td>	
				 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impsolicitadosubsidio'],2,",",".") ?></b></td>	
				 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impnointe'],2,",",".") ?></b></td>
				 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impobrasocial'],2,",",".") ?></b></td>
				 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></b></td>
				 			<td style="background-color: #99bfe6"><b><?php echo number_format($rowFactura['impretencion'],2,",",".") ?></b></td>
				 			<td style="background-color: #99bfe6">
				 				<input style="display: none" type="text" value="<?php echo $rowFactura['impretencion'] ?>" name="<?php echo $cuit."-ret"?>" id="<?php echo $cuit."-ret"?>" />
				 				<?php   $check = 0; if (isset($arrayChecked[$cuit])) { $check = 1; } ?>
				 				<input style="display: none" type="text" value="<?php echo $check ?>" name="<?php echo $cuit."-check"?>" id="<?php echo $cuit."-check"?>" />
				 			</td>
				 		</tr>	
			 <?php	} 
				} ?>
				</tbody>
			</table>
			<input type="submit" value="ASIGNAR RETENCIONES" />
		</form>
	</div>
</body>
</html>
