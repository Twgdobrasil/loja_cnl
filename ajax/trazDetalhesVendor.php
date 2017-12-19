<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql   = "select vendor_id, name, display_name, phone, address1, address2, lat_lang, email,
				 horario_semana_inicio, horario_semana_fim, horario_fds_inicio, horario_fds_fim
		  from vendor
		  where vendor_id=".$vendor_id;
$dados = recuperaDados($sql);

$dados_vendor .= $dados[0]['vendor_id'].'|';
$dados_vendor .= utf8_encode($dados[0]['name']).'|';
$dados_vendor .= utf8_encode($dados[0]['display_name']).'|';
$dados_vendor .= utf8_encode($dados[0]['phone']).'|';
$dados_vendor .= utf8_encode($dados[0]['address1']).'|';

$lat_long     = explode(',',str_replace(')','',str_replace('(','',$dados[0]['lat_lang'])));

$dados_vendor .= trim($lat_long[0]).'|'.trim($lat_long[1]).'|';
$dados_vendor .= $dados[0]['address2'].'|';
$dados_vendor .= utf8_encode($dados[0]['city']).'|';
$dados_vendor .= $dados[0]['email'].'|';

//Horario de funcionamento
$dados_vendor .= $dados[0]['horario_semana_inicio'].'|';
$dados_vendor .= $dados[0]['horario_semana_fim'].'|';
$dados_vendor .= $dados[0]['horario_fds_inicio'].'|';
$dados_vendor .= $dados[0]['horario_fds_fim'];

die($dados_vendor);
?>