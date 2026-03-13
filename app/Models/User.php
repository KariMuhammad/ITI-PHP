<?php

require_once '../Models/Model.php';
require_once '../Database.php';

class User extends Model {
    protected $fillable = [
        'firstname',
        'lastname',
        'country',
        'address',
        'gender',
        'skills',
        'username',
        'password',
        'department',
        'profile_image'];

        public function getColumns() {
            return $this->fillable;
        }

        public function setColumn($column, $value) {
            if (in_array($column, $this->fillable)) {
                $this->$column = $value;
            }
        }

        public function getColumn($column) {
            if (in_array($column, $this->fillable)) {
                return $this->$column;
            }
            return null;
        }

        public function save() {
            $connection = Database::getConnection();
            echo "Saving user: " . $this->username;
        }
}