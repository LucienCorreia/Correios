<?php

namespace Correios;

use DOMDocument;
use Correios\Correios;

class Xml extends Correios {

    private $xml;
    private $root;
    private $elemento;
    private $elementos = [];

    public function __construct(Correios $correios) {

        $this->xml = new DOMDocument('1.0', 'ISO-8859-1');
        $this->root = $this->xml->createElement('correioslog');
        $this->xml->appendChild($this->root);

        $this->adicionarElemento('tipo_arquivo', 'Postagem')
            ->adicionarElemento('versao_arquivo', '2.3')
            ->adicionarElementosEm('plp', [
                'id_plp' => '',
                'valor_global' => '',
                'mcu_unidade_postagem' => '',
                'nome_unidade_postagem' => '',
                'cartao_postagem' => parent::$cartao
            ]);
    }

    public function adicionarElemento(string $elemento, $valor = '') : Xml {

        $this->elemento = $this->xml->createElement($elemento, $valor);
        $this->root->appendChild($this->elemento);
        $this->elementos[$elemento] = $this->elemento;

        return $this;
    }

    public function adicionarElementosEm(string $elemento, array $elementos) : Xml {
        
        $elementoPai = $this->getElemento($elemento);
        $elemento = $this->getNomeElemento($elemento);
        
        $this->elemento = $this->xml->createElement($elemento);
        $elementoPai->appendChild($this->elemento);
        $this->elementos[$elemento] = [];

        foreach($elementos as $k => $v) {
            $elementoFilho = $this->xml->createElement($k, $v);
            $this->elementos[$elemento][$k] = $this->elemento->appendChild($elementoFilho);
        }

        return $this;
    }

    private function getElemento(string $elemento) {

        $elementos = explode('.', $elemento);

        $elementoAtual = $this->root;

        foreach($elementos as $k => $v) {
            $check = $elementoAtual->getElementsByTagName($v);
            
            if(count($check) == 1) {
                $elementoAtual = $check[0];
            }
        }

        return $elementoAtual;
    }

    private function getNomeElemento(string $elemento) {

        $elementos = explode('.', $elemento);

        return end($elementos);
    }

    public function getXml() {
        
        return $this->xml->saveHTML();
    }
}