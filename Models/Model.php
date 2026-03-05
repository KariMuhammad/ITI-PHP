<?php

namespace Models;

/**
 * Class Model
 * @package Models
 */
abstract class Model {
    protected $table;
    
    protected $fillable = [];

    public function __construct() {
        $this->table = strtolower(get_class($this)) . 's';
    }
}