<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql   = "select product_id, title, sale_price, num_of_imgs, added_by, description, shipping_cost,color, options, current_stock, discount, rating_num, rating_total
		  from product
		  where product_id=".$product_id;
$dados = recuperaDados($sql);

$cont = 0;
$dados_produto = '';

$dados_added_by = json_decode($dados[$cont]['added_by']);
$array_added_by = transformaObjetoJsonEmArray($dados_added_by);
$id_vendor      = $array_added_by['id'];
$sql_vendor     = "select name
				   from vendor
				   where vendor_id=".$id_vendor;
$dados_vendor   = recuperaDados($sql_vendor);

if(!empty($dados[$cont]['rating_num']) && $dados[$cont]['rating_num'] != '' && $dados[$cont]['rating_num'] > 0){
	$avaliacao  = ceil($dados[$cont]['rating_total']/$dados[$cont]['rating_num']);
	$rating_num = $dados[$cont]['rating_num']; 
}else{
	$avaliacao  = 0;	
	$rating_num = 0;
}

$dados_produto .= $dados[$cont]['product_id'].'|';
$dados_produto .= utf8_encode($dados[$cont]['title']).'|';
$dados_produto .= $dados[$cont]['sale_price'].'|';
$dados_produto .= $dados[$cont]['num_of_imgs'].'|';
$dados_produto .= utf8_encode($dados_vendor[0]['name']).'|';
$dados_produto .= utf8_encode($dados[$cont]['description']).'|';
$dados_produto .= $dados[$cont]['shipping_cost'].'|';
$dados_produto .= 'Valor de entrega: R$'.$dados[$cont]['shipping_cost'].'|';
$dados_produto .= $id_vendor.'|';
$dados_produto .= $dados[$cont]['current_stock'].'|';
$dados_produto .= $dados[$cont]['discount'].'|';
$dados_produto .= $avaliacao.'|';
$dados_produto .= $rating_num.'|';
//Cores
$dados_produto .= str_replace(']','',str_replace('[','',str_replace('"','',str_replace(',"',';"',$dados[$cont]['color'])))).'|';

//Outros campos
$lendo = json_decode($dados[$cont]['options']);
$outros_campos = '';
foreach($lendo as $campo){	
	$opcoes = implode('{OP}',$campo->option);
	$outros_campos .= $campo->type.'{S}'.$campo->title.'{S}'.$opcoes.';';
}
$dados_produto .= $outros_campos;

die(substr($dados_produto,0,-1));
?>