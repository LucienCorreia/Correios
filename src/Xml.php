<?php

namespace Correios;

use DOMDocument;
use Correios\{
    Correios,
    Request
};

class Xml extends Correios {

    private $xml;
    private $root;
    private $elemento;
    private $elementos = [];

    public function __construct(Correios $correios) {

        $this->xml = new DOMDocument('1.0', 'UTF-8');
        $this->root = $this->xml->createElement('correioslog');
        $this->xml->appendChild($this->root);

        $plp = [
            'id_plp' => '',
            'valor_global' => '',
            'mcu_unidade_postagem' => '',
            'nome_unidade_postagem' => '',
            'cartao_postagem' => parent::$cartao
        ];

        $this->adicionarElemento('tipo_arquivo', 'Postagem')
            ->adicionarElemento('versao_arquivo', '2.3')
            ->adicionarElementosEm('plp', $plp);
    }

    public function adicionarElemento(string $elemento, $valor = '') : Xml {

        $this->elemento = $this->xml->createElement($elemento, $valor);
        $this->root->appendChild($this->elemento);
        $this->elementos[$elemento] = $this->elemento;

        return $this;
    }

    public function adicionarElementosEm(string $elemento, array $elementos, bool $criarElementoPai = true, bool $elementoParalelo = false) : Xml {
        
        $elementoPai = $this->getElemento($elemento);
        $elemento = $this->getNomeElemento($elemento);
        
        if($criarElementoPai) {
            $this->elemento = $this->xml->createElement($elemento);
            
            if($elementoParalelo) {
                $this->root->appendChild($this->elemento);
            } else {
                $elementoPai->appendChild($this->elemento);
            }
        } else {
            $this->elemento = $elementoPai;
        }
        
        $this->elementos[$elemento] = [];
        foreach($elementos as $k => $v) {
            $elementoFilho = $this->xml->createElement($k, $v);
            $this->elementos[$elemento][$k] = $this->elemento->appendChild($elementoFilho);
        }

        return $this;
    }

    public function adicionarElementoDepoisDe(string $elementoReferencia, string $elementoNovo)  : Xml{

        $elementoReferencia = $this->getElemento($elementoReferencia);
        $elementoNovo = $this->xml->createElement($elementoNovo);
        $this->elemento->insertBefore($elementoNovo, $elementoReferencia);

        return $this;
    }

    private function getElemento(string $elemento) {

        $elementos = explode('.', $elemento);

        $elementoAtual = $this->root;
        
        $check = $elementoAtual->getElementsByTagName($elementos[count($elementos) - 1]);

        if($check->length > 0) {
            $elementoAtual = $check[count($check) - 1];
        }

        return $elementoAtual;
    }

    private function getNomeElemento(string $elemento) {

        $elementos = explode('.', $elemento);

        return end($elementos);
    }

    public function validarXml() : bool {

        return $this->xml->schemaValidate('../extra/validacao.xsd');
    }

    public function getXml() {
        
        return $this->xml->saveXML();
    }
}