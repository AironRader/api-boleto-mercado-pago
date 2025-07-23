<?php include('apiConfig.php'); ?>
<?php include('../conexao.php');?>
<?php

$id = $_GET['id'];

$query = $pdo->query("SELECT * from contas_receber WHERE id = $id ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$valor = @$res[0]['valor'];
$cliente = @$res[0]['cliente'];

$query = $pdo->query("SELECT nome, cpf, email, cep, logradouro, cidade, numero from clientes wHERE id = $cliente");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$clienteNome = @$res[0]['nome'];
$clienteCPF = @$res[0]['cpf'];
$clienteEmail = @$res[0]['email'];
$clienteCep = @$res[0]['cep'];
 $clienteLogradouro = @$res[0]['logradouro'];
 $clienteCidade = @$res[0]['cidade'];
 $clienteNumero = @$res[0]['numero'];

 //var_dump($clienteCPF, $clienteEmail);
//  exit();

//var_dump($clienteEmail);
// exit();

$curl = curl_init();

$dados["external_reference"]                    = '1';
$dados["transaction_amount"]                    = (float)$valor;
$dados["description"]                           = "Título do produto";
$dados["payment_method_id"]                     = "bolbradesco";
$dados["notification_url"]                      = "https://google.com";
$dados["payer"]["email"]                        = $clienteEmail;
$dados["payer"]["first_name"]                   = $clienteNome;
$dados["payer"]["last_name"]                    = $clienteNome;

$dados["payer"]["identification"]["type"]       = "CPF";
$dados["payer"]["identification"]["number"]     = $clienteCPF;

$dados["payer"]["address"]["zip_code"]          = $clienteCep;
$dados["payer"]["address"]["street_name"]       = $clienteLogradouro;
$dados["payer"]["address"]["street_number"]     =  $clienteNumero;
$dados["payer"]["address"]["neighborhood"]      = "Brasil";
$dados["payer"]["address"]["city"]              =  $clienteCidade;
$dados["payer"]["address"]["federal_unit"]      = "MT";
//Para atulizar a data de vencimento do boleto
// $data_atual = new DateTime();
// $data_atual->modify('+15 days');
// $dados['date_of_expiration'] = $data_atual->format('Y-m-d\TH:i:s.vP');

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.mercadopago.com/v1/payments',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($dados),
    CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'Authorization: Bearer ' . $access_token
    ),
));
$response = curl_exec($curl);
$resultado = json_decode($response);
//var_dump($response);
curl_close($curl);
?>



<!-- <?php  echo $resultado->transaction_details->external_resource_url;//GERA O CÓDIGO DO BOLETO
?> -->
<br><br>
<a href="<?php echo $resultado->transaction_details->external_resource_url; ?>" target="_blank">Gerar Boleto</a>


<?php
$query = $pdo->query("INSERT INTO status(nome, status, codigo) VALUES('" . isset($resultado->issexternal_reference) . "', '" .$resultado->status . "', '" . $resultado->id. "')");

?>