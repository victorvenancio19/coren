<?php

set_time_limit(5000);
// URL DO DETRAN 
$soapUrl = 'http://10.200.96.244:8080/Servico.asmx?op=consultarCPF';

// URL DO DETRAN BUSCAR PROCESSADOS
$soapUrlProcessados = 'http://10.200.96.244:8181/Servico.asmx?op=BuscarProcessados';

$cnpj = 27149095000166;
$chave = 'tX8a9a0mFYG46o09';
$perfil = 60;
//$idCidadao = 574379;
$rg;
$idPesquisador = 1;
$cpf; 
$nomePai;
$nomeMae;
$dataNascimento;
$nomeCidadao;



$url = $soapUrl;
$urlProcessados = $soapUrlProcessados;


//date_default_timezone_set('America/Sao_Paulo');
// CRIA UMA VARIAVEL E ARMAZENA A HORA ATUAL DO FUSO-HORÀRIO DEFINIDO (BRASÍLIA)
    //$horaAtual = date('H:i:s', time());
  //  $horaLimite = date('16:19:30');





//FAZENDO A LEITURA DO ARQUIVO CSV E CRIANDO UM ARRAY MUNTIDIMENSIONAL
$handle = fopen("file.csv", "r");

$header = fgetcsv($handle, 200, ";");

while ($row = fgetcsv($handle, 200, ";")) {
    $dados[] = array_combine($header, $row);
}






// FUNÇÃO CONSULTAR RG
function consultarRG($cnpj, $chave, $perfil, $url, $dados, $idPesquisador, $urlProcessados) {

  //LENDO O ARRAY MUNTIDIMENSIONAL COM OS DADOS DO CIDADÃO
  
  for($i = 0; $i < count($dados); $i++) {
    //VARIÁVEL COM O XML PARA A CHAMADA CONSULTAR NOME
    $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
    <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
      <soap12:Body>
        <consultarRG xmlns="http://www.detran.rj.gov.br">
          <CNPJ>'.$cnpj.'</CNPJ>
          <chave>'.$chave.'</chave>
          <perfil>'.$perfil.'</perfil>
          <IDCidadao>'.$dados[$i]["NUMERO COREN"].'</IDCidadao>
          <RG>'.$dados[$i]["RG"].'</RG>
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
    
   
     





     //CHAMA A FUNÇÃO BUSCAR PROCESSADOS
  $msg = buscarProcessados($cnpj, $chave, $perfil, $urlProcessados, $dados[$i]["NUMERO COREN"]);




  $msgNaoEncontrado = 'Cidadão não encontrado';
  $msgNaoExisteSolicitacao = 'Não existe solicitação de Processamento em aberto para este ID.';


 
//VERIFICA SE ENCONTROU O CIDADÃO POR RG, SALVA NO ARQUIVO CSV
if($msg["MsgRetorno"] != $msgNaoEncontrado && $msg["MsgRetorno"] != $msgNaoExisteSolicitacao ) {
  
  echo "ENTROU NA CONDIÇÃO";
  $arquivo = fopen('consultaNome3.csv', 'a+');

 
  fputcsv($arquivo, $msg);
  
  
  fclose($arquivo);

}




//CONDIÇÃO QUE VERIFICA SE O CIDADAO NÃO FOI ENCONTRADO VIA CONSULTAR RG E CHAMA A FUNÇAO CONSULTAR POR CPF
  if( $msg["MsgRetorno"] == $msgNaoEncontrado || $msg["MsgRetorno"] == $msgNaoExisteSolicitacao ) {
    
    echo "ENTROU NO IF CPF";
     consultarCPF($cnpj, $chave, $perfil, $idPesquisador, $url, $dados[$i]["NUMERO COREN"], $dados[$i]["CPF"]). "<br>";
     $msgCPF = buscarProcessados($cnpj, $chave, $perfil, $urlProcessados, $dados[$i]["NUMERO COREN"]);

   
      // SE ENCONTROU O CIDADÃO VIA CPF, SALVA NO ARQUIVO CSV
     if($msgCPF["MsgRetorno"] != $msgNaoEncontrado && $msgCPF["MsgRetorno"] != $msgNaoExisteSolicitacao ) {
      $arquivo = fopen('consultaNome3.csv', 'a+');
    
     
      fputcsv($arquivo, $msgCPF);
      
      
      fclose($arquivo);
    
    }

  
  //CONDIÇÃO QUE VERIFICA SE O CIDADAO NÃO FOI ENCONTRADO VIA CONSULTAR CPF E CHAMA A FUNÇAO CONSULTAR POR NOME
  if($msgCPF["MsgRetorno"] == $msgNaoEncontrado || $msgCPF["MsgRetorno"] == $msgNaoExisteSolicitacao ) {
    echo "Entrou no IF NOME" . "<br>";
    consultarNome($cnpj, $chave, $perfil, $url, $dados[$i]["NUMERO COREN"], $dados[$i]["NOME"], $dados[$i]["NOME DO PAI"], 
    $dados[$i]["NOME DA MÃE"], $dados[$i]["DATA DE NASCIMENTO"]);
    $msgNome = buscarProcessados($cnpj, $chave, $perfil, $urlProcessados, $dados[$i]["NUMERO COREN"]);
  

    // SE ENCONTROU O CIDADÃO VIA NOME, SALVA NO ARQUIVO CSV
    if($msgNome["MsgRetorno"] != $msgNaoEncontrado && $msgNome["MsgRetorno"] != $msgNaoExisteSolicitacao ) {
      $arquivo = fopen('consultaNome3.csv', 'a+');
    
     
      fputcsv($arquivo, $msgNome);
      
      
      fclose($arquivo);
    
    }

    // SE CASO O CIDADÃO NAO FOR ENCONTRADO POR NENHUMA FUNÇÃO
  if( $msgNome["MsgRetorno"] == $msgNaoEncontrado || $msgNome["MsgRetorno"] == $msgNaoExisteSolicitacao  ) {
    echo "CIDADÃO NÃO ENCONTRADO NA BASE DO DETRAN". "<br>";

    $arquivo = fopen('consultaNome3.csv', 'a+');

 
    fputcsv($arquivo, $msgNome);
    
    
    fclose($arquivo);

  }

  }



    }


  }
}



