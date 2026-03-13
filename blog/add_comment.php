<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    exit('Ошибка доступа');
}

$data = json_decode(file_get_contents("php://input"), true);

$postId = (int)$data['post_id'];
$text = trim($data['text']);

if ($text === '') {
    exit('Пустой комментарий');
}

$stmt = $pdo->prepare("
    INSERT INTO comments (post_id, user_id, content)
    VALUES (?, ?, ?)
");
$stmt->execute([$postId, $_SESSION['user_id'], $text]);

$userStmt = $pdo->prepare("SELECT name FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();

echo '
<div class="comment">
    <div class="comment-meta">
        <strong>' . htmlspecialchars($user['name']) . '</strong> | только что
    </div>
    <div>' . nl2br(htmlspecialchars($text)) . '</div>
</div>
';
?>