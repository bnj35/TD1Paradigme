<?php

namespace iutnc\hellokant\model;

use iutnc\hellokant\model\Model;

class Categorie extends Model {
    protected static $table = 'categorie';

    public function articles() {
        return $this->has_many('Article', 'id_categ');
    }
}