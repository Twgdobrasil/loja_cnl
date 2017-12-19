<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);
	
$sql = "delete from vendor_favoritos_user where vendor_id='".$vendor_id."' and user_id = '".$user_id."'";	

executaQuery($sql);

die('1');
?>