<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Доступ запрещён');
}

require '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if ($post && !empty($post['image']) && file_exists('../' . $post['image'])) {
    unlink('../' . $post['image']);
}

$pdo->prepare("DELETE FROM comments WHERE post_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$id]);

header('Location: posts.php');
exit;