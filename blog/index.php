<?php
require 'config/db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

$limit = 5;
$offset = ($page - 1) * $limit;

$totalStmt = $pdo->query("SELECT COUNT(*) FROM posts");
$totalPosts = (int)$totalStmt->fetchColumn();
$totalPages = (int)ceil($totalPosts / $limit);

$stmt = $pdo->prepare("
    SELECT posts.*, users.name
    FROM posts
    JOIN users ON users.id = posts.user_id
    ORDER BY posts.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll();

include 'templates/header.php';
?>

<h1 class="page-title">Лента статей</h1>

<?php if ($posts): ?>
    <?php foreach ($posts as $post): ?>
        <article class="card post-card">
            <h2>
                <a href="post.php?id=<?= $post['id'] ?>">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
            </h2>

            <?php if (!empty($post['image'])): ?>
                <div class="post-image">
                    <img src="<?= htmlspecialchars($post['image']) ?>" alt="Изображение поста">
                </div>
            <?php endif; ?>

            <div class="post-preview">
                <?= nl2br(htmlspecialchars(mb_substr($post['content'], 0, 200))) ?>...
            </div>

            <div class="post-meta">
                Автор: <?= htmlspecialchars($post['name']) ?> |
                Дата: <?= htmlspecialchars($post['created_at']) ?>
            </div>

            <a class="btn" href="post.php?id=<?= $post['id'] ?>">Читать полностью</a>
        </article>
    <?php endforeach; ?>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a class="btn" href="?page=<?= $page - 1 ?>">Предыдущая</a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a class="btn" href="?page=<?= $page + 1 ?>">Следующая</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="card">
        <p>Постов пока нет.</p>
    </div>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>