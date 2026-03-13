<?php
namespace App\Decorators;

use App\Repositories\UserRepositoryInterface;


class LoggingUserRepository implements UserRepositoryInterface {
    protected $repository;

    public function __construct(UserRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function create(array $data) {
        error_log("[LoggingUserRepository] Creating new user: " . ($data['username'] ?? 'unknown'));
        return $this->repository->create($data);
    }

    public function update($id, array $data) {
        error_log("[LoggingUserRepository] Updating user ID: $id");
        return $this->repository->update($id, $data);
    }

    public function delete($id) {
        error_log("[LoggingUserRepository] Deleting user ID: $id");
        return $this->repository->delete($id);
    }

    public function findAll() {
        error_log("[LoggingUserRepository] Fetching all users");
        return $this->repository->findAll();
    }

    public function findById($id) {
        error_log("[LoggingUserRepository] Fetching user by ID: $id");
        return $this->repository->findById($id);
    }

    public function usernameExists($username, $excludeId = null) {
        error_log("[LoggingUserRepository] Checking if username exists: $username");
        return $this->repository->usernameExists($username, $excludeId);
    }

    public function findByUsername($username) {
        error_log("[LoggingUserRepository] Fetching user by username: $username");
        return $this->repository->findByUsername($username);
    }
}