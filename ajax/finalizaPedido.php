<?php 
session_start();
require('../db/funcoes.php');

extract($_POST);

$ids_produto    = explode(',',$produtos);
$nomes_produto  = explode('|',$nomes);
$qtds_produto   = explode('|',$qtds);
$precos_produto = explode('|',$precos);
$fretes_produto = explode('|',$fretes);
$cores          = explode('|',$cores);
$opcoes_campo   = explode('{S}',$opcoes_c);
$opcoes_label   = explode('{S}',$opcoes_l);
$opcoes_valor   = explode('{S}',$opcoes_v);
$vendors_p      = explode('{S}',$vendors);

//Atualiza a tabela de creditos do vendor...
for($v=0;$v<count($vendors_p);$v++){
	//Pega o plano de credito do usuario que ainda está ativo (data_expiracao e pagamento e qtd de vendas feita maior que a qtd max do plano)
	$sql_plano   = "select pcv.plano_creditos_vendor_id, pc.valor_por_venda
	                from plano_creditos_vendor pcv, planos_creditos pc
			where pcv.data_expira_plano>now() and pcv.status='1' and qtd_vendas_feitas<qtd_max_plano and pcv.plano_creditos_id=pc.plano_creditos_id";
	$dados_plano = recuperaDados($sql_plano);
	
	//Se existe algum plano ativo
	if(!empty($dados_plano[0]['plano_creditos_vendor_id'])){
		$valor_produto = $precos_produto[$v];
		$sql_plano = "update plano_creditos_vendor 
			      set qtd_vendas_feitas=qtd_vendas_feitas+1,
				  total_recebido_vendor=total_recebido_vendor+".$valor_produto." ,
				  total_recebido_admin=total_recebido_admin+".$dados_plano[0]['valor_por_venda']."
			      where plano_creditos_vendor_id='".$dados_plano[0]['plano_creditos_vendor_id']."'";
		executaQuery($sql_plano);		
	}	
}

//Pegar dados de entrega pelo id do endereço e usuario
//Shipping address
//Dados do usuario
$sql_dados_user = "select * 
                   from user 
                   where user_id='".$user_id."'";
$dados_user     = recuperaDados($sql_dados_user);

// Pegar os dados correto de entrega... do endereco_id escolhido
$sql_dados_endereco = "select * 
                       from enderecos_entrega 
                       where enderecos_entrega_id='".$endereco_id."'";
$dados_endereco     = recuperaDados($sql_dados_endereco);

$dados_endereco_entrega = '{"firstname":"'.$dados_user[0]['username'].'","lastname":"'.$dados_user[0]['surname'].'","address1":"'.$dados_endereco[0]['endereco'].'","address2":"","zip":"'.$dados_endereco[0]['cep'].'","email":"'.$dados_user[0]['email'].'","phone":"'.$dados_user[0]['phone'].'","langlat":"('.$dados_endereco[0]['latitude'].','.$dados_endereco[0]['longitude'].')","payment_type":"'.$tipo_pagamento.'"}';

$cod_venda = substr(str_shuffle('0123456789'),0,10);

//Cria o registro na tabela de venda (sale)

//Preenche o status de entrega e o status da venda
$vendors_produto = array_unique($vendors_p);
$vendors_produto = array_values($vendors_produto);

$pagamento     = array();
$entrega       = array();
$array_vendors = array(); 
for($k=0;$k<count($vendors_produto);$k++){
    $pagamento_por_vendor = array("vendor"=>$vendors_produto[$k],"status"=>"Em aberto");
    array_push($pagamento, $pagamento_por_vendor);
    
    $entrega_por_vendor = array("vendor"=>$vendors_produto[$k],"status"=>"Pendente","delivery_time"=>'');
    array_push($entrega, $entrega_por_vendor);

    $sql_vendor        = "select email 
                          from vendor
                          where vendor_id='".$vendors_produto[$k]."'";
    $dados_vendor      = recuperaDados($sql_vendor);
    $array_vendors[$k] = $dados_vendor[0]['email'];

}
$pagamento_status = json_encode($pagamento);
$entrega_status   = json_encode($entrega);

$datetime = mktime(0,0,0,date('m'),date('d'),date('Y'));

$dados_compra_email = ' <tr>
                            <td colspan="2" style="padding:0px;">
                                <table width="100%">
                                    <thead>
                                        <tr>
                                            <th style="padding: 5px;background:rgba(128, 128, 128, 0.30)">ID</th>
                                            <th style="padding: 5px;background:rgba(128, 128, 128, 0.30)">Item</th>
                                            <th style="padding: 5px;background:rgba(128, 128, 128, 0.30)">Quantidade</th>
                                            <th style="padding: 5px;background:rgba(128, 128, 128, 0.30)">Custo Unitário</th>
                                            <th style="padding: 5px;background:rgba(128, 128, 128, 0.30)">Total</th>
                                        </tr>
                                    </thead>'; 