// FUNÇÃO CONSULTAR CPF
function consultarCPF($cnpj, $chave, $perfil, $idPesquisador, $url, $idCidadao, $cpf) {

  
  
  
    //VARIÁVEL COM O XML PARA A CHAMADA CONSULTAR NOME
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
    //fclose($handle);
    
   
  
  
    
    return $response;
  
  
  }




// FUNÇÃO CONSULTAR NOME
function consultarNome($cnpj, $chave, $perfil, $url, $idCidadao, $nomeCidadao, $nomePai, $nomeMae, $dtNascimento) {

//LENDO O ARRAY MUNTIDIMENSIONAL COM OS DADOS DO CIDADÃO


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
        <dtNascimento>'.$dtNascimento.'</dtNascimento>
        <IDPesquisador>1</IDPesquisador>
      </consultarNome>
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
  
 
  
  return $response;
  
  
  
}







//FAZENDO CHAMADAS AO SERVIÇO BUSCAR PROCESSADOS




function buscarProcessados($cnpj, $chave, $perfil, $urlProcessados, $idCidadao) {

//for($i = 0; $i < count($dados); $i++) {

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


//echo $response;
echo "<br><br>";

// CONVERTENDO
$response1 = str_replace("<soap:Body>","",$response);
$response2 = str_replace("</soap:Body>","",$response1);


// CONVERTENDO PARA XML
$parser = simplexml_load_string($response2);
// user $parser to get your data out of XML response and to display it. 

$msg = $parser->BuscarProcessadosResponse->BuscarProcessadosResult->dadosCivil->MsgRetorno;

//echo $msg;

$arr = (array) $parser->BuscarProcessadosResponse->BuscarProcessadosResult->dadosCivil;


//var_dump($parser);

// RESGATA A MENSAGEM DE RETORNO

print_r($arr["MsgRetorno"]);




//$headers = ['Base', 'idCidadao','RG','dtexpedicao','nocidadao', 'nopaicidadao', 'nomaecidadao', 'dtnascimento', 
//'cpf', 'comunicipiocertidao', 'nomunicipiocertidao', 'possuiobito', 'idcorpo', 'coretorno', 'msgretorno'];



//$arquivo = fopen('consultaNome2.csv', 'a+');

// Criar o cabeçalho
//fputcsv($arquivo , $headers);



//fputcsv($arquivo, $arr);


//fclose($arquivo);



//}

return $arr;

}
  



