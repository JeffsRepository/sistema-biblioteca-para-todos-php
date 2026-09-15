<?php

namespace App\Biblioteca\Exeception\Livros;

use Exception;

class LivroNaoConstaNoArray extends Exception
{
    public function __construct()
    {
        throw new Exception('Esse livro não consta no estoque');
    }
}