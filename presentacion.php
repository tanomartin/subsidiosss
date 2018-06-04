<?php 
include_once 'include/conector.php';
$usuario = $_SESSION['usuario'];
$sqlPresentacion = "SELECT 
						p.id,
						DATE_FORMAT(p.fechapresentacion, '%d-%m-%Y') as fechapresentacion,
						DATE_FORMAT(p.fechacancelacion, '%d-%m-%Y') as fechacancelacion,
						p.cantfactura, 
						p.impcomprobantes, 
						p.impsolicitado,
						p.impcomprobantesd, 
						p.impsolicitadod, 
						p.idcronograma, 
						intecronograma.periodo, 
						intecronograma.carpeta,
						DATE_FORMAT(intepresentacionformato.fechadevformato, '%d-%m-%Y') as fechadevformato,
						intepresentacionformato.cantformatonok,
						DATE_FORMAT(intepresentacionintegral.fechaintegral, '%d-%m-%Y') as fechaintegral,
						intepresentacionintegral.cantintegralnok,
						DATE_FORMAT(interendicioncontrol.fecharendicion, '%d-%m-%Y') as fecharendicion,
						DATE_FORMAT(p.fechacierre, '%d-%m-%Y') as fechacierre,
						DATE_FORMAT(p.fechadeposito, '%d-%m-%Y') as fechadeposito,
						p.montodepositado
					FROM intepresentacion p
          			INNER JOIN intecronograma on p.idcronograma = intecronograma.id
				  	LEFT JOIN intepresentacionformato on p.id = intepresentacionformato.id
          			LEFT JOIN intepresentacionintegral on p.id = intepresentacionintegral.id
					LEFT JOIN interendicioncontrol on p.id = interendicioncontrol.idpresentacion
					ORDER BY p.id DESC";
$resPresentacion = mysql_query($sqlPresentacion);
$canPresentacion = mysql_num_rows($resPresentacion);

$sqlAPresentar = "SELECT c.*,DATE_FORMAT(c.fechacierre,'%d/%m/%Y') as fechacierre 
					FROM intecronograma c 
					WHERE fechacierre >=  CURDATE() LIMIT 1";
$resAPresentar = mysql_query($sqlAPresentar);
$rowAPresentar = mysql_fetch_array($resAPresentar);

$sqlPresentacionPeriodo = "SELECT *
							FROM intepresentacion p
							LEFT JOIN interendicioncontrol on p.id = interendicioncontrol.idpresentacion
							WHERE p.fechacancelacion is null and interendicioncontrol.fecharendicion is null";
$resPresentacionPeriodo  = mysql_query($sqlPresentacionPeriodo);
$canPresentacionPeriodo = mysql_num_rows($resPresentacionPeriodo);