//echo(consultarNome($cnpj, $chave, $perfil, $url, $handle, $dados) . "<br>") ;
//echo(consultarRG($cnpj, $chave, $perfil, $idPesquisador, $url, $dados, $urlProcessados) . "<br>") ;
//consultarNome($cnpj, $chave, $perfil, $url, $dados, $idPesquisador, $urlProcessados) . "<br>";
consultarRG($cnpj, $chave, $perfil, $url, $dados, $idPesquisador, $urlProcessados);
//CHAMA E EXIBE O RESULTADO DA FUNÇÃO BUSCAR PROCESSADOS
//buscarProcessados($cnpj, $chave, $perfil, $urlProcessados, $dados);

//consultarRG($cnpj, $chave, $perfil, $idPesquisador, $url, 143271, 11729918571001);












// CONSULTAR CPF NÃO RETORNA DADOS
/*
$consultarCPF = '<?xml version="1.0" encoding="utf-8"?>
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
</soap12:Envelope>';*/


// CONSULTAR NOME RETORNA DADOS
/* 
$consultarNome = '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <consultarNome xmlns="http://www.detran.rj.gov.br">
      <CNPJ>'.$cnpj.'</CNPJ>
      <chave>'.$chave.'</chave>
      <perfil>'.$perfil.'</perfil>
      <IDCidadao>'.$idCidadao.'</IDCidadao>
      <nomeCidadao>VICTOR ALEXANDRE DA SILVA</nomeCidadao>
      <nomePai>LUIZ CLAUDIO BATISTA DA SILVA</nomePai>
      <nomeMae>JOSEFA RITA ALEXANDRE</nomeMae>
      <dtNascimento>22/08/1995</dtNascimento>
      <IDPesquisador>1</IDPesquisador>
    </consultarNome>
  </soap12:Body>
</soap12:Envelope>'*/


// VALIDAR EXISTÊNCIA RG NÃO RETORNA DADOS
/*
$validarExistenciaRG =
 '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <validarExistenciaRG xmlns="http://www.detran.rj.gov.br">
      <CNPJ>'.$cnpj.'</CNPJ>
      <Chave>'.$chave.'</Chave>
      <RG>'.$rg.'</RG>
    </validarExistenciaRG>
  </soap12:Body>
</soap12:Envelope>';*/

//CONSULTAR RG RETORNA DADOS
/*
$consultarRg = '<?xml version="1.0" encoding="utf-8"?>
'<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
  <soap12:Body>
    <consultarRG xmlns="http://www.detran.rj.gov.br">
      <CNPJ>'.$cnpj.'</CNPJ>
      <chave>'.$chave.'</chave>
      <perfil>60</perfil>
      <IDCidadao>'.$idCidadao.'</IDCidadao>
      <RG>'.$rg.'</RG>
      <IDPesquisador>'.$idPesquisador.'</IDPesquisador>
    </consultarRG>
  </soap12:Body>
</soap12:Envelope>';*/










