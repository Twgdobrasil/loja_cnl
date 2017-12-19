<?php 
session_start();
require('../../db/funcoes.php');

$sql   = "select * 
		  from user
		  where user_id='".$_POST['usuario_id']."'";
$dados = recuperaDados($sql);

$dados_user = utf8_encode($dados[0]['email']).'|'.utf8_encode($dados[0]['username']).'|'.utf8_encode($dados[0]['surname']).'|'.utf8_encode($dados[0]['address1']).
			  '|'.utf8_encode($dados[0]['city']).'|'.utf8_encode($dados[0]['zip']);

die($dados_user);
?>