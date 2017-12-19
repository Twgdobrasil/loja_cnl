<?php 
session_start();
require('../../db/funcoes.php');

extract($_POST);
	
if($fb_id != ''){
	$sql   = "select user_id 
		  	  from user
		  	  where fb_id='".$fb_id."'";
	$dados = recuperaDados($sql);	
	
	if($dados[0]['user_id'] && $dados[0]['user_id'] != ''){
		die('1');	
	}
}

if($grava_atualiza == 'atualiza'){
	$ac_where = '';
	if($senha != ''){
		$ac_where = ", password='".sha1($senha)."'";
	}
	
	//Se for o endereco padrao atualiza na tabela do usuario, senao nao...
	if($endereco_padrao == '1'){
		$sql = "update user 
				set username='".utf8_decode($nome)."', surname='".utf8_decode($sobrenome)."', email='".$email."', address1='".utf8_decode($endereco)."',
				city='".utf8_decode($cidade)."', zip='".$cep."' $ac_where
				where user_id='".$user_id."'";
	}else{
		$sql = "update user 
				set username='".utf8_decode($nome)."', surname='".utf8_decode($sobrenome)."', email='".$email."' $ac_where
				where user_id='".$user_id."'";
	}
}else{
	$creation_date = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$sql  = "insert into user (username, surname, email, password, fb_id, zip, address1, city, creation_date) values
							 ('".utf8_decode($nome)."','".utf8_decode($sobrenome)."','".$email."','".sha1($senha)."','".$fb_id."','".$cep."','".utf8_decode($endereco)."','".utf8_decode($cidade)."','".$creation_date."')";	
}

if(executaQuery($sql) == '1'){
	$address = urlencode($endereco.', '.$cidade.', Brasil');
	$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$address.'&sensor=false');
	$output= json_decode($geocode);
	$lat = $output->results[0]->geometry->location->lat;
	$long = $output->results[0]->geometry->location->lng;

	if($grava_atualiza != 'atualiza'){
		$user_id = mysql_insert_id();
		//Grava o endereco padrao na tabela de enderecos_entrega
		$sql_e = "insert into enderecos_entrega(user_id, nome, endereco, cep, cidade, padrao, latitude, longitude) values
						 			           ('".$user_id."','Padrao','".utf8_decode($endereco)."','".$cep."','".utf8_decode($cidade)."','1','".$lat."','".$long."')";
	}else{
		//Se for o endereco padrao, deleta da tabela de enderecos_entrega pois vai recadastrar, senao atualiza
		if($endereco_padrao == '1'){
			$sql_d = "delete from enderecos_entrega where user_id='".$user_id."' and padrao='1'";
			executaQuery($sql_d);	
			//Grava o endereco padrao na tabela de enderecos_entrega
			$sql_e = "insert into enderecos_entrega(user_id, nome, endereco, cep, cidade, padrao, latitude, longitude) values
						 			       ('".$user_id."','Padrao','".utf8_decode($endereco)."','".$cep."','".utf8_decode($cidade)."','1','".$lat."','".$long."')";
		}else{
			$sql_e = "update enderecos_entrega set endereco='".utf8_decode($endereco)."', cep='".$cep."', cidade='".utf8_decode($cidade)."', latitude='".$lat."', longitude='".$long."' 
					  where enderecos_entrega_id='".$endereco_id."'";		
		}
	}
	executaQuery($sql_e);
	die('1');
}else{
	die('-1');	
}
?>