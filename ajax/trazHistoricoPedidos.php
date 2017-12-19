<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

//Traz os dados dos pedidos do usuario
$sql   = "select *
		  from sale
		  where buyer='".$user_id."'
		  order by sale_id desc";
$dados = recuperaDados($sql);

//Tem que vir assim: NumPedido{T}Data{T}IdProd|NomeProd|precoProd|QtdProd|TotalProd{P}
//                   IdProd2|NomeProd2|precoProd2|QtdProd2|TotalProd2
//                   {S}

//id_produto,qtd,preco,nome_produto,frete,subtotal 
$cont = 0;
$pedidos = '';
while($dados[$cont]['sale_id']){
    $cod_pedido        = $dados[$cont]['sale_code'];
	$data_pedido       = formataData($dados[$cont]['data_venda']);
	$detalhes_produtos = $dados[$cont]['product_details'];
	
	//PEGAR DETALHES DOS PRODUTOS do PEDIDO
	$dados_produtos  = trazDadosProduto(utf8_encode($detalhes_produtos)); 
	
	$pedidos        .= $cod_pedido.'{T}'.$data_pedido.'{T}'.$dados_produtos.'{S}';
	
	$cont++;
}
	
$dados_pedidos = substr($pedidos,0,-3);

die($dados_pedidos);

function formataData($data){
	//2016-12-19 09:00:47
	$new_data = substr($data,8,2).'/'.substr($data,5,2).'/'.substr($data,0,4).' '.substr($data,11,2).':'.substr($data,14,2).':'.substr($data,17,2);
	return $new_data;	
}

function trazDadosProduto($obj_json){
	//HISTORICO DE PEDIDOS
    //Leitura de mais de um produto para a tela de Historico de pedidos
    //$obj_json eh o valor do jeito q veio do banco (product_details)
    $dados_principais = '';
    $opcoes           = '';
	$a = json_decode($obj_json);
	$produto_id = '';
	if(!empty($a)){
		foreach ($a as $key => $value) {
			foreach ($value as $key2 => $value2) {
				//Pega todas as informações que não são OPTIONS a mais
				if($key2 != 'option'){
					if($key2 != 'rowid' && $key2 != 'image' &&  $key2 != 'tax' && $key2 != 'coupon'){
						$dados_principais .= $value2.'|';
						if($key2 == 'id'){
							$produto_id = $value2;	
						}
					}
				}else{
					//Aqui pega os campos de options
					/*$b = json_decode($value2);
					foreach ($b as $key3 => $value3) {
						foreach ($value3 as $key4 => $value4) {
							//echo $key4.' => '.$value4.'<BR>';
							if($key4 == 'title'){
								$opcoes .= $value4.'{S}';
							}else{
								$opcoes .= $value4.'|';
							}
						}
					}
					$opcoes = substr($opcoes,0,-1);
					$opcoes .= '{P}';*/
				}
			}
			//Acrescenta a avaliação do produto
			$sql_p   = "select rating_num, rating_total
					    from product
					    where product_id=".$produto_id;
			$dados_p = recuperaDados($sql_p);
			if(!empty($dados_p[0]['rating_num']) && $dados_p[0]['rating_num'] != '' && $dados_p[0]['rating_num'] > 0){
				$avaliacao = ceil($dados_p[0]['rating_total']/$dados_p[0]['rating_num']);
			}else{
				$avaliacao = 0;	
			}
			$dados_principais .= $avaliacao;
			$dados_principais .= '{P}';
		}
	}

    //id_produto,qtd,preco,nome_produto,frete,subtotal 
    //infos do produto separado por |
    //cada produto separado por {P}
    //echo $dados_principais = substr($dados_principais,0,-3).'<BR>';

    //nome, valor 
    //Separado por nome {S}
    //opcoes do produto separado por |
    //cada produto separado por {P}
    //echo $opcoes = substr($opcoes,0,-3);

    //id_produto,qtd,preco,nome_produto,frete,subtotal
    return substr($dados_principais,0,-3);
}

?>