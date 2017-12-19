<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);
	
$sql = "insert into vendor_favoritos_user (vendor_id,user_id) values('".$vendor_id."','".$user_id."')";	


executaQuery($sql);

die('1');
?>