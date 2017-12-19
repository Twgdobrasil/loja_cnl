<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql   = "select product_id, title, sale_price, num_of_imgs, added_by, description, shipping_cost, color, current_stock
		  from product
		  where product_id=".$product_id;
$dados = recuperaDados($sql);

$cont = 0;
$dados_produto = '';
while($dados[$cont]['product_id']){
	$dados_produto .= utf8_encode($dados[$cont]['title']).'|';
	$dados_produto .= $dados[$cont]['sale_price'].'|';
	$dados_produto .= $dados[$cont]['current_stock'].'|';
	$dados_produto .= $dados[$cont]['shipping_cost'].'|';
	$dados_produto .= str_replace(']','',str_replace('[','',str_replace('"','',str_replace(',"',';"',$dados[$cont]['color']))));
	$cont++;
}

die($dados_produto);
?>