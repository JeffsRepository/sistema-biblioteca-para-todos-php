<?php

namespace App\Biblioteca\Modelos\Livros;

use Override;

enum EstadoLivro: string  implements \JsonSerializable
{
    case DISPONIVEL = 'disponivel';
    case EMPRESTADO = 'emprestado';
    case VENDIDO = 'vendido';

    public function label(): string
    {
        return match ($this) {
            self::DISPONIVEL => 'disponivel',
            self::EMPRESTADO => 'emprestado',
            self::VENDIDO => 'vendido'
        };
    }

    #[Override]
    public function jsonSerialize(): string
    {
        return $this->value;
    }
}