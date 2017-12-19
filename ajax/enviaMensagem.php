<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql  = "insert into mensagens_cnll(enviada_por, user_id, vendor_id, mensagem, data) values
							  	   ('user','".$user_id."','".$vendor_id."','".utf8_decode($mensagem)."',now())";	

executaQuery($sql);
?>