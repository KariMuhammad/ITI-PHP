<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/app/Repositories/UserRepository.php';

use App\Repositories\UserRepository;

function getUserRepository() {
    static $repository = null;
    if ($repository === null) {
        $repository = new UserRepository();
    }
    return $repository;
}

// Create Record in Database
function createRecord($data)
{
    return getUserRepository()->create($data);
}

// Update Record in Database
function updateRecord($id, $data)
{
    return getUserRepository()->update($id, $data);
}

// Delete Record in Database
function deleteRecord($id)
{
    return getUserRepository()->delete($id);
}

// Select All Records from Database
function readAllRecords()
{
    return getUserRepository()->findAll();
}

// Select Single Record from Database
function readRecord($id)
{
    return getUserRepository()->findById($id);
}

// Check if username exists
function usernameExists($username, $excludeId = null)
{
    return getUserRepository()->usernameExists($username, $excludeId);
}

// get user by username
function getUserByUsername($username)
{
    return getUserRepository()->findByUsername($username);
