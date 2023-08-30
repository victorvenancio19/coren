<?php

// URL DO DETRAN 
$soapUrl = 'http://10.200.96.170:8080/Servico.asmx?op=consultarCPF';

// URL DO DETRAN BUSCAR PROCESSADOS
$soapUrlProcessados = 'http://10.200.96.170:8181/Servico.asmx?op=BuscarProcessados';

$cnpj = 27149095000166;
$chave = 'LvfjPtEjR892sET1';
$perfil = 60;
$idCidadao = 200650;
$rg = 284150026;
$idPesquisador = 1;
$cpf = 15376746763; 
$nomePai ='ABILIO ALMEIDA';
$nomeMae='ELZA PEREIRA DE ALMEIDA';
$dataNascimento='NÃO IDÊNTIFICADO';
$nomeCidadao='ABIGAIL ALMEIDA PEREIRA';



$url = $soapUrl;
$urlProcessados = $soapUrlProcessados;




$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <consultarRG xmlns="http://www.detran.rj.gov.br">
      <CNPJ>'.$cnpj.'</CNPJ>
      <chave>'.$chave.'</chave>
      <perfil>'.$perfil.'</perfil>
      <IDCidadao>'.$idCidadao.'</IDCidadao>
      <RG>'.$rg.'</RG>
      <IDPesquisador>'.$idPesquisador.'</IDPesquisador>
    </consultarRG>
  </soap12:Body>
</soap12:Envelope>';
  
  
  
  //DEFINIÇÃO DOS HEADERS 
  $headers = array(
    "POST /Servico.asmx HTTP/1.1",
    "Host: 10.200.96.244",
    "Content-Type: application/soap+xml; charset=utf-8",
    "Content-Length:".strlen($xml_post_string)
  ); 
  
  
  // CONFIGURANDO O  CurlOPT 
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
  $response = curl_exec($ch); 
  curl_close($ch);
  
  echo $response;
  echo "<br><br>";



/*
 //VARIÁVEL COM O XML PARA A CHAMADA CONSULTAR CPF
 $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
 <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
   <soap12:Body>
   <consultarCPF xmlns="http://www.detran.rj.gov.br">
       <CNPJ>'.$cnpj.'</CNPJ>
       <chave>'.$chave.'</chave>
       <perfil>'.$perfil.'</perfil>
       <IDCidadao>'.$idCidadao.'</IDCidadao>
       <CPF>'.$cpf.'</CPF>
       <IDPesquisador>'.$idPesquisador.'</IDPesquisador>
     </consultarCPF >
   </soap12:Body>
 </soap12:Envelope>';

/*
//VARIÁVEL COM O XML PARA A CHAMADA CONSULTAR RG
$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <consultarRG xmlns="http://www.detran.rj.gov.br">
      <CNPJ>'.$cnpj.'</CNPJ>
      <chave>'.$chave.'</chave>
      <perfil>'.$perfil.'</perfil>
      <IDCidadao>'.$idCidadao.'</IDCidadao>
      <RG>'.$rg.'</RG>
      <IDPesquisador>'.$idPesquisador.'</IDPesquisador>
    </consultarRG>
  </soap12:Body>
</soap12:Envelope>';*/

/*
  //VARIÁVEL COM O XML PARA A CHAMADA CONSULTAR NOME
   $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
  <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
    <soap12:Body>
      <consultarNome xmlns="http://www.detran.rj.gov.br">
        <CNPJ>'.$cnpj.'</CNPJ>
        <chave>'.$chave.'</chave>
        <perfil>'.$perfil.'</perfil>
        <IDCidadao>'.$idCidadao.'</IDCidadao>
        <nomeCidadao>'.$nomeCidadao.'</nomeCidadao>
        <nomePai>'.$nomePai.'</nomePai>
        <nomeMae>'.$nomeMae.'</nomeMae>
        <dtNascimento>'.$dataNascimento.'</dtNascimento>
        <IDPesquisador>1</IDPesquisador>
      </consultarNome>
    </soap12:Body>
  </soap12:Envelope>';*/




//buscar processados


  //DEFININDO O XML DA REQUISIÇÃO
$xml_post_string_resposta = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <BuscarProcessados xmlns="http://www.detran.rj.gov.br">
      <CNPJ>'.$cnpj.'</CNPJ>
      <chave>'.$chave.'</chave>
      <perfil>'.$perfil.'</perfil>
      <IDCidadao>'.$idCidadao.'</IDCidadao>
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






// CONFIGURANDO O  CurlOPT 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlProcessados);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string_resposta);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch); 
curl_close($ch);


echo $response;
echo "<br><br>";