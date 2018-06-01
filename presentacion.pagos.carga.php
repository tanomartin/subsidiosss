<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];
$canPagos = 0;
if (isset($_GET['cuit'])) {
	$nrocuit= $_GET['cuit'];
	$sqlPagos = "SELECT d.*,f.tipoarchivo, f.nrocomprobante, f.impcomprobante , 
					DATE_FORMAT(c.fechatransferencia, '%d-%m-%Y') as fechatransferencia, c.nrotransferencia
					FROM intepagosdetalle d, intepresentaciondetalle f, intepagoscabecera c
					WHERE
					d.idpresentacion = $idPresentacion and
					c.idpresentacion = d.idpresentacion and
					c.nroordenpago = d.nroordenpago and
					d.idpresentacion = f.idpresentacion and
					d.nrocominterno = f.nrocominterno and
					f.codpractica not in (97,98,99) and
					f.cuit = $nrocuit and f.tipoarchivo != 'DB'
					order by f.nrocomprobante";
	$resPagos = mysql_query($sqlPagos);
	$canPagos = mysql_num_rows($resPagos);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="css/tablas.css"/>
<title>.: Carga Info Pagos S.S.S. :.</title>

<script src="include/funcionControl.js" type="text/javascript"></script><base />
<script type="text/javascript">

	function buscar(id) {
		var cuit =  document.getElementById('cuit').value;
		if (cuit!="") {
			var pagina = "presentacion.pagos.carga.php?id="+id+"&cuit="+cuit;
		 	location.href = pagina;
		} else {
			alert("Debe ingreasr un cuit para buscar");
		}
	}

	function validoNumero(id) {
		var valorNumero = document.getElementById(id).value;
		var errorNumero = "Error en la carga. Todos los datos deben ser numericos enteros postivos";
		if(!isNumberPositivo(valorNumero)) {
			alert(errorNumero);
			document.getElementById(id).value = "";
			document.getElementById(id).focus();
			return false;
		}
		return false;
	}
		

</script>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'presentacion.php'" /></p>
	 	<div class="nover"><?php include_once("include/detalle.php")?></div>
	 	
	 	<h2 class="nover">Dato de Busqueda</h2>
	 	<p class="nover">C.U.I.T.: <input type="text" id="cuit" name="cuit" /></p>
	 	<p class="nover"><button onclick="buscar('<?php echo $idPresentacion?>')" >Buscar</button></p>
<?php	if (isset($_GET['cuit'])) {
			if ($canPagos != 0) { ?>
				<h2>Datos de Pagos</h2><h3> C.U.I.T. "<?php echo $nrocuit ?>"</h3>	
				<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
	<?php		while ($rowPagos = mysql_fetch_array($resPagos)) { ?>	
		 		<form action="presentacion.pagos.carga.guardar.php" method="post">
		 			<input style="display: none" type="text" id="idpresentacion" name="idpresentacion" value="<?php echo $idPresentacion ?>" />
				 	<div class="grilla" style="margin-bottom: 20px;">
					 	<table>
					 		<thead>
					 			<th>Nro. Orden Pago</th>
					 			<th>Nro. Comp. Interno</th>
					 			<th>Tipo</th>
					 			<th>Nro. Factura</th>
					 			<th>Monto Factura</th>
					 			<th width="90px">Fec. Transf.</th>
					 			<th>Nro. Transf.</th>
					 			<th class="nover">Recibo</th>
					 			<th class="nover">Asiento</th>
					 			<th class="nover">Folio</th>
					 		</thead>
					 		<tbody>
					 			<td><?php echo $rowPagos['nroordenpago'] ?></td>
					 			<td><?php echo $rowPagos['nrocominterno'] ?></td>
					 			<td><?php echo $rowPagos['tipoarchivo'] ?></td>
					 			<td><?php echo $rowPagos['nrocomprobante'] ?></td>
					 			<td><?php echo number_format($rowPagos['impcomprobante'],"2",",","."); ?></td>
					 			<td><?php echo $rowPagos['fechatransferencia'] ?></td>
					 			<td><?php echo $rowPagos['nrotransferencia'] ?></td>	
					 			<td class="nover"><input size="5px" type="text" value="<?php echo $rowPagos['recibo'] ?>" id="recibo-<?php echo $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno']  ?>" name="recibo-<?php echo $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno']  ?>" onblur='validoNumero("recibo-<?php echo $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno']  ?>")'/></td>
					 			<td class="nover"><input size="5px" type="text" value="<?php echo $rowPagos['asiento'] ?>" id="asiento-<?php echo $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno']  ?>" name="asiento-<?php echo $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno'] ?>" onblur='validoNumero("asiento-<?php echo $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno'] ?>")'/></td>
					 			<td class="nover"><input size="5px" type="text" value="<?php echo $rowPagos['folio'] ?>" id="folio-<?php echo $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno']  ?>" name="folio-<?php echo $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno']  ?>" onblur='validoNumero("folio-<?php echo $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno'] ?>")'/></td>
					 		</tbody>
					 	</table>
					 </div>	
			<?php } ?> 
				<p class="nover"><input type="submit" value="Guardar" style="margin-top: 15px"></input></p>
			</form>
	<?php	} else { ?>
				<h3 style="color: red">No hay pagos para el C.U.I.T. "<?php echo $nrocuit ?>"</h3>
	<?php	}  	
		} ?>
	</div>
</body>
</html>