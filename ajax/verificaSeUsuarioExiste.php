<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

//Aguardando ACEITE
$sql   = "select user_id
		  from user 
		  where email='".$email."'";
$dados = recuperaDados($sql);

if($dados[0]['user_id']){
	die('1');	
}else{
	die('-1');
}
?>