<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['idpresentacion'];
$nrocomint = $_GET['nrocomint'];
$norord = $_GET['norord'];

$sqlPago = "SELECT * FROM pagos p, facturas f
				WHERE 
					p.idpresentacion = $idPresentacion and 
					p.nrocominterno = $nrocomint and 
					p.nroordenpago = $norord and
					p.nrocominterno = f.nrocominterno";
$resPago = mysql_query($sqlPago);
$rowPago = mysql_fetch_array($resPago);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Carga Info Pagos S.S.S. :.</title>

<script src="include/funcionControl.js" type="text/javascript"></script><base />
<script type="text/javascript">
	function validar(formulario) {
		var recibo = formulario.recibo.value;
		var asiento = formulario.asiento.value;
		var folio = formulario.folio.value;
		if(!isNumberPositivo(recibo)) {
			alert("El recibo debe ser un nuero positivo");
			return false;
		}
		if(!isNumberPositivo(asiento)) {
			alert("El asiento debe ser un nuero positivo");
			return false;
		}
		if(!isNumberPositivo(folio)) {
			alert("El folio debe ser un nuero positivo");
			return false;
		}
		return true;
	}
</script>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.pagos.php?id=<?php echo $idPresentacion?>'" /></p>
	 	<?php include_once("include/detalle.php")?>
	 	
	 	<h2>Datos del Pago</h2>
	 	<form onSubmit="return validar(this)" action="presentacion.pagos.carga.guardar.php" method="post">
	 		<input style="display: none" type="text" id="idpresentacion" name="idpresentacion" value="<?php echo $idPresentacion ?>" />
	 		<input style="display: none" type="text" id="nrocomint" name="nrocomint" value="<?php echo $nrocomint ?>" />
	 		<input style="display: none" type="text" id="norord" name="norord" value="<?php echo $norord ?>" />
		 	<div class="grilla" style="margin-bottom: 20px">
			 	<table>
			 		<thead>
			 			<th>CUIT</th>
			 			<th>Nro. Orden Pago</th>
			 			<th>Nro. Comprobante</th>
			 			<th>Imp. Comprobante</th>
			 			<th>Imp. Pagado</th>
			 		</thead>
			 		<tbody>
			 			<td><?php echo $rowPago['cuit'] ?></td>
			 			<td><?php echo $rowPago['nroordenpago'] ?></td>
			 			<td><?php echo $rowPago['nrocominterno'] ?></td>
			 			<td><?php echo number_format($rowPago['impcomprobante'],"2",",","."); ?></td>
			 			<td><?php echo number_format($rowPago['importepagado'],"2",",","."); ?></td>
			 		</tbody>
			 	</table>
			 </div>	
			 <div class=grilla>
				 <table>
				 	<tr>
				 		<td>Recibo</td>
				 		<td><input type="text" value="<?php echo $rowPago['recibo'] ?>" id="recibo" name="recibo"></input></td>
				 	</tr>
				 	<tr>
				 		<td>Asiento</td>
				 		<td><input type="text" value="<?php echo $rowPago['asiento'] ?>" id="asiento" name="asiento"></input></td>
				 	</tr>
				 	<tr>
				 		<td>Folio</td>
				 		<td><input type="text" value="<?php echo $rowPago['folio'] ?>" id="folio" name="folio"></input></td>
				 	</tr>
				 </table>
			 </div>	
			 <input type="submit" value="Guardar" style="margin-top: 15px"></input>
		</form>
	</div>
</body>
</html>