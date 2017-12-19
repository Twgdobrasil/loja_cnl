<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$address = urlencode($endereco.', '.$cidade.', Brasil');

$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');

$output= json_decode($geocode);

$lat = $output->results[0]->geometry->location->lat;
$long = $output->results[0]->geometry->location->lng;

$sql = "insert into enderecos_entrega(user_id, nome, endereco, cep, cidade, latitude, longitude) values
						 			 ('".$user_id."','".utf8_decode($nome)."','".utf8_decode($endereco)."','".$cep."','".utf8_decode($cidade)."','".$lat."','".$long."')";	

if(executaQuery($sql) == '1'){
	die('1');
}else{
	die('-1');	
}
?>