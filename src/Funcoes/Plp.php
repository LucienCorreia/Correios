<?php

namespace Correios\Funcoes;

use Correios\{
    Xml,
    Request,
    Correios
};

class Plp extends Correios {

    private static $correios;
    private static $etiqueta;
    private $xml;

    public function __construct(Correios $correios) {

        self::$correios = $correios;
        $this->xml = new Xml($correios);
    }

    public function setRemetente(array $remetente) : Plp {

        $remetente['cep_remetente'] = $this->formatarNumero($remetente['cep_remetente']);
        $remetente['telefone_remetente'] = $this->formatarNumero($remetente['telefone_remetente']);

        $valoresPadroes = [
            'numero_contrato' => parent::$contrato,
            'numero_diretoria' => '68',
            'codigo_administrativo' => parent::$codigoAdmin,
        ];

        $remetente = array_merge($remetente, $valoresPadroes);

        $this->xml->adicionarElementosEm('remetente', $remetente);

        return $this;
    }

    public function setObjetoPostal(array $objeto,
                                    array $destinatario, 
                                    array $nacional, 
                                    array $servico, 
                                    array $dimensao) : Plp {

        //valores obrigatórios vazios
        $valoresPadroes = [
            'objeto' => [
                'codigo_objeto_cliente' => '',
                'data_postagem_sara' => '',
                'numero_comprovante_postagem' => '',
                'valor_cobrado' => '',
                'rt1' => '',
                'rt2' => ''
            ],
            'nacional' => [
                'naturaza_nota_fiscal' => ''
            ]
        ];

        $objeto = array_merge($objeto, $valoresPadroes['objeto']);
        $nacional = array_merge($nacional, $valoresPadroes['nacional']);

        //print_r($objeto);

        $this->xml->adicionarElementosEm('objeto_postal', $objeto)
            ->adicionarElementosEm('objeto_postal.destinatario', $destinatario)
            ->adicionarElementosEm('objeto_postal.nacional', $nacional)
            ->adicionarElementosEm('objeto_postal.servico_adicional', $servico)
            ->adicionarElementosEm('objeto_postal.dimensao_objeto', $dimensao);

        return $this;
    }

    public function setFormaDePagamento(int $pagamento) {

        $this->xml->adicionarElemento('forma_pagamento', $pagamento);

        return $this;
    }

    public function setEtiqueta(string $etiqueta) : Plp {

        $etiqueta1 = substr($etiqueta, 0, 9);
        $etiqueta2 = substr($etiqueta, 10, 12);
        $etiqueta = $etiqueta1 . $etiqueta2; 

        self::$etiqueta = $etiqueta;

        return $this;
    }

    public function get() {

        $cliente = new Request(self::$correios);

        $xml = $cliente->make('fechaPlpVariosServicos')
        ->setParametros([
            'xml' => $this->getXml(),
            'idPlpCliente' => '102030',
            'cartaoPostagem' => parent::$cartao,
            'listaEtiquetas' => self::$etiqueta,
            'usuario' => parent::$usuario,
            'senha' => parent::$senha
        ])->getResposta(function($xml) {
            return $xml;
        });

        return $xml;
    }

    public function getXml() {

        return $this->xml->getXml();
    }

    private function formatarNumero($numero) {
        
        return preg_replace('([^\d]+)', '', $numero);
    }
}