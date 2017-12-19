<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql   = "select vendor_id, name, display_name, phone, address1, lat_lang 
		  from vendor
		  where (name like '%".$valor."%' or display_name like '%".$valor."%' or 
		  		 email like '%".$valor."%' or address1 like '%".$valor."%')
		  order by name";
$dados = recuperaDados($sql);

$cont = 0;
$array_dados = array();
while($dados[$cont]['vendor_id']){
	$lat_long  		  = explode(',',str_replace(')','',str_replace('(','',$dados[$cont]['lat_lang'])));//(61.21620119999999, -149.89960359999998)
	$latitude_vendor  = trim($lat_long[0]);
	$longitude_vendor = trim($lat_long[1]);
	$distancia = calculaDistancia($latitude_user,$longitude_user,$latitude_vendor,$longitude_vendor);
	
	$sql_v   = "select vendor_favoritos_user_id 
		  	    from vendor_favoritos_user
		  	    where user_id='".$user_id."' and vendor_id='".$dados[$cont]['vendor_id']."'"; 
	$dados_v = recuperaDados($sql_v);
	if(!empty($dados_v[0]['vendor_favoritos_user_id'])){
		$vendor_favorito = '1'; 	
	}else{
		$vendor_favorito = '-1';
	}
	
	$array_dados['vendor_id'][$cont]       = $dados[$cont]['vendor_id'];
	$array_dados['name'][$cont] 	       = utf8_encode($dados[$cont]['name']);
	$array_dados['display_name'][$cont]    = utf8_encode($dados[$cont]['name']);
	$array_dados['phone'][$cont] 		   = $dados[$cont]['phone'];
	$array_dados['address1'][$cont] 	   = utf8_encode($dados[$cont]['address1']);
	$array_dados['vendor_favorito'][$cont] = $vendor_favorito;
	$array_dados['distancia'][$cont] 	   = $distancia;
	
	$cont++;
}

//print_r($array_dados['distancia']);
//print_r($array_dados['name']);
//Ordena o array da menor pra maior distancia mantendo os indices originais
asort($array_dados['distancia']);
//print_r($array_dados['distancia']);

$dados_vendor = '';
foreach($array_dados['distancia'] as $key => $value){
	$dados_vendor .= $array_dados['vendor_id'][$key].'|';
	$dados_vendor .= $array_dados['name'][$key].'|';
	$dados_vendor .= $array_dados['name'][$key].'|';
	$dados_vendor .= $array_dados['phone'][$key].'|';
	$dados_vendor .= $array_dados['address1'][$key].'|';
	$dados_vendor .= $array_dados['vendor_favorito'][$key].'|'; 
	$dados_vendor .= $array_dados['distancia'][$key].' KM{S}'; 	
}
$dados_vendor = substr($dados_vendor,0,-3);

die($dados_vendor);

function calculaDistancia($lat_origem, $long_origem, $lat_destino, $long_destino){
	return round((sqrt((($lat_destino - $lat_origem) * ($lat_destino - $lat_origem) + ($long_destino - $long_origem) * ($long_destino - $long_origem))) * 111.18), 1);
}
?>