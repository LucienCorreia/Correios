<?php

namespace Correios\Funcoes;

use SoapClient;
use Correios\{
    Correios,
    Request
};

class Etiqueta extends Correios {

    private static $idServico;
    private static $correios;

    public function __construct(Correios $correios) {

        self::$correios = $correios;
    }

    public function setIdServico($idServico) : Etiqueta {
        self::$idServico = $idServico;
        
        return $this;
    }

    public function get() {
        $client = new Request(self::$correios);

        $etiqueta = $client->make('solicitaEtiquetas')
            ->setParametros([
                'tipoDestinatario' => 'C',
                'identificador' => parent::$cnpj,
                'idServico' => self::$idServico,
                'qtdEtiquetas' => '1',
                'usuario' => parent::$usuario,
                'senha' => parent::$senha
            ])->getResposta(function($etiqueta) {
                return $this->formatarResposta($etiqueta);
            });

        $resposta = $client->make('geraDigitoVerificadorEtiquetas')
            ->setParametros([
                'etiquetas' => $etiqueta,
                'usuario' => parent::$usuario,
                'senha' => parent::$senha
            ])->getResposta(function($codigo) use($etiqueta) {
                return $this->inserirCodigoVerificador($etiqueta, $codigo);
            });

        return $resposta;
    }

    private function formatarResposta(string $resposta) : string {

        $range = explode(',', $resposta);

        return $range[0];
    }

    public function inserirCodigoVerificador(string $etiqueta, int $codigo) : string {
        
        return str_replace(' ', $codigo, $etiqueta);
    }
}