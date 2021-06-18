<?php
include_once 'include/conector.php';
set_time_limit(0);

function esValidoCUIT($cuit) {
    $aMult = '5432765432';
    $aMult = str_split($aMult);
    if (strlen($cuit) == 11) {
        $aCUIT = str_split($cuit);
        $iResult = 0;
        for($i = 0; $i <= 9; $i++) {
            $iResult += $aCUIT[$i] * $aMult[$i];
        }
        $iResult = ($iResult % 11);
        if ($iResult == 1) $iResult = 0;
        if ($iResult != 0) $iResult = 11 - $iResult;
        if ($iResult == $aCUIT[10]) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

//var_dump($_POST);echo "<br><br>";
$idCarpeta = $_POST['idCronograma'];

$whereNotIn = " f.id not in (";
$banderaOut = 0;
foreach ($_POST as $key => $datos) {
	$pos = strpos($key, "sacar");
	if ($pos !== false) {
		$banderaOut = 1;
		$whereNotIn .= $datos.",";
	}
}
$whereNotIn = substr($whereNotIn, 0, -1);
$whereNotIn .= ") AND ";

if ($banderaOut == 0) { $whereNotIn = ""; }

$sqlAPresentar = "SELECT c.*,DATE_FORMAT(c.fechacierre,'%d/%m/%Y') as fechacierre FROM intecronograma c WHERE fechacierre >= CURDATE() LIMIT 1";
$resAPresentar = mysql_query($sqlAPresentar);
$rowAPresentar = mysql_fetch_array($resAPresentar);

$sqlUltimaActiva = "SELECT * FROM intepresentacion WHERE fechacancelacion is null ORDER BY id DESC LIMIT 1";
$resUltimaActiva = mysql_query($sqlUltimaActiva);
$rowUltimaActiva = mysql_fetch_assoc($resUltimaActiva);
$idPresActiva = $rowUltimaActiva['id'];

$sqlIdFactura = "SELECT f.id
				FROM madera.facturasprestaciones p, 
					 madera.facturasintegracion i,  
					 madera.practicas pr, 
					 madera.facturas f
				LEFT JOIN madera.prestadores ON madera.prestadores.codigoprestador = f.idPrestador
				WHERE
					$whereNotIn
					autorizacionpago = 0 AND
					f.id = p.idfactura AND
					p.id = i.idfacturaprestacion AND
					p.idPractica = pr.idpractica AND
					f.id NOT IN (SELECT DISTINCT nrocominterno
								FROM intepresentaciondetalle
								WHERE idpresentacion = $idPresActiva)";
//echo $sqlIdFactura."<br><br>";
$resIdFactura = mysql_query($sqlIdFactura);
$canIdFactura = mysql_num_rows($resIdFactura);
if ($canIdFactura != 0) {
	$arrayIdFactura = array();
	$whereIn = "(";
	while ($rowControl = mysql_fetch_assoc($resIdFactura)) {
		$arrayControl[$rowControl['id']] = $rowControl;
		$whereIn .= $rowControl['id'].",";
	}
	$whereIn = substr($whereIn, 0, -1);
	$whereIn .= ")";

	try {
	    $impCompTotal = 0;
	    $impDebitoTotal = 0;
	    $impNoInteTotal = 0;
	    $impPedido = 0;
	    $impCompTotalD = 0;
	    $impDebitoTotalD = 0;
	    $impNoInteTotalD = 0;
	    $impPedidoD = 0;
	    $cantFacturas = 0;
	    $sqlInsertFacturas = array();
	    
	    $sqlCodVto = "SELECT cuil, codigocertificado, DATE_FORMAT(vencimientocertificado,'%d/%m/%Y') as vencimientocertificado
        				FROM madera.discapacitados
        				WHERE codigocertificado is not null and codigocertificado != ''";
	    $resCodVto = mysql_query($sqlCodVto);
	    $arrayCodVto = array();
	    while ($rowCodVto = mysql_fetch_array($resCodVto)) {
	        $arrayCodVto[$rowCodVto['cuil']] = array('codigo' => $rowCodVto['codigocertificado'], 'vto' => $rowCodVto['vencimientocertificado']);
	    }
	    
    	$sqlFacturas = "SELECT f.id, 'DS' as tipo, 111001 as codigoOS,
    					       (CASE
    					           WHEN madera.titulares.cuil is not NULL THEN madera.titulares.cuil
    					           WHEN madera.titularesdebaja.cuil is not NULL THEN madera.titularesdebaja.cuil
    					           WHEN madera.familiares.cuil is not NULL THEN madera.familiares.cuil
    					           WHEN madera.familiaresdebaja.cuil is not NULL THEN madera.familiaresdebaja.cuil
    					       END) as cuil,
    					       DATE_FORMAT(fp.fechapractica,'%Y%m') as periodo,
    					       p.cuit, 
                               p.nombre, 
                               f.idTipocomprobante, 
                               'E' as tipoemision,
    					       DATE_FORMAT(f.fechacomprobante,'%d/%m/%Y') as fechacomprobante,
    					       f.nroautorizacion, 
                               f.puntodeventa, 
                               f.nrocomprobante, 
                               f.importecomprobante,
    					       f.totaldebito, 
    						   0 as nointe, 
    						   fi.totalsolicitado,
    					       FORMAT(ps.codigopractica,0) as codigopractica,
    					       IF (ps.codigopractica = 86 or ps.codigopractica = 87, 1, ROUND(fp.cantidad,0)) as cantidad, 
    						   0 as prov,
    					       IF (fi.dependencia = 1, 'S', 'N') as dep
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
        					f.id in $whereIn and
        					f.id = fb.idFactura and 
        					f.autorizacionpago = 0 and 
        					fb.id = fp.idFacturabeneficiario and 
        					fp.id = fi.idFacturaprestacion and
        					f.idPrestador = p.codigoprestador and
        					fp.idpractica = ps.idpractica";
    	
    	//echo $sqlFacturas;
    	$resFacturas = mysql_query($sqlFacturas);
    	$canFacturas = mysql_num_rows($resFacturas);
    	if ($canFacturas == 0) {
    	    $error = "Error en la lectura de las facturas por favor comunicarse con el Dpto. de Sistemas";
    	    throw new Exception($error);
    	}
    	
    	$arrayRegistrosInsert = array();
    	$index = 0;
    	while ($rowFacturas = mysql_fetch_assoc($resFacturas)) {
    	    $arrayRegistrosInsert[$index] = $rowFacturas;
    	    $index++;
    	}
    	
    	$sqlEscuelas = "SELECT f.id, 'DS', 111001,
    							(CASE
    							    WHEN madera.titulares.cuil is not NULL THEN madera.titulares.cuil
    							    WHEN madera.titularesdebaja.cuil is not NULL THEN madera.titularesdebaja.cuil
    							    WHEN madera.familiares.cuil is not NULL THEN madera.familiares.cuil
    							    WHEN madera.familiaresdebaja.cuil is not NULL THEN madera.familiaresdebaja.cuil
    							END) as cuil,
    							DATE_FORMAT(fp.fechapractica,'%Y%m') as periodo,
    							e.cue as cuit,
    							concat(e.nombre,' - ',p.descripcion) as nombre,
    							0 as idTipocomprobante,
                                'N' as tipoemision,
    							DATE_FORMAT(fp.fechapractica,'01/%m/%Y') as fechacomprobante,
    							0 as nroautorizacion,
                                0 as puntodeventa,
                                0 as nrocomprobante,
                                0.00 as importecomprobante,
                                0.00 as totaldebito, 
                                0.00 as nointe,
                                0.00 as totalsolicitado,
    							FORMAT(p.codigopractica,0) as codigopractica,
    							1 as cantidad,
                                0 as prov,
                                'N' as dep
    					FROM madera.facturas f, madera.facturasprestaciones fp, madera.facturasintegracion fi, 
                             madera.practicas p, madera.escuelas e, madera.facturasbeneficiarios fb
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
    					f.id in $whereIn and
    					f.id = fb.idFactura and fb.id = fp.idFacturabeneficiario and
    					f.autorizacionpago = 0 and
    					f.id = fp.idFactura and
    					fp.id = fi.idFacturaprestacion and
    					fi.tipoescuela is not NULL and
    					fi.tipoescuela = p.idpractica and
    					fi.idEscuela = e.id";
    	
    	//echo $sqlEscuelas."<br><br>";
    	$resEscuelas = mysql_query($sqlEscuelas);
    	while ($rowEscuelas = mysql_fetch_assoc($resEscuelas)) {
    	    $arrayRegistrosInsert[$index] = $rowEscuelas;
    	    $index++;
    	}
    	   
    	foreach ($arrayRegistrosInsert as $data) {  	  
    	    $impCompTotal += str_replace(',','.',$data['importecomprobante']);
    	    $impDebitoTotal += str_replace(',','.',$data['totaldebito']);
    	    $impNoInteTotal += str_replace(',','.',$data['nointe']);
    	    $impPedido += str_replace(',','.',$data['totalsolicitado']);
    	    
    	    $cuil = $data['cuil'];
    	    if (!esValidoCUIT($cuil)) {
    	        $error = "Error en el C.U.I.L. $cuil nro comprobante interno ".$data['id'];
    	        throw new Exception($error);
    	    }
    	    $cuit = $data['cuit'];
    	    $codpractica = $data['codigopractica'];
    	    if ($codpractica != 97 && $codpractica != 98 && $codpractica != 99) {
    	        if (!esValidoCUIT($cuit)) {
    	            $error = "Error en el C.U.I.T. $cuit nro comprobante interno ".$data['id'];
    	            throw new Exception($error);
    	        }
    	    } else {
    	        $cuit = (int) $cuit;
    	    }
    	    
    	    $impFactura = str_replace(',','.',$data['importecomprobante']);
    	    $impDebito = str_replace(',','.',$data['totaldebito']);
    	    $impNoInte = str_replace(',','.',$data['nointe']);
    	    $impSolicit = str_replace(',','.',$data['totalsolicitado']);
    	    
    	    if ($impFactura < $impSolicit) {
    	        $error = "El monto solicitado no puede ser superior al monto de la facutra nor comprobante interno ".$data['id'];
    	        throw new Exception($error);
    	    }
    	    $calculoSolicitado = $impFactura - $impDebito - $impNoInte;
    	    $calculoSolicitado = round($calculoSolicitado,2);
    	    if ($calculoSolicitado != $impSolicit) {
    	        $error = "El monto solicitado no concuerda con el comprobante, el debito y lo no integral ($calculoSolicitado)".$data['id'];
    	        throw new Exception($error);
    	    }
    	    
    	    if (!array_key_exists($cuil,$arrayCodVto)) {
    	        $error = "El cuil '$cuil' no tiene cargada toda la inforacion completa del certificado de discapacidad";
    	        throw new Exception($error);
    	    }
    	
        	$linea = "INSERT INTO intepresentaciondetalle VALUES (".str_replace('.','',$data['id']).",idpres,
        		'".$data['id']."',
        	    'DS',
        		'".$cuil."',
        		'".$arrayCodVto[$cuil]['codigo']."',
        		'".$arrayCodVto[$cuil]['vto']."',
        		'".$data['periodo']."',
        		'".$cuit."',
        		'".$data['nombre']."',
        		".$data['idTipocomprobante'].",
        		'".strtoupper($data['tipoemision'])."',
        		'".$data['fechacomprobante']."',
        		'".str_pad($data['nroautorizacion'],14,'0',STR_PAD_LEFT)."',
        		".$data['puntodeventa'].",
        		'".$data['nrocomprobante']."',
        		".$impFactura.",
        		".$impDebito.",
        		".$impNoInte.",
        		".$impSolicit.",
        		".$codpractica.",
        		".$data['cantidad'].",
        		".$data['prov'].",
        		'".strtoupper($data['dep'])."',
        		NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)";
        	$sqlInsertFacturas[$cantFacturas] = $linea;
        	$cantFacturas++;
    	}
    	$sqlInsertPresentacion = "INSERT INTO intepresentacion VALUES(DEFAULT, $idCarpeta, NULL, NULL, NULL,
						$cantFacturas,$impCompTotal,$impDebitoTotal,$impNoInteTotal,$impPedido,
						$impCompTotalD,$impDebitoTotalD,$impNoInteTotalD,$impPedidoD,NULL,NULL,NULL,NULL,NULL)";
    	
	} catch (Exception $e) {
	    $error = $e->getMessage();
	    $redire = "Location: presentacion.error.php?page='Nueva Presentacion'&error=$error";
	    Header($redire);
	    exit -1;
	}
	
	try {
	    $dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $dbh->beginTransaction();
	    
	    $dbh->exec($sqlInsertPresentacion);
	    //echo $sqlInsertPresentacion."<br>";
	    $lastId = $dbh->lastInsertId();
	    
	    foreach ($sqlInsertFacturas as $sqlinsert) {
	        $sqlinsert = str_replace("idpres", $lastId, $sqlinsert);
	        //echo $sqlinsert."<br>";
	        $dbh->exec($sqlinsert);
	    }
	    
	    $dbh->commit();
	    
	    //GENERO LAS CARPETAS A UTILIZAR A FUTURO
	    $anio = substr($_POST['carpeta'],0,4);
	    $carpetaanio = "archivos/$anio";
	    $carpetaGeneracion = "archivos/$anio/".$_POST['carpeta']."/generacion";
	    $carpetaResultados= "archivos/$anio/".$_POST['carpeta']."/resultados";
	    $carpetaFondos= "archivos/$anio/".$_POST['carpeta']."/fondos";
	    
	    try {
	        if (!file_exists($carpetaanio)) {
	            mkdir($carpetaanio, 0777, true);
	        }
	        if (!file_exists($carpetaGeneracion)) {
	            mkdir($carpetaGeneracion, 0777, true);
	        }
	        if (!file_exists($carpetaResultados)) {
	            mkdir($carpetaResultados, 0777, true);
	        }
	        if (!file_exists($carpetaFondos)) {
	            mkdir($carpetaFondos, 0777, true);
	        }
	    } catch (Exception $e) {
	        $error = $e->getMessage();
	        $redire = "Location: presentacion.error.php?page='Nueva Presentacion'&error=$error";
	        Header($redire);
	        exit -1;
	    }
	    
	    
	    Header("Location: presentacion.detalle.php?id=$lastId");
	} catch (PDOException $e) {
	    $dbh->rollback();
	    
	    $sqlConsultaUlitmoID = "SELECT id FROM intepresentacion i ORDER BY id desc limit 1";
	    $resConsultaUlitmoID = mysql_query($sqlConsultaUlitmoID);
	    $rowConsultaUlitmoID = mysql_fetch_array($resConsultaUlitmoID);
	    $idRestart = $rowConsultaUlitmoID['id'] + 1;
	    
	    $dbh->beginTransaction();
	    $sqlUpdateAutoAuto = "ALTER TABLE intepresentacion AUTO_INCREMENT = $idRestart";
	    $dbh->exec($sqlUpdateAutoAuto);
	    $dbh->commit();
	    
	    $error = $e->getMessage()." (INSERT: ".$sqlinsert.")";
	    //echo $error;
	    $redire = "Location: presentacion.error.php?page='Nueva Presentacion'&error=$error";
	    Header($redire);
	    exit -1;
	}
	
	
} else {
	echo "NO HAY FACTURAS PARA PRESENTAR";
}
?>