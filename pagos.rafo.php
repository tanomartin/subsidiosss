<?php
include_once 'include/conector.php';
$idPresentacion = $_GET['id'];
$canPagos = 0;
if (isset($_GET['cuit'])) {
	$nrocuit= $_GET['cuit'];
	$sqlPagos = "SELECT d.*,f.tipoarchivo, f.nrocomprobante, f.impcomprobante, t.descripcion as tipocomprobante, 
					DATE_FORMAT(c.fechatransferencia, '%d/%m/%Y') as fechatransferencia, c.nrotransferencia, f.impmontosubsidio
					FROM intepagosdetalle d, intepresentaciondetalle f, intepagoscabecera c, madera.tipocomprobante t
					WHERE
					d.idpresentacion = $idPresentacion and
					c.idpresentacion = d.idpresentacion and
					c.nroordenpago = d.nroordenpago and
					d.idpresentacion = f.idpresentacion and
					d.nrocominterno = f.nrocominterno and
					f.codpractica not in (97,98,99) and
					f.cuit = $nrocuit and f.tipoarchivo != 'DB' and
					f.tipocomprobante = t.id 
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
			var pagina = "pagos.rafo.php?id="+id+"&cuit="+cuit;
		 	location.href = pagina;
		} else {
			alert("Debe ingreasr un cuit para buscar");
		}
	}

	function validoNumero(inputObj) {
		var valorNumero = inputObj.value;
		var errorNumero = "Error en la carga. Asiento y Folio deben ser numericos enteros postivos";
		if(!isNumberPositivo(valorNumero)) {
			alert(errorNumero);
			inputObj.value = "";
			inputObj.focus();
			return false;
		}
		return false;
	}

	function habilitoRecupero(seleccion) {
		var name = seleccion.name;
		var arrayName = name.split('-');
		var nameRecu = "recu-"+arrayName[1]+"-"+arrayName[2];
		document.getElementById(nameRecu).disabled = true;
		document.getElementById(nameRecu).value = "";
		if (seleccion.checked) {
			var nameSubs = "sub-"+arrayName[1]+"-"+arrayName[2];
			document.getElementById(nameRecu).disabled = false;
			document.getElementById(nameRecu).value = document.getElementById(nameSubs).value
		}
	}

	function validoValor(inputRecu) {
		var name = inputRecu.name;
		var arrayName = name.split('-');
		var nameSubs = "sub-"+arrayName[1]+"-"+arrayName[2];
		var valueControl = document.getElementById(nameSubs).value;
		if (inputRecu.value > valueControl) {
			alert("El valor del Recupero de Fondo no puede ser mayor al Monto Subsidiado");
			inputRecu.value = valueControl;
		}
	}

</script>

<style type="text/css" media="print">
.nover {display:none}
</style>

</head>

<body bgcolor="#CCCCCC">
	<div align="center">
		<p><input class="nover" type="button" name="volver" value="Volver" onClick="location.href = 'pagos.php'" /></p>
	 	<div class="nover"><?php include_once("include/detalle.php")?></div>
	 	<h2 class="nover">Dato de Busqueda</h2>
	 	<p class="nover">C.U.I.T.: <input type="text" id="cuit" name="cuit" size="10"/></p>
	 	<p class="nover"><button onclick="buscar('<?php echo $idPresentacion?>')" >Buscar</button></p>
