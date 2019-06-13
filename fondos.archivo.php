<?php include_once 'include/conector.php';
set_time_limit(0);

$idPresentacion = $_GET['id'];
$carpeta = $_GET['carpeta'];

$sqlApliFondo = "SELECT
					d.nrocominterno,
					i.clave, 
					i.rnos,
					i.tipoarchivo,
					i.periodopresentacion,
				 	i.periodoprestacion,
					i.cuil,
					i.codpractica,
					i.impsubsidiado,
					d.impmontosubsidio,
					i.cuit,
					i.impsolicitado,
					i.nroenvioafip,
					madera.prestadoresauxiliar.cbu,
					intepagosdetalle.nroordenpago,
					DATE_FORMAT(intepagoscabecera.fechatransferencia,'%d/%m/%Y') as fechatransferencia,
					IFNULL(intepagosdetalle.impretencion,0) as impretencion,	
					(d.impmontosubsidio - IFNULL(intepagosdetalle.impretencion,0)) as imppago,		
					IF(i.tipoarchivo != 'DB', IF((d.impsolicitado - d.impmontosubsidio)<0,0,(d.impsolicitado - d.impmontosubsidio)),d.impsolicitado + d.impmontosubsidio) as impos,
					0 as impoc,
					intepagosdetalle.recibo,
					intepagosdetalle.imprecupero,
					intepagosdetalle.observacion			
				 FROM interendicion i, intepresentaciondetalle d
				 LEFT JOIN intepagosdetalle ON d.idpresentacion = intepagosdetalle.idpresentacion AND 
											   d.nrocominterno = intepagosdetalle.nrocominterno
				 LEFT JOIN intepagoscabecera ON intepagosdetalle.idpresentacion = intepagoscabecera.idpresentacion AND 
												intepagosdetalle.nroordenpago = intepagoscabecera.nroordenpago
				 LEFT JOIN madera.prestadores ON d.cuit = madera.prestadores.cuit
				 LEFT JOIN madera.prestadoresauxiliar ON madera.prestadores.codigoprestador = madera.prestadoresauxiliar.codigoprestador
				 WHERE
					i.idpresentacion = $idPresentacion and
					i.idpresentacion = d.idpresentacion and
					i.tipoarchivo = d.tipoarchivo and
					i.periodoprestacion = d.periodo and
					i.cuil = d.cuil and
					i.codpractica = d.codpractica and
					i.cuit = d.cuit and
					i.tipocomprobante = d.tipocomprobante and
					i.puntoventa = d.puntoventa and
					i.nrocomprobante = d.nrocomprobante
				 ORDER BY i.clave";
$resApliFondo = mysql_query($sqlApliFondo);
$anio = substr($carpeta,0,4);
$archivoGeneracion = "archivos/$anio/$carpeta/fondos/111001-".$carpeta."_DR.DEVOLUCION.txt";

$arrayReversiones = array();
$sqlReversionesFuturas = "SELECT d.nrocominterno
FROM intepresentaciondetalle d
WHERE d.idpresentacion > $idPresentacion AND d.tipoarchivo = 'DB'";
$resReversionesFuturas = mysql_query($sqlReversionesFuturas);
$canReversionesFuturas = mysql_num_rows($resReversionesFuturas);
if ($canReversionesFuturas > 0) {
	while ($rowReversionesFuturas = mysql_fetch_assoc($resReversionesFuturas)) {
		$arrayReversiones[$rowReversionesFuturas['nrocominterno']] = $rowReversionesFuturas['nrocominterno'];
	}
}

$arrayCredito = array();
$sqlDebitos = "SELECT d.nrocominterno FROM intepresentaciondetalle d 
				WHERE d.idpresentacion = $idPresentacion AND d.tipoarchivo = 'DB'";
