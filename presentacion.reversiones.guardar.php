<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];

$reversionSerializado = $_POST['reversion'];
$tiporev = $_POST['tiporev'];
$reversionArray = unserialize(urldecode($reversionSerializado));
$index = 0;
$indexCab = 0;
foreach ($reversionArray as $reversion) {
    $nrocominterno = $reversion['datos']['nrocominterno'];
    $tipo = $reversion['tipo'];
    $codob = $reversion['datos']['codigoob'];
    $cuil = $reversion['datos']['cuil'];
    $codcert = $reversion['datos']['codcertificado'];
    $vtocert = $reversion['datos']['vtocertificado'];
    $periodo =  $reversion['datos']['periodo'];
    $cuit = $reversion['datos']['cuit'];
    $nombre = strtoupper($reversion['nombre']);
    $tipcomp = $reversion['datos']['tipocomprobante'];
    $tipemis = $reversion['datos']['tipoemision'];
    $fechacomp = $reversion['datos']['fechacomprobante'];
    $cae = $reversion['datos']['cae'];
    $ptoventa = (int) $reversion['datos']['puntoventa'];
    $nrocomp = (int) $reversion['datos']['nrocomprobante'];
    $impcomp = $reversion['datos']['impcomprobante'];
    $impdeb = $reversion['datos']['impdebito'];
    $impsoli = $reversion['datos']['impsolicitado'];
    $nointe =  $reversion['datos']['impnointe'];
    $codprac = $reversion['datos']['codpractica'];
    $cant = $reversion['datos']['cantidad'];
    $prov = $reversion['datos']['provincia'];
    $dep = $reversion['datos']['dependencia'];
    
    $insertReversion[$index] = "INSERT INTO intepresentaciondetalle VALUES
                                ($nrocominterno,$idPresentacion,'$tipo','$codob','$cuil','$codcert','$vtocert',
                                 '$periodo','$cuit','$nombre',$tipcomp,'$tipemis','$fechacomp','$cae',$ptoventa,'$nrocomp',
                                 $impcomp,$impdeb,$nointe,$impsoli,$codprac,$cant,$prov,'$dep',
                                 NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)";
    

    if ($tipo == 'DB') {
        $updateCabecera[$indexCab] = "UPDATE intepresentacion 
                            SET cantfactura = cantfactura + 1, impcomprobantesd = impcomprobantesd + $impcomp, 
                                impdebitod = impdebitod + $impdeb, impnointed = impnointed + $nointe, 
                                impsolicitadod = impsolicitadod + $impsoli 
                            WHERE id = $idPresentacion";
        $indexCab++;
    } 
    
    if (($tiporev == 2 || $tiporev == 3) &&  $tipo != 'DB') {
        $updateCabecera[$indexCab] = "UPDATE intepresentacion
                            SET cantfactura = cantfactura + 1, impcomprobantes = impcomprobantes + $impcomp,
                                impdebito = impdebito + $impdeb, impnointe = impnointe + $nointe,
                                impsolicitado = impsolicitado + $impsoli
                            WHERE id = $idPresentacion";
        $indexCab++;
    }
    
    if ($tiporev == 4 && $tipo == 'DS') {
        $insertReversion[$index] = "INSERT INTO intepresentacionreversion VALUES
                                ($nrocominterno,$idPresentacion,'$tipo','$codob','$cuil','$codcert','$vtocert',
                                 '$periodo','$cuit','$nombre',$tipcomp,'$tipemis','$fechacomp','$cae',$ptoventa,'$nrocomp',
                                 $impcomp,$impdeb,$nointe,$impsoli,$codprac,$cant,$prov,'$dep',
                                 NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL)";
    }
    $index++;
} 

try {
    $dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->beginTransaction();
    
    foreach ($insertReversion as $regRev) {
        $dbh->exec($regRev);
        //echo $regRev."<br><br>";
    }
    
    foreach ($updateCabecera as $regCab) {
        $dbh->exec($regCab);
        //echo $regCab."<br><br>";
    }
    
    $dbh->commit();
    Header("Location: presentacion.reversiones.php?id=$idPresentacion");
} catch (PDOException $e) {
    $redire = "Location: presentacion.error.php?id=$idPresentacion&page='Guardar Retenciones'&error=".$e->getMessage();
    Header($redire);
}
?>
