<?php
session_start();

function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: /login');
        exit();
    }
}

function login($userId)
{
    $_SESSION['user_id'] = $userId;
}

function logout()
{
    session_destroy();
}
