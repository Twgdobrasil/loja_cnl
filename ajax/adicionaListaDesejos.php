<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql_b = "select wishlist
		  from user 
		  where user_id='".$usuario_id."'";
$dados = recuperaDados($sql_b);

if(!empty($dados[0]['wishlist']) && $dados[0]['wishlist'] != 'null' && $dados[0]['wishlist'] != ''){
	$lista_desejos = json_decode($dados[0]['wishlist']); 
}else{
	$lista_desejos = array();	
}

//Se o produto ainda não está na lista de desejos add no array e atualiza no banco, senão devolve -1 (já tem)
if(!in_array($produto_id,$lista_desejos)){
	array_push($lista_desejos,$produto_id);
	
	//Atualiza o campo wishlist (lista de desejos na tabela user
	$sql = "update user set wishlist='".json_encode($lista_desejos)."' where user_id='".$usuario_id."'";	
	if(executaQuery($sql) == '1'){
		die('1');
	}
}else{
	die('-1');	
}
?>