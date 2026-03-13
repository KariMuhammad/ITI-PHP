<?php
namespace App\Repositories;

interface UserRepositoryInterface {
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findAll();
    public function findById($id);
    public function usernameExists($username, $excludeId = null);
    public function findByUsername($username);
}