<?php
include_once 'include/conector.php';
require_once 'include/phpExcel/Classes/PHPExcel.php';

$idCabecera = $_GET['id'];

$sqlPagos = "SELECT inteinterbanking.*, madera.prestadoresauxiliar.cbu,
			DATE_FORMAT(inteinterbanking.fechaenvio, '%d-%m-%Y') as fechaenvio,
			madera.prestadoresauxiliar.cbu
			FROM inteinterbanking
			LEFT JOIN madera.prestadores on inteinterbanking.cuit = madera.prestadores.cuit
			LEFT JOIN madera.prestadoresauxiliar on madera.prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
			WHERE inteinterbanking.idpago = $idCabecera";
$resPagos = mysql_query($sqlPagos);

$sqlTotales = "SELECT * FROM inteinterbankingcabecera WHERE id = $idCabecera";
$resTotales = mysql_query($sqlTotales);
$rowTotales = mysql_fetch_assoc($resTotales);

$arrayFacturas = array();
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
ksort($arrayFacturas);

$nrosecu = $rowTotales['nrosecuencia'];
$maquina = $_SERVER['SERVER_NAME'];
$fechagenera=date("YmdHis");
$archivo_xls_name="inter_pago_$nrosecu.xls";
$archivo_txt_name="inter_pago_$nrosecu.txt";
if(strcmp("localhost",$maquina)!=0) {
	$archivo_xls_name="/home/sistemas/Documentos/Repositorio/Interbanking/".$archivo_xls_name;
	$archivo_txt_name="/home/sistemas/Documentos/Repositorio/Interbanking/".$archivo_txt_name;
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator($_SESSION['usuario'])
->setLastModifiedBy($_SESSION['usuario'])
->setTitle("Interbankgin")
->setSubject("Modulo de Pago SSS")
->setDescription("Archivo para importar pago SSS")
->setCategory("Modulo de Pago SSS");

$objPHPExcel->getActiveSheet()->setTitle($fechagenera);
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.5);
$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0);
$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.5);
$objPHPExcel->getActiveSheet()->getPageMargins()->setHeader(0.25);
$objPHPExcel->getActiveSheet()->getPageMargins()->setFooter(0.25);

$objPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
$objPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Nro Int.');
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Tipo');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Periodo');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'C.U.I.T.');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'C.B.U.');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Fecha');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Nro. Comp.');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->setCellValue('H1', '$ Comp.');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->setCellValue('I1', '$ Deb.');
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$objPHPExcel->getActiveSheet()->setCellValue('J1', '$ O.S.');
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
$objPHPExcel->getActiveSheet()->setCellValue('K1', '$ S.S.S.');
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
$objPHPExcel->getActiveSheet()->setCellValue('L1', '$ Ret');
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
$objPHPExcel->getActiveSheet()->setCellValue('M1', '$ Transf.');

$file = fopen($archivo_txt_name, "w");
$cbuOS = "0110599520000054914032";
$obsU = str_pad(" ",61,' ',STR_PAD_RIGHT);
$secuencia = str_pad($nrosecu,8,' ',STR_PAD_RIGHT);
$blanco = str_pad(" ",123,' ',STR_PAD_RIGHT);
$filaUtxt = "*U*".$cbuOS."D".date("Ymd")."S".$obsU."00000".date("d/m/y").$secuencia.$blanco;
fwrite($file, $filaUtxt . PHP_EOL);

