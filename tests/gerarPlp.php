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

$plp = $correios->factory('Plp');

$result = $plp->setRemetente([
        'nome_remetente' => 'Lucien Risso Correia',
        'logradouro_remetente' => 'Paulo Klitkze',
        'numero_remetente' => '474',
        'complemento_remetente' => '',
        'bairro_remetente' => 'Amizade',
        'cep_remetente' => '89255-750',
        'cidade_remetente' => 'Jaraguá do Sul',
        'uf_remetente' => 'SC',
        'telefone_remetente' => '(47) 99629-7387',
        'fax_remetente' => '',
        'email_remetente' => ''
    ])
    ->setFormaDePagamento(1)
    ->setEtiquetas(124849, 2)
    ->setObjetoPostal([
        'numero_etiqueta' => '',
        'codigo_objeto_cliente' => '',
        'codigo_servico_postagem' => '04162',
        'cubagem' => '0.00',
        'peso' => '300',
        'rt1' => '',
        'rt2' => '',
    ], [
        'nome_destinatario' => 'Lucien Correia',
        'telefone_destinatario' => '',
        'celular_destinatario' => '47996297387',
        'email_destinatario' => 'lucien@wadvice.com.br',
        'logradouro_destinatario' => 'Barão Rio Branco',
        'complemento_destinatario' => '',
        'numero_end_destinatario' => '818',
    ], [
        'bairro_destinatario' => 'Centro',
        'cidade_destinatario' => 'Jaraguá do Sul',
        'uf_destinatario' => 'SC',
        'cep_destinatario' => '89255750',
        'codigo_usuario_postal' => '',
        'centro_custo_cliente' => '',
        'numero_nota_fiscal' => '',
        'serie_nota_fiscal' => '',
        'valor_nota_fiscal' => '',
        'natureza_nota_fiscal' => '',
        'descricao_objeto' => '',
        'valor_a_cobrar' => ''
    ], [
        'codigo_servico_adicional' => '025',
        'valor_declarado' => ''
    ], [
        'tipo_objeto' => '002',
        'dimensao_altura' => '10',
        'dimensao_largura' => '11',
        'dimensao_comprimento' => '16',
        'dimensao_diametro' => '0'
    ])
    ->setObjetoPostal([
        'numero_etiqueta' => '',
        'codigo_objeto_cliente' => '',
        'codigo_servico_postagem' => '04162',
        'cubagem' => '0.00',
        'peso' => '300',
        'rt1' => '',
        'rt2' => '',
    ], [
        'nome_destinatario' => 'Lucien Correia',
        'telefone_destinatario' => '',
        'celular_destinatario' => '47996297387',
        'email_destinatario' => 'lucien@wadvice.com.br',
        'logradouro_destinatario' => 'Barão Rio Branco',
        'complemento_destinatario' => '',
        'numero_end_destinatario' => '818',
    ], [
        'bairro_destinatario' => 'Centro',
        'cidade_destinatario' => 'Jaraguá do Sul',
        'uf_destinatario' => 'SC',
        'cep_destinatario' => '89255750',
        'codigo_usuario_postal' => '',
        'centro_custo_cliente' => '',
        'numero_nota_fiscal' => '',
        'serie_nota_fiscal' => '',
        'valor_nota_fiscal' => '',
        'natureza_nota_fiscal' => '',
        'descricao_objeto' => '',
        'valor_a_cobrar' => ''
    ], [
        'codigo_servico_adicional' => '025',
        'valor_declarado' => ''
    ], [
        'tipo_objeto' => '002',
        'dimensao_altura' => '10',
        'dimensao_largura' => '11',
        'dimensao_comprimento' => '16',
        'dimensao_diametro' => '0'
    ])
    ->get();

    header('Content-type: text/xml');

    echo html_entity_decode($result);
