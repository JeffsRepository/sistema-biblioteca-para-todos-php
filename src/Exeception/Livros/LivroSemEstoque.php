<?php

namespace App\Biblioteca\Exeception\Livros;

use Exception;
use Throwable;

class LivroSemEstoque extends Exception
{
    public function __construct()
    {
        throw new Exception('Livro sem estoque suficiente');
    }
}