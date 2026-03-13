<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Доступ запрещён');
}

require '../config/db.php';

$posts = $pdo->query("
    SELECT posts.*, users.name
    FROM posts
    JOIN users ON users.id = posts.user_id
    ORDER BY posts.created_at DESC
")->fetchAll();

include '../templates/header.php';
?>

<h1 class="page-title">Админ-панель: посты</h1>

<div class="card admin-actions">
    <a class="btn" href="create.php">Добавить пост</a>
    <a class="btn" href="comments.php">Комментарии</a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Заголовок</th>
            <th>Автор</th>
            <th>Дата</th>
            <th>Действия</th>
        </tr>

        <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= $post['id'] ?></td>
                <td><?= htmlspecialchars($post['title']) ?></td>
                <td><?= htmlspecialchars($post['name']) ?></td>
                <td><?= htmlspecialchars($post['created_at']) ?></td>
                <td>
                    <div class="admin-actions">
                        <a class="btn" href="edit.php?id=<?= $post['id'] ?>">Редактировать</a>
                        <a class="btn btn-danger" href="delete.php?id=<?= $post['id'] ?>" onclick="return confirm('Удалить пост?')">Удалить</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../templates/footer.php'; ?>