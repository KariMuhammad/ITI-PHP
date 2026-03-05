<?php

function is_required($field)
{
    return trim((string) $field) !== '';
}

// prevent any number in field
function no_numbers($field)
{
    return filter_var($field, FILTER_VALIDATE_REGEXP, [
        'options' => ['regexp' => '/^[a-zA-Z\s]+$/']
    ]) !== false;
}

// username: start with letter, then letters or numbers
function is_valid_username($field)
{
    if (!is_required($field)) {
        return false;
    }

    return preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $field) === 1;
}

function check_password($field)
{
    $field = (string) $field;

    if (strlen($field) !== 8) {
        return false;
    }

    // no capital letters
    if (preg_match('/[A-Z]/', $field)) {
        return false;
    }

    // only allowed chars: lowercase letters, numbers, underscore
    if (!preg_match('/^[a-z0-9_]+$/', $field)) {
        return false;
    }

    return true;
}

// at least one skill checkbox selected
function has_at_least_one_skill($skills)
{
    if (!is_array($skills)) {
        return false;
    }

    $filtered = array_filter($skills, function ($value) {
        return trim((string) $value) !== '';
    });

    return count($filtered) > 0;
}