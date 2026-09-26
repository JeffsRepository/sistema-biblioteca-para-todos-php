<?php

declare(strict_types=1);

$autoloaderComposer = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloaderComposer)) {

    require $autoloaderComposer;

} else {

    spl_autoload_register(function(string $nomeDaClasse){
        $prefixo = 'App\\';

        if (!str_starts_with($nomeDaClasse, $prefixo)) {
            return;
        }

        $caminhoRelativo = str_replace('\\', '/', substr($nomeDaClasse, strlen($prefixo)));
        $arquivo = __DIR__ . '/../src/' . $caminhoRelativo . '.php';

        if (file_exists($arquivo)) {
            require $arquivo;
        }
    });

}

use App\Biblioteca\CLI\Menu;
use App\Biblioteca\Factory\UsuarioFactory;
use App\Biblioteca\Repository\Livro\JsonLivroRepository;
use App\Biblioteca\Repository\Transacao\InMemoryRepository;
use App\Biblioteca\Repository\Usuario\JsonUsuarioRepository;
use App\Biblioteca\Sistema\Sistema;

$pastaDeDados = __DIR__ . '/../data';

$repositorioDeLivros = new JsonLivroRepository($pastaDeDados . '/livros.json');
$repositorioDeUsuarios = new JsonUsuarioRepository($pastaDeDados . '/usuarios.json');
$repositorioDeTransacoes = new InMemoryRepository();

$sistema = new Sistema($repositorioDeLivros, $repositorioDeTransacoes, $repositorioDeUsuarios);

$menu = new Menu($sistema, new UsuarioFactory());
$menu->executar();