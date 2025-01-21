<?php

namespace iutnc\hellokant\connection;

use PDO;
use PDOException;
use Exception;



class ConnectionFactory {
    private static $connection = null;

    public static function makeConnection($conf) {
        $dsn = "mysql:host={$conf['host']};dbname={$conf['dbname']};charset=utf8";
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ];

        try {
            self::$connection = new PDO($dsn, $conf['username'], $conf['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }

        return self::$connection;
    }

    public static function getConnection() {
        if (self::$connection === null) {
            throw new Exception("Connection has not been established. Call makeConnection first.");
        }
        return self::$connection;
    }
}

// // une seule fois au lancement de l'application
// $conf = parse_ini_file('conf/db.conf.ini') ;
// ConnectionFactory::makeConnection($conf);
// // chaque fois qu'il est nécessaire d'obtenir une connexion
// $myPdo = ConnectionFactory::getConnection();