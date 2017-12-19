<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql   = "select *
          from propagandas
          order by propaganda_id
          limit 4";
$dados = recuperaDados($sql);

$cont = 0;
$dados_msg = '';
while($dados[$cont]['propaganda_id']){
    if($dados[$cont]['publicar'] == '1'){
    	  $dados_msg .= $dados[$cont]['propaganda_id'].'|';
      	$dados_msg .= $dados[$cont]['link'].'|';
      	$dados_msg .= utf8_encode($dados[$cont]['nome_fornecedor']).'|';
        $dados_msg .= $dados[$cont]['imagem'].'{S}';
    }
	  $cont++;
}

$dados_msg = substr($dados_msg,0,-3);

die($dados_msg);
?>