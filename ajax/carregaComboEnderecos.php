<?php 
session_start();
require('../db/funcoes.php');

$sql   = "select enderecos_entrega_id, nome
		  from enderecos_entrega
		  where user_id='".$_POST['user_id']."'
		  order by padrao desc";
$dados = recuperaDados($sql);

$dados_end = '';
$cont = 0;
while($dados[$cont]['enderecos_entrega_id']){
	$dados_end .= $dados[$cont]['enderecos_entrega_id'].'|'.utf8_encode($dados[$cont]['nome']).'{S}';
	$cont++;
}
$dados_end = substr($dados_end,0,-3);

die($dados_end);
?>