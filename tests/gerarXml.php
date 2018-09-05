<?php

require('../vendor/autoload.php');

use Correios\{
    Correios,
    Xml
};

$correios = new Correios([
    'usuario' => 'sigep',
    'senha' => 'n5f9t8',
    'codigoAdmin' => '17000190',
    'contrato' => '9992157880',
    'cartao' => '0067599079',
    'cnpj' => '34028316000103'
]);

$xml = new Xml($correios);

$xml->setRemetente([
    'cep_remetente' => '89255-750',
    'telefone_remetente' => '(47) 9929-7387'
])
->setFormaDePagamento(1)
->getXml();