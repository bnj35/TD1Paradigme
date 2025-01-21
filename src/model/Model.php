<?php

namespace iutnc\hellokant\model;

use iutnc\hellokant\query\Query;

abstract class Model {
    protected $attributes = [];
    protected static $table;

    public function __construct($attributes = []) {
        $this->attributes = $attributes;
    }

    public function __get($name) {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function delete() {
        if (!isset($this->attributes['id'])) {
            throw new \Exception("Primary key not set.");
        }
        $query = Query::table(static::$table)->where('id', '=', $this->attributes['id']);
        return $query->delete();
    }

    public function insert() {
        $query = Query::table(static::$table);
        $id = $query->insert($this->attributes);
        $this->attributes['id'] = $id;
        return $id;
    }

    public static function all() {
        $query = Query::table(static::$table)->select();
        $results = $query->get();
        return array_map(function($attributes) {
            return new static($attributes);
        }, $results);
    }

    public static function find($criteria, $columns = ['*']) {
        $query = Query::table(static::$table)->select($columns);

        if (is_int($criteria)) {
            $query->where('id', '=', $criteria);
        } elseif (is_array($criteria)) {
            foreach ($criteria as $criterion) {
                $query->where($criterion[0], $criterion[1], $criterion[2]);
            }
        }

        $results = $query->get();
        return array_map(function($attributes) {
            return new static($attributes);
        }, $results);
    }

    public static function first($id) {
        $result = static::find($id);
        return $result[0] ?? null;
    }

    public function belongs_to($relatedModel, $foreignKey) {
        $relatedClass = "iutnc\\hellokant\\model\\$relatedModel";
        $relatedTable = $relatedClass::$table;
        $relatedId = $this->attributes[$foreignKey];
        $query = Query::table($relatedTable)->select()->where('id', '=', $relatedId);
        $result = $query->get();
        return new $relatedClass($result[0]);
    }

    public function has_many($relatedModel, $foreignKey) {
        $relatedClass = "iutnc\\hellokant\\model\\$relatedModel";
        $relatedTable = $relatedClass::$table;
        $primaryKey = $this->attributes['id'];
        $query = Query::table($relatedTable)->select()->where($foreignKey, '=', $primaryKey);
        $results = $query->get();
        return array_map(function($attributes) use ($relatedClass) {
            return new $relatedClass($attributes);
        }, $results);
    }
}

