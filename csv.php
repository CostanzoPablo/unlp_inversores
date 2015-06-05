<?php
error_reporting(E_ALL ^ E_DEPRECATED);

include('./conectar.php');
include('./class/classDato.php');

$maximo = Dato::buscarMaximo($db);
//buscar todos los archivos, pickear algun csv...

$csv = array_map('str_getcsv', file('./csv/cargar.csv'));
//$anterior["canal1"] = 999999999999999999999;
foreach ($csv as $key => $value) {
	if ($key >= 8){
		list($day, $month, $year, $hour, $minute, $second) = split('[/ :]', $csv[$key]["1"]); 
		
		$am_pm = $csv[$key]["1"][24].$csv[$key]["1"][25];
		$fecha = strtotime("$year-$month-$day $hour:$minute $am_pm");
		$fecha += $second;
		if ($maximo >= (int)$fecha){
			echo '<font color="#AA0000">YA EXISTE</font><br>';
		}else{
			echo '<font color="#00AA00">NO EXISTE... CREANDO...</font><br>';
			$nuevo["fecha"] = $fecha;
			$nuevo["canal0"] = $csv[$key]["2"];	
			$nuevo["canal1"] = $csv[$key]["3"];
			if (($anterior["canal1"] / 1.2) > $nuevo["canal1"]){
				$error = true;
			}else{
				$error = false;
				if (($anterior["canal1"] * 3) < $nuevo["canal1"]){
					$error = true;
				}else{
					$error = false;
				}
			}
			if ($nuevo["canal0"] < 0){
				$nuevo["canal0"] = $anterior["canal0"];
			}
			if ($nuevo["canal1"] < 0){
				$nuevo["canal1"] = $anterior["canal1"];
			}			
			$anterior = $nuevo;
			if ($error == false){
				$datos[] = $nuevo;
			}
		}
	}
}

if (isset($datos)){
	Dato::nuevos($datos, $db);
}
//mover el csv adentro de una carpeta que diga ./csv/backup
?>