$total              = 0;
$total_frete        = 0;
for($i=0; $i<count($ids_produto); $i++){
    //Cria o array de opções dos produtos
    unset($array_options);
    $opcs_campo = explode(';',$opcoes_campo[$i]);
    $opcs_label = explode('|',$opcoes_label[$i]);
    $opcs_valor = explode(';',$opcoes_valor[$i]);
    for($j=0; $j<count($opcs_label); $j++){
        $array_options[$opcs_campo[$j]]['title'] = $opcs_label[$j];
		$opcs_valor[$j] = str_replace(',,',',',$opcs_valor[$j]);
		if($opcs_valor[$j][0] == ','){
			$opcs_valor[$j] = substr($opcs_valor[$j],1,strlen($opcs_valor[$j])-1); 
		}
		if($opcs_valor[$j][strlen($opcs_valor[$j])-1] == ','){
			$opcs_valor[$j] = substr($opcs_valor[$j],0,-1);
		}
        $array_options[$opcs_campo[$j]]['value'] = $opcs_valor[$j];
    }
    //Acrescenta a cor nas opções
	if(!empty($cores[$i])){
		$array_options['color']['title'] = 'Color';
		$array_options['color']['value'] = $cores[$i];
	}else{
		$array_options['color']['title'] = 'Color';
		$array_options['color']['value'] = '';
	}
	
    $subtotal    = $precos_produto[$i]*$qtds_produto[$i];
    $total       += $subtotal;
    $total_frete += $fretes_produto[$i];

    //Cria o array principal de venda (sale)
    $rowid  = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvxz'),0,32);
    $sale[$rowid]['id']       = $ids_produto[$i];
    $sale[$rowid]['qty']      = $qtds_produto[$i];
	//addslashes coloca as barras invertidas no json
    $sale[$rowid]['option']   = addslashes(json_encode($array_options));
    $sale[$rowid]['price']    = $precos_produto[$i];
    $sale[$rowid]['name']     = $nomes_produto[$i];
    $sale[$rowid]['shipping'] = $fretes_produto[$i]; 
    $sale[$rowid]['tax']      = '';
    $sale[$rowid]['image']    = '';
    $sale[$rowid]['coupon']   = '';
    $sale[$rowid]['rowid']    = $rowid;
    $sale[$rowid]['subtotal'] = $subtotal;

    $dados_compra_email .= '<tbody>
                                <tr>
                                    <td style="padding: 5px;text-align:center;background:rgba(128, 128, 128, 0.18)">'.$ids_produto[$i].'</td>
                                    <td style="padding: 5px;text-align:center;background:rgba(128, 128, 128, 0.18)">'.$nomes_produto[$i].'</td>
                                    <td style="padding: 5px;text-align:center;background:rgba(128, 128, 128, 0.18)">'.$qtds_produto[$i].'</td>
                                    <td style="padding: 5px;text-align:center;background:rgba(128, 128, 128, 0.18)">'.number_format($precos_produto[$i],2,',','.').'</td>
                                    <td style="padding: 5px;text-align:right;background:rgba(128, 128, 128, 0.18)">'.number_format($subtotal,2,',','.').'</td>
                                </tr>
                            </tbody>';
}
$detalhes_produtos = json_encode($sale,JSON_UNESCAPED_UNICODE);

$dados_compra_email .= '        </table>
                            <td>
                        </tr>';

$total_total = $total+$total_frete;

$sql  = "insert into sale (buyer, product_details, shipping_address, shipping, payment_type, grand_total, sale_code, data_venda, troco, payment_status, delivery_status, sale_datetime) values
						  ('".$user_id."','".utf8_decode($detalhes_produtos)."','".$dados_endereco_entrega."','".$total_frete."','".$tipo_pagamento."','".$valor_total."','".$cod_venda."',now(),'".$troco."','".$pagamento_status."','".$entrega_status."','".$datetime."')";	

