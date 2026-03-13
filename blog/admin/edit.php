<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit('Доступ запрещён');
}

require '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    exit('Пост не найден');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $imagePath = $post['image'];

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
                    if (!empty($post['image']) && file_exists('../' . $post['image'])) {
                        unlink('../' . $post['image']);
                    }
                    $imagePath = 'uploads/' . $fileName;
                } else {
                    $error = 'Ошибка загрузки изображения';
                }
            }
        }

        if (isset($_POST['delete_image']) && $_POST['delete_image'] === '1') {
            if (!empty($post['image']) && file_exists('../' . $post['image'])) {
                unlink('../' . $post['image']);
            }
            $imagePath = null;
        }

        if ($error === '') {
            $update = $pdo->prepare("
                UPDATE posts
                SET title = ?, content = ?, image = ?
                WHERE id = ?
            ");
            $update->execute([$title, $content, $imagePath, $id]);

            header('Location: posts.php');
            exit;
        }
    }
}

include '../templates/header.php';
?>

<div class="form-box">
    <h2>Редактировать пост</h2>

    <?php if ($error): ?>
        <div class="notice"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Заголовок</label>
            <input id="title" name="title" type="text" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="content">Текст</label>
            <textarea id="content" name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <?php if (!empty($post['image'])): ?>
            <div class="form-group">
                <p>Текущая картинка:</p>
                <img src="/blog/<?= htmlspecialchars($post['image']) ?>" alt="Изображение" style="max-width: 250px; margin-top: 10px;">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="delete_image" value="1">
                    Удалить текущую картинку
                </label>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="image">Новая картинка</label>
            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.gif,.webp">
        </div>

        <button type="submit">Обновить</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>