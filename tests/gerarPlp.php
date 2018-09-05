<?php

require('../vendor/autoload.php');

use Correios\{
    Funcoes\Plp,
    Correios
};

$correios = new Correios([
    'usuario' => 'sigep',
    'senha' => 'n5f9t8',
    'codigoAdmin' => '17000190',
    'contrato' => '9992157880',
    'cartao' => '0067599079',
    'cnpj' => '34028316000103'
]);

$etiqueta = $correios->factory('Etiqueta')
    ->setIdServico('124849')
    ->get();

echo($etiqueta);

$plp = $correios->factory('Plp');

$result = $plp->setRemetente([
        'cep_remetente' => '89255-750',
        'telefone_remetente' => '(47) 09929-7387'
    ])
    ->setFormaDePagamento(1)
    ->setEtiqueta($etiqueta)
    ->setObjetoPostal([
        'numero_etiqueta' => $etiqueta,
        'cubagem' => '0.00',
        'peso' => '300',
        'status_processamento' => '0'
    ], [
        'nome_destinatario' => 'Lucien Correia',
        'telefone_destinatario' => '47099297387',
        'celular_destinatario' => '47099297387',
        'email_destinatario' => 'lucien@wadvice.com.br',
        'logradouro_destinatario' => 'Barão Rio Branco',
        'numero_end_destinatario' => '818'
    ], [
        'bairro_destinatario' => 'centro',
        'cidade_destinatario' => 'Jaraguá do Sul',
        'uf_destinatario' => 'SC',
        'codigo_usuario_cliente' => '',
        'centro_custo_cliente' => '',
        'numero_nota_fiscal' => '',
        'serie_nota_fiscal' => '',
        'valor_nota_fiscal' => '',
        'natureza_nota_fiscal' => '',
        'valor_a_cobrar' => ''
    ], [
        'codigo_servico_adicional' => '025',
        'valor_declarado' => ''
    ], [
        'tipo_objeto' => '002',
        'dimensao_altura' => '10',
        'dimensao_largura' => '10',
        'dimensao_comprimento' => '10',
        'dimensao_diametro' => '0'
    ])
    ->get();

    header("Content-type: text/xml");

    echo html_entity_decode($result);
