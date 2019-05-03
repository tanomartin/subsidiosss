<?php
include_once 'include/conector.php';
$carpeta = $_POST['carpeta'];
$idFondos = $_POST['idfondos'];
$idPresentacion = $_POST['idpresentacion'];

if (isset($_POST['sinerrores'])) {
	$sqlUpdateCabecera = "UPDATE intefondos SET canterrores = 0 WHERE id = $idFondos";
} else {
	if ($_FILES['archivoerror']['tmp_name'] != "") {
		$archivoerror = $_FILES['archivoerror']['tmp_name'];
		$anio = substr($carpeta,0,4);
		$carpetaFondos = "archivos/$anio/$carpeta/fondos";
		try {
			$archivodestinoerr = $carpetaFondos."/".$_FILES['archivoerror']['name'];
			copy($archivoerror, $archivodestinoerr);
		} catch (Exception $e) {
			$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Fondos'&error=".$e->getMessage();
			Header($redire);
			exit -1;
		}	
		$arrayErrores = array();
		$indexUpdate = 0;
		if ($archivoerror != null) {
			$fpnok = fopen($archivoerror, "r");
			while(!feof($fpnok)) {
				$linea = fgets($fpnok);
				$arrayDatos = array();
				if ($linea != '') {
					//echo $linea."<br>";
					$arrayDatos = explode("|",$linea);
					$clave = $arrayDatos[0];
					$cuitcbu = $arrayDatos[10];
					$cbu = $arrayDatos[11];
					$orden1 = $arrayDatos[12];
					$orden2 = $arrayDatos[13];
					$fectr1 = date("Y-m-d",strtotime(str_replace('/', '-',$arrayDatos[14])));
					if (strpos($arrayDatos[15], "/") !== false)  { 
						$fectr2 = date("Y-m-d",strtotime(str_replace('/', '-',$arrayDatos[15]))); 
					} else {
						$fectr2 = 'NULL';
					}
					$cheque = $arrayDatos[16];
					$imptra = substr($arrayDatos[17],0,-2).".".substr($arrayDatos[17],-2);
					$retgan = substr($arrayDatos[18],0,-2).".".substr($arrayDatos[18],-2);
					$retib = substr($arrayDatos[19],0,-2).".".substr($arrayDatos[19],-2);
					$retotr = substr($arrayDatos[20],0,-2).".".substr($arrayDatos[20],-2);
					$fondossss = substr($arrayDatos[21],0,-2).".".substr($arrayDatos[21],-2);
					echo $fondossss."<br>";
					$fondospropios = substr($arrayDatos[22],0,-2).".".substr($arrayDatos[22],-2);
					$fondosoc = substr($arrayDatos[23],0,-2).".".substr($arrayDatos[23],-2);
					$recibo = $arrayDatos[24];
					$imptrasladado = substr($arrayDatos[25],0,-2).".".substr($arrayDatos[25],-2);
					$impdevuelto = substr($arrayDatos[26],0,-2).".".substr($arrayDatos[26],-2);
					$noaplicado = substr($arrayDatos[27],0,-2).".".substr($arrayDatos[27],-2);
					$recupero = substr($arrayDatos[28],0,-2).".".substr($arrayDatos[28],-2);
					$coderror = $arrayDatos[29];
					
					$sqlInsertError = "INSERT INTO intefondodevolucion VALUES ($idFondos,$clave,'$cuitcbu','$cbu','$orden1',
									  '$orden2','$fectr1','$fectr2','$cheque','$imptra','$retgan','$retib','$retotr',
									  '$fondossss','$fondospropios','$fondosoc','$recibo','$imptrasladado',
									  '$impdevuelto','$noaplicado','$recupero',$coderror)";
					$arrayErrores[$indexUpdate] = $sqlInsertError;
					$indexUpdate++;
				}
			}
			$sqlUpdateCabecera = "UPDATE intefondos SET canterrores = $indexUpdate WHERE id = $idFondos";
		}
	} else {
		$error = "No se cargo archivo de errores";
		$redire = "Location: presentacion.error.php?id=$idPresentacion&page='Dev. Fondos'&error=".$error;
		Header($redire);
		exit -1;
	}
}
try {
	$dbh = new PDO("mysql:host=$hostLocal;dbname=$dbname",$usuarioLocal,$claveLocal);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->beginTransaction();
		
	$dbh->exec($sqlUpdateCabecera);
	//echo $sqlUpdateCabecera."<br>";
	if (isset($arrayErrores)) {
		foreach ($arrayErrores as $sqlUpdate) {
			//echo $sqlUpdate."<br>";
			$dbh->exec($sqlUpdate);
		}
	}
	$dbh->commit();
	Header("Location: fondos.php");
} catch (PDOException $e) {
	$dbh->rollback();
	$error = $e->getMessage();
	$redire = "Location: presentacion.error.php?page='Fondos Devolucion'&error=$error";
	Header($redire);
	exit -1;
}

?>
