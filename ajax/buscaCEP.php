<?php
extract($_POST);

$cep = trim(substr($cep,0,5).substr($cep,6,3));

$caminho='http://republicavirtual.com.br/web_cep.php?cep='.urlencode($cep).'&formato=query_string';
$resultado = file_get_contents($caminho);

parse_str($resultado,$resposta);

//GARANTIDOS QUE VOLTAM
$uf 			 = $resposta["uf"];
$cidade 		 = $resposta["cidade"];
$bairro 		 = $resposta["bairro"];
$tipo_logradouro = $resposta["tipo_logradouro"];
$logradouro		 = $resposta["logradouro"];
$r				 = $resposta["resultado"];


if($r == '1'){
	$encoded = utf8_encode($cidade."|".$bairro."|".$tipo_logradouro." ".$logradouro."|".$uf);
	die($encoded);
}else{
	die('-1');
}
?>