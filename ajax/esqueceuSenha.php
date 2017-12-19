<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$senha_nova   = substr(str_shuffle("abcdefghkmnjpqrstuvxzw1234567890"),0,6);
$sql_atualiza = "update user set password='".sha1($senha_nova)."' where email='".$usuario."'";
executaQuery($sql_atualiza);

$sql   = "select * from user where email='".$usuario."'";
$dados = recuperaDados($sql);

if(!$dados[0]['user_id']){
	die('-1');
}else{
	if(!$dados[0]['phone']){
		die('-2');
	}else{
		$phone_user = str_replace('-','',str_replace(' ','',str_replace(')','',str_replace('(','',$dados[0]['phone']))));
		$msg = urlencode(utf8_decode('Nova senha gerada: '.$senha_nova.'. Você pode altera-la no menu Editar Perfil.'));
		file_get_contents('http://api.clickatell.com/http/sendmsg?user=twgtwg01&password=NUaHDFTVeQXJfg&api_id=3582576&to=55'.$phone_user.'&text='.$msg);
		die('1');	
	}
}
?>