$resDebitos = mysql_query($sqlDebitos);
$canDebitos = mysql_num_rows($resDebitos);
if ($canDebitos > 0) {
	$whereIn = "(";
	while ($rowDebitos = mysql_fetch_assoc($resDebitos)) {
		$whereIn .= $rowDebitos['nrocominterno'].",";
	}
	$whereIn = substr($whereIn, 0, -1);
	$whereIn .= ")";
	
	$sqlCredito = "SELECT d.nrocominterno FROM intepresentaciondetalle d
					WHERE d.idpresentacion = $idPresentacion AND d.tipoarchivo != 'DB' and nrocominterno in $whereIn";
	$resCredito = mysql_query($sqlCredito);
	$canCredito = mysql_num_rows($resCredito);
	if ($canCredito > 0) {
		$whereIn = "(";
		while ($rowCredito = mysql_fetch_assoc($resCredito)) {
			$arrayCredito[$rowCredito['nrocominterno']] = 0;
			$whereIn .= $rowCredito['nrocominterno'].",";
		}
		$whereIn = substr($whereIn, 0, -1);
		$whereIn .= ")";
		
		$sqlCreditoMontos = "SELECT d.nrocominterno, d.impmontosubsidio FROM intepresentaciondetalle d
							WHERE d.idpresentacion < $idPresentacion AND d.tipoarchivo = 'DS' and nrocominterno in $whereIn"; 
		$resCreditoMontos = mysql_query($sqlCreditoMontos);
		$canCreditoMontos = mysql_num_rows($resCreditoMontos);
		if ($canCreditoMontos > 0) {
			while ($rowCreditoMontos = mysql_fetch_assoc($resCreditoMontos)) {
				$arrayCredito[$rowCreditoMontos['nrocominterno']] += $rowCreditoMontos['impmontosubsidio'];
			}
		}
	}
}

