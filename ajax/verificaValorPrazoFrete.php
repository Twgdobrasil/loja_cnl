<?php 
	/*
	Tipos: 
		40010 => Sedex
		81019 => eSedex
		40045 => Sedex a Cobrar
		41106 => PAC
	*/
	function verificaValorPrazoFrete($cep_de, $cep_para, $valor, $tipo, $peso, $comprimento = 20,$largura = 20, $altura = 20){
		$valor 			= str_replace('.',',',$valor);
		$codigo_empresa = '09119132';
		$senha_empresa	= '08677327';
		
		$correios = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?"
		."nCdEmpresa=".$codigo_empresa."&"
		."sDsSenha=".$senha_empresa."&"
		."sCepOrigem=".$cep_de."&"
		."sCepDestino=".$cep_para."&"
		."nVlPeso=".$peso."&"
		."nCdFormato=1&"
		."nVlComprimento=".$comprimento."&"
		."nVlAltura=".$altura."&"
		."nVlLargura=".$largura."&"
		."sCdMaoPropria=N&"
		."nVlValorDeclarado=".$valor."&"
		."sCdAvisoRecebimento=N&"
		."nCdServico=".$tipo."&"
		."nVlDiametro=0&"
		."StrRetorno=xml";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $correios);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 2);
		$html1 = curl_exec($ch);
		curl_close($ch);
		
		//Pega o valor
		$html  = explode('<Valor>', $html1);
		$html2 = explode('</Valor>', $html[1]);
		$total = str_replace(',','.',$html2[0]);
		
		//Pega o prazo
		$pra   = explode('<PrazoEntrega>', $html1);
		$prazo = explode('</PrazoEntrega>', $pra[1]);

		return array('valor'=>$total,'prazo'=>$prazo[0]);
	}

?>