<?php

namespace Correios;

use SoapClient;

class Request extends Correios {

    private $url = 'https://apphom.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl';
    private $soap;
    private $funcao;
    private $parametros;

    public function __construct(Correios $correios) {
        
    }

    public function make(string $funcao) {

        $this->soap = new SoapClient($this->url);

        $this->funcao = $funcao;

        return $this;
    }

    public function setParametros(array $parametros) {
        $this->parametros = $parametros;

        return $this;
    }

    public function getResposta(callable $formatarReposta) {

        $funcao = $this->funcao;
        $resposta = $this->soap->$funcao($this->parametros);

        return $formatarReposta($resposta->return);
    } 
}