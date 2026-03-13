<?php
namespace App\Repositories;

use Database;

require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/UserRepositoryInterface.php';

class UserRepository implements UserRepositoryInterface {
    protected $connection;

    public function __construct() {
        $this->connection = Database::getInstance()->getConnection();
    }

    public function create(array $data) {
        $unique_id = uniqid();
        $stmt = $this->connection->prepare("
            INSERT INTO users (id, firstname, lastname, country, address, gender, skills, username, password, department, profile_image)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $unique_id,
            $data['firstname'],
            $data['lastname'],
            $data['country'],
            $data['address'],
            $data['gender'],
            $data['skills'],
            $data['username'],
            $data['password'],
            $data['department'],
            $data['profile_image'] ?? ''
        ]);
        return $unique_id;
    }

    public function update($id, array $data) {
        $stmt = $this->connection->prepare("
            UPDATE users SET 
            firstname = ?, lastname = ?, country = ?, address = ?, 
            gender = ?, skills = ?, username = ?, password = ?, department = ?, profile_image = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['firstname'],
            $data['lastname'],
            $data['country'],
            $data['address'],
            $data['gender'],
            $data['skills'],
            $data['username'],
            $data['password'],
            $data['department'],
            $data['profile_image'] ?? '',
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->connection->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findAll() {
        $stmt = $this->connection->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function usernameExists($username, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->connection->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $excludeId]);
        } else {
            $stmt = $this->connection->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
        }
        return $stmt->fetchColumn() > 0;
    }

    public function findByUsername($username) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
}