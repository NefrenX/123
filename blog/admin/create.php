<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Доступ запрещён');
}

require '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $imagePath = null;

    if ($title === '' || $content === '') {
        $error = 'Заполните все обязательные поля';
    } else {
        if (!empty($_FILES['image']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                $error = 'Разрешены только изображения JPG, PNG, GIF, WEBP';
            } else {
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $target = '../uploads/' . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $imagePath = 'uploads/' . $fileName;
                } else {
                    $error = 'Ошибка загрузки изображения';
                }
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare("
                INSERT INTO posts (title, content, image, user_id)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$title, $content, $imagePath, $_SESSION['user_id']]);

            header('Location: posts.php');
            exit;
        }
    }
}

include '../templates/header.php';
?>

<div class="form-box">
    <h2>Добавить пост</h2>

    <?php if ($error): ?>
        <div class="notice"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Заголовок</label>
            <input id="title" name="title" type="text" required>
        </div>

        <div class="form-group">
            <label for="content">Текст</label>
            <textarea id="content" name="content" required></textarea>
        </div>

        <div class="form-group">
            <label for="image">Картинка</label>
            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.gif,.webp">
        </div>

        <button type="submit">Сохранить</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>