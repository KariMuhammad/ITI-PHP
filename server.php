<?php
session_start();

define('DATA_FILE', __DIR__ . '/data/users.csv');


include_once 'utils.php';

// ─── Handle Form Submission (POST) ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = trim($_POST['edit_id'] ?? '');
    $isEditMode = !empty($editId);
    
    if (!$isEditMode) {
        $captchaInput = trim($_POST['captcha'] ?? '');
        if ($captchaInput !== ($_SESSION['captcha'] ?? '')) {
            header('Location: index.php?error=' . urlencode('Wrong captcha try again.'));
            exit;
        }
    }

    $skills = isset($_POST['skills']) ? implode('|', $_POST['skills']) : '';

    if ($isEditMode) { // Handle Edit Form
        $records = readAllRecords();
        $recordIndex = null;

        // Find the record to edit
        foreach ($records as $indx => $r) {
            if ($r['id'] === $editId) {
                $recordIndex = $i;
                break;
            }
        }

        if ($recordIndex === null) {
            header('Location: index.php?error=' . urlencode('Record not found.'));
            exit;
        }

        // Build updated record
        $updatedRecord = [
            'id' => $editId,
            'firstname' => trim($_POST['firstname'] ?? ''),
            'lastname' => trim($_POST['lastname'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'skills' => $skills,
            'username' => trim($_POST['username'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'department' => trim($_POST['department'] ?? ''),
        ];

        // Replace the old record and save
        $records[$recordIndex] = $updatedRecord;
        writeAllRecords($records);

        header('Location: table.php?updated=1');
        exit;

    } else { // Handle Create Form
        $id = uniqid('u', true);

        $newRecord = [
            'id' => $id,
            'firstname' => trim($_POST['firstname'] ?? ''),
            'lastname' => trim($_POST['lastname'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'skills' => $skills,
            'username' => trim($_POST['username'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'department' => trim($_POST['department'] ?? ''),
        ];

        $file = fopen(DATA_FILE, 'a');

        if (!file_exists(DATA_FILE)) {
            $headers = [
                'id',
                'firstname',
                'lastname',
                'country',
                'address',
                'gender',
                'skills',
                'username',
                'password',
                'department'
            ];
            fputcsv($file, $headers);
        }

        fputcsv($file, $newRecord);
        fclose($file);

        header('Location: index.php?success=1');
        exit;
    }
}

// ─── Handle DELETE ───────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $records = readAllRecords();

    // Filter out the record with the matching ID
    $records = array_filter($records, fn($r) => $r['id'] !== $deleteId);

    writeAllRecords(array_values($records));

    header('Location: table.php?deleted=1');
    exit;
}