<?php

//Namespace referente ao diretorio
namespace App\Controllers;

//Recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{
    //metodos
    public function timeline()
    {
        //Metodo para proteger as paginas privadas // Para o usuario ter acesso a essa action ele vai ter que fazer a autenticação
        $this->validaAutenticacao();
        //Recuperação dos tweets
        $tweet =  Container::getModel('Tweet'); //Instancia do objeto já com a conexão com o banco de dados

        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweets = $tweet->getAll();


        $this->view->tweets = $tweets;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);
        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('timeline');
    }

    public function tweet()
    {
        $this->validaAutenticacao();
        $tweet = Container::getModel('Tweet'); //Instancia o objeto já com a conexão com o banco de dados

        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar(); //Salva o resgitro no banco de dados
        header('Location: /timeline');
    }

    public function validaAutenticacao()
    {
        session_start();

        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=erro');
        }
    }

    public function quemSeguir()
    {
        $this->validaAutenticacao();

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = array();

        if ($pesquisarPor != '') {
            $usuario =  Container::getModel('Usuario'); //Instanciando a conexão já com o banco de dados com o objeto usuario 
            $usuario->__set('nome', $pesquisarPor);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();
        }
        $this->view->usuarios = $usuarios;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);
        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('quemSeguir');
    }

    public function acao()
    {
        $this->validaAutenticacao();

        //Acao a ser tomada
        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario =  Container::getModel('Usuario'); //Instancia com conexao com o banco de dados
        $usuario->__set('id', $_SESSION['id']);

        //Id do usuario a ser seguido 
        if ($acao == 'seguir') {
            $usuario->seguirUsuario($id_usuario_seguindo); //Ação para Seguir
        } else if ($acao == 'deixar_de_seguir') {
            $usuario->deixarSeguirUsuario($id_usuario_seguindo); //Ação para Deixar de seguir
        }

        header('Location: /quem_seguir');
    }
    public function removerTweet() //Remoção dos tweet 
    {

        $this->validaAutenticacao();
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $tweet = Container::getModel('Tweet');
        $tweet->__set('id', $id);
        $tweet->remover();
        header('location: /timeline');
    }
}
