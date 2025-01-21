<?php

use iutnc\hellokant\connection\ConnectionFactory;

require_once 'connection/ConnectionFactory.php';
require_once 'model/Model.php';
require_once 'model/Article.php';
require_once 'model/Categorie.php';
require_once 'query/Query.php';

// Configuration de la connexion
$conf = parse_ini_file('conf/db.conf.ini');
ConnectionFactory::makeConnection($conf);

// Vérification de l'existence de la table 'article'
$pdo = ConnectionFactory::getConnection();
$tableExists = $pdo->query("SHOW TABLES LIKE 'article'")->rowCount() > 0;

if ($tableExists) {
    // Création et test de l'objet Article
    $article = new \iutnc\hellokant\model\Article(['nom' => 'Test Article', 'descr' => 'This is a test.', 'tarif' => 99.99, 'id_categ' => 1]);
    echo $article->nom . "\n"; // Affiche 'Test Article'
    echo $article->descr . "\n"; // Affiche 'This is a test.'

    // Insertion de l'article dans la base de données
    $article->insert();
    echo "Inserted article ID: " . $article->id . "\n";

    // Suppression de l'article de la base de données
    $article->delete();
    echo "Article deleted.\n";

    // Test de la méthode all()
    $articles = \iutnc\hellokant\model\Article::all();
    foreach ($articles as $article) {
        echo $article->nom . "\n";
    }

    // Test de la méthode find() avec un identifiant
    $article = \iutnc\hellokant\model\Article::find(64)[0];
    echo $article->nom . "\n";

    // Test de la méthode find() avec un critère de recherche
    $articles = \iutnc\hellokant\model\Article::find([['nom', 'LIKE', '%velo%']]);
    foreach ($articles as $article) {
        echo $article->id . "\n";
    }


    // Test de la méthode categorie() de la classe Article
    $article = \iutnc\hellokant\model\Article::first(64);
    if ($article) {
        $categorie = $article->categorie();
        echo $categorie->nom . "\n"; // Affiche le nom de la catégorie
    } else {
        echo "Article not found.\n";
    }

    // Test de la méthode articles() de la classe Categorie
    $categorie = \iutnc\hellokant\model\Categorie::first(1);
    if ($categorie) {
        $articles = $categorie->articles();
        foreach ($articles as $article) {
            echo $article->nom . "\n";
        }
    } else {
        echo "Categorie not found.\n";
    }
} else {
    echo "La table 'article' n'existe pas.\n";
}

  ////// fonctionne jusqu'ici /////////