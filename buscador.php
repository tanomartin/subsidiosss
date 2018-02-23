<?php include_once 'include/conector.php';

$buscando = 0;
$noExiste = 0;

if (isset($_POST['dato']) && isset($_POST['filtro'])) {
	$dato = $_POST['dato'];
	$filtro = $_POST['filtro'];
	$pres =  $_POST['presentacion'];
	$buscando = 1;
	if ($filtro == 0) {
		$cartel = "Resultados de Busqueda por <b>Nro. Comprobante Interno $dato</b>";
	}
	if ($filtro == 1) {
		$cartel = "Resultados de Busqueda por <b>C.U.I.T. / C.U.E. Prestador $dato</b>";
	}
	if ($filtro == 2) {
		$cartel = "Resultados de Busqueda por <b>C.U.I.L. Afiliado $dato</b>";
	}
	if ($filtro == 3) {
		$cartel = "Resultados de Busqueda por <b>Nro. Factura $dato</b>";
	}
	$cartel .= "<br><br> Presentaciones <b>$pres</b>"; 
	$resultado = array();
	if (isset($dato)) {
		if ($filtro == 0) { $sqlFactura = "SELECT c.periodo as perpres, f.*, p.fechacancelacion, p.fechadeposito FROM intepresentaciondetalle f, intepresentacion p, intecronograma c WHERE f.nrocominterno = $dato and f.idpresentacion = p.id and p.idcronograma = c.id"; }
		if ($filtro == 1) { $sqlFactura = "SELECT c.periodo as perpres, f.*, p.fechacancelacion, p.fechadeposito FROM intepresentaciondetalle f, intepresentacion p, intecronograma c WHERE cuit = $dato and f.idpresentacion = p.id and p.idcronograma = c.id"; }
		if ($filtro == 2) { $sqlFactura = "SELECT c.periodo as perpres, f.*, p.fechacancelacion, p.fechadeposito FROM intepresentaciondetalle f, intepresentacion p, intecronograma c WHERE cuil = $dato and f.idpresentacion = p.id and p.idcronograma = c.id"; }
		if ($filtro == 3) { $sqlFactura = "SELECT c.periodo as perpres, f.*, p.fechacancelacion, p.fechadeposito FROM intepresentaciondetalle f, intepresentacion p, intecronograma c WHERE nrocomprobante = $dato and f.idpresentacion = p.id and p.idcronograma = c.id"; }
		
		if ($pres != 'TODAS') {
			if ($pres == 'CANCELADAS') {
				$sqlFactura .= " and p.fechacancelacion is not null";
			} else {
				$sqlFactura .= " and p.fechacancelacion is null";
				if ($pres == 'EN PROCESO') {
					$sqlFactura .= " and p.fechadeposito is null";
				} else {
					$sqlFactura .= " and p.fechadeposito is not null";
				}
			}
		}
		
		$resFactura = mysql_query($sqlFactura,$db); 
		$canFactura = mysql_num_rows($resFactura); 
		if ($canFactura == 0) {
			$noExiste = 1;
		}
	}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.: Subsidios Buscador :.</title>
<style type="text/css" media="print">
.nover {display:none}
</style>
<script src="include/jquery-ui-1.9.2.custom/js/jquery-1.8.3.js" type="text/javascript"></script>
<link rel="stylesheet" href="include/jquery.tablesorter/themes/theme.blue.css"/>
<script src="include/jquery.tablesorter/jquery.tablesorter.js"></script>
<script src="include/jquery.tablesorter/jquery.tablesorter.widgets.js"></script>
<script src="include/jquery.tablesorter/addons/pager/jquery.tablesorter.pager.js"></script> 
<script src="include/funcionControl.js" type="text/javascript"></script>
<script type="text/javascript">

$(function() {
	$("#listaResultado")
		.tablesorter({
			theme: 'blue', 
			widthFixed: true, 
			widgets: ["zebra", "filter"], 
			headers:{21:{sorter:false, filter:false}},
			widgetOptions : { 
				filter_cssFilter   : '',
				filter_childRows   : false,
				filter_hideFilters : false,
				filter_ignoreCase  : true,
				filter_searchDelay : 300,
				filter_startsWith  : false,
				filter_hideFilters : false,
			}
		});
});

function validar(formulario) {
	if(formulario.dato.value == "") {
		alert("Debe colocar un dato de busqueda");
		return false;
	}
	if (formulario.filtro[0].checked) {
		resultado = esEnteroPositivo(formulario.dato.value);
		if (!resultado) {
			alert("El Nro. de Comprobante Interno debe ser un numero entero positivo");
			return false;
		} 
	}
	if (formulario.filtro[1].checked) {
		resultado = esEnteroPositivo(formulario.dato.value);
		if (!resultado) {
			alert("El C.U.I.T. / C.U.E. debe ser un numero entero positivo");
			return false;
		} 
	}
	if (formulario.filtro[2].checked) {
		if (!verificaCuilCuit(formulario.dato.value)) {
			alert("C.U.I.L. invalido");
			return false;
		}
	}
	if (formulario.filtro[3].checked) {
		resultado = esEnteroPositivo(formulario.dato.value);
		if (!resultado) {
			alert("El Código de Delegación debe ser un numero entero positivo");
			return false;
		} 
	}
	if (formulario.filtro[4].checked) {
		resultado = esEnteroPositivo(formulario.dato.value);
		if (!resultado) {
			alert("El Nro. de afiliado debe ser un numero entero positivo");
			return false;
		} 
	}
	
	$.blockUI({ message: "<h1>Generando Busqueda... <br>Esto puede tardar unos segundos.<br> Aguarde por favor</h1>" });
	return true;
}

</script>
</head>

<body bgcolor="#CCCCCC">
<form id="form1" name="form1" method="post" onSubmit="return validar(this)" action="buscador.php">
  <div align="center" >
  <p><input class="nover" type="reset" name="volver" value="Volver" onClick="location.href = 'menu.php'"/></p>
  <h2>Buscador Reintegro S.S.S.</h2>
  </div>
  <div align="center"> 
    <table class="nover">
      <tr>
	     <td><strong>Estado Presentacion </strong> </td>
	     <td>
	    	<select name="presentacion">
	    		<option value="TODAS" selected="selected">TODAS</option>
	    		<option value="FINALIZADAS">FINALIZADAS</option>
	    		<option value="EN PROCESO">EN PROCESO</option>
	    		<option value="CANCELADAS">CANCELADAS</option>
	    	</select>
	     </td>
	   </tr>
      <tr>
        <td rowspan="4"><div align="center"><strong>Buscar por </strong></div></td>
        <td><div align="left"><input type="radio" name="filtro"  value="0" checked="checked" /> Nro. Comprobante Interno </div></td>
      </tr>
      <tr>
        <td><div align="left"><input type="radio" name="filtro" value="1" /> C.U.I.T. / C.U.E. Prestador</div></td>
      </tr>
      <tr>
        <td><div align="left"><input type="radio" name="filtro" value="2" /> C.U.I.L. Afiliado</div></td>
      </tr>
	  <tr>
        <td><div align="left"><input type="radio" name="filtro" value="3" /> Nro. Factura</div></td>
      </tr>  
	
	  <tr>
	     <td><div align="center"> <strong>Dato</strong></div> </td>
	     <td> <input name="dato" type="text" id="dato" size="14" /></td>
	   </tr>
    </table>
  </div>
  <p align="center">
    <label class="nover">
    <input type="submit" name="Buscar" value="Buscar" />
    </label>
  </p>
  <div align="center">
  <?php 
	if ($buscando == 1) {
		echo "<p>$cartel</p>";	
		if ($noExiste == 1) {
			echo "<div style='color:#FF0000'><b> NO EXISTEN FACTURAS CON ESTE FILTRO DE BUSQUEDA </b></div><br>";
		} else { ?>
		  <table id="listaResultado" class="tablesorter" style="text-align: center;">
			<thead>
					<tr>
						<th style="font-size: 12px">Id</th>
						<th style="font-size: 12px" class="filter-select" data-placeholder="Selccione">Per. Pres.</th>
			 			<th style="font-size: 12px">Est.</th>
			 			<th style="font-size: 12px">Comp. Interno</th>
			 			<th style="font-size: 12px" class="filter-select" data-placeholder="Selccione">T.</th>
			 			<th style="font-size: 12px">C.U.I.L.</th>
			 			<th style="font-size: 12px">V. Cert.</th>
			 			<th style="font-size: 12px" class="filter-select" data-placeholder="Selccione">Periodo</th>
			 			<th style="font-size: 12px">C.U.I.T. / C.U.E.</th>
			 			<th style="font-size: 12px">Fec. Comp.</th>
			 			<th style="font-size: 12px">C.A.E.</th>
			 			<th style="font-size: 12px">Pto. Venta</th>
			 			<th style="font-size: 12px">Num. Comp.</th>
			 			<th style="font-size: 12px">$ Comp.</th>
			 			<th style="font-size: 12px">$ Soli.</th>
			 			<th style="font-size: 12px">$ Subs.</th>
			 			<th style="font-size: 12px">Cod. Prac.</th>
			 			<th style="font-size: 12px">Cant.</th>
			 			<th style="font-size: 12px">Prov.</th>
			 			<th style="font-size: 12px" class="filter-select" data-placeholder="Selccione">Dep.</th>
			 			<th style="font-size: 12px">+</th>
			 		</tr>
			</thead>
			<tbody>
			<?php while ($rowFactura = mysql_fetch_array($resFactura)) { ?>
					<tr>
						<td style="font-size: 14px"><?php echo $rowFactura['idpresentacion']?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['perpres'] ?></td>
						<td style="font-size: 14px">
						<?php if ($rowFactura['fechacancelacion'] != null) { 
									echo "<font color='red'>C</font>";
							  } else {
							  		if ($rowFactura['fechadeposito'] != null) {
							  			echo "<font color='blue'>F</font>";
							  		} else {
							  			echo "P";
							  		}
							  }?>
						</td>
						<td style="font-size: 14px"><?php echo number_format($rowFactura['nrocominterno'],0,"",".") ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['tipoarchivo'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['cuil'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['vtocertificado'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['periodo'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['cuit'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['fechacomprobante'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['cae'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['puntoventa'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['nrocomprobante'] ?></td>
						
				  <?php if ($rowFactura['tipoarchivo'] == 'DB') { ?>
							<td style="font-size: 14px"><?php echo "(".number_format($rowFactura['impcomprobante'],2,",",".").")" ?></td>
							<td style="font-size: 14px"><?php echo "(".number_format($rowFactura['impsolicitado'],2,",",".").")" ?></td>
				  <?php } else {  ?>
							<td style="font-size: 14px"><?php echo number_format($rowFactura['impcomprobante'],2,",",".") ?></td>
							<td style="font-size: 14px"><?php echo number_format($rowFactura['impsolicitado'],2,",",".")  ?></td>
				  <?php }
				  		if ($rowFactura['impmontosubsidio'] < 0) {
				  			$color = 'red';	
				  		} else {
				  			$color = '';	
				  		}
				  		?>
				  		
				  		<td style="font-size: 14px; color: <?php echo $color ?>;"><?php echo number_format($rowFactura['impmontosubsidio'],2,",",".") ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['codpractica'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['cantidad'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['provincia'] ?></td>
						<td style="font-size: 14px"><?php echo $rowFactura['dependencia'] ?></td>
						<td style="font-size: 14px">
							<a href="buscador.detalle.php?id=<?php echo $rowFactura['idpresentacion'] ?>&nro=<?php echo $rowFactura['nrocominterno'] ?>" target="_blank">INFO</a>
						</td>
					</tr>
			<?php } ?>
			</tbody>
		</table>
		<p><input class="nover" type="button" name="imprimir" value="Imprimir" onclick="window.print();"></p>
 	 <?php } 
		} ?>
  </div>
</form>
</body>
</html>