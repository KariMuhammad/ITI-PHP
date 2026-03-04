<?php
include_once 'Database.php';

// Create Record in Database
function createRecord($data)
{
    $connection = create_connection();

    $unique_id = uniqid();

    $stmt = $connection->prepare("
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

// Update Record in Database
function updateRecord($id, $data)
{
    $connection = create_connection();
    $stmt = $connection->prepare("
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

// Delete Record in Database
function deleteRecord($id)
{
    $connection = create_connection();
    $stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

// Select All Records from Database
function readAllRecords()
{
    $connection = create_connection();
    $stmt = $connection->query("SELECT * FROM users ORDER BY id DESC");
    return $stmt->fetchAll();
}

// Select Single Record from Database
function readRecord($id)
{
    $connection = create_connection();
    $stmt = $connection->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Check if username exists
function usernameExists($username, $excludeId = null)
{
    $connection = create_connection();
    if ($excludeId) {
        $stmt = $connection->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $excludeId]);
    } else {
        $stmt = $connection->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
    }
    return $stmt->fetchColumn() > 0;
}

// get user by username
function getUserByUsername($username)
{
    $connection = create_connection();
    $stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch();
}
