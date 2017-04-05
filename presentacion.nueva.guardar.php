<?php
include_once 'include/conector.php';
require_once 'include/phpExcel/Classes/PHPExcel.php';

$archivo = $_FILES['archivo']['tmp_name'];
$fp = fopen ($archivo,"r");

$impCompTotal = 0;
$impPedido = 0;
$cantFacturas = 0;

$sqlInsertFacturas = "INSERT INTO facturas VALUES";
while ($data = fgetcsv ($fp, 1000, ";")) { 
	$impCompTotal += str_replace(',','.',$data['15']);
	$impPedido += str_replace(',','.',$data['16']);
    $linea = "(".str_replace('.','',$data['0']).",idpres,
    		'".$data['1']."',
    		".$data['2'].",
    		'".$data['3']."',
    		'".$data['4']."',
    		'".$data['5']."',
    		'".$data['6']."',
    		'".$data['7']."',
    		'".$data['8']."',
    		".$data['9'].",
    		'".$data['10']."',
    		'".$data['11']."',
    		'".$data['12']."',
    		".$data['13'].",
    		'".$data['14']."',
    		".str_replace(',','.',$data['15']).",
    		".str_replace(',','.',$data['16']).",
    		".$data['17'].",
    		".$data['18'].",
    		".$data['19'].",
    		'".$data['20']."',
    		NULL,NULL,NULL,NULL,NULL,NULL),";
    $sqlInsertFacturas .= $linea;
    $cantFacturas++;
} 
fclose ($fp);
$sqlInsertFacturas = substr($sqlInsertFacturas, 0, -1);
$sqlInsertPresentacion = "INSERT INTO presentacion VALUES(DEFAULT, ".$_POST['idCronograma'].", NULL, NULL,$cantFacturas,$impCompTotal,$impPedido,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)";

try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
	
	$dbh->exec($sqlInsertPresentacion);
	$lastId = $dbh->lastInsertId(); 
	$sqlInsertFacturas = str_replace("idpres", $lastId, $sqlInsertFacturas);
	$dbh->exec($sqlInsertFacturas);
	
	$dbh->commit();
	Header("Location: presentacion.detalle.php?id=$lastId");
} catch (PDOException $e) {
	echo $e->getMessage();
	$dbh->rollback();
}



?>