<?php

namespace App\Biblioteca\Exeception\Usuarios;

use Exception;

class UsuarioNaoEncontrado extends Exception
{
    public function __construct()
    {
        throw new Exception('Usuario não encontrado');
    }
}