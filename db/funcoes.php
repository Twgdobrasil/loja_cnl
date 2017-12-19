<?php 
//require('../db/conexao.php');
require('conexao.php');

function recuperaDados($sql){	
	$res = mysql_query($sql);
	
	while($row = mysql_fetch_assoc($res)){
		$return[] = $row;
	}	
	
	return $return;
}

function executaQuery($sql){
	if($res = mysql_query($sql)){
		return 1;
	}else{
		echo mysql_error();
		return -1;	
	}
}

function contaRegistros($sql){
	$exec = mysql_query($sql);
	$num_rows = mysql_num_rows($exec);
	return $num_rows;
}

function transformaObjetoJsonEmArray($d){
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}
	
	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}else{
		// Return array
		return $d;
	}
}
?>