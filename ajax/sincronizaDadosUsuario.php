<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

//LOGIN NORMAL
$sql   = "select * 
		  from user, user_vendor
		  where user_id='".$usuario_id."' and user_id=usuario_id";
$dados = recuperaDados($sql);

//Se voltou vazio pode ser q nao tenha vínculo com o vendor, aí faço a query sem a tabela USER_VENDOR...
if(empty($dados[0]['username']) && empty($dados[0]['email'])){
	$sql   = "select * 
			  from user				 
			  where user_id='".$usuario_id."'";
	$dados = recuperaDados($sql);
}

$cont = 0;
$dados_user = '';
while($dados[$cont]['user_id']){
	$dados_user .= $dados[$cont]['email'].'|';
	$dados_user .= $dados[$cont]['password'].'|';
	$dados_user .= $dados[$cont]['username'].'|';
	$dados_user .= $dados[$cont]['fb_id'];
	$cont++;
}


die($dados_user);
?>