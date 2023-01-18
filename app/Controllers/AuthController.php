<?php

namespace App\Controllers;

//recursos do miniframework

use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action
{
    //Metodo 
    public function autenticar()
    {
        $usuario = Container::getModel('Usuario');

        //Manipula os dados de usuario no banco de dados (Verifica se o registro é compativel com os que existe no banco de dados)
        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', md5($_POST['senha']));

        //Checar no banco de dados se o usuario existe 
        $usuario->autenticar();
        //Verificando se id e nome estão populados, caso estejam a autenticação foi feita com sucesso / caso não esteja, a autenticação ira falhar

        if (!empty($usuario->__get('id')) && !empty($usuario->__get('nome'))) {
            //Iniciando tratamento com sessão para o usuario autenticado 
            session_start();
            //Atribui a super global de ssão o id e o nome de usuario que está logado
            $_SESSION['id'] = $usuario->__get('id');
            $_SESSION['nome'] = $usuario->__get('nome');

            //Redireciona para a pagina protegida / timeline
            header('Location: /timeline');
        } else {
            header('Location: /?login=erro'); //Redireciona para a tela de login caso aja erro e adicionana o parametro de erro na url
        }
    }

    public function sair()
    {
        session_start();
        session_destroy(); //Destruir a sessão para deslogar o usuario
        header('Location: /'); //Redirecionando para a pagina inical
    }
}
