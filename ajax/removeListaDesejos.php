<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

//Traz a lista de desejos do usuario
$sql   = "select wishlist
		  from user
		  where user_id='".$usuario_id."'";
$dados = recuperaDados($sql);

//Quebra em array a lista de desejos que está em JSON no banco
$lista_desejos = json_decode($dados[0]['wishlist']);

$lista_nova = array();
for($i=0;$i<count($lista_desejos);$i++){
	if($lista_desejos[$i] != $produto_id){
		array_push($lista_nova,$produto_id);
	}
}
//Codifica em JSON a lista de desejos sem o id do produto removido
$wishlist = json_encode($lista_nova);

//Atualiza no banco com a nova lista de desejos
$sql = "update user set wishlist='".$wishlist."' where user_id='".$usuario_id."'";
if(executaQuery($sql) == '1'){
	die('1');
}else{
	die('-1');
}
?>