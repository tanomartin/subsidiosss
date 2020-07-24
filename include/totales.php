<?php 

$arrayTotales = array();
$sqlPeriodos = "SELECT * FROM intecronograma WHERE carpeta = ".$rowPresentacion['carpeta'];
$resPeriodos = mysql_query($sqlPeriodos);
$rowPeriodos = mysql_fetch_array($resPeriodos);
$arrayPeriodos = explode(",",$rowPeriodos['periodosincluidos']);
foreach ($arrayPeriodos as $per) {
    $arrayTotales[$per]['Facturas'] = 0;
    $arrayTotales[$per]['Escuelas'] = 0;
}

$sqlTotFacPer = "SELECT count(nrocominterno) as cant, periodo
                    FROM intepresentaciondetalle i
                    WHERE idpresentacion = $idPresentacion and codpractica not in (97,98,99)
                    GROUP BY periodo";
$resTotFacPer = mysql_query($sqlTotFacPer);
$totalFac = 0;
while ($rowTotFacPER = mysql_fetch_array($resTotFacPer)) {
    $totalFac += $rowTotFacPER['cant'];
    $arrayTotales[$rowTotFacPER['periodo']]['Facturas'] = $rowTotFacPER['cant'];
}

$sqlTotEscPer = "SELECT count(nrocominterno) as cant, periodo
                    FROM intepresentaciondetalle i
                    WHERE idpresentacion = $idPresentacion and codpractica in (97,98,99)
                    GROUP BY periodo";
$resTotEscPer = mysql_query($sqlTotEscPer);
$totalEsc = 0;
while ($rowTotEscPer = mysql_fetch_array($resTotEscPer)) {
    $totalEsc += $rowTotEscPer['cant'];
    $arrayTotales[$rowTotEscPer['periodo']]['Escuelas'] = $rowTotEscPer['cant'];
} ?>

<h3>Totales</h3>
<div class="grilla">
    <table>
    	<thead>
    		<tr>
    			<th>Periodos</th>
     		  	<th>Facturas</th>
   	 		  	<th>Escuelas</th>
   	 		  	<th>Total</th>
  			</tr>
    	</thead>
    	<tbody>
  <?php foreach ($arrayTotales as  $periodo => $arrayTotales) { ?>
	 		<tr>
	 	    	<td><?php echo $periodo ?></td>
	 	  	  <?php $totalPeriodo = 0;
	 	  	        foreach ($arrayTotales as $totales) { 
	 	  	        $totalPeriodo += $totales; ?>
    	 		  		<td><?php echo $totales ?></td>
	 	      <?php } ?>
	 	      		<td><?php echo $totalPeriodo ?></td>
	 	   	</tr>
  <?php } ?>
  		</tbody>	
  		<thead>
	 	 	<tr>
	 	 		<th>Total</th>
	 	 		<th><?php echo $totalFac ?></th>
	 	 		<th><?php echo $totalEsc ?></th>
	 	 		<th><?php echo $totalFac + $totalEsc ?></th>
	 	 	</tr>
	 	</thead>
	</table>
</div>