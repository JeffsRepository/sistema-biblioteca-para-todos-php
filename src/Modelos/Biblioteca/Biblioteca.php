<?php

declare(strict_types=1);

namespace App\Biblioteca\Modelos\Biblioteca;

use Override;

class Biblioteca implements \JsonSerializable
{
    public function __construct(
        private readonly string $nome,
        private readonly string $cnpj,
        private readonly string $numero,
        private readonly string $endereco,
        private readonly string $contato,
    ){
    }

    public function nomeBiblioteca(): string
    {
        return $this->nome;
    }

    public function cnpjBiblioteca(): string
    {
        return $this->cnpj;
    }

    public function numero(): string
    {
        return $this->numero;
    }

    public function endereco(): string
    {
        return $this->endereco;
    }

    public function contato(): string
    {
        return $this->contato;
    }

    public function __toString(): string
    {
        return "{$this->nomeBiblioteca()} (CNPJ: {$this->cnpjBiblioteca()})";
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'nome' => $this->nomeBiblioteca(),
            'cnpj' => $this->cnpjBiblioteca(),
            'numero' => $this->numero(),
            'endereco' => $this->endereco(),
            'contato' => $this->contato(),
        ];
    }

    public static function reconstruir(
        string $nome,
        string $cnpj,
        string $numero,
        string $endereco,
        string $contato,
    ): self
    {
        return new self ($nome, $cnpj, $numero, $endereco, $contato);
    }
}