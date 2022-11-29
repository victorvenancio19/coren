<?php
//FAZENDO CHAMADAS AO SERVIÇO BUSCAR PROCESSADOS

// URL DO DETRAN BUSCAR PROCESSADOS
$soapUrlProcessados = 'http://10.200.96.244:8181/Servico.asmx?op=BuscarProcessados';


$mensagemErro = '236210Cidadão não encontrado';


for($i = 0; $i < count($dados); $i++) {

//DEFININDO O XML DA REQUISIÇÃO
$xml_post_string_resposta = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <BuscarProcessados xmlns="http://www.detran.rj.gov.br">
      <CNPJ>'.$cnpj.'</CNPJ>
      <chave>'.$chave.'</chave>
      <perfil>'.$perfil.'</perfil>
      <IDCidadao>'.$dados[$i]["NUMERO COREN"].'</IDCidadao>
    </BuscarProcessados>
  </soap12:Body>
</soap12:Envelope>';



//DEFINIÇÃO DOS HEADERS 
$headers = array(
  "POST /Servico.asmx HTTP/1.1",
  "Host: 10.200.96.244",
  "Content-Type: application/soap+xml; charset=utf-8",
  "Content-Length:".strlen($xml_post_string_resposta)
); 

$url = $soapUrlProcessados;



// CONFIGURANDO O  CurlOPT 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string_resposta);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch); 
curl_close($ch);


//$xml = new SimpleXMLElement();



print_r($response);




}









