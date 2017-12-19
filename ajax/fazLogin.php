<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$senha = sha1($senha);
//Login pelo FACE
if($fb_id != ''){
	$sql   = "select *
			  from user, user_vendor 
			  where fb_id='".$fb_id."' and user_id=usuario_id";
}else{
	//LOGIN NORMAL
	$sql   = "select * 
			  from user, user_vendor
			  where email='".$usuario."' and password='".$senha."' and user_id=usuario_id";
}
$dados = recuperaDados($sql);

//Se voltou vazio pode ser q nao tenha vínculo com o vendor, aí faço a query sem a tabela USER_VENDOR...
if(empty($dados[0]['username']) && empty($dados[0]['email'])){
	//Login pelo FACE
	if($fb_id != ''){
		$sql   = "select *
				  from user
				  where fb_id='".$fb_id."'";
	}else{
		//LOGIN NORMAL
		$sql   = "select * 
				  from user
				  where email='".$usuario."' and password='".$senha."'";
	}
	$dados = recuperaDados($sql);
}

if(!empty($dados[0]['username']) || !empty($dados[0]['email'])){
	if($dados[0]['user_status'] == '-1'){
		die('-2');
	}else{
		$_SESSION['usuario_id']   = $dados[0]['user_id'];
		$_SESSION['usuario'] 	  = $dados[0]['email'];
		$_SESSION['email'] 		  = $dados[0]['email'];
		$_SESSION['usuario_name'] = $dados[0]['username'];
		$_SESSION['vendor_id']    = $dados[0]['vendor_id'];
		$_SESSION['token'] 	      = $token;
		if($token != ''){
			$sql 					  = "update user set token_cnll='".$token."', device='".$device."' where user_id='".$_SESSION['usuario_id']."'";
			executaQuery($sql);
		}
		
		die($_SESSION['usuario_id'].'|'.$_SESSION['usuario_name'].'|'.$_SESSION['vendor_id']);	
	}
}else{
	die('-1');	
}
?>