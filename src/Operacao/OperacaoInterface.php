<?php

declare(strict_types=1);

namespace App\Biblioteca\Operacao;

interface OperacaoInterface
{
    public function executar(): void;
}