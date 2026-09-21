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

    public function getcargo(): string
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
            'cargo' => $this->getcargo()
        ]);
    }
    
    #[Override]
    public function permissoes(): array
    {
        return [

        ];
    }
}