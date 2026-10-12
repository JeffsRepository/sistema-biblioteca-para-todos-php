<?php

namespace App\Biblioteca\Modelos\Usuario;

use DateTimeImmutable;
use Override;

class Funcionario extends Usuario
{
    public function __construct(
        string $nome, 
        DateTimeImmutable $dataNascimento, 
        Cpf $cpf, 
        string $endereco,
        private readonly string $cargo
    ){
        return parent::__construct($nome, $dataNascimento, $cpf, $endereco);
    }

    public function getCargo(): string
    {
        return $this->cargo;
    }

    #[Override]
    public function tipo(): string
    {
        return 'Funcionario';
    }

    #[Override]
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'cargo' => $this->getCargo()
        ]);
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'cargo' => $this->getCargo()
        ]);
    }
    
    #[Override]
    public function permissoes(): array
    {
        return [

        ];
    }

    #[Override]
    public static function reconstruir(
        string $nome, 
        string $dataNascimento, 
        string $cpf, 
        string $endereco,
        string $cargo = ''
    ): ?Usuario {
        
        return new self (
            $nome,
            new \DateTimeImmutable($dataNascimento),
            new Cpf($cpf),
            $endereco,
            $cargo,
        );
    }
}