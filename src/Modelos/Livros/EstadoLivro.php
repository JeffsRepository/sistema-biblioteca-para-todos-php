<?php

namespace App\Biblioteca\Modelos\Livros;

enum EstadoLivro: string
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
}