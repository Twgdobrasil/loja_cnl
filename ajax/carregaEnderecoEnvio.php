<?php 
session_start();
require('../db/funcoes.php');

$where = '';
if($_POST['endereco_id'] != ''){
	$where = " and enderecos_entrega_id='".$_POST['endereco_id']."'";
}

$sql   = "select enderecos_entrega_id, endereco, cidade, cep, nome, latitude, longitude
		  from enderecos_entrega
		  where user_id='".$_POST['user_id']."' $where
		  order by padrao desc";
$dados = recuperaDados($sql);

$dados_end .= $dados[0]['enderecos_entrega_id'].'|'.utf8_encode($dados[0]['endereco']).'|'.utf8_encode($dados[0]['cidade']).'|'.$dados[0]['cep'].'|'.$dados[0]['nome'].'|'.$dados[0]['latitude'].'|'.$dados[0]['longitude'];

die($dados_end);
?>