<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Доступ запрещён');
}

require '../config/db.php';

$comments = $pdo->query("
    SELECT comments.id, comments.content, comments.created_at,
           users.name AS user_name,
           posts.title AS post_title
    FROM comments
    JOIN users ON users.id = comments.user_id
    JOIN posts ON posts.id = comments.post_id
    ORDER BY comments.created_at DESC
")->fetchAll();

include '../templates/header.php';
?>

<h1 class="page-title">Админ-панель: комментарии</h1>

<div class="card admin-actions">
    <a class="btn" href="posts.php">Назад к постам</a>
</div>

<div class="table-wrap">
    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Пользователь</th>
            <th>Пост</th>
            <th>Комментарий</th>
            <th>Дата</th>
            <th>Действие</th>
        </tr>

        <?php foreach ($comments as $comment): ?>
            <tr>
                <td><?= $comment['id'] ?></td>
                <td><?= htmlspecialchars($comment['user_name']) ?></td>
                <td><?= htmlspecialchars($comment['post_title']) ?></td>
                <td><?= htmlspecialchars($comment['content']) ?></td>
                <td><?= htmlspecialchars($comment['created_at']) ?></td>
                <td>
                    <a class="btn btn-danger" href="delete_comment.php?id=<?= $comment['id'] ?>" onclick="return confirm('Удалить комментарий?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../templates/footer.php'; ?>