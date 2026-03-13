<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Доступ запрещён');
}

require '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$id]);

header('Location: comments.php');
exit;