$file = fopen($archivoGeneracion, "w");
while ($rowApliFondo = mysql_fetch_assoc($resApliFondo)) {
	$impDevolucionSSS = 0;
	$especial = false;
	
	if (array_key_exists($rowApliFondo['nrocominterno'],$arrayCredito) && $rowApliFondo['tipoarchivo'] != "DB") {
		$rowApliFondo['impmontosubsidio'] += $arrayCredito[$rowApliFondo['nrocominterno']];
		$rowApliFondo['imppago'] = $rowApliFondo['impmontosubsidio'] - $rowApliFondo['impretencion'];
		$rowApliFondo['impos'] = $rowApliFondo['impsolicitado'] - $rowApliFondo['impmontosubsidio'];
	}
				
	if ($rowApliFondo['tipoarchivo'] == "DB") {	
		$rowApliFondo['impos'] = (-1)*$rowApliFondo['impos'];
		$rowApliFondo['recibo'] = "";
		$rowApliFondo['cbu'] = "";
		$rowApliFondo['nroordenpago'] = "";
		$rowApliFondo['fechatransferencia'] = "";
		if (!array_key_exists($rowApliFondo['nrocominterno'],$arrayCredito)) {
			$impDevolucionSSS = $rowApliFondo['impmontosubsidio'];
			$rowApliFondo['impoc'] = $rowApliFondo['impsolicitado'] - $rowApliFondo['impmontosubsidio'];
			$especial = true;
		} else {
			$rowApliFondo['impos'] = 0;
			$rowApliFondo['imprecupero'] = 0;
		}
	}
				
	$impTransladado = 0;
	$impNoAplicado = 0;
	if ($rowApliFondo['nroordenpago'] == NULL and $rowApliFondo['tipoarchivo'] != "DB") {
		$especial = true;
		if (in_array($rowApliFondo['nrocominterno'],$arrayReversiones)) {
			$impTransladado = $rowApliFondo['impmontosubsidio'];
		} else {
			$impNoAplicado =  $rowApliFondo['impmontosubsidio'];
		}
		$rowApliFondo['impos'] = 0;
	}
				
	if ($especial) {
		$rowApliFondo['imppago'] = 0;
		$rowApliFondo['impretencion'] = 0;
		$rowApliFondo['impmontosubsidio'] = 0;
		$rowApliFondo['impoc'] = 0;
	}
	
	//CAMBIO EL SIGNO PARA PODER COLOCAR EL NEGATIVO EN LA PRIMERA POSICION
	$impsubsidiado = number_format($rowApliFondo['impsubsidiado'],2,"","");
	if ($impsubsidiado < 0) {
		$impsubsidiado = (-1)*$impsubsidiado;
		$impsubsidiado = "-".str_pad($impsubsidiado,9,0,STR_PAD_LEFT);
	} else {
		$impsubsidiado = str_pad($impsubsidiado,10,0,STR_PAD_LEFT);
	}
	
	$impsolicitado = number_format($rowApliFondo['impsolicitado'],2,"","");
	if ($impsolicitado < 0) {
		$impsolicitado = (-1)*$impsolicitado;
		$impsolicitado = "-".str_pad($impsolicitado,9,0,STR_PAD_LEFT);
	} else {
		$impsolicitado = str_pad($impsubsidiado,10,0,STR_PAD_LEFT);
	}
	
	$imppago = number_format($rowApliFondo['imppago'],2,"","");
	if ($imppago < 0) {
		$imppago = (-1)*$imppago;
		$imppago = "-".str_pad($imppago,9,0,STR_PAD_LEFT);
	} else {
		$imppago = str_pad($imppago,10,0,STR_PAD_LEFT);
	}
	
	$impmontosubsidio = number_format($rowApliFondo['impmontosubsidio'],2,"","");
	if ($impmontosubsidio < 0) {
		$impmontosubsidio = (-1)*$impmontosubsidio;
		$impmontosubsidio = "-".str_pad($impmontosubsidio,9,0,STR_PAD_LEFT);
	} else {
		$impmontosubsidio = str_pad($impmontosubsidio,10,0,STR_PAD_LEFT);
	}
	
	$impos = number_format($rowApliFondo['impos'],2,"","");
	if ($impos < 0) {
		$impos = (-1)*$impos;
		$impos = "-".str_pad($impos,9,0,STR_PAD_LEFT);
	} else {
		$impos = str_pad($impos,10,0,STR_PAD_LEFT);
	}
	
	$impoc = number_format($rowApliFondo['impoc'],2,"","");
	if ($impos < 0) {
		$impoc = (-1)*$impoc;
		$impoc = "-".str_pad($impoc,9,0,STR_PAD_LEFT);
	} else {
		$impoc = str_pad($impoc,10,0,STR_PAD_LEFT);
	}
	//********************************************************************************

	$linea = $rowApliFondo['clave']."|".
			 $rowApliFondo['rnos']."|".
			 $rowApliFondo['tipoarchivo']."|".
			 $rowApliFondo['periodopresentacion']."|".
			 $rowApliFondo['periodoprestacion']."|".
			 $rowApliFondo['cuil']."|".
			 str_pad($rowApliFondo['codpractica'],3,0,STR_PAD_LEFT)."|".
			 $impsubsidiado."|".
			 $impsolicitado."|".
		 	 str_pad($rowApliFondo['nroenvioafip'],4,0,STR_PAD_LEFT)."|".
		 	 str_pad($rowApliFondo['cuit'],11,0,STR_PAD_LEFT)."|".
		 	 str_pad($rowApliFondo['cbu'],22,0,STR_PAD_LEFT)."|".
		 	 str_pad($rowApliFondo['nroordenpago'],22," ",STR_PAD_RIGHT)."|".
		 	 str_pad("",22," ",STR_PAD_RIGHT)."|".
		 	 str_pad($rowApliFondo['fechatransferencia'],10," ",STR_PAD_RIGHT)."|".
		 	 str_pad("",10," ",STR_PAD_RIGHT)."|".
		 	 str_pad("",10," ",STR_PAD_RIGHT)."|".
			 $imppago."|".
		 	 str_pad(number_format($rowApliFondo['impretencion'],2,"",""),10,0,STR_PAD_LEFT)."|".
		 	 str_pad(number_format(0,2,"",""),10,0,STR_PAD_LEFT)."|".
		 	 str_pad(number_format(0,2,"",""),10,0,STR_PAD_LEFT)."|".
		 	 $impmontosubsidio."|".
		 	 $impos."|".
		 	 $impoc."|".
			 str_pad($rowApliFondo['recibo'],8,0,STR_PAD_RIGHT)."|".
			 str_pad(number_format($impTransladado,2,"",""),10,0,STR_PAD_LEFT)."|".
			 str_pad(number_format($impDevolucionSSS,2,"",""),10,0,STR_PAD_LEFT)."|".
			 str_pad(number_format($impNoAplicado,2,"",""),10,0,STR_PAD_LEFT)."|".
			 str_pad(number_format($rowApliFondo['imprecupero'],2,"",""),10,0,STR_PAD_LEFT)."|".
			 str_pad($rowApliFondo['observacion'],150," ",STR_PAD_RIGHT);
	fwrite($file, $linea . PHP_EOL);
}
fclose($file);

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();

	$sqlUpdatePresentacion = "INSERT INTO intefondos VALUES('DEFAULT',$idPresentacion,CURDATE(),NULL,NULL,NULL,NULL)";
	//echo $sqlUpdatePresentacion."<br>";
	$dbh->exec($sqlUpdatePresentacion);
	$dbh->commit();

	Header("Location: fondos.php");
} catch (PDOException $e) {
	echo $e->getMessage();
	$dbh->rollback();
	exit -1;
}





?>