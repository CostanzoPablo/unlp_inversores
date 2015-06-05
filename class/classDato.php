<?php
function insertMultiple($query,$rows, $db) {
    if (count($rows)>0) {
        $args = array_fill(0, count($rows[0]), '?');

        $params = array();
        foreach($rows as $row)
        {
            $values[] = "(".implode(',', $args).")";
            foreach($row as $value)
            {
                $params[] = $value;
            }
        }

        $query = $query." VALUES ".implode(',', $values);
        $stmt = $db->prepare($query);
        $stmt->execute($params);
    }
}

class Dato{
	public $id, $fecha, $canal0, $canal1;

    private function __construct() {
    	
    }

	public static function buscar($fecha, $db){
		$instance = new self();
		foreach($db->query("SELECT * FROM datos WHERE fecha = '$fecha'") as $row) {
			$instance->id = $row["id"];
			$instance->fecha = $row["fecha"];
			$instance->canal0 = $row["canal0"];
			$instance->canal1 = $row["canal1"];
		}			
		return $instance;
	}

	public static function buscarPorId($id, $db){
		$instance = new self();
		foreach($db->query("SELECT * FROM datos WHERE id = '$id'") as $row) {
			$instance->id = $row["id"];
			$instance->fecha = $row["fecha"];
			$instance->canal0 = $row["canal0"];
			$instance->canal1 = $row["canal1"];
		}			
		return $instance;
	}

	public static function buscarMaximo($db){
		$instance = new self();
		$fecha = null;
		foreach($db->query("SELECT * FROM datos ORDER by fecha DESC LIMIT 1") as $row) {
			$fecha = $row["fecha"];
		}	
		return $fecha;		
	}


	public static function nuevos($datos, $db){
		$sql = "INSERT INTO datos (fecha, canal0, canal1)";
		$query = $db->prepare( $sql );

		foreach ($datos as $key => $value) {
			$dataToInsert[] = array($value["fecha"], $value["canal0"], $value["canal1"]);		
		}

		insertMultiple($sql,$dataToInsert, $db);
		var_dump($dataToInsert);
		return $db->lastInsertId();
	}

	public function actualizar($fecha, $canal0, $canal1, $db){
		$db->exec("UPDATE datos SET fecha = '$fecha', canal0 = '$canal0', canal1 = '$canal1' WHERE id = '$this->id'");
		$instance->fecha = $fecha;
		$instance->canal0 = $canal0;
		$instance->canal1 = $canal1;
		return $instance;
	}

	public function eliminar($db){
		$db->exec("DELETE FROM datos WHERE id = '$this->id'");	
	}

	public static function listar($desde, $hasta, $db){
		$datos = null;
		foreach($db->query("SELECT * FROM datos WHERE fecha BETWEEN $desde AND $hasta ORDER by fecha ASC") as $row) {
			$fecha = $row["fecha"] * 1000;
			$canal0["x"] = $fecha;
			$canal0["y"] = floatval($row["canal0"] * 10);//temperatura 10-80.. una linea.......
			//Trabajar escalas de ambos lados
			$canal1["x"] = $fecha;
			$canal1["y"] = floatval($row["canal1"] * 12370);//radi. 50-1400... con fondo

			$datos["canal0"][] = $canal0;
			$datos["canal1"][] = $canal1;
		}
		return $datos;
	}
}
?>