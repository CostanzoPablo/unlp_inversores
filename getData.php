<?php
include('./conectar.php');
include('./class/classDato.php');

if (isset($_POST["ddia"])){
	$desde = mktime(0, 0, 0, $_POST["dmes"], $_POST["ddia"], $_POST["danio"]);
	$hasta = mktime(23, 59, 59, $_POST["hmes"], $_POST["hdia"], $_POST["hanio"]);
}else{
	$desde = mktime(0, 0, 0, date("m", time()), date("d", time()), date("Y", time()));
	$hasta = mktime(23, 59, 59, date("m", time()), date("d", time()), date("Y", time()));
}
$retorno = Dato::listar($desde, $hasta, $db);

echo json_encode($retorno);
?>