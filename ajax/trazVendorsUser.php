<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$sql   = "select v.vendor_id, name, display_name, phone, address1 
		  from vendor v, vendor_favoritos_user vu
		  where vu.user_id='".$user_id."' and v.vendor_id=vu.vendor_id and (name like '%".$valor."%' or display_name like '%".$valor."%' or 
		  		email like '%".$valor."%' or address1 like '%".$valor."%')
		  order by name";
$dados = recuperaDados($sql);

$cont = 0;
$dados_vendor = '';
while($dados[$cont]['vendor_id']){
	$dados_vendor .= $dados[$cont]['vendor_id'].'|';
	$dados_vendor .= utf8_encode($dados[$cont]['name']).'|';
	$dados_vendor .= utf8_encode($dados[$cont]['name']).'|';
	$dados_vendor .= utf8_encode($dados[$cont]['phone']).'|';
	$dados_vendor .= utf8_encode($dados[$cont]['address1']).'{S}';
	$cont++;
}

$dados_vendor = substr($dados_vendor,0,-3);

die($dados_vendor);
?>