<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$where = '';

$trouxe_algo = '1';
//Se clicou em algum dos fornecedores, verifica se digitou algo na busca senão faz a query crua mesmo, sem filtro de valor
if($vendor_id != ''){
	$where = " and added_by like '%{\"type\":\"vendor\",\"id\":\"".$vendor_id."\"}%'";	
	
	if($opcao == 'produtos'){
		$sql = "select product_id, title, sale_price, discount, added_by, current_stock
			    from product
			    where (title like '%".$valor."%' or tag like '%".$valor."%') and status='ok' $where
			    order by title ";
	}else{
		$sql = "select product_id, title, sale_price, discount, added_by, current_stock
			    from product
			    where status='ok' $where
			    order by title ";
	}
	
	$dados = recuperaDados($sql);
}else{
	//Se clicou no botão buscar
	if($opcao == 'produtos'){
		//Se for produtos a opcao procura na tabela de produtos direto
		$sql   = "select product_id, title, sale_price, discount, added_by, current_stock
				  from product
				  where (title like '%".$valor."%' or tag like '%".$valor."%') and status='ok' $where
				  order by title ";
		$dados = recuperaDados($sql);
	}else{
		//Se for fornecedores a opcao, 1o procura na tabela de vendors quem sao os vendor com o valor digitado e depois usa na query de produtos
		$sql_v   = "select vendor_id
					from vendor
					where (name like '%".$valor."%' or company like '%".$valor."%' or display_name like '%".$valor."%') 
					order by vendor_id";
		$dados_v = recuperaDados($sql_v);
		
		$cont  = 0;
		$where = '';
		while($dados_v[$cont]['vendor_id']){
			if($cont == 0){
				$where = ' and (';	
			}else{
				$where .= ' or ';	
			}
			$where .= " added_by like '%{\"type\":\"vendor\",\"id\":\"".$dados_v[$cont]['vendor_id']."\"}%' ";
			
			//Se ainda nao está gravado na tabela como vendor favorito do usuario grava...
			/*$sql_verifica   = "select vendor_id from vendor_favoritos_user where user_id='".$user_id."' and vendor_id='".$dados_v[$cont]['vendor_id']."'";
			$dados_verifica = recuperaDados($sql_verifica);
			if(empty($dados_verifica[0]['vendor_id'])){
				$sql_insere_vendor_user = "insert into vendor_favoritos_user (vendor_id,user_id) values('".$dados_v[$cont]['vendor_id']."','".$user_id."')";
				executaQuery($sql_insere_vendor_user);
			}*/
			
			$cont++;
		}
		if($where != ''){
			$where .= ')';
		}else{
			$trouxe_algo = '-1';	
		}
		
		$sql   = "select product_id, title, sale_price, discount, added_by, current_stock
				  from product
				  where status='ok' $where
				  order by title ";
		$dados = recuperaDados($sql);
	}
}

if($trouxe_algo == '-1'){
	die('');	
}

$cont = 0;
$dados_produto = '';
while($dados[$cont]['product_id']){
	$dados_produto .= $dados[$cont]['product_id'].'|';
	$dados_produto .= utf8_encode($dados[$cont]['title']).'|';
	$dados_produto .= $dados[$cont]['sale_price'].'|';
	$dados_produto .= $dados[$cont]['discount'].'|';
	
	$dados_added_by = json_decode($dados[$cont]['added_by']);
	$array_added_by = transformaObjetoJsonEmArray($dados_added_by);
	$id_vendor      = $array_added_by['id'];
	$sql_vendor     = "select name
				   	   from vendor
				       where vendor_id=".$id_vendor;
	$dados_vendor   = recuperaDados($sql_vendor);
	
	$dados_produto .= utf8_encode($dados_vendor[0]['name']).'|';
	$dados_produto .= $dados[$cont]['current_stock'].'{S}';
	
	$cont++;
}

$dados_produto = substr($dados_produto,0,-3);

die($dados_produto);
?>