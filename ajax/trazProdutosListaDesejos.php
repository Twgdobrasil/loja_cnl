<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

//Traz todos os vendors...
$sql_p   = "select wishlist 
		    from user
		    where user_id='".$user_id."'";
$dados_p = recuperaDados($sql_p);

if($dados_p[0]['wishlist'] == '[]' || $dados_p[0]['wishlist'] == ''){
	die('-1');
}

$produtos_ids = str_replace(',,',',',str_replace('"','',str_replace(']','',str_replace('[','',$dados_p[0]['wishlist']))));
if($produtos_ids[0] == ','){
	$produtos_ids = substr($produtos_ids,1,strlen($produtos_ids)-1);
}

$sql   = "select product_id, title, sale_price
		  from product
		  where product_id in (".$produtos_ids.")";
$dados = recuperaDados($sql);

$produtos = '';
$cont = 0;
while($dados[$cont]['product_id']){
	$produtos .= $dados[$cont]['product_id'].'|';
	$produtos .= utf8_encode($dados[$cont]['title']).'|';
	$produtos .= $dados[$cont]['sale_price'].'{S}';
	$cont++;
}
$produtos = substr($produtos,0,-3);

die($produtos);
?>