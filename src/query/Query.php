<?php

namespace iutnc\hellokant\query;

use iutnc\hellokant\connection\ConnectionFactory;
use PDO;
use autoload;

class Query {
    private $table = [];
    private $columns = [];
    private $conditions = [];
    private $values = [];    

    public static function table($tableName) {
        $instance = new self();
        $instance->table = $tableName;
        return $instance;
    }

    public function where($column, $operator, $value) {
        $this->conditions[] = [$column, $operator, $value];
        return $this;
    }

    public function select($columns = ['*']) {
        $this->columns = $columns;
        return $this;
    }

    private function tableExists($pdo, $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        return $stmt->rowCount() > 0;
    }

    public function get() {
        $pdo = ConnectionFactory::getConnection();
        if (!$this->tableExists($pdo, $this->table)) {
            throw new \Exception("Table '$this->table' n'existe pas.");
        }

        $columns = $this->columns === ['*'] ? '*' : implode(', ', array_map(function($column) {
            return "`$column`";
        }, $this->columns));
        $sql = 'SELECT ' . $columns . ' FROM `' . $this->table . '`';
        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', array_map(function($condition) {
                return "`{$condition[0]}` {$condition[1]} ?";
            }, $this->conditions));
        }
        $params = array_map(function($condition) {
            return $condition[2];
        }, $this->conditions);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete() {
        $pdo = ConnectionFactory::getConnection();
        if (!$this->tableExists($pdo, $this->table)) {
            throw new \Exception("Table '$this->table' n'existe pas.");
        }

        $sql = 'DELETE FROM `' . $this->table . '`';
        if (!empty($this->conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', array_map(function($condition) {
                return "`{$condition[0]}` {$condition[1]} ?";
            }, $this->conditions));
        }
        $params = array_map(function($condition) {
            return $condition[2];
        }, $this->conditions);

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function insert($data) {
        $pdo = ConnectionFactory::getConnection();
        if (!$this->tableExists($pdo, $this->table)) {
            throw new \Exception("Table '$this->table' n'existe pas.");
        }

        $columns = implode(', ', array_map(function($column) {
            return "`$column`";
        }, array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = 'INSERT INTO `' . $this->table . '` (' . $columns . ') VALUES (' . $placeholders . ')';
        $params = array_values($data);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $pdo->lastInsertId();
    }
}