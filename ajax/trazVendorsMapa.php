<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$raio = 10;
if($latitude_user != '' && $longitude_user != ''){
	/* Isso seria legal usar caso a latitude e longitude tivessem sendo gravados separados no banco, pois aí traria um conjunto de dados menor já de cara
	$latitude_min  = $latitude_user - ($raio / 111.18);
	$latitude_max  = $latitude_user + ($raio / 111.18);
	$longitude_min = $longitude_user - ($raio / 111.18);
	$longitude_max = $longitude_user + ($raio / 111.18);

	//Condições para trazer só os fornecedores mais próximos na query
	$conditions["FornecedorEndereco.latitude BETWEEN ? and ?"] = array($latitude_min, $latitude_max);
	$conditions["FornecedorEndereco.longitude BETWEEN ? and ?"] = array($longitude_min, $longitude_max);
	*/

	//Traz todos os vendors...
	$sql   = "select * 
			  from vendor 
			  where name like '%".$valor."%' or display_name like '%".$valor."%'";
	$dados = recuperaDados($sql);

	$vendors = '';
	$cont = 0;
	while($dados[$cont]['vendor_id']){
		//Verificar aqui se está vindo certo ou tenho q tratar...
		$lat_long  		  = explode(',',str_replace(')','',str_replace('(','',$dados[$cont]['lat_lang'])));//(61.21620119999999, -149.89960359999998)

		$latitude_vendor  = trim($lat_long[0]);
		$longitude_vendor = trim($lat_long[1]);

		//Se for menor que 10 KM a distancia
		if(strlen($latitude_vendor)>3 && strlen($longitude_vendor)>3){
			//if(calculaDistancia($latitude_user,$longitude_user,$latitude_vendor,$longitude_vendor) < 10){
				$vendors .= $dados[$cont]['vendor_id'].'|'.utf8_encode($dados[$cont]['name']).'|'.$latitude_vendor.'|'.$longitude_vendor.'|'.utf8_encode($dados[$cont]['address1']).'|'.$dados[$cont]['phone'].'{S}';
			//} 
		}
		$cont++;
	}
	$vendors = substr($vendors,0,-3);
	die($vendors);
}else{
	die('-1');
}


function calculaDistancia($lat_origem, $long_origem, $lat_destino, $long_destino){
	return round((sqrt((($lat_destino - $lat_origem) * ($lat_destino - $lat_origem) + ($long_destino - $long_origem) * ($long_destino - $long_origem))) * 111.18), 1);
}

?>