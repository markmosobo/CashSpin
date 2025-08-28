<?php
session_start();
require_once 'login.php'; // loads GameHandler

$handler = new GameHandler();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['errors'] = ["Security token mismatch. Please try again."];
        $_SESSION['modal'] = "loginModal";
        header("Location: index.php");
        exit;
    }

    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    $result = $handler->loginUser($phone, $password);

    if ($result['success']) {
        header("Location: index.php");
        exit;
    } else {
        if (!isset($_SESSION['errors'])) {
            $_SESSION['errors'] = [];
        }
        $_SESSION['errors'][] = $result['message'];
        $_SESSION['modal'] = "loginModal";
        header("Location: index.php");
        exit;
    }
}
