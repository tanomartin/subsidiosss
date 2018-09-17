<?php include_once 'include/conector.php';
set_time_limit(0);
$listadoSerializado=$_POST['empresas'];
$listadoPrestadores = unserialize(urldecode($listadoSerializado));
$fecharegistro = date("Y-m-d H:i:s");
$usuarioregistro = $_SESSION['usuario'];

$from ="facturacion@ospim.com.ar";
$subject = addslashes($_POST['asunto']);
$mensaje = addslashes($_POST['mensaje']);
$mensaje = str_replace("\r\n", '<br>', $mensaje);
$mensaje .= "<br><br>";
$bodymail ="<body>".$mensaje."</body>";
$modulo = "Integracion";	

$limiteEnvio = 20;
$agrupa = 0;
$index = 0;
$agrupaTemporal = "";
$arrayMailsAgrupados = array();

$sqlMailingReplace = "REPLACE INTO intemailing VALUES";
foreach ($listadoPrestadores as $key => $address) {
	$agrupa++;
	$sqlMailingReplace .= "($key,'$subject','$fecharegistro','$usuarioregistro'),";
	if ($agrupa <= $limiteEnvio) {
		$agrupaTemporal .= $address.";";
	} else {
		$agrupaTemporal = substr($agrupaTemporal, 0, -1);
		$index++;
		$arrayMailsAgrupados[$index] = $agrupaTemporal;
		$agrupaTemporal = "";
		$agrupa = 0;
	}
}
if ($agrupa != 0) {
	$agrupaTemporal = substr($agrupaTemporal, 0, -1);
	$index++;
	$arrayMailsAgrupados[$index] = $agrupaTemporal;
}

$sqlMailingReplace = substr($sqlMailingReplace, 0, -1);
$sqlMailingReplace .=";";

$sqlEmailCabecera = "INSERT INTO madera.bandejasalida VALUES ";
foreach ($arrayMailsAgrupados as $agrupados) {
	$sqlEmailCabecera .= "(DEFAULT, '$from', '$subject', '$bodymail', '$agrupados', '$modulo', '$fecharegistro', '$usuarioregistro'),";
}
$sqlEmailCabecera = substr($sqlEmailCabecera, 0, -1);
$sqlEmailCabecera .= ";";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	
	//echo $sqlEmailCabecera."<br>";
	$dbh->exec($sqlEmailCabecera);
	//echo $sqlMailingReplace."<br>";
	$dbh->exec($sqlMailingReplace);
	
	$dbh->commit();
	Header("Location: mailing.php?envio=1");
	
} catch (PDOException $e) {
	$dbh->rollback();
	$error = $e->getMessage()." (SQL: ".$sqlinsert.")";
	$redire = "Location: presentacion.error.php?page='Generar Archivo Pago'&error=$error";
	Header($redire);
	exit -1;
}