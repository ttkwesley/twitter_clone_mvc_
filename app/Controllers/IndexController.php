<?php

namespace App\Controllers; //Namespace do script baseado na psr-4, conforme os diretorios

use App\Models\Usuario;
use stdClass;

//Recusos do miniFramework
use MF\Controller\Action;
use MF\Model\Container;


class IndexController extends Action
{
    //----METODOOS----//---ACTIONS--- 
    public function index()
    {

        $this->view->login = isset($_GET['login']) ? $_GET['login'] : ''; //Recuperar o parametro de url da tela de login
        $this->render('index'); //---Reenderizar a action --- 
    }

    public function inscreverse()
    {

        $this->view->usuario = array( //Limpar atributos na pagina inscreverse para não surgir o warning nos campos 
            'nome' => '',
            'email' => '',
            'senha' => ''
        );

        $this->view->erroCadastro = false;
        $this->render('inscreverse'); //--Reenderizar a action--- 
    }

    public function registrar()
    {
        //Receber dados do formulario 
        $usuario = Container::getModel('Usuario'); // Instancia do usuario com a conexão com o banco 

        //Setagem dos atributos atraves do valor recebido na super global _POST 
        $usuario->__set('nome', $_POST['nome']);
        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', md5($_POST['senha']));

        //sucesso 
        if ($usuario->validarCadastro(true) and count($usuario->getUsuarioPorEmail()) == 0) { // Se o validar campo etender o criterio da condição , os registros inseridos no formulario serão salvos no bd

            $usuario->salvar();
            $this->render('cadastro');
        } else { //erro

            //Não apagar os campo digitado pelo usuario caso tenha erro
            $this->view->usuario = array(
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'senha' => $_POST['senha']
            );

            $this->view->erroCadastro = true;
            $this->render('inscreverse');
        }
    }
}