<?php	if (isset($_GET['cuit'])) { ?>
			<hr></hr>
	<?php 	if ($canPagos != 0) { ?>
				<h2>Datos de Pagos</h2><h3> C.U.I.T. "<?php echo $nrocuit ?>"</h3>	
				<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
				<form action="pagos.rafo.guardar.php" method="post">
			 		<input style="display: none" type="text" id="idpresentacion" name="idpresentacion" value="<?php echo $idPresentacion ?>" />
					<div class="grilla" style="margin-bottom: 20px;">
						<table>
						 	<thead>
						 		<th>Orden</br>Pago</th>
						 		<th>Comp</br>Interno</th>
						 		<th>Tipo</th>
						 		<th>Comp.</th>
						 		<th>Monto</br>Factura</th>
						 		<th>Monto</br>Subsidio</th>
						 		<th width="90px">Fec.</br>Transf.</th>
						 		<th>Nro.</br>Transf.</th>
						 		<th class="nover">Recibo</th>
						 		<th class="nover">Asiento</th>
						 		<th class="nover">Folio</th>
						 		<th class="nover">Obser.</th>
						 		<th class="nover">Recupero</br>Fondo</th>
						 	</thead>
						 	<tbody>
					  <?php while ($rowPagos = mysql_fetch_array($resPagos)) { 
								$inputName = $rowPagos['nroordenpago']."-".$rowPagos['nrocominterno']; ?>	
						 		<td><?php echo $rowPagos['nroordenpago'] ?></td>
						 		<td><?php echo $rowPagos['nrocominterno'] ?></td>
						 		<td><?php echo $rowPagos['tipoarchivo'] ?></td>
						 		<td><?php echo $rowPagos['tipocomprobante']."</br>".$rowPagos['nrocomprobante'] ?></td>
						 		<td><?php echo number_format($rowPagos['impcomprobante'],"2",",","."); ?></td>
						 		<td>
						 			<?php echo number_format($rowPagos['impmontosubsidio'],"2",",","."); ?>
						 			<input style="display: none" size="5px" type="text" value="<?php echo $rowPagos['impmontosubsidio'] ?>" id="sub-<?php echo $inputName ?>" name="sub-<?php echo $inputName ?>" />
						 		</td>
						 		<td><?php echo $rowPagos['fechatransferencia'] ?></td>
						 		<td><?php echo $rowPagos['nrotransferencia'] ?></td>	
						 		<td class="nover">
						 			<input size="5px" type="text" value="<?php echo $rowPagos['recibo'] ?>" id="recibo-<?php echo $inputName ?>" name="recibo-<?php echo $inputName ?>" />
						 		</td>
						 		<td class="nover">
						 			<input size="5px" type="text" value="<?php echo $rowPagos['asiento'] ?>" id="asiento-<?php echo $inputName ?>" name="asiento-<?php echo $inputName ?>" onblur="validoNumero(this)"/>
						 		</td>
						 		<td class="nover">
						 			<input size="5px" type="text" value="<?php echo $rowPagos['folio'] ?>" id="folio-<?php echo $inputName ?>" name="folio-<?php echo $inputName ?>" onblur="validoNumero(this)"/>
						 		</td>
						 		<td class="nover">
						 			<input size="30px" type="text" value="<?php echo $rowPagos['observacion'] ?>" id="obs-<?php echo $inputName ?>" name="obs-<?php echo $inputName ?>"/>
						 		</td>
						 		<td class="nover">
						  <?php if ($rowPagos['impmontosubsidio'] != 0) {
						 			$checked = '';
						 			$imprecu = '';
						 			if ($rowPagos['imprecupero'] != 0) {
						 				$checked = 'checked="checked"';
						 				$imprecu = $rowPagos['imprecupero'];
						 		  	} ?>
						 			<input <?php echo $checked ?> type="checkbox" id="recucheck-<?php echo $inputName ?>" name="recucheck-<?php echo $inputName ?>" onclick="habilitoRecupero(this)" />
						 			<input value="<?php echo $imprecu ?>" disabled="disabled" size="5px" type="text" value="<?php echo $rowPagos['folio'] ?>" id="recu-<?php echo $inputName ?>" name="recu-<?php echo $inputName ?>" onblur="validoValor(this)"/>
						 	<?php } else {
						 			echo "----------";
						  		  }?>
						 		</td>
						 	</tbody>
					   <?php } ?>
						 </table>
					</div>	
					<p class="nover"><input type="submit" value="Guardar" style="margin-top: 15px"></input></p>
				</form>
	<?php	} else { ?>
				<h3 style="color: red">No hay pagos para el C.U.I.T. "<?php echo $nrocuit ?>"</h3>
	<?php	}  	
		} ?>
	</div>
</body>
</html>