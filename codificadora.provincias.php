<?php include_once 'include/conector.php';
$sqlProv = "SELECT * FROM inteprovincia";
$resProv = mysql_query($sqlProv); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Provincias S.S.S. - Zonas desfavorables :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 <p><input type="reset" name="volver" value="Volver" onClick="location.href = 'codificadora.php'" /></p>
	 <h2>Provincias S.S.S. - Zonas desfavorables</h2>
	 <div class="grilla">
		 <table>
		 	<thead>
		 		<tr>
		 			<th>Código</th>
		 			<th>Descripcion</th>
		 		</tr>
		 	</thead>
		 	<tbody>
		<?php while ($rowProv = mysql_fetch_assoc($resProv)) {  ?>
				<tr>
					<td><?php echo $rowProv['codigo'] ?></td>
					<td><?php echo $rowProv['nombre'] ?></td>
				</tr>
		<?php } ?>
		  	</tbody>
		</table>
	</div>
	</div>
</body>
</html>