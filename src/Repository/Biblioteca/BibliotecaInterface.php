<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository\Biblioteca;

use App\Biblioteca\Modelos\Biblioteca\Biblioteca;

interface BibliotecaInterface
{
    public function registrar(Biblioteca $biblioteca): void;
    public function buscar(string $cnpj): ?Biblioteca;
    public function atualizar(Biblioteca $biblioteca): void;
    public function verifica(): ?Biblioteca;
}