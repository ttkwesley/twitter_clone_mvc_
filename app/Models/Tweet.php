<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model
{
    //Atributos// 
    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    //metodos magicos
    public function __get($atributo)
    {
        return $this->$atributo;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    //Salvar tweets no banco de dados
    public function salvar()
    {

        $query = "insert into tweets(id_usuario, tweet)values(:id_usuario, :tweet)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->execute();

        return $this;
    }

    public function remover()
    {

        $query = "delete from tweets where id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();
        return true;
    }

    //Recuperar
    public function getAll()
    { //Recuperar todos os tweets
        $query = "
            select 
                t.id,
                t.id_usuario,
                u.nome, 
                t.tweet,
                DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data
            from 
                tweets as t
                left join usuarios as u on (t.id_usuario = u.id)
            where 
                id_usuario = :id_usuario
                or t.id_usuario in (SELECT id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
            order by 
                t.data desc
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}