$sqlPagos = "SELECT idpresentacion FROM intepagoscabecera GROUP BY idpresentacion";
$resPagos  = mysql_query($sqlPagos);
$canPagos = mysql_num_rows($resPagos);
$arrayPagos = array();
if ($canPagos > 0) {
	while ($rowPagos = mysql_fetch_array($resPagos)) {
		$arrayPagos[$rowPagos['idpresentacion']] = $rowPagos['idpresentacion'];
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>

<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["zebra", "filter"], 
			headers:{3:{sorter:false, filter:false},
					 4:{sorter:false, filter:false},
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
				 	 15:{sorter:false, filter:false},
				 	 16:{sorter:false, filter:false},
				 	 17:{sorter:false},
				 	 18:{sorter:false, filter:false},
				 	 19:{sorter:false, filter:false},
				 	 20:{sorter:false, filter:false}},
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

<title>.: Presentaciones S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'menu.php'" /></p>
	 	<h2>Presentaciones S.S.S.</h2>
	 	<p><input type="button" name="todas" value="Ver Todas las Presentaciones" onClick="location.href = 'presentacion.todas.php'" /></p>
  <?php if ($canPresentacionPeriodo == 0 && $usuario == 'sistemas') {?>
	 		<p><input type="button" name="nueva" value="Nueva Presentacion" onClick="location.href = 'presentacion.nueva.php'" /></p>
  <?php } 
        if ($canPresentacion > 0) {?>
			 <table id="listaResultado" class="tablesorter" style="text-align: center;">
			 	<thead>
			 		<tr>
			 			<th style="font-size: 11px" rowspan="2">Id</th>
			 			<th rowspan="2" class="filter-select" data-placeholder="Selccione" style="font-size: 11px">Periodo</th>
			 			<th rowspan="2" style="font-size: 11px" rowspan="2">Cant. Reg.</th>
			 			<th style="font-size: 11px" colspan="2">Credito</th>
			 			<th style="font-size: 11px" colspan="2">Debito</th>
			 			<th rowspan="2" style="font-size: 11px">Fecha Present.</th>
			 			<th rowspan="2" style="font-size: 11px">Fecha Formato</th>
			 			<th rowspan="2" style="font-size: 11px">Fecha Integral</th>
			 			<th rowspan="2" style="font-size: 11px">Fecha Subsidio</th>
			 			<th rowspan="2" style="font-size: 11px">Fecha Cierre</th>
			 			<th rowspan="2" style="font-size: 11px">Deposito</th>
			 			<th rowspan="2" style="font-size: 11px">Informacion</th>
			 			<th rowspan="2" style="font-size: 11px">Errores</th>
			 			<th rowspan="2" style="font-size: 11px">Acciones</th>
			 			<th rowspan="2" class="filter-select" data-placeholder="Selccione" style="font-size: 11px">Estado</th>
			 		</tr>
			 		<tr>
			 			<th style="font-size: 11px">$ Comp</th>
			 			<th style="font-size: 11px">$ Soli</th>
			 			<th style="font-size: 11px">$ Comp</th>
			 			<th style="font-size: 11px">$ Soli</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			<?php while ($rowPresentacion = mysql_fetch_array($resPresentacion)) { 
					if ($rowPresentacion['fechacancelacion'] == null) { ?>
					<tr>
						<td style="font-size: 12px"><?php echo $rowPresentacion['id'] ?></td>
						<td style="font-size: 12px"><?php echo $rowPresentacion['carpeta']." <br> ".$rowPresentacion['periodo'] ?></td>
						<td style="font-size: 12px"><?php echo $rowPresentacion['cantfactura'] ?></td>
						<td style="font-size: 12px"><?php echo number_format($rowPresentacion['impcomprobantes'],2,",",".") ?></td>
						<td style="font-size: 12px"><?php echo number_format($rowPresentacion['impsolicitado'],2,",",".") ?></td>
						<td style="font-size: 12px"><?php echo number_format($rowPresentacion['impcomprobantesd'],2,",",".") ?></td>
						<td style="font-size: 12px"><?php echo number_format($rowPresentacion['impsolicitadod'],2,",",".") ?></td>
						<td style="font-size: 12px"><?php echo $rowPresentacion['fechapresentacion'] ?></td>
						<td style="font-size: 12px"><?php echo $rowPresentacion['fechadevformato'] ?></td>
						<td style="font-size: 12px"><?php echo $rowPresentacion['fechaintegral'] ?></td>
						<td style="font-size: 12px"><?php echo $rowPresentacion['fecharendicion'] ?></td>
						<td style="font-size: 12px"><?php echo $rowPresentacion['fechacierre'] ?></td>
						<td style="font-size: 12px"><?php echo $rowPresentacion['fechadeposito'] ?> <br><b>[<?php echo number_format($rowPresentacion['montodepositado'],2,",",".") ?>]</b></td>
						<td>
							<input style="margin-bottom: 5px" type="button" value="Facturas" onClick="location.href = 'presentacion.facturas.php?id=<?php echo $rowPresentacion['id'] ?>'"/></br>
							<input style="margin-bottom: 5px" type="button" value="Detalle" onClick="location.href = 'presentacion.detalle.php?id=<?php echo $rowPresentacion['id'] ?>'"/></br>
					  <?php if (in_array($rowPresentacion['id'],$arrayPagos)) { ?>
								<input type="button" value="Pagos" onClick="location.href = 'presentacion.pagos.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					  <?php	}  ?>
						</td>
						<td>
					<?php	if ($rowPresentacion['fechadevformato'] != NULL && $rowPresentacion['cantformatonok'] != 0) { ?>
								<input style="margin-bottom: 5px" type="button" value="Formato" onClick="location.href = 'presentacion.erroresformato.php?id=<?php echo $rowPresentacion['id'] ?>'"/></br>
					<?php   } ?>
					<?php	if ($rowPresentacion['fechaintegral'] != NULL && $rowPresentacion['cantintegralnok'] != 0) { ?>
								<input type="button" value="Integrales" onClick="location.href = 'presentacion.erroresintegral.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					<?php   } ?>
						</td>
				 <?php 	if ($rowPresentacion['fechacancelacion'] == NULL) { 
				    		 if ($usuario != 'sistemas') { 
				    		 	$display = 'style="display: none; margin-bottom: 5px"'; 
				    		 } else { 
				    		 	$display = 'style="margin-bottom: 5px"'; 
				    		 }
					    	if ($rowPresentacion['fechapresentacion'] == NULL) { ?>
								<td>	
									<input <?php echo $display ?> type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/></br>
									<input <?php echo $display ?> type="button" value="Generar Archivo" onClick="location.href = 'presentacion.archivo.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
								</td>	
								<td style="font-size: 12px">EN PROCESO</td>
					  <?php } else { 
								if ($rowPresentacion['fechadevformato'] == NULL) { ?>
									<td>
										<input <?php echo $display ?> type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/></br>
										<input <?php echo $display ?> type="button" value="Formato" onClick="location.href = 'presentacion.devformato.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
						  			</td>
						  			<td style="font-size: 12px">EN PROCESO</td>
						  <?php } else { 
						  			if ($rowPresentacion['fechaintegral'] == NULL) { ?>
						  				<td>
						  			  	  	<input <?php echo $display ?> type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/></br>
											<input <?php echo $display ?> type="button" value="Integral" onClick="location.href = 'presentacion.devintegral.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
						  			  	</td>
						  			  	<td style="font-size: 12px">EN PROCESO</td>
						  	  <?php } else { 	
					      				if ($rowPresentacion['fecharendicion'] == NULL) { ?>
					      					<td>
					      						<input <?php echo $display ?> type="button" value="Cancelar" onClick="location.href = 'presentacion.cancelar.php?id=<?php echo $rowPresentacion['id'] ?>'"/></br>
					      						<input <?php echo $display ?> type="button" value="Rendicion" onClick="location.href = 'presentacion.devrendicion.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					     			 		</td>
					     			 		<td style="font-size: 12px">EN PROCESO</td>	
					     		  <?php } else { 
					     			 		 if ($rowPresentacion['fechadeposito'] == NULL) {?>
					     						<td>
					     							<input style="margin-bottom: 5px" type="button" value="Deposito" onClick="location.href = 'presentacion.deposito.php?id=<?php echo $rowPresentacion['id'] ?>'"/></br>
					     				  	  <?php if ($rowPresentacion['fechacierre'] == NULL) { ?>
					     								<input type="button" value="Cargar Rete" onClick="location.href = 'presentacion.retenciones.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					     					  <?php } else { ?>
					     								<input type="button" value="Ver Rete" onClick="location.href = 'presentacion.retenciones.consulta.php?id=<?php echo $rowPresentacion['id'] ?>'"/>	
					     					  <?php } ?>
					     						</td>
					     						<td style="font-size: 12px">EN PROCESO</td>
					     			 <?php 	 } else {  ?>
					     						<td>
					     					<?php if (in_array($rowPresentacion['id'],$arrayPagos)) {?>
					     							<input style="margin-top: 5px" type="button" value="Carga R.A.F." onClick="location.href = 'presentacion.pagos.carga.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					     					<?php } else { 
					     								if ($rowPresentacion['fechacierre'] == NULL) { ?>
					     									<input type="button" value="Carga Reten" onClick="location.href = 'presentacion.retenciones.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					     			 		  	  <?php } else { ?>
					     									<input type="button" value="Enviar Pago" onClick="location.href = 'presentacion.pagos.interbanking.php?id=<?php echo $rowPresentacion['id'] ?>'"/>
					     						  <?php }
					     						  } ?>
					     						</td>
					     						<td style="font-size: 12px"><font color="blue">FINALIZADA</font></td>
					     			 <?php	} 
					     			 	}
						  			}
						  	 	}
						  	}	
				    	} 
			 		 } 	
				  }  ?>
			  </tbody>
			</table>
  <?php } else { ?>
			<p style="color: blue"><b>NO HAY PRESENTACIONES HASTA EL MOMENTO</b></p>
  <?php } ?>
	</div>
</body>
</html>