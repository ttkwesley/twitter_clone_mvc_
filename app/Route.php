<?php
//Definição das rotas
namespace App;

//Abstração das classe de routes
use MF\Init\Bootstrap;

class Route extends Bootstrap
{
    //Rotas que possui a aplicação 
    protected function initRoutes()
    {
        $routes['home'] = array(
            'route' => '/', //Rota a ser redirecionada
            'controller' => 'indexController', //Controler responsavel por paginas externas
            'action' => 'index'  //Ação a ser tomada
        );

        $routes['inscreverse'] = array( //Rota de se inscrever
            'route' => '/inscreverse',
            'controller' => 'indexController',
            'action' => 'inscreverse'
        );

        $routes['registrar'] = array( //rota de registro
            'route' => '/registrar',
            'controller' => 'indexController',
            'action' => 'registrar'
        );

        $routes['autenticar'] = array( //rota de autenticação
            'route' => '/autenticar',
            'controller' => 'AuthController', //Controler responsavel por processo de autenticação
            'action' => 'autenticar'
        );


        $routes['timeline'] = array(
            'route' => '/timeline',
            'controller' => 'AppController', //Controler responsavel por paginas restritas
            'action' => 'timeline'
        );

        $routes['sair'] = array(
            'route' => '/sair',
            'controller' => 'AuthController',
            'action' => 'sair'
        );

        $routes['tweet'] = array(
            'route' => '/tweet',
            'controller' => 'AppController',
            'action' => 'tweet'
        );

        $routes['quem_seguir'] = array(
            'route' => '/quem_seguir',
            'controller' => 'AppController',
            'action' => 'quemSeguir'
        );

        $routes['acao'] = array(
            'route' => '/acao',
            'controller' => 'AppController',
            'action' => 'acao'
        );

        $routes['remover_tweet'] = array(
            'route' => '/remover_tweet',
            'controller' => 'AppController',
            'action' => 'removerTweet'
        );

        $this->setRoutes($routes);
    }
}
