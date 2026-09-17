<?php

declare(strict_types=1);

namespace App\Biblioteca\Modelos\Usuario;

use Override;

/**
 * Admin
 * -----
 * Admin ESTENDE Funcionario (herda tudo que um Funcionario pode fazer:
 * vender, emprestar) e ainda ganha permissoes extras - aqui representadas
 * por $podeGerenciarUsuarios e $podeRemoverLivroDoAcervo.
 */
class Admin extends Funcionario
{
    #[Override]
    public function tipo(): string
    {
        return 'Admin';
    }


     /**
     * So o Admin pode remover um livro definitivamente do acervo (diferente
     * de vender ou emprestar, que sao operacoes normais de qualquer
     * Funcionario). O Sistema vai checar esse metodo antes de permitir a
     * acao - veja Sistema::removerLivroDoAcervo().
     */
    public function podeRemoverLivroDoAcervo(): bool
    {
        return true;
    }

    #[Override]
    public function permissoes(): array
    {
        return [
            
        ];
    }
}