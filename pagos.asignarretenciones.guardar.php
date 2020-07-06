<?php include_once 'include/conector.php';
$usuario = $_SESSION['usuario'];
$idPresentacion = $_GET['id']; 

$arrayUpdateDetalle = array();
$i = 0;

//var_dump($_POST);

foreach ($_POST as $cuit => $datos) {
	$pos = strpos($cuit, "-ret");
	$posc = strpos($cuit, "-check");
	if ($pos === false && $posc === false) { 
		$ret = $_POST[$cuit."-ret"];
		$arrayDatos = explode("-",$datos);
		$nrocominterno = $arrayDatos[0];
		$nroordenpago = $arrayDatos[1];
		$arrayUpdateDetalle[$i] = "UPDATE intepagosdetalle SET impretencion = $ret 
									WHERE idpresentacion = $idPresentacion and 
										  nrocominterno = $nrocominterno and 
										  nroordenpago = $nroordenpago";
		$i++;
	}
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	
	$sqlUpdateFechaRet = "UPDATE intepresentacion SET fechaasignacionretencion = CURDATE() WHERE id = $idPresentacion";
	//echo $sqlUpdateFechaRet."<br>";
	$dbh->exec($sqlUpdateFechaRet);
	
	$sqlLimpliarRete = "UPDATE intepagosdetalle SET impretencion = 0.00 WHERE idpresentacion = $idPresentacion";
	//echo $sqlLimpliarRete."<br>";
	$dbh->exec($sqlLimpliarRete);
	
	foreach ($arrayUpdateDetalle as $udpateDetallePago) {
		//echo $udpateDetallePago."<br>";
		$dbh->exec($udpateDetallePago);
	}
	
	$dbh->commit();
	Header("Location: pagos.asignarretenciones.php?id=$idPresentacion");
} catch (PDOException $e) {
	$dbh->rollback();
	$error = $e->getMessage();
	$redire = "Location: presentacion.error.php?page='Asignacion de Retenciones'&error=$error";
	Header($redire);
	exit -1;
}

?>