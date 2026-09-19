<?php

declare(strict_types=1);

use App\Biblioteca\Factory\UsuarioFactory;
use App\Biblioteca\Modelos\Usuario\Usuario;
use App\Biblioteca\Repository\ArmazenamentoJsonAbstrato;
use App\Biblioteca\Repository\Usuario\UsuarioRepositoryInterface;

/**
 * JsonUsuarioRepository
 * -----------------------
 * Faz EXATAMENTE o mesmo trabalho que InMemoryUsuarioRepository (implementa
 * a mesma UsuarioRepositoryInterface), mas em vez de guardar os usuarios
 * num array que desaparece quando o programa fecha, ele guarda tudo num
 * arquivo .json em disco - seu "banco de dados" simples.
 *
 * Por causa da interface, o Sistema NEM PRECISA SABER que trocamos de
 * "memoria" para "arquivo": basta, na hora de montar o Sistema, entregar
 * um "new JsonUsuarioRepository(...)" em vez de "new InMemoryUsuarioRepository()".
 * Nada em Sistema.php, em Operacao/ ou no CLI precisa mudar. Isso e o
 * beneficio pratico de programar contra uma INTERFACE, e nao contra uma
 * classe concreta.
 */
class JsonUsuarioRepository extends ArmazenamentoJsonAbstrato implements UsuarioRepositoryInterface
{
    #[Override]
    public function __construct(
        private readonly string $caminhoArquivo,
        private readonly UsuarioFactory $usuario_factory = new UsuarioFactory(),
    ){
        return parent::__construct($caminhoArquivo);
    }

    #[Override]
    public function adicionar(Usuario $usuario): void
    {
        $registros = $this->lerTodosOsRegistros();

        foreach($registros as $registro) {

            if($registro['cpf'] === $usuario->cpf()) {
                throw new \DomainException("Já existe um usuario cadastrado com CPF {$usuario->cpf()}.");
            }
        }

        $registros[] = $usuario->toArray();
        $this->salvarTodosOsRegistros($registros);
    }

    #[Override]
    public function buscarPorCpf(string $cpf): ?Usuario
    {
        foreach($this->lerTodosOsRegistros() as $registro) {
            if($registro['cpf'] === $cpf) {
                return $this->converterEmUsuario($registro);
            }
        }

        return null;
    }

    #[Override]
    public function todos(): array
    {
        return array_map(
            fn(array $registro) => $this->converterEmUsuario($registro),
            $this->lerTodosOsRegistros() 
        );
    }

    /**
     * converterEmUsuario()
     * ---------------------
     * O caminho inverso de toArray(): pega um array que veio do JSON (ex:
     * ['tipo' => 'funcionario', 'id' => '1', 'nome' => 'Ana', 'cargo' =>
     * 'Atendente']) e usa a UsuarioFactory (que voce ja tem!) pra
     * reconstruir o objeto certo - Cliente, Funcionario ou Admin.
     *
     * Isso e "desserializar": texto/array -> objeto PHP de novo.
     */
    private function converterEmUsuario(array $registro): Usuario
    {
        return $this->usuario_factory->criar(
            tipo: $registro['tipo'],
            nome: $registro['nome'],
            dataNascimento: $registro['data_nascimento'],
            cpf: $registro['cpf'],
            endereco: $registro['endereco'],
            cargo: $registro['cargo'] ?? '',
        );
    }
}