<?php

namespace Correios;

class Correios {

    protected static $usuario;
    protected static $senha;
    protected static $codigoAdmin;
    protected static $contrato;
    protected static $cartao;
    protected static $cnpj;

    public function __construct(array $args) {

        self::$usuario = $args['usuario'];
        self::$senha = $args['senha'];
        self::$codigoAdmin = $args['codigoAdmin'];
        self::$contrato = $args['contrato'];
        self::$cartao = $args['cartao'];
        self::$cnpj = $args['cnpj'];
    }

    public function factory(string $class) {

        $obj = 'Correios\\Funcoes\\' . $class;

        if(class_exists($obj)){
            return new $obj($this);
        }

        throw new \Exception('Classe ' . $class . ' não encontrada!');
    }
}