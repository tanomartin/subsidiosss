<?php
include_once 'include/conector.php';
$usuario = $_SESSION['usuario'];
$idPresentacion = $_GET['id'];

echo $idPresentacion."<br>";

$arrayUpdate = array();
foreach ($_POST as $cuit) {
	$sqlUpdate = "UPDATE inteinterbanking SET fechaenvio = CURDATE() WHERE idpresentacion = $idPresentacion and cuit = '$cuit'";
	$arrayUpdate[$cuit] = $sqlUpdate;
}

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();

	foreach ($arrayUpdate as $updateCierre) {
		$dbh->exec($updateCierre);
		//echo $updateCierre."<br>";
	}

	$dbh->commit();
	Header("Location: presentacion.pagos.interbanking.php?id=$idPresentacion");
} catch (PDOException $e) {
	$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Enviar Pago'&error=".$e->getMessage();
	Header($redire);
}


?>