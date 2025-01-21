<?php

namespace iutnc\hellokant\model;

use iutnc\hellokant\model\Model;

class Article extends Model {
    protected static $table = 'article';

    public function categorie() {
        return $this->belongs_to('Categorie', 'id_categ');
    }
}