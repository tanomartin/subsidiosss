<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];
$idFactura = $_POST['nrocominterno'];
$tipoRev = $_POST['tipo'];
$tipoArray = explode("-",$tipoRev);
$tipo = $tipoArray[0];
$tipoDescrip = $tipoArray[1];
$posibleReversion = 0;


$sqlCarpetas = "SELECT periodosincluidos FROM intepresentacion i, intecronograma c where i.id = $idPresentacion and i.idcronograma = c.id";
$resCarpetas = mysql_query($sqlCarpetas);
$rowCarpetas = mysql_fetch_assoc($resCarpetas);
$peridosArray = explode(",",$rowCarpetas['periodosincluidos']);
$periodosPermitidos = "(".$peridosArray[0].",".$peridosArray[1].")";

$sqlBusquedaFacturaAnterior = "SELECT * FROM intepresentacion p, intepresentaciondetalle d 
								WHERE p.fechacancelacion is null and 
									  p.fechapresentacion is not null and 
									  p.id = d.idpresentacion and 
									  d.nrocominterno = $idFactura and
									  d.periodo in $periodosPermitidos and
									  d.codpractica not in (97,98,99)";
$resBusquedaFacturaAnterior = mysql_query($sqlBusquedaFacturaAnterior);
$canBusquedaFacturaAnterior = mysql_num_rows($resBusquedaFacturaAnterior);
if ($canBusquedaFacturaAnterior != 0) {	
	$arrayReversion = array();
	$rowBusquedaFacturaAnterior = mysql_fetch_assoc($resBusquedaFacturaAnterior);
	$index = 0;
	$arrayReversion[$index]['datos'] = $rowBusquedaFacturaAnterior;
	$arrayReversion[$index]['tipo'] = 'DB';
	//SOLO DB
	if ($tipo == 1) {
		$posibleReversion = 1;
	} else {
		$index = 1;
		$sqlFacturaNueva = "SELECT f.id as nrocominterno, 111001 as codigoob,
					       (CASE
					           WHEN madera.titulares.cuil is not NULL THEN madera.titulares.cuil
					           WHEN madera.titularesdebaja.cuil is not NULL THEN madera.titularesdebaja.cuil
					           WHEN madera.familiares.cuil is not NULL THEN madera.familiares.cuil
					           WHEN madera.familiaresdebaja.cuil is not NULL THEN madera.familiaresdebaja.cuil
					       END) as cuil,
					       DATE_FORMAT(fp.fechapractica,'%Y%m') as periodo,
					       p.cuit, 
					       p.nombre, 
					       f.idTipocomprobante as tipocomprobante, 
					       'E' as tipoemision,
					       DATE_FORMAT(f.fechacomprobante,'%d/%m/%Y') as fechacomprobante,
					       f.nroautorizacion as cae, 
					       f.puntodeventa as puntoventa, 
					       f.nrocomprobante, 
					       f.importecomprobante as impcomprobante,
					       f.totaldebito, 
						   0 as nointe, 
						   fi.totalsolicitado as impsolicitado,
					       0 as control,
					       FORMAT(ps.codigopractica,0) as codpractica,
					       IF (ps.codigopractica = 86 or ps.codigopractica = 87, 1, ROUND(fp.cantidad,0)) as cantidad, 
						   0 as provincia,
					       IF (fi.dependencia = 1, 'S', 'N') as dependencia
					FROM madera.facturas f, madera.facturasprestaciones fp, madera.facturasintegracion fi, 
						 madera.prestadores p, madera.practicas ps, madera.facturasbeneficiarios fb
					LEFT JOIN madera.titulares ON fb.nroorden = 0 AND
					                       madera.titulares.nroafiliado = fb.nroafiliado
					LEFT JOIN madera.titularesdebaja ON fb.nroorden = 0 AND
					                       madera.titularesdebaja.nroafiliado = fb.nroafiliado
					LEFT JOIN madera.familiares ON fb.nroorden != 0 AND
					                       madera.familiares.nroafiliado = fb.nroafiliado AND
					                       madera.familiares.nroorden = fb.nroorden
					LEFT JOIN madera.familiaresdebaja ON fb.nroorden != 0  AND
					                       madera.familiaresdebaja.nroafiliado = fb.nroafiliado AND
					                       madera.familiaresdebaja.nroorden = fb.nroorden
					WHERE 
					f.id = $idFactura and
					f.id = fb.idFactura and 
					f.autorizacionpago = 0 and 
					fb.id = fp.idFacturabeneficiario and 
					fp.id = fi.idFacturaprestacion and
					f.idPrestador = p.codigoprestador and
					fp.idpractica = ps.idpractica
					ORDER BY periodo, f.id";
		$resFacturaNueva = mysql_query($sqlFacturaNueva);
		$canFacturaNueva = mysql_num_rows($resFacturaNueva);
		if ($canFacturaNueva == 1) {
			$rowFacturaNueva = mysql_fetch_assoc($resFacturaNueva);	
			//SOLO DB/DC
			if ($tipo == 2) {
				$arrayReversion[$index]['datos'] = $rowFacturaNueva;
				$arrayReversion[$index]['tipo'] = 'DC';
				$posibleReversion = 1;
			}
			//SOLO DB/DS (misma pres)
			if ($tipo == 3) {
				$arrayReversion[$index]['datos'] = $rowFacturaNueva;
				$arrayReversion[$index]['tipo'] = 'DS';
				$posibleReversion = 1;
			}
			//SOLO DB/DS (2 pres)
			if ($tipo == 4) {
				$arrayReversion[$index]['datos'] = $rowFacturaNueva;
				$arrayReversion[$index]['tipo'] = 'DS';
				$posibleReversion = 1;
			}
			
			$cuil = $arrayReversion[$index]['datos']['cuil'];
			$sqlCertificado = "SELECT codigocertificado, DATE_FORMAT(vencimientocertificado,'%d/%m/%Y') as vencimientocertificado  FROM madera.discapacitados where cuil = $cuil";
			$resCertificado = mysql_query($sqlCertificado);
			$canCertificado = mysql_num_rows($resCertificado);
			if ($canCertificado == 1) {
				$rowCertificado = mysql_fetch_assoc($resCertificado);
				$arrayReversion[$index]['datos']['codcertificado'] = $rowCertificado['codigocertificado'];
				$arrayReversion[$index]['datos']['vtocertificado'] = $rowCertificado['vencimientocertificado'];
			} else {
				$posibleReversion = 0;
			}
			
		} else {
			$posibleReversion = 0;
		}
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Nueva Reversiones Presentaciones S.S.S. :.</title>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<script src="include/funcionControl.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/tablas.css"/>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 	<p><input type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.reversiones.nueva.php?id=<?php echo $idPresentacion ?>'" /></p>
	 	<h2>Nueva Reversion para la Presentacion</h2>
	 	<?php include_once("include/detalle.php")?>
	 	<h3>Reversion Id Factura <span style="color: blue"><?php echo $idFactura ?></span> - Tipo <span style="color: blue"><?php echo $tipoDescrip ?></span></h3>
	 	<?php if ($posibleReversion == 1) { ?>
	 		<form action="presentacion.reversiones.vistaprevia.php?id=<?php echo $idPresentacion ?>" method="post" onSubmit="return validar()">
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
			 			<?php foreach ($arrayReversion as $reversion) { ?>
			 					<tr>
								<td style="font-size: 12px"><?php echo number_format($reversion['datos']['nrocominterno'],0,"",".") ?></td>
								<td style="font-size: 12px"><?php echo $reversion['tipo'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['codigoob'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['cuil'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['codcertificado'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['vtocertificado'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['periodo'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['cuit'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['tipocomprobante'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['tipoemision'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['fechacomprobante'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['cae'] ?></td>
								<td style="font-size: 12px"><?php echo (int) $reversion['datos']['puntoventa'] ?></td>
								<td style="font-size: 12px"><?php echo (int) $reversion['datos']['nrocomprobante'] ?></td>					
								<?php if ( $reversion['tipo'] == "DB") { ?>
									<td style="font-size: 12px;"><?php echo "(".number_format($reversion['datos']['impcomprobante'],2,",",".").")" ?></td>
									<td style="font-size: 12px;"><?php echo "(".number_format($reversion['datos']['impsolicitado'],2,",",".").")" ?></td>
								<?php } else { ?>
									<td style="font-size: 12px;"><?php echo number_format($reversion['datos']['impcomprobante'],2,",",".") ?></td>
									<td style="font-size: 12px;"><?php echo number_format($reversion['datos']['impsolicitado'],2,",",".") ?></td>
								<?php } ?>
								<td style="font-size: 12px"><?php echo $reversion['datos']['codpractica'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['cantidad'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['provincia'] ?></td>
								<td style="font-size: 12px"><?php echo $reversion['datos']['dependencia'] ?></td>
							</tr>
			 			<?php } ?>
			 			</tbody>
		 			</table>
	 			</div>
	 			<p><input type="submit" value="Guardar Reversion" style="margin-top: 15px"></input></p>
	 		</form>
	 	<?php } else { ?>
	 			<h3 style="color: red">No se encontro la informacion necesaria para realizar la reversion</br>Revise la informacion cargada</h3>
	 	<?php } ?>
	 </div>
</body>
</html>