$fila=1;
$obs = "";
foreach ($arrayFacturas as $key => $rowFactura) {  
	$fila++;
	$pos = strpos($key, "TOTAL");
	if ($pos === false) {
		$obs .= $rowFactura['nrocomprobante']."-";
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, $rowFactura['nrocominterno']);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, $rowFactura['tipoarchivo']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, $rowFactura['periodo']);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $rowFactura['cuit']);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, "");
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, $rowFactura['fechacomprobante']);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$fila, $rowFactura['nrocomprobante']);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$fila, $rowFactura['impcomprobanteintegral']);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$fila, $rowFactura['impdebito']);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$fila, $rowFactura['impnointe'] + $rowFactura['impobrasocial']);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$fila, $rowFactura['impmontosubsidio']);
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$fila, "");
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$fila, "");
	} else {
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, "");
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, "");
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, "");
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, $rowFactura['cuit']);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, "'".$rowFactura['cbu']."'");
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, "");
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$fila, "");
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$fila, $rowFactura['impcomprobanteintegral']);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$fila, $rowFactura['impdebito']);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$fila, $rowFactura['impnointe'] + $rowFactura['impobrasocial']);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$fila, $rowFactura['impmontosubsidio']);
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$fila, $rowFactura['impretencion']);
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$fila, $rowFactura['impapagar']);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':M'.$fila)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':M'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':M'.$fila)->getFill()->getStartColor()->setARGB('00CCFF');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':M'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		
		$impApagar = number_format($rowFactura['impapagar'],2,"","");
		$impApagar = str_pad($impApagar,17,0,STR_PAD_LEFT);
		
		$obs = substr($obs, 0, -1);
		if (strlen($obs) > 60) { $obs = "VARIAS"; }
		$obs = str_pad($obs,60,' ',STR_PAD_RIGHT);
		
		$nrofactura = str_pad("VER OBS.",12,' ',STR_PAD_RIGHT);
		$tipOrd = str_pad(" ",2,' ',STR_PAD_RIGHT);
		$nroOrd = str_pad(" ",12,' ',STR_PAD_RIGHT);
		$codigoCliente = str_pad("CODIGO",12,' ',STR_PAD_RIGHT);
		$impRet = number_format($rowFactura['impretencion'],2,"","");
		$tipRet = "  ";
		if ($impRet != 0) {
			$tipRet = "02";
		}
		$impRet = str_pad($impRet,12,0,STR_PAD_LEFT);
		
		$nroNotaCred = str_pad(" ",12,' ',STR_PAD_RIGHT);
		$impNotaCred = str_pad(0,10,0,STR_PAD_LEFT);
		$espacios = str_pad(" ",51,' ',STR_PAD_RIGHT);
		$filatxt = "*M*".$rowFactura['cbu'].$impApagar.$obs."FA".$nrofactura.$tipOrd.$nroOrd.$codigoCliente.$tipRet.$impRet.$nroNotaCred.$impNotaCred.$rowFactura['cuit'].$espacios;
		fwrite($file, $filatxt . PHP_EOL);
		$obs = "";
	}
}

$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getFill()->getStartColor()->setARGB('00CCFF');
$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//TOTALES
$fila++;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$fila, "");
$objPHPExcel->getActiveSheet()->setCellValue('B'.$fila, "");
$objPHPExcel->getActiveSheet()->setCellValue('C'.$fila, "");
$objPHPExcel->getActiveSheet()->setCellValue('D'.$fila, "");
$objPHPExcel->getActiveSheet()->setCellValue('E'.$fila, "");
$objPHPExcel->getActiveSheet()->setCellValue('F'.$fila, "");
$objPHPExcel->getActiveSheet()->setCellValue('G'.$fila, "");
$objPHPExcel->getActiveSheet()->setCellValue('H'.$fila, $rowTotales['impcomprobanteintegral']);
$objPHPExcel->getActiveSheet()->setCellValue('I'.$fila, $rowTotales['impdebito']);
$objPHPExcel->getActiveSheet()->setCellValue('J'.$fila, $rowTotales['impnointe'] + $rowTotales['impobrasocial']);
$objPHPExcel->getActiveSheet()->setCellValue('K'.$fila, $rowTotales['impmontosubsidio']);
$objPHPExcel->getActiveSheet()->setCellValue('L'.$fila, $rowTotales['impretencion']);
$objPHPExcel->getActiveSheet()->setCellValue('M'.$fila, $rowTotales['impapagar']);

$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':M'.$fila)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':M'.$fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':M'.$fila)->getFill()->getStartColor()->setARGB('00CCFF');
$objPHPExcel->getActiveSheet()->getStyle('A'.$fila.':M'.$fila)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($archivo_xls_name);
fclose($file);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Archivos Interbanking S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input type="reset" name="volver" value="Volver" onClick="location.href = 'interbanking.php'" /></p>
	 	<h2>Archivos Generados Correctamente</h2>
		<h3>Encontrar los archivos en la carpeta</h3>
		<h3 style="color: blue"> <?php echo $archivo_xls_name ?></h3>
		<h3 style="color: blue"> <?php echo $archivo_txt_name ?></h3>
	</div>
</body>
</html>
