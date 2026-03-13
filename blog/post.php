<?php
require 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT posts.*, users.name
    FROM posts
    JOIN users ON users.id = posts.user_id
    WHERE posts.id = ?
");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die('Пост не найден');
}

$commentsStmt = $pdo->prepare("
    SELECT comments.*, users.name
    FROM comments
    JOIN users ON users.id = comments.user_id
    WHERE comments.post_id = ?
    ORDER BY comments.created_at DESC
");
$commentsStmt->execute([$id]);
$comments = $commentsStmt->fetchAll();

include 'templates/header.php';
?>

<article class="card">
    <h1><?= htmlspecialchars($post['title']) ?></h1>

    <div class="post-meta">
        Автор: <?= htmlspecialchars($post['name']) ?> |
        Дата: <?= htmlspecialchars($post['created_at']) ?>
    </div>

    <?php if (!empty($post['image'])): ?>
        <div class="post-image">
            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Изображение поста">
        </div>
    <?php endif; ?>

    <div class="post-full">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>

    <div class="like-box">
        <button class="like-btn" onclick="addLike(<?= $post['id'] ?>)">Лайк</button>
        <span class="like-count" id="like-count-<?= $post['id'] ?>">0</span>
    </div>
</article>

<section class="card">
    <h2 class="comments-title">Комментарии</h2>

    <div id="comments">
        <?php if ($comments): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <div class="comment-meta">
                        <strong><?= htmlspecialchars($comment['name']) ?></strong> |
                        <?= htmlspecialchars($comment['created_at']) ?>
                    </div>
                    <div><?= nl2br(htmlspecialchars($comment['content'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Комментариев пока нет.</p>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="form-group" style="margin-top: 20px;">
            <label for="commentText">Добавить комментарий</label>
            <textarea id="commentText" placeholder="Введите комментарий"></textarea>
        </div>
        <button onclick="sendComment()">Отправить</button>
    <?php else: ?>
        <div class="notice">Чтобы оставить комментарий, войдите.</div>
    <?php endif; ?>
</section>

<script>
function sendComment() {
    const text = document.getElementById('commentText').value.trim();

    if (text === '') {
        alert('Введите комментарий');
        return;
    }

    fetch('add_comment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            post_id: <?= $id ?>,
            text: text
        })
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('comments').insertAdjacentHTML('afterbegin', html);
        document.getElementById('commentText').value = '';
    });
}

function addLike(postId) {
    const key = 'post_like_' + postId;
    let count = localStorage.getItem(key);

    if (!count) {
        count = 0;
    }

    count = parseInt(count) + 1;
    localStorage.setItem(key, count);
    document.getElementById('like-count-' + postId).textContent = count;
}

window.addEventListener('DOMContentLoaded', function () {
    const postId = <?= $id ?>;
    const key = 'post_like_' + postId;
    const count = localStorage.getItem(key) || 0;
    document.getElementById('like-count-' + postId).textContent = count;
});
</script>

<?php include 'templates/footer.php'; ?>