<?php

use iutnc\hellokant\connection\ConnectionFactory;

require_once 'connection/ConnectionFactory.php';

$conf = parse_ini_file('conf/db.conf.ini');

try {
    ConnectionFactory::makeConnection($conf);
    echo "Connection established successfully.\n";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}