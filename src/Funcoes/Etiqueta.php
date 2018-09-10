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

    public function get(int $qntEtiquetas) : array {
        $client = new Request(self::$correios);

        $etiquetas = $client->make('solicitaEtiquetas')
            ->setParametros([
                'tipoDestinatario' => 'C',
                'identificador' => parent::$cnpj,
                'idServico' => self::$idServico,
                'qtdEtiquetas' => $qntEtiquetas,
                'usuario' => parent::$usuario,
                'senha' => parent::$senha
            ])->getResposta(function($etiquetas) {
                return $this->formatarResposta($etiquetas);
            });

        $etiquetas = $this->recontarEtiquetas($qntEtiquetas, $etiquetas);

        $resposta = $client->make('geraDigitoVerificadorEtiquetas')
            ->setParametros([
                'etiquetas' => $etiquetas,
                'usuario' => parent::$usuario,
                'senha' => parent::$senha
            ])->getResposta(function($codigos) use($etiquetas) {
                return $this->inserirCodigoVerificador($etiquetas, $codigos);
            });

        return $resposta;
    }

    private function formatarResposta(string $resposta) : array {

        $range = explode(',', $resposta);

        $min = preg_replace('([^\d]+)', '', $range[0]);
        $max = preg_replace('([^\d]+)', '', $range[1]);

        $novasEtiquetas = [];

        for($i = $min; $i <= $max; $i++) {
            $novasEtiquetas[] = str_replace($min, $i, $range[0]);
        }

        return $novasEtiquetas;
    }

    public function inserirCodigoVerificador(array $etiquetas, $codigos) : array {
        
        foreach($etiquetas as $k => $v) {
            if(is_array($codigos)) {
                $etiquetas[$k] = str_replace(' ', $codigos[$k], $v);
            } else {
                $etiquetas[$k] = str_replace(' ', $codigos, $v);
            }
        }

        return $etiquetas;
    }

    private function recontarEtiquetas(int $qntEtiquetas, array $etiquetas) : array {

        if(count($etiquetas) != $qntEtiquetas) {
            $recontagem = [];

            for($i = 0; $i < $qntEtiquetas; $i++) {
                $recontagem[] = $etiquetas[$i];
            }

            return $recontagem;
        } else {
            return $etiquetas;
        }
    }
}