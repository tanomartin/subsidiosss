<?php 
include_once 'include/conector.php';
$sqlCompro = "SELECT * FROM tipocomprobante";
$resCompro = mysql_query($sqlCompro);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Comprobantes S.S.S. :.</title>
</head>

<body bgcolor="#CCCCCC">
	<div align="center">
	 <p><input type="reset" name="volver" value="Volver" onClick="location.href = 'menu.php'" /></p>
	 <h2>Comprobantes Subsidio S.S.S.</h2>
	 <div class="grilla">
		 <table>
		 	<thead>
		 		<tr>
		 			<th>Código</th>
		 			<th>Descripcion</th>
		 		</tr>
		 	</thead>
		 	<tbody>
		<?php while ($rowCompro = mysql_fetch_array($resCompro)) {  ?>
				<tr>
					<td><?php echo $rowCompro['id'] ?></td>
					<td><?php echo $rowCompro['descripcion'] ?></td>
				</tr>
		<?php } ?>
		  	</tbody>
		</table>
	</div>
	</div>
</body>
</html>