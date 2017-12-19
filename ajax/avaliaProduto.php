<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql_b = "select rating_user
		  from product 
		  where product_id='".$produto_id."'";
$dados = recuperaDados($sql_b);

if(!empty($dados[0]['rating_user']) && $dados[0]['rating_user'] != 'null' && $dados[0]['rating_user'] != ''){
	$rating = json_decode($dados[0]['rating_user']); 
}else{
	$rating = array();	
}

//Se o usuario ainda não está na lista de quem já avaliou add no array e atualiza no banco, senão devolve -1 (já tem)
if(!in_array($user_id,$rating)){
	array_push($rating,$user_id);
	
	//Atualiza o campo wishlist (lista de desejos na tabela user
	$sql = "update product set rating_user='".json_encode($rating)."',rating_num=rating_num+1,rating_total=rating_total+$avaliacao where product_id='".$produto_id."'";	
	if(executaQuery($sql) == '1'){
		die('1');
	}
}else{
	die('-1');	
}
?>