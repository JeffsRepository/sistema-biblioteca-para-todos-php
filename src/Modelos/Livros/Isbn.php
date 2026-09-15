<?php

namespace App\Biblioteca\Modelos\Livros;

use DomainException;

class Isbn
{
    private string $isbn;

    public function __construct(string $isbn)
    {
        $isbn = trim(str_replace('-', '', $isbn));

        //Verifica se tem 13 digitos e se são apenas numeros.
        if (strlen($isbn) !== 13 || !ctype_digit($isbn)) {
            return throw new DomainException('O ISBN precisa ser valido');
        }

        $this->isbn = $isbn;
    }

    /* public function isValidISBN13(): bool
    {
        $this->isbn = str_replace('-', '', $this->isbn);

        Verifica se tem 13 digitos e se são apenas numeros.
        if (strlen($this->isbn) !== 13 || !ctype_digit($this->isbn)) {
            return throw new DomainException('O ISBN precisa ser valido');
        }

        $sum = 0;
        Loop pelos 12 primeiros digitos
        for ($i = 0; $i < 12; $i++) {
            //Multiplica por 1 ou 3 alternadamente
            $peso = ($i % 2 === 0) ? 1 : 3;
            $sum += $isbn[$i] * $peso;
        }

        Calcula o dígito verificador esperado
        $checkDigit = (10 - ($sum % 10)) % 10;

        Compara com o último dígito do ISBN
        return $isbn[12] == $checkDigit;

        return true;
    }*/

    public function __toString()
    {
        return $this->isbn;
    }
}