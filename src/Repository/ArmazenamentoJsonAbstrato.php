<?php

declare(strict_types=1);

namespace App\Biblioteca\Repository;

/**
 * ArmazenamentoJsonAbstrato
 * --------------------------
 * concentra TUDO que é sobre ler e
 * escrever um arquivo .json, pra JsonUsuarioRepository e JsonLivroRepository
 * nao precisarem repetir o mesmo codigo
 *
 * ela nao sabe transformar um Usuario ou um Livro em array (isso e especifico de
 * cada um). Ela so sabe: "dado um array PHP, transformo em JSON e salvo
 * no arquivo" e "dado um arquivo JSON, devolvo o array PHP de volta".
 *
 * Cada repositorio concreto (Json...Repository) so precisa dizer QUAL
 * arquivo usar, e converter Usuario/Livro <-> array.
 */
abstract class ArmazenamentoJsonAbstrato
{
    public function __construct(
        private readonly string $caminhoArquivo
    ){
        $this->garantirQueArquivoExiste();
    }

    /**
     * Se a pasta ou o arquivo ainda nao existem (ex: primeira vez que o
     * sistema roda), cria os dois com uma lista vazia "[]" dentro.
     * Assim os metodos de leitura abaixo nunca precisam se preocupar com
     * "e se o arquivo nao existir ainda?".
     */
    private function garantirQueArquivoExiste(): void
    {
        $pasta = dirname($this->caminhoArquivo);

        if(!is_dir($pasta)) {
            // "true" recursivo = cria pastas intermediarias tambem, se
            // precisar (ex: data/ dentro do projeto).
            mkdir($this->caminhoArquivo, 0777, true);
        }

        if(!file_exists($this->caminhoArquivo)) {
            file_put_contents($this->caminhoArquivo, '[]');
        }
    }

    /**
     * Le o arquivo inteiro do disco e devolve um array PHP "cru" (ainda
     * nao convertido para Usuario/Livro - isso e responsabilidade de quem
     * chama este metodo).
     *
     * @return array<int, array<string, mixed>>
     */
    protected function lerTodosOsRegistros(): array
    {
        $conteudo = file_get_contents($this->caminhoArquivo);

        if($conteudo === false || trim($conteudo) === '') {
            return [];
        }

        // json_decode(..., true) = devolve arrays associativos em vez de
        // objetos stdClass. Isso combina melhor com o resto do sistema,
        // que ja trabalha com arrays associativos (ex: Livro::toArray()).
        $dados = json_decode($conteudo, true);

        if(!is_array($dados)) {
            throw new \RuntimeException("O arquivo {$this->caminhoArquivo} contém um JSON inválido.");
        }

        return $dados;
    }

    /**
     * Recebe um array PHP (uma lista de registros ja convertidos com
     * ->toArray()) e regrava o arquivo inteiro com esses dados.
     *
     * Reescri o arquivo INTEIRO a cada alteracao (em vez de so
     * "acrescentar uma linha") porque JSON e um formato que descreve UM
     * documento completo - nao da pra "adicionar no fim" com seguranca sem
     * reescrever os colchetes/virgulas.
     *
     * @param array<int, array<string, mixed>> $registros
     */
    protected function salvarTodosOsRegistros(array $registros): void
    {
        // JSON_PRETTY_PRINT deixa o arquivo formatado (com identacao),
        // facil de abrir e ler manualmente para conferir os dados.
        // JSON_UNESCAPED_UNICODE evita transformar acentos em codigos
        // estranhos tipo \u00e3 - "Jo\u00e3o" vira "João" no arquivo.
        $json = json_encode($registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if($json === false) {
            throw new \RuntimeException('Não foi possível salvar os dados em JSON.');
        }

        // LOCK_EX evita que duas escritas simultaneas corrompam o arquivo
        // (boa pratica mesmo num sistema simples de terminal).
        file_put_contents($this->caminhoArquivo, $json, LOCK_EX);
    }
}