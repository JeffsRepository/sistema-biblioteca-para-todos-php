<?php

namespace App\Biblioteca\Exeception\Usuarios;

use Exception;
use Throwable;
use Override;

class UsuarioComLimiteDeAluguelExcedido extends Exception
{
    public function __construct()
    {
       throw new Exception('Usuario com mais de 3 livros alugados');
    }
}