//DAQUI PRA BAIXO ESTÁ OK JÁ
if(executaQuery($sql) == '1'){
	$venda_id = mysql_insert_id();
	
	//Pegar os dados dos produtos comprados e quebrar em arrays
	$produtos_id    = explode(',',$produtos);
	$qtds_produto   = explode('|',$qtds);
	for($i=0;$i<count($produtos_id);$i++){
		$produto_id = $produtos_id[$i];
		$qtd        = $qtds_produto[$i];
		
		//Diminui a qtd do produto no current_stock na tabela product de acordo com a qtd do carrinho...
		$sql_u = "update product 
				  set current_stock=current_stock-$qtd
				  where product_id='".$produto_id."'";
		executaQuery($sql_u);
	}
	
	$msg_html ='<div style="padding:10px;background:rgba(212, 224, 212, 0.72)">
                    <center>
                        <h1 class="text-center;">Fatura do Pedido</h1>
                    </center>
                </div>
                <table width="100%" style="background:rgba(212, 224, 212, 0.17);">
                    <tr>
                        <td style="padding:10px;">
                            <img src="http://www.clicknalupa.com.br/loja/uploads/logo_image/logo_84.png" alt="" width="60%">
                        </td>
                        <td>
                            <table>
                                <tr><td><strong>Número do Pedido</strong> : '.$cod_venda.'</td></tr>
                                <tr><td><strong>Data</strong> : '.date("d M, Y",$datetime).'</td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px;">
                            <div class="tag-box tag-box-v3">
                                <h2>Informações do Cliente</h2>
                                <table>
                                    <tr><td><strong>Nome</strong>'.$dados_user[0]['username'].'</td></tr>
                                    <tr><td><strong>Sobrenome</strong>'.$dados_user[0]['surname'].'</td></tr>
                                </table>
                            </div>        
                        </td>
                        <td>
                            <div class="tag-box tag-box-v3">
                                <h2>Detalhes Pagamento</h2>  
                                <table>       
                                    <tr><td><strong>Status do Pagamento</strong> <i>Em aberto</i></td></tr>
                                    <tr><td><strong>Método do Pagamento</strong> '.$tipo_pagamento.'</td></tr>  
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:10px 5px 0px; background:purple; color:white; text-align:center;" colspan="2" >
                            <h3>Fatura de Pagamento</h3>
                        </td>
                    </tr>
                    '.$dados_compra_email.'
                    <tr>
                        <td width="50%" style="background:rgba(212, 224, 212, 0.72)">
                             <table>
                                <tr >
                                    <td style="padding:10px 20px;"><h2>Endereço</h2></td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 20px;">
                                        '.utf8_encode($dados_endereco[0]['endereco']).'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 20px;">
                                        Cidade : '.utf8_encode($dados_endereco[0]['cidade']).'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 20px;">
                                        CEP : '.$dados_endereco[0]['cep'].'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 20px;">
                                        Telefone : '.$dados_user[0]['phone'].'
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 20px;">
                                        E-mail : '.$dados_user[0]['email'].'
                                    </td>    
                                </tr> 
                             </table> 
                        </td>
                        <td style="text-align:right;">
                             <table width="100%">
                                <tr>
                                    <td style="text-align:right;padding:3px; width:80%; ">Subtotal :</h3></td>
                                    <td style="text-align:right;padding:3px"><h3>'.number_format($total,2,',','.').'</h3></td>
                                </tr>
                                <tr>
                                    <td style="text-align:right;padding:3px; width:80%;"><h3>Imposto :</h3></td>
                                    <td style="text-align:right;padding:3px"><h3>0,00</h3></td>
                                </tr>
                                <tr>
                                    <td style="text-align:right;padding:3px; width:80%;"><h3>Frete :</h3></td>
                                    <td style="text-align:right;padding:3px"><h3>'.number_format($total_frete,2,',','.').'</h3></td>
                                </tr>
                                <tr>
                                    <td style="text-align:right;padding:3px; width:80%;"><h2>Total :</h2></td>
                                    <td style="text-align:right;padding:3px"><h2>'.number_format($total_total,2,',','.').'</h2></td>
                                </tr>
                             </table>
                        </td>
                    </tr>
                </table>
                <h4>
                    ** You can download purchased (fully paid) digital products form your profile.
                </h4>';

    //Envia e-mail ao comprador
	enviaEmail($dados_user[0]['email'],'CNLL - Pedido '.$cod_venda,$msg_html);

    //Envia e-mail aos vendors
    for($p=0;$p<count($array_vendors);$p++){
		//Envia e-mail para avisar o vendor que uma nova venda foi registrada
		enviaEmail($array_vendors[$p],'CNLL - Você tem uma nova venda','Você tem uma nova venda efetuada pelo sistema CNLL. <BR>Acesse o site para gerenciá-la.<BR><BR> Att,<BR>Equipe CNLL.');
	}
	
	die($cod_venda);
}else{
	die('-1');	
}

function enviaEmail($email_envio,$assunto,$msg_html){
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: Nome da Pessoa que Envia<adm@clicknalupa.com.br>' . "\r\n";
	$headers .= 'Reply-To: Dunha<adm@clicknalupa.com.br>' . "\r\n";
	$headers .= 'Return-Path: Dunha<adm@clicknalupa.com.br>' . "\r\n";
	$headers .= "X-Priority: 3\r\n";
	$headers .= "X-Mailer: PHP" . phpversion() . "\r\n";
	$headers .= "Organization: Dunha\r\n";

	@mail($email_envio, $assunto, $msg_html, $headers, "-f " . 'adm@clicknalupa.com.br');
}
?>