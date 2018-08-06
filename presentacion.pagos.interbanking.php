<?php
include_once 'include/conector.php';
$usuario = $_SESSION['usuario'];
$idPresentacion = $_GET['id'];

$sqlTotalesCuit = "SELECT inteinterbanking.*, 
						 DATE_FORMAT(inteinterbankingcabecera.fechapago, '%d-%m-%Y') as fechapago,
						 DATE_FORMAT(inteinterbanking.fechaenvio, '%d-%m-%Y') as fechaenvio,
						 madera.prestadoresauxiliar.cbu, madera.prestadoresauxiliar.interbanking, 
						 madera.prestadoresauxiliar.fechainterbanking
	FROM inteinterbanking 
	LEFT JOIN madera.prestadores on inteinterbanking.cuit = madera.prestadores.cuit
	LEFT JOIN madera.prestadoresauxiliar on madera.prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
	LEFT JOIN inteinterbankingcabecera on inteinterbanking.idpago = inteinterbankingcabecera.id
	WHERE idpresentacion = $idPresentacion ";
$resTotalesCuit = mysql_query($sqlTotalesCuit);
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
<script src="include/jquery.blockUI.js" type="text/javascript"></script>
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
			 	 4:{sorter:false, filter:false},
			 	 5:{sorter:false, filter:false},
			 	 6:{sorter:false, filter:false},
			 	 7:{sorter:false, filter:false},
			 	 8:{sorter:false, filter:false},
			 	 9:{sorter:false, filter:false},
			 	 10:{sorter:false, filter:false},
			 	 11:{sorter:false, filter:false}},
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

function validar(fomulario) {
	fomulario.submit.disabled = true;
	$.blockUI({ message: "<h1>Enviando a Pago... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
	return true;
}


</script>
<title>.: Generacion Pagos :.</title>
<style type="text/css" media="print">
.nover {display:none}
</style>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
		<?php include_once("include/detalle.php")?>
		<form action="presentacion.pagos.interbanking.guardar.php?id=<?php echo $idPresentacion ?>" onSubmit="return validar(this)"  method="post">
			<h2>Enviar a Pago por Interbanking</h2>
			<table id="listaResultado" class="tablesorter" style="text-align: center; font-size: 15px; width: 1100px" >
				<thead>
				 	<tr>
				 		<th>C.U.I.T.</th>
				 		<th>C.B.U.</th>
				 		<th>$ Comp.</th>
				 		<th>$ Deb.</th>
				 		<th>$ No Int</th>
				 		<th>$ Soli.</th>
				 		<th>$ O.S.</th>
				 		<th>$ S.S.S</th>
				 		<th>$ Ret.</th>
				 		<th>$ A Pagar</th>
				 		<th>Enviar</th>
				 		<th>Pagado</th>
				 	</tr>
				 </thead>
				 <tbody>
		<?php	 $totComSub = 0;
				 $totSolSub = 0;
				 $totMonDeb = 0;
				 $totMonNOI = 0;
				 $totMonSub = 0;
				 $totMonOS = 0;
				 $totApagar = 0;	
				 $totRete = 0;
				 while ($rowTotales = mysql_fetch_array($resTotalesCuit)) {  
					$totComSub += $rowTotales['impcomprobanteintegral'];
					$totSolSub += $rowTotales['impsolicitadosubsidio'];
					$totMonDeb += $rowTotales['impdebito'];
					$totMonNOI += $rowTotales['impnointe'];
					$totMonOS += $rowTotales['impobrasocial'];
					$totMonSub += $rowTotales['impmontosubsidio'];
					$totRete += $rowTotales['impretencion'];
					$totApagar += $rowTotales['impapagar']; ?>
					<tr>
						<td><?php echo $rowTotales['cuit'] ?></td>
						<td><?php echo $rowTotales['cbu'] ?></td>
						<td><?php echo number_format($rowTotales['impcomprobanteintegral'],2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impdebito'],2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impnointe'],2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impsolicitadosubsidio'],2,",",".") ?></td>					
						<td><?php echo number_format($rowTotales['impobrasocial'],2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impmontosubsidio'],2,",",".") ?></td>				
						<td><?php echo number_format($rowTotales['impretencion'],2,",",".") ?></td>
						<td><?php echo number_format($rowTotales['impapagar'],2,",",".") ?></td>
						<td>
						<?php if ($rowTotales['nopagar'] == 1) {
									echo "NP";
							  } else {
								  if ($rowTotales['fechaenvio'] != NULL) { 
									echo $rowTotales['fechaenvio']; 
								  } else { 
								  		if ($rowTotales['cbu'] != NULL && $rowTotales['interbanking'] == 1 && $rowTotales['fechainterbanking'] != NULL && $rowTotales['impapagar'] > 0) { ?>
											<input type="checkbox" id="<?php echo $rowTotales['cuit']?>" name="<?php echo $rowTotales['cuit']?>" value="<?php echo $rowTotales['cuit'] ?>" />
								  <?php } 
								  } 
							   }?>		
						</td>
						<td><?php if ($rowTotales['fechapago'] != NULL) { echo $rowTotales['fechapago']; } ?></td>
					</tr>
		   <?php } ?>
				 </tbody>
				 <tr>
					<th colspan="2" rowspan="3">TOTALES</td>
					<th rowspan="2"><?php echo number_format($totComSub,2,",",".") ?></td>
					<th rowspan="2"><?php echo number_format($totMonDeb,2,",",".") ?></td>
					<th rowspan="2"><?php echo number_format($totSolSub,2,",",".") ?></td>
					<th><?php echo number_format($totMonNOI,2,",",".") ?></td>
					<th><?php echo number_format($totMonOS,2,",",".") ?></td>
					<th rowspan="2"><?php echo number_format($totMonSub,2,",",".") ?></td>
					<th rowspan="2"><?php echo number_format($totRete,2,",",".") ?></th>
					<th rowspan="2"><?php echo number_format($totApagar,2,",",".") ?></th>
					<th rowspan="3" colspan="2"><input type="submit" value="Enviar a Pago" name="submit"/> </th>
				</tr>
				<tr>					
					<th colspan="2"><?php echo number_format($totMonNOI + $totMonOS,2,",",".") ?></td>
				</tr>
				<tr>
					<th>COM<br><?php echo number_format($totComSub,2,",",".") ?></td>
					<th colspan="3">DEB+SOL+NOI<br><?php echo number_format($totMonDeb + $totSolSub + $totMonNOI,2,",",".") ?></td>
					<th colspan="2">NOI+OS+SUB+DEB<br><?php echo number_format($totMonNOI + $totMonOS + $totMonSub + $totMonDeb,2,",",".") ?></th>
					<th colspan="2">RET+DEB+PAG<br><?php echo number_format($totRete + $totMonDeb + $totApagar,2,",",".") ?></th>
				</tr>	
			</table>
		</form>
	</div>
</body>
</html>