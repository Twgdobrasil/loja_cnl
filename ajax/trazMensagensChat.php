<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql   = "select mensagens_cnll_id, enviada_por, mensagem
		  from mensagens_cnll
		  where vendor_id='".$vendor_id."' and user_id='".$user_id."'
		  order by mensagens_cnll_id asc";
$dados = recuperaDados($sql);

$cont = 0;
$dados_msg = '';
while($dados[$cont]['mensagens_cnll_id']){
	$dados_msg .= $dados[$cont]['mensagens_cnll_id'].'|';
	$dados_msg .= $dados[$cont]['enviada_por'].'|';
	$dados_msg .= utf8_encode($dados[$cont]['mensagem']).'{S}';
	$cont++;
}

$dados_msg = substr($dados_msg,0,-3);

die($dados_msg);
?>