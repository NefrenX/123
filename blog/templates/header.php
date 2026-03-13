<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Блог</title>
    <link rel="stylesheet" href="/blog/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-row">
        <div class="logo">
            <a href="/blog/index.php">Блог</a>
        </div>

        <nav class="nav">
            <a href="/blog/index.php">Главная</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/blog/admin/posts.php">Админ-панель</a>
                <?php endif; ?>
                <a href="/blog/logout.php">Выйти</a>
            <?php else: ?>
                <a href="/blog/login.php">Вход</a>
                <a href="/blog/register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="container">