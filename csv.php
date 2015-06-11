<?php
error_reporting(E_ALL ^ E_DEPRECATED);

include('./conectar.php');
include('./class/classDato.php');

$maximo = Dato::buscarMaximo($db);
//buscar todos los archivos, pickear algun csv...
$files = scandir('./csv/');
if (!isset($files[2])){
	die('No hay archivos para cargar en: ./csv');
}

$csv = array_map('str_getcsv', file('./csv/'.$files[2]));
//$anterior["canal1"] = 999999999999999999999;
foreach ($csv as $key => $value) {
	if ($key >= 8){
		list($day, $month, $year, $hour, $minute, $second) = split('[/ :]', $csv[$key]["1"]); 
		
		$am_pm = $csv[$key]["1"][24].$csv[$key]["1"][25];
		$fecha = strtotime("$year-$month-$day $hour:$minute $am_pm");
		$fecha += $second;
		if ($maximo >= (int)$fecha){
			echo '<font color="#AA0000">YA EXISTE EL DATO</font><br>';
		}else{
			echo '<font color="#00AA00">NO EXISTE... CREANDO...</font><br>';
			$nuevo["fecha"] = $fecha;
			$nuevo["canal0"] = $csv[$key]["2"];	
			$nuevo["canal1"] = $csv[$key]["3"];
			$error = false;
			//canal0 = Temperatura
			if (isset($anterior["canal1"])){
				/*if ($nuevo["canal1"] * 1.5 > $promedio){
					$error = true;
				}
				if ($nuevo["canal1"] * 1.5 < $promedio){
					$error = true;
				}*/				
				if (($nuevo["canal1"] * 12370) < ($anterior["canal1"] * 12370)-150){
					echo (int)($nuevo["canal1"] * 12370);
					echo ' POR DEBAJO DE LO NORMAL<br>';
					$error = true;
				}else{
					if ($nuevo["canal1"] * 12370 > 1300){
						echo (int)($nuevo["canal1"] * 12370);
						echo ' POR ARRIBA DE LO NORMAL<br>';						
						$error = true;
					}else{
						if (($nuevo["canal1"] * 12370)-150 > ($anterior["canal1"] * 12370)){
							echo (int)($nuevo["canal1"] * 12370);
							echo ' POR ARRIBA DE LO NORMAL<br>';						
							$error = true;
						}
					}
				}
			}
			if ($nuevo["canal0"] < 0){
				$nuevo["canal0"] = $anterior["canal0"];
			}
			if ($nuevo["canal1"] < 0){
				$nuevo["canal1"] = $anterior["canal1"];
			}			
			if ($error == false){
				$anterior = $nuevo;
				$datos[] = $nuevo;
			}
		}
	}
}

if (isset($datos)){
	Dato::nuevos($datos, $db);
}
//mover el csv adentro de una carpeta que diga ./csv/backup
rename('./csv/'.$files[2], './backup/'.$files[2]);
?>
