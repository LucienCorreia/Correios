<?php

namespace Correios\Funcoes;

use Correios\{
    Xml,
    Request,
    Correios
};

class Plp extends Correios {

    private static $correios;
    private static $etiquetas;
    private static $countObjetos = 0;
    private static $etiquetasCodigo;
    private static $idPlp;
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

        $remetente = array_merge($valoresPadroes, $remetente);

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
                'numero_etiqueta' => self::$etiquetasCodigo[self::$countObjetos],
                'codigo_objeto_cliente' => '',
                'cubagem' => '0,00',
                'rt1' => '',
                'rt2' => '',
                'data_postagem_sara' => '',
                'status_processamento' => '0',
                'numero_comprovante_postagem' => '',
                'valor_cobrado' => '',
            ],
            'destinatario' => [

            ],
            'nacional' => [
                'codigo_usuario_postal' => '',
                'centro_custo_cliente' => '',
                'numero_nota_fiscal' => '',
                'serie_nota_fiscal' => '',
                'valor_nota_fiscal' => '',
                'natureza_nota_fiscal' => '',
                'descricao_objeto' => '',
                'valor_a_cobrar' => ''
            ],
            'servico' => [
                'valor_declarado' => ''
            ]
        ];

        $objeto = array_merge($objeto, $valoresPadroes['objeto']);
        $nacional = array_merge($nacional, $valoresPadroes['nacional']);
        $destinatario = array_merge($destinatario, $valoresPadroes['destinatario']);

        $destinatario['telefone_destinatario'] == '' ?: $this->formatarNumero($destinatario['telefone_destinatario']);
        $destinatario['celular_destinatario'] == '' ?: $this->formatarNumero($destinatario['celular_destinatario']);

        $this->xml->adicionarElementosEm('objeto_postal', $objeto, true, true)
            ->adicionarElementoDepoisDe('objeto_postal.data_postagem_sara', 'destinatario')
            ->adicionarElementoDepoisDe('objeto_postal.data_postagem_sara', 'nacional')
            ->adicionarElementoDepoisDe('objeto_postal.data_postagem_sara', 'servico_adicional')
            ->adicionarElementoDepoisDe('objeto_postal.data_postagem_sara', 'dimensao_objeto')
            ->adicionarElementosEm('objeto_postal.destinatario', $destinatario, false)
            ->adicionarElementosEm('objeto_postal.nacional', $nacional, false)
            ->adicionarElementosEm('objeto_postal.servico_adicional', $servico, false)
            ->adicionarElementosEm('objeto_postal.dimensao_objeto', $dimensao, false);

        self::$countObjetos++;

        return $this;
    }

    public function setFormaDePagamento(int $pagamento) {

        $this->xml->adicionarElemento('forma_pagamento', $pagamento);

        return $this;
    }

    public function setEtiquetas($idServico, int $qntEtiquetas = 1) : Plp {

        $etiquetas = self::$correios->factory('Etiqueta')
            ->setIdServico($idServico)
            ->get($qntEtiquetas);

        self::$etiquetasCodigo = $etiquetas;

        foreach($etiquetas as $k => $v) {
            $etiqueta1 = substr($v, 0, 10);
            $etiqueta2 = substr($v, 11, 13);
            $etiquetas[$k] = $etiqueta1 . $etiqueta2; 
        }        

        self::$etiquetas = $etiquetas;

        return $this;
    }

    public function get() {

        if(!$this->xml->validarXml()) {
            throw new \Exception('Xml Inválido!');
        }

        $cliente = new Request(self::$correios);

        $codigo = $cliente->make('fechaPlpVariosServicos')
        ->setParametros([
            'xml' => $this->getXml(),
            'idPlpCliente' => '102030',
            'cartaoPostagem' => parent::$cartao,
            'listaEtiquetas' => self::$etiquetas,
            'usuario' => parent::$usuario,
            'senha' => parent::$senha
        ])->getResposta(function($codigo) {
            return $codigo;
        });

        $xml = $cliente->make('solicitaXmlPlp')
        ->setParametros([
            'idPlpMaster' => $codigo,
            'usuario' => parent::$usuario,
            'senha' => parent::$senha
        ])->getResposta(function($xml) {
            return $xml;
        });

        return $xml;
    }

    public function getXml() : string {

        return  $this->xml->getXml();
    }

    private function formatarNumero($numero) : string {
        
        return preg_replace('([^\d]+)', '', $numero);
    }
}