<?php

namespace App\Models;

use MF\Model\Model;
use PDOStatement;

class Usuario extends Model
{ //Atributos que representam as colunas no banco de dados
    private $id;
    private $nome;
    private $email;
    private $senha;

    //Metodo magico para recuperar
    public function __get($atributo)
    {
        return $this->$atributo;
    }
    //Metodo magico para setar 
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    //Salvar
    public function salvar()
    {
        $query =
            "insert into usuarios
        (nome, email, senha)values(:nome, :email, :senha)"; //paramentos em values para serem setados pelo bind do pdo 
        $stmt = $this->db->prepare($query); // Preparar a query (db recebe o metodo pdo em vendor/Model)

        //Recuperar e setar os valores no prepare da query 
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha')); //md5() - > hash de 32 caracteres
        $stmt->execute();

        return $this;
    }

    //Validar se o cadastro pode ser feito 
    public function validarCadastro()
    {
        //Metodo de validação simples, se algum campo tiver menos de 3 caractere o campo passa a não ser valido 
        $valido = true;
        if (strlen($this->__get('nome')) < 3) {
            $valido = false;
        }
        if (strlen($this->__get('email')) < 3) {
            $valido = false;
        }
        if (strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        return $valido;
    }

    //Recuperar um usuario por email 
    public function getUsuarioPorEmail()
    {
        $query = 'select nome, email from usuarios where email = :email';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC); // Array associativo 
    }

    //Verificar usuario no banco de dados
    public function autenticar()
    {
        $query = 'select id, nome, email from usuarios where email = :email and senha = :senha';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($usuario['id'] != '' && $usuario['nome'] != '') { //Autenticação de sucesso
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
        }

        return $this;
    }
    public function getAll()
    {
        $query = '
        select 
            u.id, 
            u.nome, 
            u.email,
            (
                select
                    count(*)
                from
                    usuarios_seguidores as us
                where 
                    us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
            ) as seguindo_sn  
        from 
            usuarios as u 
        where 
            u.nome like :nome and u.id != :id_usuario';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%' . $this->__get('nome') . '%'); //caracteres coringa % para indicar que o termo pode ter caracteres a esquerda e a direita
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC); //Retorna o resultado obtido em forma de array 
    }

    public function seguirUsuario($id_usuario_seguindo)
    {
        $query = 'insert into usuarios_seguidores(id_usuario, id_usuario_seguindo)values(:id_usuario, :id_usuario_seguindo)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true; //Retorno pra inserção no banco de dados ao clicar em seguir 
    }
    public function deixarSeguirUsuario($id_usuario_seguindo)
    {
        $query = 'delete from usuarios_seguidores where id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true; //Para remover do banco de dados o usuario seguindo 
    }

    //Recuperar informaçoes do usuario 
    public function getInfoUsuario()
    {
        $query = "select nome from usuarios where id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC); //Retorno em array associativo 
    }


    //Recuperar total de tweets
    public function getTotalTweets()
    {
        $query = "select count(*) as total_tweet from tweets where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    //Recuperar total de usuarios seguidos
    public function getTotalSeguindo()
    {
        $query = "select count(*) as total_seguindo from usuarios_seguidores where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC); //Retorno em array associativo 
    }


    //Recuperar total de seguidores
    public function getTotalSeguidores()
    {
        $query = "select count(*) as total_seguidores from usuarios_seguidores where id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC); //Retorno em array associativo 
    }
}
